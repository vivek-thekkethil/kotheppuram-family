@extends('frontend.layouts.app')

@section('title', 'Events & News – Kotheppuram Family')

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <h1 class="mb-2">Events & News</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Events & News</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">

    {{-- Tab toggles --}}
    <ul class="nav nav-pills mb-5 gap-2" id="eventTabs">
        <li class="nav-item">
            <button class="nav-link active px-4 py-2 rounded-pill" data-tab="events"
                    style="background:#7a5cff; border:none; color:#fff; font-weight:600;">
                <i class="bi bi-calendar-event me-2"></i>Events
                <span class="badge ms-1" style="background:rgba(255,255,255,0.25);">{{ $events->total() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 py-2 rounded-pill" data-tab="news"
                    style="background:rgba(255,107,139,0.1); border:none; color:#ff6b8b; font-weight:600;">
                <i class="bi bi-newspaper me-2"></i>News
                <span class="badge ms-1" style="background:rgba(255,107,139,0.2); color:#ff6b8b;">{{ $news->total() }}</span>
            </button>
        </li>
    </ul>

    {{-- Events Panel --}}
    <div id="panel-events">
        @if ($events->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x" style="font-size:4rem;color:#c5b8ff;"></i>
                <p class="mt-3" style="color:#888;">No events found.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($events as $event)
                    <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                        <div class="card border-0 h-100"
                             style="border-radius:16px; box-shadow:0 8px 24px rgba(122,92,255,0.08); overflow:hidden;">
                            @if ($event->photo_path)
                                <img src="{{ asset($event->photo_path) }}" alt="{{ $event->title }}"
                                     style="height:200px; object-fit:cover; width:100%;">
                            @else
                                <div class="d-flex align-items-center justify-content-center"
                                     style="height:200px;background:linear-gradient(135deg,#f5f0ff,#eef4ff);">
                                    <i class="bi bi-calendar-event" style="font-size:3.5rem;color:#c5b8ff;"></i>
                                </div>
                            @endif
                            <div class="card-body p-4">
                                @if ($event->item_date)
                                    <span class="badge mb-3" style="background:rgba(122,92,255,0.1);color:#7a5cff;font-size:11px;padding:6px 12px;border-radius:20px;">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $event->item_date->format('d M Y') }}
                                    </span>
                                @endif
                                <h5 class="mb-2" style="color:#2c2c54;font-weight:700;">{{ $event->title }}</h5>
                                @if ($event->description)
                                    <p class="mb-0" style="font-size:14px;color:#777;line-height:1.6;">
                                        {{ Str::limit($event->description, 150) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Events Pagination --}}
            @if ($events->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <nav>
                        <ul class="pagination">
                            @if ($events->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $events->previousPageUrl() }}">&laquo;</a></li>
                            @endif
                            @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $events->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}"
                                       style="{{ $page == $events->currentPage() ? 'background:#7a5cff;border-color:#7a5cff;' : '' }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            @if ($events->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $events->nextPageUrl() }}">&raquo;</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        @endif
    </div>

    {{-- News Panel --}}
    <div id="panel-news" style="display:none;">
        @if ($news->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-newspaper" style="font-size:4rem;color:#ffb8c8;"></i>
                <p class="mt-3" style="color:#888;">No news found.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($news as $article)
                    <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                        <div class="card border-0 h-100"
                             style="border-radius:16px; box-shadow:0 8px 24px rgba(255,107,139,0.08); overflow:hidden;">
                            @if ($article->photo_path)
                                <img src="{{ asset($article->photo_path) }}" alt="{{ $article->title }}"
                                     style="height:200px; object-fit:cover; width:100%;">
                            @else
                                <div class="d-flex align-items-center justify-content-center"
                                     style="height:200px;background:linear-gradient(135deg,#fff5f7,#ffeef4);">
                                    <i class="bi bi-newspaper" style="font-size:3.5rem;color:#ffb8c8;"></i>
                                </div>
                            @endif
                            <div class="card-body p-4">
                                @if ($article->item_date)
                                    <span class="badge mb-3" style="background:rgba(255,107,139,0.1);color:#ff6b8b;font-size:11px;padding:6px 12px;border-radius:20px;">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $article->item_date->format('d M Y') }}
                                    </span>
                                @endif
                                <h5 class="mb-2" style="color:#2c2c54;font-weight:700;">{{ $article->title }}</h5>
                                @if ($article->description)
                                    <p class="mb-0" style="font-size:14px;color:#777;line-height:1.6;">
                                        {{ Str::limit($article->description, 150) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- News Pagination --}}
            @if ($news->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <nav>
                        <ul class="pagination">
                            @if ($news->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $news->previousPageUrl() }}">&laquo;</a></li>
                            @endif
                            @foreach ($news->getUrlRange(1, $news->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $news->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}"
                                       style="{{ $page == $news->currentPage() ? 'background:#ff6b8b;border-color:#ff6b8b;' : '' }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            @if ($news->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $news->nextPageUrl() }}">&raquo;</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = this.getAttribute('data-tab');

            // toggle panels
            document.getElementById('panel-events').style.display = (tab === 'events') ? 'block' : 'none';
            document.getElementById('panel-news').style.display   = (tab === 'news')   ? 'block' : 'none';

            // toggle button styles
            document.querySelectorAll('[data-tab]').forEach(function (b) {
                b.style.background = '';
                b.style.color      = '';
                b.classList.remove('active');
            });

            if (tab === 'events') {
                this.style.background = '#7a5cff';
                this.style.color      = '#fff';
            } else {
                this.style.background = 'rgba(255,107,139,0.15)';
                this.style.color      = '#ff6b8b';
            }
            this.classList.add('active');
        });
    });
</script>
@endsection
