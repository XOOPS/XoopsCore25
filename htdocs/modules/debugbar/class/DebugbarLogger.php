<?php

declare(strict_types=1);

namespace XoopsModules\Debugbar;

/**
 * DebugBar Logger for XOOPS 2.5.12
 *
 * Collects log information and presents to PHP DebugBar for display.
 * Records information about database queries, blocks, execution time, and various logs.
 *
 * Ported from XOOPS 2.6.0 modules/debugbar/class/debugbarlogger.php
 * Adapted for: maximebf/debugbar v1.x API, PSR-3 v1, no namespaces, XOOPS 2.5 preload system.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Richard Griffith <richard@geekwright.com>
 * @author              trabis <lusopoemas@gmail.com>
 * @package             debugbar
 * @since               1.0
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

use DebugBar\StandardDebugBar;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\ConfigCollector;
use Psr\Log\LogLevel;

/**
 * DebugbarLogger — collects XOOPS debug data and renders via PHP DebugBar.
 *
 * Registers itself with XoopsLogger::addLogger() so it receives all
 * dispatched log entries (queries, blocks, errors, deprecations, extras).
 */
class DebugbarLogger
{
    /**
     * @var StandardDebugBar|bool
     */
    private $debugbar = false;

    /**
     * @var \DebugBar\JavascriptRenderer|bool
     */
    private $renderer = false;

    /**
     * @var bool Whether the debugbar is activated
     */
    private $activated = false;

    /**
     * @var bool Whether rendering is enabled
     */
    private $renderingEnabled = false;

    /**
     * @var bool Quiet mode (suppress output for AJAX)
     */
    private $quietmode = false;

    /**
     * @var bool Whether CSS/JS assets have been added to the theme
     */
    private $assetsAdded = false;

    /**
     * @var array Query tracking for duplicate detection: sql => count
     */
    private $queryMap = [];

    /**
     * @var int Total query count
     */
    private $queryCount = 0;

    /**
     * @var int Duplicate query count
     */
    private $duplicateCount = 0;

    /**
     * @var float Slow query threshold in seconds (default: 0.05 = 50ms)
     */
    private $slowQueryThreshold = 0.05;

    /**
     * @var bool Whether to show the included files tab
     */
    private $showIncludedFiles = true;

    /**
     * Constructor — registers this logger with XoopsLogger composite.
     */
    public function __construct()
    {
        $xoopsLogger = \XoopsLogger::getInstance();
        $xoopsLogger->addLogger($this);
    }

    /**
     * Singleton accessor.
     *
     * @return DebugbarLogger
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Get the underlying DebugBar instance.
     *
     * @return StandardDebugBar|bool
     */
    public function getDebugbar()
    {
        return $this->debugbar;
    }

    /**
     * Get the JavaScript renderer.
     *
     * @return \DebugBar\JavascriptRenderer|bool
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Disable the debugbar.
     *
     * @return void
     */
    public function disable()
    {
        $this->activated = false;
    }

    /**
     * Enable the debugbar — creates StandardDebugBar and adds custom collectors.
     *
     * @return void
     */
    public function enable()
    {
        $this->renderingEnabled = true;

        if (!$this->debugbar) {
            if (!class_exists('DebugBar\StandardDebugBar')) {
                return;
            }
            try {
                $this->debugbar = new StandardDebugBar();
                $this->renderer = $this->debugbar->getJavascriptRenderer();

                // Add custom collectors for XOOPS channels
                $this->debugbar->addCollector(new MessagesCollector('Deprecated'));
                $this->debugbar->addCollector(new MessagesCollector('Blocks'));
                $this->debugbar->addCollector(new MessagesCollector('Extra'));
                $this->debugbar->addCollector(new MessagesCollector('Queries'));

                // v1.x: disable jQuery (already loaded by XOOPS) and noConflict wrapping
                if (method_exists($this->renderer, 'disableVendor')) {
                    $this->renderer->disableVendor('jquery');
                }
                if (method_exists($this->renderer, 'setEnableJqueryNoConflict')) {
                    $this->renderer->setEnableJqueryNoConflict(false);
                }

                // Set the base path and URL for debugbar assets
                $assetsDir = XOOPS_ROOT_PATH . '/modules/debugbar/assets';
                if (is_dir($assetsDir)) {
                    $this->renderer->setBasePath($assetsDir);
                    $this->renderer->setBaseUrl(XOOPS_URL . '/modules/debugbar/assets');
                }
            } catch (\Throwable $e) {
                $this->debugbar = false;
                return;
            }
        }

        $this->activated = true;
    }

