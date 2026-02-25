<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

xoops_load('gui', 'system');

// Load theme language file
xoops_loadLanguage('main', 'system/themes/modern');

/**
 * XOOPS Modern Admin Theme
 *
 * A modern, responsive admin theme with enhanced metrics, charts, and dark mode support
 *
 * @copyright   (c) 2000-2026 XOOPS Project (www.xoops.org)
 * @license     GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package     system
 * @subpackage  GUI
 * @since       2.5.11
 * @author      Mamba <mambax7@gmail.com>
 */

/**
 * Class XoopsGuiModern
 */
class XoopsGuiModern extends XoopsSystemGui
{
    /**
     * Validate the theme
     * @return bool
     */
    public static function validate()
    {
        return true;
    }

    /**
     * Initialize theme header
     */
    public function header()
    {
        parent::header();

        global $xoopsConfig, $xoopsUser, $xoopsModule, $xoTheme, $xoopsTpl, $xoopsDB;
        $tpl =& $this->template;

        include_once dirname(__DIR__) . '/ComposerInfo.php';

        // Add Chart.js for dashboard visualizations (local with CDN fallback)
        $chartJsLocal = XOOPS_PATH . '/Frameworks/chartjs/chart.min.js';
        if (file_exists($chartJsLocal)) {
            $xoTheme->addScript('browse.php?Frameworks/chartjs/chart.min.js');
        } else {
            $xoTheme->addScript('https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js');
        }

        // Add theme scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/modern/js/theme.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/modern/js/dashboard.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/modern/js/charts.js');
        $xoTheme->addScript(XOOPS_ADMINTHEME_URL . '/modern/js/customizer.js');

        // Add theme stylesheets — load order matters:
        //   1. modern.css  — theme layout and components (replace wholesale on updates)
        //   2. xoops.css   — XOOPS element integration   (maintained by XOOPS team)
        //   3. dark.css    — dark mode CSS variable overrides
        //   4. fixes.css   — !important overrides against XOOPS core CSS
        //   5. custom.css  — site-specific user customizations (never overwritten by updates)
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/modern/css/modern.css');
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/modern/css/xoops.css');
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/modern/css/dark.css', ['id' => 'dark-theme-style']);
        $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/modern/css/fixes.css');
        // custom.css is loaded last so it wins over everything; only load if it exists
        // (absent on fresh installs until the admin creates it, never shipped in updates)
        if (file_exists(__DIR__ . '/css/custom.css')) {
            $xoTheme->addStylesheet(XOOPS_ADMINTHEME_URL . '/modern/css/custom.css');
        }

        // Basic configuration
        $tpl->assign('lang_cp', _CPHOME);
        $tpl->assign('xoops_sitename', $xoopsConfig['sitename']);
        $tpl->assign('theme_url', XOOPS_ADMINTHEME_URL . '/modern');

        // Ensure xoops_dirname is set for template conditionals
        if ($xoopsModule) {
            $tpl->assign('xoops_dirname', $xoopsModule->getVar('dirname'));
        } else {
            $tpl->assign('xoops_dirname', 'system');
        }

        // Get user preference for dark mode
        $darkMode = \Xmf\Request::getString('xoops_dark_mode', '0', 'COOKIE');
        $darkMode = ($darkMode === '1') ? '1' : '0';
        $tpl->assign('dark_mode', $darkMode);

        // System information
        $this->getSystemInfo($tpl);

        // Enhanced statistics
        $this->getEnhancedStats($tpl);

        // User statistics
        $this->getUserStats($tpl);

        // Module statistics
        $this->getModuleStats($tpl);

        // Content statistics
        $this->getContentStats($tpl);

        // Load module widgets for dashboard
        $this->loadModuleWidgets($tpl);

        // COMPOSER PACKAGES VERSION INFO
        ComposerInfo::getComposerInfo($tpl);

        // ADD MENU
        $this->buildMenu($tpl);
    }

