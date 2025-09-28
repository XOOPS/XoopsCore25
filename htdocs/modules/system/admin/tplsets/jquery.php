<?php

use Xmf\Assert;
use Xmf\Request;

/**
 * Template Manager
 * Manage all templates: theme and module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Maxime Cointin (AKA Kraven30)
 * @package             system
 */
/** @var XoopsUser $xoopsUser */
/** @var XoopsModule $xoopsModule */
/** @var XoopsConfigItem $xoopsConfig */

include dirname(__DIR__, 2) . '/header.php';

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

error_reporting(0);
$GLOBALS['xoopsLogger']->activated = false;

if (file_exists(__DIR__ . '/../../language/' . $xoopsConfig['language'] . '"/admin/tplsets.php')) {
    include_once __DIR__ . '/../../language/' . $xoopsConfig['language'] . '/admin/tplsets.php';
} else {
    include_once __DIR__ . '/../../language/english/admin/tplsets.php';
}

XoopsLoad::load('XoopsRequest');

$GLOBALS['xoopsLogger']->usePopup = true;

$op = XoopsRequest::getCmd('op', 'default');
switch ($op) {
    // Display tree folder
    case 'tpls_display_folder':
        $root = XOOPS_THEME_PATH;
        $cleanDir = urldecode(Request::getString('dir', ''));
        $requestDir = $root . $cleanDir;
        //
        $path_file = realpath($requestDir);
        $check_path = realpath($root);
        try {
            Assert::true(is_dir($check_path), _AM_SYSTEM_TEMPLATES_ERROR);
            Assert::true(is_dir($path_file), _AM_SYSTEM_TEMPLATES_ERROR);
            Assert::startsWith($path_file, $check_path, _AM_SYSTEM_TEMPLATES_ERROR);
        } catch (\InvalidArgumentException $e) {
            // handle the exception
            redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=tplsets', 2, $e->getMessage());
            exit;
        }
        //
        if (file_exists($requestDir)) {
            $files = scandir($requestDir);
            natcasesort($files);
            if (count($files) > 2) { /* The 2 accounts for . and .. */
                echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
                // All dirs
                foreach ($files as $file) {
                    if (file_exists($requestDir . $file) && $file !== '.' && $file !== '..' && is_dir($requestDir . $file)) {
                        //retirer .svn
                        $file_no_valid = ['.svn', 'icons', 'img', 'images', 'language'];

                        if (!in_array($file, $file_no_valid)) {
                            echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_REQUEST['dir'] . $file, ENT_QUOTES | ENT_HTML5) . "/\">" . htmlentities($file, ENT_QUOTES | ENT_HTML5) . '</a></li>';
                        }
                    }
                }
                // All files
                foreach ($files as $file) {
                    if (file_exists($root . $_REQUEST['dir'] . $file) && $file !== '.' && $file !== '..' && !is_dir($root . $_REQUEST['dir'] . $file) && $file !== 'index.html') {
                        $ext = preg_replace('/^.*\./', '', $file);

                        $extensions      = ['.html', '.htm', '.css', '.tpl'];
                        $extension_verif = strrchr($file, '.');

                        if (in_array($extension_verif, $extensions)) {
                            echo "<li class=\"file ext_$ext\"><a href=\"#\" onclick=\"tpls_edit_file('" . htmlentities($_REQUEST['dir'] . $file, ENT_QUOTES | ENT_HTML5) . "', '" . htmlentities((string) $_REQUEST['dir'], ENT_QUOTES | ENT_HTML5) . "', '" . htmlentities($file, ENT_QUOTES | ENT_HTML5) . "', '" . $ext . "');\" rel=\"tpls_edit_file('" . htmlentities($_REQUEST['dir'] . $file, ENT_QUOTES | ENT_HTML5) . "', '" . htmlentities((string) $_REQUEST['dir'], ENT_QUOTES | ENT_HTML5) . "', '" . htmlentities($file, ENT_QUOTES | ENT_HTML5) . "', '" . $ext . "');\">" . htmlentities($file, ENT_QUOTES | ENT_HTML5) . '</a></li>';
                        } else {
                            //echo "<li class=\"file ext_$ext\">" . htmlentities($file) . "</li>";
                        }
                    }
                }
                echo '</ul>';
            }
        }
        break;
    // Edit File
    case 'tpls_edit_file':
        $clean_file = XoopsRequest::getString('file', '');
        $clean_path_file = XoopsRequest::getString('path_file', '');
        $path_file = realpath(XOOPS_ROOT_PATH.'/themes'.trim($clean_path_file));
        $check_path = realpath(XOOPS_ROOT_PATH.'/themes');
        try {
            Assert::startsWith($path_file, $check_path, _AM_SYSTEM_TEMPLATES_ERROR);
        } catch (\InvalidArgumentException $e) {
            // handle the exception
            redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=tplsets', 2, $e->getMessage());
            exit;
        }

        $path_file = str_replace('\\', '/', $path_file);

        //Button restore
        $restore = '';
        if (file_exists($path_file . '.back')) {
            $restore = '<button class="ui-corner-all tooltip" type="button" onclick="tpls_restore(\'' . $path_file . '\')" value="' . _AM_SYSTEM_TEMPLATES_RESTORE . '" title="' . _AM_SYSTEM_TEMPLATES_RESTORE . '">
                            <img src="' . system_AdminIcons('revert.png') . '" alt="' . _AM_SYSTEM_TEMPLATES_RESTORE . '" />
                        </button>';
        }
        xoops_load('XoopsFile');
        XoopsFile::load('file');

        $file    = XoopsFile::getHandler('file', $path_file);
        $content = $file->read();
        if (empty($content)) {
            echo _AM_SYSTEM_TEMPLATES_EMPTY_FILE;
        }
        $ext = preg_replace('/^.*\./', '', $clean_path_file);

        echo '<form name="back" action="admin.php?fct=tplsets&op=tpls_save" method="POST">
              <table border="0">
                <tr>
                    <td>
                          <div class="xo-btn-actions">
                              <div class="xo-buttons">
                                  <button class="ui-corner-all tooltip" type="submit" value="' . _AM_SYSTEM_TEMPLATES_SAVE . '" title="' . _AM_SYSTEM_TEMPLATES_SAVE . '">
                                      <img src="' . system_AdminIcons('save.png') . '" alt="' . _AM_SYSTEM_TEMPLATES_SAVE . '" />
                                  </button>
                                  ' . $restore . '
                                  <button class="ui-corner-all tooltip" type="button" onclick="$(\'#display_contenu\').hide();$(\'#display_form\').fadeIn(\'fast\');" title="' . _AM_SYSTEM_TEMPLATES_CANCEL . '">
                                      <img src="' . system_AdminIcons('cancel.png') . '" alt="' . _AM_SYSTEM_TEMPLATES_CANCEL . '" />
                                  </button>
                                  <div class="clear"></div>
                             </div>
                         </div>
                    </td>
                </tr>
                <tr>
                    <td><textarea id="code_mirror" name="templates" rows=24 cols=110>'
                        . htmlentities((string) $content, ENT_QUOTES | ENT_HTML5)
                    . '</textarea></td>
                </tr>
              </table>';
        XoopsLoad::load('XoopsFormHiddenToken');
        $xoopsToken = new XoopsFormHiddenToken();
        echo $xoopsToken->render();
        echo '<input type="hidden" name="path_file" value="' . htmlentities($clean_path_file, ENT_QUOTES | ENT_HTML5)
            .'"><input type="hidden" name="file" value="' . htmlentities(trim($clean_file), ENT_QUOTES | ENT_HTML5)
            .'"><input type="hidden" name="ext" value="' . htmlentities($ext, ENT_QUOTES | ENT_HTML5) . '"></form>';
        break;

    // Restore backup file
    case 'tpls_restore':
        $extensions = ['.html', '.htm', '.css', '.tpl'];

        //check if the file is inside themes directory
        $valid_dir = stristr(realpath($_REQUEST['path_file']), (string) realpath(XOOPS_ROOT_PATH . '/themes'));

        $old_file = $_REQUEST['path_file'] . '.back';
        $new_file = $_REQUEST['path_file'];

        $extension_verif = strrchr((string) $new_file, '.');
        if ($valid_dir && in_array($extension_verif, $extensions) && file_exists($old_file) && file_exists($new_file)) {
            if (unlink($new_file)) {
                if (rename($old_file, $new_file)) {
                    xoops_result(_AM_SYSTEM_TEMPLATES_RESTORE_OK);
                    exit();
                }
            }
        }
        xoops_error(_AM_SYSTEM_TEMPLATES_RESTORE_NOTOK);
        break;
}
