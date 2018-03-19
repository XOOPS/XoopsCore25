<?php
/**
 * XOOPS Kernel Class
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
 * @since               2.0.0
 * @author              Michael van Dam <mvandam@caltech.edu>
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

// RMV-NOTIFY
include_once $GLOBALS['xoops']->path('include/notification_constants.php');
include_once $GLOBALS['xoops']->path('include/notification_functions.php');

/**
 * A Notification
 *
 * @package             kernel
 * @subpackage          notification
 *
 * @author              Michael van Dam    <mvandam@caltech.edu>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsNotification extends XoopsObject
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
        $this->initVar('not_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('not_modid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('not_category', XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('not_itemid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('not_event', XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('not_uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('not_mode', XOBJ_DTYPE_INT, 0, false);
    }

    // FIXME:???
    // To send email to multiple users simultaneously, we would need to move
    // the notify functionality to the handler class.  BUT, some of the tags
    // are user-dependent, so every email msg will be unique.  (Unless maybe use
    // smarty for email templates in the future.)  Also we would have to keep
    // track if each user wanted email or PM.

    /**
     * Returns Class Base Variable not_id
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('not_id', $format);
    }

    /**
     * Returns Class Base Variable not_id
     * @param  string $format
     * @return mixed
     */
    public function not_id($format = '')
    {
        return $this->getVar('not_id', $format);
    }

    /**
     * Returns Class Base Variable not_modid
     * @param  string $format
     * @return mixed
     */
    public function not_modid($format = '')
    {
        return $this->getVar('not_modid', $format);
    }

    /**
     * Returns Class Base Variable mid
     * @param  string $format
     * @return mixed
     */
    public function not_category($format = '')
    {
        return $this->getVar('not_category', $format);
    }

    /**
     * Returns Class Base Variable not_itemid
     * @param  string $format
     * @return mixed
     */
    public function not_itemid($format = '')
    {
        return $this->getVar('not_itemid', $format);
    }

    /**
     * Returns Class Base Variable not_event
     * @param  string $format
     * @return mixed
     */
    public function not_event($format = '')
    {
        return $this->getVar('not_event', $format);
    }

    /**
     * Returns Class Base Variable not_uid
     * @param  string $format
     * @return mixed
     */
    public function not_uid($format = '')
    {
        return $this->getVar('not_uid', $format);
    }

    /**
     * Returns Class Base Variable not_mode
     * @param  string $format
     * @return mixed
     */
    public function not_mode($format = '')
    {
        return $this->getVar('not_mode', $format);
    }

    /**
     * Send a notification message to the user
     *
     * @param string $template_dir Template directory
     * @param string $template     Template name
     * @param string $subject      Subject line for notification message
     * @param array  $tags         Array of substitutions for template variables
     *
     * @return bool true if success, false if error
     **/
    public function notifyUser($template_dir, $template, $subject, $tags)
    {
        // Check the user's notification preference.
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $user           = $member_handler->getUser($this->getVar('not_uid'));
        if (!is_object($user)) {
            return true;
        }
        $method = $user->getVar('notify_method');

        $xoopsMailer = xoops_getMailer();
        include_once $GLOBALS['xoops']->path('include/notification_constants.php');
        switch ($method) {
            case XOOPS_NOTIFICATION_METHOD_PM:
                $xoopsMailer->usePM();
                /* @var $config_handler XoopsConfigHandler  */
                $config_handler    = xoops_getHandler('config');
                $xoopsMailerConfig = $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
                $xoopsMailer->setFromUser($member_handler->getUser($xoopsMailerConfig['fromuid']));
                foreach ($tags as $k => $v) {
                    $xoopsMailer->assign($k, $v);
                }
                break;
            case XOOPS_NOTIFICATION_METHOD_EMAIL:
                $xoopsMailer->useMail();
                foreach ($tags as $k => $v) {
                    $xoopsMailer->assign($k, preg_replace('/&amp;/i', '&', $v));
                }
                break;
            default:
                return true; // report error in user's profile??
//                break;
        }

        // Set up the mailer
        $xoopsMailer->setTemplateDir($template_dir);
        $xoopsMailer->setTemplate($template);
        $xoopsMailer->setToUsers($user);
        //global $xoopsConfig;
        //$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        //$xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject($subject);
        $success = $xoopsMailer->send();

        // If send-once-then-delete, delete notification
        // If send-once-then-wait, disable notification
        include_once $GLOBALS['xoops']->path('include/notification_constants.php');
        $notification_handler = xoops_getHandler('notification');

        if ($this->getVar('not_mode') == XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE) {
            $notification_handler->delete($this);

            return $success;
        }

        if ($this->getVar('not_mode') == XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT) {
            $this->setVar('not_mode', XOOPS_NOTIFICATION_MODE_WAITFORLOGIN);
            $notification_handler->insert($this);
        }

        return $success;
    }
}

