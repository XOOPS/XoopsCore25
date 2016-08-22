<?php
/**
 * YOU SHOULD NEVER USE FUNCTIONS DEFINED IN THIS FILE. THEY SHOULD BE IMPLEMENTED AT APPLICATION LEVEL
 */
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 *  Xoops Functions
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.4.0
 * @author              Simon
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * xoops_hex2bin()
 *
 * @param hex string $hex
 * @return string
 */
function xoops_hex2bin($hex)
{
    if (!is_string($hex)) {
        return null;
    }
    $r   = '';
    $len = strlen($hex);
    for ($a = 0; $a < $len; $a += 2) {
        $r .= chr(hexdec($hex{$a} . $hex{$a + 1}));
    }

    return $r;
}

/**
 * xoops_bin2hex()
 *
 * @param bin string $bin
 * @return string
 */
function xoops_bin2hex($bin)
{
    return bin2hex($bin);
}

/**
 * xoops_ishexstr()
 *
 * @param hex      string $hex
 * @param checklen int $checklen
 * @return boolean
 */
function xoops_ishexstr($hex, $checklen = 32)
{
    $accepted = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $len      = strlen($hex);
    if ($checklen > $len) {
        $checklen = $len;//And???
    }
    $hex = strtolower($hex);
    for ($i = 0; $i < $len; ++$i) {
        if (!in_array($hex{$i}, $accepted)) {
            return false;
        }
    }

    return true;
}

/**
 * xoops_convert_encode()
 *
 * @param        value of array  $data
 * @param string $store_method
 *
 * @return boolean
 */
function xoops_convert_encode($data, $store_method = 'urlcode')
{
    switch ($store_method) {
        default:
            return urlencode($data);
        case 'base64':
            return base64_encode($data);
        case 'uucode':
            return convert_uuencode($data);
        case 'open':
            return $data;
        case 'hex':
            return bin2hex($data);
    }
}

/**
 * xoops_convert_decode()
 *
 * @param        value of array  $data
 * @param string $store_method
 * @return boolean
 */
function xoops_convert_decode($data, $store_method = 'urlcode')
{
    switch ($store_method) {
        default:
            return urldecode($data);
        case 'base64':
            return base64_decode($data);
        case 'uucode':
            return convert_uudecode($data);
        case 'open':
            return $data;
        case 'hex':
            return xoops_hex2bin($data);
    }
}

/**
 * xoops_aw_encode()
 *
 * @param  mixed $value value of array
 * @param        key   of array   $key
 * @param string $store_method
 * @return boolean
 */
function xoops_aw_encode($value, $key, $store_method = 'urlcode')
{
    $value = xoops_convert_encode($value, $store_method);
}

/**
 * xoops_aw_decode()
 *
 * @param mixed $value value of array
 * @param        key   of array $key
 * @param string $store_method
 * @return boolean
 */
function xoops_aw_decode($value, $key, $store_method = 'urlcode')
{
    $value = xoops_convert_decode($value, $store_method);
}
