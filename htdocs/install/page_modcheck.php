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
 * Installer configuration check page
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

require_once './include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

$pageHasForm = false;
$diagsOK     = false;

foreach ($wizard->configs['extensions'] as $ext => $value) {
    if (extension_loaded($ext)) {
        if (is_array($value[0])) {
            $wizard->configs['extensions'][$ext][] = xoDiag(1, implode(',', $value[0]));
        } else {
            $wizard->configs['extensions'][$ext][] = xoDiag(1, $value[0]);
        }
    } else {
        $wizard->configs['extensions'][$ext][] = xoDiag(0, $value[0]);
    }
}
ob_start();
?>
    <h3><?php echo REQUIREMENTS; ?></h3>
    <table class="table table-hover">
        <tbody>
        <tr>
            <th><?php echo SERVER_API; ?></th>
            <td><?php echo php_sapi_name(); ?><br> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
        </tr>

        <tr>
            <th><?php echo _PHP_VERSION; ?></th>
            <td><?php echo xoPhpVersion(); ?></td>
        </tr>

        <tr>
            <th><?php printf(PHP_EXTENSION, 'MySQLi'); ?></th>
            <td><?php echo xoDiag(function_exists('mysqli_connect') ? 1 : -1, @mysqli_get_client_info()); ?></td>
        </tr>

        <tr>
            <th><?php printf(PHP_EXTENSION, 'Session'); ?></th>
            <td><?php echo xoDiag(extension_loaded('session') ? 1 : -1); ?></td>
        </tr>

        <tr>
            <th><?php printf(PHP_EXTENSION, 'PCRE'); ?></th>
            <td><?php echo xoDiag(extension_loaded('pcre') ? 1 : -1); ?></td>
        </tr>

        <tr>
            <th><?php printf(PHP_EXTENSION, 'filter'); ?></th>
            <td><?php echo xoDiag(extension_loaded('filter') ? 1 : -1); ?></td>
        </tr>

        <tr>
            <th><?php printf(PHP_EXTENSION, 'iconv'); ?></th>
            <td><?php echo xoDiag(extension_loaded('iconv') ? 1 : -1); ?></td>
        </tr>

        <tr>
            <th scope="row">file_uploads</th>
            <td><?php echo xoDiagBoolSetting('file_uploads', true); ?></td>
        </tr>
        </tbody>
    </table>

    <h3><?php echo RECOMMENDED_EXTENSIONS; ?></h3>
    <table class="table table-hover">
        <caption><?php echo RECOMMENDED_EXTENSIONS_MSG; ?></caption>
        <tbody>
        <?php
        foreach ($wizard->configs['extensions'] as $key => $value) {
            echo '<tr><th>' . $value[2] . '</th><td>' . $value[1] . '</td></tr>';
        }
        ?>

        </tbody>
    </table>
<?php
$content = ob_get_contents();
ob_end_clean();

include './include/install_tpl.php';
