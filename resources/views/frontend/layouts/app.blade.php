<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Kotheppuram Family')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('assets/frontent/img/favicon.ico') }}" rel="icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/frontent/css/style.css') }}" rel="stylesheet">

    <style>
        /* ─── Global overrides ─── */
        :root {
            --primary: #7a5cff;
        }
        body { font-family: 'Open Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Space Grotesk', sans-serif; }

        /* ─── Navbar ─── */
        .site-navbar {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 16px rgba(122,92,255,0.08);
        }
        .site-navbar .navbar-brand h1 {
            font-size: 1.6rem;
            color: var(--primary);
            margin: 0;
            letter-spacing: 1px;
        }
        .site-navbar .nav-link {
            font-weight: 600;
            color: #2c2c54 !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .site-navbar .nav-link:hover,
        .site-navbar .nav-link.active {
            background: rgba(122,92,255,0.09);
            color: var(--primary) !important;
        }

        .site-navbar .navbar-toggler {
            border: 1px solid rgba(122, 92, 255, 0.35);
            border-radius: 8px;
            padding: 6px 8px;
            box-shadow: none;
        }

        .site-navbar .navbar-toggler:focus {
            box-shadow: 0 0 0 0.12rem rgba(122, 92, 255, 0.2);
        }

        .site-navbar .navbar-toggler-icon {
            width: 1.25rem;
            height: 1.25rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2844,44,84,0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ─── Page Hero ─── */
        .page-hero {
            padding: 80px 0 60px;
            background: linear-gradient(135deg, #f5f0ff 0%, #eef4ff 100%);
            border-bottom: 1px solid rgba(122,92,255,0.1);
        }
        .page-hero h1 { color: #2c2c54; }
        .page-hero .breadcrumb-item a { color: var(--primary); text-decoration: none; }

        /* ─── Section heading ─── */
        .section-title span {
            background: rgba(122,92,255,0.08);
            color: var(--primary);
            border-radius: 6px;
            padding: 2px 10px;
        }

        /* ─── Footer ─── */
        .site-footer {
            background: #1a1a2e;
            color: #aaa;
        }
        .site-footer h5 { color: #fff; }
        .site-footer a { color: #bbb; text-decoration: none; }
        .site-footer a:hover { color: var(--primary); }
        .site-footer .social-btn {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center; justify-content: center;
            border: 1px solid rgba(255,255,255,0.2);
            color: #ccc;
            transition: all 0.2s;
            margin-right: 6px;
        }
        .site-footer .social-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .today-alert-item {
            padding: 12px;
            border-radius: 10px;
        }

        .today-alert-item-birthday {
            background: rgba(122, 92, 255, 0.08);
        }

        .today-alert-item-anniversary {
            background: rgba(255, 107, 139, 0.08);
        }

        @yield('extra_css')
    </style>

    @yield('head')
</head>

<body>

    <!-- Spinner -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top site-navbar px-0">
        <div class="container">
            <a class="navbar-brand" href="{{ route('frontend.home') }}">
                <h1>KOTHEPPURAM</h1>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.gallery') ? 'active' : '' }}" href="{{ route('frontend.gallery') }}">
                            <i class="bi bi-images me-1"></i>Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.events') ? 'active' : '' }}" href="{{ route('frontend.events') }}">
                            <i class="bi bi-calendar-event me-1"></i>Events & News
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" href="{{ route('frontend.contact') }}">
                            <i class="bi bi-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    @yield('content')

    @if (isset($todayAlerts) && $todayAlerts->isNotEmpty())
        <div class="modal fade" id="todayAlertModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius:16px; overflow:hidden;">
                    <div class="modal-header" style="background:linear-gradient(135deg,#7a5cff,#8f76ff); color:#fff;">
                        <h5 class="modal-title mb-0"><i class="bi bi-bell-fill me-2"></i>Today's Family Alerts</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-3" style="color:#666;">Here are today's celebrations in the family:</p>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($todayAlerts as $alert)
                                <div class="d-flex align-items-start today-alert-item {{ $alert['type'] === 'birthday' ? 'today-alert-item-birthday' : 'today-alert-item-anniversary' }}">
                                    <span class="me-2" style="font-size:20px; line-height:1;">{{ $alert['icon'] }}</span>
                                    <div>
                                        <div style="font-weight:700; color:#2c2c54;">{{ $alert['member']->name }}</div>
                                        <div style="font-size:13px; color:#666;">{{ $alert['message'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer" style="background:#fafbff;">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Great!</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <footer class="site-footer pt-5 mt-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5>KOTHEPPURAM</h5>
                    <p class="mt-3" style="font-size:14px;">A family portal to celebrate our roots, share memories, and stay connected across generations.</p>
                    <div class="mt-3">
                        <a href="#!" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#!" class="social-btn"><i class="fab fa-instagram"></i></a>
                        <a href="#!" class="social-btn"><i class="fab fa-whatsapp"></i></a>
                        <a href="#!" class="social-btn"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled mt-3" style="font-size:14px;">
                        <li class="mb-2"><a href="{{ route('frontend.home') }}"><i class="bi bi-chevron-right me-1" style="color:var(--primary)"></i>Home</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.members') }}"><i class="bi bi-chevron-right me-1" style="color:var(--primary)"></i>Family Members</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.family-history') }}"><i class="bi bi-chevron-right me-1" style="color:var(--primary)"></i>Family History</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.gallery') }}"><i class="bi bi-chevron-right me-1" style="color:var(--primary)"></i>Gallery</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.events') }}"><i class="bi bi-chevron-right me-1" style="color:var(--primary)"></i>Events & News</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.contact') }}"><i class="bi bi-chevron-right me-1" style="color:var(--primary)"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Family Portal</h5>
                    <p class="mt-3" style="font-size:14px;">Stay connected with your family. View the family tree, explore memories in the gallery, and never miss an event or celebration.</p>
                </div>
            </div>
        </div>
        <div class="border-top border-secondary mt-4 py-3 text-center" style="font-size:13px;">
            &copy; {{ date('Y') }} Kotheppuram Family. All rights reserved.
        </div>
    </footer>

    <!-- Back to Top -->
    <a href="#!" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="{{ asset('assets/frontent/js/main.js') }}"></script>

    @if (isset($todayAlerts) && $todayAlerts->isNotEmpty())
        <script>
            window.addEventListener('load', function () {
                var modalElement = document.getElementById('todayAlertModal');
                if (!modalElement) {
                    return;
                }

                var storageKey = 'family_today_alert_seen_session';
                var alreadySeenInSession = sessionStorage.getItem(storageKey) === '1';

                if (alreadySeenInSession) {
                    return;
                }

                var alertModal = new bootstrap.Modal(modalElement);
                alertModal.show();

                sessionStorage.setItem(storageKey, '1');
            });
        </script>
    @endif

    @yield('scripts')
</body>

</html>
