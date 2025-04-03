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
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

use Xmf\Request;

require_once __DIR__ . '/include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

include_once __DIR__ . '/class/pathcontroller.php';
include_once __DIR__ . '/../include/functions.php';

$pageHasForm = true;
$pageHasHelp = true;

$pathController = new PathController($wizard->configs['xoopsPathDefault'], $wizard->configs['dataPath']);

//if ($_SERVER['REQUEST_METHOD'] === 'GET' && @$_GET['var'] && Xmf\Request::getString('action', '', 'GET') === 'checkpath') {
//    $path                   = $_GET['var'];
//    $pathController->xoopsPath[$path] = htmlspecialchars(trim($_GET['path']), ENT_QUOTES | ENT_HTML5);
//    echo genPathCheckHtml($path, $pathController->checkPath($path));
//    exit();
//}

// install/page_pathsettings.php

/*

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['var']) && isset($_GET['action']) && $_GET['action'] === 'checkpath') {
    // Sanitize the input
    $pathKey = htmlspecialchars(trim($_GET['var']), ENT_QUOTES | ENT_HTML5);
    $newPath = htmlspecialchars(trim($_GET['path']), ENT_QUOTES | ENT_HTML5);

    // Perform basic validation for the new path
    if (!is_dir($newPath)) {
        echo "Error: The specified path does not exist. Please verify the folder and try again.";
        exit();
    }

    // Update the XOOPS_TRUST_PATH dynamically if it's the library path
    if ($pathKey === 'lib') {
        // Update session and constant
        $_SESSION['settings']['TRUST_PATH'] = $newPath;

        if (!defined('XOOPS_TRUST_PATH')) {
            define('XOOPS_TRUST_PATH', $newPath);
        }

        if (defined('XOOPS_TRUST_PATH') && XOOPS_TRUST_PATH !== $newPath ){
//redefine XOOPS_TRUST_PATH if it is different from $newPath, because obviously the XOOPS_TRUST_PATH has been changed
        }

        $pathController->updateXoopsTrustPath($newPath);

//        if ($newPath) {
//            try {
//                $pathController->updateXoopsTrustPath($newPath);
//            } catch (RuntimeException $e) {
//                $pathController->validPath['lib'] = false;
//                $pathController->errorMessage = $e->getMessage();
//            }
//        } else {
//            $pathController->validPath['lib'] = false;
//            $pathController->errorMessage = "Invalid XOOPS library directory. Please check the path.";
//        }
//





        // Check for the autoloader in the new path
        $composerAutoloader = XOOPS_TRUST_PATH . '/vendor/autoload.php';
        echo "$composerAutoloader";
        if (!file_exists($composerAutoloader)) {
            echo "Error: Could not find the Composer autoloader in the specified path.";
            exit();
        }

        // Include the autoloader
        require_once $composerAutoloader;
    }

    // Perform the path check
    $pathController->xoopsPath[$pathKey] = $newPath;
    echo genPathCheckHtml($pathKey, $pathController->checkPath($pathKey));
    exit();
}

*/


// Handle GET request for path checking
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['var']) && isset($_GET['action']) && $_GET['action'] === 'checkpath') {
    // Sanitize input
    $pathKey = htmlspecialchars(trim($_GET['var']), ENT_QUOTES | ENT_HTML5);
    $newPath = htmlspecialchars(trim($_GET['path']), ENT_QUOTES | ENT_HTML5);

    // Validate directory
    if (!is_dir($newPath)) {
        echo "Error: The specified path does not exist. Please verify the folder and try again.";
        exit();
    }

    if ($pathKey === 'lib') {
        // Update session and variable
        $_SESSION['settings']['TRUST_PATH'] = $newPath;
        $xoopsTrustPath = $newPath;

        $pathController->updateXoopsTrustPath($newPath);

        // Check for Composer autoloader
        $composerAutoloader = $xoopsTrustPath . '/vendor/autoload.php';
        echo "$composerAutoloader";
        if (!file_exists($composerAutoloader)) {
            echo "Error: Could not find the Composer autoloader in the specified path.";
            exit();
        }

        // Include the autoloader only once
//        if (!class_exists('ComposerAutoloaderInit401aa2fe6008ca63602daf4ec1d196f2')) {
        include_once $composerAutoloader;
//        }
    }

    // Perform the path check
    $pathController->xoopsPath[$pathKey] = $newPath;
    echo genPathCheckHtml($pathKey, $pathController->checkPath($pathKey));
    exit();
}


$pathController->execute();
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    return null;
//}

/*

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lib']) && $_POST['lib'] !== $pathController->xoopsPath['lib']) {
        $newTrustPath = $pathController->sanitizePath(trim($_POST['lib']));

        if ($newTrustPath) {
            try {
                $pathController->updateXoopsTrustPath($newTrustPath);
            } catch (RuntimeException $e) {
                $pathController->validPath['lib'] = false;
                $pathController->errorMessage = $e->getMessage();
            }
        } else {
            $pathController->validPath['lib'] = false;
            $pathController->errorMessage = "Invalid XOOPS library directory. Please check the path.";
        }
    }
}

*/

