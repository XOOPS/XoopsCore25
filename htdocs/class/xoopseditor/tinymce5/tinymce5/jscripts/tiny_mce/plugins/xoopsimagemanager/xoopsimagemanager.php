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
 * XoopsImageManager plugin for tinymce v5
 *
 * @copyright      XOOPS Project  (https://xoops.org)
 * @license        GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author         ForMuss
 */

$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$xoops_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/xoopseditor/tinymce5/'));
include_once $xoops_root_path . '/mainfile.php';
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * This code was moved to the top to avoid overriding variables that do not come from post
 */
$op = 'list'; // default
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

// get current filename
$current_file = basename(__FILE__);

// load language definitions
xoops_loadLanguage('admin', 'system');
xoops_loadLanguage('/admin/images', 'system');

// include
xoops_load('xoopsformloader');
//xoops_load("xoopsmodule");
include_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
include_once XOOPS_ROOT_PATH . '/modules/system/constants.php';

global $xoopsConfig;

// check user/group - start
$isadmin = false;

$gperm_handler = xoops_getHandler('groupperm');
$groups        = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
$isadmin       = $gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_IMAGE, $groups);

// check categories readability/writability
$imgcat_handler = xoops_getHandler('imagecategory');
$catreadlist    = $imgcat_handler->getList($groups, 'imgcat_read', 1);    // get readable categories
$catwritelist   = $imgcat_handler->getList($groups, 'imgcat_write', 1);  // get writable categories

$catreadcount  = count($catreadlist);        // count readable categories
$catwritecount = count($catwritelist);      // count writable categories

include_once __DIR__ . '/XoopsFormRendererBootstrap4.php';
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap4());

// check/set parameters - start
if (!isset($_REQUEST['target'])) {
    exit();
} else {
    $target = $_REQUEST['target'];
}

if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}

if (isset($_GET['target'])) {
    $target = trim($_GET['target']);
}

if (isset($_GET['image_id'])) {
    $image_id = (int)$_GET['image_id'];
}

if (isset($_GET['imgcat_id'])) {
    $imgcat_id = (int)$_GET['imgcat_id'];
}

if (isset($imgcat_id)) {
    $imgcat_id = (int)$imgcat_id;
}
$target = htmlspecialchars($target, ENT_QUOTES);

