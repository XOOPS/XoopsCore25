<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */
/* @var  $xoopsUser XoopsUser */
/* @var $xoopsModule XoopsModule */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

//  Check is active
if (!xoops_getModuleOption('active_images', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');
if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}
if (isset($_GET['image_id'])) {
    $image_id = (int)$_GET['image_id'];
}
if (isset($_GET['imgcat_id'])) {
    $imgcat_id = (int)$_GET['imgcat_id'];
}

/* @var  $gperm_handler XoopsGroupPermHandler */
$gperm_handler = xoops_getHandler('groupperm');
$groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

// check READ right by category before continue
if (isset($imgcat_id) && $op === 'listimg') {
    $imgcat_read  = $gperm_handler->checkRight('imgcat_read', $imgcat_id, $groups, $xoopsModule->mid());
    $imgcat_write = $gperm_handler->checkRight('imgcat_write', $imgcat_id, $groups, $xoopsModule->mid());
    if (!$imgcat_read && !$imgcat_write) {
        redirect_header('admin.php?fct=images', 1);
    }
}

// check WRITE right by category before continue
if (isset($imgcat_id) && ($op === 'addfile' || $op === 'editcat' || $op === 'updatecat' || $op === 'delcatok' || $op === 'delcat')) {
    $imgcat_write = $gperm_handler->checkRight('imgcat_write', $imgcat_id, $groups, $xoopsModule->mid());
    if (!$imgcat_write) {
        redirect_header('admin.php?fct=images', 1);
    }
}

// Only website administator can delete categories or images

if (!$xoopsUser->isAdmin($xoopsModule->mid()) && ($op === 'delfile' || $op === 'delfileok' || $op === 'delcatok' || $op === 'delcat')) {
    redirect_header('admin.php?fct=images', 1);
}

switch ($op) {

    case 'list':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_images.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_MANAGER, system_adminVersion('images', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('images', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_IMAGES_TIPS);
        $xoBreadCrumb->render();

        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategorys = $imgcat_handler->getObjects();

        $catcount      = count($imagecategorys);
        /* @var  $image_handler XoopsImageHandler */
        $image_handler = xoops_getHandler('image');

        foreach (array_keys($imagecategorys) as $i) {
            $imgcat_read  = $gperm_handler->checkRight('imgcat_read', $imagecategorys[$i]->getVar('imgcat_id'), $groups, $xoopsModule->mid());
            $imgcat_write = $gperm_handler->checkRight('imgcat_write', $imagecategorys[$i]->getVar('imgcat_id'), $groups, $xoopsModule->mid());
            if ($imgcat_read || $imgcat_write) {
                $count = $image_handler->getCount(new Criteria('imgcat_id', $imagecategorys[$i]->getVar('imgcat_id')));

                $cat_images['id']        = $imagecategorys[$i]->getVar('imgcat_id');
                $cat_images['name']      = $imagecategorys[$i]->getVar('imgcat_name');
                $cat_images['count']     = $count;
                $cat_images['size']      = $count;
                $cat_images['maxsize']   = $imagecategorys[$i]->getVar('imgcat_maxsize');
                $cat_images['maxwidth']  = $imagecategorys[$i]->getVar('imgcat_maxwidth');
                $cat_images['maxheight'] = $imagecategorys[$i]->getVar('imgcat_maxheight');
                $cat_images['display']   = $imagecategorys[$i]->getVar('imgcat_display');
                $cat_images['weight']    = $imagecategorys[$i]->getVar('imgcat_weight');
                $cat_images['type']      = $imagecategorys[$i]->getVar('imgcat_type');
                $cat_images['store']     = $imagecategorys[$i]->getVar('imgcat_storetype');

                $xoopsTpl->append_by_ref('cat_img', $cat_images);
                unset($cat_images);
            }
        }
        // Image Form
        if (!empty($catcount)) {
            $form = new XoopsThemeForm(_ADDIMAGE, 'image_form', 'admin.php', 'post', true);
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new XoopsFormText(_IMAGENAME, 'image_nicename', 50, 255), true);
            $select = new XoopsFormSelect(_IMAGECAT, 'imgcat_id');
            $select->addOptionArray($imgcat_handler->getList($groups, 'imgcat_write'));
            $form->addElement($select, true);
            $form->addElement(new XoopsFormFile(_IMAGEFILE, 'image_file', 5000000));
            $form->addElement(new XoopsFormText(_IMGWEIGHT, 'image_weight', 3, 4, 0));
            $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'image_display', 1, _YES, _NO));
            $form->addElement(new XoopsFormHidden('op', 'addfile'));
            $form->addElement(new XoopsFormHidden('fct', 'images'));
            $form->addElement(new XoopsFormButton('', 'img_button', _SUBMIT, 'submit'));
            $form->assign($xoopsTpl);
        }
        // Category Form
        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $form = new XoopsThemeForm(_AM_SYSTEM_IMAGES_ADDCAT, 'imagecat_form', 'admin.php', 'post', true);
            $form->addElement(new XoopsFormText(_AM_SYSTEM_IMAGES_IMGCATNAME, 'imgcat_name', 50, 255), true);
            $form->addElement(new XoopsFormSelectGroup(_AM_SYSTEM_IMAGES_IMGCATRGRP, 'readgroup', true, XOOPS_GROUP_ADMIN, 5, true));
            $form->addElement(new XoopsFormSelectGroup(_AM_SYSTEM_IMAGES_IMGCATWGRP, 'writegroup', true, XOOPS_GROUP_ADMIN, 5, true));
            $form->addElement(new XoopsFormText(_IMGMAXSIZE, 'imgcat_maxsize', 10, 10, 1000000));
            $form->addElement(new XoopsFormText(_IMGMAXWIDTH, 'imgcat_maxwidth', 3, 4, 800));
            $form->addElement(new XoopsFormText(_IMGMAXHEIGHT, 'imgcat_maxheight', 3, 4, 600));
            $form->addElement(new XoopsFormText(_AM_SYSTEM_IMAGES_IMGCATWEIGHT, 'imgcat_weight', 3, 4, 0));
            $form->addElement(new XoopsFormRadioYN(_AM_SYSTEM_IMAGES_IMGCATDISPLAY, 'imgcat_display', 1, _YES, _NO));

            $storetype = new XoopsFormRadio(_MD_IMGCATSTRTYPE . '<br><span style="color:#ff0000;">' . _MD_STRTYOPENG . '</span>', 'imgcat_storetype', 'file');
            $storetype->addOptionArray(array('file' => _MD_ASFILE, 'db' => _MD_INDB));
            $form->addElement($storetype);

            //$form->addElement(new XoopsFormHidden('imgcat_storetype', 'file'));
            $form->addElement(new XoopsFormHidden('op', 'addcat'));
            $form->addElement(new XoopsFormHidden('fct', 'images'));
            $form->addElement(new XoopsFormButton('', 'imgcat_button', _SUBMIT, 'submit'));
            $form->assign($xoopsTpl);
        }
        // Call Footer
        xoops_cp_footer();
        break;

    case 'display_cat':
        // Get Image Category handler
        $imgcat_handler = xoops_getHandler('imagecategory');
        // Get category id
        $imgcat_id = system_CleanVars($_POST, 'imgcat_id', 0, 'int');
        if ($imgcat_id > 0) {
            $imgcat = $imgcat_handler->get($imgcat_id);
            $old    = $imgcat->getVar('imgcat_display');
            $imgcat->setVar('imgcat_display', !$old);
            if (!$imgcat_handler->insert($imgcat)) {
                $error = true;
            }
        }
        break;

    case 'listimg':
        // Get category id
        $imgcat_id = system_CleanVars($_REQUEST, 'imgcat_id', 0, 'int');
        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }
        // Get rights
        $imgcat_write = $gperm_handler->checkRight('imgcat_write', $imgcat_id, $groups, $xoopsModule->mid());
        // Get category handler
        $imgcat_handler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }
        // Get image handler
        $image_handler = xoops_getHandler('image');
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_images.tpl';
        // Call header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
        $xoTheme->addScript('modules/system/js/admin.js');

        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_MANAGER, system_adminVersion('images', 'adminpath'));
        $xoBreadCrumb->addLink($imagecategory->getVar('imgcat_name'));
        $xoBreadCrumb->addHelp(system_adminVersion('images', 'help') . '#cat');
        $xoBreadCrumb->addTips(_AM_SYSTEM_IMAGES_TIPS);
        $xoBreadCrumb->render();

        $criteria = new CriteriaCompo(new Criteria('imgcat_id', $imgcat_id));
        $criteria->setSort('image_weight ASC, image_id');
        $criteria->setOrder('DESC');
        $imgcount = $image_handler->getCount($criteria);
        $start    = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $criteria->setStart($start);
        $criteria->setLimit(xoops_getModuleOption('images_pager', 'system'));
        $images = $image_handler->getObjects($criteria, true, false);

        if ($imagecategory->getVar('imgcat_storetype') === 'db') {
            $xoopsTpl->assign('db_store', 1);
        }

        foreach ($images as $listImage) {
            $xoopsTpl->append('images', $listImage->toArray());
        }
        if ($imgcount > 0) {
            if ($imgcount > xoops_getModuleOption('images_pager', 'system')) {
                //include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
                $nav = new XoopsPageNav($imgcount, xoops_getModuleOption('images_pager', 'system'), $start, 'start', 'fct=images&amp;op=listimg&amp;imgcat_id=' . $imgcat_id);
                $xoopsTpl->assign('nav_menu', $nav->renderNav(4));
            }
        }

        if (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/' . $GLOBALS['xoopsConfig']['language'] . '/images/lightbox-btn-close.gif')) {
            $xoopsTpl->assign('xoops_language', $GLOBALS['xoopsConfig']['language']);
        } else {
            $xoopsTpl->assign('xoops_language', 'english');
        }
        $xoopsTpl->assign('listimg', true);
        $xoopsTpl->assign('imgcat_id', $imgcat_id);        

        // Image Form
        $form = new XoopsThemeForm(_ADDIMAGE, 'image_form', 'admin.php', 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement(new XoopsFormText(_IMAGENAME, 'image_nicename', 50, 255), true);
        $select = new XoopsFormSelect(_IMAGECAT, 'imgcat_id', $imgcat_id);
        $select->addOptionArray($imgcat_handler->getList($groups, 'imgcat_write'));
        $form->addElement($select, true);
        $form->addElement(new XoopsFormFile(_IMAGEFILE, 'image_file', 5000000));
        $form->addElement(new XoopsFormText(_IMGWEIGHT, 'image_weight', 3, 4, 0));
        $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'image_display', 1, _YES, _NO));
        $form->addElement(new XoopsFormHidden('op', 'addfile'));
        $form->addElement(new XoopsFormHidden('fct', 'images'));
        $form->addElement(new XoopsFormButton('', 'img_button', _SUBMIT, 'submit'));
        $form->assign($xoopsTpl);

        // Call Footer
        xoops_cp_footer();
        break;

    case 'display_img':
        // Get image handler
        $image_handler = xoops_getHandler('image');
        // Get image id
        $image_id = system_CleanVars($_POST, 'image_id', 0, 'int');
        if ($image_id > 0) {
            $img = $image_handler->get($image_id);
            $old = $img->getVar('image_display');
            $img->setVar('image_display', !$old);
            if (!$image_handler->insert($img)) {
                $error = true;
            }
        }
        break;

    case 'editimg':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_images.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Get image handler
        $image_handler  = xoops_getHandler('image');
        $imgcat_handler = xoops_getHandler('imagecategory');
        // Get image id
        $image_id = system_CleanVars($_REQUEST, 'image_id', 0, 'int');
        if ($image_id > 0) {
            $image     = $image_handler->get($image_id);
            $image_cat = $imgcat_handler->get($image->getVar('imgcat_id'));
            // Define Breadcrumb and tips
            $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_MANAGER, system_adminVersion('images', 'adminpath'));
            $xoBreadCrumb->addLink($image_cat->getVar('imgcat_name'), system_adminVersion('images', 'adminpath') . '&amp;op=listimg&amp;imgcat_id=' . $image->getVar('imgcat_id'));
            $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_EDITIMG);
            $xoBreadCrumb->render();
            $msg = '<div class="txtcenter"><img class="tooltip" src="' . XOOPS_URL . '/image.php?id=' . $image->getVar('image_id') . '&amp;width=120&amp;height=120" alt="' . $image->getVar('image_nicename') . '" title="' . $image->getVar('image_nicename') . '" style="max-width:120px; max-height:120px;"/></div>';

            $xoopsTpl->assign('edit_thumbs', $msg);

            $form = new XoopsThemeForm(_AM_SYSTEM_IMAGES_EDITIMG, 'edit_form', 'admin.php', 'post', true);
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new XoopsFormText(_IMAGENAME, 'image_nicename', 50, 255, $image->getVar('image_nicename')), true);
            $select = new XoopsFormSelect(_IMAGECAT, 'imgcat_id', $image->getVar('imgcat_id'));
            $select->addOptionArray($imgcat_handler->getList($groups, 'imgcat_write', $image->getVar('imgcat_write')));
            $form->addElement($select, true);
            //$form->addElement(new XoopsFormFile( _IMAGEFILE, 'image_file', 5000000) );
            $form->addElement(new XoopsFormText(_IMGWEIGHT, 'image_weight', 3, 4, $image->getVar('image_weight')));
            $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'image_display', $image->getVar('image_display'), _YES, _NO));
            $form->addElement(new XoopsFormHidden('image_id', $image_id));
            $form->addElement(new XoopsFormHidden('op', 'save'));
            $form->addElement(new XoopsFormHidden('fct', 'images'));
            //$form->addElement(new XoopsFormButton( '', 'img_button', _SUBMIT, 'submit' ) );
            $form->addElement(new XoopsFormButtonTray('', _SUBMIT, 'submit', '', false));
            $form->assign($xoopsTpl);
        } else {
            redirect_header('admin.php?fct=images', 1, _AM_SYSTEM_DBERROR);
        }
        // Call Footer
        xoops_cp_footer();
        break;

    case 'delfile':
        // Get image handler
        $image_handler  = xoops_getHandler('image');
        $imgcat_handler = xoops_getHandler('imagecategory');
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Get image id
        $image_id = system_CleanVars($_REQUEST, 'image_id', 0, 'int');
        if ($image_id > 0) {
            $image     = $image_handler->get($image_id);
            $image_cat = $imgcat_handler->get($image->getVar('imgcat_id'));
            if ($image_cat->getVar('imgcat_storetype') === 'db') {
                $msg = '<div style="width: 180px;margin:0 auto;"><img class="thumb" src="' . XOOPS_URL . '/image.php?id=' . $image->getVar('image_id') . '&width=120&height=120" alt="" title="" style="max-width:120px; max-height:120px;"/></div>';
            } else {
                $msg = '<div style="width: 180px;margin:0 auto;"><img class="thumb" src="' . XOOPS_URL . '/image.php?id=' . $image->getVar('image_id') . '&width=120&height=120" alt="" title="" style="max-width:120px; max-height:120px;"/></div>';
            }
            $msg .= '<div class="spacer">' . $image->getVar('image_nicename') . '</div>';
            $msg .= '<div class="spacer">' . _AM_SYSTEM_IMAGES_RUDELIMG . '</div>';
            xoops_confirm(array('op' => 'delfileok', 'image_id' => $image_id, 'fct' => 'images'), 'admin.php', $msg, _DELETE);
        } else {
            redirect_header('admin.php?fct=images', 1, _AM_SYSTEM_DBERROR);
        }
        // Call Footer
        xoops_cp_footer();
        break;

    case 'delfileok':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=images', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Get image id
        $image_id = system_CleanVars($_POST, 'image_id', 0, 'int');
        if ($image_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }
        $image_handler = xoops_getHandler('image');
        $image         = $image_handler->get($image_id);
        if (!is_object($image)) {
            redirect_header('admin.php?fct=images', 1);
        }
        if (!$image_handler->delete($image)) {
            xoops_cp_header();
            xoops_error(sprintf(_AM_SYSTEM_IMAGES_FAILDEL, $image->getVar('image_id')));
            xoops_cp_footer();
            exit();
        }
        @unlink(XOOPS_UPLOAD_PATH . '/' . $image->getVar('image_name'));
        redirect_header('admin.php?fct=images&op=listimg&imgcat_id=' . $image->getVar('imgcat_id'), 2, _AM_SYSTEM_DBUPDATED);
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=images', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Get image handler
        $image_handler = xoops_getHandler('image');
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Get image id
        $image_id = system_CleanVars($_POST, 'image_id', 0, 'int');
        if ($image_id > 0) {
            $image = $image_handler->get($image_id);
            $image->setVars($_POST);
            if (!$image_handler->insert($image)) {
                echo sprintf(_AM_SYSTEM_IMAGES_FAILSAVE, $avatar->getVar('avatar_name'));
                xoops_cp_footer();
                exit;
            }
            redirect_header('admin.php?fct=images&op=listimg&imgcat_id=' . $image->getVar('imgcat_id'), 2, _AM_SYSTEM_DBUPDATED);
        }

        // Call Footer
        xoops_cp_footer();
        break;

    case 'addfile':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=images', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->get((int)$imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }
        xoops_load('xoopsmediauploader');
        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . '/images', array(
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/x-png',
            'image/png',
            'image/bmp'), $imagecategory->getVar('imgcat_maxsize'), $imagecategory->getVar('imgcat_maxwidth'), $imagecategory->getVar('imgcat_maxheight'));
        $uploader->setPrefix('img');
        $err    = array();
        $ucount = count($_POST['xoops_upload_file']);
        for ($i = 0; $i < $ucount; ++$i) {
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                if (!$uploader->upload()) {
                    $err[] =& $uploader->getErrors();
                } else {
                    $image_handler = xoops_getHandler('image');
                    $image         = $image_handler->create();
                    $image->setVar('image_name', 'images/' . $uploader->getSavedFileName());
                    $image->setVar('image_nicename', $image_nicename);
                    $image->setVar('image_mimetype', $uploader->getMediaType());
                    $image->setVar('image_created', time());
                    $image_display = empty($image_display) ? 0 : 1;
                    $image->setVar('image_display', $image_display);
                    $image->setVar('image_weight', $image_weight);
                    $image->setVar('imgcat_id', $imgcat_id);
                    if ($imagecategory->getVar('imgcat_storetype') === 'db') {
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
            xoops_cp_header();
            xoops_error($err);
            xoops_cp_footer();
            exit();
        }
        redirect_header('admin.php?fct=images&op=listimg&imgcat_id=' . $image->getVar('imgcat_id'), 2, _AM_SYSTEM_DBUPDATED);
        break;

    case 'addcat':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=images', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
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
            exit();
        }
        $newid                     = $imagecategory->getVar('imgcat_id');
        /* @var  $imagecategoryperm_handler XoopsGroupPermHandler */
        $imagecategoryperm_handler = xoops_getHandler('groupperm');
        if (!isset($readgroup)) {
            $readgroup = array();
        }
        if (!in_array(XOOPS_GROUP_ADMIN, $readgroup)) {
            $readgroup[] = XOOPS_GROUP_ADMIN;
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
            $writegroup[] = XOOPS_GROUP_ADMIN;
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

        redirect_header('admin.php?fct=images', 2, _AM_SYSTEM_DBUPDATED);
        break;

    case 'editcat':
        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }

        $imagecategoryperm_handler = xoops_getHandler('groupperm');
        $form                      = new XoopsThemeForm(_AM_SYSTEM_IMAGES_EDITIMG, 'imagecat_form', 'admin.php', 'post', true);
        $form->addElement(new XoopsFormText(_AM_SYSTEM_IMAGES_IMGCATNAME, 'imgcat_name', 50, 255, $imagecategory->getVar('imgcat_name')), true);
        $form->addElement(new XoopsFormSelectGroup(_AM_SYSTEM_IMAGES_IMGCATRGRP, 'readgroup', true, $imagecategoryperm_handler->getGroupIds('imgcat_read', $imgcat_id), 5, true));
        $form->addElement(new XoopsFormSelectGroup(_AM_SYSTEM_IMAGES_IMGCATWGRP, 'writegroup', true, $imagecategoryperm_handler->getGroupIds('imgcat_write', $imgcat_id), 5, true));
        $form->addElement(new XoopsFormText(_IMGMAXSIZE, 'imgcat_maxsize', 10, 10, $imagecategory->getVar('imgcat_maxsize')));
        $form->addElement(new XoopsFormText(_IMGMAXWIDTH, 'imgcat_maxwidth', 3, 4, $imagecategory->getVar('imgcat_maxwidth')));
        $form->addElement(new XoopsFormText(_IMGMAXHEIGHT, 'imgcat_maxheight', 3, 4, $imagecategory->getVar('imgcat_maxheight')));
        $form->addElement(new XoopsFormText(_AM_SYSTEM_IMAGES_IMGCATWEIGHT, 'imgcat_weight', 3, 4, $imagecategory->getVar('imgcat_weight')));
        $form->addElement(new XoopsFormRadioYN(_AM_SYSTEM_IMAGES_IMGCATDISPLAY, 'imgcat_display', $imagecategory->getVar('imgcat_display'), _YES, _NO));
        $storetype = array('db' => _AM_SYSTEM_IMAGES_INDB, 'file' => _AM_SYSTEM_IMAGES_ASFILE);
        $form->addElement(new XoopsFormLabel(_AM_SYSTEM_IMAGES_IMGCATSTRTYPE, $storetype[$imagecategory->getVar('imgcat_storetype')]));
        $form->addElement(new XoopsFormHidden('imgcat_id', $imgcat_id));
        $form->addElement(new XoopsFormHidden('op', 'updatecat'));
        $form->addElement(new XoopsFormHidden('fct', 'images'));
        //$form->addElement(new XoopsFormButton('', 'imgcat_button', _SUBMIT, 'submit'));
        $form->addElement(new XoopsFormButtonTray('imgcat_button', _SUBMIT, 'submit', '', false));
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/lightbox.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.lightbox.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_MANAGER, system_adminVersion('images', 'adminpath'));
        $xoBreadCrumb->addLink($imagecategory->getVar('imgcat_name'), '');
        $xoBreadCrumb->render();
        echo '<br>';
        $form->display();
        // Call Footer
        xoops_cp_footer();
        exit();

    case 'updatecat':
        if (!$GLOBALS['xoopsSecurity']->check() || $imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }
        $imagecategory->setVar('imgcat_name', $imgcat_name);
        $imgcat_display = empty($imgcat_display) ? 0 : 1;
        $imagecategory->setVar('imgcat_display', $imgcat_display);
        $imagecategory->setVar('imgcat_maxsize', $imgcat_maxsize);
        $imagecategory->setVar('imgcat_maxwidth', $imgcat_maxwidth);
        $imagecategory->setVar('imgcat_maxheight', $imgcat_maxheight);
        $imagecategory->setVar('imgcat_weight', $imgcat_weight);
        if (!$imgcat_handler->insert($imagecategory)) {
            exit();
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
            $readgroup[] = XOOPS_GROUP_ADMIN;
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
            $writegroup[] = XOOPS_GROUP_ADMIN;
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
        redirect_header('admin.php?fct=images', 2, _AM_SYSTEM_DBUPDATED);
        break;

    case 'delcat':
        // Call Header
        xoops_cp_header();
        // Display message
        xoops_confirm(array('op' => 'delcatok', 'imgcat_id' => $imgcat_id, 'fct' => 'images'), 'admin.php', _AM_SYSTEM_IMAGES_RUDELIMGCAT);
        // Call Footer
        xoops_cp_footer();
        break;

    case 'delcatok':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=images', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $imgcat_id = (int)$imgcat_id;
        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }
        $imgcat_handler = xoops_getHandler('imagecategory');
        $imagecategory  = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }
        if ($imagecategory->getVar('imgcat_type') !== 'C') {
            xoops_cp_header();
            xoops_error(_MD_SCATDELNG);
            xoops_cp_footer();
            exit();
        }
        /* @var  $image_handler XoopsImageHandler */
        $image_handler = xoops_getHandler('image');
        $images        = $image_handler->getObjects(new Criteria('imgcat_id', $imgcat_id), true, false);
        $errors        = array();
        foreach (array_keys($images) as $i) {
            if (!$image_handler->delete($images[$i])) {
                $errors[] = sprintf(_AM_SYSTEM_IMAGES_FAILDEL, $i);
            } else {
                if (file_exists(XOOPS_UPLOAD_PATH . '/' . $images[$i]->getVar('image_name')) && !unlink(XOOPS_UPLOAD_PATH . '/' . $images[$i]->getVar('image_name'))) {
                    $errors[] = sprintf(_AM_SYSTEM_IMAGES_FAILUNLINK, $i);
                }
            }
        }
        if (!$imgcat_handler->delete($imagecategory)) {
            $errors[] = sprintf(_AM_SYSTEM_IMAGES_FAILDELCAT, $imagecategory->getVar('imgcat_name'));
        }
        if (count($errors) > 0) {
            xoops_cp_header();
            xoops_error($errors);
            xoops_cp_footer();
            exit();
        }
        redirect_header('admin.php?fct=images', 2, _AM_SYSTEM_DBUPDATED);
        break;

    case 'multiupload':
        // Get category id
        $imgcat_id = system_CleanVars($_REQUEST, 'imgcat_id', 0, 'int');
        if ($imgcat_id <= 0) {
            redirect_header('admin.php?fct=images', 1);
        }
        // Get rights
        $imgcat_write = $gperm_handler->checkRight('imgcat_write', $imgcat_id, $groups, $xoopsModule->mid());
        // Get category handler
        $imgcat_handler = xoops_getHandler('imagecategory');

        $imagecategory = $imgcat_handler->get($imgcat_id);
        if (!is_object($imagecategory)) {
            redirect_header('admin.php?fct=images', 1);
        }
        // Get image handler
        //$image_handler = xoops_getHandler('image');
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_images.tpl';
        // Call header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/media/fine-uploader/fine-uploader-new.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/media/fine-uploader/ManuallyTriggerUploads.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/media/font-awesome/css/font-awesome.min.css');        
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        $xoTheme->addScript('media/fine-uploader/fine-uploader.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_MANAGER, system_adminVersion('images', 'adminpath'));
        $xoBreadCrumb->addLink($imagecategory->getVar('imgcat_name'), system_adminVersion('images', 'adminpath') . '&amp;op=listimg&amp;imgcat_id=' . $imgcat_id);
        $xoBreadCrumb->addLink(_AM_SYSTEM_IMAGES_MULTIUPLOAD);
        $xoBreadCrumb->render();

        $xoopsTpl->assign('multiupload', true);
        $xoopsTpl->assign('imgcat_maxsize', $imagecategory->getVar('imgcat_maxsize'));
        $xoopsTpl->assign('imgcat_maxwidth', $imagecategory->getVar('imgcat_maxwidth'));
        $xoopsTpl->assign('imgcat_maxheight', $imagecategory->getVar('imgcat_maxheight'));
        $xoopsTpl->assign('imgcat_name', $imagecategory->getVar('imgcat_name'));
        $payload = array(
            'aud' => 'ajaxfineupload.php',
            'cat' => $imgcat_id,
            'uid' => $xoopsUser instanceof \XoopsUser ? $xoopsUser->id() : 0,
            'handler' => 'fineimuploadhandler',
            'moddir' => 'system',
        );
        $jwt = \Xmf\Jwt\TokenFactory::build('fineuploader', $payload, 60*30); // token good for 30 minutes
        $xoopsTpl->assign('jwt', $jwt);
        $fineup_debug = 'false';
        if (($xoopsUser instanceof \XoopsUser ? $xoopsUser->isAdmin() : false)
            && isset($_REQUEST['FINEUPLOADER_DEBUG']))
        {
            $fineup_debug = 'true';
        }
        $xoopsTpl->assign('fineup_debug', $fineup_debug);        
        // Call footer
        xoops_cp_footer();
}