    /**
     * Report enabled status.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->activated;
    }

    /**
     * Suppress output (for AJAX requests).
     *
     * @return void
     */
    public function quiet()
    {
        $this->quietmode = true;
    }

    /**
     * Inject DebugBar CSS/JS assets into the XOOPS theme.
     *
     * Called from preload at core.header.addmeta when $xoTheme is available.
     * Assets added here appear in the <head> via <{$xoops_module_header}>.
     * As a fallback, renderDebugBar() also outputs assets inline if needed.
     *
     * @return void
     */
    public function addToTheme()
    {
        if (!$this->activated || $this->assetsAdded || !is_object($this->debugbar)) {
            return;
        }

        if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
            return;
        }

        $this->renderer->setIncludeVendors(true);

        // maximebf/debugbar v1.x: getAssets('css') / getAssets('js') return filesystem paths
        $cssAssets = $this->renderer->getAssets('css');
        $jsAssets  = $this->renderer->getAssets('js');

        // Build path-to-URL converter using the basePath and baseUrl
        $basePath = XOOPS_ROOT_PATH . '/modules/debugbar/assets';
        $baseUrl  = XOOPS_URL . '/modules/debugbar/assets';

        $toUrl = function ($filePath) use ($basePath, $baseUrl) {
            // Normalize directory separators
            $filePath = str_replace('\\', '/', $filePath);
            $normBase = str_replace('\\', '/', $basePath);
            if (strpos($filePath, $normBase) === 0) {
                return $baseUrl . substr($filePath, strlen($normBase));
            }
            // Asset not under expected base path — return empty to avoid leaking filesystem paths
            return '';
        };

        // Exclude jQuery (already loaded by XOOPS)
        $filterFn = function ($filename) {
            return false === strpos(str_replace('\\', '/', $filename), '/vendor/jquery/');
        };

        $cssAssets = array_filter($cssAssets, $filterFn);
        $jsAssets  = array_filter($jsAssets, $filterFn);

        foreach ($cssAssets as $css) {
            $url = $toUrl($css);
            if ($url !== '') {
                $GLOBALS['xoTheme']->addStylesheet($url);
            }
        }
        foreach ($jsAssets as $js) {
            $url = $toUrl($js);
            if ($url !== '') {
                $GLOBALS['xoTheme']->addScript($url);
            }
        }

        // Add XOOPS custom settings widget (provides settings gear icon, themes, position)
        $xoopsAssetsUrl = XOOPS_URL . '/modules/debugbar/assets';
        $GLOBALS['xoTheme']->addStylesheet($xoopsAssetsUrl . '/xoops-debugbar-settings.css');
        $GLOBALS['xoTheme']->addScript($xoopsAssetsUrl . '/xoops-debugbar-settings.js');

