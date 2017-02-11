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

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
//  Check is active
if (!xoops_getModuleOption('active_smilies', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

// Parameters
$nb_smilies  = xoops_getModuleOption('smilies_pager', 'system');
$mimetypes   = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
$upload_size = 500000;
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');
// Get smilies handler
/* @var  $smilies_Handler SystemsmiliesHandler */
$smilies_Handler = xoops_getModuleHandler('smilies', 'system');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_smilies.tpl';
// Call Header
xoops_cp_header();

$xoBreadCrumb->addLink(_AM_SYSTEM_SMILIES_NAV_MANAGER, system_adminVersion('smilies', 'adminpath'));

switch ($op) {

    case 'list':
    default:
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addTips(_AM_SYSTEM_SMILIES_NAV_TIPS);
        $xoBreadCrumb->addHelp(system_adminVersion('smilies', 'help'));
        $xoBreadCrumb->render();
        // Get start pager
        $start = system_CleanVars($_REQUEST, 'start', 0, 'int');
        // Criteria
        $criteria = new CriteriaCompo();
        $criteria->setSort('id');
        $criteria->setOrder('ASC');
        $criteria->setStart($start);
        $criteria->setLimit($nb_smilies);
        // Count smilies
        $smilies_count = $smilies_Handler->getCount($criteria);
        $smilies_arr   = $smilies_Handler->getall($criteria);
        // Assign Template variables
        $xoopsTpl->assign('smilies_count', $smilies_count);
        if ($smilies_count > 0) {
            foreach (array_keys($smilies_arr) as $i) {
                $smilies_id             = $smilies_arr[$i]->getVar('id');
                $smilies['smilies_id']  = $smilies_id;
                $smilies['code']        = $smilies_arr[$i]->getVar('code');
                $smilies['emotion']     = $smilies_arr[$i]->getVar('emotion');
                $smilies['display']     = $smilies_arr[$i]->getVar('display');
                $smilies_img            = $smilies_arr[$i]->getVar('smile_url') ?: 'blank.gif';
                $smilies['image']       = '<img src="' . XOOPS_UPLOAD_URL . '/' . $smilies_img . '" alt="" />';
                $smilies['edit_delete'] = '<a href="admin.php?fct=smilies&amp;op=edit_smilie&amp;smilies_id=' . $smilies_id . '">
                                           <img src="./images/icons/edit.png" border="0" alt="' . _AM_SYSTEM_SMILIES_EDIT . '" title="' . _AM_SYSTEM_SMILIES_EDIT . '"></a>
                                           <a href="admin.php?fct=smilies&amp;op=smilies_delete&amp;smilies_id=' . $smilies_id . '">
                                           <img src="./images/icons/delete.png" border="0" alt="' . _AM_SYSTEM_SMILIES_DELETE . '" title="' . _AM_SYSTEM_SMILIES_DELETE . '"></a>';
                $xoopsTpl->append_by_ref('smilies', $smilies);
                unset($smilies);
            }
        }
        // Display Page Navigation
        if ($smilies_count > $nb_smilies) {
            $nav = new XoopsPageNav($smilies_count, $nb_smilies, $start, 'start', 'fct=smilies&amp;op=list');
            $xoopsTpl->assign('nav_menu', $nav->renderNav(4));
        }
        break;

    // New smilie
    case 'new_smilie':
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_SMILIES_NAV_ADD);
        $xoBreadCrumb->addHelp(system_adminVersion('smilies', 'help') . '#new');
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_SMILIES_NAV_TIPS_FORM1, implode(', ', $mimetypes)) . sprintf(_AM_SYSTEM_SMILIES_NAV_TIPS_FORM2, $upload_size / 1000));
        $xoBreadCrumb->render();
        // Create form
        $obj  = $smilies_Handler->create();
        $form = $obj->getForm();
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        break;

    // Edit smilie
    case 'edit_smilie':
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_SMILIES_NAV_EDIT);
        $xoBreadCrumb->addHelp(system_adminVersion('smilies', 'help') . '#edit');
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_SMILIES_NAV_TIPS_FORM1, implode(', ', $mimetypes)) . sprintf(_AM_SYSTEM_SMILIES_NAV_TIPS_FORM2, $upload_size / 1000));
        $xoBreadCrumb->render();
        // Create form
        $obj  = $smilies_Handler->get(system_CleanVars($_REQUEST, 'smilies_id', 0, 'int'));
        $form = $obj->getForm();
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        break;

    // Save smilie
    case 'save_smilie':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=smilies', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_SMILIES_NAV_ADD);
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_SMILIES_NAV_TIPS_FORM1, implode(', ', $mimetypes)) . sprintf(_AM_SYSTEM_SMILIES_NAV_TIPS_FORM2, $upload_size / 1000));
        $xoBreadCrumb->render();

        if (isset($_POST['smilies_id'])) {
            $obj = $smilies_Handler->get(system_CleanVars($_POST, 'smilies_id', 0, 'int'));
        } else {
            $obj = $smilies_Handler->create();
        }
        // erreur
        $obj->setVar('code', $_POST['code']);
        $obj->setVar('emotion', $_POST['emotion']);
        $display = ($_POST['display'] == 1) ? '1' : '0';
        $obj->setVar('display', $display);

        include_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $uploader_smilies_img = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . '/smilies', $mimetypes, $upload_size, null, null);
        if ($_FILES['smile_url']['error'] != UPLOAD_ERR_NO_FILE) {
            if ($uploader_smilies_img->fetchMedia('smile_url')) {
                $uploader_smilies_img->setPrefix('smil');
                $uploader_smilies_img->fetchMedia('smile_url');
                if (!$uploader_smilies_img->upload()) {
                    $err[] =& $uploader_smilies_img->getErrors();
                } else {
                    $obj->setVar('smile_url', 'smilies/' . $uploader_smilies_img->getSavedFileName());
                    if (!$smilies_Handler->insert($obj)) {
                        $err[] = sprintf(_FAILSAVEIMG, $obj->getVar('code'));
                    }
                }
            } else {
                $err[] = $uploader_smilies_img->getErrors();
            }
        } else {
            $obj->setVar('smile_url', 'smilies/' . $_POST['smile_url']);
            if (!$smilies_Handler->insert($obj)) {
                $err[] = sprintf(_FAILSAVEIMG, $obj->getVar('code'));
            }
        }
        if (count($err) > 0) {
            // Define Stylesheet
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
            // Display errors
            xoops_error($err);
            // Call Footer
            xoops_cp_footer();
            exit();
        }
        redirect_header('admin.php?fct=smilies', 2, _AM_SYSTEM_SMILIES_SAVE);
        break;

    //Del a smilie
    case 'smilies_delete':
        $smilies_id = system_CleanVars($_REQUEST, 'smilies_id', 0, 'int');
        $obj        = $smilies_Handler->get($smilies_id);
        if (isset($_POST['ok']) && $_POST['ok'] == 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('admin.php?fct=smilies', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($smilies_Handler->delete($obj)) {
                $urlfile = XOOPS_UPLOAD_PATH . '/' . $obj->getVar('smile_url');
                if (is_file($urlfile)) {
                    chmod($urlfile, 0777);
                    unlink($urlfile);
                }
                redirect_header('admin.php?fct=smilies', 2, _AM_SYSTEM_SMILIES_SAVE);
            } else {
                xoops_error($obj->getHtmlErrors());
            }
        } else {
            // Define Stylesheet
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
            // Define Breadcrumb and tips
            $xoBreadCrumb->addLink(_AM_SYSTEM_SMILIES_NAV_DELETE);
            $xoBreadCrumb->addHelp(system_adminVersion('smilies', 'help') . '#delete');
            $xoBreadCrumb->render();
            $smilies_img = $obj->getVar('smile_url') ?: 'blank.gif';
            xoops_confirm(array(
                              'ok' => 1,
                              'smilies_id' => $_REQUEST['smilies_id'],
                              'op' => 'smilies_delete'), $_SERVER['REQUEST_URI'], sprintf(_AM_SYSTEM_SMILIES_SUREDEL) . '<br \><img src="' . XOOPS_UPLOAD_URL . '/' . $smilies_img . '" alt="" /><br \>');
        }
        break;

    case 'smilies_update_display':
        // Get smilies id
        $smilies_id = system_CleanVars($_POST, 'smilies_id', 0, 'int');
        if ($smilies_id > 0) {
            $obj = $smilies_Handler->get($smilies_id);
            $old = $obj->getVar('display');
            $obj->setVar('display', !$old);
            if ($smilies_Handler->insert($obj)) {
                exit;
            }
            echo $obj->getHtmlErrors();
        }
        break;
}
// Call Footer
xoops_cp_footer();