// Handle POST request for updating paths
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lib']) && $_POST['lib'] !== $pathController->xoopsPath['lib']) {
        $newTrustPath = $pathController->sanitizePath(trim($_POST['lib']));

        if ($newTrustPath && is_dir($newTrustPath)) {
            $xoopsTrustPath = $newTrustPath;
            $_SESSION['settings']['TRUST_PATH'] = $newTrustPath;

            try {
                $pathController->updateXoopsTrustPath($newTrustPath);
            } catch (RuntimeException $e) {
                $pathController->validPath['lib'] = false;
                $pathController->errorMessage = $e->getMessage();
            }
        } else {
            $pathController->validPath['lib'] = false;
            $pathController->errorMessage = "Invalid XOOPS library directory. Please check the path.";
        }
    }
}

// Include Composer autoloader if not already included
//if (!class_exists('ComposerAutoloaderInit401aa2fe6008ca63602daf4ec1d196f2')) {
//   include_once $xoopsTrustPath . '/vendor/autoload.php';
//}





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

        //function updPath(key, val) {
        //    val = removeTrailing(key, val);
        //    $.get( "<?php //echo $_SERVER['PHP_SELF']; ?>//", { action: "checkpath", var: key, path: val } )
        //        .done(function( data ) {
        //            $("#" + key + 'pathimg').html(data);
        //        });
        //    $("#" + key + 'perms').style.display = 'none';
        //}

        function updPath(key, val) {
            // Remove trailing slashes
            val = removeTrailing(key, val);

            // Perform AJAX request to validate the path
            $.get("<?php echo $_SERVER['PHP_SELF']; ?>", { action: "checkpath", var: key, path: val })
                .done(function(data) {
                    // Update the path check result
                    $("#" + key + 'pathimg').html(data);
                })
                .fail(function() {
                    console.error("Error while checking path for key:", key);
                });

            // Hide permissions element if it exists
            const permsElement = $("#" + key + 'perms')[0];
            if (permsElement) {
                permsElement.style.display = 'none';
            } else {
                console.warn("Permissions element with ID '" + key + "perms' not found.");
            }
        }

    </script>
    <div class="panel panel-info">
        <div class="panel-heading"><?php echo XOOPS_PATHS; ?></div>
        <div class="panel-body">

            <div class="form-group">
                <label class="xolabel" for="root"><?php echo XOOPS_ROOT_PATH_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_ROOT_PATH_HELP; ?></div>
                <input type="text" class="form-control" name="root" id="root" value="<?php echo $pathController->xoopsPath['root']; ?>" onchange="updPath('root', this.value)"/>
                <span id="rootpathimg"><?php echo genPathCheckHtml('root', $pathController->validPath['root']); ?></span>
            </div>

            <?php
            if ($pathController->validPath['root'] && !empty($pathController->permErrors['root'])) {
                echo '<div id="rootperms" class="x2-note">';
                echo CHECKING_PERMISSIONS . '<br><p>' . ERR_NEED_WRITE_ACCESS . '</p>';
                echo '<ul class="diags">';
                foreach ($pathController->permErrors['root'] as $path => $result) {
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
                <input type="text" class="form-control" name="data" id="data" value="<?php echo $pathController->xoopsPath['data']; ?>" onchange="updPath('data', this.value)"/>
                <span id="datapathimg"><?php echo genPathCheckHtml('data', $pathController->validPath['data']); ?></span>
            </div>
            <?php
            if ($pathController->validPath['data'] && !empty($pathController->permErrors['data'])) {
                echo '<div id="dataperms" class="x2-note">';
                echo CHECKING_PERMISSIONS . '<br><p>' . ERR_NEED_WRITE_ACCESS . '</p>';
                echo '<ul class="diags">';
                foreach ($pathController->permErrors['data'] as $path => $result) {
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
                <input type="text" class="form-control" name="lib" id="lib" value="<?php echo $pathController->xoopsPath['lib']; ?>" onchange="updPath('lib', this.value)"/>
                <span id="libpathimg"><?php echo genPathCheckHtml('lib', $pathController->validPath['lib']); ?></span>
            </div>

            <div id="libperms" class="x2-note" style="display: none;"></div>
            <?php
            if (!empty($pathController->errorMessage)) {
                echo '<div class="alert alert-danger" role="alert">';
                echo $pathController->errorMessage;
                echo '</div>';
            }
            ?>
        </div>
    </div>


    <div class="panel panel-info">
        <div class="panel-heading"><?php echo XOOPS_URLS; ?></div>
        <div class="panel-body">

            <div class="form-group">
                <label class="xolabel" for="url"><?php echo XOOPS_URL_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_URL_HELP; ?></div>
                <input type="text" class="form-control" name="URL" id="url" value="<?php echo $pathController->xoopsUrl; ?>" onchange="removeTrailing('url', this.value)"/>
            </div>

            <div class="form-group">
                <label class="xolabel" for="cookie_domain"><?php echo XOOPS_COOKIE_DOMAIN_LABEL; ?></label>
                <div class="xoform-help alert alert-info"><?php echo XOOPS_COOKIE_DOMAIN_HELP; ?></div>
                <input type="text" class="form-control" name="COOKIE_DOMAIN" id="cookie_domain" value="<?php echo $pathController->xoopsCookieDomain; ?>" onchange="removeTrailing('url', this.value)"/>
            </div>
        </div>
    </div>

<?php
$content = ob_get_contents();
ob_end_clean();

include __DIR__ . '/include/install_tpl.php';
