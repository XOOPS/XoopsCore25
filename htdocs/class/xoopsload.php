<?php
/**
 * Xoops Autoload class
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
 * @package             class
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @todo                For PHP 5 compliant
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XoopsLoad
 */
class XoopsLoad
{
    //static  $loaded;
    //static  $configs;

    /**
     * @param        $name
     * @param string $type
     *
     * @return bool
     */
    public static function load($name, $type = 'core')
    {
        static $loaded;
        static $deprecated;

        if (!isset($deprecated)) {
            $deprecated = array(
                'uploader'    => 'xoopsmediauploader',
                'utility'     => 'xoopsutility',
                'captcha'     => 'xoopscaptcha',
                'cache'       => 'xoopscache',
                'file'        => 'xoopsfile',
                'model'       => 'xoopsmodelfactory',
                'calendar'    => 'xoopscalendar',
                'userutility' => 'xoopsuserutility');
        }
        $name = strtolower($name);
        if (in_array($type, array('core', 'class')) && array_key_exists($name, $deprecated)) {
            if (isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger']->addDeprecated("xoops_load('{$name}') is deprecated, use xoops_load('{$deprecated[$name]}')");
            } else {
                trigger_error("xoops_load('{$name}') is deprecated, use xoops_load('{$deprecated[$name]}')", E_USER_WARNING);
            }
            $name = $deprecated[$name];
        }

        $type = empty($type) ? 'core' : $type;
        if (isset($loaded[$type][$name])) {
            return $loaded[$type][$name];
        }

        if (class_exists($name, false)) {
            $loaded[$type][$name] = true;

            return true;
        }
        $isloaded = false;
        switch ($type) {
            case 'framework':
                $isloaded = XoopsLoad::loadFramework($name);
                break;
            case 'class':
            case 'core':
                $type     = 'core';
                $isloaded = XoopsLoad::loadCore($name);
                break;
            default:
                $isloaded = XoopsLoad::loadModule($name, $type);
                break;
        }
        $loaded[$type][$name] = $isloaded;

        return $loaded[$type][$name];
    }

