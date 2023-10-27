<?php
/**
 * XOOPS template engine class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2021 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @package             kernel
 * @subpackage          core
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_loadLanguage('global');

/**
 * Template engine
 *
 * @package             kernel
 * @subpackage          core
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2022 XOOPS Project (https://xoops.org)
 */
class XoopsTpl extends SmartyBC
{
    /** @var xos_opal_Theme */
    public $currentTheme;
    /**
     * XoopsTpl constructor.
     */
    public function __construct()
    {
        global $xoopsConfig;

        parent::__construct();

        $this->setLeftDelimiter('<{');
        $this->setRightDelimiter('}>');
        $this->setTemplateDir(XOOPS_THEME_PATH);
        $this->setCacheDir(XOOPS_VAR_PATH . '/caches/smarty_cache');
        $this->setCompileDir(XOOPS_VAR_PATH . '/caches/smarty_compile');
        $this->compile_check   = \Smarty::COMPILECHECK_ON; // ($xoopsConfig['theme_fromfile'] == 1);
        $this->addPluginsDir(XOOPS_ROOT_PATH . '/class/smarty3_plugins');
        if ($xoopsConfig['debug_mode']) {
            $this->debugging_ctrl = 'URL';
            // $this->debug_tpl = XOOPS_ROOT_PATH . '/class/smarty/xoops_tpl/debug.tpl';
            if ($xoopsConfig['debug_mode'] == 3) {
                $this->debugging = true;
            }
        }
        $this->setCompileId();
        $this->assign(array(
                          'xoops_url'        => XOOPS_URL,
                          'xoops_rootpath'   => XOOPS_ROOT_PATH,
                          'xoops_langcode'   => _LANGCODE,
                          'xoops_charset'    => _CHARSET,
                          'xoops_version'    => XOOPS_VERSION,
                          'xoops_upload_url' => XOOPS_UPLOAD_URL));
        $xoopsPreload = XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('core.class.template.new', array($this));
    }

    /**
     * Renders output from template data
     *
     * @param string $tplSource The template to render
     * @param bool   $display   If rendered text should be output or returned
     * @param null   $vars
     *
     * @return string Rendered output if $display was false
     */
    public function fetchFromData($tplSource, $display = false, $vars = null)
    {
        if (!function_exists('smarty_function_eval')) {
            require_once SMARTY_DIR . '/plugins/function.eval.php';
        }
        if (isset($vars)) {
            $oldVars = $this->_tpl_vars;
            $this->assign($vars);
            $out             = smarty_function_eval(array(
                                                        'var' => $tplSource), $this);
            $this->_tpl_vars = $oldVars;

            return $out;
        }

        return smarty_function_eval(array(
                                        'var' => $tplSource), $this);
    }

    /**
     * XoopsTpl::touch
     *
     * @param  mixed $resourceName
     * @return bool
     */
    public function xoopsTouch($resourceName)
    {
        //$result = $this->compileAllTemplates($resourceName, true); // May be necessary?
        $this->clearCache($resourceName);
        return true;
    }

    /**
     * returns an auto_id for auto-file-functions
     *
     * @param  string $cache_id
     * @param  string $compile_id
     * @return string |null
     */
    public function _get_auto_id($cache_id = null, $compile_id = null)
    {
        if (isset($cache_id)) {
            return isset($compile_id) ? $compile_id . '-' . $cache_id : $cache_id;
        } elseif (isset($compile_id)) {
            return $compile_id;
        } else {
            return null;
        }
    }

    /**
     * XoopsTpl::setCompileId()
     *
     * @param  mixed $module_dirname
     * @param  mixed $theme_set
     * @param  mixed $template_set
     * @return void
     */
    public function setCompileId($module_dirname = null, $theme_set = null, $template_set = null)
    {
        global $xoopsConfig;

        $template_set      = empty($template_set) ? $xoopsConfig['template_set'] : $template_set;
        $theme_set         = empty($theme_set) ? $xoopsConfig['theme_set'] : $theme_set;
        if (class_exists('XoopsSystemCpanel', false)) {
            $cPrefix = 'cp-';
            $theme_set =  isset($xoopsConfig['cpanel']) ? $cPrefix .$xoopsConfig['cpanel'] : $cPrefix . 'default';
        }
        $module_dirname    = empty($module_dirname) ? (empty($GLOBALS['xoopsModule']) ? 'system' : $GLOBALS['xoopsModule']->getVar('dirname', 'n')) : $module_dirname;
        $this->compile_id  = substr(md5(XOOPS_URL), 0, 8) . '-' . $module_dirname . '-' . $theme_set . '-' . $template_set;
        //$this->_compile_id = $this->compile_id;
    }

