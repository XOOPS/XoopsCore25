<?php
/**
 * Smarty <{ray_context}> function plugin — dump all template variables to Ray
 *
 * Usage (XOOPS uses <{ }> as Smarty delimiters):
 *   <{ray_context}>
 *   <{ray_context label="Before User Loop"}>
 *   <{ray_context exclude="xoops_*,smarty"}>
 *
 * Sends all currently assigned template variables to Ray, organized
 * as a table. Great for understanding what data is available at any
 * point in a template.
 *
 * Parameters:
 *   label   - (optional) Label for the Ray entry (default: "Template Context")
 *   exclude - (optional) Comma-separated variable names or prefixes with * to exclude
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
function smarty_function_ray_context($params, &$smarty)
{
    if (!class_exists('XoopsModules\Debugbar\RayLogger', false)
        || !\XoopsModules\Debugbar\RayLogger::getInstance()->isEnable()
        || !function_exists('ray')) {
        return '';
    }

    $label   = isset($params['label']) ? $params['label'] : 'Template Context';
    $exclude = isset($params['exclude']) ? $params['exclude'] : '';

    // Get all template variables
    $allVars = $smarty->getTemplateVars();
    if (!is_array($allVars) || empty($allVars)) {
        ray('(no template variables)')->label($label)->color('gray');
        return '';
    }

    // Apply exclusion filters
    if ($exclude !== '') {
        $excludeList = array_map('trim', explode(',', $exclude));
        foreach ($excludeList as $pattern) {
            // Wildcard prefix match: "xoops_*" excludes all starting with "xoops_"
            if (substr($pattern, -1) === '*') {
                $prefix = substr($pattern, 0, -1);
                foreach (array_keys($allVars) as $key) {
                    if (strpos($key, $prefix) === 0) {
                        unset($allVars[$key]);
                    }
                }
            } else {
                // Exact match
                unset($allVars[$pattern]);
            }
        }
    }

    // Normalize values for display in Ray
    $display = [];
    foreach ($allVars as $key => $value) {
        if (is_object($value)) {
            $display[$key] = '{' . get_class($value) . '}';
        } elseif (is_array($value)) {
            $count = count($value);
            $display[$key] = "Array[{$count}]";
        } elseif (is_bool($value)) {
            $display[$key] = $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $display[$key] = 'NULL';
        } elseif (is_string($value) && strlen($value) > 200) {
            $display[$key] = substr($value, 0, 200) . '...';
        } else {
            $display[$key] = $value;
        }
    }

    ksort($display, SORT_NATURAL | SORT_FLAG_CASE);

    // Send summary as table
    ray()->table($display, $label . ' (' . count($display) . ' vars)')->color('blue');

    return '';
}
