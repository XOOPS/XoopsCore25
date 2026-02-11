<?php
declare(strict_types=1);

namespace XoopsModules\Debugbar;

/**
 * Ray Logger for XOOPS 2.5.12
 *
 * Optional companion to DebugbarLogger. When spatie/ray is installed,
 * this logger forwards all XOOPS debug data (queries, blocks, errors,
 * deprecations, extras) to the Ray desktop app.
 *
 * If ray() is not available, this class does nothing.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             debugbar
 * @since               1.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('Restricted access');
}

use Psr\Log\LogLevel;

/**
 * RayLogger — forwards XOOPS debug data to Ray desktop app.
 *
 * Registers itself with XoopsLogger::addLogger() so it receives all
 * dispatched log entries, just like DebugbarLogger.
 *
 * Requires: composer require --dev spatie/ray
 */
class RayLogger
{
    /**
     * @var bool Whether Ray is available and enabled
     */
    private $activated = false;

    /**
     * @var int Query counter
     */
    private $queryCount = 0;

    /**
     * @var array Query tracking for duplicate detection: sql => count
     */
    private $queryMap = [];

    /**
     * @var float Slow query threshold in seconds
     */
    private $slowQueryThreshold = 0.05;

    /**
     * @var array Map of timer name => key used for Ray measure (to keep start/stop consistent)
     */
    private $timerKeys = [];

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
     * @return RayLogger
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
     * Enable the Ray logger.
     *
     * @return void
     */
    public function enable()
    {
        if (function_exists('ray')) {
            $this->activated = true;
        }
    }

