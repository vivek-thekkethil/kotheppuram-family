@extends('frontend.layouts.app')

@section('title', 'Gallery – Kotheppuram Family')

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <h1 class="mb-2">Family Gallery</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Gallery Grid --}}
<div class="container py-5">
    @if ($photos->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-images" style="font-size:4rem;color:#c5b8ff;"></i>
            <p class="mt-3" style="color:#888;">No photos in the gallery yet. Check back soon!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($photos as $index => $photo)
                <div class="col-6 col-md-4 col-lg-3 wow fadeIn" data-wow-delay="0.1s">
                    <div class="overflow-hidden rounded-3 position-relative gallery-thumb"
                         style="height:220px; cursor:pointer;"
                         data-src="{{ asset($photo->path) }}"
                         data-index="{{ $index }}">
                        <img src="{{ asset($photo->path) }}"
                             alt="Family Photo"
                             class="w-100 h-100"
                             style="object-fit:cover; transition:transform 0.35s;">
                        <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                             style="background:rgba(122,92,255,0.55); opacity:0; transition:opacity 0.3s;">
                            <i class="bi bi-zoom-in text-white" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($photos->hasPages())
            <div class="d-flex justify-content-center mt-5">
                <nav>
                    <ul class="pagination">
                        {{-- Previous --}}
                        @if ($photos->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" style="border-radius:8px 0 0 8px;">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $photos->previousPageUrl() }}" style="border-radius:8px 0 0 8px;">&laquo;</a>
                            </li>
                        @endif

                        @foreach ($photos->getUrlRange(1, $photos->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $photos->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}"
                                   style="{{ $page == $photos->currentPage() ? 'background:#7a5cff;border-color:#7a5cff;color:#fff;' : '' }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endforeach

                        {{-- Next --}}
                        @if ($photos->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $photos->nextPageUrl() }}" style="border-radius:0 8px 8px 0;">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link" style="border-radius:0 8px 8px 0;">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    @endif
</div>

{{-- Lightbox Modal --}}
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            <button type="button" id="galleryPrevBtn" class="btn btn-light position-absolute top-50 start-0 translate-middle-y ms-2 rounded-circle shadow" style="width:44px;height:44px;z-index:2;">
                <i class="bi bi-chevron-left"></i>
            </button>
            <img id="galleryModalImg" src="" alt="" class="img-fluid rounded-3 w-100">
            <button type="button" id="galleryNextBtn" class="btn btn-light position-absolute top-50 end-0 translate-middle-y me-2 rounded-circle shadow" style="width:44px;height:44px;z-index:2;">
                <i class="bi bi-chevron-right"></i>
            </button>
            <div class="text-center text-white mt-3" id="galleryModalCounter" style="font-size:14px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var galleryItems = Array.from(document.querySelectorAll('.gallery-thumb'));
    var galleryModalElement = document.getElementById('galleryModal');
    var galleryModalImage = document.getElementById('galleryModalImg');
    var galleryCounter = document.getElementById('galleryModalCounter');
    var galleryPrevBtn = document.getElementById('galleryPrevBtn');
    var galleryNextBtn = document.getElementById('galleryNextBtn');
    var galleryModal = new bootstrap.Modal(galleryModalElement);
    var currentGalleryIndex = 0;

    function updateGalleryModal(index) {
        if (!galleryItems.length) {
            return;
        }

        if (index < 0) {
            index = galleryItems.length - 1;
        }

        if (index >= galleryItems.length) {
            index = 0;
        }

        currentGalleryIndex = index;

        var activeItem = galleryItems[currentGalleryIndex];
        galleryModalImage.src = activeItem.getAttribute('data-src');
        galleryCounter.textContent = (currentGalleryIndex + 1) + ' / ' + galleryItems.length;
    }

    galleryItems.forEach(function (el) {
        el.addEventListener('mouseenter', function () {
            this.querySelector('img').style.transform = 'scale(1.08)';
            this.querySelector('.gallery-overlay').style.opacity = '1';
        });
        el.addEventListener('mouseleave', function () {
            this.querySelector('img').style.transform = 'scale(1)';
            this.querySelector('.gallery-overlay').style.opacity = '0';
        });
        el.addEventListener('click', function () {
            updateGalleryModal(Number(this.getAttribute('data-index')));
            galleryModal.show();
        });
    });

    if (galleryPrevBtn) {
        galleryPrevBtn.addEventListener('click', function () {
            updateGalleryModal(currentGalleryIndex - 1);
        });
    }

    if (galleryNextBtn) {
        galleryNextBtn.addEventListener('click', function () {
            updateGalleryModal(currentGalleryIndex + 1);
        });
    }

    document.addEventListener('keydown', function (event) {
        if (!galleryModalElement.classList.contains('show')) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            updateGalleryModal(currentGalleryIndex - 1);
        }

        if (event.key === 'ArrowRight') {
            updateGalleryModal(currentGalleryIndex + 1);
        }
    });
</script>
@endsection
