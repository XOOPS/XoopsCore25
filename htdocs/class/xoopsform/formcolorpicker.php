<?php
/**
 * XoopsFormColorPicker component class file
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
 * @author              Zoullou <webmaster@zoullou.org>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Color Selection Field
 *
 * @author              Zoullou <webmaster@zoullou.org>
 * @author              John Neill <catzwolf@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             Kernel
 * @access              public
 */
class XoopsFormColorPicker extends XoopsFormText
{
    /**
     * XoopsFormColorPicker::XoopsFormColorPicker()
     *
     * @param mixed  $caption
     * @param mixed  $name
     * @param string $value
     */
    public function __construct($caption, $name, $value = '#FFFFFF')
    {
        parent::__construct($caption, $name, 9, 7, $value);
    }

    /**
     * XoopsFormColorPicker::render()
     *
     * @return string
     */
    public function render()
    {
        return XoopsFormRenderer::getInstance()->get()->renderFormColorPicker($this);
    }

    /**
     * Returns custom validation Javascript
     *
     * @return string Element validation Javascript
     */
    public function renderValidationJS()
    {
        $eltname    = $this->getName();
        $eltcaption = $this->getCaption();
        $eltmsg     = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);

        return "if ( !(new RegExp(\"^#[0-9a-fA-F]{6}\",\"i\").test(myform.{$eltname}.value)) ) { window.alert(\"{$eltmsg}\"); myform.{$eltname}.focus(); return false; }";
    }
}
