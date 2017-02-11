<?php
/**
 * System help page
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Andricq Nicolas (AKA MusS)
 */
/* @var $module XoopsModule */

// Include header
include __DIR__ . '/header.php';

$page = system_CleanVars($_REQUEST, 'page', '', 'string');
$mid  = system_CleanVars($_REQUEST, 'mid', 0, 'int');

// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_help.tpl';
xoops_cp_header();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/help.css');
// Define Breadcrumb and tips
$xoBreadCrumb->addLink(_AM_SYSTEM_HELP, 'help.php');

// If $mid > 0, we're in a module's help section.
if ($mid > 0) {
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    /* @var $module XoopsModule */
    $module         = $module_handler->get($mid);

    $xoBreadCrumb->addLink($module->getVar('name'), 'help.php?mid=' . $module->getVar('mid', 's'));
    $xoBreadCrumb->addLink(system_adminVersion($page, 'name'));
    $xoBreadCrumb->render();

    // Special handling for the System Module
    if ($module->getVar('dirname', 'e') === 'system') {
        xoops_load('xoopslists');
        $admin_dir = XOOPS_ROOT_PATH . '/modules/system/admin';
        $dirlist   = XoopsLists::getDirListAsArray($admin_dir);
        foreach ($dirlist as $directory) {
            if (file_exists($admin_dir . '/' . $directory . '/xoops_version.php')) {
                require $admin_dir . '/' . $directory . '/xoops_version.php';
                if ($modversion['help']) {
                    $help['name'] = system_adminVersion($directory, 'name');
                    $help['link'] = 'help.php?mid=' . $mid . '&amp;' . system_adminVersion($directory, 'help');
                    $xoopsTpl->append_by_ref('help', $help);
                    unset($help);
                }
                unset($modversion);
            }
        }
        unset($dirlist);

        // Handling for all other modules.
    } else {
        $list_help      = array();
        $listed_mods[0] = $module->toArray();
        $helplist       = $module->getInfo('helpsection');
        $j              = 0;
        if (is_array($helplist)) {
            foreach ($helplist as $helpitem) {
                if (($helpitem['name'] !== '') && ($helpitem['link'] !== '')) {
                    $list_help[$j]['name'] = $helpitem['name'];
                    $list_help[$j]['link'] = 'help.php?mid=' . $mid . '&amp;' . $helpitem['link'];
                    ++$j;
                }
            }
            $listed_mods[0]['help_page'] = $list_help;
            $xoopsTpl->assign('list_mods', $listed_mods);
        }
        unset($helplist);
        if (($module->getInfo('help') !== '') && ($j == 0)) {
            $help['name'] = $module->getInfo('name');
            $help['link'] = 'help.php?mid=' . $mid . '&amp;' . $module->getInfo('help');
            $xoopsTpl->append_by_ref('help', $help);
        }
        unset($help);
    }

    $xoopsTpl->assign('modname', $module->getVar('name'));

    if ($page !== '') {
        // Call template
        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/' . $xoopsConfig['language'] . '/help/' . $page . '.html')) {
            $helpcontent = $xoopsTpl->fetch(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/' . $xoopsConfig['language'] . '/help/' . $page . '.html');
        } elseif (file_exists(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/' . $xoopsConfig['language'] . '/help/' . $page . '.tpl')) {
            $helpcontent = $xoopsTpl->fetch(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/' . $xoopsConfig['language'] . '/help/' . $page . '.tpl');
        } elseif (file_exists(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/english/help/' . $page . '.html')) {
            $helpcontent = $xoopsTpl->fetch(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/english/help/' . $page . '.html');
        } elseif (file_exists(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/english/help/' . $page . '.tpl')) {
            $helpcontent = $xoopsTpl->fetch(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/english/help/' . $page . '.tpl');
        } else {
            $xoopsTpl->assign('load_error', 1);
        }
        $xoopsTpl->assign('helpcontent', $helpcontent);
    } else {
        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/' . $xoopsConfig['language'] . '/help/module_index.html')) {
            $helpcontent = $xoopsTpl->fetch(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'e') . '/language/' . $xoopsConfig['language'] . '/help/module_index.html');
        } else {
            $helpcontent = $module->getInfo('description');
            $helpcontent = '<div id="non-modhelp">' . $helpcontent . '</div>';
        }
        $xoopsTpl->assign('helpcontent', $helpcontent);
    }

    // This section is called if we're in the general help area.
} else {
    $xoBreadCrumb->render();

    // Get Module Handler
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $criteria       = new CriteriaCompo();
    $criteria->setOrder('weight');

    // Get all installed modules
    $installed_mods = $module_handler->getObjects($criteria);
    $listed_mods    = array();
    $i              = 0;
    $j              = 0;
    foreach ($installed_mods as $module) {
        $list_help                      = array();
        $listed_mods[$i]                = $module->toArray();
        $listed_mods[$i]['image']       = $module->getInfo('image');
        $listed_mods[$i]['adminindex']  = $module->getInfo('adminindex');
        $listed_mods[$i]['version']     = round($module->getVar('version') / 100, 2);
        $listed_mods[$i]['last_update'] = formatTimestamp($module->getVar('last_update'), 'm');
        $listed_mods[$i]['author']      = $module->getInfo('author');
        $listed_mods[$i]['credits']     = $module->getInfo('credits');
        $listed_mods[$i]['license']     = $module->getInfo('license');
        $listed_mods[$i]['description'] = $module->getInfo('description');

        // Special handling for the System module
        if ($module->getVar('dirname', 'e') === 'system') {
            xoops_load('xoopslists');
            $admin_dir = XOOPS_ROOT_PATH . '/modules/system/admin';
            $dirlist   = XoopsLists::getDirListAsArray($admin_dir);
            foreach ($dirlist as $directory) {
                if (file_exists($admin_dir . '/' . $directory . '/xoops_version.php')) {
                    require $admin_dir . '/' . $directory . '/xoops_version.php';
                    if ($modversion['help']) {
                        $list_help[$j]['name'] = system_adminVersion($directory, 'name');
                        $list_help[$j]['link'] = 'help.php?mid=' . $module->getVar('mid', 'e') . '&amp;' . system_adminVersion($directory, 'help');
                    }
                    unset($modversion);
                    ++$j;
                }
            }
            unset($dirlist);

            // Handling for all other modules
        } else {
            $helplist = $module->getInfo('helpsection');
            $k        = 0;

            // Only build the list if one has been defined.
            if (is_array($helplist)) {
                foreach ($helplist as $helpitem) {
                    if (($helpitem['name'] !== '') && ($helpitem['link'] !== '')) {
                        $list_help[$j]['name'] = $helpitem['name'];
                        $list_help[$j]['link'] = 'help.php?mid=' . $module->getVar('mid', 'e') . '&amp;' . $helpitem['link'];
                        ++$j;
                        ++$k;
                    }
                }
            }
            unset($helplist);

            // If there is no help section ($k=0), and a lone help parameter has been defined.
            if (($module->getInfo('help') !== '') && ($k == 0)) {
                $list_help[$j]['name'] = $module->getInfo('name');
                $list_help[$j]['link'] = 'help.php?mid=' . $module->getVar('mid', 'e') . '&amp;' . $module->getInfo('help');
            }
        }

        $listed_mods[$i]['help_page'] = $list_help;
        if ($module->getInfo('help') === '') {
            unset($listed_mods[$i]);
        }
        unset($list_help, $module);
        ++$i;
        ++$j;
    }
    $xoopsTpl->assign('list_mods', $listed_mods);

    if (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/help/help_center.html')) {
        $helpcontent = $xoopsTpl->fetch(XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/help/help_center.html');
    } else {
        $helpcontent = '<div id="non-modhelp">' . _MD_CPANEL_HELPCENTER . '</div>';
    }

    $xoopsTpl->assign('helpcontent', $helpcontent);
}

xoops_cp_footer();
