<?php
/**
 * Filemanager main page
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Maxime Cointin (AKA Kraven30)
 * @package             system
 */
/* @var  $xoopsUser XoopsUser */
/* @var $xoopsModule XoopsModule */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
//  Check is active
if (!xoops_getModuleOption('active_filemanager', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'default', 'string');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_filemanager.tpl';
// Call Header
xoops_cp_header();

$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('modules/system/js/jquery.easing.js');
$xoTheme->addScript('modules/system/js/jqueryFileTree.js');
$xoTheme->addScript('modules/system/js/filemanager.js');
$xoTheme->addScript('modules/system/js/admin.js');
$xoTheme->addScript('modules/system/js/code_mirror/codemirror.js');
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/code_mirror/docs.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
// Define Breadcrumb and tips
$xoBreadCrumb->addLink(_AM_SYSTEM_FILEMANAGER_NAV_MAIN, system_adminVersion('filemanager', 'adminpath'));

xoops_load('XoopsFile');
XoopsFile::load('file');

switch ($op) {
    default:
        // Assign Breadcrumb menu
        $xoBreadCrumb->addHelp(system_adminVersion('filemanager', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_FILEMANAGER_NAV_TIPS);
        $xoBreadCrumb->render();

        $xoopsTpl->assign('index', true);
        $xoopsTpl->debugging = false;

        $nbcolonnes_file = 4;
        $width           = 100 / $nbcolonnes_file;
        $root            = XOOPS_ROOT_PATH . '/';
        $url_file        = XOOPS_URL . '/';
        $xoopsTpl->assign('width', $width);

        if (file_exists($root)) {
            $files = scandir($root);
            natcasesort($files);
            if (count($files) > 2) {
                $count_file = 1;
                $file_arr   = array();
                $edit       = false;
                // All files
                foreach ($files as $file) {
                    if (!preg_match('#.back#', $file)) {
                        if (file_exists($root . $file) && $file !== '.' && $file !== '..' && !is_dir($root . $file)) {
                            $folder          = XoopsFile::getHandler('file', $root . $file);
                            $extension_verif = $folder->ext();

                            switch ($extension_verif) {
                                case 'ico':
                                case 'png':
                                case 'gif':
                                case 'jpg':
                                case 'jpeg':
                                    $extension_verif = 'picture';
                                    break;
                                case 'html':
                                case 'htm':
                                    $extension_verif = 'html';
                                    $edit            = true;
                                    break;
                                case 'zip':
                                case 'rar':
                                case 'tar':
                                case 'gz':
                                    $extension_verif = 'rar';
                                    $edit            = true;
                                    $unzip           = '<img class="cursorpointer" src="./images/icons/untar.png" onclick=\'filemanager_unzip_file("' . $path_file . $file . '", "' . $path_file . '", "' . $file . '");\' width="16" alt="edit" />&nbsp;';
                                    break;
                                case 'css':
                                    $extension_verif = 'css';
                                    $edit            = true;
                                    break;
                                case 'avi':
                                case 'mov':
                                case 'real':
                                case 'flv':
                                case 'swf':
                                    $extension_verif = 'movie';
                                    break;
                                case 'log':
                                    $extension_verif = 'log';
                                    $edit            = true;
                                    break;
                                case 'php':
                                    $extension_verif = 'php';
                                    $edit            = true;
                                    break;
                                case 'info':
                                case 'htaccess':
                                    $extension_verif = 'info';
                                    break;
                                case 'sql':
                                    $extension_verif = 'sql';
                                    $edit            = true;
                                    break;
                                default:
                                    $extension_verif = 'file';
                                    $edit            = true;
                                    break;
                            }

                            //Edit ?
                            $file_arr['edit'] = $edit;
                            //File
                            $file_arr['path_file'] = $root . $file;
                            $file_arr['path']      = $root;
                            //Chmod
                            $file_arr['chmod'] = substr($folder->perms(), 1);

                            $file_arr['chmod'] = modify_chmod($file_arr['chmod'], $file_arr['path_file'], $count_file);

                            if ($extension_verif === 'picture') {
                                list($width, $height) = getimagesize($root . $file);
                                if ($height > 60) {
                                    $file_arr['img'] = '<img src="' . $url_file . $file . '" height="47" title="" alt="" />';
                                } else {
                                    $file_arr['img'] = '<img src="' . $url_file . $file . '" title="" alt="" />';
                                }
                            } else {
                                $file_arr['img'] = '<img src="./images/mimetypes/' . $extension_verif . '_48.png" title="" alt="" />';
                            }
                            $file_arr['extension'] = $extension_verif;
                            $file_arr['file']      = htmlentities($file);
                            $count_file++;
                            $file_arr['newline'] = ($count_file % $nbcolonnes_file == 1) ? true : false;
                            $xoopsTpl->assign('newline', $file_arr['newline']);
                            $xoopsTpl->append('files', $file_arr);
                        }
                        $edit = false;
                    }
                }
            }
        }
        break;

    //save
    case 'filemanager_save':
        //Save the file or restore file
        if (isset($_REQUEST['path_file'])) {
            //save file
            $copy_file = $_REQUEST['path_file'];
            copy($copy_file, $_REQUEST['path'] . $_REQUEST['file'] . '.back');
            //Save modif
            if (isset($_REQUEST['filemanager'])) {
                $open = fopen('' . $_REQUEST['path_file'] . '', 'w+');
                if (!fwrite($open, utf8_encode(stripslashes($_REQUEST['filemanager'])))) {
                    redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_ERROR);
                }
                fclose($open);
            }
            redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_DBUPDATED);
        } else {
            //restore
            $old_file = $_REQUEST['path_file'] . '.back';
            //echo $old_file;
            $new_file = $_REQUEST['path_file'];
            //echo $new_file;
            if (file_exists($old_file)) {
                if (unlink($new_file)) {
                    if (rename($old_file, $new_file)) {
                        redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_DBUPDATED);
                    } else {
                        redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_RESTORE_ERROR_FILE_RENAME);
                    }
                } else {
                    redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_RESTORE_ERROR_FILE_DELETE);
                }
            } else {
                redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_RESTORE_ERROR_FILE_EXISTS);
            }
        }

        break;

    case 'filemanager_upload_save':
        if ($_REQUEST['path'] != '') {
            $path = trim($_REQUEST['path']);
        } else {
            $path = XOOPS_ROOT_PATH . '/';
        }
        include_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $mimetypes = include $GLOBALS['xoops']->path('include/mimetypes.inc.php');
        $uploader  = new XoopsMediaUploader($path, $mimetypes, 500000);
        if ($uploader->fetchMedia('upload_file')) {
            if (!$uploader->upload()) {
                $err[] =& $uploader->getErrors();
            }
        }
        if (isset($err)) {
            foreach ($err as $line) {
                echo $line;
            }
        }
        redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_UPLOAD_FILE);
        break;

    case 'filemanager_add_dir_save':
        $path = system_CleanVars($_REQUEST, 'path', XOOPS_ROOT_PATH . '/', 'string');

        xoops_load('XoopsFile');
        XoopsFile::load('folder');
        $folder = XoopsFile::getHandler('folder');
        if ($folder->create($path . $_REQUEST['dir_name'], 0777)) {
            $indexFile = XOOPS_ROOT_PATH . '/modules/system/index.html';
            copy($indexFile, $path . $_REQUEST['dir_name'] . '/index.html');
            redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_DIR_SUCCESS);
        } else {
            redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_DIR_ERROR);
        }
        break;

    case 'filemanager_add_file_save':
        $path = system_CleanVars($_REQUEST, 'path', XOOPS_ROOT_PATH . '/', 'string');
        if ($path == '') {
            $path = XOOPS_ROOT_PATH . '/';
        }
        $open = fopen($path . $_REQUEST['file_name'], 'w+');
        fclose($open);
        redirect_header('admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_FILE_SUCCESS);
        //if ($file->create ($path . $_REQUEST['file_name'])) {
        //    redirect_header( 'admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_DIR_SUCCESS );
        //} else {
        //  redirect_header( 'admin.php?fct=filemanager', 2, _AM_SYSTEM_FILEMANAGER_DIR_ERROR );
        //}
        break;
}

xoops_cp_footer();