    /**
     * XoopsTpl::clearCache()
     *
     * @param  mixed $module_dirname
     * @param  mixed $theme_set
     * @param  mixed $template_set
     * @return bool
     */
    public function xoopsClearCache($module_dirname = null, $theme_set = null, $template_set = null)
    {
        $compile_id = $this->compile_id;
        $this->setCompileId($module_dirname, $template_set, $theme_set);
        return $this->clearCompiledTemplate(null, $compile_id);
    }

    /**
     *
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param $dirname
     */
    public function xoops_setTemplateDir($dirname)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->template_dir=$value;\' instead.');

        $this->template_dir = $dirname;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @return string
     */
    public function xoops_getTemplateDir()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '() is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->template_dir;\' instead.');

        return $this->template_dir;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param bool $flag
     */
    public function xoops_setDebugging($flag = false)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->debugging=$value;\' instead.');

        $this->debugging = is_bool($flag) ? $flag : false;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param int $num
     */
    public function xoops_setCaching($num = 0)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->caching=$value;\' instead.');

        $this->caching = (int)$num;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param $dirname
     */
    public function xoops_setCompileDir($dirname)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->compile_dir=$value;\' instead.');

        $this->compile_dir = $dirname;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param $dirname
     */
    public function xoops_setCacheDir($dirname)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->cache_dir=$value;\' instead.');

        $this->cache_dir = $dirname;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @return bool
     */
    public function xoops_canUpdateFromFile()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->compile_check;\' instead.');

        return $this->compile_check;
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param $data
     *
     * @return string
     */
    public function xoops_fetchFromData($data)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->fetchFromData($value);\' instead.');

        return $this->fetchFromData($data);
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     * @param int $num
     */
    public function xoops_setCacheTime($num = 0)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . '($value) is deprecated since XOOPS 2.5.4, please use \'$xoopsTpl->cache_lifetime=$value;\' instead.');

        if (($num = (int)$num) <= 0) {
            $this->caching = 0;
        } else {
            $this->cache_lifetime = $num;
        }
    }

    /**
     * deprecated assign_by_ref
     *
     * @param string $tpl_var the template variable name
     * @param mixed  &$value  the referenced value to assign
     */
    public function assign_by_ref($tpl_var, &$value)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use assignByRef");
        $this->assignByRef($tpl_var, $value);
    }

    /**
     * deprecated append_by_ref
     *
     * @param string  $tpl_var the template variable name
     * @param mixed   &$value  the referenced value to append
     * @param boolean $merge   flag if array elements shall be merged
     */
    public function append_by_ref($tpl_var, &$value, $merge = false)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use appendByRef");
        $this->appendByRef($tpl_var, $value, $merge);
    }

    /**
     * deprecated clear_assign
     *
     * @param string $tpl_var the template variable to clear
     */
    public function clear_assign($tpl_var)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use clearAssign");
        $this->clearAssign($tpl_var);
    }

    /**
     * deprecated register_function
     *
     * @param string $function      the name of the template function
     * @param string $function_impl the name of the PHP function to register
     * @param bool   $cacheable
     * @param mixed  $cache_attrs
     *
     * @throws \SmartyException
     */
    public function register_function($function, $function_impl, $cacheable = true, $cache_attrs = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerPlugin");
        $this->registerPlugin('function', $function, $function_impl, $cacheable, $cache_attrs);
    }

    /**
     * deprecated unregister_function
     *
     * @param string $function name of template function
     */
    public function unregister_function($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterPlugin");
        $this->unregisterPlugin('function', $function);
    }

    /**
     * deprecated register_object
     *
     * @param string  $object        name of template object
     * @param object  $object_impl   the referenced PHP object to register
     * @param array   $allowed       list of allowed methods (empty = all)
     * @param boolean $smarty_args   smarty argument format, else traditional
     * @param array   $block_methods list of methods that are block format
     *
     * @throws   SmartyException
     * @internal param array $block_functs list of methods that are block format
     */
    public function register_object(
        $object,
        $object_impl,
        $allowed = array(),
        $smarty_args = true,
        $block_methods = array()
    ) {
        settype($allowed, 'array');
        settype($smarty_args, 'boolean');
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerObject");
        $this->registerObject($object, $object_impl, $allowed, $smarty_args, $block_methods);
    }

    /**
     * deprecated unregister_object
     *
     * @param string $object name of template object
     */
    public function unregister_object($object)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterObject");
        $this->unregisterObject($object);
    }

    /**
     * deprecated register_block
     *
     * @param string $block      name of template block
     * @param string $block_impl PHP function to register
     * @param bool   $cacheable
     * @param mixed  $cache_attrs
     *
     * @throws \SmartyException
     */
    public function register_block($block, $block_impl, $cacheable = true, $cache_attrs = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerPlugin");
        $this->registerPlugin('block', $block, $block_impl, $cacheable, $cache_attrs);
    }

    /**
     * deprecated unregister_block
     *
     * @param string $block name of template function
     */
    public function unregister_block($block)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterPlugin");
        $this->unregisterPlugin('block', $block);
    }

    /**
     * deprecated register_compiler_function
     *
     * @param string $function      name of template function
     * @param string $function_impl name of PHP function to register
     * @param bool   $cacheable
     *
     * @throws \SmartyException
     */
    public function register_compiler_function($function, $function_impl, $cacheable = true)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerPlugin");
        $this->registerPlugin('compiler', $function, $function_impl, $cacheable);
    }

    /**
     * deprecated unregister_compiler_function
     *
     * @param string $function name of template function
     */
    public function unregister_compiler_function($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterPlugin");
        $this->unregisterPlugin('compiler', $function);
    }

    /**
     * deprecated register_modifier
     *
     * @param string $modifier      name of template modifier
     * @param string $modifier_impl name of PHP function to register
     *
     * @throws \SmartyException
     */
    public function register_modifier($modifier, $modifier_impl)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerPlugin");
        $this->registerPlugin('modifier', $modifier, $modifier_impl);
    }

    /**
     * deprecated unregister_modifier
     *
     * @param string $modifier name of template modifier
     */
    public function unregister_modifier($modifier)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterPlugin");
        $this->unregisterPlugin('modifier', $modifier);
    }

    /**
     * deprecated register_resource
     *
     * @param string $type      name of resource
     * @param array  $functions array of functions to handle resource
     */
    public function register_resource($type, $functions)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerResource");
        $this->registerResource($type, $functions);
    }

    /**
     * deprecated unregister_resource
     *
     * @param string $type name of resource
     */
    public function unregister_resource($type)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterResource");
        $this->unregisterResource($type);
    }

    /**
     * deprecated register_prefilter
     *
     * @param callable $function
     *
     * @throws \SmartyException
     */
    public function register_prefilter($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerFilter");
        $this->registerFilter('pre', $function);
    }

    /**
     * deprecated unregister_prefilter
     *
     * @param callable $function
     */
    public function unregister_prefilter($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterFilter");
        $this->unregisterFilter('pre', $function);
    }

    /**
     * deprecated register_postfilter
     *
     * @param callable $function
     *
     * @throws \SmartyException
     */
    public function register_postfilter($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerFilter");
        $this->registerFilter('post', $function);
    }

    /**
     * deprecated unregister_postfilter
     *
     * @param callable $function
     */
    public function unregister_postfilter($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterFilter");
        $this->unregisterFilter('post', $function);
    }

    /**
     * deprecated register_outputfilter
     *
     * @param callable $function
     *
     * @throws \SmartyException
     */
    public function register_outputfilter($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use registerFilter");
        $this->registerFilter('output', $function);
    }

    /**
     * deprecated unregister_outputfilter
     *
     * @param callable $function
     */
    public function unregister_outputfilter($function)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use unregisterFilter");
        $this->unregisterFilter('output', $function);
    }

    /**
     * deprecated load_filter
     *
     * @param string $type filter type
     * @param string $name filter name
     *
     * @throws \SmartyException
     */
    public function load_filter($type, $name)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use loadFilter");
        $this->loadFilter($type, $name);
    }

    /**
     * deprecated clear_cache
     *
     * @param string $tpl_file   name of template file
     * @param string $cache_id   name of cache_id
     * @param string $compile_id name of compile_id
     * @param string $exp_time   expiration time
     *
     * @return boolean
     */
    public function clear_cache($tpl_file = null, $cache_id = null, $compile_id = null, $exp_time = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use clearCache");
        return $this->clearCache($tpl_file, $cache_id, $compile_id, $exp_time);
    }

    /**
     * deprecated clear_all_cache
     *
     * @param string $exp_time expire time
     *
     * @return boolean
     */
    public function clear_all_cache($exp_time = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use clearCache");
        return $this->clearCache(null, null, null, $exp_time);
    }

    /**
     * deprecated is_cached
     *
     * @param string $tpl_file name of template file
     * @param string $cache_id
     * @param string $compile_id
     *
     * @return bool
     * @throws \Exception
     * @throws \SmartyException
     */
    public function is_cached($tpl_file, $cache_id = null, $compile_id = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use isCached");
        return $this->isCached($tpl_file, $cache_id, $compile_id);
    }

    /**
     * deprecated clear_all_assign
     */
    public function clear_all_assign()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use clearAllAssign");
        $this->clearAllAssign();
    }

    /**
     * deprecated clear_compiled_tpl
     *
     * @param string $tpl_file
     * @param string $compile_id
     * @param string $exp_time
     *
     * @return boolean results of {@link smarty_core_rm_auto()}
     */
    public function clear_compiled_tpl($tpl_file = null, $compile_id = null, $exp_time = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use clearCompiledTemplate");
        return $this->clearCompiledTemplate($tpl_file, $compile_id, $exp_time);
    }

    /**
     * deprecated template_exists
     *
     * @param string $tpl_file
     *
     * @return bool
     * @throws \SmartyException
     */
    public function template_exists($tpl_file)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use templateExists");
        return $this->templateExists($tpl_file);
    }

    /**
     * deprecated get_template_vars
     *
     * @param string $name
     *
     * @return array
     */
    public function get_template_vars($name = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use getTemplateVars");
        return $this->getTemplateVars($name);
    }

    /**
     * deprecated get_config_vars
     *
     * @param string $name
     *
     * @return array
     */
    public function get_config_vars($name = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use getConfigVars");
        return $this->getConfigVars($name);
    }

    /**
     * deprecated config_load
     *
     * @param string $file
     * @param string $section
     * @param string $scope
     */
    public function config_load($file, $section = null, $scope = 'global')
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use ConfigLoad");
        $this->ConfigLoad($file, $section, $scope);
    }

    /**
     * deprecated get_registered_object
     *
     * @param string $name
     *
     * @return object
     */
    public function get_registered_object($name)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use getRegisteredObject");
        return $this->getRegisteredObject($name);
    }

    /**
     * deprecated clear_config
     *
     * @param string $var
     */
    public function clear_config($var = null)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated, please use clearConfig");
        $this->clearConfig($var);
    }
}

/**
 * function to update compiled template file in templates_c folder
 *
 * @param  string  $tpl_id
 * @param  boolean $clear_old
 * @return boolean
 */
function xoops_template_touch($tpl_id)
{
    $tplfile_handler = xoops_getHandler('tplfile');
    $tplfile         = $tplfile_handler->get((int)$tpl_id);

    if (is_object($tplfile)) {
        $file = $tplfile->getVar('tpl_file', 'n');
        $tpl  = new XoopsTpl();

        return $tpl->xoopsTouch('db:' . $file);
    }

    return false;
}

/**
 * Clear the module cache
 *
 * @param  int $mid Module ID
 * @return void
 */
function xoops_template_clear_module_cache($mid)
{
    $block_arr = XoopsBlock::getByModule($mid);
    $count     = count($block_arr);
    if ($count > 0) {
        $xoopsTpl          = new XoopsTpl();
        $xoopsTpl->caching = 2;
        for ($i = 0; $i < $count; ++$i) {
            if ($block_arr[$i]->getVar('template') != '') {
                $xoopsTpl->clearCache('db:' . $block_arr[$i]->getVar('template'), 'blk_' . $block_arr[$i]->getVar('bid'));
            }
        }
    }
}