    /**
     * Get system information
     */
    private function getSystemInfo(&$tpl)
    {
        global $xoopsDB;

        $tpl->assign('lang_php_version', PHP_VERSION);
        $tpl->assign('lang_smarty_version', $tpl::SMARTY_VERSION);
        $tpl->assign('lang_mysql_version', $xoopsDB->conn ? mysqli_get_server_info($xoopsDB->conn) : 'Unknown');
        $tpl->assign('lang_server_api', PHP_SAPI);
        $tpl->assign('lang_os_name', PHP_OS);
        $tpl->assign('post_max_size', ini_get('post_max_size'));
        $tpl->assign('max_execution_time', ini_get('max_execution_time'));
        $tpl->assign('memory_limit', ini_get('memory_limit'));
        $tpl->assign('file_uploads', ini_get('file_uploads') ? 'On' : 'Off');
        $tpl->assign('upload_max_filesize', ini_get('upload_max_filesize'));

        // Server load (if available)
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $tpl->assign('server_load', round($load[0], 2));
        } else {
            $tpl->assign('server_load', 'N/A');
        }
    }

    /**
     * Get enhanced statistics for dashboard
     */
    private function getEnhancedStats(&$tpl)
    {
        global $xoopsDB;

        // Calculate various statistics
        $stats = [];

        // Users statistics
        $result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('users'));
        if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
            list($stats['total_users']) = $xoopsDB->fetchRow($result);
        } else {
            $stats['total_users'] = 0;
        }

        // Users registered in last 30 days
        $result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('users') . " WHERE user_regdate > " . (time() - 2592000));
        if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
            list($stats['new_users_30d']) = $xoopsDB->fetchRow($result);
        } else {
            $stats['new_users_30d'] = 0;
        }

        // Active users (logged in last 30 days)
        $result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('users') . " WHERE last_login > " . (time() - 2592000));
        if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
            list($stats['active_users']) = $xoopsDB->fetchRow($result);
        } else {
            $stats['active_users'] = 0;
        }

        $tpl->assign('enhanced_stats', $stats);
    }

    /**
     * Get user statistics for charts
     */
    private function getUserStats(&$tpl)
    {
        global $xoopsDB;

        // Get user registrations by month for the last 6 months
        $userChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month_start = mktime(0, 0, 0, (int)date('n') - $i, 1, (int)date('Y'));
            $month_end = mktime(23, 59, 59, (int)date('n') - $i + 1, 0, (int)date('Y'));

            $result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('users') .
                                     " WHERE user_regdate >= $month_start AND user_regdate < $month_end");
            if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
                list($count) = $xoopsDB->fetchRow($result);
            } else {
                $count = 0;
            }

            $userChartData[] = [
                'month' => date('M Y', $month_start),
                'count' => (int)$count
            ];
        }

        $tpl->assign('user_chart_data', json_encode($userChartData));

        // Get user group distribution
        $groupStats = [];
        $result = $xoopsDB->query("SELECT g.name, COUNT(DISTINCT gm.uid) as count
                                  FROM " . $xoopsDB->prefix('groups') . " g
                                  LEFT JOIN " . $xoopsDB->prefix('groups_users_link') . " gm ON g.groupid = gm.groupid
                                  GROUP BY g.groupid, g.name
                                  ORDER BY count DESC");

        if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $groupStats[] = $row;
            }
        }

        $tpl->assign('group_stats', json_encode($groupStats));
    }

    /**
     * Get module statistics
     */
    private function getModuleStats(&$tpl)
    {
        /** @var XoopsModuleHandler $module_handler */
        $module_handler = xoops_getHandler('module');
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));

        $active_modules = $module_handler->getCount($criteria);

        $criteria_all = new Criteria('dirname', '', '!=');
        $total_modules = $module_handler->getCount($criteria_all);

        $tpl->assign('active_modules', $active_modules);
        $tpl->assign('total_modules', $total_modules);
        $tpl->assign('inactive_modules', $total_modules - $active_modules);
    }

    /**
     * Get content statistics for all known modules.
     * Returns all available stats; client-side filtering by user preference.
     */
    private function getContentStats(&$tpl)
    {
        global $xoopsDB;

        // Map of module dirname => primary content table and display label
        $knownModules = [
            'publisher'    => ['table' => 'publisher_items',          'label' => _MODERN_MOD_ARTICLES],
            'news'         => ['table' => 'news_stories',             'label' => _MODERN_MOD_NEWS],
            'tdmdownloads' => ['table' => 'tdmdownloads_downloads',   'label' => _MODERN_MOD_DOWNLOADS],
            'jobs'         => ['table' => 'jobs_jobs',                'label' => _MODERN_MOD_JOBS],
            'xblog'        => ['table' => 'xblog_posts',             'label' => _MODERN_MOD_BLOG_POSTS],
            'alumni'       => ['table' => 'alumni_profiles',          'label' => _MODERN_MOD_ALUMNI],
            'pedigree'     => ['table' => 'pedigree_animals',         'label' => _MODERN_MOD_PEDIGREES],
            'realestate'   => ['table' => 'realestate_properties',    'label' => _MODERN_MOD_PROPERTIES],
            'newbb'        => ['table' => 'bb_posts',                'label' => _MODERN_MOD_FORUM_POSTS],
            'mydownloads'  => ['table' => 'mydownloads_downloads',    'label' => _MODERN_MOD_DOWNLOADS],
            'mylinks'      => ['table' => 'mylinks_links',           'label' => _MODERN_MOD_LINKS],
            'articles'     => ['table' => 'articles',                'label' => _MODERN_MOD_ARTICLES],
        ];

        $contentStats = [];
        $availableModules = []; // For customizer checkboxes

        foreach ($knownModules as $dirname => $info) {
            $table = $xoopsDB->prefix($info['table']);
            $escapedTable = $xoopsDB->escape($table);

            $result = $xoopsDB->query(sprintf("SHOW TABLES LIKE '%s'", $escapedTable));
            if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result && $xoopsDB->getRowsNum($result) > 0) {
                $count_result = $xoopsDB->query(sprintf("SELECT COUNT(*) FROM `%s`", $escapedTable));
                if ($xoopsDB->isResultSet($count_result) && $count_result instanceof \mysqli_result) {
                    list($count) = $xoopsDB->fetchRow($count_result);
                    $contentStats[] = [
                        'module' => $dirname,
                        'label'  => $info['label'],
                        'count'  => (int)$count
                    ];
                    $availableModules[] = [
                        'dirname' => $dirname,
                        'label'   => $info['label']
                    ];
                }
            }
        }

        $tpl->assign('content_stats', json_encode($contentStats));
        $tpl->assign('available_content_modules', $availableModules);
    }

    /**
     * Load module widgets for dashboard
     */
    private function loadModuleWidgets(&$tpl)
    {
        $widgetLoaderFile = __DIR__ . '/class/WidgetLoader.php';
        if (file_exists($widgetLoaderFile)) {
            require_once $widgetLoaderFile;
            $widgetLoader = new ModernThemeWidgetLoader();
            $widgets = $widgetLoader->loadWidgets();
            $tpl->assign('module_widgets', $widgets);
        }
    }

    /**
     * Build navigation menu
     */
    private function buildMenu(&$tpl)
    {
        global $xoopsUser, $xoopsModule;

        // Control Panel Menu items
        $menu = [];
        $menu[0]['link'] = XOOPS_URL;
        $menu[0]['title'] = _YOURHOME;
        $menu[0]['absolute'] = 1;
        $menu[0]['icon'] = 'home';

        $menu[1]['link'] = XOOPS_URL . '/admin.php';
        $menu[1]['title'] = _CPHOME;
        $menu[1]['absolute'] = 1;
        $menu[1]['icon'] = 'dashboard';

        $menu[2]['link'] = XOOPS_URL . '/user.php?op=logout';
        $menu[2]['title'] = _LOGOUT;
        $menu[2]['absolute'] = 1;
        $menu[2]['icon'] = 'logout';

        $tpl->assign('control_menu', $menu);

        // Add the system menu items
        xoops_loadLanguage('menu', 'system');
        include_once __DIR__ . '/menu.php';

        // Build system services list (always available for header toolbar)
        /** @var array $adminmenu Populated by menu.php include above */
        $system_services = isset($adminmenu) ? $adminmenu : [];
        foreach (array_keys($system_services) as $item) {
            $system_services[$item]['link'] = empty($system_services[$item]['absolute']) ? XOOPS_URL . '/modules/system/' . $system_services[$item]['link'] : $system_services[$item]['link'];
            $system_services[$item]['icon'] = empty($system_services[$item]['icon']) ? '' : XOOPS_ADMINTHEME_URL . '/modern/' . $system_services[$item]['icon'];
            unset($system_services[$item]['icon_small']);
        }
        $tpl->assign('system_services', $system_services);

        // Handle current module context
        if (empty($xoopsModule) || 'system' === $xoopsModule->getVar('dirname', 'n')) {
            $modpath = XOOPS_URL . '/admin.php';
            $modname = _OXYGEN_SYSOPTIONS;
            $modid   = 1;
            $moddir  = 'system';

            $mod_options = $system_services;
        } else {
            $moddir  = $xoopsModule->getVar('dirname', 'n');
            $modpath = XOOPS_URL . '/modules/' . $moddir;
            $modname = $xoopsModule->getInfo('name') . '  (' . $xoopsModule->getInfo('version') . ')';
            $modid   = $xoopsModule->getVar('mid');

            $mod_options = $xoopsModule->getAdminMenu();
            foreach (array_keys($mod_options) as $item) {
                $mod_options[$item]['link'] = empty($mod_options[$item]['absolute']) ? XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['link'] : $mod_options[$item]['link'];
                $mod_options[$item]['icon'] = empty($mod_options[$item]['icon']) ? '' : (filter_var($mod_options[$item]['icon'], FILTER_VALIDATE_URL) ? $mod_options[$item]['icon'] : (XOOPS_URL . "/modules/{$moddir}/" . $mod_options[$item]['icon']));
            }
        }

        $tpl->assign('mod_options', $mod_options);
        $tpl->assign('modpath', $modpath);
        $tpl->assign('modname', $modname);
        $tpl->assign('modid', $modid);
        $tpl->assign('moddir', $moddir);

        // Add module menu items
        $module_handler = xoops_getHandler('module');
        $moduleperm_handler = xoops_getHandler('groupperm');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('hasadmin', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->setSort('mid');

        $modules = $module_handler->getObjects($criteria);
        $module_menu = [];

        // Build modules array for dashboard display
        $modules_list = [];
        foreach ($modules as $mod) {
            if ($moduleperm_handler->checkRight('module_admin', $mod->getVar('mid'), $xoopsUser->getGroups())) {
                $info = $mod->getInfo();

                $item = [];
                $item['name'] = $mod->getVar('name');
                $item['dirname'] = $mod->getVar('dirname');
                $item['icon'] = isset($info['image']) ? XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $info['image'] : '';

                if (!empty($info['adminindex'])) {
                    $item['link'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['adminindex'];
                } else {
                    $item['link'] = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                }

                $module_menu[] = $item;

                // For dashboard
                $rtn = [];
                if (!empty($info['adminindex'])) {
                    $rtn['link'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['adminindex'];
                } else {
                    $rtn['link'] = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->getVar('mid');
                }
                $rtn['title'] = htmlspecialchars((string) $mod->getVar('name'), ENT_QUOTES | ENT_HTML5);
                $rtn['description'] = $mod->getInfo('description');
                $rtn['absolute'] = 1;
                if (isset($info['icon_big'])) {
                    $rtn['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['icon_big'];
                } elseif (isset($info['image'])) {
                    $rtn['icon'] = XOOPS_URL . '/modules/' . $mod->getVar('dirname', 'n') . '/' . $info['image'];
                }
                $modules_list[] = $rtn;
            }
        }

        $tpl->assign('module_menu', $module_menu);
        $tpl->assign('modules', $modules_list);
    }
}
