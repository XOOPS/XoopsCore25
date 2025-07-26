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
 * TinyMCE7 adapter for XOOPS
 *
 * @category  XoopsEditor
 * @package   TinyMCE7
 * @author    Gregory Mage
 * @author    Taiwen Jiang <phppp@users.sourceforge.net>
 * @author    Lucio Rota <lucio.rota@gmail.com>
 * @author    Laurent JEN <dugris@frxoops.org>
 * @copyright 2000-2025 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */


/**
 * Class TinyMCE
 *
 * Manages the configuration and rendering of the TinyMCE editor.
 * This class can be used as a singleton via the instance() method.
 */
class TinyMCE
{
	/** @var string The web-accessible root path to the TinyMCE script directory. */
	public string $rootpath;

	/** @var array The initial configuration passed to the constructor. */
	public array $config = [];

	/** @var array The final, merged settings used to initialize the editor. */
	public array $setting = [];

	/** @var string Stores the 'elements' string from the last initialization. */
	public static string $lastOfElementsTinymce = '';

	/** @var string[] A list of all element IDs to be converted into editors on the page. */
	public static array $listOfElementsTinymce = [];

	/**
	 * TinyMCE constructor.
	 *
	 * @param array $config Initial configuration for the editor instance.
	 */
	public function __construct(array $config = [])
	{
		$this->setConfig($config);
		$this->rootpath = ($this->config['rootpath'] ?? '') . '/js/tinymce';
		self::$lastOfElementsTinymce = $this->config['elements'] ?? '';
		if (!empty(self::$lastOfElementsTinymce)) {
			self::$listOfElementsTinymce[] = self::$lastOfElementsTinymce;
		}
	}

	/**
	 * Gets a singleton instance of the TinyMCE class.
	 *
	 * @param array $config Configuration to apply on first call or to update the existing instance.
	 * @return self The singleton instance.
	 */
	public static function &instance(array $config = []): self
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
	 * Sets or updates the configuration.
	 *
	 * @param array $config An array of configuration options.
	 */
	public function setConfig(array $config): void
	{
		$this->config = array_merge($this->config, $config);
	}

	/**
	 * Initializes the editor settings by merging defaults with custom configurations.
	 */
	public function init(): void
	{
		// 1. Load base settings from a file
		$settingsFile = $GLOBALS['xoops']->path('var/configs/tinymce.php');
		$this->setting = is_readable($settingsFile)
			? (include $settingsFile)
			: (include __DIR__ . '/settings.php');

		// 2. Override base settings with values from the constructor config
		$this->setting['language'] = $this->config['language'] ?? $this->setting['language'] ?? 'en';
		$this->setting['theme'] = $this->config['theme'] ?? $this->setting['theme'] ?? 'silver';
		$this->setting['mode'] = $this->config['mode'] ?? $this->setting['mode'] ?? 'exact';

		// 3. Load dynamic settings that depend on other configs
		$this->setting['plugins'] = implode(',', $this->loadPlugins());
		$this->setting['content_css'] = implode(',', $this->loadCss());

		// 4. Merge any remaining constructor configs that were not explicitly handled
		$this->setting = array_merge($this->setting, $this->config);
	}

	/**
	 * Compiles a list of plugins to load based on settings and availability.
	 *
	 * @return string[] A list of unique plugin names.
	 */
	public function loadPlugins(): array
	{
		$plugins_dir = XOOPS_ROOT_PATH . $this->rootpath . '/plugins';
		if (!is_dir($plugins_dir)) {
			return [];
		}
		$availablePlugins = XoopsLists::getDirListAsArray($plugins_dir);

		$defaultPlugins = !empty($this->setting['plugins']) ? explode(',', (string)$this->setting['plugins']) : $availablePlugins;
		$configPlugins = !empty($this->config['plugins']) ? (array)$this->config['plugins'] : [];

		$plugins = array_intersect($defaultPlugins, $availablePlugins);
		return array_unique(array_merge($plugins, $configPlugins));
	}

