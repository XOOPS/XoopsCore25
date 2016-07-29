<?php
/**
 * XOOPS form element of datetime
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
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Date and time selection field
 *
 * @author         Kazumi Ono <onokazu@xoops.org>
 * @package        kernel
 * @subpackage     form
 */
class XoopsFormDateTime extends XoopsFormElementTray
{
    const SHOW_BOTH = 1;
    const SHOW_DATE = 0;
    const SHOW_TIME = 2;

    /**
     * XoopsFormDateTime::XoopsFormDateTime()
     *
     * @param mixed   $caption  form field caption
     * @param mixed   $name     form variable name
     * @param integer $size     size of date select
     * @param integer $value    unix timestamp, defaults to now
     * @param mixed   $showtime control display of date and time elements
     *                           SHOW_BOTH, true  - show both date and time selectors
     *                           SHOW_DATE, false - only show date selector
     *                           SHOW_TIME        - only show time selector
     */
    public function __construct($caption, $name, $size = 15, $value = 0, $showtime = true)
    {
        parent::__construct($caption, '&nbsp;');
        switch ((int) $showtime) {
            case static::SHOW_DATE:
                $displayDate = true;
                $displayTime = false;
                break;
            case static::SHOW_TIME:
                $displayDate = false;
                $displayTime = true;
                break;
            default:
                $displayDate = true;
                $displayTime = true;
                break;
        }
        $value    = (int)$value;
        $value    = ($value > 0) ? $value : time();
        $datetime = getdate($value);
        if ($displayDate) {
            $this->addElement(new XoopsFormTextDateSelect('', $name . '[date]', $size, $value));
        } else {
            $value = !is_numeric($value) ? time() : (int)$value;
            $value = ($value == 0) ? time() : $value;
            $displayValue = date(_SHORTDATESTRING, $value);
            $this->addElement(new XoopsFormHidden($name . '[date]', $displayValue));
        }

        if ($displayTime) {
            $timearray = array();
            for ($i = 0; $i < 24; ++$i) {
                for ($j = 0; $j < 60; $j += 10) {
                    $key = ($i * 3600) + ($j * 60);
                    $timearray[$key] = ($j != 0) ? $i . ':' . $j : $i . ':0' . $j;
                }
            }
            ksort($timearray);

            $timeselect = new XoopsFormSelect('', $name . '[time]', $datetime['hours'] * 3600 + 600 * ceil($datetime['minutes'] / 10));
            $timeselect->addOptionArray($timearray);
            $this->addElement($timeselect);
        } else {
            $this->addElement(new XoopsFormHidden($name . '[time]', 0));
        }
    }
}
