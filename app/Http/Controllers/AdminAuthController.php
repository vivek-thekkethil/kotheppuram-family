<?php

namespace App\Http\Controllers;

use App\Models\EventNews;
use App\Models\FamilyHistory;
use App\Models\GalleryPhoto;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check() && $request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password) || ! $user->is_admin) {
            return back()->withErrors([
                'email' => 'Invalid admin credentials.',
            ])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function dashboard(): View
    {
        $upcomingAlerts = $this->getUpcomingMemberAlerts(30);
        $contactMessages = ContactMessage::latest()->take(10)->get();
        $memberStats = $this->buildMemberDashboardStats();
        $dashboardCounts = [
            'messages' => ContactMessage::count(),
            'gallery_photos' => GalleryPhoto::count(),
            'members' => $memberStats['total_members'],
            'events' => EventNews::where('type', 'event')->count(),
            'news' => EventNews::where('type', 'news')->count(),
        ];

        return view('admin.dashboard', compact('upcomingAlerts', 'contactMessages', 'dashboardCounts', 'memberStats'));
    }

    private function buildMemberDashboardStats(): array
    {
        $members = Member::select(['id', 'gender', 'date_of_birth', 'wedding_anniversary', 'email', 'phone'])->get();

        $totalMembers = $members->count();
        $maleMembers = $members->where('gender', 'male')->count();
        $femaleMembers = $members->where('gender', 'female')->count();
        $otherGenderMembers = $members->where('gender', 'other')->count();
        $marriedMembers = $members->filter(fn (Member $member) => !empty($member->wedding_anniversary))->count();
        $membersWithDob = $members->filter(fn (Member $member) => !empty($member->date_of_birth));
        $below18Members = $membersWithDob->filter(fn (Member $member) => $member->date_of_birth->age < 18)->count();
        $between18And59Members = $membersWithDob->filter(fn (Member $member) => $member->date_of_birth->age >= 18 && $member->date_of_birth->age < 60)->count();
        $seniorMembers = $membersWithDob->filter(fn (Member $member) => $member->date_of_birth->age >= 60)->count();
        $withEmail = $members->filter(fn (Member $member) => !empty(trim((string) $member->email)))->count();
        $withPhone = $members->filter(fn (Member $member) => !empty(trim((string) $member->phone)))->count();

        return [
            'total_members' => $totalMembers,
            'male_members' => $maleMembers,
            'female_members' => $femaleMembers,
            'other_gender_members' => $otherGenderMembers,
            'married_members' => $marriedMembers,
            'below_18_members' => $below18Members,
            'between_18_59_members' => $between18And59Members,
            'senior_members' => $seniorMembers,
            'with_email_members' => $withEmail,
            'with_phone_members' => $withPhone,
            'without_dob_members' => max($totalMembers - $membersWithDob->count(), 0),
            'male_percent' => $this->percentage($maleMembers, $totalMembers),
            'female_percent' => $this->percentage($femaleMembers, $totalMembers),
            'other_percent' => $this->percentage($otherGenderMembers, $totalMembers),
            'married_percent' => $this->percentage($marriedMembers, $totalMembers),
            'below_18_percent' => $this->percentage($below18Members, $totalMembers),
            'age_18_59_percent' => $this->percentage($between18And59Members, $totalMembers),
            'senior_percent' => $this->percentage($seniorMembers, $totalMembers),
            'with_email_percent' => $this->percentage($withEmail, $totalMembers),
            'with_phone_percent' => $this->percentage($withPhone, $totalMembers),
        ];
    }

    private function percentage(int $value, int $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($value / $total) * 100, 1);
    }

    public function contactMessages(): View
    {
        $contactMessages = ContactMessage::latest()->paginate(20);

        return view('admin.contact_messages', compact('contactMessages'));
    }

    public function customMessages(): View
    {
        $members = Member::orderBy('name')->get(['id', 'name', 'email', 'phone']);

        return view('admin.custom_messages', compact('members'));
    }

    public function sendCustomMessage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'channel' => ['required', 'in:email,whatsapp,both'],
            'send_to_all' => ['nullable', 'boolean'],
            'recipient_ids' => ['required_without:send_to_all', 'array'],
            'recipient_ids.*' => ['integer', 'exists:members,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $sendToAll = $request->boolean('send_to_all');

        $members = $sendToAll
            ? Member::orderBy('name')->get(['id', 'name', 'email', 'phone'])
            : Member::whereIn('id', $validated['recipient_ids'] ?? [])->orderBy('name')->get(['id', 'name', 'email', 'phone']);

        if ($members->isEmpty()) {
            return back()->withErrors([
                'recipient_ids' => 'Please select at least one valid recipient.',
            ])->withInput();
        }

        $attachmentPath = null;
        $attachmentOriginalName = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $attachmentPath = $this->storeCustomMessageAttachment($attachment);
            $attachmentOriginalName = $attachment->getClientOriginalName();
        }

        $channel = $validated['channel'];
        $shouldSendEmail = in_array($channel, ['email', 'both'], true);
        $shouldSendWhatsApp = in_array($channel, ['whatsapp', 'both'], true);

        $emailSent = 0;
        $emailSkipped = 0;
        $whatsAppSent = 0;
        $whatsAppSkipped = 0;
        $whatsAppFailed = 0;

        foreach ($members as $member) {
            if ($shouldSendEmail) {
                if (! empty($member->email)) {
                    Mail::send('emails.custom_member_message', [
                        'member' => $member,
                        'subjectLine' => $validated['subject'],
                        'description' => $validated['description'],
                        'attachmentName' => $attachmentOriginalName,
                    ], function ($message) use ($member, $validated, $attachmentPath, $attachmentOriginalName) {
                        $message->to($member->email, $member->name)->subject($validated['subject']);

                        if ($attachmentPath && $attachmentOriginalName) {
                            $message->attach(public_path($attachmentPath), [
                                'as' => $attachmentOriginalName,
                            ]);
                        }
                    });

                    $emailSent++;
                } else {
                    $emailSkipped++;
                }
            }

            if ($shouldSendWhatsApp) {
                $normalizedPhone = $this->normalizePhone((string) ($member->phone ?? ''));

                if ($normalizedPhone === '') {
                    $whatsAppSkipped++;
                    continue;
                }

                $whatsAppMessage = "Subject: {$validated['subject']}\n\n{$validated['description']}";
                $mediaUrl = $attachmentPath ? asset($attachmentPath) : null;

                if ($this->sendCustomWhatsAppMessage($normalizedPhone, $whatsAppMessage, $mediaUrl)) {
                    $whatsAppSent++;
                } else {
                    $whatsAppFailed++;
                }
            }
        }

        $statusParts = [];

        if ($shouldSendEmail) {
            $statusParts[] = "Email sent: {$emailSent}";
            if ($emailSkipped > 0) {
                $statusParts[] = "email skipped (no address): {$emailSkipped}";
            }
        }

        if ($shouldSendWhatsApp) {
            $statusParts[] = "WhatsApp sent: {$whatsAppSent}";
            if ($whatsAppSkipped > 0) {
                $statusParts[] = "WhatsApp skipped (no number): {$whatsAppSkipped}";
            }
            if ($whatsAppFailed > 0) {
                $statusParts[] = "WhatsApp failed: {$whatsAppFailed}";
            }
        }

        return back()->with('custom_message_success', 'Custom message processed. '.implode(' | ', $statusParts));
    }

    public function buyToken(): View
    {
        $members = Member::with('relatedMember')
            ->orderByRaw('CASE WHEN date_of_birth IS NULL THEN 1 ELSE 0 END')
            ->orderBy('date_of_birth')
            ->orderBy('name')
            ->get();
        $familyTree = $this->buildFamilyTree($members);

        return view('admin.members', compact('members', 'familyTree'));
    }

    public function storeMember(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'email' => ['nullable', 'email', 'max:255', 'unique:members,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'cropped_photo_data' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'wedding_anniversary' => ['nullable', 'date'],
            'related_member_id' => ['nullable', 'exists:members,id'],
            'relationship_to_other' => ['nullable', 'in:husband,wife,son,daughter'],
        ]);

        if (! empty($validated['cropped_photo_data'])) {
            $validated['photo_path'] = $this->storeMemberCroppedPhoto($validated['cropped_photo_data']);
        } elseif ($request->hasFile('photo')) {
            $validated['photo_path'] = $this->storeMemberPhoto($request->file('photo'));
        }

        unset($validated['photo']);
        unset($validated['cropped_photo_data']);

        Member::create($validated);

        return back()->with('member_success', 'Member added successfully.');
    }

    public function updateMember(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'email' => ['nullable', 'email', 'max:255', 'unique:members,email,'.$member->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'cropped_photo_data' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'wedding_anniversary' => ['nullable', 'date'],
            'related_member_id' => ['nullable', 'exists:members,id'],
            'relationship_to_other' => ['nullable', 'in:husband,wife,son,daughter'],
        ]);

        $relatedMemberId = isset($validated['related_member_id']) ? (int) $validated['related_member_id'] : null;

        if ($relatedMemberId !== null && $relatedMemberId === $member->id) {
            return back()->withErrors([
                'related_member_id' => 'Member cannot be related to itself.',
            ]);
        }

        if ($relatedMemberId !== null && $this->createsCircularRelation($member, $relatedMemberId)) {
            return back()->withErrors([
                'related_member_id' => 'This relation would create a circular tree.',
            ]);
        }

        if (! empty($validated['cropped_photo_data'])) {
            if ($member->photo_path && is_file(public_path($member->photo_path))) {
                unlink(public_path($member->photo_path));
            }

            $validated['photo_path'] = $this->storeMemberCroppedPhoto($validated['cropped_photo_data']);
        } elseif ($request->hasFile('photo')) {
            if ($member->photo_path && is_file(public_path($member->photo_path))) {
                unlink(public_path($member->photo_path));
            }

            $validated['photo_path'] = $this->storeMemberPhoto($request->file('photo'));
        }

        unset($validated['photo']);
        unset($validated['cropped_photo_data']);

        $member->update($validated);

        return back()->with('member_success', 'Member updated successfully.');
    }

    public function deleteMember(Member $member): RedirectResponse
    {
        Member::where('related_member_id', $member->id)->update([
            'related_member_id' => null,
            'relationship_to_other' => null,
        ]);

        if ($member->photo_path && is_file(public_path($member->photo_path))) {
            unlink(public_path($member->photo_path));
        }

        $member->delete();

        return back()->with('member_success', 'Member deleted successfully.');
    }

    public function icoDistribution(): View
    {
        $photos = GalleryPhoto::latest()->paginate(10);

        return view('admin.gallery', compact('photos'));
    }

    public function storeGalleryPhoto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'max:5120'],
        ]);

        $directory = public_path('uploads/gallery');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($validated['photos'] as $file) {
            $filename = uniqid('gallery_', true).'.'.$file->getClientOriginalExtension();

            $file->move($directory, $filename);

            GalleryPhoto::create([
                'path' => 'uploads/gallery/'.$filename,
            ]);
        }

        return back()->with('gallery_success', count($validated['photos']).' photo(s) added to gallery.');
    }

    public function deleteGalleryPhoto(GalleryPhoto $photo): RedirectResponse
    {
        $absolutePath = public_path($photo->path);

        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }

        $photo->delete();

        return back()->with('gallery_success', 'Photo deleted from gallery.');
    }

    public function transactions(): View
    {
        $items = EventNews::orderByDesc('item_date')->latest()->get();
        $events = $items->where('type', 'event')->values();
        $news = $items->where('type', 'news')->values();

        return view('admin.event_news', compact('items', 'events', 'news'));
    }

    public function storeEventNews(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:event,news'],
            'title' => ['required', 'string', 'max:255'],
            'item_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $this->storeEventNewsPhoto($request->file('photo'));
        }

        unset($validated['photo']);

        EventNews::create($validated);

        return back()->with('event_news_success', ucfirst($validated['type']).' added successfully.');
    }

    public function updateEventNews(Request $request, EventNews $item): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:event,news'],
            'title' => ['required', 'string', 'max:255'],
            'item_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('photo')) {
            if ($item->photo_path && is_file(public_path($item->photo_path))) {
                unlink(public_path($item->photo_path));
            }

            $validated['photo_path'] = $this->storeEventNewsPhoto($request->file('photo'));
        }

        unset($validated['photo']);

        $item->update($validated);

        return back()->with('event_news_success', ucfirst($validated['type']).' updated successfully.');
    }

    public function deleteEventNews(EventNews $item): RedirectResponse
    {
        if ($item->photo_path && is_file(public_path($item->photo_path))) {
            unlink(public_path($item->photo_path));
        }

        $item->delete();

        return back()->with('event_news_success', 'Entry deleted successfully.');
    }

    public function landingSlides(): View
    {
        $slides = HeroSlide::orderBy('sort_order')->latest()->get();

        return view('admin.landing_slides', compact('slides'));
    }

    public function storeLandingSlide(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['image_path'] = $this->storeLandingSlidePhoto($request->file('image'));
        $validated['is_active'] = (bool) $request->boolean('is_active', true);

        unset($validated['image']);

        HeroSlide::create($validated);

        return back()->with('landing_slide_success', 'Landing slide added successfully.');
    }

    public function updateLandingSlide(Request $request, HeroSlide $slide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($slide->image_path && is_file(public_path($slide->image_path))) {
                unlink(public_path($slide->image_path));
            }

            $validated['image_path'] = $this->storeLandingSlidePhoto($request->file('image'));
        }

        $validated['is_active'] = (bool) $request->boolean('is_active');

        unset($validated['image']);

        $slide->update($validated);

        return back()->with('landing_slide_success', 'Landing slide updated successfully.');
    }

    public function deleteLandingSlide(HeroSlide $slide): RedirectResponse
    {
        if ($slide->image_path && is_file(public_path($slide->image_path))) {
            unlink(public_path($slide->image_path));
        }

        $slide->delete();

        return back()->with('landing_slide_success', 'Landing slide deleted successfully.');
    }

    public function familyHistory(): View
    {
        $history = FamilyHistory::latest()->first();

        return view('admin.family_history', compact('history'));
    }

    public function updateFamilyHistory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $history = FamilyHistory::latest()->first() ?? new FamilyHistory();

        if ($request->hasFile('photo')) {
            if ($history->image_path && is_file(public_path($history->image_path))) {
                unlink(public_path($history->image_path));
            }

            $validated['image_path'] = $this->storeFamilyHistoryPhoto($request->file('photo'));
        }

        unset($validated['photo']);

        $history->fill($validated);
        $history->save();

        return back()->with('family_history_success', 'Family history updated successfully.');
    }

    public function profile(): View
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->update($validated);

        return back()->with('profile_updated', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('password_updated', 'Password changed successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

    private function getUpcomingMemberAlerts(int $withinDays = 30): Collection
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($withinDays);

        return Member::orderBy('name')
            ->get()
            ->flatMap(function (Member $member) use ($today, $endDate) {
                $alerts = collect();

                if ($member->date_of_birth) {
                    $currentYearBirthday = Carbon::create(
                        $today->year,
                        $member->date_of_birth->month,
                        $member->date_of_birth->day
                    );

                    if ($currentYearBirthday->lt($today)) {
                        $currentYearBirthday->addYear();
                    }

                    if ($currentYearBirthday->betweenIncluded($today, $endDate)) {
                        $alerts->push([
                            'member' => $member,
                            'type' => 'birthday',
                            'date' => $currentYearBirthday,
                            'days_left' => $today->diffInDays($currentYearBirthday, false),
                            'message' => $currentYearBirthday->isSameDay($today)
                                ? "Today is {$member->name}'s birthday 🎉"
                                : "{$member->name}'s birthday is in ".$today->diffInDays($currentYearBirthday)." day(s)",
                        ]);
                    }
                }

                if ($member->wedding_anniversary) {
                    $currentYearAnniversary = Carbon::create(
                        $today->year,
                        $member->wedding_anniversary->month,
                        $member->wedding_anniversary->day
                    );

                    if ($currentYearAnniversary->lt($today)) {
                        $currentYearAnniversary->addYear();
                    }

                    if ($currentYearAnniversary->betweenIncluded($today, $endDate)) {
                        $alerts->push([
                            'member' => $member,
                            'type' => 'anniversary',
                            'date' => $currentYearAnniversary,
                            'days_left' => $today->diffInDays($currentYearAnniversary, false),
                            'message' => $currentYearAnniversary->isSameDay($today)
                                ? "Today is {$member->name}'s wedding anniversary 💍"
                                : "{$member->name}'s wedding anniversary is in ".$today->diffInDays($currentYearAnniversary)." day(s)",
                        ]);
                    }
                }

                return $alerts;
            })
            ->sortBy('date')
            ->values();
    }

    private function storeMemberPhoto($photo): string
    {
        $directory = public_path('uploads/members');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('member_', true).'.'.$photo->getClientOriginalExtension();
        $photo->move($directory, $filename);

        return 'uploads/members/'.$filename;
    }

    private function storeMemberCroppedPhoto(string $photoData): string
    {
        if (! preg_match('/^data:image\/(png|jpe?g|webp);base64,/', $photoData, $matches)) {
            throw new \InvalidArgumentException('Invalid cropped photo data.');
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $photoData = preg_replace('/^data:image\/(png|jpe?g|webp);base64,/', '', $photoData);
        $binaryData = base64_decode(str_replace(' ', '+', $photoData), true);

        if ($binaryData === false) {
            throw new \InvalidArgumentException('Unable to decode cropped photo data.');
        }

        $directory = public_path('uploads/members');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('member_', true).'.'.$extension;
        file_put_contents($directory.'/'.$filename, $binaryData);

        return 'uploads/members/'.$filename;
    }

    private function storeEventNewsPhoto($photo): string
    {
        $directory = public_path('uploads/event-news');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('event_news_', true).'.'.$photo->getClientOriginalExtension();
        $photo->move($directory, $filename);

        return 'uploads/event-news/'.$filename;
    }

    private function storeFamilyHistoryPhoto($photo): string
    {
        $directory = public_path('uploads/family-history');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('family_history_', true).'.'.$photo->getClientOriginalExtension();
        $photo->move($directory, $filename);

        return 'uploads/family-history/'.$filename;
    }

    private function storeLandingSlidePhoto($photo): string
    {
        $directory = public_path('uploads/landing-slides');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('landing_slide_', true).'.'.$photo->getClientOriginalExtension();
        $photo->move($directory, $filename);

        return 'uploads/landing-slides/'.$filename;
    }

    private function storeCustomMessageAttachment($attachment): string
    {
        $directory = public_path('uploads/custom-messages');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('custom_message_', true).'.'.$attachment->getClientOriginalExtension();
        $attachment->move($directory, $filename);

        return 'uploads/custom-messages/'.$filename;
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

    private function sendCustomWhatsAppMessage(string $phone, string $message, ?string $mediaUrl = null): bool
    {
        $accountSid = trim((string) config('family_notifications.twilio_account_sid', ''));
        $authToken  = trim((string) config('family_notifications.twilio_auth_token', ''));
        $fromNumber = trim((string) config('family_notifications.twilio_whatsapp_from', ''));

        if ($accountSid === '' || $authToken === '' || $fromNumber === '') {
            Log::warning('Custom WhatsApp: Twilio credentials are not fully configured.', ['to' => $phone]);
            return false;
        }

        $payload = [
            'From' => 'whatsapp:'.$fromNumber,
            'To' => 'whatsapp:'.$phone,
            'Body' => $message,
        ];

        if ($mediaUrl) {
            $payload['MediaUrl'] = $mediaUrl;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        try {
            $response = Http::timeout(20)
                ->withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('Custom WhatsApp send failed (Twilio).', [
                'to' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Custom WhatsApp send exception (Twilio).', [
                'to' => $phone,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private function createsCircularRelation(Member $member, int $relatedMemberId): bool
    {
        $current = Member::find($relatedMemberId);

        while ($current) {
            if ($current->id === $member->id) {
                return true;
            }

            if (! $current->related_member_id) {
                return false;
            }

            $current = Member::find($current->related_member_id);
        }

        return false;
    }

    private function buildFamilyTree(Collection $members): array
    {
        $memberById = $members->keyBy('id');
        $spouses = [];
        $parentsOf = [];

        foreach ($members as $member) {
            if (! $member->related_member_id) {
                continue;
            }

            $relatedId = (int) $member->related_member_id;
            $relation = strtolower((string) $member->relationship_to_other);

            if ($this->isSpouseRelation($relation)) {
                $spouses[$member->id][] = $relatedId;
                $spouses[$relatedId][] = $member->id;

                continue;
            }

            if ($this->isChildRelation($relation)) {
                $parentsOf[$member->id][] = $relatedId;

                continue;
            }

            if ($this->isParentRelation($relation)) {
                $parentsOf[$relatedId][] = $member->id;

                continue;
            }

            $parentsOf[$member->id][] = $relatedId;
        }

        foreach ($spouses as $memberId => $spouseIds) {
            $spouses[$memberId] = collect($spouseIds)
                ->filter(fn (int $id) => isset($memberById[$id]))
                ->unique()
                ->values()
                ->all();
        }

        foreach ($parentsOf as $memberId => $parentIds) {
            $parentsOf[$memberId] = collect($parentIds)
                ->filter(fn (int $id) => isset($memberById[$id]))
                ->unique()
                ->take(2)
                ->values()
                ->all();
        }

        $rootIds = $members
            ->filter(fn (Member $member) => empty($parentsOf[$member->id]))
            ->pluck('id')
            ->values()
            ->all();

        if (empty($rootIds)) {
            $rootIds = $members->pluck('id')->values()->all();
        }

        $processed = [];
        $tree = [];

        foreach ($rootIds as $rootId) {
            if (isset($processed[$rootId])) {
                continue;
            }

            $node = $this->buildFamilyNode($rootId, $memberById, $spouses, $parentsOf, $processed, []);

            if ($node !== null) {
                $tree[] = $node;
            }
        }

        foreach ($members->pluck('id') as $memberId) {
            if (isset($processed[$memberId])) {
                continue;
            }

            $node = $this->buildFamilyNode($memberId, $memberById, $spouses, $parentsOf, $processed, []);

            if ($node !== null) {
                $tree[] = $node;
            }
        }

        return $tree;
    }

    private function buildFamilyGraph(Collection $members): array
    {
        $nodes = [];
        $edges = [];
        $spousePairs = [];

        foreach ($members as $member) {
            $relationCategory = $this->resolveRelationCategory($member);
            $nodeTheme = $this->relationCategoryTheme($relationCategory);
            $relationText = $member->relationship_to_other && $member->relatedMember
                ? ucfirst($member->relationship_to_other).' of '.$member->relatedMember->name
                : 'No linked relation';

            $nodes[] = [
                'id' => $member->id,
                'label' => $member->name,
                'shape' => $member->photo_path ? 'circularImage' : 'box',
                'image' => $member->photo_path ? asset($member->photo_path) : null,
                'size' => $member->photo_path ? 40 : null,
                'borderWidth' => 3,
                'color' => $nodeTheme,
                'font' => [
                    'color' => '#2c2c54',
                    'size' => 14,
                    'face' => 'Inter, Arial, sans-serif',
                    'bold' => [
                        'color' => '#1f2340',
                    ],
                ],
                'margin' => 12,
                'title' => '<div style="min-width:180px"><strong>'.e($member->name).'</strong><br>'.
                    'Relation: '.e($relationText).'<br>'.
                    'Email: '.e($member->email ?: 'N/A').'<br>'.
                    'Phone: '.e($member->phone ?: 'N/A').'<br>'.
                    'DOB: '.e(optional($member->date_of_birth)->format('Y-m-d') ?: 'N/A').'</div>',
                'member' => [
                    'name' => $member->name,
                    'email' => $member->email ?: 'N/A',
                    'phone' => $member->phone ?: 'N/A',
                    'dob' => optional($member->date_of_birth)->format('Y-m-d') ?: 'N/A',
                    'relation' => $relationText,
                    'photo' => $member->photo_path ? asset($member->photo_path) : null,
                    'category' => ucfirst($relationCategory),
                ],
            ];

            if (! $member->related_member_id) {
                continue;
            }

            $relatedId = (int) $member->related_member_id;
            $relation = strtolower((string) $member->relationship_to_other);

            if ($this->isSpouseRelation($relation)) {
                $pairKey = collect([$member->id, $relatedId])->sort()->implode('-');

                if (! isset($spousePairs[$pairKey])) {
                    $edges[] = [
                        'id' => 'spouse-'.$pairKey,
                        'from' => min($member->id, $relatedId),
                        'to' => max($member->id, $relatedId),
                        'label' => 'Partner',
                        'color' => ['color' => '#ff6b8b', 'highlight' => '#ff4f7a'],
                        'width' => 3,
                        'font' => ['color' => '#ff4f7a', 'strokeWidth' => 0, 'size' => 11],
                        'smooth' => ['enabled' => true, 'type' => 'curvedCW', 'roundness' => 0.12],
                        'dashes' => false,
                        'arrows' => ['to' => ['enabled' => false]],
                    ];

                    $spousePairs[$pairKey] = true;
                }

                continue;
            }

            if ($this->isChildRelation($relation)) {
                $edges[] = [
                    'from' => $relatedId,
                    'to' => $member->id,
                    'label' => ucfirst($member->relationship_to_other ?: 'Child'),
                    'color' => ['color' => '#7a5cff', 'highlight' => '#5f43e9'],
                    'width' => 2,
                ];

                continue;
            }

            if ($this->isParentRelation($relation)) {
                $edges[] = [
                    'from' => $member->id,
                    'to' => $relatedId,
                    'label' => ucfirst($member->relationship_to_other ?: 'Parent'),
                    'color' => ['color' => '#7a5cff', 'highlight' => '#5f43e9'],
                    'width' => 2,
                ];

                continue;
            }

            $edges[] = [
                'from' => $relatedId,
                'to' => $member->id,
                'label' => ucfirst($member->relationship_to_other ?: 'Relation'),
                'color' => ['color' => '#8f9bb3', 'highlight' => '#62708a'],
                'width' => 2,
                'dashes' => [6, 4],
            ];
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }

    private function resolveRelationCategory(Member $member): string
    {
        $relation = strtolower((string) $member->relationship_to_other);

        if ($relation === '') {
            return 'member';
        }

        if ($this->isSpouseRelation($relation)) {
            return 'partner';
        }

        if ($this->isParentRelation($relation)) {
            return 'parent';
        }

        if ($this->isChildRelation($relation)) {
            return 'child';
        }

        return 'relative';
    }

    private function relationCategoryTheme(string $category): array
    {
        return match ($category) {
            'partner' => [
                'background' => '#fff0f5',
                'border' => '#ff6b8b',
                'highlight' => [
                    'background' => '#ffe0eb',
                    'border' => '#ff4f7a',
                ],
            ],
            'parent' => [
                'background' => '#f2efff',
                'border' => '#7a5cff',
                'highlight' => [
                    'background' => '#e7e0ff',
                    'border' => '#5f43e9',
                ],
            ],
            'child' => [
                'background' => '#eef8ff',
                'border' => '#3f8cff',
                'highlight' => [
                    'background' => '#dff1ff',
                    'border' => '#2374ee',
                ],
            ],
            'relative' => [
                'background' => '#f5f7fb',
                'border' => '#8f9bb3',
                'highlight' => [
                    'background' => '#ebeff7',
                    'border' => '#62708a',
                ],
            ],
            default => [
                'background' => '#eefaf5',
                'border' => '#2bb673',
                'highlight' => [
                    'background' => '#def7ea',
                    'border' => '#159a5d',
                ],
            ],
        };
    }

    private function buildFamilyNode(
        int $memberId,
        Collection $memberById,
        array $spouses,
        array $parentsOf,
        array &$processed,
        array $ancestry
    ): ?array {
        if (! isset($memberById[$memberId])) {
            return null;
        }

        if (in_array($memberId, $ancestry, true)) {
            return null;
        }

        $member = $memberById[$memberId];
        $processed[$memberId] = true;

        $spouseId = collect($spouses[$memberId] ?? [])
            ->first(fn (int $candidateId) => isset($memberById[$candidateId]) && ! in_array($candidateId, $ancestry, true));

        $spouse = $spouseId ? $memberById[$spouseId] : null;

        if ($spouseId) {
            $processed[$spouseId] = true;
        }

        $parentPair = array_filter([$memberId, $spouseId]);
        $nextAncestry = array_values(array_unique(array_merge($ancestry, $parentPair)));

        $childrenIds = collect($parentsOf)
            ->filter(function (array $parentIds) use ($parentPair): bool {
                return ! empty(array_intersect($parentIds, $parentPair));
            })
            ->keys()
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => ! in_array($id, $parentPair, true))
            ->sortBy(fn (int $id): string => strtolower($memberById[$id]->name ?? ''))
            ->values()
            ->all();

        $children = [];

        foreach ($childrenIds as $childId) {
            $childNode = $this->buildFamilyNode($childId, $memberById, $spouses, $parentsOf, $processed, $nextAncestry);

            if ($childNode !== null) {
                $children[] = $childNode;
            }
        }

        return [
            'member' => $member,
            'spouse' => $spouse,
            'children' => $children,
        ];
    }

    private function isSpouseRelation(string $relation): bool
    {
        return str_contains($relation, 'wife')
            || str_contains($relation, 'husband')
            || str_contains($relation, 'spouse')
            || str_contains($relation, 'partner');
    }

    private function isChildRelation(string $relation): bool
    {
        return str_contains($relation, 'son')
            || str_contains($relation, 'daughter')
            || str_contains($relation, 'child');
    }

    private function isParentRelation(string $relation): bool
    {
        return str_contains($relation, 'father')
            || str_contains($relation, 'mother')
            || str_contains($relation, 'parent');
    }
}
