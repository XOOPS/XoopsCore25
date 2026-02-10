<?php
declare(strict_types=1);

/**
 * DebugBar Module - Preload Event Hooks
 *
 * Wires DebugbarLogger into the XOOPS 2.5.12 request lifecycle via
 * the XoopsPreload event system. Replaces 2.6's DebugbarPreload.
 *
 * Class name: DebugbarCorePreload (module=debugbar, file=core)
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Richard Griffith <richard@geekwright.com>
 * @author              trabis <lusopoemas@gmail.com>
 * @package             debugbar
 */

use XoopsModules\Debugbar\DebugbarLogger;
use XoopsModules\Debugbar\RayLogger;

/**
 * Class DebugbarCorePreload
 *
 * Event hooks for the DebugBar module. Method names map to XOOPS 2.5 events:
 *   eventCoreIncludeCommonStart      -> core.include.common.start
 *   eventCoreIncludeCommonAuthSuccess -> core.include.common.auth.success
 *   eventCoreIncludeCommonEnd        -> core.include.common.end
 *   eventCoreHeaderStart             -> core.header.start
 *   eventCoreHeaderAddmeta           -> core.header.addmeta
 *   eventCoreHeaderEnd               -> core.header.end
 *   eventCoreFooterStart             -> core.footer.start
 *   eventCoreFooterEnd               -> core.footer.end
 */
class DebugbarCorePreload extends XoopsPreloadItem
{
    /**
     * core.include.common.start — earliest event in the bootstrap.
     *
     * Register the PSR-4 autoloader, create the logger singletons,
     * enable them, and start the initial timers.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreIncludeCommonStart($args)
    {
        // Ensure XoopsLogger is loaded — this event fires before common.php loads it
        if (!class_exists('XoopsLogger', false)) {
            XoopsLoad::load('xoopslogger');
        }

        // Register PSR-4 autoloader for XoopsModules\Debugbar namespace
        require_once __DIR__ . '/autoloader.php';

        // Enable DebugbarLogger
        $logger = DebugbarLogger::getInstance();
        $logger->enable();
        $logger->startTime('XOOPS');
        $logger->startTime('XOOPS Boot');

        // Enable RayLogger (optional — silently does nothing if ray() is not available)
        $rayLogger = RayLogger::getInstance();
        $rayLogger->enable();
    }

    /**
     * core.include.common.auth.success — user is authenticated.
     *
     * Check if the current user has permission to see the debugbar.
     * Only admin users see it by default. Also check module config.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreIncludeCommonAuthSuccess($args)
    {
        $logger = DebugbarLogger::getInstance();

        // Only show debugbar to admin users
        if (empty($GLOBALS['xoopsUserIsAdmin'])) {
            $logger->disable();
            self::disableRay();
            return;
        }

        // Check if debug_mode is enabled in XOOPS config
        if (isset($GLOBALS['xoopsConfig']['debug_mode']) && 0 == $GLOBALS['xoopsConfig']['debug_mode']) {
            $logger->disable();
            self::disableRay();
            return;
        }

        // Check module config if available
        $moduleConfig = self::getModuleConfig();
        if (is_array($moduleConfig) && isset($moduleConfig['debugbar_enable']) && !$moduleConfig['debugbar_enable']) {
            $logger->disable();
            self::disableRay();
            return;
        }

        // Apply slow query threshold from module config
        if (is_array($moduleConfig) && isset($moduleConfig['slow_query_threshold'])) {
            $threshold = (float) $moduleConfig['slow_query_threshold'];
            if ($threshold > 0) {
                $logger->setSlowQueryThreshold($threshold);
                RayLogger::getInstance()->setSlowQueryThreshold($threshold);
            }
        }

        // Check Ray-specific config (disable Ray independently of DebugBar)
        if (RayLogger::getInstance()->isEnable()) {
            if (is_array($moduleConfig) && isset($moduleConfig['ray_enable']) && !$moduleConfig['ray_enable']) {
                self::disableRay();
            }
        }

        // If debugbar is active, suppress the legacy XoopsLogger HTML output
        // so we don't get double debug output
        if ($logger->isEnable()) {
            $xoopsLogger = \XoopsLogger::getInstance();
            $xoopsLogger->renderingEnabled = false;
            // Keep activated=true so data still flows to our composite loggers
        }
    }

    /**
     * core.include.common.end — bootstrap complete.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreIncludeCommonEnd($args)
    {
        $logger = DebugbarLogger::getInstance();
        $logger->stopTime('XOOPS Boot');
        $logger->startTime('Module init');
    }

    /**
     * core.header.start — header.php begins.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreHeaderStart($args)
    {
        $logger = DebugbarLogger::getInstance();
        $logger->stopTime('Module init');
        $logger->startTime('XOOPS output init');
    }

    /**
     * core.header.addmeta — theme is available.
     *
     * Assets are now rendered inline by renderDebugBar() at core.footer.end
     * for full theme-independence. This event is kept for future use.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreHeaderAddmeta($args)
    {
        // Assets rendered inline in renderDebugBar() — no theme dependency
    }

    /**
     * core.header.end — header complete.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreHeaderEnd($args)
    {
        $logger = DebugbarLogger::getInstance();
        $logger->stopTime('XOOPS output init');
        $logger->startTime('Module display');
    }

    /**
     * core.footer.start — footer begins.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreFooterStart($args)
    {
        $logger = DebugbarLogger::getInstance();
        $logger->stopTime('Module display');

        $moduleConfig = self::getModuleConfig();

        // Add Smarty variables (controlled by module config, default: on)
        $showSmarty = true;
        if (is_array($moduleConfig) && isset($moduleConfig['debug_smarty_enable'])) {
            $showSmarty = !empty($moduleConfig['debug_smarty_enable']);
        }
        if ($showSmarty) {
            $logger->addSmarty();
        }

        // Control included files tab (default: on)
        if (is_array($moduleConfig) && isset($moduleConfig['debug_files_enable'])) {
            $logger->setShowIncludedFiles(!empty($moduleConfig['debug_files_enable']));
        }
    }

    /**
     * core.footer.end — final event, render the debugbar.
     * This replaces XOOPS 2.6's core.session.shutdown.
     *
     * @param array $args event arguments
     * @return void
     */
    public static function eventCoreFooterEnd($args)
    {
        $logger = DebugbarLogger::getInstance();
        $logger->stopTime('XOOPS');
        $logger->renderDebugBar();
    }

    /**
     * Helper: disable RayLogger if active.
     *
     * @return void
     */
    private static function disableRay(): void
    {
        RayLogger::getInstance()->disable();
    }

    /**
     * Helper: get the debugbar module configuration.
     *
     * @return array|false
     */
    private static function getModuleConfig()
    {
        static $config = null;
        if (null !== $config) {
            return $config;
        }

        try {
            /** @var \XoopsConfigHandler $config_handler */
            $config_handler = xoops_getHandler('config');
            /** @var \XoopsModuleHandler $module_handler */
            $module_handler = xoops_getHandler('module');
            if ($config_handler && $module_handler) {
                $debugbarModule = $module_handler->getByDirname('debugbar');
                if (is_object($debugbarModule)) {
                    $config = $config_handler->getConfigsByCat(0, $debugbarModule->getVar('mid'));
                    return $config;
                }
            }
        } catch (\Throwable $e) {
            // Module may not be fully installed yet
        }

        $config = false;
        return $config;
    }
}
