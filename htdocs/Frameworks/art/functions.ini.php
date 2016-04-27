<?php
/**
 * Initial functions
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @since               1.00
 * @package             Frameworks
 * @subpackage          art
 */

if (substr(XOOPS_VERSION, 0, 9) < 'XOOPS 2.3') {
    trigger_error('The package only works for XOOPS 2.3+', E_USER_ERROR);
}

if (!defined('FRAMEWORKS_ART_FUNCTIONS_INI')):
    define('FRAMEWORKS_ART_FUNCTIONS_INI', true);

    define('FRAMEWORKS_ROOT_PATH', XOOPS_ROOT_PATH . '/Frameworks');

    /**
     * Load declaration of an object handler
     *
     *
     * @param string $handler handler name, optional
     * @param string $dirname
     *
     * @return bool
     */
    function load_objectHandler($handler = '', $dirname = 'art')
    {
        if (empty($handler)) {
            $handlerClass = 'ArtObject';
            $fileName     = 'object.php';
        } else {
            $handlerClass = 'ArtObject' . ucfirst($handler) . 'Handler';
            $fileName     = "object.{$handler}.php";
        }

        class_exists($handlerClass) || require_once FRAMEWORKS_ROOT_PATH . "/{$dirname}/{$fileName}";

        return class_exists($handlerClass);
    }

    /**
     * @return bool
     */
    function load_object()
    {
        return load_objectHandler();
    }

    /**
     * Load a collective functions of Frameworks
     *
     * @param  string $group   name of  the collective functions, empty for functions.php
     * @param  string $dirname
     * @return bool
     */
    function load_functions($group = '', $dirname = 'art')
    {
        $dirname  = ('' == $dirname) ? 'art' : $dirname;
        $constant = strtoupper("frameworks_{$dirname}_functions" . ($group ? "_{$group}" : ''));
        if (defined($constant)) {
            return true;
        }

        return include_once FRAMEWORKS_ROOT_PATH . "/{$dirname}/functions.{$group}" . (empty($group) ? '' : '.') . 'php';
    }

    /**
     * Load a collective functions of a module
     *
     * The function file should be located in /modules/MODULE/functions.{$group}.php
     * To avoid slowdown caused by include_once, a constant is suggested in the corresponding file: capitalized {$dirname}_{functions}[_{$group}]
     *
     * The function is going to be formulated to use xos_kernel_Xoops2::loadService() in XOOPS 2.3+
     *
     * @param  string $group   name of  the collective functions, empty for functions.php
     * @param  string $dirname module dirname, optional
     * @return bool
     */
    function mod_loadFunctions($group = '', $dirname = '')
    {
        $dirname  = !empty($dirname) ? $dirname : $GLOBALS['xoopsModule']->getVar('dirname', 'n');
        $constant = strtoupper("{$dirname}_functions" . ($group ? "_{$group}" : '') . '_loaded');
        if (defined($constant)) {
            return true;
        }
        $filename = XOOPS_ROOT_PATH . "/modules/{$dirname}/include/functions.{$group}" . (empty($group) ? '' : '.') . 'php';

        return include_once $filename;
    }

    /**
     * Load renderer for a class
     *
     * The class file should be located in /modules/MODULE/{$class}.renderer.php
     * The classf name should be defined as Capitalized(module_dirname)Capitalized(class_name)Renderer
     *
     * @param  string $class   name of  the classname
     * @param  string $dirname module dirname, optional
     * @return bool
     */
    function mod_loadRenderer($class, $dirname = '')
    {
        $dirname  = !empty($dirname) ? $dirname : $GLOBALS['xoopsModule']->getVar('dirname', 'n');
        $renderer = ucfirst($dirname) . ucfirst($class) . 'Renderer';
        if (!class_exists($renderer)) {
            require_once XOOPS_ROOT_PATH . "/modules/{$dirname}/class/{$class}.renderer.php";
        }
        $instance = eval("{$renderer}::instance()");

        return $instance;
    }

    /**
     * Get localized string if it is defined
     *
     * @param string $name string to be localized
     */
    if (!function_exists('mod_constant')) {
        /**
         * @param $name
         *
         * @return mixed
         */
        function mod_constant($name)
        {
            if (!empty($GLOBALS['VAR_PREFIXU']) && @defined($GLOBALS['VAR_PREFIXU'] . '_' . strtoupper($name))) {
                return constant($GLOBALS['VAR_PREFIXU'] . '_' . strtoupper($name));
            } elseif (!empty($GLOBALS['xoopsModule']) && @defined(strtoupper($GLOBALS['xoopsModule']->getVar('dirname', 'n') . '_' . $name))) {
                return constant(strtoupper($GLOBALS['xoopsModule']->getVar('dirname', 'n') . '_' . $name));
            } elseif (defined(strtoupper($name))) {
                return constant(strtoupper($name));
            } else {
                return str_replace('_', ' ', strtolower($name));
            }
        }
    }

    /**
     * Get completed DB prefix if it is defined
     *
     * @param string  $name  string to be completed
     * @param boolean $isRel relative - do not add XOOPS->DB prefix
     */
    if (!function_exists('mod_DB_prefix')) {
        /**
         * @param      $name
         * @param bool $isRel
         *
         * @return string
         */
        function mod_DB_prefix($name, $isRel = false)
        {
            $relative_name = $GLOBALS['MOD_DB_PREFIX'] . '_' . $name;
            if ($isRel) {
                return $relative_name;
            }

            return $GLOBALS['xoopsDB']->prefix($relative_name);
        }
    }

    /**
     * Display contents of a variable, an array or an object or an array of objects
     *
     * @param mixed $message variable/array/object
     */
    if (!function_exists('xoops_message')):
        /**
         * @param     $message
         * @param int $userlevel
         */
        function xoops_message($message, $userlevel = 0)
        {
            global $xoopsUser;

            $level = 1;
            if (!$xoopsUser) {
                $level = 0;
            } elseif ($xoopsUser->isAdmin()) {
                $level = 99;
            }
            if ($userlevel > $level) {
                return null;
            }

            echo "<div style=\"clear:both;\"> </div>";
            if (is_array($message) || is_object($message)) {
                echo '<div><pre>';
                print_r($message);
                echo '</pre></div>';
            } else {
                echo "<div>{$message}</div>";
            }
            echo "<div style=\"clear:both;\"> </div>";
        }
    endif;
    /**
     * @param $message
     *
     * @return bool
     */
    function mod_message($message)
    {
        global $xoopsModuleConfig;
        if (!empty($xoopsModuleConfig['do_debug'])) {
            if (is_array($message) || is_object($message)) {
                echo '<div><pre>';
                print_r($message);
                echo '</pre></div>';
            } else {
                echo "<div>$message</div>";
            }
        }

        return true;
    }

    /**
     * Get dirname of a module according to current path
     *
     * @param  string $current_path path to where the function is called
     * @return string $dirname
     */
    function mod_getDirname($current_path = null)
    {
        if (DIRECTORY_SEPARATOR !== '/') {
            $current_path = str_replace(strpos($current_path, '\\\\', 2) ? '\\\\' : DIRECTORY_SEPARATOR, '/', $current_path);
        }
        $url_arr = explode('/', strstr($current_path, '/modules/'));

        return $url_arr[2];
    }

    /**
     * Is a module being installed, updated or uninstalled
     * Used for setting module configuration default values or options
     *
     * The function should be in functions.admin.php, however it requires extra inclusion in xoops_version.php if so
     *
     * @param  string $dirname dirname of current module
     * @return bool
     */
    function mod_isModuleAction($dirname = 'system')
    {
        $ret = @(// action module "system"
            !empty($GLOBALS['xoopsModule']) && 'system' === $GLOBALS['xoopsModule']->getVar('dirname', 'n') && // current dirname
            ($dirname == $_POST['dirname'] || $dirname == $_POST['module']) && // current op
            ('update_ok' === $_POST['op'] || 'install_ok' === $_POST['op'] || 'uninstall_ok' === $_POST['op']) && // current action
            'modulesadmin' === $_POST['fct']);

        return $ret;
    }

endif;
