<?php
/**
 * XOOPS image manager
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
 * @package             core
 * @since               2.0.0
 */
include __DIR__ . '/mainfile.php';
XoopsLoad::load('XoopsRequest');
 
// Get Action type
$op = XoopsRequest::getCmd('op', 'list');

switch ($op) {
    case 'list':
        default:
        XoopsLoad::load('XoopsFilterInput');
        if (isset($_REQUEST['target'])) {
            $target = trim(XoopsFilterInput::clean($_REQUEST['target'], 'WORD'));
        } else {
            exit('Target not set');
        }
        if (!is_object($xoopsUser)) {
            $group = array(XOOPS_GROUP_ANONYMOUS);
        } else {
            $group = $xoopsUser->getGroups();
        }
        require_once $GLOBALS['xoops']->path('class/template.php');
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->assign('lang_imgmanager', _IMGMANAGER);
        $xoopsTpl->assign('sitename', htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
        $target = htmlspecialchars($target, ENT_QUOTES);
        $xoopsTpl->assign('target', $target);
        $imgcat_handler = xoops_getHandler('imagecategory');
        $catlist        = $imgcat_handler->getList($group, 'imgcat_read', 1);
        $catcount       = count($catlist);
        $xoopsTpl->assign('lang_align', _ALIGN);
        $xoopsTpl->assign('lang_add', _ADD);
        $xoopsTpl->assign('lang_close', _CLOSE);
        if ($catcount > 0) {
            $xoopsTpl->assign('lang_go', _GO);
            $catshow = (!isset($_GET['cat_id'])) ? 0 : (int)$_GET['cat_id'];
            //        $catshow = (!empty($catshow) && in_array($catshow, array_keys($catlist))) ? $catshow : 0;
            $catshow = (!empty($catshow) && array_key_exists($catshow, $catlist)) ? $catshow : 0;
            $xoopsTpl->assign('show_cat', $catshow);
            if ($catshow > 0) {
                $xoopsTpl->assign('lang_addimage', _ADDIMAGE);
            }
            $catlist     = array('0' => '--') + $catlist;
            $cat_options = '';
            foreach ($catlist as $c_id => $c_name) {
                $sel = '';
                if ($c_id == $catshow) {
                    $sel = ' selected';
                }
                $cat_options .= '<option value="' . $c_id . '"' . $sel . '>' . $c_name . '</option>';
            }
            $xoopsTpl->assign('cat_options', $cat_options);
            if ($catshow > 0) {
                $image_handler = xoops_getHandler('image');
                $criteria      = new CriteriaCompo(new Criteria('imgcat_id', $catshow));
                $criteria->add(new Criteria('image_display', 1));
                $total = $image_handler->getCount($criteria);
                if ($total > 0) {
                    $imgcat_handler = xoops_getHandler('imagecategory');
                    $imgcat         = $imgcat_handler->get($catshow);
                    $xoopsTpl->assign('image_total', $total);
                    $xoopsTpl->assign('lang_image', _IMAGE);
                    $xoopsTpl->assign('lang_imagename', _IMAGENAME);
                    $xoopsTpl->assign('lang_imagemime', _IMAGEMIME);
                    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
                    $criteria->setLimit(10);
                    $criteria->setStart($start);
                    $storetype = $imgcat->getVar('imgcat_storetype');
                    if ($storetype === 'db') {
                        $criteria->setSort('i.image_weight ASC, i.image_id');
                        $criteria->setOrder('DESC');
                        $images = $image_handler->getObjects($criteria, false, true);
                    } else {
                        $criteria->setSort('image_weight ASC, image_id');
                        $criteria->setOrder('DESC');
                        $images = $image_handler->getObjects($criteria, false, false);
                    }
                    $imgcount = count($images);
                    $max      = ($imgcount > 10) ? 10 : $imgcount;

                    for ($i = 0; $i < $max; ++$i) {
                        if ($storetype === 'db') {
                            $lcode = '[img align=left id=' . $images[$i]->getVar('image_id') . ']' . $images[$i]->getVar('image_nicename') . '[/img]';
                            $code  = '[img align=center id=' . $images[$i]->getVar('image_id') . ']' . $images[$i]->getVar('image_nicename') . '[/img]';
                            $rcode = '[img align=right id=' . $images[$i]->getVar('image_id') . ']' . $images[$i]->getVar('image_nicename') . '[/img]';
                            $src   = XOOPS_URL . '/image.php?id=' . $images[$i]->getVar('image_id');
                        } else {
                            $lcode = '[img align=left]' . XOOPS_UPLOAD_URL . '/' . $images[$i]->getVar('image_name') . '[/img]';
                            $code  = '[img align=center]' . XOOPS_UPLOAD_URL . '/' . $images[$i]->getVar('image_name') . '[/img]';
                            $rcode = '[img align=right]' . XOOPS_UPLOAD_URL . '/' . $images[$i]->getVar('image_name') . '[/img]';
                            $src   = XOOPS_UPLOAD_URL . '/' . $images[$i]->getVar('image_name');
                        }
                        $xoopsTpl->append('images', array(
                            'id'       => $images[$i]->getVar('image_id'),
                            'nicename' => $images[$i]->getVar('image_nicename'),
                            'mimetype' => $images[$i]->getVar('image_mimetype'),
                            'src'      => $src,
                            'lxcode'   => $lcode,
                            'xcode'    => $code,
                            'rxcode'   => $rcode));
                    }
                    if ($total > 10) {
                        include_once $GLOBALS['xoops']->path('class/pagenav.php');
                        $nav = new XoopsPageNav($total, 10, $start, 'start', 'target=' . $target . '&amp;cat_id=' . $catshow);
                        $xoopsTpl->assign('pagenav', $nav->renderNav());
                    }
                } else {
                    $xoopsTpl->assign('image_total', 0);
                }
            }
            $xoopsTpl->assign('xsize', 800);
            $xoopsTpl->assign('ysize', 600);
        } else {
            $xoopsTpl->assign('xsize', 400);
            $xoopsTpl->assign('ysize', 180);
        }
        $xoopsTpl->display('db:system_imagemanager.tpl');
        exit();
        break;

    case 'upload':
        XoopsLoad::load('XoopsFilterInput');
        if (isset($_REQUEST['target'])) {
            $target = trim(XoopsFilterInput::clean($_REQUEST['target'], 'WORD'));
        } else {
            exit('Target not set');
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imgcat_id      = (int)$_GET['imgcat_id'];
        $imgcat         = $imgcat_handler->get($imgcat_id);
        $error          = false;
        if (!is_object($imgcat)) {
            $error = true;
        } else {
            $imgcatperm_handler = xoops_getHandler('groupperm');
            if (is_object($xoopsUser)) {
                if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, $xoopsUser->getGroups())) {
                    $error = true;
                }
            } else {
                if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, XOOPS_GROUP_ANONYMOUS)) {
                    $error = true;
                }
            }
        }
        if ($error != false) {
            xoops_header(false);
            echo '</head><body><div style="text-align:center;"><input value="' . _BACK . '" type="button" onclick="history.go(-1);" /></div>';
            xoops_footer();
            exit();
        }
        require_once $GLOBALS['xoops']->path('class/template.php');
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->assign('show_cat', $imgcat_id);
        $xoopsTpl->assign('lang_imgmanager', _IMGMANAGER);
        $xoopsTpl->assign('sitename', htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
        $xoopsTpl->assign('target', htmlspecialchars($target, ENT_QUOTES));
        $xoopsTpl->assign('imgcat_maxsize', $imgcat->getVar('imgcat_maxsize'));
        $xoopsTpl->assign('imgcat_maxwidth', $imgcat->getVar('imgcat_maxwidth'));
        $xoopsTpl->assign('imgcat_maxheight', $imgcat->getVar('imgcat_maxheight'));
        $xoopsTpl->assign('imgcat_name', $imgcat->getVar('imgcat_name'));
        $xoopsTpl->assign('lang_close', _CLOSE);
        $xoopsTpl->display('db:system_imagemanager2.tpl');
        exit(); 
        break;

    case 'doupload':
        $xoopsLogger->activated = false;
        // Include the upload handler class
        require_once __DIR__ . '/Frameworks/fine-uploader/handler.php';

        // Get imgcat_id
        $imgcat_id = XoopsRequest::getInt('imgcat_id', 0);

        $imgcat_handler = xoops_getHandler('imagecategory');
        $imgcat         = $imgcat_handler->get($imgcat_id);
        $error          = false;
        if (!is_object($imgcat)) {
            $error = true;
        } else {
            $imgcatperm_handler = xoops_getHandler('groupperm');
            if (is_object($xoopsUser)) {
                if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, $xoopsUser->getGroups())) {
                    $error = true;
                }
            } else {
                if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, XOOPS_GROUP_ANONYMOUS)) {
                    $error = true;
                }
            }
        }
        if ($error != false) {
            // erreur
            //header('location: ' . XOOPS_URL . 'imagemanager.php?op=list');
            exit();
        }
        //config
        $uploader = new UploadHandler();
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array(); // all files types allowed by default
        // Specify max file size in bytes.
        $uploader->sizeLimit = null;
        // Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
        // If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = __DIR__ . '/uploads/fine-uploader/files';

        $method = get_request_method();
        
        if ($method == "POST") {
            header("Content-Type: text/plain");

            // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
            // For example: /myserver/handlers/endpoint.php?done
            if (isset($_GET["done"])) {
                $result = $uploader->combineChunks(__DIR__ . '/uploads/images');
            }
            // Handles upload requests
            else {
                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload(__DIR__ . '/uploads/images', null, 'img');
                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();
            }
            echo json_encode($result);
            
            $nicename = substr($uploader->getName(), 0, strrpos ($uploader->getName(), '.'));
            $mimetype = mime_content_type(__DIR__ . '/uploads/images/' . $uploader->getUploadName());
            
            //save image	
            $image_handler = xoops_getHandler('image');
            $image         = $image_handler->create();
            $image->setVar('image_name', 'images/' . $uploader->getUploadName());
            $image->setVar('image_nicename', $nicename);
            $image->setVar('image_mimetype', $mimetype);
            $image->setVar('image_created', time());
            $image->setVar('image_display', 1);
            $image->setVar('image_weight', 0);
            $image->setVar('imgcat_id', $imgcat_id);
            
            if ($imgcat->getVar('imgcat_storetype') === 'db') {
                $destination = __DIR__ . '/uploads/images/' . $uploader->getUploadName();
                $fp      = @fopen($destination, 'rb');
                $fbinary = @fread($fp, filesize($destination));
                @fclose($fp);
                $image->setVar('image_body', $fbinary, true);
                @unlink($destination);
            }
            if (!$image_handler->insert($image)) {
                $err = sprintf(_FAILSAVEIMG, $image->getVar('image_nicename'));
                // erreur
                //header('location: ' . XOOPS_URL . 'imagemanager.php?op=list');
                exit();
            }
            
        // for delete file requests        
        } else if ($method == "DELETE") {
            $result = $uploader->handleDelete("files");
            echo json_encode($result);
        }
        else {
            header("HTTP/1.0 405 Method Not Allowed");
        }
        break;
}

function get_request_method() {
    global $HTTP_RAW_POST_DATA;
    if(isset($HTTP_RAW_POST_DATA)) {
        parse_str($HTTP_RAW_POST_DATA, $_POST);
    }
    if (isset($_POST["_method"]) && $_POST["_method"] != null) {
        return $_POST["_method"];
    }
    return $_SERVER["REQUEST_METHOD"];
}