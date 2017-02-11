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
include_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
$modid = isset($_POST['modid']) ? (int)$_POST['modid'] : 0;

// we don't want system module permissions to be changed here
if ($modid <= 1 || !is_object($xoopsUser) || !$xoopsUser->isAdmin($modid)) {
    redirect_header(XOOPS_URL . '/index.php', 1, _NOPERM);
}
/* @var $module_handler XoopsModuleHandler */
$module_handler = xoops_getHandler('module');
$module         = $module_handler->get($modid);
if (!is_object($module) || !$module->getVar('isactive')) {
    redirect_header(XOOPS_URL . '/admin.php', 1, _MODULENOEXIST);
}

$msg = array();

/* @var $member_handler XoopsMemberHandler */
$member_handler = xoops_getHandler('member');
$group_list     = $member_handler->getGroupList();

if (is_array($_POST['perms']) && !empty($_POST['perms'])) {
    /* @var  $gperm_handler XoopsGroupPermHandler */
    $gperm_handler = xoops_getHandler('groupperm');
    foreach ($_POST['perms'] as $perm_name => $perm_data) {
        if ($GLOBALS['xoopsSecurity']->check(true, false, $perm_name) && false !== $gperm_handler->deleteByModule($modid, $perm_name)) {
            foreach ($perm_data['groups'] as $group_id => $item_ids) {
                foreach ($item_ids as $item_id => $selected) {
                    if ($selected == 1) {
                        // make sure that all parent ids are selected as well
                        if ($perm_data['parents'][$item_id] !== '') {
                            $parent_ids = explode(':', $perm_data['parents'][$item_id]);
                            foreach ($parent_ids as $pid) {
                                //                                if ($pid != 0 && !in_array($pid, array_keys($item_ids))) {
                                if ($pid != 0 && !array_key_exists($pid, $item_ids)) {
                                    // one of the parent items were not selected, so skip this item
                                    $msg[] = sprintf(_MD_AM_PERMADDNG, '<strong>' . $perm_name . '</strong>', '<strong>' . $perm_data['itemname'][$item_id] . '</strong>', '<strong>' . $group_list[$group_id] . '</strong>') . ' (' . _MD_AM_PERMADDNGP . ')';
                                    continue 2;
                                }
                            }
                        }
                        /* @var $gperm XoopsGroupPerm */
                        $gperm = $gperm_handler->create();
                        $gperm->setVar('gperm_groupid', $group_id);
                        $gperm->setVar('gperm_name', $perm_name);
                        $gperm->setVar('gperm_modid', $modid);
                        $gperm->setVar('gperm_itemid', $item_id);
                        if (!$gperm_handler->insert($gperm)) {
                            $msg[] = sprintf(_MD_AM_PERMADDNG, '<strong>' . $perm_name . '</strong>', '<strong>' . $perm_data['itemname'][$item_id] . '</strong>', '<strong>' . $group_list[$group_id] . '</strong>');
                        } else {
                            $msg[] = sprintf(_MD_AM_PERMADDOK, '<strong>' . $perm_name . '</strong>', '<strong>' . $perm_data['itemname'][$item_id] . '</strong>', '<strong>' . $group_list[$group_id] . '</strong>');
                        }
                        unset($gperm);
                    }
                }
            }
        } else {
            $msg[] = sprintf(_MD_AM_PERMRESETNG, $module->getVar('name') . '(' . $perm_name . ')');
        }
    }
}

$backlink = xoops_getenv('HTTP_REFERER');
if ($module->getVar('hasadmin')) {
    $adminindex = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : $module->getInfo('adminindex');
    if ($adminindex) {
        $backlink = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $adminindex;
    }
}
$backlink = $backlink ?: XOOPS_URL . '/admin.php';

redirect_header($backlink, 2, implode('<br>', $msg));
