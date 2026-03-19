/**
 * Frontend Multi-Level Menu Helpers
 *
 * Handles nested submenu toggle, keyboard accessibility, and
 * the xswatch toolbar hook for both Bootstrap 4 and 5 themes.
 *
 * @copyright 2001-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2+ (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author    XOOPS Development Team
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /**
     * Close all peer submenus at the same nesting level.
     */
    function closePeerSubmenus(currentItem) {
        if (!currentItem || !currentItem.parentElement) {
            return;
        }
        currentItem.parentElement.querySelectorAll(':scope > .dropdown-submenu.is-open').forEach(function (item) {
            if (item !== currentItem) {
                item.classList.remove('is-open');
                var trigger = item.querySelector(':scope > .dropdown-toggle');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    /**
     * Close every open submenu on the page.
     */
    function closeAllSubmenus() {
        document.querySelectorAll('.dropdown-submenu.is-open').forEach(function (item) {
            item.classList.remove('is-open');
            var trigger = item.querySelector(':scope > .dropdown-toggle');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // --- Submenu toggle on click ---
    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('.dropdown-submenu > .dropdown-toggle');
        if (!toggle) {
            // Click outside any toggle — close all submenus
            closeAllSubmenus();
            return;
        }

        var submenu = toggle.closest('.dropdown-submenu');
        var href = toggle.getAttribute('href') || '';

        // If submenu is already open and link has a real URL, allow navigation
        if (submenu.classList.contains('is-open') && href && href !== '#') {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        closePeerSubmenus(submenu);
        submenu.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', submenu.classList.contains('is-open') ? 'true' : 'false');
    });

    // --- Escape key closes all submenus ---
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllSubmenus();
        }
    });

    // --- Clean up when Bootstrap dropdown parent closes ---
    document.addEventListener('hide.bs.dropdown', function (e) {
        if (e.target) {
            e.target.querySelectorAll('.dropdown-submenu.is-open').forEach(function (item) {
                item.classList.remove('is-open');
                var trigger = item.querySelector(':scope > .dropdown-toggle');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });

    // --- xswatch toolbar toggle hook ---
    document.querySelectorAll('a[href="#xswatch-toolbar-toggle"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            if (typeof window.xswatchToolbarToggle === 'function') {
                window.xswatchToolbarToggle();
            }
        });
    });
});
