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
        || !\XoopsModules\Debugbar\RayLogger::getInstance()->isEnabled()
        || !function_exists('ray')) {
        return '';
    }

    $label   = isset($params['label']) ? $params['label'] : _MD_DEBUGBAR_RAY_TEMPLATE_CONTEXT;
    $exclude = isset($params['exclude']) ? $params['exclude'] : '';

    // Get all template variables
    $allVars = $smarty->getTemplateVars();
    if (!is_array($allVars) || empty($allVars)) {
        ray(_MD_DEBUGBAR_RAY_NO_VARS)->label($label)->color('gray');
        return '';
    }

    // Apply exclusion filters
    if ($exclude !== '') {
        $allVars = _ray_context_apply_exclusions($allVars, $exclude);
    }

    // Normalize values for display in Ray
    $display = _ray_context_normalize_values($allVars);

    ksort($display, SORT_NATURAL | SORT_FLAG_CASE);

    // Send summary as table
    ray()->table($display, sprintf(_MD_DEBUGBAR_RAY_VARS_COUNT, $label, count($display)))->color('blue');

    return '';
}

/**
 * Apply comma-separated exclusion patterns to a variable array.
 *
 * Supports exact matches and wildcard prefix matches (e.g., "xoops_*").
 *
 * @param array  $vars    Template variables
 * @param string $exclude Comma-separated exclusion patterns
 * @return array Filtered variables
 */
function _ray_context_apply_exclusions(array $vars, $exclude)
{
    $patterns = array_map('trim', explode(',', $exclude));
    foreach ($patterns as $pattern) {
        if (substr($pattern, -1) === '*') {
            $vars = _ray_context_exclude_by_prefix($vars, substr($pattern, 0, -1));
        } else {
            unset($vars[$pattern]);
        }
    }
    return $vars;
}

/**
 * Remove all keys starting with the given prefix.
 *
 * @param array  $vars   Template variables
 * @param string $prefix Key prefix to exclude
 * @return array Filtered variables
 */
function _ray_context_exclude_by_prefix(array $vars, $prefix)
{
    foreach (array_keys($vars) as $key) {
        if (strpos($key, $prefix) === 0) {
            unset($vars[$key]);
        }
    }
    return $vars;
}

/**
 * Normalize template variable values for display in Ray.
 *
 * @param array $vars Template variables
 * @return array Display-ready values
 */
function _ray_context_normalize_values(array $vars)
{
    $display = [];
    foreach ($vars as $key => $value) {
        $display[$key] = _ray_context_format_value($value);
    }
    return $display;
}

/**
 * Format a single template variable value for display.
 *
 * @param mixed $value
 * @return mixed Formatted value
 */
function _ray_context_format_value($value)
{
    if (is_object($value)) {
        return '{' . get_class($value) . '}';
    }
    if (is_array($value)) {
        return 'Array[' . count($value) . ']';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (null === $value) {
        return 'NULL';
    }
    if (is_string($value) && strlen($value) > 200) {
        return substr($value, 0, 200) . '...';
    }
    return $value;
}
