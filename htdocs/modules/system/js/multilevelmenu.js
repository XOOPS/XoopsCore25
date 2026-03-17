/*
 * JavaScript helpers for multilevel dropdown menus
 *
 * Shared file included automatically by class/theme.php for every theme.
 *
 * Licensed under GNU GPL 2.0 or later (see LICENSE in root).
 */

// toggle submenus inside multilevel dropdowns
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-submenu > a').forEach(function(el) {
        el.addEventListener('click', function (e) {
            var href = this.getAttribute('href');
            // Only prevent default for empty/hash links; allow real URLs to navigate
            if (!href || href === '#' || href === 'javascript:;') {
                e.preventDefault();
            }
            e.stopPropagation();
            var sub = this.nextElementSibling;
            if (sub) sub.classList.toggle('show');
        });
    });
});
