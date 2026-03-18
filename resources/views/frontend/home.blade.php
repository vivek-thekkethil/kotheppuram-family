@extends('frontend.layouts.app')

@section('title', 'Kotheppuram Family – Home')

@section('extra_css')
.celebration-fab {
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1040;
    border: 0;
    border-radius: 18px 0 0 18px;
    background: linear-gradient(135deg, #7a5cff 0%, #9a86ff 100%);
    color: #fff;
    width: 52px;
    height: 56px;
    padding: 0;
    box-shadow: 0 16px 32px rgba(122, 92, 255, 0.26);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.celebration-fab:hover {
    color: #fff;
}

.celebration-overlay {
    position: fixed;
    inset: 0;
    background: rgba(18, 21, 42, 0.45);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
    z-index: 1045;
}

.celebration-overlay.is-open {
    opacity: 1;
    visibility: visible;
}

.celebration-drawer {
    position: fixed;
    top: 0;
    right: 0;
    width: min(420px, 100vw);
    height: 100vh;
    background: linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
    box-shadow: -18px 0 48px rgba(24, 28, 58, 0.18);
    transform: translateX(100%);
    transition: transform 0.28s ease;
    z-index: 1050;
    display: flex;
    flex-direction: column;
}

.celebration-drawer.is-open {
    transform: translateX(0);
}

.celebration-drawer-header {
    padding: 22px 22px 16px;
    border-bottom: 1px solid rgba(122, 92, 255, 0.1);
    background: linear-gradient(135deg, #f7f2ff 0%, #eef4ff 100%);
}

.celebration-drawer-body {
    padding: 18px;
    overflow: auto;
}

.celebration-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 18px;
    background: #fff;
    border: 1px solid rgba(122, 92, 255, 0.1);
    box-shadow: 0 12px 24px rgba(122, 92, 255, 0.08);
}

.celebration-icon {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

@media (max-width: 767.98px) {
    .celebration-fab {
        width: 46px;
        height: 50px;
        font-size: 19px;
    }
}
@endsection

@section('content')

{{-- ── Hero ── --}}
<div class="container-fluid pb-5 hero-header bg-light mb-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center mb-5">
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                <h1 class="display-2 mb-4" style="color:#2c2c54; font-family:'Space Grotesk',sans-serif;">
                    Welcome to<br><span style="color:#7a5cff;">Kotheppuram</span> Family
                </h1>
                <p class="lead mb-4" style="color:#555;">
                    Celebrating our roots, preserving memories, and staying connected across generations.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('frontend.members') }}" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-people me-2"></i>Meet the Family
                    </a>
                    <a href="{{ route('frontend.gallery') }}" class="btn btn-outline-primary px-4 py-2">
                        <i class="bi bi-images me-2"></i>View Gallery
                    </a>
                </div>
            </div>
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.4s">
                @if ($heroSlides->isNotEmpty())
                    <div class="owl-carousel header-carousel">
                        @foreach ($heroSlides as $slide)
                            <div class="position-relative rounded-3 overflow-hidden">
                                <img class="img-fluid" src="{{ asset($slide->image_path) }}" alt="{{ $slide->title }}"
                                     style="height:340px; width:100%; object-fit:cover;">
                                <div class="position-absolute w-100 h-100 top-0 start-0 d-flex align-items-end"
                                     style="background:linear-gradient(180deg, rgba(0,0,0,0.1) 20%, rgba(0,0,0,0.65) 100%);">
                                    <div class="p-3 p-md-4">
                                        <h5 class="text-white mb-1" style="font-weight:700;">{{ $slide->title }}</h5>
                                        @if ($slide->subtitle)
                                            <p class="text-white mb-0" style="font-size:13px; opacity:0.95;">{{ $slide->subtitle }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif ($galleryPreview->isNotEmpty())
                    <div class="owl-carousel header-carousel">
                        @foreach ($galleryPreview->take(4) as $photo)
                            <div class="position-relative rounded-3 overflow-hidden">
                                  <img class="img-fluid" src="{{ asset($photo->path) }}" alt="Family Photo"
                                     style="height:340px; width:100%; object-fit:cover;">
                                <div class="position-absolute w-100 h-100 top-0 start-0 d-flex align-items-end"
                                     style="background:linear-gradient(180deg, rgba(0,0,0,0.1) 20%, rgba(0,0,0,0.65) 100%);">
                                    <div class="p-3 p-md-4">
                                        <h5 class="text-white mb-1" style="font-weight:700;">Family Moments</h5>
                                        <p class="text-white mb-0" style="font-size:13px; opacity:0.95;">Memories captured across generations</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                         style="height:340px; background:linear-gradient(135deg,#f5f0ff,#eef4ff);">
                        <div class="text-center">
                            <i class="bi bi-image" style="font-size:4rem; color:#c5b8ff;"></i>
                            <p class="mt-2" style="color:#888;">Gallery photos will appear here</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Stats strip --}}
        <div class="row g-4 wow fadeIn" data-wow-delay="0.6s">
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2"
                         style="width:50px;height:50px;background:rgba(122,92,255,0.1);">
                        <i class="bi bi-people-fill" style="color:#7a5cff; font-size:1.4rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#2c2c54;">{{ \App\Models\Member::count() }}</h5>
                        <small style="color:#888;">Family Members</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2"
                         style="width:50px;height:50px;background:rgba(255,107,139,0.1);">
                        <i class="bi bi-images" style="color:#ff6b8b; font-size:1.4rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#2c2c54;">{{ \App\Models\GalleryPhoto::count() }}</h5>
                        <small style="color:#888;">Gallery Photos</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2"
                         style="width:50px;height:50px;background:rgba(40,199,111,0.1);">
                        <i class="bi bi-calendar-check" style="color:#28c76f; font-size:1.4rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#2c2c54;">{{ \App\Models\EventNews::where('type','event')->count() }}</h5>
                        <small style="color:#888;">Events</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2"
                         style="width:50px;height:50px;background:rgba(255,165,0,0.1);">
                        <i class="bi bi-newspaper" style="color:#ffa500; font-size:1.4rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#2c2c54;">{{ \App\Models\EventNews::where('type','news')->count() }}</h5>
                        <small style="color:#888;">News</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Upcoming Celebrations Drawer Trigger ── --}}
