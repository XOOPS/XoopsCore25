<?php
/**
 * System functions
 *
 * LICENSE
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             system
 */

/**
 * Get variables passed by GET or POST method
 *
 * Comment by Taiwen Jiang (a.k.a. phppp): THE METHOD IS NOT COMPLETE AND NOT SAFE. YOU ARE ENCOURAGED TO USE PHP'S NATIVE FILTER_VAR OR FILTER_INPUT FUNCTIONS DIRECTLY BEFORE WE MIGRATE TO XOOPS 3.
 * @param                   $global
 * @param                   $key
 * @param  string           $default
 * @param  string           $type
 * @return int|mixed|string
 */
function system_CleanVars(&$global, $key, $default = '', $type = 'int')
{
    switch ($type) {
        case 'array':
            $ret = (isset($global[$key]) && is_array($global[$key])) ? $global[$key] : $default;
            break;
        case 'date':
            $ret = isset($global[$key]) ? strtotime($global[$key]) : $default;
            break;
        case 'string':
            $ret = isset($global[$key]) ? filter_var($global[$key], FILTER_SANITIZE_MAGIC_QUOTES) : $default;
            break;
        case 'int':
        default:
            $ret = isset($global[$key]) ? filter_var($global[$key], FILTER_SANITIZE_NUMBER_INT) : $default;
            break;
    }
    if ($ret === false) {
        return $default;
    }

    return $ret;
}

/**
 * System language loader wrapper
 *
 *
 * @param  string  $name     Name of language file to be loaded, without extension
 * @param  string  $domain   Module dirname; global language file will be loaded if $domain is set to 'global' or not specified
 * @param  string  $language Language to be loaded, current language content will be loaded if not specified
 * @return boolean
 * @todo    expand domain to multiple categories, e.g. module:system, framework:filter, etc.
 *
 */
function system_loadLanguage($name, $domain = '', $language = null)
{
    /**
     * We must check later for an empty value. As xoops_getPageOption could be empty
     */
    if (empty($name)) {
        return false;
    }
    $language = empty($language) ? $GLOBALS['xoopsConfig']['language'] : $language;
    $path     = 'modules/' . $domain . '/language/';
    if (file_exists($file = $GLOBALS['xoops']->path($path . $language . '/admin/' . $name . '.php'))) {
        $ret = include_once $file;
    } else {
        $ret = include_once $GLOBALS['xoops']->path($path . 'english/admin/' . $name . '.php');
    }

    return $ret;
}

/**
 * @param        $version
 * @param string $value
 *
 * @return mixed
 */
function system_adminVersion($version, $value = '')
{
    static $tblVersion = array();
    if (is_array($tblVersion) && array_key_exists($version . '.' . $value, $tblVersion)) {
        return $tblVersion[$version . '.' . $value];
    }
    $path = XOOPS_ROOT_PATH . '/modules/system/admin/' . $version . '/xoops_version.php';
    if (file_exists($path)) {
        include $path;

        $retvalue                            = $modversion[$value];
        $tblVersion[$version . '.' . $value] = $retvalue;

        return $retvalue;
    }

    return null;
}

/**
 * @param $img
 *
 * @return mixed
 */
function system_AdminIcons($img)
{
    $style = xoops_getModuleOption('typeicons', 'system');
    if ($style == '') {
        $style = 'default';
    }

    $url = $GLOBALS['xoops']->url('modules/system/images/icons/' . $style . '/' . $img);

    return $url;
}

/**
 * @param $name
 */
function system_loadTemplate($name)
{
    global $sysTpl, $xoopsModule;

    $path = XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/templates/admin/' . $name . '.tpl';
    if (file_exists($path)) {
        echo $sysTpl->fetch($path);
    } else {
        echo 'Unable to read ' . $name;
    }
}

/**
 * @param $value_chmod
 * @param $path_file
 * @param $id
 *
 * @return string
 */
function modify_chmod($value_chmod, $path_file, $id)
{
    $chmod = '<div id="loading_' . $id . '" align="center" style="display:none;">' . '<img src="./images/mimetypes/spinner.gif" title="Loading" alt="Loading" width="12px"/></div>' . '<div id="chmod' . $id . '">' . '<select size="1" onChange="filemanager_modify_chmod(\'' . $path_file . '\', \'' . $id . '\')" name="chmod" id="chmod">';
    if ($value_chmod == 777) {
        $chmod .= '<option value="777" selected><span style="color:green;">777</span></option>';
    } else {
        $chmod .= '<option value="777"><span style="color:green;">777</span></option>';
    }

    if ($value_chmod == 776) {
        $chmod .= '<option value="776" selected>776</option>';
    } else {
        $chmod .= '<option value="776">776</option>';
    }

    if ($value_chmod == 766) {
        $chmod .= '<option value="766" selected>766</option>';
    } else {
        $chmod .= '<option value="766">766</option>';
    }

    if ($value_chmod == 666) {
        $chmod .= '<option value="666" selected>666</option>';
    } else {
        $chmod .= '<option value="666">666</option>';
    }

    if ($value_chmod == 664) {
        $chmod .= '<option value="664" selected>664</option>';
    } else {
        $chmod .= '<option value="664">664</option>';
    }

    if ($value_chmod == 644) {
        $chmod .= '<option value="644" selected>644</option>';
    } else {
        $chmod .= '<option value="644">644</option>';
    }

    if ($value_chmod == 444) {
        $chmod .= '<option value="444" selected><span style="color:red;">444</span></option>';
    } else {
        $chmod .= '<option value="444">444</option>';
    }

    if ($value_chmod == 440) {
        $chmod .= '<option value="440" selected>440</option>';
    } else {
        $chmod .= '<option value="440">440</option>';
    }

    if ($value_chmod == 400) {
        $chmod .= '<option value="400" selected>400</option>';
    } else {
        $chmod .= '<option value="400">400</option>';
    }
    $chmod .= '</select>';

    return $chmod;
}