    /**
     * Disable the Ray logger.
     *
     * @return void
     */
    public function disable()
    {
        $this->activated = false;
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
     * Set the slow query threshold in seconds.
     *
     * @param float $seconds
     * @return void
     */
    public function setSlowQueryThreshold($seconds)
    {
        $this->slowQueryThreshold = (float) $seconds;
    }

    /**
     * Start a timer — Ray has built-in measure support.
     *
     * @param string      $name  name of the timer
     * @param string|null $label optional label
     * @return void
     */
    public function startTime($name = 'XOOPS', $label = null)
    {
        if (!$this->activated) {
            return;
        }
        try {
            $key = $label ?: $name;
            $this->timerKeys[$name] = $key;
            ray()->measure($key);
        } catch (\Throwable $e) {
            // ignore
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
        if (!$this->activated) {
            return;
        }
        try {
            $key = isset($this->timerKeys[$name]) ? $this->timerKeys[$name] : $name;
            ray()->measure($key);
            unset($this->timerKeys[$name]);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Log an exception to Ray.
     *
     * @param \Exception|\Throwable $e
     * @return void
     */
    public function addException($e)
    {
        if (!$this->activated) {
            return;
        }
        try {
            ray()->exception($e)->color('red')->label(_MD_DEBUGBAR_RAY_EXCEPTION);
        } catch (\Throwable $ex) {
            // ignore
        }
    }

    /**
     * Suppress output (for AJAX requests).
     * Ray doesn't need this — it always sends to the desktop app.
     *
     * @return void
     */
    public function quiet()
    {
        // no-op: Ray always sends to the desktop app
    }

    /**
     * PSR-3 compatible log method — routes to Ray with colors and labels.
     *
     * @param mixed  $level   PSR-3 log level
     * @param string $message log message
     * @param array  $context context array, may include 'channel' key
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (!$this->activated) {
            return;
        }

        $channel = isset($context['channel']) ? strtolower($context['channel']) : 'messages';

        try {
            switch ($channel) {
                case 'queries':
                    $this->logQuery($level, $message, $context);
                    break;
                case 'blocks':
                    $this->logBlock($message, $context);
                    break;
                case 'deprecated':
                    ray($message)->color('orange')->label(_MD_DEBUGBAR_DEPRECATED);
                    break;
                case 'extra':
                    $name = isset($context['name']) ? $context['name'] : '';
                    ray($message)->color('gray')->label($name ?: _MD_DEBUGBAR_EXTRA);
                    break;
                default:
                    // General messages — map PSR-3 level to Ray color
                    $color = $this->levelToColor($level);
                    ray($message)->color($color)->label($this->levelToLabel($level));
                    break;
            }
        } catch (\Throwable $e) {
            // Silently ignore Ray errors
        }
    }

    /**
     * Log a database query with duplicate detection and slow highlighting.
     *
     * @param string $level
     * @param string $message SQL query
     * @param array  $context
     * @return void
     */
    private function logQuery($level, $message, array $context)
    {
        $queryTime = !empty($context['query_time']) ? (float) $context['query_time'] : 0.0;

        // Track duplicates
        $this->queryCount++;
        $sqlKey = trim($message);
        if (!isset($this->queryMap[$sqlKey])) {
            $this->queryMap[$sqlKey] = 0;
        }
        $this->queryMap[$sqlKey]++;
        $isDuplicate = ($this->queryMap[$sqlKey] > 1);

        // Build display
        $timeStr = $queryTime > 0 ? sprintf('%.2fms', $queryTime * 1000) : '';
        $label = sprintf(_MD_DEBUGBAR_RAY_QUERY, $this->queryCount);

        if ($isDuplicate) {
            $label .= sprintf(_MD_DEBUGBAR_RAY_DUP, $this->queryMap[$sqlKey]);
        }
        if ($timeStr) {
            $label .= ' (' . $timeStr . ')';
        }

        // Determine color
        if ($level === LogLevel::ERROR) {
            $color = 'red';
        } elseif ($queryTime > 0 && $queryTime >= $this->slowQueryThreshold) {
            $color = 'red';
            $label .= _MD_DEBUGBAR_RAY_SLOW;
        } elseif ($isDuplicate) {
            $color = 'orange';
        } else {
            $color = 'purple';
        }

        // Handle query errors
        $msg = $message;
        if ($level === LogLevel::ERROR) {
            $errno = isset($context['errno']) && is_scalar($context['errno']) ? $context['errno'] : '?';
            $error = isset($context['error']) && is_scalar($context['error']) ? $context['error'] : '?';
            $msg .= sprintf(_MD_DEBUGBAR_QUERY_ERROR_RAY, $errno, $error);
        }

        ray($msg)->color($color)->label($label);
    }

    /**
     * Log a block rendering event.
     *
     * @param string $message block name
     * @param array  $context
     * @return void
     */
    private function logBlock($message, array $context)
    {
        $cached = !empty($context['cached']);
        $cacheTime = (int) ($context['cachetime'] ?? 0);

        $label = $cached
            ? sprintf(_MD_DEBUGBAR_RAY_BLOCK_CACHED, $cacheTime)
            : _MD_DEBUGBAR_RAY_BLOCK_NOT_CACHED;
        $color = $cached ? 'green' : 'blue';

        ray($message)->color($color)->label($label);
    }

    /**
     * Map PSR-3 log level to Ray color.
     *
     * @param string $level
     * @return string
     */
    private function levelToColor($level)
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                return 'red';
            case LogLevel::WARNING:
                return 'orange';
            case LogLevel::NOTICE:
                return 'green';
            case LogLevel::INFO:
                return 'blue';
            case LogLevel::DEBUG:
            default:
                return 'gray';
        }
    }

    /**
     * Map PSR-3 log level to Ray label.
     *
     * @param string $level
     * @return string
     */
    private function levelToLabel($level)
    {
        switch ($level) {
            case LogLevel::EMERGENCY: return 'EMERGENCY';
            case LogLevel::ALERT:     return 'ALERT';
            case LogLevel::CRITICAL:  return 'CRITICAL';
            case LogLevel::ERROR:     return 'Error';
            case LogLevel::WARNING:   return 'Warning';
            case LogLevel::NOTICE:    return 'Notice';
            case LogLevel::INFO:      return 'Info';
            case LogLevel::DEBUG:
            default:                  return 'Debug';
        }
    }
}
