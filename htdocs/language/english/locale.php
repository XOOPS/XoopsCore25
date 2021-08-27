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
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @todo                To be handled by i18n/l10n
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

setlocale(LC_ALL, 'en_US');

// !!IMPORTANT!! insert '\' before any char among reserved chars: "a","A","B","c","d","D","e","F","g","G","h","H","i","I","j","l","L","m","M","n","O","r","s","S","t","T","U","w","W","Y","y","z","Z"
// insert double '\' before 't','r','n'
define('_TODAY', "\T\o\d\a\y G:i");
define('_YESTERDAY', "\Y\\e\s\\t\\e\\r\d\a\y G:i");
define('_MONTHDAY', 'n/j G:i');
define('_YEARMONTHDAY', 'Y/n/j G:i');
define('_ELAPSE', '%s ago');
define('_TIMEFORMAT_DESC', "Valid formats: \"s\" - " . _SHORTDATESTRING . "; \"m\" - " . _MEDIUMDATESTRING . "; \"l\" - " . _DATESTRING . ';<br>' . "\"c\" or \"custom\" - format determined according to interval to present; \"e\" - Elapsed; \"mysql\" - Y-m-d H:i:s;<br>" . "specified string - Refer to <a href=\"https://php.net/manual/en/function.date.php\" rel=\"external\">PHP manual</a>.");

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
     * @param  int|float $number
     * @return string
     */
    public function number_format($number)
    {
        return number_format($number, 2, '.', ',');
    }

    /**
     * Money Format
     *
     * @param  string $format
     * @param  float $number
     * @return string|null formatted money or error message
     */
    public function money_format($format, $number)
    {
        $trace                  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $errorMessageDeprecated = "Function '" . __FUNCTION__ . "' in '" . __FILE__ . ' is deprecated, ';
        $errorMessageRemoved    = "Function '" . __FUNCTION__ . "' in '" . __FILE__ . ' is removed in PHP8, ';
        $errorMessageAdvise     = 'use formatCurrency($number, $currency) instead.' . ". Called from {$trace[0]['file']}line {$trace[0]['line']}";
        $GLOBALS['xoopsLogger']->addDeprecated($errorMessageDeprecated . $errorMessageAdvise);

        setlocale(LC_MONETARY, 'en_US');

        if (function_exists('money_format')) {
            return money_format($format, $number);
        }

        trigger_error($errorMessageRemoved . $errorMessageAdvise, E_USER_NOTICE);
        return null;
    }

    /**
     * Format a currency value
     *
     * @param float  $amount The numeric currency value.
     * @param string $currency The 3-letter ISO 4217 currency code indicating the currency to use.
     * @return string String representing the formatted currency value.
     */
    public function formatCurrency($amount, $currency = 'USD')
    {
        $fmt = new \NumberFormatter('en_US', NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($amount, $currency);
    }

}
