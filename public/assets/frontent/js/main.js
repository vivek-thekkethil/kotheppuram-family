(function ($) {
    "use strict";

    /* ── Spinner ─────────────────────────────────────────── */
    var spinner = function () {
        setTimeout(function () {
            var el = document.getElementById('spinner');
            if (el) {
                el.classList.remove('show');
            }
        }, 1);
    };
    spinner();

    /* ── WOW.js animations ───────────────────────────────── */
    if (typeof WOW !== 'undefined') {
        new WOW({
            boxClass:     'wow',
            animateClass: 'animate__animated',
            offset:       80,
            mobile:       true,
            live:         true
        }).init();
    }

    /* ── Owl Carousel – header slider ───────────────────── */
    if ($.fn.owlCarousel) {
        $(".header-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1500,
            autoplayHoverPause: true,
            dots: true,
            loop: true,
            nav: true,
            navText: ['&#8249;', '&#8250;'],
            responsive: {
                0:   { items: 1 },
                768: { items: 1 }
            }
        });
    }

    /* ── Back to top ─────────────────────────────────────── */
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
        return false;
    });

})(jQuery);