if ($isadmin || ($catreadcount > 0) || ($catwritecount > 0)) {
    // Add new image - start
    if (!empty($_POST['op']) && $op === 'addfile') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($current_file . '?target=' . $target, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat = $imgcat_handler->get((int)$imgcat_id);
        if (!is_object($imgcat)) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        include_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, array(
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/x-png',
            'image/png',
            'image/bmp'), $imgcat->getVar('imgcat_maxsize'), $imgcat->getVar('imgcat_maxwidth'), $imgcat->getVar('imgcat_maxheight'));
        $uploader->setPrefix('img');
        $err    = array();
        $ucount = count($_POST['xoops_upload_file']);
        for ($i = 0; $i < $ucount; ++$i) {
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                if (!$uploader->upload()) {
                    $err[] = $uploader->getErrors();
                } else {
                    $image_handler = xoops_getHandler('image');
                    $image         = $image_handler->create();
                    $image->setVar('image_name', $uploader->getSavedFileName());
                    $image->setVar('image_nicename', $image_nicename);
                    $image->setVar('image_mimetype', $uploader->getMediaType());
                    $image->setVar('image_created', time());
                    $image_display = empty($image_display) ? 0 : 1;
                    $image->setVar('image_display', $image_display);
                    $image->setVar('image_weight', $image_weight);
                    $image->setVar('imgcat_id', $imgcat_id);
                    if ($imgcat->getVar('imgcat_storetype') === 'db') {
                        $fp      = @fopen($uploader->getSavedDestination(), 'rb');
                        $fbinary = @fread($fp, filesize($uploader->getSavedDestination()));
                        @fclose($fp);
                        $image->setVar('image_body', $fbinary, true);
                        @unlink($uploader->getSavedDestination());
                    }
                    if (!$image_handler->insert($image)) {
                        $err[] = sprintf(_FAILSAVEIMG, $image->getVar('image_nicename'));
                    }
                }
            } else {
                $err[] = sprintf(_FAILFETCHIMG, $i);
                $err   = array_merge($err, $uploader->getErrors(false));
            }
        }
        if (count($err) > 0) {
            redirect_header($current_file . '?target=' . $target, 3, xoops_error(implode('<br>', $err)));
        }
        redirect_header($current_file . '?target=' . $target, 3, _AM_SYSTEM_DBUPDATED);
    }

    // Add new category - start
    if (!empty($_POST['op']) && $op === 'addcat') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($current_file . '?target=' . $target, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->create();
        $imagecategory->setVar('imgcat_name', $imgcat_name);
        $imagecategory->setVar('imgcat_maxsize', $imgcat_maxsize);
        $imagecategory->setVar('imgcat_maxwidth', $imgcat_maxwidth);
        $imagecategory->setVar('imgcat_maxheight', $imgcat_maxheight);
        $imgcat_display = empty($imgcat_display) ? 0 : 1;
        $imagecategory->setVar('imgcat_display', $imgcat_display);
        $imagecategory->setVar('imgcat_weight', $imgcat_weight);
        $imagecategory->setVar('imgcat_storetype', $imgcat_storetype);
        $imagecategory->setVar('imgcat_type', 'C');
        if (!$imgcat_handler->insert($imagecategory)) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        $newid                     = $imagecategory->getVar('imgcat_id');
        $imagecategoryperm_handler = xoops_getHandler('groupperm');
        if (!isset($readgroup)) {
            $readgroup = array();
        }
        if (!in_array(XOOPS_GROUP_ADMIN, $readgroup)) {
            array_push($readgroup, XOOPS_GROUP_ADMIN);
        }
        foreach ($readgroup as $rgroup) {
            $imagecategoryperm = $imagecategoryperm_handler->create();
            $imagecategoryperm->setVar('gperm_groupid', $rgroup);
            $imagecategoryperm->setVar('gperm_itemid', $newid);
            $imagecategoryperm->setVar('gperm_name', 'imgcat_read');
            $imagecategoryperm->setVar('gperm_modid', 1);
            $imagecategoryperm_handler->insert($imagecategoryperm);
            unset($imagecategoryperm);
        }
        if (!isset($writegroup)) {
            $writegroup = array();
        }
        if (!in_array(XOOPS_GROUP_ADMIN, $writegroup)) {
            array_push($writegroup, XOOPS_GROUP_ADMIN);
        }
        foreach ($writegroup as $wgroup) {
            $imagecategoryperm = $imagecategoryperm_handler->create();
            $imagecategoryperm->setVar('gperm_groupid', $wgroup);
            $imagecategoryperm->setVar('gperm_itemid', $newid);
            $imagecategoryperm->setVar('gperm_name', 'imgcat_write');
            $imagecategoryperm->setVar('gperm_modid', 1);
            $imagecategoryperm_handler->insert($imagecategoryperm);
            unset($imagecategoryperm);
        }
        redirect_header($current_file . '?target=' . $target, 3, _AM_SYSTEM_DBUPDATED);
    }
    // Add new category - end

    // Update categorie - start
    if (!empty($_POST['op']) && $op === 'updatecat') {
        if (!$GLOBALS['xoopsSecurity']->check() || $imgcat_id <= 0) {
            redirect_header($current_file . '?target=' . $target, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        $imagecategory->setVar('imgcat_name', $imgcat_name);
        $imgcat_display = empty($imgcat_display) ? 0 : 1;
        $imagecategory->setVar('imgcat_display', $imgcat_display);
        $imagecategory->setVar('imgcat_maxsize', $imgcat_maxsize);
        $imagecategory->setVar('imgcat_maxwidth', $imgcat_maxwidth);
        $imagecategory->setVar('imgcat_maxheight', $imgcat_maxheight);
        $imagecategory->setVar('imgcat_weight', $imgcat_weight);
        if (!$imgcat_handler->insert($imagecategory)) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        $imagecategoryperm_handler = xoops_getHandler('groupperm');
        $criteria                  = new CriteriaCompo(new Criteria('gperm_itemid', $imgcat_id));
        $criteria->add(new Criteria('gperm_modid', 1));
        $criteria2 = new CriteriaCompo(new Criteria('gperm_name', 'imgcat_write'));
        $criteria2->add(new Criteria('gperm_name', 'imgcat_read'), 'OR');
        $criteria->add($criteria2);
        $imagecategoryperm_handler->deleteAll($criteria);
        if (!isset($readgroup)) {
            $readgroup = array();
        }
        if (!in_array(XOOPS_GROUP_ADMIN, $readgroup)) {
            array_push($readgroup, XOOPS_GROUP_ADMIN);
        }
        foreach ($readgroup as $rgroup) {
            $imagecategoryperm = $imagecategoryperm_handler->create();
            $imagecategoryperm->setVar('gperm_groupid', $rgroup);
            $imagecategoryperm->setVar('gperm_itemid', $imgcat_id);
            $imagecategoryperm->setVar('gperm_name', 'imgcat_read');
            $imagecategoryperm->setVar('gperm_modid', 1);
            $imagecategoryperm_handler->insert($imagecategoryperm);
            unset($imagecategoryperm);
        }
        if (!isset($writegroup)) {
            $writegroup = array();
        }
        if (!in_array(XOOPS_GROUP_ADMIN, $writegroup)) {
            array_push($writegroup, XOOPS_GROUP_ADMIN);
        }
        foreach ($writegroup as $wgroup) {
            $imagecategoryperm = $imagecategoryperm_handler->create();
            $imagecategoryperm->setVar('gperm_groupid', $wgroup);
            $imagecategoryperm->setVar('gperm_itemid', $imgcat_id);
            $imagecategoryperm->setVar('gperm_name', 'imgcat_write');
            $imagecategoryperm->setVar('gperm_modid', 1);
            $imagecategoryperm_handler->insert($imagecategoryperm);
            unset($imagecategoryperm);
        }
        redirect_header($current_file . '?target=' . $target, 3, _AM_SYSTEM_DBUPDATED);
    }
    // Update categorie - end

    // Confirm delete categorie - start
    if (!empty($_GET['op']) && $op === 'delcat') {
        xoops_header();
        echo "<link href='css/xoopsimagebrowser.css' rel='stylesheet' type='text/css' />";
        xoops_confirm(array('op' => 'delcatok', 'imgcat_id' => $imgcat_id, 'target' => $target), $current_file, _MD_RUDELIMGCAT);
        xoops_footer();
        exit();
    }
    // Confirm delete categorie - end

    // Delete categorie - start
    if (!empty($_POST['op']) && $op === 'delcatok') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($current_file . '?target=' . $target, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat_id = (int)$imgcat_id;
        if ($imgcat_id <= 0) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        if ($imagecategory->getVar('imgcat_type') !== 'C') {
            redirect_header($current_file . '?target=' . $target, 3, _MD_SCATDELNG);
        }
        $image_handler = xoops_getHandler('image');
        $images        = $image_handler->getObjects(new Criteria('imgcat_id', $imgcat_id), true, false);
        $errors        = array();
        foreach (array_keys($images) as $i) {
            if (!$image_handler->delete($images[$i])) {
                $errors[] = sprintf(_MD_FAILDEL, $i);
            } else {
                if (file_exists(XOOPS_UPLOAD_PATH . '/' . $images[$i]->getVar('image_name')) && !unlink(XOOPS_UPLOAD_PATH . '/' . $images[$i]->getVar('image_name'))) {
                    $errors[] = sprintf(_MD_FAILUNLINK, $i);
                }
            }
        }
        if (!$imgcat_handler->delete($imagecategory)) {
            $errors[] = sprintf(_MD_FAILDELCAT, $imagecategory->getVar('imgcat_name'));
        }
        if (count($errors) > 0) {
            redirect_header($current_file . '?target=' . $target, 3, xoops_error(implode('<br>', $error)));
        }
        redirect_header($current_file . '?target=' . $target, 3, _AM_SYSTEM_DBUPDATED);
    }
    // Delete categorie - end

    // ************************* NOT USED ************************************
    // Confirm delete file - start
    if (!empty($_GET['op']) && $op === 'delfile') {
        xoops_header();
        echo "<link href='css/xoopsimagebrowser.css' rel='stylesheet' type='text/css' />";
        xoops_confirm(array('op' => 'delfileok', 'image_id' => $image_id, 'target' => $target), $current_file, _MD_RUDELIMG);
        xoops_footer();
        exit();
    }
    // Confirm delete file - end

    // Delete file - start
    if ($op === 'delfileok') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($current_file . '?target=' . $target, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $image_id = (int)$image_id;
        if ($image_id <= 0) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        $image_handler = xoops_getHandler('image');
        $image         = $image_handler->get($image_id);
        if (!is_object($image)) {
            redirect_header($current_file . '?target=' . $target, 3);
        }
        if (!$image_handler->delete($image)) {
            redirect_header($current_file . '?target=' . $target, 3, xoops_error(sprintf(_MD_FAILDEL, $image->getVar('image_id'))));
        }
        @unlink(XOOPS_UPLOAD_PATH . '/' . $image->getVar('image_name'));
        redirect_header($current_file . '?target=' . $target, 3, _AM_SYSTEM_DBUPDATED);
    }
}

$GLOBALS['xoopsLogger']->activated = false;

echo '<!doctype html>';
echo '<html lang="' . _LANGCODE . '">';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />';
echo '<meta http-equiv="content-language" content="' . _LANGCODE . '" />';
echo '<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>';
echo '<link href="css/xoopsimagemanager.css" rel="stylesheet" type="text/css"/>';
echo '<link href="' . xoops_getcss($xoopsConfig['theme_set']) . '" rel="stylesheet" type="text/css"/>';
echo '<script src="' . XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js"></script>';
echo '<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>';
echo '</head>';
echo '<body>';
echo '<div class="container-fluid pt-1">';

echo '<ul class="nav nav-tabs" id="imgTabs" role="tablist">';
echo '<li class="nav-item"><a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
aria-selected="true">';
if ($op === 'listimg') { 
    echo _AM_SYSTEM_IMAGES_IMGLIST; 
} else { 
    echo _AM_SYSTEM_IMAGES_CATLIST; 
} 
echo '</a></li>';
if (!empty($catwritelist)) {
    echo '<li class="nav-item"><a class="nav-link" id="addimg-tab" data-toggle="tab" href="#addimg" role="tab" aria-controls="img" aria-selected="true">';
    echo _AM_SYSTEM_IMAGES_ADDIMG;
    echo '</a></li>';
}
if ($isadmin) {
    echo '<li class="nav-item"><a class="nav-link" id="addcat-tab" data-toggle="tab" href="#addcat" role="tab" aria-controls="cat" aria-selected="true">';
    echo _AM_SYSTEM_IMAGES_ADDCAT;
    echo '</a></li>';
}
echo '</ul>';

echo '<div class="tab-content" id="myTabContent">';
echo '<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">';
echo '<div class="row">';
echo '<div class="col p-4">';
if ($op === 'list') {
    if (!empty($catreadlist)) {
        echo '<table class="table table-borderless table-sm">';
        echo '<tbody>';

        // get all categories
        $imagecategories = $imgcat_handler->getObjects();
        $catcount        = count($imagecategories);
        $image_handler   = xoops_getHandler('image');
        for ($i = 0; $i < $catcount; ++$i) {
            echo '<tr>';
            if (in_array($imagecategories[$i]->getVar('imgcat_id'), array_keys($catreadlist))) {
                // count images stored in this category
                $this_imgcat_id   = $imagecategories[$i]->getVar('imgcat_id');
                $countimagesincat = $image_handler->getCount(new Criteria('imgcat_id', $this_imgcat_id));
                echo '<td><a href="' . $current_file . '?target=' . $target . '&amp;op=listimg&amp;imgcat_id=' . $this_imgcat_id . '">' . $imagecategories[$i]->getVar('imgcat_name') . '</a></td>';
                echo '<td>' . $countimagesincat . ' ' . _AM_SYSTEM_IMAGES_NBIMAGES . '</td>';
                echo '<td class="xo-actions txtcenter"><a href="' . $current_file . '?target=' . $target . '&amp;op=listimg&amp;imgcat_id=' . $this_imgcat_id . '"><img src="images/display.png" data-toggle="tooltip" alt="' . _LIST . '" title="' . _LIST . '"></a>';
                if ($isadmin) {
                    echo '&nbsp;<a href="' . $current_file . '?target=' . $target . '&amp;op=editcat&amp;imgcat_id=' . $this_imgcat_id . '"><img src="images/edit.png" data-toggle="tooltip" alt="' . _EDIT . '" title="' . _EDIT . '"></a>';
                }
                if ($isadmin && $imagecategories[$i]->getVar('imgcat_type') === 'C') {
                    echo '&nbsp;<a href="' . $current_file . '?target=' . $target . '&amp;op=delcat&amp;imgcat_id=' . $this_imgcat_id . '"><img src="images/delete.png" data-toggle="tooltip" alt="' . _DELETE . '" title="' . _DELETE . '"></a>';
                }
            }
            echo '<td></tr>';
        }
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<div class="alert alert-danger" role="alert">' . _AM_SYSTEM_IMAGES_NOCAT . '</div>';
    }
}

if ($op === 'listimg') {
    $imgcat_id = (int)$imgcat_id;
    if ($imgcat_id <= 0) {
        redirect_header($current_file . '?target=' . $target, 1);
    }
    $imgcat_handler = xoops_getHandler('imagecategory');
    $imagecategory  = $imgcat_handler->get($imgcat_id);
    if (!is_object($imagecategory)) {
        redirect_header($current_file . '?target=' . $target, 1);
    }
    $image_handler = xoops_getHandler('image');

    $criteria = new Criteria('imgcat_id', $imgcat_id);
    $imgcount = $image_handler->getCount($criteria);
    $start    = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $criteria->setStart($start);
    $criteria->setSort('image_id');
    $criteria->setOrder('DESC');
    $criteria->setLimit(20);
    $images = $image_handler->getObjects($criteria, true, false);

    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">';
    echo '<li class="breadcrumb-item"><a href="' . $current_file . '?target=' . $target . '">' . _MD_IMGMAIN . '</a></li>';
    echo '<li class="breadcrumb-item active" aria-current="page">' . $imagecategory->getVar('imgcat_name') . '</li>';
    echo '</ol>';
    echo '</nav>';

    echo '<div class="row card-imagemanager">';
    foreach (array_keys($images) as $i) {
        $image_src = '';
        // check if image stored in db/as file - start
        if ($imagecategory->getVar('imgcat_storetype') === 'db') {
            $image_src = '' . XOOPS_URL . '/image.php?id=' . $i . '';
            if (ini_get('allow_url_fopen') == true){
                $image_info = true;
                $image_size = getimagesize($image_src);
            } else {
                $image_info = false;
            }
        } else {
            $image_src = '' . XOOPS_UPLOAD_URL . '/' . $images[$i]->getVar('image_name') . '';
            $image_size = getimagesize(XOOPS_ROOT_PATH . '/uploads/' . $images[$i]->getVar('image_name'));
            $image_info = true;
        }

        echo '<div class="col mb-2">';
        echo '<div class="card h-100">';
        echo '<img class="card-img-top xoopsimg" style="cursor: pointer;" src="' . $image_src . '" alt="' . $images[$i]->getVar('image_nicename', 'E') . '" title="' . $images[$i]->getVar('image_nicename', 'E') . '">';
        echo '<div class="card-body">';
        echo '<h6>' . $images[$i]->getVar('image_nicename', 'E') . '</h6>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';

}

if ($op === 'editcat') {
    if ($imgcat_id <= 0) {
        redirect_header($current_file . '?target=' . $target, 1);
    }
    $imgcat_handler = xoops_getHandler('imagecategory');
    $imagecategory  = $imgcat_handler->get($imgcat_id);
    if (!is_object($imagecategory)) {
        redirect_header($current_file . '?target=' . $target, 1);
    }
    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $imagecategoryperm_handler = xoops_getHandler('groupperm');
    $form                      = new XoopsThemeForm('', 'imagecat_form', '' . $current_file . '?target=' . $target . '', 'post', true);
    $form->addElement(new XoopsFormText(_MD_IMGCATNAME, 'imgcat_name', 50, 255, $imagecategory->getVar('imgcat_name')), true);
    $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATRGRP, 'readgroup', true, $imagecategoryperm_handler->getGroupIds('imgcat_read', $imgcat_id), 5, true));
    $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATWGRP, 'writegroup', true, $imagecategoryperm_handler->getGroupIds('imgcat_write', $imgcat_id), 5, true));
    $form->addElement(new XoopsFormText(_IMGMAXSIZE, 'imgcat_maxsize', 10, 10, $imagecategory->getVar('imgcat_maxsize')));
    $form->addElement(new XoopsFormText(_IMGMAXWIDTH, 'imgcat_maxwidth', 3, 4, $imagecategory->getVar('imgcat_maxwidth')));
    $form->addElement(new XoopsFormText(_IMGMAXHEIGHT, 'imgcat_maxheight', 3, 4, $imagecategory->getVar('imgcat_maxheight')));
    $form->addElement(new XoopsFormText(_MD_IMGCATWEIGHT, 'imgcat_weight', 3, 4, $imagecategory->getVar('imgcat_weight')));
    $form->addElement(new XoopsFormRadioYN(_MD_IMGCATDISPLAY, 'imgcat_display', $imagecategory->getVar('imgcat_display'), _YES, _NO));
    $storetype = array('db' => _MD_INDB, 'file' => _MD_ASFILE);
    $form->addElement(new XoopsFormLabel(_MD_IMGCATSTRTYPE, $storetype[$imagecategory->getVar('imgcat_storetype')]));
    $form->addElement(new XoopsFormHidden('imgcat_id', $imgcat_id));
    $form->addElement(new XoopsFormHidden('op', 'updatecat'));
    $form->addElement(new XoopsFormButton('', 'imgcat_button', _SUBMIT, 'submit'));
    
    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">';
    echo '<li class="breadcrumb-item"><a href="' . $current_file . '?target=' . $target . '">' . _MD_IMGMAIN . '</a></li>';
    echo '<li class="breadcrumb-item active" aria-current="page">' . $imagecategory->getVar('imgcat_name') . '</li>';
    echo '</ol>';
    echo '</nav>';

    $form->display();
}

