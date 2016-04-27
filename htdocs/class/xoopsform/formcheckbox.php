<?php
/**
 * XOOPS form checkbox compo
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsFormElement');

/**
 * Class XoopsFormCheckBox
 */
class XoopsFormCheckBox extends XoopsFormElement
{
    /**
     * Availlable options
     *
     * @var array
     * @access private
     */
    public $_options = array();

    /**
     * pre-selected values in array
     *
     * @var array
     * @access private
     */
    public $_value = array();

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
     * @param string $caption
     * @param string $name
     * @param mixed  $value Either one value as a string or an array of them.
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
        $this->setFormType('checkbox');
    }

    /**
     * Get the "value"
     *
     * @param  bool $encode To sanitizer the text?
     * @return array
     */
    public function getValue($encode = false)
    {
        if (!$encode) {
            return $this->_value;
        }
        $value = array();
        foreach ($this->_value as $val) {
            $value[] = $val ? htmlspecialchars($val, ENT_QUOTES) : $val;
        }

        return $value;
    }

    /**
     * Set the "value"
     *
     * @param array $value
     *
     */
    public function setValue($value)
    {
        $this->_value = array();
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->_value[] = $v;
            }
        } else {
            $this->_value[] = $value;
        }
    }

    /**
     * Add an option
     *
     * @param string $value
     * @param string $name
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
     * Add multiple Options at once
     *
     * @param array $options Associative array of value->name pairs
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
     * @param  bool|int $encode To sanitizer the text? potential values: 0 - skip; 1 - only for value; 2 - for both value and name
     * @return array    Associative array of value->name pairs
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
     * prepare HTML for output
     *
     * @return string
     */
    public function render()
    {
        $ele_name      = $this->getName();
        $ele_title     = $this->getTitle();
        $ele_id        = $ele_name;
        $ele_value     = $this->getValue();
        $ele_options   = $this->getOptions();
        $ele_extra     = $this->getExtra();
        $ele_delimeter = empty($this->columns) ? $this->getDelimeter() : '';

        if (count($ele_options) > 1 && substr($ele_name, -2, 2) !== '[]') {
            $ele_name .= '[]';
            $this->setName($ele_name);
        }
        $ret = '';
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
            // $name may be a link, should we use $name in the title tag?
            $ret .= "<input type='checkbox' name='{$ele_name}' id='{$ele_id}{$id_ele}' title='" . $ele_title . "' value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
            if (count($ele_value) > 0 && in_array($value, $ele_value)) {
                $ret .= ' checked="checked"';
            }
            $ret .= $ele_extra . ' />' . "<label name='xolb_{$ele_name}' for='{$ele_id}{$id_ele}'>" . $name . '</label>' . $ele_delimeter;
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

    /**
     * Render custom javascript validation code
     *
     * @seealso XoopsForm::renderValidationJS
     */
    public function renderValidationJS()
    {
        // render custom validation code if any
        if (!empty($this->customValidationCode)) {
            return implode(NWLINE, $this->customValidationCode);
            // generate validation code if required
        } elseif ($this->isRequired()) {
            $eltname    = $this->getName();
            $eltcaption = $this->getCaption();
            $eltmsg     = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg     = str_replace('"', '\"', stripslashes($eltmsg));

            return NWLINE . "var hasChecked = false; var checkBox = myform.elements['{$eltname}']; if (checkBox.length) {for (var i = 0; i < checkBox.length; i++) {if (checkBox[i].checked == true) {hasChecked = true; break;}}} else {if (checkBox.checked == true) {hasChecked = true;}}if (!hasChecked) {window.alert(\"{$eltmsg}\");if (checkBox.length) {checkBox[0].focus();} else {checkBox.focus();}return false;}";
        }

        return '';
    }
}
