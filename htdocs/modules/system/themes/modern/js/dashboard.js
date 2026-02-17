/**
 * XOOPS Modern Admin Theme - Dashboard Functions
 */

(function($) {
    'use strict';

    // Refresh Dashboard Data
    window.refreshDashboard = function() {
        // Reload the page to get fresh data
        location.reload();
    };

    // Add Hover Effects to Tables
    function initTableInteractions() {
        $('.info-table tbody tr').hover(
            function() {
                $(this).css('background-color', 'var(--bg-tertiary)');
            },
            function() {
                $(this).css('background-color', '');
            }
        );
    }

    // Initialize on DOM ready
    $(document).ready(function() {
        initTableInteractions();
    });

})(jQuery);
