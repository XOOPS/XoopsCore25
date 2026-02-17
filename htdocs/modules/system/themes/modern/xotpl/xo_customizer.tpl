<{* Theme Customization Panel *}>

<div id="customizerPanel" class="customizer-panel">
    <div class="customizer-header">
        <h3><{$smarty.const._MODERN_THEME_SETTINGS}></h3>
        <button id="customizerClose" class="btn-icon-sm">✕</button>
    </div>

    <div class="customizer-body">
        <!-- Color Scheme Section -->
        <div class="customizer-section">
            <h4 class="customizer-section-title"><{$smarty.const._MODERN_COLOR_SCHEME}></h4>
            <div class="color-presets">
                <button class="color-preset active" data-theme="default" title="<{$smarty.const._MODERN_COLOR_DEFAULT_BLUE}>">
                    <span class="preset-color" style="background: linear-gradient(135deg, #2563eb, #1e40af)"></span>
                    <span class="preset-name"><{$smarty.const._MODERN_COLOR_DEFAULT}></span>
                </button>
                <button class="color-preset" data-theme="green" title="<{$smarty.const._MODERN_COLOR_NATURE_GREEN}>">
                    <span class="preset-color" style="background: linear-gradient(135deg, #10b981, #059669)"></span>
                    <span class="preset-name"><{$smarty.const._MODERN_COLOR_GREEN}></span>
                </button>
                <button class="color-preset" data-theme="purple" title="<{$smarty.const._MODERN_COLOR_ROYAL_PURPLE}>">
                    <span class="preset-color" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)"></span>
                    <span class="preset-name"><{$smarty.const._MODERN_COLOR_PURPLE}></span>
                </button>
                <button class="color-preset" data-theme="orange" title="<{$smarty.const._MODERN_COLOR_WARM_ORANGE}>">
                    <span class="preset-color" style="background: linear-gradient(135deg, #f59e0b, #d97706)"></span>
                    <span class="preset-name"><{$smarty.const._MODERN_COLOR_ORANGE}></span>
                </button>
                <button class="color-preset" data-theme="teal" title="<{$smarty.const._MODERN_COLOR_OCEAN_TEAL}>">
                    <span class="preset-color" style="background: linear-gradient(135deg, #14b8a6, #0d9488)"></span>
                    <span class="preset-name"><{$smarty.const._MODERN_COLOR_TEAL}></span>
                </button>
                <button class="color-preset" data-theme="red" title="<{$smarty.const._MODERN_COLOR_BOLD_RED}>">
                    <span class="preset-color" style="background: linear-gradient(135deg, #ef4444, #dc2626)"></span>
                    <span class="preset-name"><{$smarty.const._MODERN_COLOR_RED}></span>
                </button>
            </div>
        </div>

        <!-- Dashboard Sections -->
        <div class="customizer-section">
            <h4 class="customizer-section-title"><{$smarty.const._MODERN_DASHBOARD_SECTIONS}></h4>
            <div class="toggle-list">
                <label class="toggle-item">
                    <input type="checkbox" id="toggleKPIs" checked>
                    <span class="toggle-label"><{$smarty.const._MODERN_KPI_CARDS}></span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" id="toggleCharts" checked>
                    <span class="toggle-label"><{$smarty.const._MODERN_CHARTS}></span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" id="toggleWidgets" checked>
                    <span class="toggle-label"><{$smarty.const._MODERN_MODULE_WIDGETS}></span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" id="toggleSystemInfo" checked>
                    <span class="toggle-label"><{$smarty.const._MODERN_SYSTEM_INFORMATION}></span>
                </label>
            </div>
        </div>

        <!-- Content Tracking -->
        <{if $available_content_modules}>
        <div class="customizer-section">
            <h4 class="customizer-section-title"><{$smarty.const._MODERN_CONTENT_TRACKING}></h4>
            <p class="customizer-hint"><{$smarty.const._MODERN_CONTENT_TRACKING_HINT}></p>
            <div class="toggle-list" id="contentModuleToggles">
                <{foreach item=mod from=$available_content_modules}>
                <label class="toggle-item">
                    <input type="checkbox" class="content-module-toggle" data-module="<{$mod.dirname}>" checked>
                    <span class="toggle-label"><{$mod.label}></span>
                </label>
                <{/foreach}>
            </div>
        </div>
        <{/if}>

        <!-- Sidebar Options -->
        <div class="customizer-section">
            <h4 class="customizer-section-title"><{$smarty.const._MODERN_SIDEBAR}></h4>
            <div class="toggle-list">
                <label class="toggle-item">
                    <input type="checkbox" id="compactSidebar">
                    <span class="toggle-label"><{$smarty.const._MODERN_COMPACT_MODE}></span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" id="sidebarIcons" checked>
                    <span class="toggle-label"><{$smarty.const._MODERN_SHOW_ICONS}></span>
                </label>
            </div>
        </div>

        <!-- Display Options -->
        <div class="customizer-section">
            <h4 class="customizer-section-title"><{$smarty.const._MODERN_DISPLAY}></h4>
            <div class="toggle-list">
                <label class="toggle-item">
                    <input type="checkbox" id="animationsEnabled" checked>
                    <span class="toggle-label"><{$smarty.const._MODERN_ANIMATIONS}></span>
                </label>
                <label class="toggle-item">
                    <input type="checkbox" id="compactView">
                    <span class="toggle-label"><{$smarty.const._MODERN_COMPACT_VIEW}></span>
                </label>
            </div>
        </div>

        <!-- Reset Button -->
        <div class="customizer-section">
            <button id="resetSettings" class="btn-secondary-full">
                <{$smarty.const._MODERN_RESET_TO_DEFAULTS}>
            </button>
        </div>

        <!-- Close Button at bottom -->
        <div class="customizer-section">
            <button id="customizerCloseBottom" class="btn-primary-full">
                <{$smarty.const._MODERN_CLOSE_SETTINGS}>
            </button>
        </div>
    </div>
