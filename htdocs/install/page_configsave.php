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
 * Installer mainfile creation page
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

require_once __DIR__ . '/include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

$pageHasForm = false;
$pageHasHelp = false;

$vars = & $_SESSION['settings'];

if (empty($vars['ROOT_PATH'])) {
    $wizard->redirectToPage('pathsettings');
    exit();
} elseif (empty($vars['DB_HOST'])) {
    $wizard->redirectToPage('dbsettings');
    exit();
}

$writeFiles = [
    $vars['ROOT_PATH'] . '/mainfile.php',
    $vars['VAR_PATH'] . '/data/secure.php',
];

$writeCheck = checkFileWriteablity($writeFiles);
if (true === $writeCheck) {
    $rewrite = [
        'GROUP_ADMIN' => 1,
        'GROUP_USERS' => 2,
        'GROUP_ANONYMOUS' => 3,
    ];
    $rewrite = array_merge($rewrite, $vars);

    $result = writeConfigurationFile($rewrite, $vars['VAR_PATH'] . '/data', 'secure.dist.php', 'secure.php');
    $GLOBALS['error'] = ($result !== true);
    if ($result === true) {
        $result = copyConfigDistFiles($vars);
        $GLOBALS['error'] = ($result !== true);
    }
    if ($result === true) {
        $result = writeConfigurationFile($rewrite, $vars['ROOT_PATH'], 'mainfile.dist.php', 'mainfile.php');
        $GLOBALS['error'] = ($result !== true);
    }

    $_SESSION['settings']['authorized'] = false;

    if ($result === true) {
        $_SESSION['UserLogin'] = true;
        $_SESSION['settings']['authorized'] = true;
        ob_start();
        ?>

        <div class="alert alert-success"><span class="fa-solid fa-check text-success"></span> <?php echo SAVED_MAINFILE; ?></div>
        <div class='well'><?php echo SAVED_MAINFILE_MSG; ?>
        <ul class='diags'>
            <?php
            foreach ($vars as $k => $v) {
                if ($k === 'authorized') {
                    continue;
                }
                echo "<li><strong>XOOPS_{$k}</strong> " . IS_VALOR . " {$v}</li>";
            }
        ?>
        </ul>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
    } else {
        $GLOBALS['error'] = true;
        $pageHasForm = true; // will redirect to same page
        $content = '<div class="alert alert-danger"><span class="fa-solid fa-ban text-danger"></span> ' . $result . '</div>';
    }
} else {
    $content = '';
    foreach ($writeCheck as $errorMsg) {
        $GLOBALS['error'] = true;
        $pageHasForm = true; // will redirect to same page
        $content .= '<div class="alert alert-danger"><span class="fa-solid fa-ban text-danger"></span> ' . $errorMsg . '</div>' . "\n";
    }
}
include __DIR__ . '/include/install_tpl.php';

/**
 * Copy a configuration file from template, then rewrite with actual configuration values
 *
 * @param string[] $vars       config values
 * @param string   $path       directory path where files reside
 * @param string   $sourceName template file name
 * @param string   $fileName   configuration file name
 *
 * @return true|string true on success, error message on failure
 */
