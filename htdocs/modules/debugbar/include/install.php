<?php
/**
 * DebugBar Module - Install/Update callbacks
 *
 * Copies DebugBar vendor assets to web-accessible module directory.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             debugbar
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('Restricted access');
}

/**
 * Copy DebugBar vendor assets to modules/debugbar/assets/ for web access.
 *
 * @param XoopsModule $module
 * @return bool
 */
function xoops_module_install_debugbar($module)
{
    return _debugbar_copy_assets();
}

/**
 * Copy assets on module update too.
 *
 * @param XoopsModule $module
 * @param int $previousVersion
 * @return bool
 */
function xoops_module_update_debugbar($module, $previousVersion)
{
    return _debugbar_copy_assets();
}

/**
 * Internal: copy vendor debugbar Resources to module assets directory.
 *
 * @return bool
 */
function _debugbar_copy_assets()
{
    // Source: vendor debugbar resources
    $vendorBase  = XOOPS_ROOT_PATH . '/class/libraries/vendor';
    $vendorPaths = [
        $vendorBase . '/maximebf/debugbar/src/DebugBar/Resources',
        $vendorBase . '/php-debugbar/php-debugbar/src/DebugBar/Resources',
    ];

    $srcDir = false;
    foreach ($vendorPaths as $path) {
        if (is_dir($path)) {
            $srcDir = $path;
            break;
        }
    }

    if (!$srcDir) {
        // Assets not found in vendor — they may be loaded differently
        return true;
    }

    // Destination: modules/debugbar/assets/
    $destDir = XOOPS_ROOT_PATH . '/modules/debugbar/assets';
    if (!is_dir($destDir)) {
        if (!mkdir($destDir, 0755, true) && !is_dir($destDir)) {
            throw new \RuntimeException(sprintf('Directory "modules/debugbar/%s" was not created', basename($destDir)));
        }
    }

    // Recursive copy
    return _debugbar_recursive_copy($srcDir, $destDir);
}

/**
 * Recursively copy a directory.
 *
 * @param string $src  source directory
 * @param string $dest destination directory
 * @return bool
 */
function _debugbar_recursive_copy($src, $dest)
{
    if (!is_dir($src)) {
        return false;
    }
    if (!is_dir($dest)) {
        if (!mkdir($dest, 0755, true) && !is_dir($dest)) {
            throw new \RuntimeException(sprintf('Failed to create directory "%s" during asset copy', basename($dest)));
        }
    }

    $dir = opendir($src);
    if (!$dir) {
        return false;
    }

    $success = true;
    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $srcPath  = $src . '/' . $file;
        $destPath = $dest . '/' . $file;
        if (is_dir($srcPath)) {
            if (!_debugbar_recursive_copy($srcPath, $destPath)) {
                $success = false;
            }
        } else {
            if (!copy($srcPath, $destPath)) {
                $success = false;
            }
        }
    }
    closedir($dir);

    return $success;
}
