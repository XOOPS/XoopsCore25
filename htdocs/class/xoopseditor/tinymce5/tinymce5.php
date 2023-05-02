<?php
 /**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * TinyMCE5 adapter for XOOPS
 *
 * @category  XoopsEditor
 * @package   TinyMCE5
 * @author    Gregory Mage
 * @author    Taiwen Jiang <phppp@users.sourceforge.net>
 * @author    Lucio Rota <lucio.rota@gmail.com>
 * @author    Laurent JEN <dugris@frxoops.org>
 * @copyright 2020 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class TinyMCE
{
    public        $rootpath;
    public        $config                = array();
    public        $setting               = array();
    public static $LastOfElementsTinymce = '';
    public static $ListOfElementsTinymce = array();

    // PHP 5 Constructor
    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->setConfig($config);
        $this->rootpath                = $this->config['rootpath'] . '/tinymce5/jscripts/tiny_mce';
        self::$LastOfElementsTinymce   = $this->config['elements'];
        self::$ListOfElementsTinymce[] = self::$LastOfElementsTinymce;
    }

    /**
     * @param $config
     *
     * @return TinyMCE
     */
    public function &instance($config)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new TinyMCE($config);
        } else {
            $instance->setConfig($config);
        }

        return $instance;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        foreach ($config as $key => $val) {
            $this->config[$key] = $val;
        }
    }

    /**
     * @return bool
     */
    public function init()
    {
        // list of configured options
        $configured = array();

        // Load default settings
        if (!($this->setting = @include($GLOBALS['xoops']->path('var/configs/tinymce.php')))) {
            $this->setting = include __DIR__ . '/settings.php';
        }

        // get editor language (from ...)
        if (isset($this->config['language']) && is_readable(XOOPS_ROOT_PATH . $this->rootpath . '/langs/' . $this->config['language'] . '.js')) {
            $this->setting['language'] = $this->config['language'];
            $configured[]              = 'language';
        }

        $this->setting['content_css'] = implode(',', $this->loadCss());
        $configured[]                 = 'content_css';

        if (!empty($this->config['theme']) && is_dir(XOOPS_ROOT_PATH . $this->rootpath . '/themes/' . $this->config['theme'])) {
            $this->setting['theme'] = $this->config['theme'];
            $configured[]           = 'theme';
        }

        if (!empty($this->config['mode'])) {
            $this->setting['mode'] = $this->config['mode'];
            $configured[]          = 'mode';
        }

        // load all plugins
        $this->setting['plugins'] = implode(',', $this->loadPlugins());
        $configured[]             = 'plugins';

        $configured = array_unique($configured);
        foreach ($this->config as $key => $val) {
            if (isset($this->setting[$key]) || in_array($key, $configured)) {
                continue;
            }
            $this->setting[$key] = $val;
        }

        if (!is_dir(XOOPS_ROOT_PATH . $this->rootpath . '/themes/' . $this->setting['theme'] . '/docs/' . $this->setting['language'] . '/')) {
            $this->setting['docs_language'] = 'en';
        }

        unset($this->config, $configured);

        return true;
    }

    // load all plugins
    /**
     * @return array
     */
    public function loadPlugins()
    {
        $plugins      = array();
        $plugins_list = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . $this->rootpath . '/plugins');
        if (empty($this->setting['plugins'])) {
            $plugins = $plugins_list;
        } else {
            $plugins = array_intersect(explode(',', $this->setting['plugins']), $plugins_list);
        }
        if (!empty($this->config['plugins'])) {
            $plugins = array_merge($plugins, $this->config['plugins']);
        }

        return $plugins;
    }

    /**
     * @param string $css_file
     *
     * @return array
     */
    public function loadCss($css_file = 'tinymce.css')
    {
        static $css_url, $css_path;

        if (!isset($css_url)) {
            $css_url  = dirname(xoops_getcss($GLOBALS['xoopsConfig']['theme_set']));
            $css_path = str_replace(XOOPS_THEME_URL, XOOPS_THEME_PATH, $css_url);
        }
		$css         = array();
		if (is_file($css_path . '/' . $css_file) == true){
			$css[]       = $css_url . '/' . $css_file;		
			$css_content = file_get_contents($css_path . '/' . $css_file);
			// get all import css files
			if (preg_match_all("~\@import url\((.*\.css)\);~sUi", $css_content, $matches, PREG_PATTERN_ORDER)) {
				foreach ($matches[1] as $key => $css_import) {
					$css = array_merge($css, $this->loadCss($css_import)); 
				}
			}
		}
        return $css;
    }

    /**
     * @return string
     */
    public function render()
    {
        static $isTinyMceJsLoaded = false;

        $this->init();
        if (isset($this->setting['elements']) && self::$LastOfElementsTinymce != $this->setting['elements']) {
            $ret = "\n<!-- 'tinymce.js' SCRIPT NOT YET " . $this->setting['elements'] . " -->\n"; //debug

            return $ret;
        } else {
            $this->setting['elements'] = implode(',', self::$ListOfElementsTinymce);
        }
        if (!empty($this->setting['callback'])) {
            $callback = $this->setting['callback'];
            unset($this->setting['callback']);
        } else {
            $callback = '';
        }
        if (!empty($this->setting['file_browser_callback'])) {
            $fbc_name = XOOPS_ROOT_PATH . '/class/xoopseditor/tinymce5/include/' . $this->setting['file_browser_callback'] . '.js';
            //suis passé la /lesrevespossibles/x244/class/xoopseditor/tinymce/tinymce/jscripts/include/openFinder.js
            $file_browser_callback = "MyXoopsUrl ='" . XOOPS_URL . "';\n";
            $file_browser_callback .= file_get_contents($fbc_name);
            $file_browser_callback .= "\n//suis passé la " . $fbc_name;
            //unset($this->setting["file_browser_callback"]);
        } else {
            $file_browser_callback = '//suis absent';
        }

        // create returned string - start
        $ret = "\n";

        $ret .= "<!-- Start TinyMce Rendering -->\n"; //debug
        if ($isTinyMceJsLoaded) {
            $ret .= "<!-- 'tinymce.js' SCRIPT IS ALREADY LOADED -->\n"; //debug
        } else {
            $ret .= "<script type='text/javascript' src='" . XOOPS_URL . $this->rootpath . "/tinymce.js'></script>\n";
            $isTinyMceJsLoaded = true;
        }
        $ret .= "<script type='text/javascript'>\n";
        $ret .= "tinymce.init({\n";
        // set options - start
        foreach ($this->setting as $key => $val) {
            $ret .= $key . ':';
            if ($val === true) {
                $ret .= 'true,';
            } elseif ($val === false) {
                $ret .= 'false,';
            } else {
                $ret .= "'{$val}',";
            }
            $ret .= "\n";
        }
        // set options - end
        $ret .= "tinymceload: true\n";
        $ret .= "});\n";
        $ret .= $callback . "\n";
        $ret .= $file_browser_callback . "\n";
        //$ret .= "function toggleEditor(id) {tinyMCE.execCommand('mceToggleEditor',false, id);}\n";
        $ret .= "</script>\n";
        $ret .= "<!-- End TinyMce Rendering -->\n";//debug
        // create returned string - end
        return $ret;
    }
}
