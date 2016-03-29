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
$pageHasHelp = false;

$vars =& $_SESSION['siteconfig'];

$error =& $_SESSION['error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vars['adminname']  = $_POST['adminname'];
    $vars['adminmail']  = $_POST['adminmail'];
    $vars['adminpass']  = $_POST['adminpass'];
    $vars['adminpass2'] = $_POST['adminpass2'];
    $error              = array();

    if (empty($vars['adminname'])) {
        $error['name'][] = ERR_REQUIRED;
    }
    if (empty($vars['adminmail'])) {
        $error['email'][] = ERR_REQUIRED;
    }
    if (empty($vars['adminpass'])) {
        $error['pass'][] = ERR_REQUIRED;
    }
    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $vars['adminmail'])) {
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
    include_once './class/dbmanager.php';
    $dbm = new Db_manager();

    if (!$dbm->isConnectable()) {
        $wizard->redirectToPage('dbsettings');
        exit();
    }

    $res = $dbm->query('SELECT COUNT(*) FROM ' . $dbm->db->prefix('users'));
    list($isadmin) = $dbm->db->fetchRow($res);
}

ob_start();

if ($isadmin) {
    $pageHasForm = false;
    $pageHasHelp = false;
    echo "<div class='x2-note errorMsg'>" . ADMIN_EXIST . "</div>\n";
} else {
    ?>
    <fieldset>
        <legend><?php echo LEGEND_ADMIN_ACCOUNT;
            ?></legend>

        <?php
        echo '<script type="text/javascript">
                var desc = new Array();
                desc[0] = "' . PASSWORD_DESC . '";
                desc[1] = "' . PASSWORD_VERY_WEAK . '";
                desc[2] = "' . PASSWORD_WEAK . '";
                desc[3] = "' . PASSWORD_BETTER . '";
                desc[4] = "' . PASSWORD_MEDIUM . '";
                desc[5] = "' . PASSWORD_STRONG . '";
                desc[6] = "' . PASSWORD_STRONGEST . '";
        </script>';

        echo xoFormField('adminname', $vars['adminname'], ADMIN_LOGIN_LABEL);
        if (!empty($error['name'])) {
            echo '<ul class="diags1">';
            foreach ($error['name'] as $errmsg) {
                echo '<li class="failure">' . $errmsg . '</li>';
            }
            echo '</ul>';
        }

        echo xoFormField('adminmail', $vars['adminmail'], ADMIN_EMAIL_LABEL);
        if (!empty($error['email'])) {
            echo '<ul class="diags1">';
            foreach ($error['email'] as $errmsg) {
                echo '<li class="failure">' . $errmsg . '</li>';
            }
            echo '</ul>';
        }
        ?>

        <div id="password">
            <div id="passwordinput">
                <?php
                echo xoPassField('adminpass', '', ADMIN_PASS_LABEL);
                echo xoPassField('adminpass2', '', ADMIN_CONFIRMPASS_LABEL);
                if (!empty($error['pass'])) {
                    echo '<ul class="diags1">';
                    foreach ($error['pass'] as $errmsg) {
                        echo '<li class="failure">' . $errmsg . '</li>';
                    }
                    echo '</ul>';
                }
                ?>
            </div>

            <div id="passwordmetter" class="xoform-help">
                <label class="xolabel" for='passwordStrength'><strong><?php echo PASSWORD_LABEL;
                        ?></strong></label>

                <div id='passwordStrength' class='strength0'>
                    <span id='passwordDescription'><?php echo PASSWORD_DESC;
                        ?></span>
                </div>

                <label class="xolabel" for='password_generator'><strong><?php echo PASSWORD_GENERATOR;
                        ?></strong></label>

                <div id="passwordgenerator">
                    <input type='text' name='generated_pw' id='generated_pw' value=''/><br/>
                    <button type='button' onclick='suggestPassword(14);'/>
                    <?php echo PASSWORD_GENERATE;
                    ?></button>
                    <button type='button' onclick='suggestPasswordCopy("adminpass");'/>
                    <?php echo PASSWORD_COPY;
                    ?></button>
                </div>
            </div>
        </div>
        <br style="clear: both;"/>
    </fieldset>
    <script type="text/javascript">
        showHideHelp(this);
    </script>
    <?php

}
$content = ob_get_contents();
ob_end_clean();
$error = '';
include './include/install_tpl.php';
