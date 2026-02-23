<?php
/**
 * XOOPS Monolog Logger Adapter
 *
 * Wraps Monolog 2.x as a PSR-3 compatible logger that integrates with
 * XoopsLogger via the addLogger() composite pattern (ported from XOOPS 2.6).
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          logger
 * @since               2.5.12
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Psr\Log\LogLevel;

/**
 * Monolog adapter for XoopsLogger composite pattern.
 *
 * Usage:
 *   $monolog = new XoopsMonologLogger('xoops');
 *   XoopsLogger::getInstance()->addLogger($monolog);
 *
 * @package kernel
 */
class XoopsMonologLogger
{
    /**
     * @var Logger|null Monolog logger instance
     */
    private $monolog;

    /**
     * @var bool Whether this logger is active
     */
    private $activated = true;

    /**
     * Constructor — creates a Monolog logger with sensible defaults.
     *
     * @param string $channelName  Monolog channel name (default: 'xoops')
     * @param array  $handlers     Optional array of Monolog handlers; if empty, defaults to a RotatingFileHandler
     * @param array  $processors   Optional array of Monolog processors
     */
    public function __construct($channelName = 'xoops', array $handlers = [], array $processors = [])
    {
        if (!class_exists('Monolog\Logger')) {
            $this->activated = false;
            return;
        }

        try {
            $this->monolog = new Logger($channelName);

            if (empty($handlers)) {
                // Default: rotating file handler in XOOPS_VAR_PATH/logs/
                // Require XOOPS_VAR_PATH (outside webroot); never fall back
                // to XOOPS_ROOT_PATH which would expose logs publicly.
                if (!defined('XOOPS_VAR_PATH')) {
                    $this->activated = false;
                    return;
                }
                $logDir = XOOPS_VAR_PATH . '/logs';
                if (!is_dir($logDir)) {
                    if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                        $this->activated = false;
                        return;
                    }
                }
                $logFile = $logDir . '/xoops.log';
                $handler = new RotatingFileHandler($logFile, 30, Logger::DEBUG);
                $this->monolog->pushHandler($handler);
            } else {
                foreach ($handlers as $handler) {
                    $this->monolog->pushHandler($handler);
                }
            }

            foreach ($processors as $processor) {
                $this->monolog->pushProcessor($processor);
            }
        } catch (\Throwable $e) {
            $this->activated = false;
        }
    }

    /**
     * PSR-3 compatible log method (v1 untyped signature for PHP 7.4 compat).
     *
     * @param mixed  $level    PSR-3 log level string or Monolog integer level
     * @param string $message  log message
     * @param array  $context  context array
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (!$this->activated || null === $this->monolog) {
            return;
        }

        // Strip the 'channel' key used for DebugBar routing — not relevant for file logging
        $logContext = $context;
        unset($logContext['channel']);

        try {
            $this->monolog->log($this->normalizeLevel($level), (string) $message, $logContext);
        } catch (\Throwable $e) {
            // Silently ignore to prevent cascading failures
        }
    }

    /**
     * Quiet mode — no-op for file-based logger (nothing to suppress).
     *
     * @return void
     */
    public function quiet()
    {
        // File logger has no page output to suppress
    }

    /**
     * Get the underlying Monolog Logger instance for advanced configuration.
     *
     * @return Logger|null
     */
    public function getMonolog()
    {
        return $this->monolog;
    }

    /**
     * Normalize a PSR-3 string level to a Monolog integer level.
     *
     * @param mixed $level  PSR-3 level string or integer
     * @return int  Monolog level constant
     */
    private function normalizeLevel($level)
    {
        if (is_int($level)) {
            return $level;
        }

        $map = [
            LogLevel::EMERGENCY => Logger::EMERGENCY,
            LogLevel::ALERT     => Logger::ALERT,
            LogLevel::CRITICAL  => Logger::CRITICAL,
            LogLevel::ERROR     => Logger::ERROR,
            LogLevel::WARNING   => Logger::WARNING,
            LogLevel::NOTICE    => Logger::NOTICE,
            LogLevel::INFO      => Logger::INFO,
            LogLevel::DEBUG     => Logger::DEBUG,
        ];

        return isset($map[$level]) ? $map[$level] : Logger::DEBUG;
    }
}
