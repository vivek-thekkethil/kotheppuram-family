@extends('frontend.layouts.app')

@section('title', 'Family Members – Kotheppuram')

@section('extra_css')
.family-tree-stage {
    overflow: auto;
    padding: 16px;
    border-radius: 22px;
    background: radial-gradient(circle at top left, rgba(122, 92, 255, 0.14), transparent 28%), linear-gradient(180deg, #ffffff 0%, #f7f8ff 100%);
    border: 1px solid rgba(122, 92, 255, 0.12);
    box-shadow: 0 20px 60px rgba(122, 92, 255, 0.12);
    user-select: none;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
    max-height: 75vh;
}

.family-tree-toolbar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.family-tree-toolbtn {
    border: 0;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #fff;
    color: #5e49ca;
    box-shadow: 0 10px 24px rgba(122, 92, 255, 0.14);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 800;
    line-height: 1;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.family-tree-toolbtn:hover {
    transform: translateY(-2px);
    background: #f7f4ff;
    box-shadow: 0 14px 28px rgba(122, 92, 255, 0.18);
}

.family-tree-zoom-label {
    min-width: 60px;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: #7a5cff;
    padding: 9px 10px;
    border-radius: 999px;
    background: rgba(122, 92, 255, 0.1);
}

.family-tree-canvas {
    position: relative;
    display: inline-block;
    min-width: 100%;
}

.family-tree-scale {
    position: absolute;
    top: 0;
    left: 0;
    transform-origin: top left;
}

.family-tree-public,
.family-tree-public ul {
    margin: 0;
    padding: 0;
    list-style: none;
    text-align: center;
}

.family-tree-public {
    display: inline-block;
    min-width: max-content;
    padding: 6px 12px 12px;
}

.family-tree-public li {
    position: relative;
    display: inline-block;
    vertical-align: top;
    padding: 20px 8px 0;
}

.family-tree-public li::before,
.family-tree-public li::after {
    content: '';
    position: absolute;
    top: 0;
    width: 50%;
    height: 20px;
    border-top: 2px solid #d8ddff;
}

.family-tree-public li::before {
    right: 50%;
    border-right: 2px solid #d8ddff;
}

.family-tree-public li::after {
    left: 50%;
    border-left: 2px solid #d8ddff;
}

.family-tree-public li:only-child::before,
.family-tree-public li:only-child::after,
.family-tree-public > li::before,
.family-tree-public > li::after,
.family-tree-public ul::before {
    display: none !important;
}

.family-tree-public li:first-child::before,
.family-tree-public li:last-child::after {
    border: 0;
}

.family-tree-public li:last-child::before {
    border-right: 2px solid #d8ddff;
    border-radius: 0 10px 0 0;
}

.family-tree-public li:first-child::after {
    border-radius: 10px 0 0 0;
}

.family-tree-public ul {
    position: relative;
    padding-top: 12px;
}

.ft-branch-card {
    display: inline-flex;
    align-items: stretch;
    gap: 8px;
    padding: 8px;
    border-radius: 18px;
    background: linear-gradient(180deg, rgba(122, 92, 255, 0.08), rgba(255, 255, 255, 0.6));
    box-shadow: 0 12px 30px rgba(122, 92, 255, 0.1);
}

.ft-person-card {
    width: 170px;
    padding: 10px;
    text-align: left;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfbff 100%);
    border: 1px solid rgba(122, 92, 255, 0.12);
    box-shadow: 0 12px 28px rgba(50, 57, 105, 0.08);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}

.ft-person-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 34px rgba(122, 92, 255, 0.16);
    z-index: 20;
}

