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
 * @license             GNU GPL 3 (http://www.gnu.org/licenses/gpl-3.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.5.7
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Txmod Xoops http://www.txmodxoops.org/
 * @version             $Id: formtime.php 14030 2016-05-18 22:52:20Z timgno $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Time text field
 *
 * @author         Txmod Xoops <support@txmodxoops.org>
 * @package        kernel
 * @subpackage     form
 * @access         public
 */
class XoopsFormTime extends XoopsFormElementTray
{
    /**
     * XoopsFormTime::XoopsFormTime()
     *
     * @param mixed   $caption
     * @param mixed   $name
     * @param integer $size
     * @param integer $value
     * @param mixed   $showtime
     */
    public function __construct($caption, $name, $size = 15, $value = 0, $showtime = true)
    {
        parent::__construct($caption, '&nbsp;');
        $value    = (int)($value);
        $value    = ($value > 0) ? $value : time();
        $datetime = getdate($value);

        if ($showtime) {
            $timearray = array();
            for ($i = 0; $i < 24; ++$i) {
                for ($j = 0; $j < 60; $j += 10) {
                    $key             = ($i * 3600) + ($j * 60);
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
