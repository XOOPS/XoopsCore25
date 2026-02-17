/**
 * XOOPS Modern Admin Theme - Chart Initialization
 * Requires Chart.js to be loaded
 */

(function($) {
    'use strict';

    let charts = {};

    // Chart.js Default Configuration
    const defaultChartConfig = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        }
    };

    // Initialize User Registration Chart
    function initUserRegistrationChart() {
        const canvas = document.getElementById('userRegistrationChart');
        if (!canvas || !window.XOOPS_DASHBOARD_DATA || !window.XOOPS_DASHBOARD_DATA.userChart) {
            return;
        }

        const data = window.XOOPS_DASHBOARD_DATA.userChart;
        const labels = data.map(item => item.month);
        const values = data.map(item => item.count);

        const ctx = canvas.getContext('2d');
        charts.userChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: (window.MODERN_LANG && window.MODERN_LANG.newUsers) || 'New Users',
                    data: values,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#2563eb'
                }]
            },
            options: {
                ...defaultChartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Initialize User Group Chart
    function initUserGroupChart() {
        const canvas = document.getElementById('userGroupChart');
        if (!canvas || !window.XOOPS_DASHBOARD_DATA || !window.XOOPS_DASHBOARD_DATA.groupStats) {
            return;
        }

        const data = window.XOOPS_DASHBOARD_DATA.groupStats;
        const labels = data.map(item => item.name);
        const values = data.map(item => parseInt(item.count));

        const colors = [
            '#2563eb',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#06b6d4',
            '#8b5cf6',
            '#ec4899',
            '#14b8a6'
        ];

        const ctx = canvas.getContext('2d');
        charts.groupChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                ...defaultChartConfig,
                cutout: '60%'
            }
        });
    }

    // Content chart bar colors
    const contentColors = [
        '#2563eb', '#10b981', '#f59e0b', '#ef4444',
        '#06b6d4', '#8b5cf6', '#ec4899', '#14b8a6',
        '#f97316', '#6366f1', '#84cc16', '#e11d48'
    ];

    // Get selected content modules from cookie
    function getSelectedContentModules() {
        var saved = getCookieLocal('xoops_content_modules');
        if (saved) {
            try { return JSON.parse(saved); } catch (e) { /* ignore */ }
        }
        return null; // null = all selected (default)
    }

    // Filter content stats by selected modules
    function filterContentStats(allData, selected) {
        if (!selected) return allData; // null = show all
        return allData.filter(function(item) {
            return selected.indexOf(item.module) !== -1;
        });
    }

    // Initialize Content Distribution Chart
    function initContentChart() {
        const canvas = document.getElementById('contentChart');
        if (!canvas || !window.XOOPS_DASHBOARD_DATA || !window.XOOPS_DASHBOARD_DATA.contentStats) {
            return;
        }

        var allData = window.XOOPS_DASHBOARD_DATA.contentStats;
        var selected = getSelectedContentModules();
        var data = filterContentStats(allData, selected);

        // Hide chart card if no modules selected
        var card = canvas.closest('.chart-card');
        if (data.length === 0 && card) {
            card.style.display = 'none';
            return;
        }

        var labels = data.map(function(item) { return item.label; });
        var values = data.map(function(item) { return parseInt(item.count); });
        var bgColors = data.map(function(item, i) { return contentColors[i % contentColors.length]; });

        const ctx = canvas.getContext('2d');
        charts.contentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: (window.MODERN_LANG && window.MODERN_LANG.items) || 'Items',
                    data: values,
                    backgroundColor: bgColors,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                ...defaultChartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    ...defaultChartConfig.plugins,
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Rebuild the content chart with new module selection
    function rebuildContentChart(selected) {
        if (!window.XOOPS_DASHBOARD_DATA || !window.XOOPS_DASHBOARD_DATA.contentStats) return;
        if (!charts.contentChart) return;

        var canvas = document.getElementById('contentChart');
        if (!canvas) return;

        var allData = window.XOOPS_DASHBOARD_DATA.contentStats;
        var data = filterContentStats(allData, selected);

        // Hide the entire chart card when nothing is selected
        var card = canvas.closest('.chart-card');
        if (card) {
            card.style.display = data.length > 0 ? '' : 'none';
        }

        charts.contentChart.data.labels = data.map(function(item) { return item.label; });
        charts.contentChart.data.datasets[0].data = data.map(function(item) { return parseInt(item.count); });
        charts.contentChart.data.datasets[0].backgroundColor = data.map(function(item, i) { return contentColors[i % contentColors.length]; });
        charts.contentChart.update();
    }

    // Cookie helper (local to charts scope)
    function getCookieLocal(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length));
        }
        return null;
    }

    // Expose rebuild for customizer
    window.XOOPS_CHARTS = { rebuildContentChart: rebuildContentChart };

    // Update Charts for Dark Mode
    function updateChartsForTheme() {
        const isDark = document.body.classList.contains('dark-mode');
        const textColor = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? '#334155' : '#e2e8f0';

        Object.values(charts).forEach(chart => {
            if (chart.options.scales) {
                // Update scales
                ['x', 'y'].forEach(axis => {
                    if (chart.options.scales[axis]) {
                        chart.options.scales[axis].ticks = {
                            ...chart.options.scales[axis].ticks,
                            color: textColor
                        };
                        chart.options.scales[axis].grid = {
                            ...chart.options.scales[axis].grid,
                            color: gridColor
                        };
                    }
                });
            }

            // Update legend
            if (chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels = {
                    ...chart.options.plugins.legend.labels,
                    color: textColor
                };
            }

            chart.update();
        });
    }

    // Initialize all charts
    function initCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js is not loaded');
            return;
        }

        initUserRegistrationChart();
        initUserGroupChart();
        initContentChart();

        // Update charts when dark mode toggles
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    updateChartsForTheme();
                }
            });
        });

        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Initial theme update
        updateChartsForTheme();
    }

    // Initialize on DOM ready
    $(document).ready(function() {
        // Wait for Chart.js to load
        if (typeof Chart !== 'undefined') {
            initCharts();
        } else {
            // Retry after a short delay
            setTimeout(initCharts, 500);
        }
    });

})(jQuery);
