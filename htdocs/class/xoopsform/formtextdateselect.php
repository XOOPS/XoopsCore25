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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
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
     * @param     $caption
     * @param     $name
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
     * @return string
     */
    public function render()
    {
        static $included = false;
        include_once XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/calendar.php';

        $ele_name  = $this->getName();
        $ele_value = $this->getValue(false);
        if (is_string($ele_value)) {
            $display_value = $ele_value;
            $ele_value     = time();
        } else {
            $display_value = date(_SHORTDATESTRING, $ele_value);
        }

        $jstime = formatTimestamp($ele_value, _SHORTDATESTRING);
        if (is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/calendar.js');
            $GLOBALS['xoTheme']->addStylesheet('include/calendar-blue.css');
            if (!$included) {
                $included = true;
                $GLOBALS['xoTheme']->addScript('', '', '
                    var calendar = null;

                    function selected(cal, date)
                    {
                    cal.sel.value = date;
                    }

                    function closeHandler(cal)
                    {
                    cal.hide();
                    Calendar.removeEvent(document, "mousedown", checkCalendar);
                    }

                    function checkCalendar(ev)
                    {
                    var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
                    for (; el != null; el = el.parentNode)
                    if (el == calendar.element || el.tagName == "A") break;
                    if (el == null) {
                    calendar.callCloseHandler(); Calendar.stopEvent(ev);
                    }
                    }
                    function showCalendar(id)
                    {
                    var el = xoopsGetElementById(id);
                    if (calendar != null) {
                    calendar.hide();
                    } else {
                    var cal = new Calendar(true, "' . $jstime . '", selected, closeHandler);
                    calendar = cal;
                    cal.setRange(1900, 2100);
                    calendar.create();
                    }
                    calendar.sel = el;
                    calendar.parseDate(el.value);
                    calendar.showAtElement(el);
                    Calendar.addEvent(document, "mousedown", checkCalendar);

                    return false;
                    }

                    Calendar._DN = new Array
                    ("' . _CAL_SUNDAY . '",
                    "' . _CAL_MONDAY . '",
                    "' . _CAL_TUESDAY . '",
                    "' . _CAL_WEDNESDAY . '",
                    "' . _CAL_THURSDAY . '",
                    "' . _CAL_FRIDAY . '",
                    "' . _CAL_SATURDAY . '",
                    "' . _CAL_SUNDAY . '");
                    Calendar._MN = new Array
                    ("' . _CAL_JANUARY . '",
                    "' . _CAL_FEBRUARY . '",
                    "' . _CAL_MARCH . '",
                    "' . _CAL_APRIL . '",
                    "' . _CAL_MAY . '",
                    "' . _CAL_JUNE . '",
                    "' . _CAL_JULY . '",
                    "' . _CAL_AUGUST . '",
                    "' . _CAL_SEPTEMBER . '",
                    "' . _CAL_OCTOBER . '",
                    "' . _CAL_NOVEMBER . '",
                    "' . _CAL_DECEMBER . '");

                    Calendar._TT = {};
                    Calendar._TT["TOGGLE"] = "' . _CAL_TGL1STD . '";
                    Calendar._TT["PREV_YEAR"] = "' . _CAL_PREVYR . '";
                    Calendar._TT["PREV_MONTH"] = "' . _CAL_PREVMNTH . '";
                    Calendar._TT["GO_TODAY"] = "' . _CAL_GOTODAY . '";
                    Calendar._TT["NEXT_MONTH"] = "' . _CAL_NXTMNTH . '";
                    Calendar._TT["NEXT_YEAR"] = "' . _CAL_NEXTYR . '";
                    Calendar._TT["SEL_DATE"] = "' . _CAL_SELDATE . '";
                    Calendar._TT["DRAG_TO_MOVE"] = "' . _CAL_DRAGMOVE . '";
                    Calendar._TT["PART_TODAY"] = "(' . _CAL_TODAY . ')";
                    Calendar._TT["MON_FIRST"] = "' . _CAL_DISPM1ST . '";
                    Calendar._TT["SUN_FIRST"] = "' . _CAL_DISPS1ST . '";
                    Calendar._TT["CLOSE"] = "' . _CLOSE . '";
                    Calendar._TT["TODAY"] = "' . _CAL_TODAY . '";

                    // date formats
                    Calendar._TT["DEF_DATE_FORMAT"] = "' . _SHORTDATESTRING . '";
                    Calendar._TT["TT_DATE_FORMAT"] = "' . _SHORTDATESTRING . '";

                    Calendar._TT["WK"] = "";
                ');
            }
        }

        return "<input type='text' name='" . $ele_name . "' id='" . $ele_name . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $display_value . "'" . $this->getExtra() . " /><input type='reset' value=' ... ' onclick='return showCalendar(\"" . $ele_name . "\");'>";
    }
}
