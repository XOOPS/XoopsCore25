<?php
// 
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//          Copyright (c) 2000-2016 XOOPS Project (www.xoops.org)            //
//                         <http://xoops.org/>                               //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 * Manage user rank.
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @TODO                Fix register_globals!
 */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
//  Check is active
if (!xoops_getModuleOption('active_userrank', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

// Parameters
$nb_rank     = xoops_getModuleOption('userranks_pager', 'system');
$mimetypes   = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
$upload_size = 500000;
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');
// Get userrank handler
$userrank_Handler = xoops_getModuleHandler('userrank', 'system');
// Define main template
$xoopsOption['template_main'] = 'system_userrank.tpl';
// Call Header
xoops_cp_header();

switch ($op) {

    case 'list':
    default:
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_MANAGER, system_adminVersion('userrank', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('userrank', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_USERRANK_TIPS);
        $xoBreadCrumb->render();
        // Get start pager
        $start = system_CleanVars($_REQUEST, 'start', 0, 'int');
        // Criteria
        $criteria = new CriteriaCompo();
        $criteria->setSort('rank_id');
        $criteria->setOrder('ASC');
        $criteria->setStart($start);
        $criteria->setLimit($nb_rank);
        // Count rank
        $userrank_count = $userrank_Handler->getCount($criteria);
        $userrank_arr   = $userrank_Handler->getall($criteria);
        // Assign Template variables
        $xoopsTpl->assign('userrank_count', $userrank_count);
        if ($userrank_count > 0) {
            foreach (array_keys($userrank_arr) as $i) {
                $rank_id                  = $userrank_arr[$i]->getVar('rank_id');
                $userrank['rank_id']      = $rank_id;
                $userrank['rank_title']   = $userrank_arr[$i]->getVar('rank_title');
                $userrank['rank_min']     = $userrank_arr[$i]->getVar('rank_min');
                $userrank['rank_max']     = $userrank_arr[$i]->getVar('rank_max');
                $userrank['rank_special'] = $userrank_arr[$i]->getVar('rank_special');
                $rank_img                 = $userrank_arr[$i]->getVar('rank_image') ?: 'blank.gif';
                $userrank['rank_image']   = '<img src="' . XOOPS_UPLOAD_URL . '/' . $rank_img . '" alt="" />';
                $xoopsTpl->append_by_ref('userrank', $userrank);
                unset($userrank);
            }
        }
        // Display Page Navigation
        if ($userrank_count > $nb_rank) {
            $nav = new XoopsPageNav($userrank_count, $nb_rank, $start, 'start', 'fct=userrank&amp;op=list');
            $xoopsTpl->assign('nav_menu', $nav->renderNav(4));
        }
        break;

    // New userrank
    case 'userrank_new':
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_MANAGER, system_adminVersion('userrank', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_ADD);
        $xoBreadCrumb->addHelp(system_adminVersion('userrank', 'help') . '#new');
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_USERRANK_TIPS_FORM1, implode(', ', $mimetypes)) . sprintf(_AM_SYSTEM_USERRANK_TIPS_FORM2, $upload_size / 1000));
        $xoBreadCrumb->render();
        // Create form
        $obj  = $userrank_Handler->create();
        $form = $obj->getForm();
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        break;

    // Edit userrank
    case 'userrank_edit':
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_MANAGER, system_adminVersion('userrank', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_EDIT);
        $xoBreadCrumb->addHelp(system_adminVersion('userrank', 'help') . '#edit');
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_USERRANK_TIPS_FORM1, implode(', ', $mimetypes)) . sprintf(_AM_SYSTEM_USERRANK_TIPS_FORM2, $upload_size / 1000));
        $xoBreadCrumb->render();
        // Create form
        $obj  = $userrank_Handler->get(system_CleanVars($_REQUEST, 'rank_id', 0, 'int'));
        $form = $obj->getForm();
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        break;

    // Save rank
    case 'userrank_save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=userrank', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (isset($_POST['rank_id'])) {
            $obj = $userrank_Handler->get($_POST['rank_id']);
        } else {
            $obj = $userrank_Handler->create();
        }

        $obj->setVar('rank_title', $_POST['rank_title']);
        $obj->setVar('rank_min', $_POST['rank_min']);
        $obj->setVar('rank_max', $_POST['rank_max']);
        $verif_rank_special = ($_POST['rank_special'] == 1) ? '1' : '0';
        $obj->setVar('rank_special', $verif_rank_special);

        include_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $uploader_rank_img = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . '/ranks', $mimetypes, $upload_size, null, null);

        if ($uploader_rank_img->fetchMedia('rank_image')) {
            $uploader_rank_img->setPrefix('rank');
            $uploader_rank_img->fetchMedia('rank_image');
            if (!$uploader_rank_img->upload()) {
                $errors =& $uploader_rank_img->getErrors();
                redirect_header('javascript:history.go(-1)', 3, $errors);
            } else {
                $obj->setVar('rank_image', 'ranks/' . $uploader_rank_img->getSavedFileName());
            }
        } else {
            $obj->setVar('rank_image', 'ranks/' . $_POST['rank_image']);
        }

        if ($userrank_Handler->insert($obj)) {
            redirect_header('admin.php?fct=userrank', 2, _AM_SYSTEM_USERRANK_SAVE);
        }
        break;

    // Delete userrank
    case 'userrank_delete':
        $rank_id = system_CleanVars($_REQUEST, 'rank_id', 0, 'int');
        $obj     = $userrank_Handler->get($rank_id);
        if (isset($_POST['ok']) && $_POST['ok'] == 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('admin.php?fct=userrank', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($userrank_Handler->delete($obj)) {
                $urlfile = XOOPS_UPLOAD_PATH . '/' . $obj->getVar('rank_image');
                if (is_file($urlfile)) {
                    chmod($urlfile, 0777);
                    unlink($urlfile);
                }
                redirect_header('admin.php?fct=userrank', 2, _AM_SYSTEM_USERRANK_SAVE);
            } else {
                xoops_error($obj->getHtmlErrors());
            }
        } else {
            // Define Stylesheet
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
            // Define Breadcrumb and tips
            $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_MANAGER, system_adminVersion('userrank', 'adminpath'));
            $xoBreadCrumb->addLink(_AM_SYSTEM_USERRANK_NAV_DELETE);
            $xoBreadCrumb->addHelp(system_adminVersion('userrank', 'help') . '#delete');
            $xoBreadCrumb->render();
            $rank_img = $obj->getVar('rank_image') ?: 'blank.gif';
            xoops_confirm(array(
                              'ok' => 1,
                              'rank_id' => $_REQUEST['rank_id'],
                              'op' => 'userrank_delete'), $_SERVER['REQUEST_URI'], sprintf(_AM_SYSTEM_USERRANK_SUREDEL) . '<br \><img src="' . XOOPS_UPLOAD_URL . '/' . $rank_img . '" alt="" /><br \>');
        }
        break;

    // Update userrank status
    case 'userrank_update_special':
        // Get rank id
        $rank_id = system_CleanVars($_POST, 'rank_id', 0, 'int');
        if ($rank_id > 0) {
            $obj = $userrank_Handler->get($rank_id);
            $old = $obj->getVar('rank_special');
            $obj->setVar('rank_special', !$old);
            if ($userrank_Handler->insert($obj)) {
                exit;
            }
            echo $obj->getHtmlErrors();
        }
        break;

}
// Call Footer
xoops_cp_footer();
