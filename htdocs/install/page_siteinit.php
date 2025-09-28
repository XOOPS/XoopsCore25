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
 * Installer site configuration page
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

$pageHasForm = true;
$pageHasHelp = false;
$isadmin     = false;

$vars =& $_SESSION['siteconfig'];

$error =& $_SESSION['error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vars['adminname']  = trim($_POST['adminname']);
    $vars['adminmail']  = trim($_POST['adminmail']);
    $vars['adminpass']  = trim($_POST['adminpass']);
    $vars['adminpass2'] = trim($_POST['adminpass2']);
    $error              = [];

    if (empty($vars['adminname'])) {
        $error['name'][] = ERR_REQUIRED;
    }
    if (empty($vars['adminmail'])) {
        $error['email'][] = ERR_REQUIRED;
    }
    if (empty($vars['adminpass'])) {
        $error['pass'][] = ERR_REQUIRED;
    }
    if (!filter_var($vars['adminmail'], FILTER_VALIDATE_EMAIL)) {
        $error['email'][] = ERR_INVALID_EMAIL;
    }
    if ($vars['adminpass'] != $vars['adminpass2']) {
        $error['pass'][] = ERR_PASSWORD_MATCH;
    }
    if ($error) {
        $wizard->redirectToPage('+0');

        return 200;
    } else {
        $wizard->redirectToPage('+1');

        return 302;
    }
} else {
    require_once __DIR__ . '/class/dbmanager.php';
    $dbm = new Db_manager();

    if (!$dbm->isConnectable()) {
        $wizard->redirectToPage('dbsettings');
        exit();
    }

    $sql = 'SELECT COUNT(*) FROM ' . $dbm->db->prefix('users');
    $result = $dbm->db->query($sql);
    if ($dbm->db->isResultSet($result)) {
        [$isadmin] = $dbm->db->fetchRow($result);
    }
}

ob_start();

$extraSources = '<script type="text/javascript" src="./assets/js/zxcvbn.js"></script>';
if ($isadmin) {
    $pageHasForm = false;
    $pageHasHelp = false;
    echo "<div class='alert alert-warning'>" . ADMIN_EXIST . "</div>\n";
} else {
        echo '<script type="text/javascript">
                var desc = new Array();
                desc[0] = "' . PASSWORD_VERY_WEAK . '";
                desc[1] = "' . PASSWORD_WEAK . '";
                desc[2] = "' . PASSWORD_BETTER . '";
                desc[3] = "' . PASSWORD_MEDIUM . '";
                desc[4] = "' . PASSWORD_STRONG . '";
        </script>';

    ?>
    <div class="panel panel-info">
        <div class="panel-heading"><?php echo LEGEND_ADMIN_ACCOUNT; ?></div>
        <div class="panel-body">

        <?php
        echo '<div class="row"><div class="col-md-9">';
        xoFormField('adminname', $vars['adminname'] ?? '', ADMIN_LOGIN_LABEL);

        if (isset($error['name'])) {
            foreach ($error['name'] as $errmsg) {
                echo '<div class="alert alert-danger"><span class="fa-solid fa-ban text-danger"></span> ' . $errmsg . '</div>';
            }
        }

        xoFormField('adminmail', $vars['adminmail'] ?? '', ADMIN_EMAIL_LABEL);
        if (isset($error['email'])) {
            foreach ($error['email'] as $errmsg) {
                echo '<div class="alert alert-danger"><span class="fa-solid fa-ban text-danger"></span> ' . $errmsg . '</div>';
            }
        }
        ?>

        <div id="password">
            <div id="passwordinput">
                <?php
                echo xoPassField('adminpass', '', ADMIN_PASS_LABEL);
                echo xoPassField('adminpass2', '', ADMIN_CONFIRMPASS_LABEL);
                if (isset($error['pass'])) {
                    foreach ($error['pass'] as $errmsg) {
                        echo '<div class="alert alert-danger"><span class="fa-solid fa-ban text-danger"></span> ' . $errmsg . '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
            <div id="passwordmeter" class="well">
                <h4><?php echo PASSWORD_LABEL; ?></h4>

                <div id='passwordStrength' class='strength0'>
                    <span id='passwordDescription'><?php echo PASSWORD_DESC; ?></span>
                </div>


                <div id="passwordgenerator">
                    <label for='password_generator'><?php echo PASSWORD_GENERATOR; ?></label>
                    <label for="generated_pw"></label><input type="text" class="form-control" name="generated_pw" id="generated_pw" value="" onclick="this.setSelectionRange(0, this.value.length); document.execCommand('copy');"><br>
                    <button type="button" class="btn btn-default" onclick="suggestPassword(16);">
                    <?php echo PASSWORD_GENERATE; ?></button>
                    <button type="button" class="btn btn-default" onclick="suggestPasswordCopy();">
                    <?php echo PASSWORD_COPY; ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php

}
$content = ob_get_contents();
ob_end_clean();
$error = !empty($error);
include __DIR__ . '/include/install_tpl.php';
