<?php
/**
 * Smarty <{ray_table}> function plugin — show array as table in Ray
 *
 * Usage (XOOPS uses <{ }> as Smarty delimiters):
 *   <{ray_table value=$users}>
 *   <{ray_table value=$config label="Module Config"}>
 *
 * Sends an associative array to Ray's table() method for clean
 * tabular display in the desktop app.
 *
 * Requires: spatie/ray via composer OR spatie/global-ray via php.ini auto_prepend_file
 * If Ray is not installed or disabled in module settings, this plugin silently does nothing.
 *
 * @copyright   (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @param  array  $params  Smarty parameters
 * @param  Smarty &$smarty Smarty object
 * @return string Empty string
 */
function smarty_function_ray_table($params, &$smarty)
{
    if (!class_exists('XoopsModules\Debugbar\RayLogger', false)
        || !\XoopsModules\Debugbar\RayLogger::getInstance()->isEnabled()
        || !function_exists('ray')) {
        return '';
    }

    $value = isset($params['value']) ? $params['value'] : null;
    $label = isset($params['label']) ? $params['label'] : null;

    if ($value === null || !is_array($value)) {
        return '';
    }

    if ($label !== null) {
        ray()->table($value, $label);
    } else {
        ray()->table($value);
    }

    return '';
}
