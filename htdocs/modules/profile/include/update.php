<?php
/**
 * Extended User Profile
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
 * @package             profile
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

$path = dirname(dirname(dirname(__DIR__)));
require_once $path . '/include' . '/cp_header.php';

/**
 * @param      $module
 * @param null $oldversion
 * @return bool
 */
/**
 * @param      $module
 * @param null $oldversion
 * @return bool
 */
function xoops_module_update_profile(XoopsModule $module, $oldversion = null)
{
    if ($oldversion < 162) {
        $GLOBALS['xoopsDB']->queryF('UPDATE `' . $GLOBALS['xoopsDB']->prefix('profile_field') . ' SET field_valuetype=2 WHERE field_name=umode');
    }

    if ($oldversion < 100) {

        // Drop old category table
        $sql = 'DROP TABLE ' . $GLOBALS['xoopsDB']->prefix('profile_category');
        $GLOBALS['xoopsDB']->queryF($sql);

        // Drop old field-category link table
        $sql = 'DROP TABLE ' . $GLOBALS['xoopsDB']->prefix('profile_fieldcategory');
        $GLOBALS['xoopsDB']->queryF($sql);

        // Create new tables for new profile module
        $GLOBALS['xoopsDB']->queryFromFile(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/sql/mysql.sql');

        include_once __DIR__ . '/install.php';
        xoops_module_install_profile($module);
        /* @var $goupperm_handler XoopsGroupPermHandler */
        $goupperm_handler = xoops_getHandler('groupperm');

        $field_handler = xoops_getModuleHandler('field', $module->getVar('dirname', 'n'));
        $skip_fields   = $field_handler->getUserVars();
        $skip_fields[] = 'newemail';
        $skip_fields[] = 'pm_link';
        $sql           = 'SELECT * FROM `' . $GLOBALS['xoopsDB']->prefix('user_profile_field') . "` WHERE `field_name` NOT IN ('" . implode("', '", $skip_fields) . "')";
        $result        = $GLOBALS['xoopsDB']->query($sql);
        $fields        = array();
        while (false !== ($myrow = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $fields[] = $myrow['field_name'];
            $object   = $field_handler->create();
            $object->setVars($myrow, true);
            $object->setVar('cat_id', 1);
            if (!empty($myrow['field_register'])) {
                $object->setVar('step_id', 2);
            }
            if (!empty($myrow['field_options'])) {
                $object->setVar('field_options', unserialize($myrow['field_options']));
            }
            $field_handler->insert($object, true);

            $gperm_itemid = $object->getVar('field_id');
            $sql          = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('group_permission') . ' SET gperm_itemid = ' . $gperm_itemid . '   WHERE gperm_itemid = ' . $myrow['fieldid'] . '       AND gperm_modid = ' . $module->getVar('mid') . "       AND gperm_name IN ('profile_edit', 'profile_search')";
            $GLOBALS['xoopsDB']->queryF($sql);

            $groups_visible = $goupperm_handler->getGroupIds('profile_visible', $myrow['fieldid'], $module->getVar('mid'));
            $groups_show    = $goupperm_handler->getGroupIds('profile_show', $myrow['fieldid'], $module->getVar('mid'));
            foreach ($groups_visible as $ugid) {
                foreach ($groups_show as $pgid) {
                    $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('profile_visibility') . ' (field_id, user_group, profile_group) ' . ' VALUES ' . " ({$gperm_itemid}, {$ugid}, {$pgid})";
                    $GLOBALS['xoopsDB']->queryF($sql);
                }
            }

            //profile_install_setPermissions($object->getVar('field_id'), $module->getVar('mid'), $canedit, $visible);
            unset($object);
        }

        // Copy data from profile table
        foreach ($fields as $field) {
            $GLOBALS['xoopsDB']->queryF('UPDATE `' . $GLOBALS['xoopsDB']->prefix('profile_profile') . '` u, `' . $GLOBALS['xoopsDB']->prefix('user_profile') . "` p SET u.{$field} = p.{$field} WHERE u.profile_id=p.profileid");
        }

        // Drop old profile table
        $sql = 'DROP TABLE ' . $GLOBALS['xoopsDB']->prefix('user_profile');
        $GLOBALS['xoopsDB']->queryF($sql);

        // Drop old field module
        $sql = 'DROP TABLE ' . $GLOBALS['xoopsDB']->prefix('user_profile_field');
        $GLOBALS['xoopsDB']->queryF($sql);

        // Remove not used items
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('group_permission') . '   WHERE `gperm_modid` = ' . $module->getVar('mid') . " AND `gperm_name` IN ('profile_show', 'profile_visible')";
        $GLOBALS['xoopsDB']->queryF($sql);
    }

    if ($oldversion < 162) {
        $GLOBALS['xoopsDB']->queryF('UPDATE `' . $GLOBALS['xoopsDB']->prefix('profile_field') . "` SET `field_valuetype`=1 WHERE `field_name`='umode'");
    }

    if ($oldversion < 186) {
        // delete old html template files
        $templateDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/templates/';
        $template_list     = array_diff(scandir($templateDirectory), array('..', '.'));
        foreach ($template_list as $k => $v) {
            $fileinfo = new SplFileInfo($templateDirectory . $v);
            if ($fileinfo->getExtension() === 'html' && $fileinfo->getFilename() !== 'index.html') {
                @unlink($templateDirectory . $v);
            }
        }

        xoops_load('xoopsfile');
        //delete /images directory
        $imagesDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/images/';
        $folderHandler   = XoopsFile::getHandler('folder', $imagesDirectory);
        $folderHandler->delete($imagesDirectory);
        //delete /templates/style.css file
        $cssFile       = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/templates/style.css';
        $folderHandler = XoopsFile::getHandler('file', $cssFile);
        $folderHandler->delete($cssFile);
        //delete .html entries from the tpl table
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE `tpl_module` = '" . $module->getVar('dirname', 'n') . "' AND `tpl_file` LIKE '%.html%'";
        $GLOBALS['xoopsDB']->queryF($sql);
    }

    if ($oldversion < 188) {
        // update user_sig field to use dhtml editor
        $tables = new Xmf\Database\Tables();
        $tables->useTable('profile_field');
        $criteria = new Criteria('field_name', 'user_sig', '=');
        $tables->update('profile_field', array('field_type' => 'dhtml'), $criteria);
        $tables->executeQueue(true);
    }

    $profile_handler = xoops_getModuleHandler('profile', $module->getVar('dirname', 'n'));
    $profile_handler->cleanOrphan($GLOBALS['xoopsDB']->prefix('users'), 'uid', 'profile_id');
    $field_handler = xoops_getModuleHandler('field', $module->getVar('dirname', 'n'));
    $user_fields   = $field_handler->getUserVars();
    $criteria      = new Criteria('field_name', "('" . implode("', '", $user_fields) . "')", 'IN');
    $field_handler->updateAll('field_config', 0, $criteria);

    return true;
}
