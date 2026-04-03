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
    /** @var int Maximum users to show in the dropdown */
    private static $limit = 200;

    /** @var string Cache TTL interpreted by strtotime() */
    private static $cacheTtl = '+5 minutes';

    /**
     * Sanitize allowed group IDs to strict positive integers.
     *
     * Rejects non-digit values entirely (intval('1foo') would silently become 1).
     * Fails closed with [0] (impossible group) when a non-empty input sanitizes to empty.
     *
     * @param array $allowedGroups Raw group ID values
     *
     * @return array Sanitized group IDs, sorted
     */
    private static function normalizeAllowedGroups(array $allowedGroups): array
    {
        $hadGroups = !empty($allowedGroups);
        $allowedGroups = array_values(array_unique(array_filter(
            array_map(
                static function ($v) { return ctype_digit((string) $v) ? (int) $v : 0; },
                $allowedGroups
            ),
            static function ($groupId) { return $groupId > 0; }
        )));
        if ($hadGroups && empty($allowedGroups)) {
            $allowedGroups = [0]; // impossible group — matches no users
        }
        sort($allowedGroups);

        return $allowedGroups;
    }

    /**
     * Build the user option list, using cache when available.
     *
     * @param XoopsMemberHandler $memberHandler Member handler
     * @param array              $allowedGroups Sanitized group IDs (empty = no restriction)
     * @param string             $cachekey      Cache key for this group set
     * @param array              $queryCache    Reference to static query cache
     *
     * @return array uid => uname pairs
     */
    private static function getUserOptions(XoopsMemberHandler $memberHandler, array $allowedGroups, string $cachekey, array &$queryCache): array
    {
        if (!array_key_exists($cachekey, $queryCache)) {
            XoopsLoad::load('XoopsCache');
            $queryCache[$cachekey] = XoopsCache::read($cachekey);
            if (!is_array($queryCache[$cachekey])) {
                $criteria = new CriteriaCompo();
                $userCount = !empty($allowedGroups)
                    ? $memberHandler->getUserCountByGroupLink($allowedGroups)
                    : $memberHandler->getUserCount();
                if (self::$limit <= $userCount) {
                    $criteria->setLimit(self::$limit);
                    $criteria->setSort('last_login');
                    $criteria->setOrder('DESC');
                } else {
                    $criteria->setSort('uname');
                    $criteria->setOrder('ASC');
                }
                if (!empty($allowedGroups)) {
                    $filteredUsers = $memberHandler->getUsersByGroupLink($allowedGroups, $criteria, true, true);
                    $queryCache[$cachekey] = [];
                    foreach ($filteredUsers as $uid => $user) {
                        $queryCache[$cachekey][$uid] = $user->getVar('uname');
                    }
                } else {
                    $queryCache[$cachekey] = $memberHandler->getUserList($criteria);
                }
                asort($queryCache[$cachekey]);
                XoopsCache::write($cachekey, $queryCache[$cachekey], self::$cacheTtl);
            }
        }

        return $queryCache[$cachekey];
    }

    /**
     * Filter pre-selected values to only include users in allowed groups.
     *
     * @param array              $selectedUsers uid => uname of pre-selected users
     * @param array              $allowedGroups Sanitized group IDs
     * @param XoopsMemberHandler $memberHandler Member handler
     *
     * @return array Filtered uid => uname pairs
     */
    private static function filterSelectedByGroups(array $selectedUsers, array $allowedGroups, XoopsMemberHandler $memberHandler): array
    {
        if (empty($allowedGroups) || empty($selectedUsers)) {
            return $selectedUsers;
        }
        $allowedUids = $memberHandler->getUsersByGroupLink(
            $allowedGroups,
            new Criteria('uid', '(' . implode(',', array_keys($selectedUsers)) . ')', 'IN'),
            false
        );

        return array_intersect_key($selectedUsers, array_flip($allowedUids));
    }

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

        $allowedGroups = self::normalizeAllowedGroups($allowedGroups);
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
            $criteria = new Criteria('uid', '(' . implode(',', $value) . ')', 'IN');
            $criteria->setSort('uname');
            $criteria->setOrder('ASC');
            $selectedUsers = $member_handler->getUserList($criteria);
        }

        $options = self::getUserOptions($member_handler, $allowedGroups, $cachekey, $queryCache);
        $selectedUsers = self::filterSelectedByGroups($selectedUsers, $allowedGroups, $member_handler);
        $users = $selectedUsers + $options;

        $select_element->addOptionArray($users);
        if (self::$limit > count($users)) {
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
