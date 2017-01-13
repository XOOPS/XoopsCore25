<?php
/**
 * XOOPS form element of button
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

xoops_load('XoopsFormElement');

/**
 *
 *
 * @package             kernel
 * @subpackage          form
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */

/**
 * A button
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 *
 * @package             kernel
 * @subpackage          form
 */
class XoopsFormButton extends XoopsFormElement
{
    /**
     * Value
     * @var string
     * @access    private
     */
    public $_value;

    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     * @var string
     * @access    private
     */
    public $_type;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name
     * @param string $value
     * @param string $type    Type of the button. Potential values: "button", "submit", or "reset"
     */
    public function __construct($caption, $name, $value = '', $type = 'button')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_type = $type;
        $this->setValue($value);
    }

    /**
     * Get the initial value
     *
     * @param  bool $encode To sanitizer the text?
     * @return string
     */
    public function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    /**
     * Set the initial value
     *
     * @param $value
     *
     * @return string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType()
    {
        return in_array(strtolower($this->_type), array('button', 'submit', 'reset')) ? $this->_type : 'button';
    }

    /**
     * prepare HTML for output
     *
     * @return string
     */
    public function render()
    {
        return XoopsFormRenderer::getInstance()->get()->renderFormButton($this);
    }
}
