<?php
/**
 * XOOPS form element
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
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Parent
 */
xoops_load('XoopsFormElement');

/**
 * A select field with a choice of available groups
 */
class XoopsFormSelectGroup extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool   $include_anon Include group "anonymous"?
     * @param mixed  $value        Pre-selected value (or array of them).
     * @param int    $size         Number of rows. "1" makes a drop-down-list.
     * @param bool   $multiple     Allow multiple selections?
     */
    public function __construct($caption, $name, $include_anon = false, $value = null, $size = 1, $multiple = false)
    {
        parent::__construct($caption, $name, $value, $size, $multiple);
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        if (!$include_anon) {
            $this->addOptionArray($member_handler->getGroupList(new Criteria('groupid', XOOPS_GROUP_ANONYMOUS, '!=')));
        } else {
            $this->addOptionArray($member_handler->getGroupList());
        }
    }
}
