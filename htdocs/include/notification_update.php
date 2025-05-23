<?php
/**
 * XOOPS Notifications
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          Xoop Notifications Select
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    die('Restricted access');
}
// RMV-NOTIFY

// This module expects the following arguments:
//
// not_submit
// not_redirect (to return back after update)
// not_mid (TODO)
// not_uid (TODO)
// not_list[1][params] = {category},{itemid},{event}
// not_list[1][status] = 1 if selected; 0 or missing if not selected
// etc...
// TODO: can we put arguments in the not_redirect argument??? do we need
// to specially encode them first???
// TODO: allow 'GET' also so we can process 'unsubscribe' requests??

include_once $GLOBALS['xoops']->path('include/notification_constants.php');
include_once $GLOBALS['xoops']->path('include/notification_functions.php');
xoops_loadLanguage('notification');

if (!isset($_POST['not_submit'])) {
    redirect_header($_POST['not_redirect'], 3, _NOPERM);
}

if (!$GLOBALS['xoopsSecurity']->check()) {
    redirect_header($_POST['not_redirect'], 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
}

// NOTE: in addition to the templates provided in the block and view
// modes, we can have buttons, etc. which load the arguments to be
// read by this script.  That way a module can really customize its
// look as to where/how the notification options are made available.
$update_list = $_POST['not_list'];
$module_id   = $xoopsModule->getVar('mid');
$user_id     = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;

// For each event, update the notification depending on the status.
// If status=1, subscribe to the event; otherwise, unsubscribe.

// FIXME: right now I just ignore database errors (e.g. if already
//  subscribed)... deal with this more gracefully?
/** @var  XoopsNotificationHandler $notification_handler */
$notification_handler = xoops_getHandler('notification');
foreach ($update_list as $update_item) {
    [$category, $item_id, $event] = preg_split('/,/', $update_item['params']);
    $status = !empty($update_item['status']) ? 1 : 0;
    if (!$status) {
        $notification_handler->unsubscribe($category, $item_id, $event, $module_id, $user_id);
    } else {
        $notification_handler->subscribe($category, $item_id, $event);
    }
}

// TODO: something like grey box summary of actions (like multiple comment
// deletion), with a button to return back...  NOTE: we need some arguments
// to help us get back to where we were...

// TODO: finish integration with comments... i.e. need calls to
// notifyUsers at appropriate places... (need to figure out where
// comment submit occurs and where comment approval occurs)...
$redirect_args = [];
foreach ($update_list as $update_item) {
    [$category, $item_id, $event] = preg_split('/,/', $update_item['params']);
    $category_info =& notificationCategoryInfo($category);
    if (!empty($category_info['item_name'])) {
        $redirect_args[$category_info['item_name']] = $item_id;
    }
}

// TODO: write a central function to put together args with '?' and '&'
// symbols...
$argstring = '';
$first_arg = 1;
foreach (array_keys($redirect_args) as $arg) {
    if ($first_arg) {
        $argstring .= '?' . $arg . '=' . $redirect_args[$arg];
        $first_arg = 0;
    } else {
        $argstring .= '&' . $arg . '=' . $redirect_args[$arg];
    }
}

redirect_header($_POST['not_redirect'] . $argstring, 3, _NOT_UPDATEOK);
exit();
