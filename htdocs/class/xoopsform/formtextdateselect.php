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
 * @copyright       (c) 2000-2017 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * A text field with calendar popup
 */
class XoopsFormTextDateSelect extends XoopsFormText
{
    /**
     * @param string $caption
     * @param string $name
     * @param int $size
     * @param int $value
     */
    public function __construct($caption, $name, $size = 15, $value = 0)
    {
        $value = !is_numeric($value) ? time() : (int)$value;
        $value = ($value == 0) ? time() : $value;
        parent::__construct($caption, $name, $size, 25, $value);
    }

    /**
     * {@inheritDoc}
     * @see XoopsFormText::render()
     */
    public function render()
    {
        return XoopsFormRenderer::getInstance()->get()->renderFormTextDateSelect($this);
    }
}