echo '</div>';
echo '</div>';

echo '</div>'; // Tab home

echo '<div class="tab-pane fade" id="addimg" role="tabpanel" aria-labelledby="home-tab">';
echo '<div class="row">';
echo '<div class="col p-4">';

$form = new XoopsThemeForm('', 'image_form', '' . $current_file . '?target=' . $target . '', 'post', true);
$form->setExtra('enctype="multipart/form-data"');

$form->addElement(new XoopsFormText(_IMAGENAME, 'image_nicename', 50, 255), true);
$select = new XoopsFormSelect(_IMAGECAT, 'imgcat_id');
if ($isadmin) {
    $select->addOptionArray($imgcat_handler->getList());
} else {
    $select->addOptionArray($catwritelist);
}
$form->addElement($select, true);
$form->addElement(new XoopsFormFile(_IMAGEFILE, 'image_file', 5000000));
$form->addElement(new XoopsFormText(_IMGWEIGHT, 'image_weight', 3, 4, 0));
$form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'image_display', 1, _YES, _NO));
$form->addElement(new XoopsFormHidden('op', 'addfile'));
$form->addElement(new XoopsFormButton('', 'img_button', _SUBMIT, 'submit'));
echo $form->display();

echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="tab-pane fade" id="addcat" role="tabpanel" aria-labelledby="addcat-tab">';
echo '<div class="row">';
echo '<div class="col p-4">';

    $form = new XoopsThemeForm('', 'imagecat_form', '' . $current_file . '?target=' . $target . '', 'post', true);
    $form->addElement(new XoopsFormText(_MD_IMGCATNAME, 'imgcat_name', 50, 255), true);
    $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATRGRP, 'readgroup', true, XOOPS_GROUP_ADMIN, 5, true));
    $form->addElement(new XoopsFormSelectGroup(_MD_IMGCATWGRP, 'writegroup', true, XOOPS_GROUP_ADMIN, 5, true));
    $form->addElement(new XoopsFormText(_IMGMAXSIZE, 'imgcat_maxsize', 10, 10, 50000));
    $form->addElement(new XoopsFormText(_IMGMAXWIDTH, 'imgcat_maxwidth', 3, 4, 120));
    $form->addElement(new XoopsFormText(_IMGMAXHEIGHT, 'imgcat_maxheight', 3, 4, 120));
    $form->addElement(new XoopsFormText(_MD_IMGCATWEIGHT, 'imgcat_weight', 3, 4, 0));
    $form->addElement(new XoopsFormRadioYN(_MD_IMGCATDISPLAY, 'imgcat_display', 1, _YES, _NO));
    $storetype = new XoopsFormRadio(_MD_IMGCATSTRTYPE . '<br><span style="color:#ff0000;">' . _MD_STRTYOPENG . '</span>', 'imgcat_storetype', 'file');
    $storetype->addOptionArray(array('file' => _MD_ASFILE, 'db' => _MD_INDB));
    $form->addElement($storetype);
    $form->addElement(new XoopsFormHidden('op', 'addcat'));
    $form->addElement(new XoopsFormButton('', 'imgcat_button', _SUBMIT, 'submit'));
    $form->display();

echo '</div>';
echo '</div>';
echo '</div>';


echo '</div>'; // tab-content

echo '</div>';
echo '</body>';
?>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    var elements = document.getElementsByClassName('xoopsimg');

    var myFunction = function() {
        var img = this.src;
        var title = this.title;

        window.parent.postMessage({
            mceAction: 'insertImage',
            data: {
                src: img,
                title: title
            }
        }, origin);
    };

    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', myFunction, false);
    }

</script>
<?php
xoops_footer();
