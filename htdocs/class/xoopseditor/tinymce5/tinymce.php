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
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class TinyMCE
{
    public string $rootpath;
    public array $config                = [];
    public array $setting               = [];
    public static $lastOfElementsTinymce = '';
    public static $listOfElementsTinymce = [];

    // Constructor
    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = [];
        $this->setConfig($config);
        $this->rootpath                = $this->config['rootpath'] . '/js/tinymce';
        self::$lastOfElementsTinymce   = $this->config['elements'];
        self::$listOfElementsTinymce[] = self::$lastOfElementsTinymce;
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
            $instance = new self($config);
        } else {
            $instance->setConfig($config);
        }

        return $instance;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
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
        $configured = [];

        // Load default settings
//        if (!($this->setting = @include($GLOBALS['xoops']->path('var/configs/tinymce.php')))) {
//            $this->setting = include __DIR__ . '/settings.php';
//        }

        if (file_exists($GLOBALS['xoops']->path('var/configs/tinymce.php')) && is_readable($GLOBALS['xoops']->path('var/configs/tinymce.php'))) {
            $this->setting = include($GLOBALS['xoops']->path('var/configs/tinymce.php'));
        } else {
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
        if (!empty($this->config)) {
        foreach ($this->config as $key => $val) {
            if (isset($this->setting[$key]) || in_array($key, $configured)) {
                continue;
            }
            $this->setting[$key] = $val;
        }
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
        $plugins      = [];
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
    public function loadCss(string $css_file = 'tinymce.css')
    {
        static $css_url, $css_path;

        if (!isset($css_url)) {
            $css_url  = dirname(xoops_getcss($GLOBALS['xoopsConfig']['theme_set']));
            $css_path = str_replace(XOOPS_THEME_URL, XOOPS_THEME_PATH, $css_url);
        }
        $css         = [];
        if (is_file($css_path . '/' . $css_file) == true) {
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
        // Prevent duplicate initialization if 'elements' changed
		if (isset($this->setting['elements']) && self::$lastOfElementsTinymce !== $this->setting['elements']) {
			return "\n<!-- TinyMCE SKIPPED: 'elements' setting has changed. -->\n";
        }
		$this->setting['elements'] = implode(',', self::$listOfElementsTinymce);

		// Extract custom JS callbacks for later insertion
		$customCallbackJs = !empty($this->setting['callback']) ? $this->setting['callback'] : '';
            unset($this->setting['callback']);

		// Handle file browser callback (external JS)
		$fileBrowserCallbackJs = '';
        if (!empty($this->setting['file_browser_callback'])) {
			$fbc_path = XOOPS_ROOT_PATH . '/class/xoopseditor/tinymce7/include/' . $this->setting['file_browser_callback'] . '.js';
			if (is_readable($fbc_path)) {
				$fileBrowserCallbackJs = "var MyXoopsUrl = '" . XOOPS_URL . "';\n";
				$fileBrowserCallbackJs .= file_get_contents($fbc_path);
				$fileBrowserCallbackJs .= "\n// Callback loaded from: " . $fbc_path;
        } else {
				$fileBrowserCallbackJs = "// FAILED to load callback from: " . $fbc_path;
			}
			unset($this->setting['file_browser_callback']);
        }

		// Safely encode all settings to JS object
		$jsonOptions = json_encode($this->setting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		// Only include TinyMCE core script once
		$tinyMceScriptTag = $isTinyMceJsLoaded
			? "<!-- 'tinymce.min.js' SCRIPT IS ALREADY LOADED -->"
			: "<script type='text/javascript' src='" . XOOPS_URL . $this->rootpath . "/tinymce.min.js'></script>";

            $isTinyMceJsLoaded = true;

		// Output final script
		$output = <<<HTML
<!-- Start TinyMce Rendering -->
{$tinyMceScriptTag}
<script type='text/javascript'>
//<![CDATA[
tinymce.init({$jsonOptions});
{$customCallbackJs}
{$fileBrowserCallbackJs}
//]]>
</script>
<!-- End TinyMce Rendering -->
HTML;

		return $output;
        }

}
