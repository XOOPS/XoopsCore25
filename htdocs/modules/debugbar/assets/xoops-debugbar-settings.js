/**
 * XOOPS DebugBar Settings Widget for maximebf/debugbar v1.x
 *
 * Adds a Settings gear icon to the debugbar with:
 *  - Theme (Auto / Light / Dark)
 *  - Toolbar Position (Bottom Left / Bottom Right / Top Left / Top Right)
 *  - Hide Empty Tabs
 *  - Autoshow (AJAX requests)
 *  - Reset to Defaults
 *
 * Ported from php-debugbar v3.3's built-in Settings class.
 * Uses the same HTML structure and CSS classes as v3.3 for visual consistency.
 *
 * @copyright  (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license    GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 */
(function($) {

    if (typeof PhpDebugBar === 'undefined') {
        return;
    }

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-');
    var STORAGE_KEY = 'phpdebugbar-settings';

    /**
     * Load saved settings from localStorage
     */
    function loadSettings() {
        try {
            var saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : {};
        } catch (e) {
            return {};
        }
    }

    /**
     * Save settings to localStorage
     */
    function saveSettings(settings) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
        } catch (e) {
            // localStorage not available
        }
    }

    /**
     * Settings Widget — creates the form UI for settings panel.
     * Generates the same HTML structure as php-debugbar v3.3:
     *   <form class="phpdebugbar-settings">
     *     <div class="phpdebugbar-form-row">
     *       <div class="phpdebugbar-form-label">Label</div>
     *       <div class="phpdebugbar-form-input">...</div>
     *     </div>
     *   </form>
     */
    var SettingsWidget = PhpDebugBar.Widget.extend({

        className: csscls('xoops-settings'),

        render: function() {
            var self = this;
            this.debugbar = this.get('debugbar') || null;
            this.settings = loadSettings();

            // Wrap everything in a <form> like v3.3
            var $form = $('<form />')
                .addClass(csscls('settings'))
                .appendTo(this.$el);

            // Theme selector
            this._createSelectRow(
                $form,
                'Theme',
                'theme',
                [
                    { value: 'auto', label: 'Auto (System preference)' },
                    { value: 'light', label: 'Light' },
                    { value: 'dark', label: 'Dark' }
                ],
                this.settings.theme || 'auto'
            );

            // Toolbar Position selector
            this._createSelectRow(
                $form,
                'Toolbar Position',
                'position',
                [
                    { value: 'bottom-left', label: 'Bottom Left' },
                    { value: 'bottom-right', label: 'Bottom Right' },
                    { value: 'top-left', label: 'Top Left' },
                    { value: 'top-right', label: 'Top Right' }
                ],
                this.settings.position || 'bottom-left'
            );

            // Hide Empty Tabs checkbox
            this._createCheckboxRow(
                $form,
                'Hide Empty Tabs',
                'hideEmptyTabs',
                'Hide empty tabs until they have data',
                this.settings.hideEmptyTabs || false
            );

            // Autoshow AJAX checkbox
            this._createCheckboxRow(
                $form,
                'Autoshow',
                'autoshow',
                'Automatically show new incoming Ajax requests',
                this.settings.autoshow !== false
            );

            // Reset button row
            var $resetRow = $('<div />').addClass(csscls('form-row')).appendTo($form);
            $('<div />').addClass(csscls('form-label')).text('Reset to defaults').appendTo($resetRow);
            var $resetInput = $('<div />').addClass(csscls('form-input')).appendTo($resetRow);
            $('<button />')
                .text('Reset settings')
                .appendTo($resetInput)
                .click(function(e) {
                    e.preventDefault();
                    self._resetSettings();
                });

            this.$form = $form;

            // Apply saved settings on load
            this._applySettings();
        },

        /**
         * Create a select dropdown row (v3.3 structure)
         */
        _createSelectRow: function($container, label, key, options, currentValue) {
            var self = this;
            var $row = $('<div />').addClass(csscls('form-row')).appendTo($container);
            $('<div />').addClass(csscls('form-label')).text(label).appendTo($row);
            var $inputWrap = $('<div />').addClass(csscls('form-input')).appendTo($row);
            var $select = $('<select />').appendTo($inputWrap);

            for (var i = 0; i < options.length; i++) {
                var $opt = $('<option />').val(options[i].value).text(options[i].label);
                if (options[i].value === currentValue) {
                    $opt.prop('selected', true);
                }
                $opt.appendTo($select);
            }

            $select.on('change', function() {
                self.settings[key] = $(this).val();
                saveSettings(self.settings);
                self._applySettings();
            });

            return $row;
        },

        /**
         * Create a checkbox row (v3.3 structure)
         */
        _createCheckboxRow: function($container, label, key, description, checked) {
            var self = this;
            var $row = $('<div />').addClass(csscls('form-row')).appendTo($container);
            $('<div />').addClass(csscls('form-label')).text(label).appendTo($row);
            var $inputWrap = $('<div />').addClass(csscls('form-input')).appendTo($row);
            var $label = $('<label />').appendTo($inputWrap);
            var $cb = $('<input type="checkbox" />')
                .prop('checked', checked)
                .appendTo($label);
            $('<span />').text(description).appendTo($label);

            $cb.on('change', function() {
                self.settings[key] = $(this).is(':checked');
                saveSettings(self.settings);
                self._applySettings();
            });

            return $row;
        },

        /**
         * Apply all settings to the debugbar
         */
        _applySettings: function() {
            if (!this.debugbar) {
                return;
            }

            var settings = this.settings;

            // Apply theme
            this._applyTheme(settings.theme || 'auto');

            // Apply position
            this._applyPosition(settings.position || 'bottom-left');

            // Apply hideEmptyTabs
            if (typeof this.debugbar.setHideEmptyTabs === 'function') {
                this.debugbar.setHideEmptyTabs(settings.hideEmptyTabs || false);
            }
            // Show/hide existing tabs based on setting
            this._updateTabVisibility(settings.hideEmptyTabs || false);

            // Apply autoshow
            if (this.debugbar.ajaxHandler && typeof this.debugbar.ajaxHandler.setAutoShow === 'function') {
                this.debugbar.ajaxHandler.setAutoShow(settings.autoshow !== false);
            }
        },

        /**
         * Apply theme (dark/light/auto)
         */
        _applyTheme: function(theme) {
            var $bar = this.debugbar.$el;
            if (!$bar || !$bar.length) {
                $bar = $('div.phpdebugbar');
            }

            $bar.removeClass('phpdebugbar-theme-light phpdebugbar-theme-dark phpdebugbar-theme-auto');

            if (theme === 'dark') {
                $bar.addClass('phpdebugbar-theme-dark');
            } else if (theme === 'light') {
                $bar.addClass('phpdebugbar-theme-light');
            } else {
                // auto: detect system preference
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    $bar.addClass('phpdebugbar-theme-dark');
                } else {
                    $bar.addClass('phpdebugbar-theme-light');
                }
            }
        },

        /**
         * Apply toolbar position
         */
        _applyPosition: function(position) {
            var $bar = this.debugbar.$el;
            if (!$bar || !$bar.length) {
                $bar = $('div.phpdebugbar');
            }

            $bar.removeClass('phpdebugbar-position-bl phpdebugbar-position-br phpdebugbar-position-tl phpdebugbar-position-tr');

            switch (position) {
                case 'bottom-right':
                    $bar.addClass('phpdebugbar-position-br');
                    break;
                case 'top-left':
                    $bar.addClass('phpdebugbar-position-tl');
                    break;
                case 'top-right':
                    $bar.addClass('phpdebugbar-position-tr');
                    break;
                default: // bottom-left
                    $bar.addClass('phpdebugbar-position-bl');
                    break;
            }
        },

        /**
         * Show/hide empty tabs
         */
        _updateTabVisibility: function(hideEmpty) {
            if (!this.debugbar || !this.debugbar.controls) {
                return;
            }
            var controls = this.debugbar.controls;
            for (var name in controls) {
                if (controls.hasOwnProperty(name) && controls[name].$tab) {
                    var tab = controls[name];
                    if (hideEmpty) {
                        // Check if the tab has data
                        var hasData = false;
                        if (tab.has && tab.has('data')) {
                            var data = tab.get('data');
                            if (data && !$.isEmptyObject(data)) {
                                hasData = true;
                            }
                        }
                        // Check badge
                        if (tab.$badge && tab.$badge.text() && tab.$badge.text() !== '0') {
                            hasData = true;
                        }
                        // Always show messages and active tab
                        if (name === 'messages' || name === this.debugbar.activePanelName) {
                            hasData = true;
                        }
                        if (!hasData) {
                            tab.$tab.hide();
                        } else {
                            tab.$tab.show();
                        }
                    } else {
                        tab.$tab.show();
                    }
                }
            }
        },

        /**
         * Reset all settings to defaults
         */
        _resetSettings: function() {
            this.settings = {};
            saveSettings(this.settings);

            // Reset form controls
            this.$form.find('select').each(function() {
                $(this).find('option').first().prop('selected', true);
            });
            this.$form.find('input[type=checkbox]').each(function(i) {
                $(this).prop('checked', i === 1); // autoshow defaults to on
            });

            this._applySettings();
        }
    });

    /**
     * Initialize the settings tab on the debugbar
     *
     * Called after the debugbar is rendered. Adds the gear icon as a
     * tab in the right-side controls area.
     */
    PhpDebugBar.DebugBar.prototype._initSettings = function() {
        var self = this;

        // Create the settings widget
        var settingsWidget = new SettingsWidget({ debugbar: this });

        // Create a tab with the gear icon
        var settingsTab = new PhpDebugBar.DebugBar.Tab({
            icon: 'gear',
            title: 'Settings',
            widget: settingsWidget
        });

        // Add to the right side of the header (near close/minimize buttons)
        settingsTab.$tab.addClass(csscls('tab-settings'));
        settingsTab.$tab.insertBefore(this.$datasets);
        settingsTab.$tab.click(function() {
            if (!self.isMinimized() && self.activePanelName === '__settings') {
                self.minimize();
            } else {
                self.showTab('__settings');
            }
        });
        settingsTab.$el.appendTo(this.$body);
        this.controls['__settings'] = settingsTab;
    };

})(PhpDebugBar.$);