</div>

<!-- Customizer Toggle Button -->
<button id="customizerToggle" class="customizer-fab" title="<{$smarty.const._MODERN_CUSTOMIZE_THEME}>">
    ⚙️
</button>

<style>
/* Customizer Panel */
.customizer-panel {
    position: fixed;
    top: 0;
    right: -320px;
    width: 320px;
    height: 100vh;
    background: var(--bg-secondary);
    box-shadow: var(--shadow-lg);
    z-index: 1100;
    transition: right 0.3s ease;
    overflow-y: auto;
}

.customizer-panel.open {
    right: 0;
}

.customizer-header {
    padding: 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: var(--bg-secondary);
    z-index: 1;
}

.customizer-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.btn-icon-sm {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    border-radius: var(--radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 20px;
    color: var(--text-secondary);
}

.btn-icon-sm:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.customizer-body {
    padding: 20px;
}

.customizer-section {
    margin-bottom: 24px;
}

.customizer-section-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-secondary);
    margin: 0 0 12px 0;
}

/* Color Presets */
.color-presets {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.color-preset {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 12px;
    border: 2px solid var(--border);
    border-radius: var(--radius);
    background: transparent;
    cursor: pointer;
    transition: all 0.2s;
}

.color-preset:hover {
    border-color: var(--primary);
}

.color-preset.active {
    border-color: var(--primary);
    background: var(--bg-tertiary);
}

.preset-color {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: var(--shadow);
}

.preset-name {
    font-size: 12px;
    color: var(--text-primary);
    font-weight: 500;
}

/* Toggle List */
.toggle-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.toggle-item {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px;
    border-radius: var(--radius);
    transition: background 0.2s;
}

.toggle-item:hover {
    background: var(--bg-tertiary);
}

.toggle-item input[type="checkbox"] {
    width: 40px;
    height: 20px;
    -webkit-appearance: none;
    appearance: none;
    background: var(--border);
    border-radius: 10px;
    position: relative;
    cursor: pointer;
    transition: background 0.2s;
}

.toggle-item input[type="checkbox"]:checked {
    background: var(--primary);
}

.toggle-item input[type="checkbox"]::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    top: 2px;
    left: 2px;
    transition: left 0.2s;
}

.toggle-item input[type="checkbox"]:checked::before {
    left: 22px;
}

.toggle-label {
    font-size: 14px;
    color: var(--text-primary);
}

/* Buttons */
.btn-secondary-full {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border);
    background: var(--bg-tertiary);
    color: var(--text-primary);
    border-radius: var(--radius);
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-secondary-full:hover {
    background: var(--border);
}

.btn-primary-full {
    width: 100%;
    padding: 10px;
    border: none;
    background: var(--primary);
    color: white;
    border-radius: var(--radius);
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary-full:hover {
    background: var(--primary-dark);
}

.customizer-hint {
    font-size: 12px;
    color: var(--text-tertiary);
    margin: 0 0 10px 0;
}

/* Customizer FAB */
.customizer-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    border: none;
    font-size: 24px;
    cursor: pointer;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.customizer-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.2);
}

.customizer-fab:active {
    transform: scale(0.95);
}

/* Overlay */
.customizer-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    display: none;
}

.customizer-overlay.active {
    display: block;
}

/* Widgets Grid */
.widgets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .customizer-panel {
        width: 100%;
        right: -100%;
    }

    .widgets-grid {
        grid-template-columns: 1fr;
    }
}
</style>
