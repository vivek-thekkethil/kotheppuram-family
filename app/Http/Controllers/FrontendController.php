<?php

namespace App\Http\Controllers;

use App\Models\EventNews;
use App\Models\FamilyHistory;
use App\Models\GalleryPhoto;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $heroSlides = HeroSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();
        $galleryPreview = GalleryPhoto::latest()->take(6)->get();
        $memberPreview  = Member::orderByRaw('CASE WHEN date_of_birth IS NULL THEN 1 ELSE 0 END')
            ->orderBy('date_of_birth')
            ->orderBy('name')
            ->take(8)
            ->get();
        $eventsPreview  = EventNews::where('type', 'event')->latest('item_date')->take(3)->get();
        $newsPreview    = EventNews::where('type', 'news')->latest('item_date')->take(3)->get();
        $familyHistory  = FamilyHistory::latest()->first();
        $todayAlerts    = $this->getTodayMemberAlerts();
        $upcomingCelebrations = $this->getUpcomingMemberAlerts(30)->take(6);

        return view('frontend.home', compact(
            'heroSlides',
            'galleryPreview',
            'memberPreview',
            'eventsPreview',
            'newsPreview',
            'familyHistory',
            'todayAlerts',
            'upcomingCelebrations'
        ));
    }

    public function familyHistory(): View
    {
        $history = FamilyHistory::latest()->first();
        $todayAlerts = $this->getTodayMemberAlerts();

        return view('frontend.family_history', compact('history', 'todayAlerts'));
    }

    public function gallery(): View
    {
        $photos = GalleryPhoto::latest()->paginate(12);
        $todayAlerts = $this->getTodayMemberAlerts();

        return view('frontend.gallery', compact('photos', 'todayAlerts'));
    }

    public function events(): View
    {
        $events = EventNews::where('type', 'event')->latest('item_date')->paginate(9, ['*'], 'events_page');
        $news   = EventNews::where('type', 'news')->latest('item_date')->paginate(9, ['*'], 'news_page');
        $todayAlerts = $this->getTodayMemberAlerts();

        return view('frontend.events', compact('events', 'news', 'todayAlerts'));
    }

    public function members(): View
    {
        $members = Member::with('relatedMember')
            ->orderByRaw('CASE WHEN date_of_birth IS NULL THEN 1 ELSE 0 END')
            ->orderBy('date_of_birth')
            ->orderBy('name')
            ->get();
        $familyTree = $this->buildFamilyTree($members);
        $todayAlerts = $this->getTodayMemberAlerts();

        return view('frontend.members', compact('members', 'familyTree', 'todayAlerts'));
    }

    public function contact(): View
    {
        $todayAlerts = $this->getTodayMemberAlerts();

        return view('frontend.contact', compact('todayAlerts'));
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $honeypot = trim((string) $request->input('website', ''));
        if ($honeypot !== '') {
            return back()->with('contact_success', 'Your message has been sent successfully.');
        }

        $startedAt = (int) $request->input('form_started_at');
        if ($startedAt <= 0 || (now()->timestamp - $startedAt) < 3) {
            return back()
                ->withInput()
                ->with('contact_error', 'Please wait a moment before sending your message.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            Mail::send('emails.contact_submission', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subjectLine' => $validated['subject'],
                'messageBody' => $validated['message'],
            ], function ($message) use ($validated) {
                $message->to('viveknair97k@gmail.com')
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('Family Website Contact: '.$validated['subject']);
            });
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('contact_error', 'Unable to send message right now. Please check mail configuration.');
        }

        return back()->with('contact_success', 'Your message has been sent successfully.');
    }

    private function getTodayMemberAlerts(): Collection
    {
        $today = Carbon::today();

        return Member::orderBy('name')
            ->get()
            ->flatMap(function (Member $member) use ($today) {
                $alerts = collect();

                if ($member->date_of_birth
                    && $member->date_of_birth->month === $today->month
                    && $member->date_of_birth->day === $today->day) {
                    $alerts->push([
                        'type' => 'birthday',
                        'icon' => '🎂',
                        'member' => $member,
                        'message' => "Today is {$member->name}'s birthday",
                    ]);
                }

                if ($member->wedding_anniversary
                    && $member->wedding_anniversary->month === $today->month
                    && $member->wedding_anniversary->day === $today->day) {
                    $alerts->push([
                        'type' => 'anniversary',
                        'icon' => '💍',
                        'member' => $member,
                        'message' => "Today is {$member->name}'s wedding anniversary",
                    ]);
                }

                return $alerts;
            })
            ->values();
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
                    $birthday = Carbon::create($today->year, $member->date_of_birth->month, $member->date_of_birth->day);

                    if ($birthday->lt($today)) {
                        $birthday->addYear();
                    }

                    if ($birthday->betweenIncluded($today, $endDate)) {
                        $alerts->push([
                            'type' => 'birthday',
                            'icon' => '🎂',
                            'member' => $member,
                            'date' => $birthday,
                            'days_left' => $today->diffInDays($birthday),
                            'label' => $birthday->isSameDay($today) ? 'Today' : $today->diffInDays($birthday).' day(s) left',
                        ]);
                    }
                }

                if ($member->wedding_anniversary) {
                    $anniversary = Carbon::create($today->year, $member->wedding_anniversary->month, $member->wedding_anniversary->day);

                    if ($anniversary->lt($today)) {
                        $anniversary->addYear();
                    }

                    if ($anniversary->betweenIncluded($today, $endDate)) {
                        $alerts->push([
                            'type' => 'anniversary',
                            'icon' => '💍',
                            'member' => $member,
                            'date' => $anniversary,
                            'days_left' => $today->diffInDays($anniversary),
                            'label' => $anniversary->isSameDay($today) ? 'Today' : $today->diffInDays($anniversary).' day(s) left',
                        ]);
                    }
                }

                return $alerts;
            })
            ->sortBy('date')
            ->values();
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
