/**
 * XOOPS Modern Admin Theme - Core Theme Functions
 */

(function($) {
    'use strict';

    // Dark Mode Toggle
    function initDarkMode() {
        const toggle = document.getElementById('darkModeToggle');
        const body = document.body;

        // Check saved preference
        const darkMode = getCookie('xoops_dark_mode');
        if (darkMode === '1') {
            body.classList.add('dark-mode');
        }

        if (toggle) {
            toggle.addEventListener('click', function() {
                body.classList.toggle('dark-mode');
                const isDark = body.classList.contains('dark-mode') ? '1' : '0';
                setCookie('xoops_dark_mode', isDark, 365);
            });
        }
    }

    // Sidebar Toggle
    function initSidebarToggle() {
        const toggle = document.getElementById('sidebarToggle');
        const body = document.body;

        if (toggle) {
            toggle.addEventListener('click', function() {
                body.classList.toggle('sidebar-open');
            });
        }
    }

    // Cookie Helper Functions
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        var secure = location.protocol === 'https:' ? ';Secure' : '';
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax' + secure;
    }

    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Help Section Toggle
    function initHelpToggle() {
        const helpViewButtons = document.querySelectorAll('.help_view');
        const helpHideButtons = document.querySelectorAll('.help_hide');

        // Show help when "view" button is clicked
        helpViewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Add class to body to manage button visibility
                document.body.classList.add('help-active');

                // Show all help sections
                document.querySelectorAll('.tips, #xo-system-help, .xo-help-content').forEach(help => {
                    help.classList.add('help-visible');
                });

                // Save preference
                setCookie('xoops_help_visible', '1', 30);
            });
        });

        // Hide help when "hide" button is clicked
        helpHideButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove class from body
                document.body.classList.remove('help-active');

                // Hide all help sections
                document.querySelectorAll('.tips, #xo-system-help, .xo-help-content').forEach(help => {
                    help.classList.remove('help-visible');
                });

                // Save preference
                setCookie('xoops_help_visible', '0', 30);
            });
        });

        // Check saved preference on page load
        const helpVisible = getCookie('xoops_help_visible');
        if (helpVisible === '1') {
            document.body.classList.add('help-active');
            document.querySelectorAll('.tips, #xo-system-help, .xo-help-content').forEach(help => {
                help.classList.add('help-visible');
            });
        }
    }

    // Move messages to top of page and auto-dismiss
    function initMessages() {
        var $messages = $('.errorMsg, .warningMsg');
        if ($messages.length === 0) return;

        // Move messages to top of .modern-main (above dashboard/content)
        var $main = $('.modern-main');
        if ($main.length > 0) {
            $messages.each(function() {
                $(this).detach().prependTo($main).hide().slideDown(300);
            });
        }

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $messages.slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Initialize on DOM ready
    $(document).ready(function() {
        initDarkMode();
        initSidebarToggle();
        initHelpToggle();
        initMessages();
    });

})(jQuery);
