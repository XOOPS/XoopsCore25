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
 * Installer path configuration page
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

include_once './class/pathcontroller.php';
include_once '../include/functions.php';

$pageHasForm = true;
$pageHasHelp = true;

$ctrl = new PathStuffController($wizard->configs['xoopsPathDefault'], $wizard->configs['dataPath']);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && @$_GET['var'] && @$_GET['action'] === 'checkpath') {
    $path                   = $_GET['var'];
    $ctrl->xoopsPath[$path] = htmlspecialchars(trim($_GET['path']));
    echo genPathCheckHtml($path, $ctrl->checkPath($path));
    exit();
}
$ctrl->execute();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return null;
}
ob_start();
?>
    <script type="text/javascript">
        function removeTrailing(id, val) {
            if (val[val.length - 1] == '/') {
                val = val.substr(0, val.length - 1);
                $(id).value = val;
            }

            return val;
        }

        function updPath(key, val) {
            val = removeTrailing(key, val);
            $.get( "<?php echo $_SERVER['PHP_SELF']; ?>", { action: "checkpath", var: key, path: val } )
                .done(function( data ) {
                    $("#" + key + 'pathimg').html(data);
                });
            $("#" + key + 'perms').style.display = 'none';
        }
    </script>
    <div class="panel panel-info">
        <div class="panel-heading"><?php echo XOOPS_PATHS; ?></div>
        <div class="panel-body">

            <div class="form-group">
                <label class="xolabel" for="root"><?php echo XOOPS_ROOT_PATH_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_ROOT_PATH_HELP; ?></div>
                <input type="text" class="form-control" name="root" id="root" value="<?php echo $ctrl->xoopsPath['root']; ?>" onchange="updPath('root', this.value)"/>
                <span id="rootpathimg"><?php echo genPathCheckHtml('root', $ctrl->validPath['root']); ?></span>
            </div>

            <?php
            if ($ctrl->validPath['root'] && !empty($ctrl->permErrors['root'])) {
                echo '<div id="rootperms" class="x2-note">';
                echo CHECKING_PERMISSIONS . '<br><p>' . ERR_NEED_WRITE_ACCESS . '</p>';
                echo '<ul class="diags">';
                foreach ($ctrl->permErrors['root'] as $path => $result) {
                    if ($result) {
                        echo '<li class="success">' . sprintf(IS_WRITABLE, $path) . '</li>';
                    } else {
                        echo '<li class="failure">' . sprintf(IS_NOT_WRITABLE, $path) . '</li>';
                    }
                }
                echo '</ul></div>';
            } else {
                echo '<div id="rootperms" class="x2-note" style="display: none;"></div>';
            }
            ?>

            <div class="form-group">
                <label for="data"><?php echo XOOPS_DATA_PATH_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_DATA_PATH_HELP; ?></div>
                <input type="text" class="form-control" name="data" id="data" value="<?php echo $ctrl->xoopsPath['data']; ?>" onchange="updPath('data', this.value)"/>
                <span id="datapathimg"><?php echo genPathCheckHtml('data', $ctrl->validPath['data']); ?></span>
            </div>
            <?php
            if ($ctrl->validPath['data'] && !empty($ctrl->permErrors['data'])) {
                echo '<div id="dataperms" class="x2-note">';
                echo CHECKING_PERMISSIONS . '<br><p>' . ERR_NEED_WRITE_ACCESS . '</p>';
                echo '<ul class="diags">';
                foreach ($ctrl->permErrors['data'] as $path => $result) {
                    if ($result) {
                        echo '<li class="success">' . sprintf(IS_WRITABLE, $path) . '</li>';
                    } else {
                        echo '<li class="failure">' . sprintf(IS_NOT_WRITABLE, $path) . '</li>';
                    }
                }
                echo '</ul></div>';
            } else {
                echo '<div id="dataperms" class="x2-note" style="display: none;"></div>';
            }
            ?>

            <div class="form-group">
                <label class="xolabel" for="lib"><?php echo XOOPS_LIB_PATH_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_LIB_PATH_HELP; ?></div>
                <input type="text" class="form-control" name="lib" id="lib" value="<?php echo $ctrl->xoopsPath['lib']; ?>" onchange="updPath('lib', this.value)"/>
                <span id="libpathimg"><?php echo genPathCheckHtml('lib', $ctrl->validPath['lib']); ?></span>
            </div>

            <div id="libperms" class="x2-note" style="display: none;"></div>
        </div>
    </div>


    <div class="panel panel-info">
        <div class="panel-heading"><?php echo XOOPS_URLS; ?></div>
        <div class="panel-body">

            <div class="form-group">
                <label class="xolabel" for="url"><?php echo XOOPS_URL_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_URL_HELP; ?></div>
                <input type="text" class="form-control" name="URL" id="url" value="<?php echo $ctrl->xoopsUrl; ?>" onchange="removeTrailing('url', this.value)"/>
            </div>

            <div class="form-group">
                <label class="xolabel" for="cookie_domain"><?php echo XOOPS_COOKIE_DOMAIN_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_COOKIE_DOMAIN_HELP; ?></div>
                <input type="text" class="form-control" name="COOKIE_DOMAIN" id="cookie_domain" value="<?php echo $ctrl->xoopsCookieDomain; ?>" onchange="removeTrailing('url', this.value)"/>
            </div>
        </div>
    </div>

<?php
$content = ob_get_contents();
ob_end_clean();

include './include/install_tpl.php';
