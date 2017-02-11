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
 * @subpackage          Xoop Notifications Functions
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

// RMV-NOTIFY

// FIXME: Do some caching, so we don't retrieve the same category / event
// info many times.

/**
 * Determine if notification is enabled for the selected module.
 *
 * @param  string $style     Subscription style: 'block' or 'inline'
 * @param  int    $module_id ID of the module  (default current module)
 * @return bool
 */
function notificationEnabled($style, $module_id = null)
{
    if (isset($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
        $status = $GLOBALS['xoopsModuleConfig']['notification_enabled'];
    } else {
        if (!isset($module_id)) {
            return false;
        }
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $module         = $module_handler->get($module_id);
        if (!empty($module) && $module->getVar('hasnotification') == 1) {
            /* @var $config_handler XoopsConfigHandler  */
            $config_handler = xoops_getHandler('config');
            $config         = $config_handler->getConfigsByCat(0, $module_id);
            $status         = $config['notification_enabled'];
        } else {
            return false;
        }
    }
    include_once $GLOBALS['xoops']->path('include/notification_constants.php');
    if (($style === 'block') && ($status === XOOPS_NOTIFICATION_ENABLEBLOCK || $status === XOOPS_NOTIFICATION_ENABLEBOTH)) {
        return true;
    }

    return ($style === 'inline') && ($status === XOOPS_NOTIFICATION_ENABLEINLINE || $status === XOOPS_NOTIFICATION_ENABLEBOTH);
}

/**
 * Get an associative array of info for a particular notification
 * category in the selected module.  If no category is selected,
 * return an array of info for all categories.
 *
 * @param string $category_name
 * @param  int   $module_id ID of the module (default current module)
 *
 * @internal param string $name Category name (default all categories)
 * @return mixed
 */
function &notificationCategoryInfo($category_name = '', $module_id = null)
{
    if (!isset($module_id)) {
        global $xoopsModule;
        $module_id = !empty($xoopsModule) ? $xoopsModule->getVar('mid') : 0;
        $module    =& $xoopsModule;
    } else {
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $module         = $module_handler->get($module_id);
    }
    $not_config = &$module->getInfo('notification');
    if (empty($category_name)) {
        return $not_config['category'];
    }
    foreach ($not_config['category'] as $category) {
        if ($category['name'] == $category_name) {
            return $category;
        }
    }
    $ret = false;

    return $ret;
}

/**
 * Get associative array of info for the category to which comment events
 * belong.
 *
 * @todo This could be more efficient... maybe specify in
 *        $modversion['comments'] the notification category.
 *       This would also serve as a way to enable notification
 *        of comments, and also remove the restriction that
 *        all notification categories must have unique item_name. (TODO)
 *
 * @param  int $module_id ID of the module (default current module)
 * @return mixed            Associative array of category info
 */
function &notificationCommentCategoryInfo($module_id = null)
{
    $ret            = false;
    $all_categories =& notificationCategoryInfo('', $module_id);
    if (empty($all_categories)) {
        return $ret;
    }
    foreach ($all_categories as $category) {
        $all_events =& notificationEvents($category['name'], false, $module_id);
        if (empty($all_events)) {
            continue;
        }
        foreach ($all_events as $event) {
            if ($event['name'] === 'comment') {
                return $category;
            }
        }
    }

    return $ret;
}

// TODO: some way to include or exclude admin-only events...

/**
 * Get an array of info for all events (each event has associative array)
 * in the selected category of the selected module.
 *
 * @param  string $category_name Category name
 * @param  bool   $enabled_only  If true, return only enabled events
 * @param  int    $module_id     ID of the module (default current module)
 * @return mixed
 */
function &notificationEvents($category_name, $enabled_only, $module_id = null)
{
    if (!isset($module_id)) {
        global $xoopsModule;
        $module_id = !empty($xoopsModule) ? $xoopsModule->getVar('mid') : 0;
        $module    =& $xoopsModule;
    } else {
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $module         = $module_handler->get($module_id);
    }
    $not_config     = $module->getInfo('notification');
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler = xoops_getHandler('config');
    $mod_config     = $config_handler->getConfigsByCat(0, $module_id);

    $category =& notificationCategoryInfo($category_name, $module_id);

    global $xoopsConfig;
    $event_array = array();

    $override_comment       = false;
    $override_commentsubmit = false;
    $override_bookmark      = false;

    foreach ($not_config['event'] as $event) {
        if ($event['category'] == $category_name) {
            if (!is_dir($dir = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/mail_template/')) {
                $dir = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname') . '/language/english/mail_template/';
            }
            $event['mail_template_dir'] = $dir;
            if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
                $event_array[] = $event;
            }
            if ($event['name'] === 'comment') {
                $override_comment = true;
            }
            if ($event['name'] === 'comment_submit') {
                $override_commentsubmit = true;
            }
            if ($event['name'] === 'bookmark') {
                $override_bookmark = true;
            }
        }
    }

    xoops_loadLanguage('notification');
    // Insert comment info if applicable

    if ($module->getVar('hascomments')) {
        $com_config = $module->getInfo('comments');
        if (!empty($category['item_name']) && $category['item_name'] == $com_config['itemName']) {
            if (!is_dir($dir = XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/mail_template/')) {
                $dir = XOOPS_ROOT_PATH . '/language/english/mail_template/';
            }
            $mail_template_dir = $dir;

            include_once $GLOBALS['xoops']->path('include/comment_constants.php');
            $config_handler = xoops_getHandler('config');
            $com_config     = $config_handler->getConfigsByCat(0, $module_id);
            if (!$enabled_only) {
                $insert_comment = true;
                $insert_submit  = true;
            } else {
                $insert_comment = false;
                $insert_submit  = false;
                switch ($com_config['com_rule']) {
                    case XOOPS_COMMENT_APPROVENONE:
                        // comments disabled, no comment events
                        break;
                    case XOOPS_COMMENT_APPROVEALL:
                        // all comments are automatically approved, no 'submit'
                        if (!$override_comment) {
                            $insert_comment = true;
                        }
                        break;
                    case XOOPS_COMMENT_APPROVEUSER:
                    case XOOPS_COMMENT_APPROVEADMIN:
                        // comments first submitted, require later approval
                        if (!$override_comment) {
                            $insert_comment = true;
                        }
                        if (!$override_commentsubmit) {
                            $insert_submit = true;
                        }
                        break;
                }
            }
            if ($insert_comment) {
                $event = array(
                    'name'              => 'comment',
                    'category'          => $category['name'],
                    'title'             => _NOT_COMMENT_NOTIFY,
                    'caption'           => _NOT_COMMENT_NOTIFYCAP,
                    'description'       => _NOT_COMMENT_NOTIFYDSC,
                    'mail_template_dir' => $mail_template_dir,
                    'mail_template'     => 'comment_notify',
                    'mail_subject'      => _NOT_COMMENT_NOTIFYSBJ);
                if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
                    $event_array[] = $event;
                }
            }
            if ($insert_submit) {
                $event = array(
                    'name'              => 'comment_submit',
                    'category'          => $category['name'],
                    'title'             => _NOT_COMMENTSUBMIT_NOTIFY,
                    'caption'           => _NOT_COMMENTSUBMIT_NOTIFYCAP,
                    'description'       => _NOT_COMMENTSUBMIT_NOTIFYDSC,
                    'mail_template_dir' => $mail_template_dir,
                    'mail_template'     => 'commentsubmit_notify',
                    'mail_subject'      => _NOT_COMMENTSUBMIT_NOTIFYSBJ,
                    'admin_only'        => 1);
                if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
                    $event_array[] = $event;
                }
            }
        }
    }

    // Insert bookmark info if appropriate

    if (!empty($category['allow_bookmark'])) {
        if (!$override_bookmark) {
            $event = array(
                'name'        => 'bookmark',
                'category'    => $category['name'],
                'title'       => _NOT_BOOKMARK_NOTIFY,
                'caption'     => _NOT_BOOKMARK_NOTIFYCAP,
                'description' => _NOT_BOOKMARK_NOTIFYDSC);
            if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
                $event_array[] = $event;
            }
        }
    }

    return $event_array;
}

