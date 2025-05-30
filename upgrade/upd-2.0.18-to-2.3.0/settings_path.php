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
 * @copyright    (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.3.0
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 */

if (!defined('XOOPS_ROOT_PATH')) {
    die('Bad installation: please add this folder to the XOOPS install you want to upgrade');
}

/**
 * @param $path
 * @param $valid
 *
 * @return string
 */
function genPathCheckHtml($path, $valid)
{
    $myts = \MyTextSanitizer::getInstance();
    if ($valid) {
        switch ($path) {
            case 'lib':
            case 'data':
            default:
                $msg = XOOPS_PATH_FOUND;
                break;
        }
        $msg = $myts->htmlSpecialChars($msg, ENT_QUOTES, _UPGRADE_CHARSET, false);

        return '<span class="result-y">y</span> ' . $msg;
    } else {
        switch ($path) {
            case 'lib':
            case 'data':
            default:
                $msg = ERR_COULD_NOT_ACCESS;
                break;
        }
        $msg = $myts->htmlSpecialChars($msg, ENT_QUOTES, _UPGRADE_CHARSET, false);

        return '<span class="result-x">x</span> ' . $msg;
    }
}

$vars = & $_SESSION['settings'];
$pathController = new PathController();
if ($res = $pathController->execute()) {
    return $res;
}

$myts = \MyTextSanitizer::getInstance();

?>

<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>

    <fieldset>
        <legend><?php echo LEGEND_XOOPS_PATHS; ?></legend>
        <label for="data"><?php echo XOOPS_DATA_PATH_LABEL; ?></label>

        <div class="xoform-help"><?php echo $myts->htmlSpecialChars(XOOPS_DATA_PATH_HELP, ENT_QUOTES, _UPGRADE_CHARSET, false); ?></div>
        <span class="bold"><?php echo $pathController->xoopsPath['data']; ?></span>

        <div><?php echo genPathCheckHtml('data', $pathController->validPath['data']); ?></div>
        <?php if ($pathController->validPath['data'] && !empty($pathController->permErrors['data'])) { ?>
        <div id="dataperms" class="x2-note">
            <?php echo CHECKING_PERMISSIONS . '<br><p>' . ERR_NEED_WRITE_ACCESS . '</p>'; ?>
            <ul class="diags">
                <?php foreach ($pathController->permErrors['data'] as $path => $result) {
                    if ($result) {
                        echo '<li class="success">' . sprintf(IS_WRITABLE, $path) . '</li>';
                    } else {
                        echo '<li class="failure">' . sprintf(IS_NOT_WRITABLE, $path) . '</li>';
                    }
                } ?>
            </ul>
            <?php
        } else { ?>
                <div id="dataperms" class="x2-note" style="display: none;"/>
            <?php } ?>
        </div>

        <label for="lib"><?php echo XOOPS_LIB_PATH_LABEL; ?></label>

        <div class="xoform-help"><?php echo $myts->htmlSpecialChars(XOOPS_LIB_PATH_HELP, ENT_QUOTES, _UPGRADE_CHARSET, false); ?></div>
        <span class="bold"><?php echo $pathController->xoopsPath['lib']; ?></span><br/>
        <span><?php echo genPathCheckHtml('lib', $pathController->validPath['lib']); ?></span>

    </fieldset>
    <input type="hidden" name="action" value="next"/>
    <input type="hidden" name="task" value="path"/>

    <div class="xo-formbuttons">
        <button type="submit"><?php echo _SUBMIT; ?></button>
    </div>