@if ($upcomingCelebrations->isNotEmpty())
<button type="button" class="celebration-fab" id="openCelebrationDrawer" aria-label="Open upcoming celebrations" title="Upcoming Celebrations">
    <i class="bi bi-gift-fill"></i>
</button>

<div class="celebration-overlay" id="celebrationOverlay"></div>

<aside class="celebration-drawer" id="celebrationDrawer" aria-hidden="true">
    <div class="celebration-drawer-header d-flex justify-content-between align-items-start gap-3">
        <div>
            <h4 class="mb-1" style="color:#2c2c54;">Upcoming Celebrations</h4>
            <p class="mb-0" style="color:#7f849d; font-size:14px;">Birthdays and anniversaries coming in the next 30 days.</p>
        </div>
        <button type="button" class="btn btn-light rounded-circle" id="closeCelebrationDrawer" style="width:40px;height:40px;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="celebration-drawer-body">
        <div class="d-flex flex-column gap-3">
            @foreach ($upcomingCelebrations as $celebration)
                <div class="celebration-card">
                    <div class="celebration-icon" style="background:{{ $celebration['type'] === 'birthday' ? 'rgba(122,92,255,0.1)' : 'rgba(255,107,139,0.1)' }};">
                        {{ $celebration['icon'] }}
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.08em; color:{{ $celebration['type'] === 'birthday' ? '#7a5cff' : '#ff6b8b' }}; font-weight:700;">
                            {{ $celebration['type'] === 'birthday' ? 'Birthday' : 'Anniversary' }}
                        </div>
                        <h6 class="mb-1" style="color:#2c2c54; font-weight:700;">{{ $celebration['member']->name }}</h6>
                        <div style="font-size:13px; color:#7f849d;">{{ $celebration['date']->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:22px; font-weight:800; color:{{ $celebration['type'] === 'birthday' ? '#7a5cff' : '#ff6b8b' }}; line-height:1;">
                            {{ $celebration['days_left'] }}
                        </div>
                        <div style="font-size:11px; color:#8d91aa;">{{ $celebration['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</aside>
@endif

{{-- ── Family History Preview ── --}}
@if ($familyHistory)
<div class="container-fluid py-5" style="background:linear-gradient(135deg,#f8f6ff 0%,#eef4ff 100%);">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                @if ($familyHistory->image_path)
                    <img src="{{ asset($familyHistory->image_path) }}" alt="Family History"
                         class="img-fluid rounded-3"
                         style="width:100%; max-height:360px; object-fit:cover; box-shadow:0 12px 30px rgba(122,92,255,0.16);">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                         style="min-height:320px; background:linear-gradient(135deg,#f5f0ff,#eef4ff);">
                        <i class="bi bi-journal-richtext" style="font-size:4rem;color:#c5b8ff;"></i>
                    </div>
                @endif
            </div>
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.3s">
                <h2 class="mb-3 section-title" style="color:#2c2c54;">{{ $familyHistory->title }}</h2>
                @if ($familyHistory->subtitle)
                    <h5 class="mb-3" style="color:#7a5cff;">{{ $familyHistory->subtitle }}</h5>
                @endif
                <p style="color:#666; line-height:1.8;">
                    {{ Str::limit($familyHistory->content, 280) }}
                </p>
                <a href="{{ route('frontend.family-history') }}" class="btn btn-primary px-4 py-2">
                    Read Full History <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Members Preview ── --}}
@if ($memberPreview->isNotEmpty())
<div class="container-fluid py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <h2 class="mb-0 section-title" style="color:#2c2c54;">Our <span>Family Members</span></h2>
            <a href="{{ route('frontend.members') }}" class="btn btn-outline-primary btn-sm">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach ($memberPreview as $member)
                <div class="col-6 col-md-4 col-lg-3 wow fadeIn" data-wow-delay="0.1s">
                    <div class="card border-0 text-center h-100"
                         style="border-radius:16px; box-shadow:0 8px 24px rgba(122,92,255,0.1);">
                        <div class="card-body p-4">
                            @if ($member->photo_path)
                                <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }}"
                                     class="rounded-circle mb-3"
                                     style="width:80px;height:80px;object-fit:cover;border:3px solid rgba(122,92,255,0.2);">
                            @else
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width:80px;height:80px;background:linear-gradient(135deg,#f5f0ff,#eef4ff);border:3px solid rgba(122,92,255,0.15);">
                                    <i class="bi bi-person-fill" style="font-size:2rem;color:#c5b8ff;"></i>
                                </div>
                            @endif
                            <h6 class="mb-1" style="color:#2c2c54; font-weight:700;">{{ $member->name }}</h6>
                            @if ($member->relatedMember)
                                <small style="color:#888;">
                                    {{ $member->relationship_to_other ?? 'Related' }} of {{ $member->relatedMember->name }}
                                </small>
                            @endif
                            @if ($member->date_of_birth)
                                <div class="mt-2">
                                    <small style="color:#aaa;"><i class="bi bi-cake2 me-1"></i>{{ $member->date_of_birth->format('d M') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Gallery Preview ── --}}
@if ($galleryPreview->isNotEmpty())
<div class="container-fluid py-5" style="background:linear-gradient(135deg,#f9f7ff 0%,#f0f4ff 100%);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <h2 class="mb-0 section-title" style="color:#2c2c54;">Family <span>Gallery</span></h2>
            <a href="{{ route('frontend.gallery') }}" class="btn btn-outline-primary btn-sm">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach ($galleryPreview as $photo)
                <div class="col-6 col-md-4 col-lg-2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="overflow-hidden rounded-3" style="height:140px;">
                                <img src="{{ asset($photo->path) }}" alt="Gallery"
                             class="w-100 h-100"
                             style="object-fit:cover; transition:transform 0.3s;"
                             onmouseover="this.style.transform='scale(1.08)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Events & News Preview ── --}}
@if ($eventsPreview->isNotEmpty() || $newsPreview->isNotEmpty())
<div class="container-fluid py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <h2 class="mb-0 section-title" style="color:#2c2c54;">Events <span>& News</span></h2>
            <a href="{{ route('frontend.events') }}" class="btn btn-outline-primary btn-sm">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        @if ($eventsPreview->isNotEmpty())
        <h5 class="mb-4" style="color:#7a5cff;"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h5>
        <div class="row g-4 mb-5">
            @foreach ($eventsPreview as $event)
            <div class="col-md-4 wow fadeIn" data-wow-delay="0.1s">
                <div class="card border-0 h-100"
                     style="border-radius:16px; box-shadow:0 8px 24px rgba(122,92,255,0.08); overflow:hidden;">
                    @if ($event->photo_path)
                        <img src="{{ asset($event->photo_path) }}" alt="{{ $event->title }}"
                             style="height:180px; object-fit:cover; width:100%;">
                    @else
                        <div class="d-flex align-items-center justify-content-center"
                             style="height:180px;background:linear-gradient(135deg,#f5f0ff,#eef4ff);">
                            <i class="bi bi-calendar-event" style="font-size:3rem;color:#c5b8ff;"></i>
                        </div>
                    @endif
                    <div class="card-body p-4">
                        @if ($event->item_date)
                            <small class="badge mb-2" style="background:rgba(122,92,255,0.1);color:#7a5cff;font-size:11px;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $event->item_date->format('d M Y') }}
                            </small>
                        @endif
                        <h6 class="mb-2" style="color:#2c2c54;font-weight:700;">{{ $event->title }}</h6>
                        @if ($event->description)
                            <p class="mb-0" style="font-size:13px;color:#777;line-height:1.5;">
                                {{ Str::limit($event->description, 90) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if ($newsPreview->isNotEmpty())
        <h5 class="mb-4" style="color:#ff6b8b;"><i class="bi bi-newspaper me-2"></i>Latest News</h5>
        <div class="row g-4">
            @foreach ($newsPreview as $news)
            <div class="col-md-4 wow fadeIn" data-wow-delay="0.2s">
                <div class="card border-0 h-100"
                     style="border-radius:16px; box-shadow:0 8px 24px rgba(255,107,139,0.08); overflow:hidden;">
                    @if ($news->photo_path)
                        <img src="{{ asset($news->photo_path) }}" alt="{{ $news->title }}"
                             style="height:180px; object-fit:cover; width:100%;">
                    @else
                        <div class="d-flex align-items-center justify-content-center"
                             style="height:180px;background:linear-gradient(135deg,#fff5f7,#ffeef4);">
                            <i class="bi bi-newspaper" style="font-size:3rem;color:#ffb8c8;"></i>
                        </div>
                    @endif
                    <div class="card-body p-4">
                        @if ($news->item_date)
                            <small class="badge mb-2" style="background:rgba(255,107,139,0.1);color:#ff6b8b;font-size:11px;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $news->item_date->format('d M Y') }}
                            </small>
                        @endif
                        <h6 class="mb-2" style="color:#2c2c54;font-weight:700;">{{ $news->title }}</h6>
                        @if ($news->description)
                            <p class="mb-0" style="font-size:13px;color:#777;line-height:1.5;">
                                {{ Str::limit($news->description, 90) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    (function () {
        var openBtn = document.getElementById('openCelebrationDrawer');
        var closeBtn = document.getElementById('closeCelebrationDrawer');
        var drawer = document.getElementById('celebrationDrawer');
        var overlay = document.getElementById('celebrationOverlay');

        if (!openBtn || !closeBtn || !drawer || !overlay) {
            return;
        }

        function openDrawer() {
            drawer.classList.add('is-open');
            overlay.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', openDrawer);
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDrawer();
            }
        });
    })();
</script>
@endsection