.ft-spouse-card {
    border-color: rgba(255, 107, 139, 0.18);
    background: linear-gradient(180deg, #fff9fb 0%, #ffffff 100%);
}

.ft-person-top {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.ft-avatar,
.ft-avatar-placeholder {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    flex-shrink: 0;
}

.ft-avatar {
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 6px 16px rgba(122, 92, 255, 0.18);
}

.ft-avatar-placeholder {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f4efff, #eaf2ff);
    color: #7a5cff;
    font-size: 16px;
    border: 2px dashed rgba(122, 92, 255, 0.22);
}

.member-photo-hover,
.ft-photo-hover {
    position: relative;
    display: inline-flex;
    z-index: 1;
}

.member-photo-hover:hover,
.ft-photo-hover:hover {
    z-index: 100;
}

.member-photo-preview,
.ft-photo-preview {
    position: absolute;
    left: 50%;
    top: calc(100% + 12px);
    transform: translateX(-50%) scale(0.92);
    width: 170px;
    height: 170px;
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    border: 3px solid #fff;
    box-shadow: 0 18px 40px rgba(25, 31, 72, 0.24);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.22s ease, transform 0.22s ease;
    z-index: 30;
}

.member-photo-preview img,
.ft-photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-photo-hover:hover .member-photo-preview,
.ft-photo-hover:hover .ft-photo-preview {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) scale(1);
}

.member-photo-hover:hover img.rounded-circle,
.ft-photo-hover:hover .ft-avatar {
    transform: scale(1.06);
}

.ft-name {
    color: #22264d;
    font-weight: 700;
    font-size: 13px;
    line-height: 1.25;
}

.ft-meta {
    color: #8b8fa7;
    font-size: 10px;
}

.ft-badge {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 600;
    color: #6c56d8;
    padding: 5px 8px;
    border-radius: 999px;
    background: rgba(122, 92, 255, 0.08);
}

.ft-badge-love {
    color: #e55479;
    background: rgba(255, 107, 139, 0.1);
}

.ft-link-heart {
    position: relative;
    align-self: center;
    width: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ff6b8b;
    font-size: 11px;
}

.ft-link-heart::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    border-top: 2px solid #ffabc0;
    transform: translateY(-50%);
}

.ft-link-heart i {
    position: relative;
    z-index: 1;
    background: #fff;
    border-radius: 50%;
    padding: 4px;
}

