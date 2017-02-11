<?php
/**
 * Functions handling module configs
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @since               1.00
 * @package             Frameworks
 * @subpackage          art
 */

if (!defined('FRAMEWORKS_ART_FUNCTIONS_CONFIG')):
    define('FRAMEWORKS_ART_FUNCTIONS_CONFIG', true);

    /**
     * Load configs of a module
     *
     *
     * @param    string $dirname module dirname
     * @return    array
     */
    function mod_loadConfig($dirname = '')
    {
        if (empty($dirname) && empty($GLOBALS['xoopsModule'])) {
            return null;
        }
        $dirname = !empty($dirname) ? $dirname : $GLOBALS['xoopsModule']->getVar('dirname');

        if (isset($GLOBALS['xoopsModule']) && is_object($GLOBALS['xoopsModule']) && $GLOBALS['xoopsModule']->getVar('dirname', 'n') == $dirname) {
            if (isset($GLOBALS['xoopsModuleConfig'])) {
                $moduleConfig =& $GLOBALS['xoopsModuleConfig'];
            } else {
                return null;
            }
        } else {
            xoops_load('XoopsCache');
            if (!$moduleConfig = XoopsCache::read("{$dirname}_config")) {
                $moduleConfig = mod_fetchConfig($dirname);
                XoopsCache::write("{$dirname}_config", $moduleConfig);
            }
        }
        if ($customConfig = @include XOOPS_ROOT_PATH . "/modules/{$dirname}/include/plugin.php") {
            $moduleConfig = array_merge($moduleConfig, $customConfig);
        }

        return $moduleConfig;
    }

    /**
     * @param string $dirname
     *
     * @return array
     */
    function mod_loadConfg($dirname = '')
    {
        return mod_loadConfig($dirname);
    }

    /**
     * Fetch configs of a module from database
     *
     *
     * @param    string $dirname module dirname
     * @return    array
     */
    function mod_fetchConfig($dirname = '')
    {
        if (empty($dirname)) {
            return null;
        }

        $module_handler = xoops_getHandler('module');
        if (!$module = $module_handler->getByDirname($dirname)) {
            trigger_error("Module '{$dirname}' does not exist", E_USER_WARNING);

            return null;
        }

        /* @var $config_handler XoopsConfigHandler  */
        $config_handler = xoops_getHandler('config');
        $criteria       = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
        $configs        = $config_handler->getConfigs($criteria);
        foreach (array_keys($configs) as $i) {
            $moduleConfig[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
        }
        unset($module, $configs);

        return $moduleConfig;
    }

    /**
     * @param string $dirname
     *
     * @return array
     */
    function mod_fetchConfg($dirname = '')
    {
        return mod_fetchConfig($dirname);
    }

    /**
     * clear config cache of a module
     *
     *
     * @param    string $dirname module dirname
     * @return    bool
     */
    function mod_clearConfig($dirname = '')
    {
        if (empty($dirname)) {
            return false;
        }

        xoops_load('XoopsCache');

        return XoopsCache::delete("{$dirname}_config");
    }

    /**
     * @param string $dirname
     *
     * @return bool
     */
    function mod_clearConfg($dirname = '')
    {
        return mod_clearConfig($dirname);
    }

endif;