    /**
     * Load core class
     *
     * @access private
     * @param $name
     * @return bool|string
     */
    public static function loadCore($name)
    {
        static $configs;

        if (!isset($configs)) {
            $configs = XoopsLoad::loadCoreConfig();
        }
        if (isset($configs[$name])) {
            require_once $configs[$name];
            if (class_exists($name) && method_exists($name, '__autoload')) {
                call_user_func(array($name, '__autoload'));
            }

            return true;
        } elseif (file_exists($file = XOOPS_ROOT_PATH . '/class/' . $name . '.php')) {
            include_once $file;
            $class = 'Xoops' . ucfirst($name);
            if (class_exists($class)) {
                return $class;
            } else {
                trigger_error('Class ' . $name . ' not found in file ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);
            }
        }

        return false;
    }

    /**
     * Load Framework class
     *
     * @access private
     * @param $name
     * @return bool|string
     */
    public static function loadFramework($name)
    {
        if (!file_exists($file = XOOPS_ROOT_PATH . '/Frameworks/' . $name . '/xoops' . $name . '.php')) {
            trigger_error('File ' . str_replace(XOOPS_ROOT_PATH, '', $file) . ' not found in file ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);

            return false;
        }
        include_once $file;
        $class = 'Xoops' . ucfirst($name);
        if (class_exists($class)) {
            return $class;
        }
        return null;
    }

    /**
     * Load module class
     *
     * @access private
     * @param  string      $name    class file name
     * @param  string|null $dirname module directory name
     * @return bool
     */
    public static function loadModule($name, $dirname = null)
    {
        if (empty($dirname)) {
            return false;
        }
        if (file_exists($file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/' . $name . '.php')) {
            include_once $file;
            if (class_exists(ucfirst($dirname) . ucfirst($name))) {
                return true;
            }
        }

        return false;
    }

    /**
     * XoopsLoad::loadCoreConfig()
     *
     * @return array
     */
    public static function loadCoreConfig()
    {
        return $configs = array(
            'xoopsuserutility'           => XOOPS_ROOT_PATH . '/class/userutility.php',
            'xoopsmediauploader'         => XOOPS_ROOT_PATH . '/class/uploader.php',
            'xoopsutility'               => XOOPS_ROOT_PATH . '/class/utility/xoopsutility.php',
            'xoopscaptcha'               => XOOPS_ROOT_PATH . '/class/captcha/xoopscaptcha.php',
            'xoopscache'                 => XOOPS_ROOT_PATH . '/class/cache/xoopscache.php',
            'xoopsfile'                  => XOOPS_ROOT_PATH . '/class/file/xoopsfile.php',
            'xoopsmodelfactory'          => XOOPS_ROOT_PATH . '/class/model/xoopsmodel.php',
            'xoopscalendar'              => XOOPS_ROOT_PATH . '/class/calendar/xoopscalendar.php',
            'xoopskernel'                => XOOPS_ROOT_PATH . '/class/xoopskernel.php',
            'xoopssecurity'              => XOOPS_ROOT_PATH . '/class/xoopssecurity.php',
            'xoopslogger'                => XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php',
            'xoopspagenav'               => XOOPS_ROOT_PATH . '/class/pagenav.php',
            'xoopslists'                 => XOOPS_ROOT_PATH . '/class/xoopslists.php',
            'xoopslocal'                 => XOOPS_ROOT_PATH . '/include/xoopslocal.php',
            'xoopslocalabstract'         => XOOPS_ROOT_PATH . '/class/xoopslocal.php',
            'xoopseditor'                => XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php',
            'xoopseditorhandler'         => XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php',
            'xoopsformloader'            => XOOPS_ROOT_PATH . '/class/xoopsformloader.php',
            'xoopsformelement'           => XOOPS_ROOT_PATH . '/class/xoopsform/formelement.php',
            'xoopsform'                  => XOOPS_ROOT_PATH . '/class/xoopsform/form.php',
            'xoopsformlabel'             => XOOPS_ROOT_PATH . '/class/xoopsform/formlabel.php',
            'xoopsformselect'            => XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php',
            'xoopsformpassword'          => XOOPS_ROOT_PATH . '/class/xoopsform/formpassword.php',
            'xoopsformbutton'            => XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php',
            'xoopsformbuttontray'        => XOOPS_ROOT_PATH . '/class/xoopsform/formbuttontray.php',
            'xoopsformcheckbox'          => XOOPS_ROOT_PATH . '/class/xoopsform/formcheckbox.php',
            'xoopsformselectcheckgroup'  => XOOPS_ROOT_PATH . '/class/xoopsform/formselectcheckgroup.php',
            'xoopsformhidden'            => XOOPS_ROOT_PATH . '/class/xoopsform/formhidden.php',
            'xoopsformfile'              => XOOPS_ROOT_PATH . '/class/xoopsform/formfile.php',
            'xoopsformradio'             => XOOPS_ROOT_PATH . '/class/xoopsform/formradio.php',
            'xoopsformradioyn'           => XOOPS_ROOT_PATH . '/class/xoopsform/formradioyn.php',
            'xoopsformselectcountry'     => XOOPS_ROOT_PATH . '/class/xoopsform/formselectcountry.php',
            'xoopsformselecttimezone'    => XOOPS_ROOT_PATH . '/class/xoopsform/formselecttimezone.php',
            'xoopsformselectlang'        => XOOPS_ROOT_PATH . '/class/xoopsform/formselectlang.php',
            'xoopsformselectgroup'       => XOOPS_ROOT_PATH . '/class/xoopsform/formselectgroup.php',
            'xoopsformselectuser'        => XOOPS_ROOT_PATH . '/class/xoopsform/formselectuser.php',
            'xoopsformselecttheme'       => XOOPS_ROOT_PATH . '/class/xoopsform/formselecttheme.php',
            'xoopsformselectmatchoption' => XOOPS_ROOT_PATH . '/class/xoopsform/formselectmatchoption.php',
            'xoopsformtext'              => XOOPS_ROOT_PATH . '/class/xoopsform/formtext.php',
            'xoopsformtextarea'          => XOOPS_ROOT_PATH . '/class/xoopsform/formtextarea.php',
            'xoopsformdhtmltextarea'     => XOOPS_ROOT_PATH . '/class/xoopsform/formdhtmltextarea.php',
            'xoopsformelementtray'       => XOOPS_ROOT_PATH . '/class/xoopsform/formelementtray.php',
            'xoopsthemeform'             => XOOPS_ROOT_PATH . '/class/xoopsform/themeform.php',
            'xoopssimpleform'            => XOOPS_ROOT_PATH . '/class/xoopsform/simpleform.php',
            'xoopsformtextdateselect'    => XOOPS_ROOT_PATH . '/class/xoopsform/formtextdateselect.php',
            'xoopsformdatetime'          => XOOPS_ROOT_PATH . '/class/xoopsform/formdatetime.php',
            'xoopsformhiddentoken'       => XOOPS_ROOT_PATH . '/class/xoopsform/formhiddentoken.php',
            'xoopsformcolorpicker'       => XOOPS_ROOT_PATH . '/class/xoopsform/formcolorpicker.php',
            'xoopsformcaptcha'           => XOOPS_ROOT_PATH . '/class/xoopsform/formcaptcha.php',
            'xoopsformeditor'            => XOOPS_ROOT_PATH . '/class/xoopsform/formeditor.php',
            'xoopsformselecteditor'      => XOOPS_ROOT_PATH . '/class/xoopsform/formselecteditor.php',
            'xoopsformcalendar'          => XOOPS_ROOT_PATH . '/class/xoopsform/formcalendar.php',
            'xoopsformrenderer'          => XOOPS_ROOT_PATH . '/class/xoopsform/renderer/XoopsFormRenderer.php',
            'xoopsformrendererinterface' => XOOPS_ROOT_PATH . '/class/xoopsform/renderer/XoopsFormRendererInterface.php',
            'xoopsformrendererlegacy'    => XOOPS_ROOT_PATH . '/class/xoopsform/renderer/XoopsFormRendererLegacy.php',
            'xoopsformrendererbootstrap3'=> XOOPS_ROOT_PATH . '/class/xoopsform/renderer/XoopsFormRendererBootstrap3.php',
            'xoopsfilterinput'           => XOOPS_ROOT_PATH . '/class/xoopsfilterinput.php',
            'xoopsrequest'               => XOOPS_ROOT_PATH . '/class/xoopsrequest.php');
    }

    /**
     * XoopsLoad::loadConfig()
     *
     * @param mixed $data
     *
     * @return array|bool
     */
    public function loadConfig($data = null)
    {
        if (is_array($data)) {
            $configs = $data;
        } else {
            if (!empty($data)) {
                $dirname = $data;
            } elseif (is_object($GLOBALS['xoopsModule'])) {
                $dirname = $GLOBALS['xoopsModule']->getVar('dirname', 'n');
            } else {
                return false;
            }
            if (file_exists($file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/include/autoload.php')) {
                if (!$configs = include $file) {
                    return false;
                }
            }
        }

        return $configs = array_merge(XoopsLoad::loadCoreConfig(), $configs);
    }
}
// To be enabled in XOOPS 3.0
// spl_autoload_register(array('XoopsLoad', 'load'));

/**
 * XMF libraries
 */
include_once XOOPS_ROOT_PATH . '/class/libraries/vendor/autoload.php';
