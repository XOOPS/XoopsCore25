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
 * Installer database configuration page
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
defined('XOOPS_INSTALL') or die('XOOPS Installation wizard die');

$pageHasForm = true;
$pageHasHelp = true;

$vars =& $_SESSION['settings'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = array('DB_TYPE', 'DB_HOST', 'DB_USER', 'DB_PASS');
    foreach ($params as $name) {
        $vars[$name] = $_POST[$name];
    }
    $vars['DB_PCONNECT'] = @$_POST['DB_PCONNECT'] ? 1 : 0;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($vars['DB_HOST']) && !empty($vars['DB_USER'])) {
    $hostConnectPrefix = empty($vars['DB_PCONNECT']) ? '' : 'p:';
    if (!($link = @mysqli_connect($hostConnectPrefix.$vars['DB_HOST'], $vars['DB_USER'], $vars['DB_PASS']))) {
        $error = ERR_NO_DBCONNECTION;
    }
    if (empty($error)) {
        $wizard->redirectToPage('+1');
        exit();
    }
}

if (@empty($vars['DB_HOST'])) {
    // Fill with default values
    $vars = array_merge($vars, array(
                                 'DB_TYPE'     => 'mysql',
                                 'DB_HOST'     => 'localhost',
                                 'DB_USER'     => '',
                                 'DB_PASS'     => '',
                                 'DB_PCONNECT' => 0));
}
ob_start();
?>
<?php if (!empty($error)) {
    echo '<div class="x2-note errorMsg">' . $error . "</div>\n";
} ?>
    <fieldset>
        <legend><?php echo LEGEND_CONNECTION; ?></legend>
        <label class="xolabel" for="DB_DATABASE_LABEL" class="center">
            <?php echo DB_DATABASE_LABEL; ?>
            <select size="1" name="DB_TYPE">
                <?php
                foreach ($wizard->configs['db_types'] as $db_type) {
                    $selected = ($vars['DB_TYPE'] == $db_type) ? 'selected' : '';
                    echo "<option value='$db_type' selected='$selected'>$db_type</option>";
                }
                ?>
            </select>
        </label>
        <?php echo xoFormField('DB_HOST', $vars['DB_HOST'], DB_HOST_LABEL, DB_HOST_HELP); ?>
        <?php echo xoFormField('DB_USER', $vars['DB_USER'], DB_USER_LABEL, DB_USER_HELP); ?>
        <?php echo xoPassField('DB_PASS', $vars['DB_PASS'], DB_PASS_LABEL, DB_PASS_HELP); ?>

        <label class="xolabel" for="DB_PCONNECT" class="center">
            <?php echo DB_PCONNECT_LABEL; ?>
            <input class="checkbox" type="checkbox" name="DB_PCONNECT" value="1" <?php echo $vars['DB_PCONNECT'] ? "'checked'" : ''; ?>/>

            <div class="xoform-help"><?php echo DB_PCONNECT_HELP; ?></div>
        </label>
    </fieldset>

<?php
$content = ob_get_contents();
ob_end_clean();
include './include/install_tpl.php';
