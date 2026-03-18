<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Admin dashboard">
    <link rel="shortcut icon" href="{{ asset('assets/image/favicon.png') }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .admin-toast-stack {
            position: fixed;
            top: 84px;
            right: 20px;
            z-index: 1200;
            width: min(420px, calc(100vw - 24px));
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .admin-toast {
            color: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 14px 34px rgba(26, 31, 70, 0.24);
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .admin-toast.is-open {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .admin-toast-success {
            background: linear-gradient(135deg, #23b26d 0%, #2dd485 100%);
        }

        .admin-toast-error {
            background: linear-gradient(135deg, #f35e75 0%, #ff7b93 100%);
        }

        .admin-toast-close {
            border: 0;
            background: transparent;
            color: #fff;
            opacity: 0.9;
            padding: 0;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            cursor: pointer;
        }

        .admin-toast-close:hover {
            opacity: 1;
        }

        .admin-delete-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(243, 94, 117, 0.12);
            color: #f35e75;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 12px;
        }

        .topbar-logo-text {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
            line-height: 1;
            text-transform: uppercase;
        }

        @media (max-width: 767.98px) {
            .topbar-logo-text {
                font-size: 22px;
            }
        }

        @media (max-width: 767.98px) {
            .admin-toast-stack {
                right: 12px;
                top: 76px;
            }
        }
    </style>
    @yield('head')
</head>

<body class="page-user">
    <div class="topbar-wrap">
        <div class="topbar is-sticky">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="topbar-nav d-lg-none">
                        <li class="topbar-nav-item relative">
                            <a class="toggle-nav" href="#">
                                <div class="toggle-icon">
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <a class="topbar-logo" href="{{ route('admin.dashboard') }}">
                        <span class="topbar-logo-text">KOTHEPPURAM</span>
                    </a>
                    <ul class="topbar-nav">
                        <li class="topbar-nav-item relative">
                            <span class="user-welcome d-none d-lg-inline-block">Welcome! {{ auth()->user()->name }}</span>
                            <a class="toggle-tigger user-thumb" href="#"><em class="ti ti-user"></em></a>
                            <div class="toggle-class dropdown-content dropdown-content-right dropdown-arrow-right user-dropdown">
                                <div class="user-status">
                                    <h6 class="user-status-title">Total Members</h6>
                                    <div class="user-status-balance">{{ \App\Models\Member::count() }}</div>
                                </div>
                                <ul class="user-links">
                                    <li><a href="{{ route('admin.profile') }}"><i class="ti ti-id-badge"></i>My Profile</a></li>
                                    <li><a href="{{ route('admin.custom-messages') }}"><i class="ti ti-email"></i>Custom Messages</a></li>
                                </ul>
                                <ul class="user-links bg-light">
                                    <li>
                                        <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                            <i class="ti ti-power-off"></i>Logout
                                        </a>
                                        <form id="admin-logout-form" method="POST" action="{{ route('admin.logout') }}" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li><!-- .topbar-nav-item -->
                    </ul><!-- .topbar-nav -->
                </div>
            </div>
        </div>

        <div class="navbar">
            <div class="container">
                <div class="navbar-innr">
                    <ul class="navbar-menu">
                        <li><a href="{{ route('admin.dashboard') }}"><em class="ikon ikon-dashboard"></em> Dashboard</a></li>
                        <li><a href="{{ route('admin.members') }}"><em class="ikon ikon-coins"></em> Members</a></li>
                        <li><a href="{{ route('admin.landing-slides') }}"><em class="ikon ikon-distribution"></em> Landing Slides</a></li>
                        <li><a href="{{ route('admin.family-history') }}"><em class="ikon ikon-user"></em> Family History</a></li>
                        <li><a href="{{ route('admin.gallery') }}"><em class="ikon ikon-distribution"></em> Gallery</a></li>
                        <li><a href="{{ route('admin.event-news') }}"><em class="ikon ikon-transactions"></em> Events & News</a></li>
                        <li><a href="{{ route('admin.contact-messages') }}"><em class="ikon ikon-user"></em> Contact Messages</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @php
        $adminSuccessMessages = collect([
            session('member_success'),
            session('gallery_success'),
            session('event_news_success'),
            session('landing_slide_success'),
            session('family_history_success'),
            session('custom_message_success'),
            session('profile_updated'),
            session('password_updated'),
        ])->filter(fn ($message) => is_string($message) && trim($message) !== '')->values();

        $adminErrorMessage = null;

        if ($errors->any()) {
            $adminErrorMessage = $errors->first();
        } elseif (session('error')) {
            $adminErrorMessage = (string) session('error');
        }
    @endphp

    @if ($adminSuccessMessages->isNotEmpty() || !empty($adminErrorMessage))
        <div class="admin-toast-stack" id="adminToastStack">
            @foreach ($adminSuccessMessages as $successMessage)
                <div class="admin-toast admin-toast-success" role="status" aria-live="polite">
                    <div class="d-flex align-items-center gap-2">
                        <em class="ti ti-check-box"></em>
                        <span>{{ $successMessage }}</span>
                        <button type="button" class="admin-toast-close js-admin-toast-close" aria-label="Dismiss message">
                            <em class="ti ti-close"></em>
                        </button>
                    </div>
                </div>
            @endforeach

            @if (!empty($adminErrorMessage))
                <div class="admin-toast admin-toast-error" role="alert" aria-live="assertive">
                    <div class="d-flex align-items-center gap-2">
                        <em class="ti ti-alert"></em>
                        <span>{{ $adminErrorMessage }}</span>
                        <button type="button" class="admin-toast-close js-admin-toast-close" aria-label="Dismiss message">
                            <em class="ti ti-close"></em>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="modal fade" id="global-delete-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="global-delete-confirm-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="global-delete-confirm-title">Confirm Delete</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="admin-delete-icon">
                        <em class="ti ti-trash"></em>
                    </div>
                    <p class="mb-0" id="global-delete-confirm-message">Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="global-delete-confirm-submit">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <div class="footer-bar">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12">
                    <div class="text-center pdt-0-5x pdb-0-5x">
                        <div class="copyright-text">&copy; 2026 Kotheppuram Family. All rights reserved.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        (function () {
            var stack = document.getElementById('adminToastStack');
            if (!stack) {
                return;
            }

            var toasts = Array.from(stack.querySelectorAll('.admin-toast'));

            function closeToast(toast) {
                if (!toast) {
                    return;
                }

                toast.classList.remove('is-open');

                setTimeout(function () {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }

                    if (!stack.querySelector('.admin-toast')) {
                        stack.remove();
                    }
                }, 260);
            }

            requestAnimationFrame(function () {
                toasts.forEach(function (toast, index) {
                    setTimeout(function () {
                        toast.classList.add('is-open');
                    }, index * 60);

                    setTimeout(function () {
                        closeToast(toast);
                    }, 5200 + (index * 120));
                });
            });

            stack.querySelectorAll('.js-admin-toast-close').forEach(function (button) {
                button.addEventListener('click', function () {
                    closeToast(this.closest('.admin-toast'));
                });
            });
        })();

        (function () {
            var modalElement = window.jQuery ? window.jQuery('#global-delete-confirm-modal') : null;
            var titleElement = document.getElementById('global-delete-confirm-title');
            var messageElement = document.getElementById('global-delete-confirm-message');
            var confirmButton = document.getElementById('global-delete-confirm-submit');
            var targetForm = null;

            document.addEventListener('submit', function (event) {
                var form = event.target;

                if (!form || form.getAttribute('data-delete-confirm') !== 'true') {
                    return;
                }

                event.preventDefault();

                if (!modalElement || !titleElement || !messageElement || !confirmButton) {
                    if (window.confirm(form.getAttribute('data-delete-message') || 'Are you sure you want to delete this item?')) {
                        form.submit();
                    }
                    return;
                }

                targetForm = form;
                titleElement.textContent = form.getAttribute('data-delete-title') || 'Confirm Delete';
                messageElement.textContent = form.getAttribute('data-delete-message') || 'Are you sure you want to delete this item?';
                modalElement.modal('show');
            });

            if (confirmButton) {
                confirmButton.addEventListener('click', function () {
                    if (!targetForm) {
                        return;
                    }

                    var formToSubmit = targetForm;
                    targetForm = null;

                    if (modalElement) {
                        modalElement.modal('hide');
                    }

                    formToSubmit.submit();
                });
            }
        })();

    </script>
    @yield('scripts')
</body>

</html>