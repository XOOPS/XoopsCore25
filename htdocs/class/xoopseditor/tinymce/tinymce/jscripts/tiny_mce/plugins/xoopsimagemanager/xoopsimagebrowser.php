<?php
/**
 *  Xoopsemotions plugin for tinymce
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class / xoopseditor
 * @subpackage          tinymce / xoops plugins
 * @since               2.3.0
 * @author              ralf57
 * @author              luciorota <lucio.rota@gmail.com>
 * @author              Laurent JEN <dugris@frxoops.org>
 */

// load mainfile.php
$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$xoops_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/xoopseditor/tinymce/'));
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
$target = htmlspecialchars($target);

if ($isadmin || ($catreadcount > 0) || ($catwritecount > 0)) {

    // Save Image modification - start
    if (!empty($_POST['op']) && $op === 'save') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($current_file . '?target=' . $target, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $count = count($image_id);
        if ($count > 0) {
            $image_handler = xoops_getHandler('image');
            $error         = array();
            for ($i = 0; $i < $count; ++$i) {
                $image = $image_handler->get($image_id[$i]);
                if (!is_object($image)) {
                    $error[] = sprintf(_FAILGETIMG, $image_id[$i]);
                    continue;
                }
                $image_display[$i] = empty($image_display[$i]) ? 0 : 1;
                $image->setVar('image_display', $image_display[$i]);
                $image->setVar('image_weight', $image_weight[$i]);
                $image->setVar('image_nicename', $image_nicename[$i]);
                $image->setVar('imgcat_id', $imgcat_id[$i]);
                if (!$image_handler->insert($image)) {
                    $error[] = sprintf(_FAILSAVEIMG, $image_id[$i]);
                }
            }
            if (count($error) > 0) {
                redirect_header($current_file . '?target=' . $target, 3, xoops_error(implode('<br>', $error)));
            }
        }
        redirect_header($current_file . '?target=' . $target, 3, _AM_SYSTEM_DBUPDATED);
    }
    // Save Image modification - end

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
    // Add new image - end

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
        xoops_confirm(array('op' => 'delcatok', 'imgcat_id' => $imgcat_id, 'target' => $target), 'xoopsimagebrowser.php', _MD_RUDELIMGCAT);
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
        xoops_confirm(array('op' => 'delfileok', 'image_id' => $image_id, 'target' => $target), 'xoopsimagebrowser.php', _MD_RUDELIMG);
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
    // Delete file - end
    // ************************* NOT USED ************************************
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />';
echo '<meta http-equiv="content-language" content="' . _LANGCODE . '" />';
echo '<title>{#xoopsimagebrowser_dlg.dialog_title}</title>';
echo '<script type="text/javascript" src="../../tiny_mce_popup.js"></script>';
echo '<script type="text/javascript" src="../../utils/mctabs.js"></script>';
echo '<script type="text/javascript" src="../../utils/form_utils.js"></script>';
echo '<script type="text/javascript" src="../../utils/validate.js"></script>';
echo '<script type="text/javascript" src="js/xoopsimagebrowser.js"></script>';
echo '<link href="' . xoops_getcss($xoopsConfig['theme_set']) . '" rel="stylesheet" type="text/css" />';
echo '<link href="css/xoopsimagebrowser.css" rel="stylesheet" type="text/css" />';
echo '<base target="_self" />';
echo '</head>';
echo '<body>';

echo '<div class="tabs">';
echo '<ul>';
echo '<li id="imagebrowser_tab" class="current"><span><a href="javascript:mcTabs.displayTab(\'imagebrowser_tab\',\'imagebrowser_panel\');" onmousedown="return false;">';
if ($op === 'listimg') {
    echo '{#xoopsimagebrowser_dlg.tab_listimages}';
} else {
    echo '{#xoopsimagebrowser_dlg.tab_listcategories}';
}
echo '</a></span></li>';
if (!empty($catwritelist)) {
    echo '<li id="loadimage_tab"><span><a href="javascript:mcTabs.displayTab(\'loadimage_tab\',\'loadimage_panel\');" onmousedown="return false;">{#xoopsimagebrowser_dlg.tab_loadimage}</a></span></li>';
}
if ($isadmin) {
    echo '<li id="createcategory_tab"><span><a href="javascript:mcTabs.displayTab(\'createcategory_tab\',\'createcategory_panel\');" onmousedown="return false;">{#xoopsimagebrowser_dlg.tab_createcategory}</a></span></li>';
}
echo '</ul>';
echo '</div>';

echo '<div class="panel_wrapper">';
echo '<div id="imagebrowser_panel" class="panel current" style="overflow:auto;">';

//list Categories - start
if ($op === 'list') {
    if (!empty($catreadlist)) {
        echo '<table width="100%" class="outer" cellspacing="1">';
        // get all categories
        $imagecategories = $imgcat_handler->getObjects();
        $catcount        = count($imagecategories);
        $image_handler   = xoops_getHandler('image');
        for ($i = 0; $i < $catcount; ++$i) {
            echo '<tr valign="top" align="left"><td class="head">';
            if (in_array($imagecategories[$i]->getVar('imgcat_id'), array_keys($catreadlist))) {
                // count images stored in this category
                $this_imgcat_id   = $imagecategories[$i]->getVar('imgcat_id');
                $countimagesincat = $image_handler->getCount(new Criteria('imgcat_id', $this_imgcat_id));
                echo $this_imgcat_id . ' - ' . $imagecategories[$i]->getVar('imgcat_name') . ' (' . sprintf(_NUMIMAGES, '<strong>' . $countimagesincat . '</strong>') . ')';
                echo '</td><td class="even">';
                echo '&nbsp;[<a href="' . $current_file . '?target=' . $target . '&amp;op=listimg&amp;imgcat_id=' . $this_imgcat_id . '">' . _LIST . '</a>]';
                if ($isadmin) {
                    echo '&nbsp;[<a href="' . $current_file . '?target=' . $target . '&amp;op=editcat&amp;imgcat_id=' . $this_imgcat_id . '">' . _EDIT . '</a>]';
                }
                if ($isadmin && $imagecategories[$i]->getVar('imgcat_type') === 'C') {
                    echo '&nbsp;[<a href="' . $current_file . '?target=' . $target . '&amp;op=delcat&amp;imgcat_id=' . $this_imgcat_id . '">' . _DELETE . '</a>]';
                }
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }
}
//list Categories - end

//list images - start
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

    echo '<a href="' . $current_file . '?target=' . $target . '">' . _MD_IMGMAIN . '</a>&nbsp;<span style="font-weight:bold;">&gt;</span>&nbsp;' . $imagecategory->getVar('imgcat_name');
    echo '<br><br><strong>{#xoopsimagebrowser_dlg.select_image}</strong>';
    echo '<form action="' . $current_file . '?target=' . $target . '" method="post">';
    $rowspan = $catwritelist ? 5 : 2;
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
        
        // check if image stored in db/as file - end
        echo '<table width="100%" class="outer">';
        echo '<tr>';
        echo '<td rowspan="' . $rowspan . '" class="xoopsimage">';

        echo '<img id="imageid' . $images[$i]->getVar('image_id') . '" src="' . $image_src . '" alt="' . $images[$i]->getVar('image_nicename', 'E') . '" title="' . $images[$i]->getVar('image_nicename', 'E') . '" onclick="XoopsimagebrowserDialog.insertAndClose(\'imageid' . $images[$i]->getVar('image_id') . '\');return false;"/>';
        echo '<br>';
        if ($image_info == true){
            echo '' . $image_size[0] . 'x' . $image_size[1] . '';
        }
        echo '</td>';
        echo '<td class="head">' . _IMAGENAME, '</td>';
        echo '<td class="even"><input type="hidden" name="image_id[]" value="' . $i . '" /><input type="text" name="image_nicename[]" value="' . $images[$i]->getVar('image_nicename', 'E') . '" size="20" maxlength="255" /></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="head">' . _IMAGEMIME . '</td>';
        echo '<td class="odd">' . $images[$i]->getVar('image_mimetype') . '</td>';
        echo '</tr>';

        if ($catwritelist) {
            echo '<tr>';
            echo '<td class="head">' . _IMAGECAT . '</td>';
            echo '<td class="even">';
            echo '<select name="imgcat_id[]" size="1">';
            $list = $imgcat_handler->getList($groups, null, null, $imagecategory->getVar('imgcat_storetype'));
            foreach ($list as $value => $name) {
                echo '<option value="' . $value . '"' . (($value == $images[$i]->getVar('imgcat_id')) ? ' selected="selected"' : '') . '>' . $name . '</option>';
            }
            echo '</select>';
            echo '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td class="head">' . _IMGWEIGHT . '</td>';
            echo '<td class="odd"><input type="text" name="image_weight[]" value="' . $images[$i]->getVar('image_weight') . '" size="3" maxlength="4" /></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td class="head">' . _IMGDISPLAY . '</td>';
            echo '<td class="even">';
            echo '<input type="checkbox" name="image_display[]" value="1"' . (($images[$i]->getVar('image_display') == 1) ? ' checked="checked"' : '') . ' />';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<br>';
    }

    if ($imgcount > 0) {
        if ($imgcount > 20) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $nav = new XoopsPageNav($imgcount, 20, $start, 'start', 'op=listimg&amp;target=' . $target . '&amp;imgcat_id=' . $imgcat_id);
            echo '<div text-align="right">' . $nav->renderNav() . '</div>';
        }
        if ($catwritelist) {
            echo '<input type="hidden" name="op" value="save" />' . $GLOBALS['xoopsSecurity']->getTokenHTML() . '<input type="submit" name="submit" value="' . _SUBMIT . '" />';
            echo '</form>';
        }
    }
}
//list images - end

//edit category - start
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
    $form                      = new XoopsThemeForm(_MD_EDITIMGCAT, 'imagecat_form', '' . $current_file . '?target=' . $target . '', 'post', true);
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
    echo '<a href="' . $current_file . '?target=' . $target . '">' . _MD_IMGMAIN . '</a>&nbsp;<span style="font-weight:bold;">&gt;</span>&nbsp;' . $imagecategory->getVar('imgcat_name') . '<br><br>';
    $form->display();
}
echo '<div class="mceActionPanel floatright" >';
echo '<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />';
echo '</div>';
echo '</div>';
//edit category - end

//create Image - start
if ($isadmin || !empty($catwritelist)) {
    echo '<div id="loadimage_panel" class="panel" style="overflow:auto;">';
    $form = new XoopsThemeForm(_ADDIMAGE, 'image_form', '' . $current_file . '?target=' . $target . '', 'post', true);
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
    $form->display();
    echo '<div class="mceActionPanel floatright" >';
    echo '<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />';
    echo '</div>';
    echo '</div>';
}
//create Image - end

//create Category - start
if ($isadmin) {
    echo '<div id="createcategory_panel" class="panel" style="overflow:auto;">';
    $form = new XoopsThemeForm(_MD_ADDIMGCAT, 'imagecat_form', '' . $current_file . '?target=' . $target . '', 'post', true);
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
    echo '<div class="mceActionPanel floatright" >';
    echo '<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />';
    echo '</div>';
    echo '</div>';
}
//create Category - end

echo '</div>';
xoops_footer();