function writeConfigurationFile($vars, $path, $sourceName, $fileName)
{
    $path .= '/';
    if (!@copy($path . $sourceName, $path . $fileName)) {
        return sprintf(ERR_COPY_MAINFILE, $fileName);
    }

    clearstatcache();
    if (!$file = fopen($path . $fileName, 'r')) {
        return sprintf(ERR_READ_MAINFILE, $fileName);
    }

    $content = fread($file, filesize($path . $fileName));
    fclose($file);

    // First, update the XOOPS_PROT detection code
    $protDetection = <<<'EOD'
    // Protocol detection for SSL and proxy compatibility
    $IS_HTTPS = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
        || (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && (int)$_SERVER['HTTP_X_FORWARDED_PORT'] === 443)
        || (isset($_SERVER['REDIRECT_HTTPS']) && $_SERVER['REDIRECT_HTTPS'] === 'on');
    
    define('XOOPS_PROT', $IS_HTTPS ? 'https://' : 'http://');
    unset($IS_HTTPS);
EOD;

    // Replace the old XOOPS_PROT detection code
    $content = preg_replace(
        '/\/\/ URL Association for SSL.*?define\(\'XOOPS_PROT\',.*?\);/s',
        $protDetection,
        $content
    );

    // Then handle the rest of the configuration variables
    foreach ($vars as $key => $val) {
        if ($key === 'XOOPS_URL') {
            $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", "define('XOOPS_{$key}', XOOPS_PROT . {$val})", $content );
            continue;
        }
        if (is_int($val) && preg_match("/(define\()([\"'])(XOOPS_{$key})\\2,\s*(\d+)\s*\)/", $content)) {
            $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*(\d+)\s*\)/", "define('XOOPS_{$key}', {$val})", $content);
        } elseif (preg_match("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", $content)) {
            $val = str_replace('$', '\$', addslashes($val));
            $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", "define('XOOPS_{$key}', '{$val}')", $content);
        }
    }

    $file = fopen($path . $fileName, 'w');
    if (false === $file) {
        return sprintf(ERR_WRITE_MAINFILE, $fileName);
    }
    $writeResult = fwrite($file, $content);
    fclose($file);
    if (false === $writeResult) {
        return sprintf(ERR_WRITE_MAINFILE, $fileName);
    }

    return true;
}


/**
 * Get file stats
 *
 * @param string $filename file or directory name
 *
 * @return array|false false on error, or array of file stat information
 */
function getStats($filename)
{
    $stat = stat($filename);
    if (false === $stat) {
        return false;
    }
    return prepStats($stat);
}

/**
 * Get file stats on a created temp file
 *
 * @return array|false false on error, or array of file stat information
 */
function getTmpStats()
{
    $temp = tmpfile();
    if (false === $temp) {
        return false;
    }
    $stat = fstat($temp);
    fclose($temp);
    if (false === $stat) {
        return false;
    }
    return prepStats($stat);
}

/**
 * Get stat() info in a more usable form
 *
 * @param array $stat return from PHP stat()
 *
 * @return array selected information gleaned from $stat
 */
function prepStats($stat)
{
    $subSet = [];
    $mode = $stat['mode'];
    $subSet['mode'] = $mode;
    $subSet['uid'] = $stat['uid'];
    $subSet['gid'] = $stat['gid'];

    $subSet['user']['read']   = (bool) ($mode & 0400);
    $subSet['user']['write']  = (bool) ($mode & 0200);
    $subSet['user']['exec']   = (bool) ($mode & 0100);
    $subSet['group']['read']  = (bool) ($mode & 040);
    $subSet['group']['write'] = (bool) ($mode & 020);
    $subSet['group']['exec']  = (bool) ($mode & 010);
    $subSet['other']['read']  = (bool) ($mode & 04);
    $subSet['other']['write'] = (bool) ($mode & 02);
    $subSet['other']['exec']  = (bool) ($mode & 01);

    return $subSet;
}

/**
 * Attempt to check if a set of files can be written
 *
 * @param string[] $files fully qualified file names to check
 *
 * @return string[]|true true if no issues found, array
 */
function checkFileWriteablity($files)
{
    if (isset($_POST['op']) && $_POST['op'] === 'proceed') {
        return true; // user said skip this
    }
    $tmpStats = getTmpStats();
    if (false === $tmpStats) {
        return true; // tests are not applicable
    }

    $message = [];

    foreach ($files as $file) {
        $dirName = dirname($file);
        $fileName = basename($file);
        $dirStat = getStats($dirName);
        if (false !== $dirStat) {
            $uid = $tmpStats['uid'];
            $gid = $tmpStats['gid'];
            if (!(
                (false !== stripos(PHP_OS, 'WIN'))
                || ($uid === $dirStat['uid'] && $dirStat['user']['write'])
                || ($gid === $dirStat['gid'] && $dirStat['group']['write'])
                || (file_exists($file) && is_writable($file))
            )
            ) {
                $uidStr = (string) $uid;
                $dUidStr = (string) $dirStat['uid'];
                $gidStr = (string) $gid;
                $dGidStr = (string) $dirStat['gid'];
                if (function_exists('posix_getpwuid')) {
                    $tempUsr = posix_getpwuid($uid);
                    $uidStr = $tempUsr['name'] ?? (string)$uid;
                    $tempUsr = posix_getpwuid($dirStat['uid']);
                    $dUidStr = $tempUsr['name'] ?? (string)$dirStat['uid'];
                }
                if (function_exists('posix_getgrgid')) {
                    $tempGrp = posix_getgrgid($gid);
                    $gidStr = $tempGrp['name'] ?? (string)$gid;
                    $tempGrp = posix_getgrgid($dirStat['gid']);
                    $dGidStr = $tempGrp['name'] ?? (string)$dirStat['gid'];
                }
                $message[] = sprintf(
                    CHMOD_CHGRP_ERROR,
                    $fileName,
                    $uidStr,
                    $gidStr,
                    basename($dirName),
                    $dUidStr,
                    $dGidStr,
                );
            }
        }
    }
    return empty($message) ? true : $message;
}

