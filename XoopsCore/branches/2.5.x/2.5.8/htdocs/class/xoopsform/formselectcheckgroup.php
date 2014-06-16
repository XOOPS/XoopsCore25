<?php

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 *  Xoops Form Class Elements
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      form
 * @since           2.3.0
 * @author          John Neill <catzwolf@xoops.org>
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_load('XoopsFormCheckBox');
/**
 * Xoops Form Select Check Groups
 *
 * @author 		John Neill <catzwolf@xoops.org>
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @package 	kernel
 * @subpackage 	form
 * @access 		public
 */
class XoopsFormSelectCheckGroup extends XoopsFormCheckBox
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed  $value    Pre-selected value (or array of them).
     * @param int    $size     Number or rows. "1" makes a drop-down-list.
     * @param bool   $multiple Allow multiple selections?
     *
     */
    function XoopsFormSelectCheckGroup($caption, $name, $value = null, $size = 1, $multiple = false)
    {
        $member_handler = &xoops_gethandler('member');
        $this->userGroups = $member_handler->getGroupList();
        $this->XoopsFormCheckBox($caption, $name, $value, '', true);
        $this->columns = 3;
        foreach ($this->userGroups as $group_id => $group_name) {
            $this->addOption($group_id, $group_name);
        }
    }
}
