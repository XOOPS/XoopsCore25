<?php
/**
 * Smarty |ray modifier plugin — debug value and pass through
 *
 * Usage (XOOPS uses <{ }> as Smarty delimiters):
 *   <div><{$user.name|ray}></div>
 *   <div><{$user.name|ray:"Username"}></div>
 *   <{assign var="debugged" value=$data|ray:"My Data"}>
 *
 * Sends the value to Ray for inspection, then returns it unchanged.
 * This allows inline debugging without disrupting template output.
 *
 * Parameters:
 *   $label - (optional) Label displayed in Ray
 *
 * Requires: spatie/ray via composer OR spatie/global-ray via php.ini auto_prepend_file
 * If Ray is not installed or disabled in module settings, the value passes through unchanged.
 *
 * @copyright   (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @param  mixed  $value The value being modified
 * @param  string $label Optional label for Ray
 * @return mixed  The original value (pass-through)
 */
function smarty_modifier_ray($value, $label = null)
{
    if (class_exists('XoopsModules\Debugbar\RayLogger', false)
        && \XoopsModules\Debugbar\RayLogger::getInstance()->isEnabled()
        && function_exists('ray')) {
        $r = ray($value);
        if ($label !== null) {
            $r->label($label);
        }
    }

    // Always return the original value — pass-through modifier
    return $value;
}
