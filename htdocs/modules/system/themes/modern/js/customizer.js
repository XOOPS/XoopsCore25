/**
 * XOOPS Modern Admin Theme - Customizer
 */

(function($) {
    'use strict';

    // Color scheme definitions
    const colorSchemes = {
        default: {
            primary: '#2563eb',
            primaryDark: '#1e40af',
            primaryLight: '#3b82f6'
        },
        green: {
            primary: '#10b981',
            primaryDark: '#059669',
            primaryLight: '#34d399'
        },
        purple: {
            primary: '#8b5cf6',
            primaryDark: '#7c3aed',
            primaryLight: '#a78bfa'
        },
        orange: {
            primary: '#f59e0b',
            primaryDark: '#d97706',
            primaryLight: '#fbbf24'
        },
        teal: {
            primary: '#14b8a6',
            primaryDark: '#0d9488',
            primaryLight: '#2dd4bf'
        },
        red: {
            primary: '#ef4444',
            primaryDark: '#dc2626',
            primaryLight: '#f87171'
        }
    };

    // Initialize customizer
    function initCustomizer() {
        const panel = document.getElementById('customizerPanel');
        const toggle = document.getElementById('customizerToggle');
        const close = document.getElementById('customizerClose');

        // Load saved settings
        loadSettings();

        // Toggle panel
        if (toggle) {
            toggle.addEventListener('click', function() {
                panel.classList.add('open');
                createOverlay();
            });
        }

        // Close panel (top X button)
        if (close) {
            close.addEventListener('click', function() {
                panel.classList.remove('open');
                removeOverlay();
            });
        }

        // Close panel (bottom button)
        var closeBottom = document.getElementById('customizerCloseBottom');
        if (closeBottom) {
            closeBottom.addEventListener('click', function() {
                panel.classList.remove('open');
                removeOverlay();
            });
        }

        // Color presets
        document.querySelectorAll('.color-preset').forEach(button => {
            button.addEventListener('click', function() {
                const theme = this.dataset.theme;
                applyColorScheme(theme);

                // Update active state
                document.querySelectorAll('.color-preset').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                // Save preference
                setCookie('xoops_color_scheme', theme, 365);
            });
        });

        // Dashboard toggles
        $('#toggleKPIs').on('change', function() {
            $('.kpis').toggle(this.checked);
            setCookie('xoops_show_kpis', this.checked ? '1' : '0', 365);
        });

        $('#toggleCharts').on('change', function() {
            $('.charts').toggle(this.checked);
            setCookie('xoops_show_charts', this.checked ? '1' : '0', 365);
        });

        $('#toggleWidgets').on('change', function() {
            $('.widgets-grid').toggle(this.checked);
            setCookie('xoops_show_widgets', this.checked ? '1' : '0', 365);
        });

        $('#toggleSystemInfo').on('change', function() {
            $('.info-card').toggle(this.checked);
            setCookie('xoops_show_system_info', this.checked ? '1' : '0', 365);
        });

        // Sidebar options
        $('#compactSidebar').on('change', function() {
            $('body').toggleClass('compact-sidebar', this.checked);
            setCookie('xoops_compact_sidebar', this.checked ? '1' : '0', 365);
        });

        $('#sidebarIcons').on('change', function() {
            $('.nav-icon, .nav-icon-img').toggle(this.checked);
            setCookie('xoops_sidebar_icons', this.checked ? '1' : '0', 365);
        });

        // Display options
        $('#animationsEnabled').on('change', function() {
            $('body').toggleClass('no-animations', !this.checked);
            setCookie('xoops_animations', this.checked ? '1' : '0', 365);
        });

        $('#compactView').on('change', function() {
            $('body').toggleClass('compact-view', this.checked);
            setCookie('xoops_compact_view', this.checked ? '1' : '0', 365);
        });

        // Content module toggles
        initContentModuleToggles();

        // Reset settings
        $('#resetSettings').on('click', function() {
            if (confirm((window.MODERN_LANG && window.MODERN_LANG.confirmReset) || 'Reset all customizations to default?')) {
                resetToDefaults();
            }
        });
    }

    // Content module toggle handling
    function initContentModuleToggles() {
        var $toggles = $('.content-module-toggle');
        if ($toggles.length === 0) return;

        // Restore saved selection
        var saved = getCookie('xoops_content_modules');
        if (saved) {
            try {
                var selected = JSON.parse(saved);
                $toggles.each(function() {
                    $(this).prop('checked', selected.indexOf($(this).data('module')) !== -1);
                });
            } catch (e) { /* ignore, all stay checked */ }
        }

        // On change, save selection and rebuild chart
        $toggles.on('change', function() {
            var selected = [];
            $toggles.filter(':checked').each(function() {
                selected.push($(this).data('module'));
            });
            setCookie('xoops_content_modules', JSON.stringify(selected), 365);

            if (window.XOOPS_CHARTS && window.XOOPS_CHARTS.rebuildContentChart) {
                window.XOOPS_CHARTS.rebuildContentChart(selected);
            }
        });
    }

    // Apply color scheme
    function applyColorScheme(theme) {
        const colors = colorSchemes[theme];
        const root = document.documentElement;

        root.style.setProperty('--primary', colors.primary);
        root.style.setProperty('--primary-dark', colors.primaryDark);
        root.style.setProperty('--primary-light', colors.primaryLight);
    }

    // Load saved settings
    function loadSettings() {
        // Color scheme
        const colorScheme = getCookie('xoops_color_scheme') || 'default';
        applyColorScheme(colorScheme);
        document.querySelector(`.color-preset[data-theme="${colorScheme}"]`)?.classList.add('active');

        // Dashboard sections
        const showKPIs = getCookie('xoops_show_kpis') !== '0';
        const showCharts = getCookie('xoops_show_charts') !== '0';
        const showWidgets = getCookie('xoops_show_widgets') !== '0';
        const showSystemInfo = getCookie('xoops_show_system_info') !== '0';

        $('#toggleKPIs').prop('checked', showKPIs);
        $('.kpis').toggle(showKPIs);

        $('#toggleCharts').prop('checked', showCharts);
        $('.charts').toggle(showCharts);

        $('#toggleWidgets').prop('checked', showWidgets);
        $('.widgets-grid').toggle(showWidgets);

        $('#toggleSystemInfo').prop('checked', showSystemInfo);
        $('.info-card').toggle(showSystemInfo);

        // Sidebar options
        const compactSidebar = getCookie('xoops_compact_sidebar') === '1';
        $('#compactSidebar').prop('checked', compactSidebar);
        $('body').toggleClass('compact-sidebar', compactSidebar);

        const sidebarIcons = getCookie('xoops_sidebar_icons') !== '0';
        $('#sidebarIcons').prop('checked', sidebarIcons);
        $('.nav-icon, .nav-icon-img').toggle(sidebarIcons);

        // Display options
        const animations = getCookie('xoops_animations') !== '0';
        $('#animationsEnabled').prop('checked', animations);
        $('body').toggleClass('no-animations', !animations);

        const compactView = getCookie('xoops_compact_view') === '1';
        $('#compactView').prop('checked', compactView);
        $('body').toggleClass('compact-view', compactView);
    }

    // Reset to defaults
    function resetToDefaults() {
        // Clear all cookies
        const cookies = [
            'xoops_color_scheme',
            'xoops_show_kpis',
            'xoops_show_charts',
            'xoops_show_widgets',
            'xoops_show_system_info',
            'xoops_compact_sidebar',
            'xoops_sidebar_icons',
            'xoops_animations',
            'xoops_compact_view',
            'xoops_content_modules'
        ];

        cookies.forEach(cookie => {
            document.cookie = cookie + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });

        // Reload page
        location.reload();
    }

    // Create overlay
    function createOverlay() {
        if (!document.querySelector('.customizer-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'customizer-overlay active';
            overlay.onclick = function() {
                document.getElementById('customizerPanel').classList.remove('open');
                removeOverlay();
            };
            document.body.appendChild(overlay);
        }
    }

    // Remove overlay
    function removeOverlay() {
        const overlay = document.querySelector('.customizer-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    // Cookie helpers
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
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

    // Initialize on DOM ready
    $(document).ready(function() {
        initCustomizer();
    });

})(jQuery);
