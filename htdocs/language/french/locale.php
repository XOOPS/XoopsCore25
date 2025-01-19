<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Xoops locale
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @todo                To be handled by i18n/l10n
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

setlocale(LC_ALL, 'fr_FR');

// !!IMPORTANT!! insert '\' before any char among reserved chars: "a","A","B","c","d","D","e","F","g","G","h","H","i","I","j","l","L","m","M","n","O","r","s","S","t","T","U","w","W","Y","y","z","Z"
// insert double '\' before 't','r','n'
define('_TODAY', "\A\u\j\o\u\\r\d\'\h\u\i G:i");
define('_YESTERDAY', "\H\i\\e\\r G:i");
define('_MONTHDAY', 'd/m H:i');
define('_YEARMONTHDAY', 'd/m/Y H:i');
define('_ELAPSE', 'Il y a %s');
define('_TIMEFORMAT_DESC', "Formats valides: \"s\" - " . _SHORTDATESTRING . "; \"m\" - " . _MEDIUMDATESTRING . "; \"l\" - " . _DATESTRING . ';<br>' . "\"c\" or \"custom\" - le format est déterminé en fonction de l'intervalle pour présenter; \"e\" - passé; \"mysql\" - d-m-Y H:i:s;<br>" . "chaîne de caractères spécifiée - Se référer au <a href=\"http://php.net/manual/fr/function.date.php\" rel=\"external\">manuel PHP</a>.");

if (!class_exists('XoopsLocalAbstract')) {
    include_once XOOPS_ROOT_PATH . '/class/xoopslocal.php';
}

/**
 * A Xoops Local
 *
 * @package             kernel
 * @subpackage          Language
 *
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsLocal extends XoopsLocalAbstract
{
    /**
     * Number Formats
     *
     * @param  unknown_type $number
     * @return unknown
     */
    public function number_format($number)
    {
        return number_format($number, 2, '.', ',');
    }

    /**
     * Money Format
     *
     * @param  string $format
     * @param  string $number
     * @return money  format
     */
    public function money_format($format, $number)
    {
        setlocale(LC_MONETARY, 'fr_FR');

        return money_format($format, $number);
    }
}
