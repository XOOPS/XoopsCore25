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

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A group of form elements
 */
class XoopsFormElementTray extends XoopsFormElement
{
    /**
     * array of form element objects
     *
     * @var array
     * @access private
     */
    private $_elements = array();

    /**
     * required elements
     *
     * @var array
     */
    public $_required = array();

    /**
     * HTML to seperate the elements
     *
     * @var string
     * @access private
     */
    private $_delimeter;

    /**
     * constructor
     *
     * @param string $caption   Caption for the group.
     * @param string $delimeter HTML to separate the elements
     * @param string $name
     *
     */
    public function __construct($caption, $delimeter = '&nbsp;', $name = '')
    {
        $this->setName($name);
        $this->setCaption($caption);
        $this->_delimeter = $delimeter;
    }

    /**
     * Is this element a container of other elements?
     *
     * @return bool true
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * Find out if there are required elements.
     *
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->_required);
    }

    /**
     * Add an element to the group
     *
     * @param XoopsFormElement $formElement {@link XoopsFormElement} to add
     * @param bool             $required
     *
     */
    public function addElement(XoopsFormElement $formElement, $required = false)
    {
        $this->_elements[] = $formElement;
        if (!$formElement->isContainer()) {
            if ($required) {
                $formElement->_required = true;
                $this->_required[]      = $formElement;
            }
        } else {
            $required_elements = $formElement->getRequired();
            $count             = count($required_elements);
            for ($i = 0; $i < $count; ++$i) {
                $this->_required[] = &$required_elements[$i];
            }
        }
    }

    /**
     * get an array of "required" form elements
     *
     * @return array array of {@link XoopsFormElement}s
     */
    public function &getRequired()
    {
        return $this->_required;
    }

    /**
     * Get an array of the elements in this group
     *
     * @param  bool $recurse get elements recursively?
     * @return XoopsFormElement[]  Array of {@link XoopsFormElement} objects.
     */
    public function &getElements($recurse = false)
    {
        if (!$recurse) {
            return $this->_elements;
        } else {
            $ret   = array();
            $count = count($this->_elements);
            for ($i = 0; $i < $count; ++$i) {
                if (!$this->_elements[$i]->isContainer()) {
                    $ret[] = &$this->_elements[$i];
                } else {
                    $elements = &$this->_elements[$i]->getElements(true);
                    $count2   = count($elements);
                    for ($j = 0; $j < $count2; ++$j) {
                        $ret[] = &$elements[$j];
                    }
                    unset($elements);
                }
            }

            return $ret;
        }
    }

    /**
     * Get the delimiter of this group
     *
     * @param  bool $encode To sanitizer the text?
     * @return string The delimiter
     */
    public function getDelimeter($encode = false)
    {
        return $encode ? htmlspecialchars(str_replace('&nbsp;', ' ', $this->_delimeter)) : $this->_delimeter;
    }

    /**
     * prepare HTML to output this group
     *
     * @return string HTML output
     */
    public function render()
    {
        return XoopsFormRenderer::getInstance()->get()->renderFormElementTray($this);
    }
}
