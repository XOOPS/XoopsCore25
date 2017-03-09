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
 * @copyright       The XOOPS Project (http://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
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
     * @param int    $size             Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple         Allow multiple selections?
     */
    public function __construct($caption, $name, $includeAnonymous = false, $value = null, $size = 1, $multiple = false)
    {
        /**
         * @var mixed array|false - cache any result for this session.
         *            Some modules use multiple copies of this element on a single page, so this call will
         *            be made multiple times. This is only used when $value is null.
         * @todo this should be replaced with better interface, with autocomplete style search
         * and user specific MRU cache
         */
        static $queryCache = false;

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
        $cachekey = 'formselectuser';

        $select_element = new XoopsFormSelect('', $name, $value, $size, $multiple);
        if ($includeAnonymous) {
            $select_element->addOption(0, $GLOBALS['xoopsConfig']['anonymous']);
        }
        /* @var $member_handler XoopsMemberHandler */
        $member_handler = xoops_getHandler('member');
        $value          = is_array($value) ? $value : (empty($value) ? array() : array($value));
        $selectedUsers = array();
        if (count($value) > 0) {
            // fetch the set of uids in $value
            $criteria = new Criteria('uid', '(' . implode(',', $value) . ')', 'IN');
            $criteria->setSort('uname');
            $criteria->setOrder('ASC');
            $selectedUsers = $member_handler->getUserList($criteria);
        }

        // get the full selection list
        // we will always cache this version to reduce expense
        if (empty($queryCache)) {
            XoopsLoad::load('XoopsCache');
            $queryCache = XoopsCache::read($cachekey);
            if ($queryCache === false) {
                $criteria = new CriteriaCompo();
                if ($limit <= $member_handler->getUserCount()) {
                    // if we have more than $limit users, we will select who to show based on last_login
                    $criteria->setLimit($limit);
                    $criteria->setSort('last_login');
                    $criteria->setOrder('DESC');
                } else {
                    $criteria->setSort('uname');
                    $criteria->setOrder('ASC');
                }
                $queryCache = $member_handler->getUserList($criteria);
                asort($queryCache);
                XoopsCache::write($cachekey, $queryCache, $cachettl); // won't do anything different if write fails
            }
        }

        // merge with selected
        $users = $selectedUsers + $queryCache;

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

        $searchUsers = new XoopsFormButton('', 'srchusr_' . $name, _MA_USER_MORE, 'button');
        $searchUsers->setExtra(' onclick="openWithSelfMain(\'' . XOOPS_URL . '/include/findusers.php?target=' . $name . '&amp;multiple=' . $multiple . '&amp;token=' . $token . '\', \'userselect\', 800, 600, null); return false;" ');
        $action_tray->addElement($searchUsers);

         if (isset($GLOBALS['xoTheme']) && is_object($GLOBALS['xoTheme'])) {
             $GLOBALS['xoTheme']->addScript('', array(), $js_addusers);
         } else {
             echo '<script>' . $js_addusers . '</script>';
         }
        parent::__construct($caption, '', $name);
        $this->addElement($select_element);
        $this->addElement($action_tray);
    }
}
