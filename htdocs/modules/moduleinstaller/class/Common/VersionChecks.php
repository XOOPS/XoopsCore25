<?php declare(strict_types=1);

namespace XoopsModules\Moduleinstaller\Common;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use XoopsModule;

/**
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      mamba <mambax7@gmail.com>
 */
trait VersionChecks
{
    /**
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param \XoopsModule|null $module
     *
     * @param null|string $requiredVer
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerXoops(?\XoopsModule $module = null, ?string $requiredVer = null): bool
    {
        $moduleDirName      = \basename(\dirname(__DIR__, 2));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
        if (null === $module) {
            $module = XoopsModule::getByDirname($moduleDirName);
        }
        \xoops_loadLanguage('admin', $moduleDirName);
        \xoops_loadLanguage('common', $moduleDirName);

        //check for minimum XOOPS version
        $currentVer = mb_substr((string) \XOOPS_VERSION, 6); // get the numeric part of string
        if (null === $requiredVer) {
            $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        }
        $success = true;

        if ($module->versionCompare($currentVer, $requiredVer, '<')) {
            $success = false;
            $module->setErrors(\sprintf(\constant('CO_' . $moduleDirNameUpper . '_' . 'ERROR_BAD_XOOPS'), $requiredVer, $currentVer));
        }

        return $success;
    }

    /**
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param \XoopsModule|bool|null $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerPhp(?XoopsModule $module = null)
    {
        $moduleDirName      = \basename(\dirname(__DIR__, 2));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
        if (null === $module) {
            $module = XoopsModule::getByDirname($moduleDirName);
        }
        \xoops_loadLanguage('admin', $moduleDirName);
        \xoops_loadLanguage('common', $moduleDirName);

        // check for minimum PHP version
        $success = true;

        $verNum = \PHP_VERSION;
        $reqVer = &$module->getInfo('min_php');

        if (false !== $reqVer && '' !== $reqVer) {
            if ($module->versionCompare($verNum, $reqVer, '<')) {
                $module->setErrors(\sprintf(\constant('CO_' . $moduleDirNameUpper . '_' . 'ERROR_BAD_PHP'), $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }

    /**
     * compares current module version with the latest GitHub release
     * @static
     *
     * @return string|array info about the latest module version, if newer
     */
    public static function checkVerModule(\Xmf\Module\Helper $helper, ?string $source = 'github', ?string $default = 'master'): ?array
    {
        $moduleDirName      = \basename(\dirname(__DIR__, 2));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
        $module             = $helper->getModule();
        $update             = '';
        $repository         = 'XoopsModules25x/' . $moduleDirName;
        //        $repository         = 'XoopsModules25x/publisher'; //for testing only
        $ret             = null;
        $infoReleasesUrl = "https://api.github.com/repos/$repository/releases";
        if ('github' === $source) {
            if (\function_exists('curl_init') && false !== ($curlHandle = \curl_init())) {
                \curl_setopt($curlHandle, \CURLOPT_URL, $infoReleasesUrl);
                \curl_setopt($curlHandle, \CURLOPT_RETURNTRANSFER, true);
                \curl_setopt($curlHandle, \CURLOPT_SSL_VERIFYPEER, true); //TODO: how to avoid an error when 'Peer's Certificate issuer is not recognized'
                \curl_setopt($curlHandle, \CURLOPT_HTTPHEADER, ["User-Agent:Publisher\r\n"]);
                $curlReturn = \curl_exec($curlHandle);
                if (false === $curlReturn) {
                    \trigger_error(\curl_error($curlHandle));
                } elseif (false !== \mb_strpos($curlReturn, 'Not Found')) {
                    \trigger_error('Repository Not Found: ' . $infoReleasesUrl);
                } else {
                    $file              = json_decode($curlReturn, false, 512, \JSON_THROW_ON_ERROR);
                    $latestVersionLink = \sprintf("https://github.com/$repository/archive/%s.zip", $file ? \reset($file)->tag_name : $default);
                    $latestVersion     = $file[0]->tag_name;
                    $prerelease        = $file[0]->prerelease;
                    if ('master' !== $latestVersionLink) {
                        $update = \constant('CO_' . $moduleDirNameUpper . '_' . 'NEW_VERSION') . $latestVersion;
                    }
                    //"PHP-standardized" version
                    $latestVersion = \mb_strtolower((string) $latestVersion);
                    if (false !== mb_strpos($latestVersion, 'final')) {
                        $latestVersion = \str_replace('_', '', \mb_strtolower($latestVersion));
                        $latestVersion = \str_replace('final', '', \mb_strtolower($latestVersion));
                    }
                    $moduleVersion = ($helper->getModule()->getInfo('version') . '_' . $helper->getModule()->getInfo('module_status'));
                    //"PHP-standardized" version
                    $moduleVersion = \str_replace(' ', '', \mb_strtolower($moduleVersion));
                    //                    $moduleVersion = '1.0'; //for testing only
                    //                    $moduleDirName = 'publisher'; //for testing only
                    if (!$prerelease && $module->versionCompare($moduleVersion, $latestVersion, '<')) {
                        $ret   = [];
                        $ret[] = $update;
                        $ret[] = $latestVersionLink;
                    }
                }
                \curl_close($curlHandle);
            }
        }

        return $ret;
    }
}
