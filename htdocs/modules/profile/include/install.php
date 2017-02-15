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
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @param $module
 * @return bool
 */

function xoops_module_install_profile(XoopsModule $module)
{
    global $module_id;
    $module_id = $module->getVar('mid');
    xoops_loadLanguage('user');

    // Create registration steps
    profile_install_addStep(_PROFILE_MI_STEP_BASIC, '', 1, 0);
    profile_install_addStep(_PROFILE_MI_STEP_COMPLEMENTARY, '', 2, 1);

    // Create categories
    profile_install_addCategory(_PROFILE_MI_CATEGORY_PERSONAL, 1);
    profile_install_addCategory(_PROFILE_MI_CATEGORY_MESSAGING, 2);
    profile_install_addCategory(_PROFILE_MI_CATEGORY_SETTINGS, 3);
    profile_install_addCategory(_PROFILE_MI_CATEGORY_COMMUNITY, 4);

    // Add user fields
    xoops_loadLanguage('notification');
    xoops_loadLanguage('main', $module->getVar('dirname', 'n'));
    include_once $GLOBALS['xoops']->path('include/notification_constants.php');
    $umode_options         = array(
        'nest'   => _NESTED,
        'flat'   => _FLAT,
        'thread' => _THREADED);
    $uorder_options        = array(
        0 => _OLDESTFIRST,
        1 => _NEWESTFIRST);
    $notify_mode_options   = array(
        XOOPS_NOTIFICATION_MODE_SENDALWAYS         => _NOT_MODE_SENDALWAYS,
        XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE => _NOT_MODE_SENDONCE,
        XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT   => _NOT_MODE_SENDONCEPERLOGIN);
    $notify_method_options = array(
        XOOPS_NOTIFICATION_METHOD_DISABLE => _NOT_METHOD_DISABLE,
        XOOPS_NOTIFICATION_METHOD_PM      => _NOT_METHOD_PM,
        XOOPS_NOTIFICATION_METHOD_EMAIL   => _NOT_METHOD_EMAIL);

    profile_install_addField('name', _US_REALNAME, '', 1, 'textbox', 1, 1, 1, array(), 2, 255);
    profile_install_addField('user_from', _US_LOCATION, '', 1, 'textbox', 1, 2, 1, array(), 2, 255);
    profile_install_addField('timezone_offset', _US_TIMEZONE, '', 1, 'timezone', 1, 3, 1, array(), 2, 0);
    profile_install_addField('user_occ', _US_OCCUPATION, '', 1, 'textbox', 1, 4, 1, array(), 2, 255);
    profile_install_addField('user_intrest', _US_INTEREST, '', 1, 'textbox', 1, 5, 1, array(), 2, 255);
    profile_install_addField('bio', _US_EXTRAINFO, '', 1, 'textarea', 2, 6, 1, array(), 2, 0);
    profile_install_addField('user_regdate', _US_MEMBERSINCE, '', 1, 'datetime', 3, 7, 0, array(), 0, 10);

    profile_install_addField('user_icq', _US_ICQ, '', 2, 'textbox', 1, 1, 1, array(), 0, 255, false);
    profile_install_addField('user_aim', _US_AIM, '', 2, 'textbox', 1, 2, 1, array(), 0, 255, false);
    profile_install_addField('user_yim', _US_YIM, '', 2, 'textbox', 1, 3, 1, array(), 0, 255, false);
    profile_install_addField('user_msnm', _US_MSNM, '', 2, 'textbox', 1, 4, 1, array(), 0, 255, false);

    profile_install_addField('user_viewemail', _US_ALLOWVIEWEMAIL, '', 3, 'yesno', 3, 1, 1, array(), 2, 1, false);
    profile_install_addField('attachsig', _US_SHOWSIG, '', 3, 'yesno', 3, 2, 1, array(), 0, 1, false);
    profile_install_addField('user_mailok', _US_MAILOK, '', 3, 'yesno', 3, 3, 1, array(), 2, 1, false);
    profile_install_addField('theme', _PROFILE_MA_THEME, '', 3, 'theme', 1, 4, 1, array(), 0, 0, false);
    profile_install_addField('umode', _US_CDISPLAYMODE, '', 3, 'select', 1, 5, 1, $umode_options, 0, 0, false);
    profile_install_addField('uorder', _US_CSORTORDER, '', 3, 'select', 3, 6, 1, $uorder_options, 0, 0, false);
    profile_install_addField('notify_mode', _NOT_NOTIFYMODE, '', 3, 'select', 3, 7, 1, $notify_mode_options, 0, 0, false);
    profile_install_addField('notify_method', _NOT_NOTIFYMETHOD, '', 3, 'select', 3, 8, 1, $notify_method_options, 0, 0, false);

    profile_install_addField('url', _PROFILE_MI_URL_TITLE, '', 4, 'textbox', 1, 1, 1, array(), 2, 255);
    profile_install_addField('posts', _US_POSTS, '', 4, 'textbox', 3, 2, 0, array(), 0, 255);
    profile_install_addField('rank', _US_RANK, '', 4, 'rank', 3, 3, 2, array(), 0, 0);
    profile_install_addField('last_login', _US_LASTLOGIN, '', 4, 'datetime', 3, 4, 0, array(), 0, 10);
    profile_install_addField('user_sig', _US_SIGNATURE, '', 4, 'dhtml', 1, 5, 1, array(), 0, 0);

    profile_install_initializeProfiles();

    return true;
}

