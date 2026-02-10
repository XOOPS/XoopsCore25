<?php
/**
 * Smarty <{ray_dump}> function plugin — dump variable structure to Ray
 *
 * Usage (XOOPS uses <{ }> as Smarty delimiters):
 *   <{ray_dump value=$config}>
 *   <{ray_dump value=$user label="User Dump"}>
 *
 * Shows the full variable structure (objects, arrays, nested data) in Ray
 * using Ray's raw() method for complete property visibility.
 *
 * Requires: spatie/ray via composer OR spatie/global-ray via php.ini auto_prepend_file
 * If Ray is not installed or disabled in module settings, this plugin silently does nothing.
 *
 * @copyright   (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @param  array  $params  Smarty parameters
 * @param  Smarty &$smarty Smarty object
 * @return string Empty string
 */
function smarty_function_ray_dump($params, &$smarty)
{
    if (!class_exists('XoopsModules\Debugbar\RayLogger', false)
        || !\XoopsModules\Debugbar\RayLogger::getInstance()->isEnable()
        || !function_exists('ray')) {
        return '';
    }

    $value = isset($params['value']) ? $params['value'] : null;
    $label = isset($params['label']) ? $params['label'] : 'Dump';

    if ($value === null) {
        return '';
    }

    ray($value)->label($label)->color('purple');

    return '';
}
