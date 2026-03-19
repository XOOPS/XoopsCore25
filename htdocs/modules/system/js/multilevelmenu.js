/**
 * Frontend Multi-Level Menu Toggle
 * Handles submenu click/hover for nested dropdowns.
 */
(function () {
    'use strict';

    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('.dropdown-submenu > .dropdown-toggle');
        if (!toggle) return;

        var submenu = toggle.closest('.dropdown-submenu');
        var href = toggle.getAttribute('href');

        // If submenu is already open and link has a real URL, navigate
        if (submenu.classList.contains('is-open') && href && href !== '#') {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        // Close sibling submenus
        var siblings = submenu.parentElement.querySelectorAll(':scope > .dropdown-submenu.is-open');
        siblings.forEach(function (sib) {
            if (sib !== submenu) {
                sib.classList.remove('is-open');
                var menu = sib.querySelector(':scope > .dropdown-menu');
                if (menu) menu.classList.remove('show');
            }
        });

        // Toggle this submenu
        submenu.classList.toggle('is-open');
        var subMenu = submenu.querySelector(':scope > .dropdown-menu');
        if (subMenu) {
            subMenu.classList.toggle('show');
        }
    });

    // Close submenus when parent dropdown closes
    document.addEventListener('hide.bs.dropdown', function (e) {
        var openSubs = e.target.querySelectorAll('.dropdown-submenu.is-open');
        openSubs.forEach(function (sub) {
            sub.classList.remove('is-open');
            var menu = sub.querySelector(':scope > .dropdown-menu');
            if (menu) menu.classList.remove('show');
        });
    });
})();
