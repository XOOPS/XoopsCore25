<{* Module-Specific Dashboard Widgets *}>
<{* Dynamically renders widgets from any module that implements ModernThemeWidgetInterface *}>

<{if !empty($module_widgets)}>
    <{foreach key=mod_name item=widget from=$module_widgets}>
        <{if is_array($widget)}>
        <div class="widget-card">
            <div class="widget-header">
                <h3 class="widget-title"><{if $widget.icon}><{$widget.icon|escape:'html'}> <{/if}><{$widget.title|escape:'html'}></h3>
                <{if !empty($widget.admin_url)}>
                    <a href="<{$widget.admin_url|escape:'html'}>" class="widget-link"><{$smarty.const._MODERN_VIEW_ALL}> &rarr;</a>
                <{/if}>
            </div>
            <div class="widget-body">
                <{if !empty($widget.stats)}>
                <div class="widget-stats">
                    <{foreach key=stat_key item=stat_val from=$widget.stats}>
                    <div class="widget-stat">
                        <div class="widget-stat-value"><{$stat_val|escape:'html'}></div>
                        <div class="widget-stat-label"><{$stat_key|replace:'_':' '|capitalize}></div>
                    </div>
                    <{/foreach}>
                </div>
                <{/if}>

                <{if !empty($widget.recent)}>
                <div class="widget-recent">
                    <{foreach item=item from=$widget.recent}>
                    <div class="widget-recent-item">
                        <span class="widget-recent-title"><{$item.title|escape:'html'|truncate:50}></span>
                        <{if !empty($item.author)}>
                            <span class="widget-recent-meta">by <{$item.author|escape:'html'}></span>
                        <{/if}>
                        <{if !empty($item.status)}>
                            <span class="widget-status widget-status-<{$item.status_class|escape:'html'}>"><{$item.status|escape:'html'}></span>
                        <{/if}>
                    </div>
                    <{/foreach}>
                </div>
                <{/if}>
            </div>
        </div>
        <{/if}>
    <{/foreach}>
<{/if}>

<style>
/* Widget Styling */
.widget-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: all 0.3s;
}

.widget-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.widget-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.widget-link {
    font-size: 13px;
    color: var(--primary);
    text-decoration: none;
    transition: color 0.2s;
}

.widget-link:hover {
    color: var(--primary-dark);
}

.widget-body {
    padding: 20px;
}

.widget-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 16px;
    text-align: center;
}

.widget-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
}

.widget-stat-label {
    font-size: 11px;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
}

/* Recent items list */
.widget-recent {
    margin-top: 16px;
    border-top: 1px solid var(--border-light);
    padding-top: 12px;
}

.widget-recent-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 13px;
}

.widget-recent-item:last-child {
    border-bottom: none;
}

.widget-recent-title {
    color: var(--text-primary);
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.widget-recent-meta {
    color: var(--text-tertiary);
    font-size: 11px;
    white-space: nowrap;
}

.widget-status {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: var(--radius-sm);
    font-weight: 500;
    white-space: nowrap;
}

.widget-status-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.widget-status-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}
</style>
