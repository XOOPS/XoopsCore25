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
/* @var  $xoopsUser XoopsUser */
/* @var $xoopsModule XoopsModule */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
// Parameters
$nb_group = xoops_getModuleOption('groups_pager', 'system');
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');
// Get groups handler
/* @var $groups_Handler SystemGroupHandler */
$groups_Handler = xoops_getModuleHandler('group', 'system');
/* @var $member_handler XoopsMemberHandler */
$member_handler = xoops_getHandler('member');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_groups.tpl';
// Call Header
xoops_cp_header();
$xoBreadCrumb->addLink(_AM_SYSTEM_GROUPS_NAV_MANAGER, system_adminVersion('groups', 'adminpath'));

switch ($op) {

    case 'list':
    default:
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addHelp(system_adminVersion('groups', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_GROUPS_NAV_TIPS_1);
        $xoBreadCrumb->render();
        // Get start pager
        $start = system_CleanVars($_REQUEST, 'start', 0, 'int');
        // Criteria
        $criteria = new CriteriaCompo();
        $criteria->setSort('groupid');
        $criteria->setOrder('ASC');
        $criteria->setStart($start);
        $criteria->setLimit($nb_group);
        // Count group
        $groups_count = $groups_Handler->getCount($criteria);
        $groups_arr   = $groups_Handler->getall($criteria);
        // Assign Template variables
        $xoopsTpl->assign('groups_count', $groups_count);
        if ($groups_count > 0) {
            foreach (array_keys($groups_arr) as $i) {
                $groups_id             = $groups_arr[$i]->getVar('groupid');
                $groups['groups_id']   = $groups_id;
                $groups['name']        = $groups_arr[$i]->getVar('name');
                $groups['description'] = $groups_arr[$i]->getVar('description');
                /* @var $member_handler SystemMemberHandler */
                $member_handler        = xoops_getHandler('member', 'system');
                if ($groups_id != XOOPS_GROUP_ANONYMOUS) {
                    $group_id_arr[0]              = $groups_id;
                    $nb_users_by_groups           = $member_handler->getUserCountByGroupLink($group_id_arr);
                    $groups['nb_users_by_groups'] = sprintf(_AM_SYSTEM_GROUPS_NB_USERS_BY_GROUPS_USERS, $nb_users_by_groups);
                } else {
                    $groups['nb_users_by_groups'] = '';
                }
                $edit_delete = '<a href="admin.php?fct=groups&amp;op=groups_edit&amp;groups_id=' . $groups_id . '">
                                           <img src="./images/icons/edit.png" border="0" alt="' . _AM_SYSTEM_GROUPS_EDIT . '" title="' . _AM_SYSTEM_GROUPS_EDIT . '"></a>';
                if (!in_array($groups_arr[$i]->getVar('groupid'), array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS))) {
                    $groups['delete'] = 1;
                    $edit_delete .= '<a href="admin.php?fct=groups&amp;op=groups_delete&amp;groups_id=' . $groups_id . '">
                                     <img src="./images/icons/delete.png" border="0" alt="' . _AM_SYSTEM_GROUPS_DELETE . '" title="' . _AM_SYSTEM_GROUPS_DELETE . '"></a>';
                }
                $groups['edit_delete'] = $edit_delete;
                $xoopsTpl->append_by_ref('groups', $groups);
                unset($groups);
            }
        }
        // Display Page Navigation
        if ($groups_count > $nb_group) {
            $nav = new XoopsPageNav($groups_count, $nb_group, $start, 'start', 'fct=groups&amp;op=list');
            $xoopsTpl->assign('nav_menu', $nav->renderNav(4));
        }
        break;

    //Add a group
    case 'groups_add':
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_GROUPS_NAV_ADD);
        $xoBreadCrumb->addHelp(system_adminVersion('groups', 'help') . '#add');
        $xoBreadCrumb->addTips(_AM_SYSTEM_GROUPS_NAV_TIPS_2);
        $xoBreadCrumb->render();
        // Create form
        $obj  = $groups_Handler->create();
        $form = $obj->getForm();
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        break;

    //Edit a group
    case 'groups_edit':
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_GROUPS_NAV_EDIT);
        $xoBreadCrumb->addHelp(system_adminVersion('groups', 'help') . '#edit');
        $xoBreadCrumb->addTips(_AM_SYSTEM_GROUPS_NAV_TIPS_2);
        $xoBreadCrumb->render();
        // Create form
        $groups_id = system_CleanVars($_REQUEST, 'groups_id', 0, 'int');
        if ($groups_id > 0) {
            $obj  = $groups_Handler->get($groups_id);
            $form = $obj->getForm();
            // Assign form
            $xoopsTpl->assign('form', $form->render());
        } else {
            redirect_header('admin.php?fct=groups', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    //Save a new group
    case 'groups_save_add':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=groups', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $system_catids = system_CleanVars($_POST, 'system_catids', array(), 'array');
        $admin_mids    = system_CleanVars($_POST, 'admin_mids', array(), 'array');
        $read_mids     = system_CleanVars($_POST, 'read_mids', array(), 'array');
        $read_bids     = system_CleanVars($_POST, 'read_bids', array(), 'array');
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $group          = $member_handler->createGroup();
        $group->setVar('name', $_POST['name']);
        $group->setVar('description', $_POST['desc']);
        if (count($system_catids) > 0) {
            $group->setVar('group_type', 'Admin');
        }
        if (!$member_handler->insertGroup($group)) {
            xoops_cp_header();
            xoops_error($group->getHtmlErrors());
            xoops_cp_footer();
        } else {
            $groupid       = $group->getVar('groupid');
            /* @var  $gperm_handler XoopsGroupPermHandler */
            $gperm_handler = xoops_getHandler('groupperm');
            if (count($system_catids) > 0) {
                $admin_mids[] = 1;
                foreach ($system_catids as $s_cid) {
                    $sysperm = $gperm_handler->create();
                    $sysperm->setVar('gperm_groupid', $groupid);
                    $sysperm->setVar('gperm_itemid', $s_cid);
                    $sysperm->setVar('gperm_name', 'system_admin');
                    $sysperm->setVar('gperm_modid', 1);
                    $gperm_handler->insert($sysperm);
                }
            }
            foreach ($admin_mids as $a_mid) {
                $modperm = $gperm_handler->create();
                $modperm->setVar('gperm_groupid', $groupid);
                $modperm->setVar('gperm_itemid', $a_mid);
                $modperm->setVar('gperm_name', 'module_admin');
                $modperm->setVar('gperm_modid', 1);
                $gperm_handler->insert($modperm);
            }
            $read_mids[] = 1;
            foreach ($read_mids as $r_mid) {
                $modperm = $gperm_handler->create();
                $modperm->setVar('gperm_groupid', $groupid);
                $modperm->setVar('gperm_itemid', $r_mid);
                $modperm->setVar('gperm_name', 'module_read');
                $modperm->setVar('gperm_modid', 1);
                $gperm_handler->insert($modperm);
            }
            foreach ($read_bids as $r_bid) {
                $blockperm = $gperm_handler->create();
                $blockperm->setVar('gperm_groupid', $groupid);
                $blockperm->setVar('gperm_itemid', $r_bid);
                $blockperm->setVar('gperm_name', 'block_read');
                $blockperm->setVar('gperm_modid', 1);
                $gperm_handler->insert($blockperm);
            }
            redirect_header('admin.php?fct=groups', 1, _AM_SYSTEM_GROUPS_DBUPDATED);
        }
        break;

    //Save a edit group
    case 'groups_save_update':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=groups', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $system_catids = system_CleanVars($_POST, 'system_catids', array(), 'array');
        $admin_mids    = system_CleanVars($_POST, 'admin_mids', array(), 'array');
        $read_mids     = system_CleanVars($_POST, 'read_mids', array(), 'array');
        $read_bids     = system_CleanVars($_POST, 'read_bids', array(), 'array');
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $gid            = system_CleanVars($_POST, 'g_id', 0, 'int');
        if ($gid > 0) {
            $group = $member_handler->getGroup($gid);
            $group->setVar('name', $_POST['name']);
            $group->setVar('description', $_POST['desc']);
            // if this group is not one of the default groups
            if (!in_array($group->getVar('groupid'), array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS))) {
                if (count($system_catids) > 0) {
                    $group->setVar('group_type', 'Admin');
                } else {
                    $group->setVar('group_type', '');
                }
            }
            if (!$member_handler->insertGroup($group)) {
                xoops_cp_header();
                echo $group->getHtmlErrors();
                xoops_cp_footer();
            } else {
                $groupid       = $group->getVar('groupid');
                /* @var  $gperm_handler XoopsGroupPermHandler */
                $gperm_handler = xoops_getHandler('groupperm');
                $criteria      = new CriteriaCompo(new Criteria('gperm_groupid', $groupid));
                $criteria->add(new Criteria('gperm_modid', 1));
                $criteria2 = new CriteriaCompo(new Criteria('gperm_name', 'system_admin'));
                $criteria2->add(new Criteria('gperm_name', 'module_admin'), 'OR');
                $criteria2->add(new Criteria('gperm_name', 'module_read'), 'OR');
                $criteria2->add(new Criteria('gperm_name', 'block_read'), 'OR');
                $criteria->add($criteria2);
                $gperm_handler->deleteAll($criteria);
                if (count($system_catids) > 0) {
                    $admin_mids[] = 1;
                    foreach ($system_catids as $s_cid) {
                        $sysperm = $gperm_handler->create();
                        $sysperm->setVar('gperm_groupid', $groupid);
                        $sysperm->setVar('gperm_itemid', $s_cid);
                        $sysperm->setVar('gperm_name', 'system_admin');
                        $sysperm->setVar('gperm_modid', 1);
                        $gperm_handler->insert($sysperm);
                    }
                }
                foreach ($admin_mids as $a_mid) {
                    $modperm = $gperm_handler->create();
                    $modperm->setVar('gperm_groupid', $groupid);
                    $modperm->setVar('gperm_itemid', $a_mid);
                    $modperm->setVar('gperm_name', 'module_admin');
                    $modperm->setVar('gperm_modid', 1);
                    $gperm_handler->insert($modperm);
                }
                $read_mids[] = 1;
                foreach ($read_mids as $r_mid) {
                    $modperm = $gperm_handler->create();
                    $modperm->setVar('gperm_groupid', $groupid);
                    $modperm->setVar('gperm_itemid', $r_mid);
                    $modperm->setVar('gperm_name', 'module_read');
                    $modperm->setVar('gperm_modid', 1);
                    $gperm_handler->insert($modperm);
                }
                foreach ($read_bids as $r_bid) {
                    $blockperm = $gperm_handler->create();
                    $blockperm->setVar('gperm_groupid', $groupid);
                    $blockperm->setVar('gperm_itemid', $r_bid);
                    $blockperm->setVar('gperm_name', 'block_read');
                    $blockperm->setVar('gperm_modid', 1);
                    $gperm_handler->insert($blockperm);
                }
                redirect_header('admin.php?fct=groups', 1, _AM_SYSTEM_GROUPS_DBUPDATED);
            }
        } else {
            redirect_header('admin.php?fct=groups', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    //Del a group
    case 'groups_delete':
        $groups_id = system_CleanVars($_REQUEST, 'groups_id', 0, 'int');
        if ($groups_id > 0) {
            $obj = $groups_Handler->get($groups_id);
            if (isset($_POST['ok']) && $_POST['ok'] == 1) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('admin.php?fct=groups', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                if ($groups_id > 0 && !in_array($groups_id, array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS))) {
                    /* @var $member_handler XoopsMemberHandler */
                    $member_handler = xoops_getHandler('member');
                    $group          = $member_handler->getGroup($groups_id);
                    $member_handler->deleteGroup($group);
                    /* @var $gperm_handler XoopsGroupPermHandler */
                    $gperm_handler = xoops_getHandler('groupperm');
                    $gperm_handler->deleteByGroup($groups_id);
                    redirect_header('admin.php?fct=groups', 1, _AM_SYSTEM_GROUPS_DBUPDATED);
                } else {
                    redirect_header('admin.php?fct=groups', 2, _AM_SYSTEM_GROUPS_ERROR_DELETE);
                }
            } else {
                // Define Stylesheet
                $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
                // Define Breadcrumb and tips
                $xoBreadCrumb->addLink(_AM_SYSTEM_GROUPS_NAV_DELETE);
                $xoBreadCrumb->addHelp(system_adminVersion('groups', 'help') . '#edit');
                $xoBreadCrumb->render();
                // Display message
                xoops_confirm(array(
                                  'ok' => 1,
                                  'groups_id' => $_REQUEST['groups_id'],
                                  'op' => 'groups_delete'), 'admin.php?fct=groups', sprintf(_AM_SYSTEM_GROUPS_SUREDEL) . '<br \>' . $obj->getVar('name') . '<br \>');
            }
        } else {
            redirect_header('admin.php?fct=groups', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    //Add users group
    case 'action_group':
        $error = true;
        if (isset($_REQUEST['edit_group'])) {
            if (isset($_REQUEST['edit_group']) && $_REQUEST['edit_group'] === 'add_group' && isset($_REQUEST['selgroups'])) {
                foreach ($_REQUEST['memberslist_id'] as $uid) {
                    $member_handler->addUserToGroup($_REQUEST['selgroups'], $uid);
                    $error = false;
                }
            } elseif (isset($_REQUEST['edit_group']) && $_REQUEST['edit_group'] === 'delete_group' && isset($_REQUEST['selgroups'])) {
                $member_handler->removeUsersFromGroup($_REQUEST['selgroups'], $_REQUEST['memberslist_id']);
                $error = false;
            }
            //if ($error === true)
            redirect_header('admin.php?fct=users', 1, _AM_SYSTEM_GROUPS_DBUPDATED);
        }
        break;
}
// Call Footer
xoops_cp_footer();