@media (max-width: 767.98px) {
    .ft-person-card {
        width: 148px;
    }

    .family-tree-stage {
        padding: 12px;
        max-height: 60vh;
    }

    .family-tree-toolbtn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }
}
@endsection

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <h1 class="mb-2">Family Members</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Members</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">

    @if ($members->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-people" style="font-size:4rem;color:#c5b8ff;"></i>
            <p class="mt-3" style="color:#888;">No family members have been added yet. Check back soon!</p>
        </div>
    @else

        {{-- Search / Filter --}}
        <div class="mb-5 d-flex align-items-center gap-3 flex-wrap">
                     <input type="text" id="memberSearch" class="form-control" placeholder="Search by name…"
                   style="max-width:320px; border-radius:10px; border:1px solid rgba(122,92,255,0.25);">
                     <span style="color:#aaa; font-size:13px;">{{ $members->count() }} member(s)</span>
        </div>

        <div class="row g-4" id="membersGrid">
            @foreach ($members as $member)
                <div class="col-sm-6 col-md-4 col-lg-3 member-card wow fadeIn" data-wow-delay="0.1s"
                     data-name="{{ strtolower($member->name) }}">
                    <div class="card border-0 h-100 text-center"
                        style="border-radius:18px; box-shadow:0 8px 32px rgba(122,92,255,0.1); overflow:visible; transition:transform 0.25s, box-shadow 0.25s;"
                         onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(122,92,255,0.18)';"
                         onmouseout="this.style.transform='';this.style.boxShadow='0 8px 32px rgba(122,92,255,0.1)';">

                        {{-- Photo Strip --}}
                        <div style="height:8px; background:linear-gradient(90deg, #7a5cff, #ff6b8b);"></div>

                        <div class="card-body p-4 pt-6 position-relative" style="padding-top: 64px !important;">
                            {{-- Avatar --}}
                            <div class="position-absolute top-0 start-50 translate-middle" style="margin-top: 14px; z-index: 2;">
                                @if ($member->photo_path)
                                    <span class="member-photo-hover">
                                        <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }}"
                                             class="rounded-circle"
                                             style="width:72px;height:72px;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 14px rgba(122,92,255,0.22); transition:transform 0.22s ease;">
                                        <span class="member-photo-preview">
                                            <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }} full photo">
                                        </span>
                                    </span>
                                @else
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                         style="width:72px;height:72px;background:linear-gradient(135deg,#f5f0ff,#eef4ff);border:4px solid #fff;box-shadow:0 4px 14px rgba(122,92,255,0.18);">
                                        <i class="bi bi-person-fill" style="font-size:2rem;color:#c5b8ff;"></i>
                                    </div>
                                @endif
                            </div>

                            <h6 class="mt-4 mb-1" style="color:#2c2c54;font-weight:700;font-size:15px;">{{ $member->name }}</h6>

                            @if ($member->relatedMember)
                                <p class="mb-2" style="font-size:12px;color:#7a5cff;">
                                    {{ $member->relationship_to_other ?? 'Related' }} of {{ $member->relatedMember->name }}
                                </p>
                            @endif

                            <hr style="border-color:rgba(122,92,255,0.1); margin:10px 0;">

                            <div class="d-flex flex-column gap-1" style="font-size:12px; color:#888;">
                                @if ($member->date_of_birth)
                                    <span><i class="bi bi-cake2 me-1" style="color:#7a5cff;"></i>
                                        Birthday: {{ $member->date_of_birth->format('d M Y') }}
                                    </span>
                                @endif
                                @if ($member->wedding_anniversary)
                                    <span><i class="bi bi-heart-fill me-1" style="color:#ff6b8b;"></i>
                                        Anniversary: {{ $member->wedding_anniversary->format('d M Y') }}
                                    </span>
                                @endif
                                @if ($member->phone)
                                    <span><i class="bi bi-telephone me-1" style="color:#28c76f;"></i>{{ $member->phone }}</span>
                                @endif
                                @if ($member->email)
                                    <span class="text-truncate"><i class="bi bi-envelope me-1" style="color:#ffa500;"></i>{{ $member->email }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- No results message --}}
        <div id="noResults" class="text-center py-5" style="display:none;">
            <i class="bi bi-search" style="font-size:3rem;color:#c5b8ff;"></i>
            <p class="mt-3" style="color:#888;">No members found matching your search.</p>
        </div>

        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
                <div>
                    <h2 class="mb-2 section-title" style="color:#2c2c54;">Our<span>Family Tree</span></h2>
                    <p class="mb-0" style="color:#7f849d; max-width:700px;">Easily find your relatives and explore your family connections through our interactive family tree.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <span class="badge rounded-pill" style="background:rgba(122,92,255,0.1); color:#7a5cff; padding:10px 14px; font-size:12px;">Swipe to scroll</span>
                    <div class="family-tree-toolbar">
                        <button type="button" class="family-tree-toolbtn" id="treeZoomOut" aria-label="Zoom out" title="Zoom out">
                            −
                        </button>
                        <div class="family-tree-zoom-label" id="treeZoomLabel">100%</div>
                        <button type="button" class="family-tree-toolbtn" id="treeZoomIn" aria-label="Zoom in" title="Zoom in">
                            +
                        </button>
                        <button type="button" class="family-tree-toolbtn" id="treeReset" aria-label="Reset view" title="Reset view">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button type="button" class="family-tree-toolbtn" id="treeCenter" aria-label="Center tree" title="Center tree">
                            <i class="bi bi-bullseye"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if (!empty($familyTree))
                <div class="family-tree-stage" id="familyTreeStage">
                    <div class="family-tree-canvas" id="familyTreeCanvas">
                        <div class="family-tree-scale" id="familyTreeScale">
                            <ul class="family-tree-public" id="familyTreeRoot">
                                @foreach($familyTree as $node)
                                    @include('frontend.partials.family-tree-node', ['node' => $node])
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    (function () {
        var searchInput = document.getElementById('memberSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var query = this.value.toLowerCase().trim();
                var cards = document.querySelectorAll('.member-card');
                var visible = 0;

                cards.forEach(function (card) {
                    var name = card.getAttribute('data-name');
                    if (name.includes(query)) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                var noResults = document.getElementById('noResults');
                if (noResults) {
                    noResults.style.display = (visible === 0) ? 'block' : 'none';
                }
            });
        }

        var stage = document.getElementById('familyTreeStage');
        var canvas = document.getElementById('familyTreeCanvas');
        var scaleLayer = document.getElementById('familyTreeScale');
        var tree = document.getElementById('familyTreeRoot');
        var zoomInBtn = document.getElementById('treeZoomIn');
        var zoomOutBtn = document.getElementById('treeZoomOut');
        var resetBtn = document.getElementById('treeReset');
        var centerBtn = document.getElementById('treeCenter');
        var zoomLabel = document.getElementById('treeZoomLabel');

        if (!stage || !canvas || !scaleLayer || !tree) {
            return;
        }

        var minScale = 0.65;
        var maxScale = 1.8;
        var step = 0.15;
        var scale = 1;
        var baseWidth = 0;
        var baseHeight = 0;

        function clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        }

        function measureTree() {
            scaleLayer.style.transform = 'scale(1)';
            canvas.style.width = 'auto';
            canvas.style.height = 'auto';
            baseWidth = tree.offsetWidth;
            baseHeight = tree.offsetHeight;
            updateScale(scale, false);
        }

        function updateLabel() {
            if (zoomLabel) {
                zoomLabel.textContent = Math.round(scale * 100) + '%';
            }
        }

        function centerTree(smooth) {
            var left = Math.max((canvas.offsetWidth - stage.clientWidth) / 2, 0);
            var top = Math.max((canvas.offsetHeight - stage.clientHeight) / 2, 0);

            if (typeof stage.scrollTo === 'function') {
                stage.scrollTo({
                    left: left,
                    top: top,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            } else {
                stage.scrollLeft = left;
                stage.scrollTop = top;
            }
        }

        function updateScale(nextScale, preserveCenter) {
            var previousScale = scale;
            var targetScale = clamp(nextScale, minScale, maxScale);
            var centerRatioX = 0.5;
            var centerRatioY = 0.5;

            if (preserveCenter) {
                centerRatioX = (stage.scrollLeft + (stage.clientWidth / 2)) / Math.max(canvas.offsetWidth || 1, 1);
                centerRatioY = (stage.scrollTop + (stage.clientHeight / 2)) / Math.max(canvas.offsetHeight || 1, 1);
            }

            scale = targetScale;
            scaleLayer.style.transform = 'scale(' + scale + ')';
            canvas.style.width = Math.max(baseWidth * scale, stage.clientWidth) + 'px';
            canvas.style.height = Math.max(baseHeight * scale, stage.clientHeight) + 'px';
            updateLabel();

            if (preserveCenter && previousScale !== targetScale) {
                stage.scrollLeft = Math.max((canvas.offsetWidth * centerRatioX) - (stage.clientWidth / 2), 0);
                stage.scrollTop = Math.max((canvas.offsetHeight * centerRatioY) - (stage.clientHeight / 2), 0);
            }
        }

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', function () {
                updateScale(scale + step, true);
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function () {
                updateScale(scale - step, true);
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                scale = 1;
                updateScale(1, false);
                centerTree(true);
            });
        }

        if (centerBtn) {
            centerBtn.addEventListener('click', function () {
                centerTree(true);
            });
        }

        window.addEventListener('resize', function () {
            measureTree();
            centerTree(false);
        });

        measureTree();
        centerTree(false);
    })();
</script>
@endsection