/**
 * XOOPS notification handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS notification class objects.
 *
 *
 * @package             kernel
 * @subpackage          notification
 *
 * @author              Michael van Dam <mvandam@caltech.edu>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsNotificationHandler extends XoopsObjectHandler
{
    /**
     * Create a {@link XoopsNotification}
     *
     * @param bool $isNew Flag the object as "new"?
     *
     * @return XoopsNotification
     */
    public function create($isNew = true)
    {
        $notification = new XoopsNotification();
        if ($isNew) {
            $notification->setNew();
        }

        return $notification;
    }

    /**
     * Retrieve a {@link XoopsNotification}
     *
     * @param int $id ID
     *
     * @return XoopsNotification {@link XoopsNotification}, FALSE on fail
     **/
    public function get($id)
    {
        $notification = false;
        $id           = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('xoopsnotifications') . ' WHERE not_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $notification;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $notification = new XoopsNotification();
                $notification->assignVars($this->db->fetchArray($result));
            }
        }

        return $notification;
    }

    /**
     * Write a notification(subscription) to database
     *
     * @param  XoopsObject|XoopsNotification $notification a XoopsNotification object
     *
     * @return bool true on success, otherwise false
     **/
    public function insert(XoopsObject $notification)
    {
        $className = 'XoopsNotification';
        if (!($notification instanceof $className)) {
            return false;
        }
        if (!$notification->isDirty()) {
            return true;
        }
        if (!$notification->cleanVars()) {
            return false;
        }
        foreach ($notification->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($notification->isNew()) {
            $not_id = $this->db->genId('xoopsnotifications_not_id_seq');
            $sql    = sprintf('INSERT INTO %s (not_id, not_modid, not_itemid, not_category, not_uid, not_event, not_mode) VALUES (%u, %u, %u, %s, %u, %s, %u)', $this->db->prefix('xoopsnotifications'), $not_id, $not_modid, $not_itemid, $this->db->quoteString($not_category), $not_uid, $this->db->quoteString($not_event), $not_mode);
        } else {
            $sql = sprintf('UPDATE %s SET not_modid = %u, not_itemid = %u, not_category = %s, not_uid = %u, not_event = %s, not_mode = %u WHERE not_id = %u', $this->db->prefix('xoopsnotifications'), $not_modid, $not_itemid, $this->db->quoteString($not_category), $not_uid, $this->db->quoteString($not_event), $not_mode, $not_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($not_id)) {
            $not_id = $this->db->getInsertId();
        }
        $notification->assignVar('not_id', $not_id);

        return true;
    }

    /**
     * Delete a {@link XoopsNotification} from the database
     *
     * @param  XoopsObject|XoopsNotification $notification a XoopsNotification object
     *
     * @return bool true on success, otherwise false
     **/
    public function delete(XoopsObject $notification)
    {
        $className = 'XoopsNotification';
        if (!($notification instanceof $className)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE not_id = %u', $this->db->prefix('xoopsnotifications'), $notification->getVar('not_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get some {@link XoopsNotification}s
     *
     * @param CriteriaElement|CriteriaCompo $criteria
     * @param bool            $id_as_key Use IDs as keys into the array?
     *
     * @return array Array of {@link XoopsNotification} objects
     **/
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('xoopsnotifications');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sort = ($criteria->getSort() != '') ? $criteria->getSort() : 'not_id';
            $sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $notification = new XoopsNotification();
            $notification->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $notification;
            } else {
                $ret[$myrow['not_id']] = $notification;
            }
            unset($notification);
        }

        return $ret;
    }

    // TODO: Need this??
    /**
     * Count Notifications
     *
     * @param CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     *
     * @return int Count
     **/
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('xoopsnotifications');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Delete multiple notifications
     *
     * @param CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     *
     * @return bool
     **/
    public function deleteAll(CriteriaElement $criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('xoopsnotifications');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    // TODO: rename this...
    // Also, should we have get by module, get by category, etc...??
    /**
     * @param $module_id
     * @param $category
     * @param $item_id
     * @param $event
     * @param $user_id
     *
     * @return bool
     */
    public function &getNotification($module_id, $category, $item_id, $event, $user_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$module_id));
        $criteria->add(new Criteria('not_category', $this->db->escape($category)));
        $criteria->add(new Criteria('not_itemid', (int)$item_id));
        $criteria->add(new Criteria('not_event', $this->db->escape($event)));
        $criteria->add(new Criteria('not_uid', (int)$user_id));
        $objects = $this->getObjects($criteria);
        if (count($objects) == 1) {
            return $objects[0];
        }
        $inst = false;

        return $inst;
    }

    /**
     * Determine if a user is subscribed to a particular event in
     * a particular module.
     *
     * @param string $category  Category of notification event
     * @param int    $item_id   Item ID of notification event
     * @param string $event     Event
     * @param int    $module_id ID of module (default current module)
     * @param int    $user_id   ID of user (default current user)
     *                          return int  0 if not subscribe; non-zero if subscribed
     *
     * @return int
     */
    public function isSubscribed($category, $item_id, $event, $module_id, $user_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$module_id));
        $criteria->add(new Criteria('not_category', $this->db->escape($category)));
        $criteria->add(new Criteria('not_itemid', (int)$item_id));
        $criteria->add(new Criteria('not_event', $this->db->escape($event)));
        $criteria->add(new Criteria('not_uid', (int)$user_id));

        return $this->getCount($criteria);
    }

    // TODO: how about a function to subscribe a whole group of users???
    // e.g. if we want to add all moderators to be notified of subscription
    // of new threads...
    /**
     * Subscribe for notification for an event(s)
     *
     * @param string $category  category of notification
     * @param int    $item_id   ID of the item
     * @param mixed  $events    event string or array of events
     * @param int    $mode      force a particular notification mode
     *                          (e.g. once_only) (default to current user preference)
     * @param int    $module_id ID of the module (default to current module)
     * @param int    $user_id   ID of the user (default to current user)
     *                          *
     *
     * @return bool
     */
    public function subscribe($category, $item_id, $events, $mode = null, $module_id = null, $user_id = null)
    {
        if (!isset($user_id)) {
            global $xoopsUser;
            if (empty($xoopsUser)) {
                return false; // anonymous cannot subscribe
            } else {
                $user_id = $xoopsUser->getVar('uid');
            }
        }

        if (!isset($module_id)) {
            global $xoopsModule;
            $module_id = $xoopsModule->getVar('mid');
        }

        if (!isset($mode)) {
            $user = new XoopsUser($user_id);
            $mode = $user->getVar('notify_mode');
        }

        if (!is_array($events)) {
            $events = array($events);
        }
        foreach ($events as $event) {
            /* @var  $notification XoopsNotification */
            if ($notification = $this->getNotification($module_id, $category, $item_id, $event, $user_id)) {
                if ($notification->getVar('not_mode') != $mode) {
                    $this->updateByField($notification, 'not_mode', $mode);
                }
            } else {
                $notification = $this->create();
                $notification->setVar('not_modid', $module_id);
                $notification->setVar('not_category', $category);
                $notification->setVar('not_itemid', $item_id);
                $notification->setVar('not_uid', $user_id);
                $notification->setVar('not_event', $event);
                $notification->setVar('not_mode', $mode);
                $this->insert($notification);
            }
        }
        return null;
    }

    // TODO: this will be to provide a list of everything a particular
    // user has subscribed to... e.g. for on the 'Profile' page, similar
    // to how we see the various posts etc. that the user has made.
    // We may also want to have a function where we can specify module id
    /**
     * Get a list of notifications by user ID
     *
     * @param int $user_id ID of the user
     *
     * @return array Array of {@link XoopsNotification} objects
     **/
    public function getByUser($user_id)
    {
        $criteria = new Criteria('not_uid', $user_id);

        return $this->getObjects($criteria, true);
    }

    // TODO: rename this??
    /**
     * Get a list of notification events for the current item/mod/user
     *
     * @param $category
     * @param $item_id
     * @param $module_id
     * @param $user_id
     * @return array
     */
    public function getSubscribedEvents($category, $item_id, $module_id, $user_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$module_id));
        $criteria->add(new Criteria('not_category', $this->db->escape($category)));
        if ($item_id) {
            $criteria->add(new Criteria('not_itemid', (int)$item_id));
        }
        $criteria->add(new Criteria('not_uid', (int)$user_id));
        $results = $this->getObjects($criteria, true);
        $ret     = array();
        foreach (array_keys($results) as $i) {
            $ret[] = $results[$i]->getVar('not_event');
        }

        return $ret;
    }

    // TODO: is this a useful function?? (Copied from comment_handler)
    /**
     * Retrieve items by their ID
     *
     * @param int    $module_id Module ID
     * @param int    $item_id   Item ID
     * @param string $order     Sort order
     *
     * @param null   $status
     *
     * @return array Array of {@link XoopsNotification} objects
     */
    public function getByItemId($module_id, $item_id, $order = null, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', (int)$module_id));
        $criteria->add(new Criteria('com_itemid', (int)$item_id));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', (int)$status));
        }
        if (isset($order)) {
            $criteria->setOrder($order);
        }

        return $this->getObjects($criteria);
    }

    /**
     * Send notifications to users
     *
     * @param string $category     notification category
     * @param int    $item_id      ID of the item
     * @param array  $events       trigger events
     * @param array  $extra_tags   array of substitutions for template to be
     *                              merged with the one from function..
     * @param array  $user_list    only notify the selected users
     * @param int    $module_id    ID of the module
     * @param int    $omit_user_id ID of the user to omit from notifications. (default to current user).  set to 0 for all users to receive notification.
     * @internal param string $event notification event
     */
    // TODO:(?) - pass in an event LIST.  This will help to avoid
    // problem of sending people multiple emails for similar events.
    // BUT, then we need an array of mail templates, etc...  Unless
    // mail templates can include logic in the future, then we can
    // tailor the mail so it makes sense for any of the possible
    // (or combination of) events.
    public function triggerEvents($category, $item_id, $events, $extra_tags = array(), $user_list = array(), $module_id = null, $omit_user_id = null)
    {
        if (!is_array($events)) {
            $events = array($events);
        }
        foreach ($events as $event) {
            $this->triggerEvent($category, $item_id, $event, $extra_tags, $user_list, $module_id, $omit_user_id);
        }
    }

    /**
     * Enter description here...
     *
     * @param  int   $category
     * @param  int   $item_id
     * @param  int   $event
     * @param  array $extra_tags
     * @param  array $user_list
     * @param  int   $module_id
     * @param  int   $omit_user_id
     * @return mixed
     */
    public function triggerEvent($category, $item_id, $event, $extra_tags = array(), $user_list = array(), $module_id = null, $omit_user_id = null)
    {
        if (!isset($module_id)) {
            global $xoopsModule;
            $module    = $xoopsModule;
            $module_id = !empty($xoopsModule) ? $xoopsModule->getVar('mid') : 0;
        } else {
            /* @var $module_handler XoopsModuleHandler */
            $module_handler = xoops_getHandler('module');
            $module         = $module_handler->get($module_id);
        }

        // Check if event is enabled
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler = xoops_getHandler('config');
        $mod_config     = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        if (empty($mod_config['notification_enabled'])) {
            return false;
        }
        $category_info =& notificationCategoryInfo($category, $module_id);
        $event_info    =& notificationEventInfo($category, $event, $module_id);
        if (!in_array(notificationGenerateConfig($category_info, $event_info, 'option_name'), $mod_config['notification_events']) && empty($event_info['invisible'])) {
            return false;
        }

        if (!isset($omit_user_id)) {
            global $xoopsUser;
            $omit_user_id = 0;
            if (!empty($xoopsUser)) {
                $omit_user_id = $xoopsUser->getVar('uid');
            }
        }
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$module_id));
        $criteria->add(new Criteria('not_category', $this->db->escape($category)));
        $criteria->add(new Criteria('not_itemid', (int)$item_id));
        $criteria->add(new Criteria('not_event', $this->db->escape($event)));
        $mode_criteria = new CriteriaCompo();
        $mode_criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDALWAYS), 'OR');
        $mode_criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE), 'OR');
        $mode_criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT), 'OR');
        $criteria->add($mode_criteria);
        if (!empty($user_list)) {
            $user_criteria = new CriteriaCompo();
            foreach ($user_list as $user) {
                $user_criteria->add(new Criteria('not_uid', (int)$user), 'OR');
            }
            $criteria->add($user_criteria);
        }
        $notifications = $this->getObjects($criteria);
        if (empty($notifications)) {
            return null;
        }

        // Add some tag substitutions here

        $not_config = $module->getInfo('notification');
        $tags       = array();
        if (!empty($not_config)) {
            if (!empty($not_config['tags_file'])) {
                $tags_file = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname') . '/' . $not_config['tags_file']);
                if (file_exists($tags_file)) {
                    include_once $tags_file;
                    if (!empty($not_config['tags_func'])) {
                        $tags_func = $not_config['tags_func'];
                        if (function_exists($tags_func)) {
                            $tags = $tags_func($category, (int)$item_id, $event);
                        }
                    }
                }
            }
            // RMV-NEW
            if (!empty($not_config['lookup_file'])) {
                $lookup_file = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname') . '/' . $not_config['lookup_file']);
                if (file_exists($lookup_file)) {
                    include_once $lookup_file;
                    if (!empty($not_config['lookup_func'])) {
                        $lookup_func = $not_config['lookup_func'];
                        if (function_exists($lookup_func)) {
                            $item_info = $lookup_func($category, (int)$item_id);
                        }
                    }
                }
            }
        }
        $tags['X_ITEM_NAME']       = !empty($item_info['name']) ? $item_info['name'] : '[' . _NOT_ITEMNAMENOTAVAILABLE . ']';
        $tags['X_ITEM_URL']        = !empty($item_info['url']) ? $item_info['url'] : '[' . _NOT_ITEMURLNOTAVAILABLE . ']';
        $tags['X_ITEM_TYPE']       = !empty($category_info['item_name']) ? $category_info['title'] : '[' . _NOT_ITEMTYPENOTAVAILABLE . ']';
        $tags['X_MODULE']          = $module->getVar('name');
        $tags['X_MODULE_URL']      = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/';
        $tags['X_NOTIFY_CATEGORY'] = $category;
        $tags['X_NOTIFY_EVENT']    = $event;

        $template_dir = $event_info['mail_template_dir'];
        $template     = $event_info['mail_template'] . '.tpl';
        $subject      = $event_info['mail_subject'];

        foreach ($notifications as $notification) {
            if (empty($omit_user_id) || $notification->getVar('not_uid') != $omit_user_id) {
                // user-specific tags
                //$tags['X_UNSUBSCRIBE_URL'] = 'TODO';
                // TODO: don't show unsubscribe link if it is 'one-time' ??
                $tags['X_UNSUBSCRIBE_URL'] = XOOPS_URL . '/notifications.php';
                $tags                      = array_merge($tags, $extra_tags);
                $notification->notifyUser($template_dir, $template, $subject, $tags);
            }
        }
        return null;
    }

    /**
     * Delete all notifications for one user
     *
     * @param  int $user_id ID of the user
     * @return bool
     **/
    public function unsubscribeByUser($user_id)
    {
        $criteria = new Criteria('not_uid', (int)$user_id);

        return $this->deleteAll($criteria);
    }

    // TODO: allow these to use current module, etc...
    /**
     * Unsubscribe notifications for an event(s).
     *
     * @param string $category  category of the events
     * @param int    $item_id   ID of the item
     * @param mixed  $events    event string or array of events
     * @param int    $module_id ID of the module (default current module)
     * @param int    $user_id   UID of the user (default current user)
     *
     * @return bool
     **/
    public function unsubscribe($category, $item_id, $events, $module_id = null, $user_id = null)
    {
        if (!isset($user_id)) {
            global $xoopsUser;
            if (empty($xoopsUser)) {
                return false; // anonymous cannot subscribe
            } else {
                $user_id = $xoopsUser->getVar('uid');
            }
        }
        if (!isset($module_id)) {
            global $xoopsModule;
            $module_id = $xoopsModule->getVar('mid');
        }
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$module_id));
        $criteria->add(new Criteria('not_category', $this->db->escape($category)));
        $criteria->add(new Criteria('not_itemid', (int)$item_id));
        $criteria->add(new Criteria('not_uid', (int)$user_id));
        if (!is_array($events)) {
            $events = array($events);
        }
        $event_criteria = new CriteriaCompo();
        foreach ($events as $event) {
            $event_criteria->add(new Criteria('not_event', $this->db->escape($event)), 'OR');
        }
        $criteria->add($event_criteria);

        return $this->deleteAll($criteria);
    }

    // TODO: When 'update' a module, may need to switch around some
    //  notification classes/IDs...  or delete the ones that no longer
    //  exist.
    /**
     * Delete all notifications for a particular module
     *
     * @param  int $module_id ID of the module
     * @return bool
     **/
    public function unsubscribeByModule($module_id)
    {
        $criteria = new Criteria('not_modid', (int)$module_id);

        return $this->deleteAll($criteria);
    }

    /**
     * Delete all subscriptions for a particular item.
     *
     * @param int    $module_id ID of the module to which item belongs
     * @param string $category  Notification category of the item
     * @param int    $item_id   ID of the item
     *
     * @return bool
     **/
    public function unsubscribeByItem($module_id, $category, $item_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$module_id));
        $criteria->add(new Criteria('not_category', $this->db->escape($category)));
        $criteria->add(new Criteria('not_itemid', (int)$item_id));

        return $this->deleteAll($criteria);
    }

    /**
     * Perform notification maintenance activites at login time.
     * In particular, any notifications for the newly logged-in
     * user with mode XOOPS_NOTIFICATION_MODE_WAITFORLOGIN are
     * switched to mode XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT.
     *
     * @param int $user_id ID of the user being logged in
     **/
    public function doLoginMaintenance($user_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_uid', (int)$user_id));
        $criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_WAITFORLOGIN));

        $notifications = $this->getObjects($criteria, true);
        foreach ($notifications as $n) {
            $n->setVar('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT);
            $this->insert($n);
        }
    }

    /**
     * Update
     *
     * @param XoopsNotification $notification {@link XoopsNotification} object
     * @param string            $field_name   Name of the field
     * @param mixed             $field_value  Value to write
     *
     * @return bool
     **/
    public function updateByField(XoopsNotification $notification, $field_name, $field_value)
    {
        $notification->unsetNew();
        $notification->setVar($field_name, $field_value);

        return $this->insert($notification);
    }
}
