<?php
/**
 * Smarty plugin
 *
 * Fetches templates from a database
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @param $tpl_name
 * @param $tpl_source
 * @param $smarty
 * @return bool
 */
function smarty_resource_db_source($tpl_name, &$tpl_source, &$smarty)
{
    if (!$tpl = smarty_resource_db_tplinfo($tpl_name)) {
        return false;
    }
    if (is_object($tpl)) {
        $tpl_source = $tpl->getVar('tpl_source', 'n');
    } else {
        $fp         = fopen($tpl, 'r');
        $filesize   = filesize($tpl);
        $tpl_source = ($filesize > 0) ? fread($fp, $filesize) : '';
        fclose($fp);
    }

    return true;
}

/**
 * @param $tpl_name
 * @param $tpl_timestamp
 * @param $smarty
 *
 * @return bool
 */
function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    if (!$tpl = smarty_resource_db_tplinfo($tpl_name)) {
        return false;
    }
    if (is_object($tpl)) {
        $tpl_timestamp = $tpl->getVar('tpl_lastmodified', 'n');
    } else {
        $tpl_timestamp = filemtime($tpl);
    }

    return true;
}

/**
 * @param $tpl_name
 * @param $smarty
 *
 * @return bool
 */
function smarty_resource_db_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

/**
 * @param $tpl_name
 * @param $smarty
 */
function smarty_resource_db_trusted($tpl_name, &$smarty)
{
    // not used for templates
}

/**
 * @param $tpl_name
 *
 * @return bool|string
 */
function smarty_resource_db_tplinfo($tpl_name)
{
    static $cache = array();
    global $xoopsConfig;

    if (isset($cache[$tpl_name])) {
        return $cache[$tpl_name];
    }
    $tplset          = $xoopsConfig['template_set'];
    $theme           = isset($xoopsConfig['theme_set']) ? $xoopsConfig['theme_set'] : 'default';
    $tplfile_handler = xoops_getHandler('tplfile');
    // If we're not using the "default" template set, then get the templates from the DB
    if ($tplset !== 'default') {
        $tplobj = $tplfile_handler->find($tplset, null, null, null, $tpl_name, true);
        if (count($tplobj)) {
            return $cache[$tpl_name] = $tplobj[0];
        }
    }
    // If we'using the default tplset, get the template from the filesystem
    $tplobj = $tplfile_handler->find('default', null, null, null, $tpl_name, true);

    if (!count($tplobj)) {
        return $cache[$tpl_name] = false;
    }
    $tplobj = $tplobj[0];
    $module = $tplobj->getVar('tpl_module', 'n');
    $type   = $tplobj->getVar('tpl_type', 'n');
    // Construct template path
    switch ($type) {
        case 'block':
            $directory = XOOPS_THEME_PATH;
            $path      = 'blocks/';
            break;
        case 'admin':
            $theme     = isset($xoopsConfig['cpanel']) ? $xoopsConfig['cpanel'] : 'default';
            $directory = XOOPS_ADMINTHEME_PATH;
            $path      = 'admin/';
            break;
        default:
            $directory = XOOPS_THEME_PATH;
            $path      = '';
            break;
    }
    // First, check for an overloaded version within the theme folder
    $filepath = $directory . "/{$theme}/modules/{$module}/{$path}{$tpl_name}";
    if (!file_exists($filepath)) {
        // If no custom version exists, get the tpl from its default location
        $filepath = XOOPS_ROOT_PATH . "/modules/{$module}/templates/{$path}{$tpl_name}";
        if (!file_exists($filepath)) {
            return $cache[$tpl_name] = $tplobj;
        }
    }

    return $cache[$tpl_name] = $filepath;
}
