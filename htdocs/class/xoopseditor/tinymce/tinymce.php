<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              Lucio Rota <lucio.rota@gmail.com>
 * @author              Laurent JEN <dugris@frxoops.org>
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
        $this->rootpath                = $this->config['rootpath'] . '/tinymce/jscripts/tiny_mce';
        $this->xoopsPlugins            = $this->get_xoopsPlugins();
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

        // load all plugins except the plugins in setting["exclude_plugins"]
        $this->setting['plugins'] = implode(',', $this->loadPlugins());
        $configured[]             = 'plugins';

        if ($this->setting['theme'] !== 'simple') {
            if (empty($this->config['buttons'])) {
                $this->config['buttons'][] = array(
                    'before' => '',
                    'add' => '');
                $this->config['buttons'][] = array(
                    'before' => '',
                    'add' => '');
                $this->config['buttons'][] = array(
                    'before' => '',
                    'add' => '');
            }
            $i = 0;
            foreach ($this->config['buttons'] as $button) {
                ++$i;
                if (isset($button['before'])) {
                    $this->setting['theme_' . $this->setting['theme'] . "_buttons{$i}_add_before"] = $button['before'];
                }
                if (isset($button['add'])) {
                    $this->setting['theme_' . $this->setting['theme'] . "_buttons{$i}_add"] = $button['add'];
                }
                if (isset($button[''])) {
                    $this->setting['theme_' . $this->setting['theme'] . "_buttons{$i}"] = $button[''];
                }
            }
            $configured[] = 'buttons';

            if (isset($this->config['toolbar_location'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_toolbar_location'] = $this->config['toolbar_location'];
                $configured[]                                                            = 'toolbar_location';
            } else {
                $this->setting['theme_' . $this->setting['theme'] . '_toolbar_location'] = 'top';
            }

            if (isset($this->config['toolbar_align'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_toolbar_align'] = $this->config['toolbar_align'];
                $configured[]                                                         = 'toolbar_align';
            } else {
                $this->setting['theme_' . $this->setting['theme'] . '_toolbar_align'] = 'left';
            }

            if (isset($this->config['statusbar_location'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_statusbar_location'] = $this->config['statusbar_location'];
                $configured[]                                                              = 'statusbar_location';
            }

            if (isset($this->config['path_location'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_path_location'] = $this->config['path_location'];
                $configured[]                                                         = 'path_location';
            }

            if (isset($this->config['resize_horizontal'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_resize_horizontal'] = $this->config['resize_horizontal'];
                $configured[]                                                             = 'resize_horizontal';
            }

            if (isset($this->config['resizing'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_resizing'] = $this->config['resizing'];
                $configured[]                                                    = 'resizing';
            }

            if (!empty($this->config['fonts'])) {
                $this->setting['theme_' . $this->setting['theme'] . '_fonts'] = $this->config['fonts'];
                $configured[]                                                 = 'fonts';
            }

            for ($i = 1; $i <= 4; ++$i) {
                $buttons = array();
                if (isset($this->setting['theme_' . $this->setting['theme'] . "_buttons{$i}"])) {
                    $checklist = explode(',', $this->setting['theme_' . $this->setting['theme'] . "_buttons{$i}"]);
                    foreach ($checklist as $plugin) {
                        if (strpos(strtolower($plugin), 'xoops') != false) {
                            if (in_array($plugin, $this->xoopsPlugins)) {
                                $buttons[] = $plugin;
                            }
                        } else {
                            $buttons[] = $plugin;
                        }
                    }
                    $this->setting['theme_' . $this->setting['theme'] . "_buttons{$i}"] = implode(',', $buttons);
                }
            }
        }

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

    // load all plugins execpt the plugins in setting["exclude_plugins"]
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
        if (!empty($this->setting['exclude_plugins'])) {
            $plugins = array_diff($plugins, explode(',', $this->setting['exclude_plugins']));
        }
        if (!empty($this->config['plugins'])) {
            $plugins = array_merge($plugins, $this->config['plugins']);
        }

        return $plugins;
    }

    // return all xoops plugins
    /**
     * @return array
     */
    public function get_xoopsPlugins()
    {
        $xoopsPlugins = array();
        $allplugins   = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . $this->rootpath . '/plugins');
        foreach ($allplugins as $plugin) {
            if (strpos(strtolower($plugin), 'xoops') != false && file_exists(XOOPS_ROOT_PATH . $this->config['rootpath'] . "/include/$plugin.php")) {
                if ($right = @include XOOPS_ROOT_PATH . $this->config['rootpath'] . "/include/$plugin.php") {
                    $xoopsPlugins[$plugin] = $plugin;
                }
            }
        }

        return $xoopsPlugins;
    }

    /**
     * @param string $css_file
     *
     * @return array
     */
    public function loadCss($css_file = 'style.css')
    {
        static $css_url, $css_path;

        if (!isset($css_url)) {
            $css_url  = dirname(xoops_getcss($GLOBALS['xoopsConfig']['theme_set']));
            $css_path = str_replace(XOOPS_THEME_URL, XOOPS_THEME_PATH, $css_url);
        }

        $css         = array();
        $css[]       = $css_url . '/' . $css_file;
        $css_content = file_get_contents($css_path . '/' . $css_file);

        // get all import css files
        if (preg_match_all("~\@import url\((.*\.css)\);~sUi", $css_content, $matches, PREG_PATTERN_ORDER)) {
            foreach ($matches[1] as $key => $css_import) {
                $css = array_merge($css, $this->loadCss($css_import));
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
            $ret = "\n<!-- 'tiny_mce.js' SCRIPT NOT YET " . $this->setting['elements'] . " -->\n"; //debug

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
            $fbc_name = XOOPS_ROOT_PATH . '/class/xoopseditor/tinymce/include/' . $this->setting['file_browser_callback'] . '.js';
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

        /*
        // IE BUG - start
        // more info here:
        // http://www.456bereastreet.com/archive/200802/beware_of_id_and_name_attribute_mixups_when_using_getelementbyid_in_internet_explorer/
        // possible solution here:
        // http://www.sixteensmallstones.org/ie-javascript-bugs-overriding-internet-explorers-documentgetelementbyid-to-be-w3c-compliant-exposes-an-additional-bug-in-getattributes
                $ret =<<<EOF
        <script language='javascript' type='text/javascript'>
            if (/msie/i.test (navigator.userAgent)) { //only override IE
                document.nativeGetElementById = document.getElementById;
                document.getElementById = function(id) {
                    var elem = document.nativeGetElementById(id);
                    if (elem) {
                        //make sure that it is a valid match on id
                        if (elem.attributes['id'].value == id) {
                            return elem;
                        } else {
                            //otherwise find the correct element
                            for (var i=1;i<document.all[id].length;i++) {
                                if (document.all[id][i].attributes['id'].value == id) {
                                    return document.all[id][i];
                                }
                            }
                        }
                    }

                    return null;
                };
            }
        </script>
        \n
        EOF;
        // IE BUG - end
        */

        $ret .= "<!-- Start TinyMce Rendering -->\n"; //debug
        if ($isTinyMceJsLoaded) {
            $ret .= "<!-- 'tiny_mce.js' SCRIPT IS ALREADY LOADED -->\n"; //debug
        } else {
            $ret .= "<script type='text/javascript' src='" . XOOPS_URL . $this->rootpath . "/tiny_mce.js'></script>\n";
            $isTinyMceJsLoaded = true;
        }
        $ret .= "<script type='text/javascript'>\n";
        $ret .= "tinyMCE.init({\n";
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