/**
 * Determine whether a particular notification event is enabled.
 * Depends on module config options.
 *
 * @todo  Check that this works correctly for comment and other
 *   events which depend on additional config options...
 *
 * @param  array  $category Category info array
 * @param  array  $event    Event info array
 * @param  object $module   Module
 * @return bool
 **/
function notificationEventEnabled(&$category, &$event, &$module)
{
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler = xoops_getHandler('config');
    $mod_config     = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

    if (is_array($mod_config['notification_events']) && $mod_config['notification_events'] != array()) {
        $option_name = notificationGenerateConfig($category, $event, 'option_name');
        if (in_array($option_name, $mod_config['notification_events'])) {
            return true;
        }
        $notification_handler = xoops_getHandler('notification');
    }

    return false;
}

/**
 * Get associative array of info for the selected event in the selected
 * category (for the selected module).
 *
 * @param  string $category_name Notification category
 * @param  string $event_name    Notification event
 * @param  int    $module_id     ID of the module (default current module)
 * @return mixed
 */
function &notificationEventInfo($category_name, $event_name, $module_id = null)
{
    $all_events =& notificationEvents($category_name, false, $module_id);
    foreach ($all_events as $event) {
        if ($event['name'] == $event_name) {
            return $event;
        }
    }
    $ret = false;

    return $ret;
}

