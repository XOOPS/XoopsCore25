<?php

/**
 * XOOPS form radio compo
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          kernel
 * @since            2.0
 * @author           Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @package          kernel
 * @subpackage       form
 * @todo             template
 */
class XoopsFormRadio extends XoopsFormElement
{
    /**
     * Array of Options
     *
     * @var array
     * @access private
     */
    public $_options = array();

    /**
     * Pre-selected value
     *
     * @var string
     * @access private
     */
    public $_value;

    /**
     * HTML to seperate the elements
     *
     * @var string
     * @access private
     */
    public $_delimeter;

    /**
     * Column number for rendering
     *
     * @var int
     * @access public
     */
    public $columns;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name    "name" attribute
     * @param string $value   Pre-selected value
     * @param string $delimeter
     */
    public function __construct($caption, $name, $value = null, $delimeter = '&nbsp;')
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($value)) {
            $this->setValue($value);
        }
        $this->_delimeter = $delimeter;
    }

    /**
     * Get the "value" attribute
     *
     * @param  bool $encode To sanitizer the text?
     * @return string
     */
    public function getValue($encode = false)
    {
        return ($encode && $this->_value !== null) ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    /**
     * Set the pre-selected value
     *
     * @param  $value string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Add an option
     *
     * @param string $value "value" attribute - This gets submitted as form-data.
     * @param string $name  "name" attribute - This is displayed. If empty, we use the "value" instead.
     */
    public function addOption($value, $name = '')
    {
        if ($name != '') {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Adds multiple options
     *
     * @param array $options Associative array of value->name pairs.
     */
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $k => $v) {
                $this->addOption($k, $v);
            }
        }
    }

    /**
     * Get an array with all the options
     *
     * @param bool|int $encode To sanitizer the text? potential values: 0 - skip; 1 - only for value; 2 - for both value and name
     *
     * @return array Associative array of value->name pairs
     */
    public function getOptions($encode = false)
    {
        if (!$encode) {
            return $this->_options;
        }
        $value = array();
        foreach ($this->_options as $val => $name) {
            $value[$encode ? htmlspecialchars($val, ENT_QUOTES) : $val] = ($encode > 1) ? htmlspecialchars($name, ENT_QUOTES) : $name;
        }

        return $value;
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
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $ret           = '';
        $ele_name      = $this->getName();
        $ele_title     = $this->getTitle();
        $ele_value     = $this->getValue();
        $ele_options   = $this->getOptions();
        $ele_extra     = $this->getExtra();
        $ele_delimeter = empty($this->columns) ? $this->getDelimeter() : '';
        if (!empty($this->columns)) {
            $ret .= '<table><tr>';
        }
        $i      = 0;
        $id_ele = 0;
        foreach ($ele_options as $value => $name) {
            ++$id_ele;
            if (!empty($this->columns)) {
                if ($i % $this->columns == 0) {
                    $ret .= '<tr>';
                }
                $ret .= '<td>';
            }
            $ret .= '<input type="radio" name="' . $ele_name . '" id="' . $ele_name . $id_ele . '" title = "' . htmlspecialchars($ele_title, ENT_QUOTES) . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            if (isset($ele_value) && $value == $ele_value) {
                $ret .= ' checked="checked"';
            }
            $ret .= $ele_extra . ' />' . "<label name='xolb_{$ele_name}' for='" . $ele_name . $id_ele . "'>" . $name . '</label>' . $ele_delimeter;
            if (!empty($this->columns)) {
                $ret .= '</td>';
                if (++$i % $this->columns == 0) {
                    $ret .= '</tr>';
                }
            }
        }
        if (!empty($this->columns)) {
            if ($span = $i % $this->columns) {
                $ret .= '<td colspan="' . ($this->columns - $span) . '"></td></tr>';
            }
            $ret .= '</table>';
        }

        return $ret;
    }
}
