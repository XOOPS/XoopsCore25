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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          Xoop Notifications Select
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once $GLOBALS['xoops']->path('include/notification_constants.php');
include_once $GLOBALS['xoops']->path('include/notification_functions.php');

$xoops_notification         = array();
$xoops_notification['show'] = isset($xoopsModule) && is_object($xoopsUser) && notificationEnabled('inline') ? 1 : 0;
if ($xoops_notification['show']) {
    xoops_loadLanguage('notification');
    $categories  =& notificationSubscribableCategoryInfo();
    $event_count = 0;
    if (!empty($categories)) {
        /* @var  $notification_handler XoopsNotificationHandler */
        $notification_handler = xoops_getHandler('notification');
        foreach ($categories as $category) {
            $section['name']        = $category['name'];
            $section['title']       = $category['title'];
            $section['description'] = $category['description'];
            $section['itemid']      = $category['item_id'];
            $section['events']      = array();
            $subscribed_events      = $notification_handler->getSubscribedEvents($category['name'], $category['item_id'], $xoopsModule->getVar('mid'), $xoopsUser->getVar('uid'));
            foreach (notificationEvents($category['name'], true) as $event) {
                if (!empty($event['admin_only']) && !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                    continue;
                }
                if (!empty($event['invisible'])) {
                    continue;
                }
                $subscribed                        = in_array($event['name'], $subscribed_events) ? 1 : 0;
                $section['events'][$event['name']] = array(
                    'name'        => $event['name'],
                    'title'       => $event['title'],
                    'caption'     => $event['caption'],
                    'description' => $event['description'],
                    'subscribed'  => $subscribed);
                ++$event_count;
            }
            $xoops_notification['categories'][$category['name']] = $section;
        }
        $xoops_notification['target_page']     = 'notification_update.php';
        $xoops_notification['redirect_script'] = xoops_getenv('PHP_SELF');
        $xoopsTpl->assign(array(
                              'lang_activenotifications'  => _NOT_ACTIVENOTIFICATIONS,
                              'lang_notificationoptions'  => _NOT_NOTIFICATIONOPTIONS,
                              'lang_updateoptions'        => _NOT_UPDATEOPTIONS,
                              'lang_updatenow'            => _NOT_UPDATENOW,
                              'lang_category'             => _NOT_CATEGORY,
                              'lang_event'                => _NOT_EVENT,
                              'lang_events'               => _NOT_EVENTS,
                              'lang_checkall'             => _NOT_CHECKALL,
                              'lang_notificationmethodis' => _NOT_NOTIFICATIONMETHODIS,
                              'lang_change'               => _NOT_CHANGE,
                              'editprofile_url'           => XOOPS_URL . '/edituser.php?uid=' . $xoopsUser->getVar('uid')));
        switch ($xoopsUser->getVar('notify_method')) {
            case XOOPS_NOTIFICATION_METHOD_DISABLE:
                $xoopsTpl->assign('user_method', _NOT_DISABLE);
                break;
            case XOOPS_NOTIFICATION_METHOD_PM:
                $xoopsTpl->assign('user_method', _NOT_PM);
                break;
            case XOOPS_NOTIFICATION_METHOD_EMAIL:
                $xoopsTpl->assign('user_method', _NOT_EMAIL);
                break;
        }
    } else {
        $xoops_notification['show'] = 0;
    }
    if ($event_count == 0) {
        $xoops_notification['show'] = 0;
    }
}
$xoopsTpl->assign('xoops_notification', $xoops_notification);