	/**
	 * Recursively loads a CSS file and its @import dependencies.
	 *
	 * @param string $cssFile The entry CSS file relative to the theme directory.
	 * @param array  $visitedPaths Used internally to prevent infinite recursion.
	 * @return string[] A flat list of unique CSS URLs.
	 */
	public function loadCss(string $cssFile = 'tinymce.css', array &$visitedPaths = []): array
	{
		static $themeUrl, $themePath;
		if (!isset($themeUrl)) {
			$themeUrl = dirname(xoops_getcss($GLOBALS['xoopsConfig']['theme_set']));
			$themePath = str_replace(XOOPS_THEME_URL, XOOPS_THEME_PATH, $themeUrl);
		}

		// Resolve the absolute path of the CSS file
		$currentPath = empty($visitedPaths) ? $themePath : dirname(end($visitedPaths));
		$absolutePath = realpath($currentPath . '/' . $cssFile);

		if (!$absolutePath || !is_readable($absolutePath) || in_array($absolutePath, $visitedPaths, true)) {
			return [];
		}

		$visitedPaths[] = $absolutePath;
		$cssContent = file_get_contents($absolutePath);
		$relativeUrl = str_replace($themePath, '', $absolutePath);
		$cssUrls = [$themeUrl . $relativeUrl];

		if (preg_match_all('~@import\s+url\((["\']?)(.*?\.css)\1\);~i', $cssContent, $matches)) {
			foreach ($matches[2] as $importedFile) {
				$importedUrls = $this->loadCss($importedFile, $visitedPaths);
				$cssUrls = array_merge($cssUrls, $importedUrls);
			}
		}

		// Return unique URLs on the final return of the recursion
		return empty($visitedPaths) ? array_unique($cssUrls) : $cssUrls;
	}

	/**
	 * Renders the TinyMCE editor initialization script.
	 *
	 * @param array $config Optional runtime configuration overrides, e.g., ['debug' => true].
	 * @return string The HTML and JavaScript code for TinyMCE initialization.
	 */
	public function render(array $config = []): string
	{
		static $isTinyMceJsLoaded = false;

		try {
			$this->init();

			if (isset($this->setting['elements']) && self::$lastOfElementsTinymce !== $this->setting['elements']) {
				return "\n<!-- TinyMCE SKIPPED: 'elements' setting has changed. -->\n";
			}
			$this->setting['elements'] = implode(',', self::$listOfElementsTinymce);

			$options = array_merge($this->setting, $config);
			$rawJsReplacements = [];

			foreach ($options as $key => $value) {
				if (is_string($value) && strpos(trim($value), 'function(') === 0) {
					$placeholder = sprintf('%%RAW_JS_%s_%s%%', $key, uniqid());
					$rawJsReplacements['"' . $placeholder . '"'] = $value;
					$options[$key] = $placeholder;
				}
			}

			$jsonOptions = json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
			if (!empty($rawJsReplacements)) {
				$jsonOptions = str_replace(array_keys($rawJsReplacements), array_values($rawJsReplacements), $jsonOptions);
			}

			$tinyMceScriptTag = "<!-- 'tinymce.min.js' already loaded -->";
			if (!$isTinyMceJsLoaded) {
				$tinyMceUrl = htmlspecialchars(XOOPS_URL . $this->rootpath . '/tinymce.min.js', ENT_QUOTES, 'UTF-8');
				$tinyMceScriptTag = "<script src='{$tinyMceUrl}'></script>";
				$isTinyMceJsLoaded = true;
			}

			$output = <<<HTML
<!-- Start TinyMCE Rendering -->
{$tinyMceScriptTag}
<script>
//<![CDATA[
document.addEventListener('DOMContentLoaded', function () {
    const config = {$jsonOptions};
    const initPromise = tinymce.init(config);

    if (config.debug) {
        initPromise.then(editors => {
            const selector = config.selector || config.elements;
            if (editors.length === 0) {
                console.warn('TinyMCE Debug: No editors were initialized for selector:', selector);
            } else if (selector && !document.querySelector(selector)) {
                console.warn('TinyMCE Debug: No DOM elements found for selector:', selector);
            } else {
                console.log('TinyMCE Debug: Initialized ' + editors.length + ' editor(s):', editors);
            }
        }).catch(error => {
            console.error('TinyMCE Initialization Error:', error);
        });
    }
});
//]]>
</script>
<!-- End TinyMCE Rendering -->
HTML;
			return $output;

		} catch (\JsonException $e) {
			error_log('Failed to render TinyMCE config: ' . $e->getMessage());
			return "<!-- TinyMCE failed to render due to a configuration error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . " -->";
		}
	}
}
