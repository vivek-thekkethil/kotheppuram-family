@extends('frontend.layouts.app')

@section('title', 'Family History – Kotheppuram')

@section('content')
<div class="page-hero">
    <div class="container">
        <h1 class="mb-2">Family History</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Family History</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    @if (!$history)
        <div class="text-center py-5">
            <i class="bi bi-journal-richtext" style="font-size:4rem;color:#c5b8ff;"></i>
            <p class="mt-3" style="color:#888;">Family history will be published soon.</p>
        </div>
    @else
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                @if ($history->image_path)
                    <img src="{{ asset($history->image_path) }}" alt="Family History"
                         class="img-fluid rounded-3"
                         style="width:100%; max-height:460px; object-fit:cover; box-shadow:0 16px 36px rgba(122,92,255,0.18);">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                         style="min-height:360px; background:linear-gradient(135deg,#f5f0ff,#eef4ff);">
                        <i class="bi bi-journal-richtext" style="font-size:4rem;color:#c5b8ff;"></i>
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <h2 class="mb-2 section-title" style="color:#2c2c54;">{{ $history->title }}</h2>
                @if ($history->subtitle)
                    <h5 class="mb-4" style="color:#7a5cff;">{{ $history->subtitle }}</h5>
                @endif
                <div style="font-size:15px; line-height:1.9; color:#555; white-space:pre-line;">
                    {{ $history->content }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
