<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyCelebrationNotifications extends Command
{
    protected $signature = 'family:send-daily-celebrations {--dry-run : Preview notifications without sending emails} {--force : Send even if already sent today}';

    protected $description = 'Send daily birthday and anniversary reminders to all members (email + WhatsApp-ready).';

    public function handle(): int
    {
        $today = Carbon::today();
        $cacheKey = 'family_celebrations_sent_'.$today->format('Y-m-d');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (! $dryRun && ! $force && Cache::has($cacheKey)) {
            $this->warn('Notifications already sent today ('.$today->format('d M Y').'). Use --force to send again.');
            return self::SUCCESS;
        }

        $todayAlerts = $this->collectAlerts($today, $today);

        if ($todayAlerts->isEmpty()) {
            $this->info('No birthdays or anniversaries found for today.');
            return self::SUCCESS;
        }

        $members = Member::orderBy('name')->get();
        $whatsAppEnabled = (bool) config('family_notifications.whatsapp_enabled', false);

        $emailSent = 0;
        $whatsAppSent = 0;
        $whatsAppFailed = 0;
        $sentEmails = []; // track addresses already emailed

        foreach ($members as $recipient) {
            $messageBody = $this->buildRecipientMessage($recipient, $todayAlerts, $today);

            if ($dryRun) {
                $this->line('---');
                $this->line('Recipient: '.$recipient->name);
                $this->line('Email: '.($recipient->email ?: 'N/A'));
                $this->line('Phone: '.($recipient->phone ?: 'N/A'));
                $this->line($messageBody);
                continue;
            }

            if ($recipient->email) {
                $normalizedEmail = strtolower(trim($recipient->email));

                if (! in_array($normalizedEmail, $sentEmails, true)) {
                    $subject = 'Family Celebration Reminder - '.$today->format('d M Y');

                    Mail::send('emails.family_celebrations', [
                        'recipient' => $recipient,
                        'todayAlerts' => $todayAlerts,
                        'today' => $today,
                    ], function ($message) use ($recipient, $subject) {
                        $message->to($recipient->email, $recipient->name)->subject($subject);
                    });

                    $sentEmails[] = $normalizedEmail;
                    $emailSent++;
                }
            }

            $normalizedPhone = $this->normalizePhone((string) ($recipient->phone ?? ''));
            if ($whatsAppEnabled && $normalizedPhone !== '') {
                if ($this->sendWhatsAppMessage($normalizedPhone, $messageBody)) {
                    $whatsAppSent++;
                } else {
                    $whatsAppFailed++;
                }
            }
        }

        if ($dryRun) {
            $this->info('Dry run completed. No emails/WhatsApp messages were sent.');
            return self::SUCCESS;
        }

        $extraRecipients = collect(config('family_notifications.email_recipients', []))
            ->filter(fn (mixed $email) => is_string($email) && trim($email) !== '')
            ->map(fn (string $email) => strtolower(trim($email)))
            ->unique()
            ->reject(fn (string $email) => in_array($email, $sentEmails, true)) // skip already-emailed members
            ->values();

        if ($extraRecipients->isNotEmpty()) {
            foreach ($extraRecipients as $email) {
                Mail::send('emails.family_celebrations_summary', [
                    'todayAlerts' => $todayAlerts,
                    'today' => $today,
                ], function ($message) use ($email, $today) {
                    $message->to($email)->subject('Family Celebration Summary - '.$today->format('d M Y'));
                });
            }

            $emailSent += $extraRecipients->count();
        }

        if ($whatsAppEnabled) {
            $this->info('WhatsApp sent: '.$whatsAppSent.' | failed: '.$whatsAppFailed);
        }

        $this->info('Email notifications sent: '.$emailSent);

        // Mark as sent for today so subsequent runs are blocked
        Cache::put($cacheKey, true, $today->copy()->endOfDay());

        return self::SUCCESS;
    }

    private function collectAlerts(Carbon $startDate, Carbon $endDate): Collection
    {
        $alerts = collect();
        $today = Carbon::today();
        $members = Member::with('relatedMember')->orderBy('name')->get();
        $membersById = $members->keyBy('id');
        $spouseMap = $this->buildSpouseMap($members);
        $processedAnniversaryKeys = [];

        $members->each(function (Member $member) use (&$alerts, $startDate, $endDate, $today, $membersById, $spouseMap, &$processedAnniversaryKeys): void {
            $birthday = $this->nextOccurrence($member->date_of_birth, $startDate);
            if ($birthday && $birthday->betweenIncluded($startDate, $endDate)) {
                $alerts->push([
                    'type' => 'birthday',
                    'member' => $member,
                    'display_name' => $member->name,
                    'recipient_ids' => [(int) $member->id],
                    'date' => $birthday,
                    'days_left' => $today->diffInDays($birthday),
                ]);
            }

            $anniversary = $this->nextOccurrence($member->wedding_anniversary, $startDate);
            if ($anniversary && $anniversary->betweenIncluded($startDate, $endDate)) {
                $recipientIds = [(int) $member->id];
                $displayName = $member->name;

                $spouseId = $spouseMap[(int) $member->id] ?? null;
                $spouse = $spouseId ? $membersById->get($spouseId) : null;

                if ($spouse) {
                    $ids = [(int) $member->id, (int) $spouse->id];
                    sort($ids);
                    $anniversaryKey = implode('-', $ids).'-'.$anniversary->format('Y-m-d');

                    if (isset($processedAnniversaryKeys[$anniversaryKey])) {
                        return;
                    }

                    $processedAnniversaryKeys[$anniversaryKey] = true;
                    $displayName = $member->name.' and '.$spouse->name;
                    $recipientIds = $ids;
                }

                $alerts->push([
                    'type' => 'anniversary',
                    'member' => $member,
                    'display_name' => $displayName,
                    'recipient_ids' => $recipientIds,
                    'date' => $anniversary,
                    'days_left' => $today->diffInDays($anniversary),
                ]);
            }
        });

        return $alerts->sortBy('date')->values();
    }

    private function nextOccurrence(?Carbon $sourceDate, Carbon $fromDate): ?Carbon
    {
        if (! $sourceDate) {
            return null;
        }

        $occurrence = Carbon::create($fromDate->year, $sourceDate->month, $sourceDate->day)->startOfDay();

        if ($occurrence->lt($fromDate->copy()->startOfDay())) {
            $occurrence->addYear();
        }

        return $occurrence;
    }

    private function buildRecipientMessage(Member $recipient, Collection $todayAlerts, Carbon $today): string
    {
        $lines = [
            'Dear '.$recipient->name.',',
            '',
            'Date: '.$today->format('d M Y'),
            '',
        ];

        foreach ($todayAlerts as $alert) {
            $celebrationName = $alert['display_name'] ?? $alert['member']->name;
            $recipientIds = collect($alert['recipient_ids'] ?? [(int) $alert['member']->id])
                ->map(fn (mixed $id) => (int) $id)
                ->all();

            if (in_array((int) $recipient->id, $recipientIds, true)) {
                if ($alert['type'] === 'birthday') {
                    $lines[] = 'Happy Birthday to you, '.$recipient->name.'! 🎂';
                } else {
                    $lines[] = 'Happy Anniversary to you, '.$recipient->name.'! 💍';
                }
            } else {
                $pronoun = $this->objectPronoun($alert['member']);
                if ($alert['type'] === 'birthday') {
                    $lines[] = $celebrationName.' has birthday today. Please wish '.$pronoun.'.';
                } else {
                    if (count($recipientIds) > 1) {
                        $lines[] = $celebrationName.' have wedding anniversary today. Please wish them.';
                    } else {
                        $lines[] = $celebrationName.' has wedding anniversary today. Please wish '.$pronoun.'.';
                    }
                }
            }
            $lines[] = '';
        }

        $lines[] = 'Regards,';
        $lines[] = 'Family Team';

        return implode(PHP_EOL, $lines);
    }

    private function buildSummaryMessage(Collection $todayAlerts, Carbon $today): string
    {
        $lines = [
            'Daily Family Celebrations Summary',
            'Date: '.$today->format('d M Y'),
            '',
            'Today\'s Celebrations',
            '-------------------',
        ];

        foreach ($todayAlerts as $alert) {
            $lines[] = '- '.($alert['display_name'] ?? $alert['member']->name).' • '.ucfirst($alert['type']);
        }

        $lines[] = '';
        $lines[] = 'Please reach out and wish them.';

        return implode(PHP_EOL, $lines);
    }

    private function normalizePhone(string $phone): string
    {
        $normalized = preg_replace('/[^0-9+]/', '', $phone) ?: '';

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '00')) {
            return '+'.substr($normalized, 2);
        }

        if (! str_starts_with($normalized, '+')) {
            $defaultCode = trim((string) config('family_notifications.whatsapp_default_country_code', ''));
            if ($defaultCode !== '') {
                $normalized = $defaultCode.$normalized;
            }
        }

        return $normalized;
    }

    private function sendWhatsAppMessage(string $phone, string $message): bool
    {
        $accountSid = trim((string) config('family_notifications.twilio_account_sid', ''));
        $authToken  = trim((string) config('family_notifications.twilio_auth_token', ''));
        $fromNumber = trim((string) config('family_notifications.twilio_whatsapp_from', ''));

        if ($accountSid === '' || $authToken === '' || $fromNumber === '') {
            Log::warning('WhatsApp: Twilio credentials are not fully configured.', ['to' => $phone]);
            return false;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";
        try {
            $response = Http::timeout(20)
                ->withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post($url, [
                    'From' => 'whatsapp:'.$fromNumber,
                    'To'   => 'whatsapp:'.$phone,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp send failed (Twilio).', [
                'to'     => $phone,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::error('WhatsApp send exception (Twilio).', [
                'to'    => $phone,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private function objectPronoun(Member $member): string
    {
        return match (strtolower((string) $member->gender)) {
            'male' => 'him',
            'female' => 'her',
            default => 'them',
        };
    }

    private function buildSpouseMap(Collection $members): array
    {
        $map = [];

        foreach ($members as $member) {
            if (! $member->related_member_id) {
                continue;
            }

            if (! $this->isSpouseRelation((string) $member->relationship_to_other)) {
                continue;
            }

            $memberId = (int) $member->id;
            $relatedId = (int) $member->related_member_id;

            $map[$memberId] = $relatedId;
            $map[$relatedId] = $memberId;
        }

        return $map;
    }

    private function isSpouseRelation(string $relation): bool
    {
        $normalized = strtolower(trim($relation));

        if ($normalized === '') {
            return false;
        }

        return in_array($normalized, [
            'spouse',
            'wife',
            'husband',
            'partner',
        ], true);
    }
}
