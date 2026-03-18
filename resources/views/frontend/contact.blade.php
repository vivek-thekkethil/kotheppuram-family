@extends('frontend.layouts.app')

@section('title', 'Contact – Kotheppuram Family')

@section('extra_css')
.contact-honeypot {
    position: absolute !important;
    left: -10000px !important;
    width: 1px !important;
    height: 1px !important;
    overflow: hidden !important;
}

.contact-toast {
    position: fixed;
    top: 92px;
    right: 20px;
    max-width: min(420px, calc(100vw - 24px));
    z-index: 1080;
    color: #fff;
    border-radius: 14px;
    padding: 12px 14px;
    box-shadow: 0 14px 34px rgba(26, 31, 70, 0.24);
    opacity: 0;
    transform: translateY(-10px);
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.contact-toast.is-open {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.contact-toast-success {
    background: linear-gradient(135deg, #23b26d 0%, #2dd485 100%);
}

.contact-toast-error {
    background: linear-gradient(135deg, #f35e75 0%, #ff7b93 100%);
}

.contact-toast-close {
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
}

.contact-toast-close:hover {
    opacity: 1;
}

@media (max-width: 767.98px) {
    .contact-toast {
        right: 12px;
        top: 82px;
    }
}
@endsection

@section('content')
@if (session('contact_success') || session('contact_error'))
    <div class="contact-toast {{ session('contact_success') ? 'contact-toast-success' : 'contact-toast-error' }}" id="contactToast" role="status" aria-live="polite">
        <div class="d-flex align-items-center gap-2">
            <i class="bi {{ session('contact_success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
            <span>{{ session('contact_success') ?: session('contact_error') }}</span>
            <button type="button" class="contact-toast-close" id="dismissContactToast" aria-label="Dismiss message">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
@endif

<div class="page-hero">
    <div class="container">
        <h1 class="mb-2">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 align-items-start">
        <div class="col-lg-5">
            <div class="p-4 rounded-3" style="background:linear-gradient(135deg,#f7f2ff 0%,#eef4ff 100%); border:1px solid rgba(122,92,255,0.12);">
                <h4 style="color:#2c2c54;">Get in Touch</h4>
                <p style="color:#666; line-height:1.8;">
                    Send us your message and we will get back to you.
                </p>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="p-4 p-md-5 rounded-3" style="background:#fff; border:1px solid rgba(122,92,255,0.12); box-shadow:0 10px 28px rgba(122,92,255,0.08);">
                <h4 class="mb-4" style="color:#2c2c54;">Send Message</h4>

                <form method="POST" action="{{ route('frontend.contact.send') }}" id="contactForm">
                    @csrf

                    <div class="contact-honeypot" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <input type="hidden" name="form_started_at" value="{{ now()->timestamp }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="subject">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary px-4 py-2" id="contactSubmitBtn">
                                <span class="contact-btn-default"><i class="bi bi-send me-1"></i>Send Message</span>
                                <span class="contact-btn-loading d-none"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        var form = document.getElementById('contactForm');
        var submitBtn = document.getElementById('contactSubmitBtn');
        var defaultContent = submitBtn ? submitBtn.querySelector('.contact-btn-default') : null;
        var loadingContent = submitBtn ? submitBtn.querySelector('.contact-btn-loading') : null;

        if (form && submitBtn && defaultContent && loadingContent) {
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                defaultContent.classList.add('d-none');
                loadingContent.classList.remove('d-none');
            });
        }

        var toast = document.getElementById('contactToast');
        var dismiss = document.getElementById('dismissContactToast');
        var toastTimer = null;

        function closeToast() {
            if (!toast) {
                return;
            }

            toast.classList.remove('is-open');
        }

        if (toast) {
            requestAnimationFrame(function () {
                toast.classList.add('is-open');
            });

            toastTimer = setTimeout(closeToast, 5000);
        }

        if (dismiss) {
            dismiss.addEventListener('click', function () {
                closeToast();
                if (toastTimer) {
                    clearTimeout(toastTimer);
                }
            });
        }
    })();
</script>
@endsection