/**
 * Get an array of associative info arrays for subscribable categories
 * for the selected module.
 *
 * @param  int $module_id ID of the module
 * @return mixed
 */

function &notificationSubscribableCategoryInfo($module_id = null)
{
    $all_categories =& notificationCategoryInfo('', $module_id);

    // FIXME: better or more standardized way to do this?
    $script_url  = explode('/', $_SERVER['PHP_SELF']);
    $script_name = $script_url[count($script_url) - 1];

    $sub_categories = array();
    if (null != $all_categories) {
    foreach ($all_categories as $category) {
        // Check the script name
        $subscribe_from = $category['subscribe_from'];
        if (!is_array($subscribe_from)) {
            if ($subscribe_from === '*') {
                $subscribe_from = array(
                    $script_name);
                // FIXME: this is just a hack: force a match
            } else {
                $subscribe_from = array(
                    $subscribe_from);
            }
        }
        if (!in_array($script_name, $subscribe_from)) {
            continue;
        }
        // If 'item_name' is missing, automatic match.  Otherwise
        // check if that argument exists...
        if (empty($category['item_name'])) {
            $category['item_name'] = '';
            $category['item_id']   = 0;
            $sub_categories[]      = $category;
        } else {
            $item_name = $category['item_name'];
            $id        = ($item_name != '' && isset($_GET[$item_name])) ? (int)$_GET[$item_name] : 0;
            if ($id > 0) {
                $category['item_id'] = $id;
                $sub_categories[]    = $category;
            }
        }
    }
    }
    return $sub_categories;
}

/**
 * Generate module config info for a particular category, event pair.
 * The selectable config options are given names depending on the
 * category and event names, and the text depends on the category
 * and event titles.  These are pieced together in this function in
 * case we wish to alter the syntax.
 *
 * @param  array  $category Array of category info
 * @param  array  $event    Array of event info
 * @param  string $type     The particular name to generate
 *                          return string
 *                          *
 *
 * @return bool|string
 */
function notificationGenerateConfig(&$category, &$event, $type)
{
    switch ($type) {
        case 'option_value':
        case 'name':
            return 'notify:' . $category['name'] . '-' . $event['name'];
            break;
        case 'option_name':
            return $category['name'] . '-' . $event['name'];
            break;
        default:
            return false;
            break;
    }
}
