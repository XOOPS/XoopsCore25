<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *           truncate (Smarty online manual)
 * @link https://www.guyrutenberg.com/2007/12/04/multibyte-string-truncate-modifier-for-smarty-mb_truncate/
 * @author   Guy Rutenberg <guyrutenberg@gmail.com> based on the original
 *           truncate by Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '…', $break_words = false, $middle = false)
{
    if (0 >= $length) {
        return '';
    }
    $charset = defined('_CHARSET') ? _CHARSET : 'UTF-8';

    if (mb_strlen($string) <= $length) {
        return $string;
    }
    $length -= min($length, mb_strlen($etc));
    if (!$break_words && !$middle) {
        $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1, $charset));
    }
    if (!$middle) {
        return mb_substr($string, 0, $length, $charset) . $etc;
    }
    return mb_substr($string, 0, $length / 2, $charset) . $etc
        . mb_substr($string, -$length / 2, (mb_strlen($string) - $length / 2), $charset);
}
