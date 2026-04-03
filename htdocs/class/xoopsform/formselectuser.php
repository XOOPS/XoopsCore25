<?php
/**
 * user select with page navigation
 *
 * limit: Only work with javascript enabled
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      form
 * @since           2.0.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsFormElementTray');
xoops_load('XoopsFormSelect');

/**
 * User Select field
 */
class XoopsFormSelectUser extends XoopsFormElementTray
{
    /**
     * Constructor
     *
     * @param string $caption          form element caption
     * @param string $name             form element name
     * @param bool   $includeAnonymous Include user "anonymous"?
     * @param mixed  $value            Pre-selected value (or array of them).
     *                                 For an item with massive members, such as "Registered Users", "$value"
     *                                 should be used to store selected temporary users only instead of all
     *                                 members of that item
     * @param int    $size             Number of rows. "1" makes a drop-down-list.
     * @param bool   $multiple         Allow multiple selections?
     * @param array  $allowedGroups    Group IDs to restrict users to; empty means no restriction.
     * @param array  $extraQuery       Extra query parameters appended to the findusers.php popup URL.
     */
    public function __construct($caption, $name, $includeAnonymous = false, $value = null, $size = 1, $multiple = false, array $allowedGroups = [], array $extraQuery = [])
    {
        /**
         * @var array - cache any result for this session.
         *            Some modules use multiple copies of this element on a single page, so this call will
         *            be made multiple times. This is only used when $value is null.
         * @todo this should be replaced with better interface, with autocomplete style search
         * and user specific MRU cache
         */
        static $queryCache = [];

        /**
         * @var int - limit to this many rows
         */
        $limit = 200;

        /**
         * @var string - cache time to live - will be interpreted by strtotime()
         */
        $cachettl = '+5 minutes';

        /**
         * @var string - cache key
         */
        // Filter to valid positive integer group IDs; reject non-numeric values.
        // If caller passed a non-empty list that sanitizes to empty, fail closed
        // with an impossible group ID so no users are shown.
        $hadGroups = !empty($allowedGroups);
        $allowedGroups = array_values(array_unique(array_filter(
            array_map('intval', $allowedGroups),
            static function ($groupId) { return $groupId > 0; }
        )));
        if ($hadGroups && empty($allowedGroups)) {
            $allowedGroups = [0]; // impossible group — matches no users
        }
        sort($allowedGroups);
        $cachekey = 'formselectuser';
        if (!empty($allowedGroups)) {
            $cachekey .= '_' . md5(implode(',', $allowedGroups));
        }

        $select_element = new XoopsFormSelect('', $name, $value, $size, $multiple);
        if ($includeAnonymous) {
            $select_element->addOption(0, $GLOBALS['xoopsConfig']['anonymous']);
        }
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        $value          = is_array($value) ? $value : (empty($value) ? [] : [$value]);
        $selectedUsers = [];
        if (count($value) > 0) {
            // fetch the set of uids in $value
            $criteria = new Criteria('uid', '(' . implode(',', $value) . ')', 'IN');
            $criteria->setSort('uname');
            $criteria->setOrder('ASC');
            $selectedUsers = $member_handler->getUserList($criteria);
        }

        // get the full selection list
        // we will always cache this version to reduce expense
        if (!array_key_exists($cachekey, $queryCache)) {
            XoopsLoad::load('XoopsCache');
            $queryCache[$cachekey] = XoopsCache::read($cachekey);
            if (!is_array($queryCache[$cachekey])) {
                $criteria = new CriteriaCompo();
                $userCount = !empty($allowedGroups)
                    ? $member_handler->getUserCountByGroupLink($allowedGroups)
                    : $member_handler->getUserCount();
                if ($limit <= $userCount) {
                    // if we have more than $limit users, we will select who to show based on last_login
                    $criteria->setLimit($limit);
                    $criteria->setSort('last_login');
                    $criteria->setOrder('DESC');
                } else {
                    $criteria->setSort('uname');
                    $criteria->setOrder('ASC');
                }
                if (!empty($allowedGroups)) {
                    $filteredUsers = $member_handler->getUsersByGroupLink($allowedGroups, $criteria, true, true);
                    $queryCache[$cachekey] = [];
                    foreach ($filteredUsers as $uid => $user) {
                        $queryCache[$cachekey][$uid] = $user->getVar('uname');
                    }
                } else {
                    $queryCache[$cachekey] = $member_handler->getUserList($criteria);
                }
                asort($queryCache[$cachekey]);
                XoopsCache::write($cachekey, $queryCache[$cachekey], $cachettl); // won't do anything different if write fails
            }
        }

        // Filter pre-selected users by allowed groups to prevent disallowed
        // recipients from appearing when a value is prefilled via URL params
        if (!empty($allowedGroups) && !empty($selectedUsers)) {
            $allowedUids = $member_handler->getUsersByGroupLink($allowedGroups, new Criteria('uid', '(' . implode(',', array_keys($selectedUsers)) . ')', 'IN'), false);
            $selectedUsers = array_intersect_key($selectedUsers, array_flip($allowedUids));
        }
        $users = $selectedUsers + $queryCache[$cachekey];

        $select_element->addOptionArray($users);
        if ($limit > count($users)) {
            parent::__construct($caption, '', $name);
            $this->addElement($select_element);

            return null;
        }

        xoops_loadLanguage('findusers');
        $js_addusers = "
            function addusers(opts)
            {
                var num = opts.substring(0, opts.indexOf(':'));
                opts = opts.substring(opts.indexOf(':')+1, opts.length);
                var sel = xoopsGetElementById('" . $name . "');
                var arr = new Array(num);
                for (var n=0; n < num; n++) {
                    var nm = opts.substring(0, opts.indexOf(':'));
                    opts = opts.substring(opts.indexOf(':')+1, opts.length);
                    var val = opts.substring(0, opts.indexOf(':'));
                    opts = opts.substring(opts.indexOf(':')+1, opts.length);
                    var txt = opts.substring(0, nm - val.length);
                    opts = opts.substring(nm - val.length, opts.length);
                    var added = false;
                    for (var k = 0; k < sel.options.length; k++) {
                        if (sel.options[k].value == val) {
                            added = true;
                            sel.options[k].selected = true;
                            break;
                        }
                    }
                    if (added == false) {
                        sel.options[k] = new Option(txt, val);
                        sel.options[k].selected = true;
                    }
                }

                return true;
            }";
        $token       = $GLOBALS['xoopsSecurity']->createToken();
        $action_tray = new XoopsFormElementTray('', '');
        $removeUsers = new XoopsFormButton('', 'rmvusr_' . $name, _MA_USER_REMOVE, 'button');
        $removeUsers->setExtra(' onclick="var sel = xoopsGetElementById(\'' . $name . '\');for (var i = sel.options.length-1; i >= 0; i--) {if (!sel.options[i].selected) {sel.options[i] = null;}}; return false;" ');
        $action_tray->addElement($removeUsers);

        $searchUrl = XOOPS_URL . '/include/findusers.php?' . http_build_query([
            'target' => $name,
            'multiple' => (int) $multiple,
            'token' => $token,
        ] + $extraQuery, '', '&amp;');
        $searchUsers = new XoopsFormButton('', 'srchusr_' . $name, _MA_USER_MORE, 'button');
        $searchUsers->setExtra(' onclick="openWithSelfMain(\'' . $searchUrl . '\', \'userselect\', 800, 600, null); return false;" ');
        $action_tray->addElement($searchUsers);

         if (isset($GLOBALS['xoTheme']) && is_object($GLOBALS['xoTheme'])) {
             $GLOBALS['xoTheme']->addScript('', [], $js_addusers);
         } else {
             echo '<script>' . $js_addusers . '</script>';
         }
        parent::__construct($caption, '', $name);
        $this->addElement($select_element);
        $this->addElement($action_tray);
    }
}
