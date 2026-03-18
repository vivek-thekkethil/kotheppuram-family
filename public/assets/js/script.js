/**
 * Kotheppuram Admin – UI Interactions
 * Handles: dropdown toggles, mobile nav, active links, outside-click close.
 */
(function () {
    "use strict";

    /* ── Dropdown toggles (.toggle-tigger → .toggle-class sibling) ── */
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.toggle-tigger');

        if (trigger) {
            e.preventDefault();
            var parent  = trigger.closest('.topbar-nav-item, .relative');
            var content = parent && parent.querySelector('.toggle-class');

            if (content) {
                var isActive = content.classList.contains('active');
                // Close all open dropdowns first
                document.querySelectorAll('.toggle-class.active').forEach(function (el) {
                    el.classList.remove('active');
                });
                if (!isActive) {
                    content.classList.add('active');
                }
            }
            return;
        }

        /* ── Mobile hamburger (.toggle-nav → .navbar-menu) ── */
        var navToggle = e.target.closest('.toggle-nav');
        if (navToggle) {
            e.preventDefault();
            var menu = document.querySelector('.navbar-menu');
            if (menu) { menu.classList.toggle('menu-open'); }
            return;
        }

        /* ── Close on outside click ── */
        if (!e.target.closest('.toggle-class') && !e.target.closest('.toggle-tigger')) {
            document.querySelectorAll('.toggle-class.active').forEach(function (el) {
                el.classList.remove('active');
            });
        }
    });

    /* ── Active nav link highlighting ── */
    var currentPath = window.location.pathname;
    document.querySelectorAll('.navbar-menu a').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.startsWith(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });

}());
