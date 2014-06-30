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
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright    (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package     installer
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 * @version     $Id$
**/

require_once './include/common.inc.php';
defined('XOOPS_INSTALL') or die('XOOPS Installation wizard die');

$pageHasForm = false;
$pageHasHelp = false;

$vars =& $_SESSION['settings'];

if (empty($vars['ROOT_PATH'])) {
    $wizard->redirectToPage('pathsettings');
    exit();
} else if (empty($vars['DB_HOST'])) {
    $wizard->redirectToPage('dbsettings');
    exit();
}

$error = '';
if (!@copy($vars['ROOT_PATH'] . '/mainfile.dist.php', $vars['ROOT_PATH'] . '/mainfile.php')) {
    $error = ERR_COPY_MAINFILE;
} else {
    clearstatcache();

    $rewrite = array( 'GROUP_ADMIN'      => 1,
                      'GROUP_USERS'      => 2,
                      'GROUP_ANONYMOUS'  => 3
                    );

    $rewrite = array_merge($rewrite, $vars);
    if (!$file = fopen($vars['ROOT_PATH'] . '/mainfile.php', "r")) {
        $error = ERR_READ_MAINFILE;
    } else {
        $content = fread($file, filesize( $vars['ROOT_PATH'] . '/mainfile.php'));
        fclose($file);

        foreach ($rewrite as $key => $val) {
            if ($key == 'authorized') continue;
            if (is_int($val) && preg_match("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([0-9]+)\s*\)/", $content)) {
                $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([0-9]+)\s*\)/", "define('XOOPS_{$key}', {$val})", $content);
            } else if(preg_match("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", $content)) {
                $val = str_replace('$', '\$', addslashes($val));
                $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", "define('XOOPS_{$key}', '{$val}')", $content);
            } else {
                //$this->error = true;
                //$this->report .= _NGIMG.sprintf( ERR_WRITING_CONSTANT, "<strong>$val</strong>")."<br />\n";
            }
        }
        if (!$file = fopen( $vars['ROOT_PATH'] . '/mainfile.php', "w")) {
            $error = ERR_WRITE_MAINFILE;
        } else {
            if (fwrite( $file, $content ) == -1) {
                $error = ERR_WRITE_MAINFILE;
            }
            fclose($file);
        }
    }
}

//if (!@copy($rewrite['VAR_PATH'] . '/data/secure.dist.php', $rewrite['VAR_PATH'] . '/data/secure.php')) {
//    $error = ERR_COPY_MAINFILE.$rewrite['VAR_PATH'] . '/data/secure.dist.php';
//} else {
//    clearstatcache();

//from XavierS:
if (!@copy($vars['VAR_PATH'] . '/data/secure.dist.php', $vars['VAR_PATH'] . '/data/secure.php')) {
   $error = ERR_COPY_MAINFILE.$vars['VAR_PATH'] . '/data/secure.dist.php';
   } else {
   clearstatcache();


   $rewrite = array_merge($rewrite, $vars);

    $rewrite = array_merge($rewrite, $vars);
    if (!$file = fopen($rewrite['VAR_PATH'] . '/data/secure.php', "r")) {
        $error = ERR_READ_MAINFILE;
    } else {
        $content = fread($file, filesize( $rewrite['VAR_PATH'] . '/data/secure.php'));
        fclose($file);

        foreach ($rewrite as $key => $val) {
            if ($key == 'authorized') continue;
            if (is_int($val) && preg_match("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([0-9]+)\s*\)/", $content)) {
                $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([0-9]+)\s*\)/", "define('XOOPS_{$key}', {$val})", $content);
            } else if(preg_match("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", $content)) {
                $val = str_replace('$', '\$', addslashes($val));
                $content = preg_replace("/(define\()([\"'])(XOOPS_{$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", "define('XOOPS_{$key}', '{$val}')", $content);
            }
        }
        if (!$file = fopen( $rewrite['VAR_PATH'] . '/data/secure.php', "w")) {
            $error = ERR_WRITE_MAINFILE;
        } else {
            if (fwrite( $file, $content ) == -1) {
                $error = ERR_WRITE_MAINFILE;
            }
            fclose($file);
        }
    }
}

$_SESSION['settings']['authorized'] = false;
if (empty($error)) {
    $_SESSION['UserLogin'] = true;
    $_SESSION['settings']['authorized'] = true;
    ob_start();
?>

<div class="caption"><?php echo SAVED_MAINFILE; ?></div>
<div class='x2-note confirmMsg'><?php echo SAVED_MAINFILE_MSG; ?></div>
<ul class='diags'>
    <?php
    foreach ($vars as $k => $v) {
        if ($k == 'authorized') continue;
        echo "<li><strong>XOOPS_{$k}</strong> " . IS_VALOR . " {$v}</li>";
    }
    ?>
</ul>
<?php
    $content = ob_get_contents();
    ob_end_clean();
} else {
    $content = '<div class="errorMsg">' . $error . '</div>';
}
include './include/install_tpl.php';
