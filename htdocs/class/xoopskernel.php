<?php
/**
 * XOOPS kernel
 *
 * !IMPORTANT: The file should have not been created and will be removed
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class xos_kernel_Xoops2
 */
class xos_kernel_Xoops2
{
    public $paths = ['XOOPS' => [], 'www' => [], 'var' => [], 'lib' => [], 'modules' => [], 'themes' => []];

    /**
     * Actual Xoops OS
     */
    public function __construct()
    {
        $this->paths['XOOPS']   = [XOOPS_PATH, XOOPS_URL . '/browse.php'];
        $this->paths['www']     = [XOOPS_ROOT_PATH, XOOPS_URL];
        $this->paths['var']     = [XOOPS_VAR_PATH, null];
        $this->paths['lib']     = [XOOPS_PATH, XOOPS_URL . '/browse.php'];
        $this->paths['modules'] = [XOOPS_ROOT_PATH . '/modules', XOOPS_URL . '/modules'];
        $this->paths['themes']  = [XOOPS_ROOT_PATH . '/themes', XOOPS_URL . '/themes'];
    }

    /**
     * Convert a XOOPS path to a physical one
     * @param               $url
     * @param  bool         $virtual
     * @return mixed|string
     */
    public function path($url, $virtual = false)
    {
        $path = '';
        $parts = explode('/', $url, 2);

        if (count($parts) < 2) {
            $root = 'www'; // Default root
            $path = $url;  // Entire URL is treated as the path
        } else {
            [$root, $path] = $parts;
        }

        if (!isset($this->paths[$root])) {
            [$root, $path] = ['www', $url];
        }

        if (!$virtual) { // Returns a physical path
            $path = $this->paths[$root][0] . '/' . $path;
            $path = str_replace('/', DS, $path);

            return $path;
        }

        return !isset($this->paths[$root][1]) ? '' : ($this->paths[$root][1] . '/' . $path);
    }

    /**
     * Convert a XOOPS path to a URL
     * @param string $url
     * @return mixed|string
     */
    public function url(?string $url='')
    {
        return (false !== strpos($url, '://') ? $url : $this->path($url, true));
    }

    /**
     * Build a URL with the specified request params
     * @param         $url
     * @param  array  $params
     * @return string
     */
    public function buildUrl($url, $params = [])
    {
        if ($url === '.') {
            $url = $_SERVER['REQUEST_URI'];
        }
        $split = explode('?', $url);
        if (count($split) > 1) {
            [$url, $query] = $split;
            parse_str($query, $query);
            $params = array_merge($query, $params);
        }
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $params[$k] = $k . '=' . rawurlencode((string)$v);
            }
            $url .= '?' . implode('&', $params);
        }

        return $url;
    }

    /**
     * xos_kernel_Xoops2::pathExists()
     *
     * @param $path
     * @param $error_type
     *
     * @return bool
     */
    public function pathExists($path, $error_type)
    {
        if (file_exists($path)) {
            return $path;
        } else {
            $GLOBALS['xoopsLogger']->triggerError($path, _XO_ER_FILENOTFOUND, __FILE__, __LINE__, $error_type);

            return false;
        }
    }

    /**
     * xos_kernel_Xoops2::gzipCompression()
     *
     * @return void
     */
    public function gzipCompression()
    {
        /**
         * Disable gzip compression if PHP is run under CLI mode and needs to be refactored to work correctly
         */
        if (empty($_SERVER['SERVER_NAME']) || substr(PHP_SAPI, 0, 3) === 'cli') {
            xoops_setConfigOption('gzip_compression', 0);
        }

        if (xoops_getConfigOption('gzip_compression') == 1 && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            if (@ini_get('zlib.output_compression_level') < 0) {
                ini_set('zlib.output_compression_level', 6);
            }
            ob_start('ob_gzhandler');
        }
    }

    /**
     * xos_kernel_Xoops2::pathTranslation()
     *
     * @return void
     */
    public function pathTranslation()
    {
        /**
         * *#@+
         * Host abstraction layer
         */
        if (!isset($_SERVER['PATH_TRANSLATED']) && isset($_SERVER['SCRIPT_FILENAME'])) {
            $_SERVER['PATH_TRANSLATED'] = $_SERVER['SCRIPT_FILENAME']; // For Apache CGI
        } elseif (isset($_SERVER['PATH_TRANSLATED']) && !isset($_SERVER['SCRIPT_FILENAME'])) {
            $_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED']; // For IIS/2K now I think :-(
        }
        /**
         * User Mulitbytes
         */
        if (empty($_SERVER['REQUEST_URI'])) { // Not defined by IIS
            // Under some configs, IIS makes SCRIPT_NAME point to php.exe :-(
            if (!(isset($_SERVER['PHP_SELF']) && ($_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF']))) {
                $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
            }

            if (isset($_SERVER['QUERY_STRING'])) {
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
    }

    /**
     * xos_kernel_Xoops2::themeSelect()
     *
     * @return void
     */
    public function themeSelect()
    {
        if (!empty($_POST['xoops_theme_select']) && in_array($_POST['xoops_theme_select'], xoops_getConfigOption('theme_set_allowed'))) {
            xoops_setConfigOption('theme_set', $_POST['xoops_theme_select']);
            $_SESSION['xoopsUserTheme'] = $_POST['xoops_theme_select'];
        } elseif (!empty($_SESSION['xoopsUserTheme']) && in_array($_SESSION['xoopsUserTheme'], xoops_getConfigOption('theme_set_allowed'))) {
            xoops_setConfigOption('theme_set', $_SESSION['xoopsUserTheme']);
        }
    }
}
