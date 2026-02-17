<{* Dashboard shown only on admin.php homepage *}>
<{if $xoops_dirname == 'system' && !isset($smarty.get.fct)}>
<section class="dashboard-section">
    <!-- KPI Cards -->
    <div class="kpis">
        <div class="kpi-card">
            <div class="kpi-label"><{$smarty.const._MODERN_TOTAL_USERS}></div>
            <div class="kpi-value"><{$enhanced_stats.total_users|number_format}></div>
            <div class="kpi-change positive">
                <span class="icon">↑</span>
                <span><{$enhanced_stats.new_users_30d}> <{$smarty.const._MODERN_NEW_THIS_MONTH}></span>
            </div>
        </div>

        <div class="kpi-card success">
            <div class="kpi-label"><{$smarty.const._MODERN_ACTIVE_MODULES}></div>
            <div class="kpi-value"><{$active_modules}></div>
            <div class="kpi-change">
                <span><{$inactive_modules}> <{$smarty.const._MODERN_INACTIVE}></span>
            </div>
        </div>

        <div class="kpi-card info">
            <div class="kpi-label"><{$smarty.const._MODERN_ACTIVE_USERS}></div>
            <div class="kpi-value"><{$enhanced_stats.active_users|number_format}></div>
            <div class="kpi-change">
                <span><{$smarty.const._MODERN_LAST_30_DAYS}></span>
            </div>
        </div>

        <div class="kpi-card warning">
            <div class="kpi-label"><{$smarty.const._MODERN_SERVER_LOAD}></div>
            <div class="kpi-value"><{$server_load}></div>
            <div class="kpi-change">
                <span><{$smarty.const._MODERN_CURRENT}></span>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title"><{$smarty.const._MODERN_USER_REGISTRATIONS}></div>
                <div class="chart-subtitle"><{$smarty.const._MODERN_NEW_USERS_6_MONTHS}></div>
            </div>
            <canvas id="userRegistrationChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title"><{$smarty.const._MODERN_USER_GROUPS}></div>
                <div class="chart-subtitle"><{$smarty.const._MODERN_DISTRIBUTION_BY_GROUP}></div>
            </div>
            <canvas id="userGroupChart"></canvas>
        </div>

        <{if $content_stats}>
        <div class="chart-card full">
            <div class="chart-header">
                <div class="chart-title"><{$smarty.const._MODERN_CONTENT_DISTRIBUTION}></div>
                <div class="chart-subtitle"><{$smarty.const._MODERN_CONTENT_ACROSS_MODULES}></div>
            </div>
            <canvas id="contentChart"></canvas>
        </div>
        <{/if}>
    </div>

    <!-- Module Widgets -->
    <div class="widgets-grid">
        <{include file="$theme_tpl/xo_widgets.tpl"}>
    </div>

    <!-- System Info (Collapsible) -->
    <div class="info-card">
        <details>
            <summary class="system-info-toggle">
                <span class="chart-title"><{$smarty.const._MODERN_SYSTEM_INFORMATION}></span>
                <span class="toggle-indicator">&#9654;</span>
            </summary>
            <div class="system-info-content">
                <table class="info-table">
                    <thead>
                        <tr>
                            <th><{$smarty.const._MODERN_COMPONENT}></th>
                            <th><{$smarty.const._MODERN_VALUE}></th>
                            <th><{$smarty.const._MODERN_STATUS}></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>PHP</strong></td>
                            <td><{$lang_php_version}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_ACTIVE}></span></td>
                        </tr>
                        <tr>
                            <td><strong>MySQL</strong></td>
                            <td><{$lang_mysql_version}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_ACTIVE}></span></td>
                        </tr>
                        <tr>
                            <td><strong>Smarty</strong></td>
                            <td><{$lang_smarty_version}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_ACTIVE}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_SERVER_API}></strong></td>
                            <td><{$lang_server_api}></td>
                            <td><span class="badge info"><{$smarty.const._MODERN_STATUS_RUNNING}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_OPERATING_SYSTEM}></strong></td>
                            <td><{$lang_os_name}></td>
                            <td><span class="badge info"><{$smarty.const._MODERN_STATUS_ACTIVE}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_MEMORY_LIMIT}></strong></td>
                            <td><{$memory_limit}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_GOOD}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_UPLOAD_MAX_SIZE}></strong></td>
                            <td><{$upload_max_filesize}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_CONFIGURED}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_MAX_EXECUTION_TIME}></strong></td>
                            <td><{$max_execution_time}>s</td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_ADEQUATE}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_POST_MAX_SIZE}></strong></td>
                            <td><{$post_max_size}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_CONFIGURED}></span></td>
                        </tr>
                        <tr>
                            <td><strong><{$smarty.const._MODERN_FILE_UPLOADS}></strong></td>
                            <td><{$file_uploads}></td>
                            <td><span class="badge success"><{$smarty.const._MODERN_STATUS_ENABLED}></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </details>

        <{if $composerPackages}>
        <details>
            <summary class="system-info-toggle">
                <span class="chart-title"><{$smarty.const._MODERN_COMPOSER_PACKAGES}></span>
                <span class="toggle-indicator">&#9654;</span>
            </summary>
            <div class="system-info-content">
                <table class="info-table">
                    <thead>
                        <tr>
                            <th><{$smarty.const._MODERN_PACKAGE}></th>
                            <th><{$smarty.const._MODERN_VERSION}></th>
                        </tr>
                    </thead>
                    <tbody>
                        <{foreach from=$composerPackages item=package}>
                        <tr>
                            <td><{$package.name}></td>
                            <td><{$package.version}></td>
                        </tr>
                        <{/foreach}>
                    </tbody>
                </table>
            </div>
        </details>
        <{/if}>
    </div>
</section>

<script>
// Pass PHP data to JavaScript
window.XOOPS_DASHBOARD_DATA = {
    userChart: <{$user_chart_data}>,
    groupStats: <{$group_stats}>,
    <{if $content_stats}>contentStats: <{$content_stats}><{/if}>
};
window.MODERN_LANG = {
    newUsers: '<{$smarty.const._MODERN_NEW_USERS}>',
    items: '<{$smarty.const._MODERN_ITEMS}>',
    confirmReset: '<{$smarty.const._MODERN_CONFIRM_RESET}>'
};
</script>
<{/if}>
