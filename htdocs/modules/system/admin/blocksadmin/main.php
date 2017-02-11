<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');

$filter = system_CleanVars($_GET, 'filter', 0, 'int');
if ($filter) {
    $method = $_GET;
} else {
    $method = $_REQUEST;
}

$sel = array(
    'selmod' => -2,
    'selgen' => -1,
    'selgrp' => XOOPS_GROUP_USERS,
    'selvis' => -1);
foreach ($sel as $key => $value) {
    $_{$key} = isset($_COOKIE[$key]) ? (int)$_COOKIE[$key] : $value;
    ${$key}  = system_CleanVars($method, $key, $_{$key}, 'int');
    setcookie($key, ${$key});
}

$type = system_CleanVars($method, 'type', '', 'string');
if ($type === 'preview') {
    $op = 'preview';
}

if (isset($_GET['op'])) {
    if ($_GET['op'] === 'edit' || $_GET['op'] === 'delete' || $_GET['op'] === 'delete_ok' || $_GET['op'] === 'clone') {
        $op  = $_GET['op'];
        $bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;
    }
}

switch ($op) {

    case 'list':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_blocks.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        $xoTheme->addScript('modules/system/js/blocks.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_ADMIN, system_adminVersion('blocksadmin', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('blocksadmin', 'help'));
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_BLOCKS_TIPS, system_AdminIcons('block.png'), system_AdminIcons('success.png'), system_AdminIcons('cancel.png')));
        $xoBreadCrumb->render();

        // Initialize module handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $modules        = $module_handler->getObjects(null, true);
        $criteria       = new CriteriaCompo(new Criteria('hasmain', 1));

        $criteria->add(new Criteria('isactive', 1));
        // Modules for blocks to be visible in
        $display_list = $module_handler->getList($criteria);
        unset($criteria);
        // Initialize blocks handler
        /* @var $block_handler SystemBlockHandler */
        $block_handler = xoops_getModuleHandler('block');
        // Initialize module handler
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $modules        = $module_handler->getObjects(null, true);

        $filterform = new XoopsThemeForm('', 'filterform', 'admin.php', 'get');
        $filterform->addElement(new XoopsFormHidden('fct', 'blocksadmin'));
        $filterform->addElement(new XoopsFormHidden('op', 'list'));
        $filterform->addElement(new XoopsFormHidden('filter', 1));
        $sel_gen = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_GENERATOR, 'selgen', $selgen);
        $sel_gen->setExtra("onchange='submit()'");
        $sel_gen->addOption(-1, _AM_SYSTEM_BLOCKS_TYPES);
        $sel_gen->addOption(0, _AM_SYSTEM_BLOCKS_CUSTOM);
        foreach ($modules as $list) {
            $sel_gen->addOption($list->getVar('mid'), $list->getVar('name'));
        }
        $filterform->addElement($sel_gen);

        $sel_mod = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_SVISIBLEIN, 'selmod', $selmod);
        $sel_mod->setExtra("onchange='submit()'");
        ksort($display_list);
        $display_list_spec[0]  = _AM_SYSTEM_BLOCKS_ALLPAGES;
        $display_list_spec[-1] = _AM_SYSTEM_BLOCKS_TOPPAGE;
        $display_list_spec[-2] = _AM_SYSTEM_BLOCKS_TYPES;
        $display_list          = $display_list_spec + $display_list;
        foreach ($display_list as $k => $v) {
            $sel_mod->addOption($k, $v);
        }
        $filterform->addElement($sel_mod);

        // For selection of group access
        $sel_grp = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_GROUP, 'selgrp', $selgrp);
        $sel_grp->setExtra("onchange='submit()'");
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $group_list     = $member_handler->getGroupList();
        $sel_grp->addOption(-1, _AM_SYSTEM_BLOCKS_TYPES);
        $sel_grp->addOption(0, _AM_SYSTEM_BLOCKS_UNASSIGNED);
        foreach ($group_list as $k => $v) {
            $sel_grp->addOption($k, $v);
        }
        $filterform->addElement($sel_grp);

        $sel_vis = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_VISIBLE, 'selvis', $selvis);
        $sel_vis->setExtra("onchange='submit()'");
        $sel_vis->addOption(-1, _AM_SYSTEM_BLOCKS_TYPES);
        $sel_vis->addOption(0, _NO);
        $sel_vis->addOption(1, _YES);

        $filterform->addElement($sel_vis);

        $filterform->assign($xoopsTpl);

        /* Get blocks */
        $selvis      = ($selvis == -1) ? null : $selvis;
        $selmod      = ($selmod == -2) ? null : $selmod;
        $order_block = (isset($selvis) ? '' : 'b.visible DESC, ') . 'b.side,b.weight,b.bid';

        if ($selgrp == 0) {
            // get blocks that are not assigned to any groups
            $blocks_arr = $block_handler->getNonGroupedBlocks($selmod, $toponlyblock = false, $selvis, $order_block);
        } else {
            $blocks_arr = $block_handler->getAllByGroupModule($selgrp, $selmod, $toponlyblock = false, $selvis, $order_block);
        }

        if ($selgen >= 0) {
            foreach (array_keys($blocks_arr) as $bid) {
                if ($blocks_arr[$bid]->getVar('mid') != $selgen) {
                    unset($blocks_arr[$bid]);
                }
            }
        }

        $arr = array();
        foreach (array_keys($blocks_arr) as $i) {
            $arr[$i] = $blocks_arr[$i]->toArray();
            $xoopsTpl->append_by_ref('blocks', $arr[$i]);
        }
        $block     = $block_handler->create();
        $blockform = $block->getForm();
        $xoopsTpl->assign('blockform', $blockform->render());
        // Call Footer
        xoops_cp_footer();
        break;

    case 'add':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_blocks.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.form.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        $xoTheme->addScript('modules/system/js/blocks.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_ADMIN, system_adminVersion('blocksadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_ADDBLOCK);
        $xoBreadCrumb->render();
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        /* @var  $block SystemBlock */
        $block         = $block_handler->create();
        $blockform     = $block->getForm();
        $xoopsTpl->assign('blockform', $blockform->render());
        // Call Footer
        xoops_cp_footer();
        break;

    case 'display':
        // Initialize blocks handler
        /* @var $block_handler SystemBlockHandler */
        $block_handler = xoops_getModuleHandler('block');
        // Get variable
        $block_id = system_CleanVars($_POST, 'bid', 0, 'int');
        $visible  = system_CleanVars($_POST, 'visible', 0, 'int');
        if ($block_id > 0) {
            $block = $block_handler->get($block_id);
            $block->setVar('visible', $visible);
            if (!$block_handler->insert($block)) {
                $error = true;
            }
        }
        break;

    case 'drag':
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        // Get variable
        $block_id = system_CleanVars($_POST, 'bid', 0, 'int');
        $side     = system_CleanVars($_POST, 'side', 0, 'int');
        if ($block_id > 0) {
            $block = $block_handler->get($block_id);
            $block->setVar('side', $side);
            if (!$block_handler->insert($block)) {
                $error = true;
            }
        }
        break;

    case 'order':
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        if (isset($_POST['blk'])) {
            $i = 0;
            foreach ($_POST['blk'] as $order) {
                if ($order > 0) {
                    $block = $block_handler->get($order);
                    $block->setVar('weight', $i);
                    if (!$block_handler->insert($block)) {
                        $error = true;
                    }
                    ++$i;
                }
            }
        }
        exit;
        break;

    case 'preview':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=blocksadmin', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Initialize blocks handler
        /* @var $block_handler XoopsBlockHandler */
        $block_handler = xoops_getModuleHandler('block');
        $block         = $block_handler->create();
        $block->setVars($_POST);
        $content = isset($_POST['content_block']) ? $_POST['content_block'] : '';
        $block->setVar('content', $content);
        $myts = MyTextSanitizer::getInstance();
        echo '<div id="xo-preview-dialog" title="' . $block->getVar('title', 's') . '">' . $block->getContent('s', $block->getVar('c_type')) . '</div>';
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=blocksadmin', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        // Get avatar id
        $block_id = system_CleanVars($_POST, 'bid', 0, 'int');
        if ($block_id > 0) {
            $block = $block_handler->get($block_id);
        } else {
            $block = $block_handler->create();
        }
        $block_type = system_CleanVars($_POST, 'block_type', '', 'string');
        $block->setVar('block_type', $block_type);

        if (!$block->isCustom()) {
            $block->setVars($_POST);
            $type = $block->getVar('block_type');
            $name = $block->getVar('name');
            // Save block options
            $options = $_POST['options'];
            if (isset($options)) {
                $options_count = count($options);
                if ($options_count > 0) {
                    //Convert array values to comma-separated
                    for ($i = 0; $i < $options_count; ++$i) {
                        if (is_array($options[$i])) {
                            $options[$i] = implode(',', $options[$i]);
                        }
                    }
                    $options = implode('|', $options);
                    $block->setVar('options', $options);
                }
            }
        } else {
            $block->setVars($_POST);
            switch ($block->getVar('c_type')) {
                case 'H':
                    $name = _AM_SYSTEM_BLOCKS_CUSTOMHTML;
                    break;
                case 'P':
                    $name = _AM_SYSTEM_BLOCKS_CUSTOMPHP;
                    break;
                case 'S':
                    $name = _AM_SYSTEM_BLOCKS_CUSTOMSMILE;
                    break;
                default:
                    $name = _AM_SYSTEM_BLOCKS_CUSTOMNOSMILE;
                    break;
            }
        }
        $block->setVar('name', $name);
        $block->setVar('isactive', 1);

        $content = isset($_POST['content_block']) ? $_POST['content_block'] : '';
        $block->setVar('content', $content);

        if (!$newid = $block_handler->insert($block)) {
            xoops_cp_header();
            xoops_error($block->getHtmlErrors());
            xoops_cp_footer();
            exit();
        }
        if ($newid != 0) {
            $blocklinkmodule_handler = xoops_getModuleHandler('blocklinkmodule');
            // Delete old link
            $criteria = new CriteriaCompo(new Criteria('block_id', $newid));
            $blocklinkmodule_handler->deleteAll($criteria);
            // Assign link
            $modules = $_POST['modules'];
            foreach ($modules as $mid) {
                $blocklinkmodule = $blocklinkmodule_handler->create();
                $blocklinkmodule->setVar('block_id', $newid);
                $blocklinkmodule->setVar('module_id', $mid);
                if (!$blocklinkmodule_handler->insert($blocklinkmodule)) {
                    xoops_cp_header();
                    xoops_error($blocklinkmodule->getHtmlErrors());
                    xoops_cp_footer();
                    exit();
                }
            }
        }
        /* @var $groupperm_handler XoopsGroupPermHandler  */
        $groupperm_handler  = xoops_getHandler('groupperm');
        $groups             = $_POST['groups'];
        $groups_with_access = $groupperm_handler->getGroupIds('block_read', $newid);
        $removed_groups     = array_diff($groups_with_access, $groups);
        if (count($removed_groups) > 0) {
            foreach ($removed_groups as $groupid) {
                $criteria = new CriteriaCompo(new Criteria('gperm_name', 'block_read'));
                $criteria->add(new Criteria('gperm_groupid', $groupid));
                $criteria->add(new Criteria('gperm_itemid', $newid));
                $criteria->add(new Criteria('gperm_modid', 1));
                $perm = $groupperm_handler->getObjects($criteria);
                if (isset($perm[0]) && is_object($perm[0])) {
                    $groupperm_handler->delete($perm[0]);
                }
            }
        }
        $new_groups = array_diff($groups, $groups_with_access);
        if (count($new_groups) > 0) {
            foreach ($new_groups as $groupid) {
                $groupperm_handler->addRight('block_read', $newid, $groupid);
            }
        }
        redirect_header('admin.php?fct=blocksadmin', 1, _AM_SYSTEM_BLOCKS_DBUPDATED);
        break;

    case 'edit':
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        // Get avatar id
        $block_id = system_CleanVars($_REQUEST, 'bid', 0, 'int');
        if ($block_id > 0) {
            // Define main template
            $GLOBALS['xoopsOption']['template_main'] = 'system_blocks.tpl';
            // Call Header
            xoops_cp_header();
            // Define Stylesheet
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
            // Define scripts
            $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
            $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
            $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.form.js');
            $xoTheme->addScript('modules/system/js/admin.js');
            // Define Breadcrumb and tips
            $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_ADMIN, system_adminVersion('blocksadmin', 'adminpath'));
            $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_EDITBLOCK);
            $xoBreadCrumb->render();

            $block     = $block_handler->get($block_id);
            $blockform = $block->getForm();
            $xoopsTpl->assign('blockform', $blockform->render());
            // Call Footer
            xoops_cp_footer();
        } else {
            redirect_header('admin.php?fct=blocksadmin', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    case 'delete':
        // Initialize blocks handler
        /* @var $block_handler SystemBlockHandler */
        $block_handler = xoops_getModuleHandler('block');
        // Get avatar id
        $block_id = system_CleanVars($_REQUEST, 'bid', 0, 'int');
        if ($block_id > 0) {
            $block = $block_handler->get($block_id);
            if ($block->getVar('block_type') === 'S') {
                redirect_header('admin.php?fct=blocksadmin', 4, _AM_SYSTEM_BLOCKS_SYSTEMCANT);
            } elseif ($block->getVar('block_type') === 'M') {
                // Fix for duplicated blocks created in 2.0.9 module update
                // A module block can be deleted if there is more than 1 that
                // has the same func_num/show_func which is mostly likely
                // be the one that was duplicated in 2.0.9
                if (1 >= $count = $block_handler->countSimilarBlocks($block->getVar('mid'), $block->getVar('func_num'), $block->getVar('show_func'))) {
                    redirect_header('admin.php?fct=blocksadmin', 4, _AM_SYSTEM_BLOCKS_MODULECANT);
                }
            }
            // Define main template
            $GLOBALS['xoopsOption']['template_main'] = 'system_header.tpl';
            // Call Header
            xoops_cp_header();
            // Display Question
            xoops_confirm(array(
                              'op'  => 'delete_ok',
                              'fct' => 'blocksadmin',
                              'bid' => $block->getVar('bid')), 'admin.php', sprintf(_AM_SYSTEM_BLOCKS_RUSUREDEL, $block->getVar('title')));
            // Call Footer
            xoops_cp_footer();
        }
        break;

    case 'delete_ok':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=blocksadmin', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        // Get avatar id
        $block_id = system_CleanVars($_POST, 'bid', 0, 'int');
        if ($block_id > 0) {
            $block = $block_handler->get($block_id);
            if ($block_handler->delete($block)) {
                // Delete Group link
                $blocklinkmodule_handler = xoops_getModuleHandler('blocklinkmodule');
                $blocklinkmodule         = $blocklinkmodule_handler->getObjects(new CriteriaCompo(new Criteria('block_id', $block_id)));
                foreach ($blocklinkmodule as $link) {
                    $blocklinkmodule_handler->delete($link, true);
                }
                // Delete Group permission
                /* @var  $groupperm_handler XoopsGroupPermHandler */
                $groupperm_handler = xoops_getHandler('groupperm');
                $criteria          = new CriteriaCompo(new Criteria('gperm_name', 'block_read'));
                $criteria->add(new Criteria('gperm_itemid', $block_id));
                $groupperm = $groupperm_handler->getObjects($criteria);
                foreach ($groupperm as $perm) {
                    $groupperm_handler->delete($perm, true);
                }
                // Delete template
                if ($block->getVar('template') != '') {
                    $tplfile_handler = xoops_getHandler('tplfile');
                    $btemplate       = $tplfile_handler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $block_id);
                    if (count($btemplate) > 0) {
                        $tplfile_handler->delete($btemplate[0]);
                    }
                }
                redirect_header('admin.php?fct=blocksadmin', 1, _AM_SYSTEM_BLOCKS_DBUPDATED);
            }
        } else {
            redirect_header('admin.php?fct=blocksadmin', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    case 'clone':
        // Initialize blocks handler
        $block_handler = xoops_getModuleHandler('block');
        // Get avatar id
        $block_id = system_CleanVars($_REQUEST, 'bid', 0, 'int');
        if ($block_id > 0) {
            // Define main template
            $GLOBALS['xoopsOption']['template_main'] = 'system_blocks.tpl';
            // Call Header
            xoops_cp_header();
            // Define Stylesheet
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
            // Define Breadcrumb and tips
            $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_ADMIN, system_adminVersion('blocksadmin', 'adminpath'));
            $xoBreadCrumb->addLink(_AM_SYSTEM_BLOCKS_CLONEBLOCK);
            $xoBreadCrumb->render();
            /* @var $block XoopsBlock */
            $block     = $block_handler->get($block_id);
            $blockform = $block->getForm('clone');
            $xoopsTpl->assign('blockform', $blockform->render());
            // Call Footer
            xoops_cp_footer();
        } else {
            redirect_header('admin.php?fct=blocksadmin', 1, _AM_SYSTEM_DBERROR);
        }
        break;

}
