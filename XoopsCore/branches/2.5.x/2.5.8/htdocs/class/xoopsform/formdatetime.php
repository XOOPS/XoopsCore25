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
 * Date and time selection field
 *
 * @author 		Kazumi Ono <onokazu@xoops.org>
 * @package 	kernel
 * @subpackage 	form
 * @access 		public
 */
class XoopsFormDateTime extends XoopsFormElementTray
{
    /**
     * XoopsFormDateTime::XoopsFormDateTime()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $size
     * @param integer $value
     * @param mixed $showtime
     */
    function XoopsFormDateTime($caption, $name, $size = 15, $value = 0, $showtime = true)
    {
        $this->XoopsFormElementTray($caption, '&nbsp;');
        $value = intval($value);
        $value = ($value > 0) ? $value : time();
        $datetime = getDate($value);
        $this->addElement(new XoopsFormTextDateSelect('', $name . '[date]', $size, $value, $showtime));

        if ($showtime) {
        $timearray = array();
        for ($i = 0; $i < 24; ++$i) {
            for ($j = 0; $j < 60; $j = $j + 10) {
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
