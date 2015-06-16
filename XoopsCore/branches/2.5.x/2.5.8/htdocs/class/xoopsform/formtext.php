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
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      form
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version         $Id$
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * A simple text field
 */
class XoopsFormText extends XoopsFormElement
{
    /**
     * Size
     *
     * @var int
     * @access private
     */
    var $_size;

    /**
     * Maximum length of the text
     *
     * @var int
     * @access private
     */
    var $_maxlength;

    /**
     * Initial text
     *
     * @var string
     * @access private
     */
    var $_value;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param int $size Size
     * @param int $maxlength Maximum length of text
     * @param string $value Initial text
     */
    function XoopsFormText($caption, $name, $size, $maxlength, $value = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size = (int)($size);
        $this->_maxlength = (int)($maxlength);
        $this->setValue($value);
    }

    /**
     * Get size
     *
     * @return int
     */
    function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial content
     *
     * @param bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compatibility
     * @return string
     */
    function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     */
    function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render()
    {
        return "<input type='text' name='" . $this->getName() . "' title='" . $this->getTitle() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $this->getExtra() . " />";
    }
}
