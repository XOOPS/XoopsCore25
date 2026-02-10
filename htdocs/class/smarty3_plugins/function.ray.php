<?php
/**
 * Smarty <{ray}> function plugin — send data to Ray desktop debugger
 *
 * Usage (XOOPS uses <{ }> as Smarty delimiters):
 *   <{ray value=$variable}>
 *   <{ray value=$user label="User Object" color="green"}>
 *   <{ray value=$items label="Items" color="blue"}>
 *   <{ray msg="Reached this point" color="red"}>
 *
 * Parameters:
 *   value  - (optional) Variable or value to send to Ray
 *   msg    - (optional) String message to send (alternative to value)
 *   label  - (optional) Label displayed next to the item in Ray
 *   color  - (optional) Ray color: green, red, blue, orange, purple, gray
 *
 * Requires: spatie/ray via composer OR spatie/global-ray via php.ini auto_prepend_file
 * If Ray is not installed or disabled in module settings, this plugin silently does nothing.
 *
 * @copyright   (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @param  array  $params  Smarty parameters
 * @param  Smarty &$smarty Smarty object
 * @return string Empty string (output goes to Ray app, not template)
 */
function smarty_function_ray($params, &$smarty)
{
    // Only operate when the Debugbar RayLogger is available and enabled,
    // and the global ray() helper function exists.
    if (!class_exists('XoopsModules\Debugbar\RayLogger', false)) {
        return '';
    }

    $rayLogger = \XoopsModules\Debugbar\RayLogger::getInstance();
    if (!$rayLogger->isEnable()) {
        return '';
    }

    if (!function_exists('ray')) {
        return '';
    }

    // Get the value to send
    $value = isset($params['value']) ? $params['value'] : null;
    $msg   = isset($params['msg']) ? $params['msg'] : null;
    $label = isset($params['label']) ? $params['label'] : null;
    $color = isset($params['color']) ? $params['color'] : null;

    // Determine what to send
    $data = ($value !== null) ? $value : $msg;
    if ($data === null) {
        return '';
    }

    // Send to Ray
    $r = ray($data);

    if ($label !== null) {
        $r->label($label);
    }
    if ($color !== null) {
        $r->color($color);
    }

    return '';
}