/**
 * Install working versions of various *.dist.php files to xoops_data/configs/
 *
 * @param $vars array of system variables, we care about ROOT_PATH and VAR_PATH keys
 *
 * @return true|string true if all files were copied, otherwise error message
 */
function copyConfigDistFiles($vars)
{
    $copied = 0;
    $failed = 0;
    $logs = [];

    /* xoopsconfig.php */
    $source = $vars['VAR_PATH'] . '/configs/xoopsconfig.dist.php';
    $destination = $vars['VAR_PATH'] . '/configs/xoopsconfig.php';
    if (!file_exists($destination)) { // don't overwrite anything
        $result = copy($source, $destination);
        $result ? ++$copied : ++$failed;
        if (false === $result) {
            $logs[] = sprintf(ERR_COPY_CONFIG_FILE, 'configs/' . basename($destination));
        }
    }

    /* captcha files */
    $captchaConfigFiles = [
        'config.dist.php'            => 'config.php',
        'config.image.dist.php'      => 'config.image.php',
        'config.recaptcha2.dist.php' => 'config.recaptcha2.php',
        'config.text.dist.php'       => 'config.text.php',
    ];

    foreach ($captchaConfigFiles as $source => $destination) {
        $src  = $vars['ROOT_PATH'] . '/class/captcha/' . $source;
        $dest = $vars['VAR_PATH'] . '/configs/captcha/' . $destination;
        if (!file_exists($dest) && file_exists($src)) {
            $result = copy($src, $dest);
            $result ? ++$copied : ++$failed;
            if (false === $result) {
                $logs[] = sprintf('captcha config file copy to %s failed', $destination);
                $logs[] = sprintf(ERR_COPY_CONFIG_FILE, 'captcha/' . $destination);
            }
        }
    }

    /* text sanitizer  files */
    $textsanitizerConfigFiles = [
        'config.dist.php'                 => 'config.php',
        'censor/config.dist.php'          => 'config.censor.php',
//        'flash/config.dist.php'           => 'config.flash.php',
        'image/config.dist.php'           => 'config.image.php',
        'mms/config.dist.php'             => 'config.mms.php',
        'rtsp/config.dist.php'            => 'config.rtsp.php',
        'syntaxhighlight/config.dist.php' => 'config.syntaxhighlight.php',
        'textfilter/config.dist.php'      => 'config.textfilter.php',
        'wiki/config.dist.php'            => 'config.wiki.php',
        'wmp/config.dist.php'             => 'config.wmp.php',
    ];
    foreach ($textsanitizerConfigFiles as $source => $destination) {
        $src  = $vars['ROOT_PATH'] . '/class/textsanitizer/' . $source;
        $dest = $vars['VAR_PATH'] . '/configs/textsanitizer/' . $destination;
        if (!file_exists($dest) && file_exists($src)) {
            $result = copy($src, $dest);
            $result ? ++$copied : ++$failed;
            if (false === $result) {
                $logs[] = sprintf(ERR_COPY_CONFIG_FILE, 'textsanitizer/' . $destination);
            }
        }
    }

    return $failed === 0 ? true : implode('<br>', $logs);
}