        $this->assetsAdded = true;
    }

    /**
     * Start a timer.
     *
     * @param string      $name  name of the timer
     * @param string|null $label optional label
     * @return void
     */
    public function startTime($name = 'XOOPS', $label = null)
    {
        if ($this->activated && is_object($this->debugbar)) {
            try {
                $this->debugbar['time']->startMeasure($name, $label);
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    /**
     * Stop a timer.
     *
     * @param string $name name of the timer
     * @return void
     */
    public function stopTime($name = 'XOOPS')
    {
        if ($this->activated && is_object($this->debugbar)) {
            try {
                $this->debugbar['time']->stopMeasure($name);
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    /**
     * Log an exception to the exceptions collector.
     *
     * @param \Exception|\Throwable $e
     * @return void
     */
    public function addException($e)
    {
        if ($this->activated && is_object($this->debugbar)) {
            try {
                // v1.x uses addException(); v3.3 uses addThrowable()
                if (method_exists($this->debugbar['exceptions'], 'addThrowable')) {
                    $this->debugbar['exceptions']->addThrowable($e);
                } else {
                    $this->debugbar['exceptions']->addException($e);
                }
            } catch (\Throwable $ex) {
                // ignore
            }
        }
    }

    /**
     * Dump Smarty template variables into a ConfigCollector.
     *
     * @return void
     */
    public function addSmarty()
    {
        if (!$this->activated || !is_object($this->debugbar)) {
            return;
        }
        if (!isset($GLOBALS['xoopsTpl']) || !is_object($GLOBALS['xoopsTpl'])) {
            return;
        }

        $data = $GLOBALS['xoopsTpl']->getTemplateVars();
        if (!is_array($data)) {
            return;
        }

        // Normalize values for display
        foreach ($data as $k => $v) {
            if ($v === '') {
                $data[$k] = _MD_DEBUGBAR_EMPTY_STRING;
            } elseif ($v === null) {
                $data[$k] = _MD_DEBUGBAR_NULL;
            } elseif ($v === true) {
                $data[$k] = _MD_DEBUGBAR_BOOL_TRUE;
            } elseif ($v === false) {
                $data[$k] = _MD_DEBUGBAR_BOOL_FALSE;
            } elseif (is_array($v) || is_object($v)) {
                $data[$k] = print_r($v, true);
            }
        }
        ksort($data, SORT_NATURAL | SORT_FLAG_CASE);

        if (class_exists('DebugBar\DataCollector\ConfigCollector')) {
            $this->debugbar->addCollector(
                new \DebugBar\DataCollector\ConfigCollector($data, 'Smarty')
            );
        }
    }

    /**
     * Enable or disable the included files tab.
     *
     * @param bool $show
     * @return void
     */
    public function setShowIncludedFiles($show)
    {
        $this->showIncludedFiles = (bool) $show;
    }

    /**
     * Set the slow query threshold in seconds.
     *
     * @param float $seconds threshold in seconds (e.g. 0.05 for 50ms)
     * @return void
     */
    public function setSlowQueryThreshold($seconds)
    {
        $this->slowQueryThreshold = (float) $seconds;
    }

    /**
     * Add included files list to a ConfigCollector tab.
     *
     * @return void
     */
    public function addIncludedFiles()
    {
        if (!$this->activated || !is_object($this->debugbar)) {
            return;
        }

        $files = get_included_files();
        $data = [];
        $rootPath = str_replace('\\', '/', XOOPS_ROOT_PATH);

        foreach ($files as $i => $file) {
            // Show paths relative to XOOPS_ROOT_PATH for readability
            $file = str_replace('\\', '/', $file);
            if (strpos($file, $rootPath) === 0) {
                $display = substr($file, strlen($rootPath));
            } else {
                $display = $file;
            }
            $data[(string)($i + 1)] = $display;
        }

        if (class_exists('DebugBar\DataCollector\ConfigCollector')) {
            $this->debugbar->addCollector(
                new ConfigCollector($data, 'Files (' . count($files) . ')')
            );
        }
    }

    /**
     * Stack data before a redirect (preserve debug info across redirects).
     *
     * @return void
     */
    public function stackData()
    {
        if ($this->activated && is_object($this->debugbar)) {
            $this->debugbar->stackData();
            $this->activated = false;
            $this->renderingEnabled = false;
        }
    }

    /**
     * Final render — called at core.footer.end to output the debugbar.
     * This replaces 2.6's core.session.shutdown event.
     *
     * @return void
     */
    public function renderDebugBar()
    {
        if (!$this->activated || !is_object($this->debugbar)) {
            return;
        }

        // Add final extra info
        $this->log(LogLevel::INFO, PHP_VERSION, ['channel' => 'Extra', 'name' => _MD_DEBUGBAR_PHP_VERSION]);
        $this->log(LogLevel::INFO, (string) count(get_included_files()), ['channel' => 'Extra', 'name' => _MD_DEBUGBAR_INCLUDED_FILES]);

        // Add database info if available
        try {
            $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
            if (is_object($xoopsDB) && method_exists($xoopsDB, 'conn') && $xoopsDB->conn instanceof \PDO) {
                $this->log(LogLevel::INFO, $xoopsDB->conn->getAttribute(\PDO::ATTR_SERVER_VERSION), [
                    'channel' => 'Extra',
                    'name'    => sprintf(_MD_DEBUGBAR_DB_VERSION, $xoopsDB->conn->getAttribute(\PDO::ATTR_DRIVER_NAME)),
                ]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Add query summary to Extra
        if ($this->queryCount > 0) {
            $querySummary = sprintf(_MD_DEBUGBAR_QUERY_SUMMARY, $this->queryCount);
            if ($this->duplicateCount > 0) {
                $querySummary .= sprintf(_MD_DEBUGBAR_QUERY_DUPLICATES, $this->duplicateCount);
            }
            $this->log(LogLevel::INFO, $querySummary, [
                'channel' => 'Extra',
                'name'    => _MD_DEBUGBAR_DATABASE_QUERIES,
            ]);
        }

        // Add memory usage
        $this->log(LogLevel::INFO, sprintf(_MD_DEBUGBAR_BYTES, memory_get_usage()), [
            'channel' => 'Extra',
            'name'    => _MD_DEBUGBAR_MEMORY_USAGE,
        ]);

        // Add included files tab (configurable)
        if ($this->showIncludedFiles) {
            $this->addIncludedFiles();
        }

        if (false === $this->quietmode) {
            $isAjax = \Xmf\Request::getHeader('X-Requested-With') === 'XMLHttpRequest';

            if ($isAjax) {
                // AJAX: add dataset without new toolbar initialization
                echo $this->renderer->render(false);
            } else {
                // Full page: output CSS/JS assets + initialization + data
                // Always render assets inline here for theme-independence.
                // This works across ALL themes without requiring <{$xoops_module_header}>.
                $this->renderer->setIncludeVendors(true);
                echo $this->renderer->renderHead();

                // Load XOOPS custom settings CSS
                $xoopsAssetsUrl = XOOPS_URL . '/modules/debugbar/assets';
                echo '<link rel="stylesheet" type="text/css" href="'
                    . $xoopsAssetsUrl . '/xoops-debugbar-settings.css">' . "\n";

                // Render debugbar initialization and data
                echo $this->renderer->render();

                // Load the settings widget JS as an external script (cacheable by browser)
                // followed by a small inline init call.
                echo '<script type="text/javascript" src="'
                    . $xoopsAssetsUrl . '/xoops-debugbar-settings.js"></script>' . "\n";
                echo '<script type="text/javascript">' . "\n";
                echo 'if (typeof phpdebugbar !== "undefined" && typeof phpdebugbar._initSettings === "function") {' . "\n";
                echo '  try { phpdebugbar._initSettings(); } catch(e) {}' . "\n";
                echo '}' . "\n";
                echo '</script>' . "\n";
            }
        } else {
            $this->debugbar->sendDataInHeaders();
        }
    }

    /**
     * PSR-3 v1 compatible log method (untyped for PHP 7.4 compat).
     *
     * Routes messages to the appropriate DebugBar collector based on
     * the 'channel' key in the context array.
     *
     * @param mixed  $level   PSR-3 log level
     * @param string $message log message
     * @param array  $context context array, may include 'channel' key
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (!$this->activated || !is_object($this->debugbar)) {
            return;
        }

        $channel = 'messages';
        $msg = $message;

        // Route to appropriate collector based on channel
        if (isset($context['channel'])) {
            $chan = strtolower($context['channel']);
            switch ($chan) {
                case 'blocks':
                    $channel = 'Blocks';
                    $msg = $message . ': ';
                    if (!empty($context['cached'])) {
                        $msg .= sprintf(_MD_DEBUGBAR_CACHED, (int) ($context['cachetime'] ?? 0));
                    } else {
                        $msg .= _MD_DEBUGBAR_NOT_CACHED;
                    }
                    break;
                case 'deprecated':
                    $channel = 'Deprecated';
                    $msg = $message;
                    break;
                case 'extra':
                    $channel = 'Extra';
                    $name = isset($context['name']) ? $context['name'] : '';
                    $msg = $name ? ($name . ': ' . $message) : $message;
                    break;
                case 'queries':
                    $channel = 'Queries';
                    $queryTime = !empty($context['query_time']) ? (float) $context['query_time'] : 0.0;
                    $qt = $queryTime > 0 ? sprintf('%0.6f', $queryTime) : '';

                    // Track duplicates
                    $this->queryCount++;
                    $sqlKey = trim($message);
                    if (!isset($this->queryMap[$sqlKey])) {
                        $this->queryMap[$sqlKey] = 0;
                    }
                    $this->queryMap[$sqlKey]++;
                    $isDuplicate = ($this->queryMap[$sqlKey] > 1);
                    if ($isDuplicate) {
                        $this->duplicateCount++;
                    }

                    // Build formatted message
                    if ($level === LogLevel::ERROR) {
                        $errno = isset($context['errno']) && is_scalar($context['errno']) ? $context['errno'] : '?';
                        $error = isset($context['error']) && is_scalar($context['error']) ? $context['error'] : '?';
                        $msg .= sprintf(_MD_DEBUGBAR_QUERY_ERROR, $errno, $error);
                    }

                    // Prefix with timing
                    $msg = ($qt ? $qt . 's - ' : '') . $msg;

                    // Add duplicate indicator
                    if ($isDuplicate) {
                        $msg = '[DUP×' . $this->queryMap[$sqlKey] . '] ' . $msg;
                    }

                    // Override level for slow/duplicate queries to get color labels
                    if ($level !== LogLevel::ERROR) {
                        if ($queryTime > 0 && $queryTime >= $this->slowQueryThreshold) {
                            $level = LogLevel::ERROR;    // red — slow query
                        } elseif ($isDuplicate) {
                            $level = LogLevel::WARNING;  // yellow — duplicate
                        }
                    }

                    break;
                default:
                    $channel = 'messages';
                    break;
            }
        }

        // Fall back to 'messages' if collector doesn't exist
        if (!$this->debugbar->hasCollector($channel)) {
            $channel = 'messages';
        }

        // Dispatch to the collector by PSR-3 level
        try {
            switch ($level) {
                case LogLevel::EMERGENCY:
                    $this->debugbar[$channel]->emergency($msg);
                    break;
                case LogLevel::ALERT:
                    $this->debugbar[$channel]->alert($msg);
                    break;
                case LogLevel::CRITICAL:
                    $this->debugbar[$channel]->critical($msg);
                    break;
                case LogLevel::ERROR:
                    $this->debugbar[$channel]->error($msg);
                    break;
                case LogLevel::WARNING:
                    $this->debugbar[$channel]->warning($msg);
                    break;
                case LogLevel::NOTICE:
                    $this->debugbar[$channel]->notice($msg);
                    break;
                case LogLevel::INFO:
                    $this->debugbar[$channel]->info($msg);
                    break;
                case LogLevel::DEBUG:
                default:
                    $this->debugbar[$channel]->debug($msg);
                    break;
            }
        } catch (\Throwable $e) {
            // Silently ignore collector errors
        }
    }
}