function profile_install_initializeProfiles()
{
    global $module_id;

    $GLOBALS['xoopsDB']->queryF('   INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('profile_profile') . ' (profile_id) ' . '   SELECT uid ' . '   FROM ' . $GLOBALS['xoopsDB']->prefix('users'));

    $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('group_permission') . ' (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) ' . ' VALUES ' . ' (' . XOOPS_GROUP_ADMIN . ', ' . XOOPS_GROUP_ADMIN . ", {$module_id}, 'profile_access'), " . ' (' . XOOPS_GROUP_ADMIN . ', ' . XOOPS_GROUP_USERS . ", {$module_id}, 'profile_access'), " . ' (' . XOOPS_GROUP_USERS . ', ' . XOOPS_GROUP_USERS . ", {$module_id}, 'profile_access'), " . ' (' . XOOPS_GROUP_ANONYMOUS . ', ' . XOOPS_GROUP_USERS . ", {$module_id}, 'profile_access') " . ' ';
    $GLOBALS['xoopsDB']->queryF($sql);
}

// canedit: 0 - no; 1 - admin; 2 - admin & owner
/**
 * @param      $name
 * @param      $title
 * @param      $description
 * @param      $category
 * @param      $type
 * @param      $valuetype
 * @param      $weight
 * @param      $canedit
 * @param      $options
 * @param      $step_id
 * @param      $length
 * @param bool $visible
 *
 * @return bool
 */
function profile_install_addField($name, $title, $description, $category, $type, $valuetype, $weight, $canedit, $options, $step_id, $length, $visible = true)
{
    global $module_id;

    $profilefield_handler = xoops_getModuleHandler('field', 'profile');
    $obj                  = $profilefield_handler->create();
    $obj->setVar('field_name', $name, true);
    $obj->setVar('field_moduleid', $module_id, true);
    $obj->setVar('field_show', 1);
    $obj->setVar('field_edit', $canedit ? 1 : 0);
    $obj->setVar('field_config', 0);
    $obj->setVar('field_title', strip_tags($title), true);
    $obj->setVar('field_description', strip_tags($description), true);
    $obj->setVar('field_type', $type, true);
    $obj->setVar('field_valuetype', $valuetype, true);
    $obj->setVar('field_options', $options, true);
    if ($canedit) {
        $obj->setVar('field_maxlength', $length, true);
    }

    $obj->setVar('field_weight', $weight, true);
    $obj->setVar('cat_id', $category, true);
    $obj->setVar('step_id', $step_id, true);
    $profilefield_handler->insert($obj);

    profile_install_setPermissions($obj->getVar('field_id'), $module_id, $canedit, $visible);

    return true;
    /*
    //$GLOBALS['xoopsDB']->query("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("profile_field")." VALUES (0, {$category}, '{$type}', {$valuetype}, '{$name}', " . $GLOBALS['xoopsDB']->quote($title) . ", " . $GLOBALS['xoopsDB']->quote($description) . ", 0, {$length}, {$weight}, '', 1, {$canedit}, 1, 0, '" . serialize($options) . "', {$step_id})");
    $gperm_itemid = $obj->getVar('field_id');
    unset($obj);
    $gperm_modid = $module_id;
    $sql = "INSERT INTO " . $GLOBALS['xoopsDB']->prefix("group_permission") .
        " (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) " .
        " VALUES " .
        ($canedit ?
            " (" . XOOPS_GROUP_ADMIN . ", {$gperm_itemid}, {$gperm_modid}, 'profile_edit'), "
        : "" ) .
        ($canedit == 1 ?
            " (" . XOOPS_GROUP_USERS . ", {$gperm_itemid}, {$gperm_modid}, 'profile_edit'), "
        : "" ) .
        " (" . XOOPS_GROUP_ADMIN . ", {$gperm_itemid}, {$gperm_modid}, 'profile_search'), " .
        " (" . XOOPS_GROUP_USERS . ", {$gperm_itemid}, {$gperm_modid}, 'profile_search') " .
        " ";
    $GLOBALS['xoopsDB']->query($sql);

    if ($visible) {
        $sql = "INSERT INTO " . $GLOBALS['xoopsDB']->prefix("profile_visibility") .
            " (field_id, user_group, profile_group) " .
            " VALUES " .
            " ({$gperm_itemid}, " . XOOPS_GROUP_ADMIN . ", " . XOOPS_GROUP_ADMIN . "), " .
            " ({$gperm_itemid}, " . XOOPS_GROUP_ADMIN . ", " . XOOPS_GROUP_USERS . "), " .
            " ({$gperm_itemid}, " . XOOPS_GROUP_USERS . ", " . XOOPS_GROUP_ADMIN . "), " .
            " ({$gperm_itemid}, " . XOOPS_GROUP_USERS . ", " . XOOPS_GROUP_USERS . "), " .
            " ({$gperm_itemid}, " . XOOPS_GROUP_ANONYMOUS . ", " . XOOPS_GROUP_ADMIN . "), " .
            " ({$gperm_itemid}, " . XOOPS_GROUP_ANONYMOUS . ", " . XOOPS_GROUP_USERS . ")" .
            " ";
        $GLOBALS['xoopsDB']->query($sql);
    }
    */
}

/**
 * @param $field_id
 * @param $module_id
 * @param $canedit
 * @param $visible
 */
function profile_install_setPermissions($field_id, $module_id, $canedit, $visible)
{
    $gperm_itemid = $field_id;
    $gperm_modid  = $module_id;
    $sql          = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('group_permission') . ' (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) ' . ' VALUES ' . ($canedit ? ' (' . XOOPS_GROUP_ADMIN . ", {$gperm_itemid}, {$gperm_modid}, 'profile_edit'), " : '') . ($canedit == 1 ? ' (' . XOOPS_GROUP_USERS . ", {$gperm_itemid}, {$gperm_modid}, 'profile_edit'), " : '') . ' (' . XOOPS_GROUP_ADMIN . ", {$gperm_itemid}, {$gperm_modid}, 'profile_search'), " . ' (' . XOOPS_GROUP_USERS . ", {$gperm_itemid}, {$gperm_modid}, 'profile_search') " . ' ';
    $GLOBALS['xoopsDB']->queryF($sql);

    if ($visible) {
        $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('profile_visibility') . ' (field_id, user_group, profile_group) ' . ' VALUES ' . " ({$gperm_itemid}, " . XOOPS_GROUP_ADMIN . ', ' . XOOPS_GROUP_ADMIN . '), ' . " ({$gperm_itemid}, " . XOOPS_GROUP_ADMIN . ', ' . XOOPS_GROUP_USERS . '), ' . " ({$gperm_itemid}, " . XOOPS_GROUP_USERS . ', ' . XOOPS_GROUP_ADMIN . '), ' . " ({$gperm_itemid}, " . XOOPS_GROUP_USERS . ', ' . XOOPS_GROUP_USERS . '), ' . " ({$gperm_itemid}, " . XOOPS_GROUP_ANONYMOUS . ', ' . XOOPS_GROUP_ADMIN . '), ' . " ({$gperm_itemid}, " . XOOPS_GROUP_ANONYMOUS . ', ' . XOOPS_GROUP_USERS . ')' . ' ';
        $GLOBALS['xoopsDB']->queryF($sql);
    }
}

/**
 * @param $name
 * @param $weight
 */
function profile_install_addCategory($name, $weight)
{
    $GLOBALS['xoopsDB']->query('INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('profile_category') . ' VALUES (0, ' . $GLOBALS['xoopsDB']->quote($name) . ", '', {$weight})");
}

/**
 * @param $name
 * @param $desc
 * @param $order
 * @param $save
 */
function profile_install_addStep($name, $desc, $order, $save)
{
    $GLOBALS['xoopsDB']->query('INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('profile_regstep') . ' VALUES (0, ' . $GLOBALS['xoopsDB']->quote($name) . ', ' . $GLOBALS['xoopsDB']->quote($desc) . ", {$order}, {$save})");
}
