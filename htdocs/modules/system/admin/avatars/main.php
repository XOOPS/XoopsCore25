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
if (!xoops_getModuleOption('active_avatars', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');

switch ($op) {

    case 'list':
    default:
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_avatars.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MANAGER, system_adminVersion('avatars', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('avatars', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_AVATAR_TIPS);
        $xoBreadCrumb->render();
        // Get avatar handler
        /* @var  $avt_handler XoopsAvatarHandler */
        $avt_handler = xoops_getModuleHandler('avatar');
        // Get User Config
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler  = xoops_getHandler('config');
        $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
        // User language
        xoops_loadLanguage('user');
        // Count avatars
        $savatar_count = $avt_handler->getCount(new Criteria('avatar_type', 'S'));
        $cavatar_count = $avt_handler->getCount(new Criteria('avatar_type', 'C'));
        // Assign Template variables
        $xoopsTpl->assign('view_cat', true);
        $xoopsTpl->assign('count_system', $savatar_count);
        $xoopsTpl->assign('count_custom', $cavatar_count);
        // Create form
        $avatar = $avt_handler->create();
        $form   = $avatar->getForm();
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        // Call Footer
        xoops_cp_footer();
        break;

    case 'listavt':
        // Get Avatar type
        $type  = system_CleanVars($_REQUEST, 'type', 'c', 'string');
        $start = system_CleanVars($_REQUEST, 'start', 0, 'int');
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_avatars.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript('modules/system/js/admin.js');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MANAGER, system_adminVersion('avatars', 'adminpath'));
        $xoBreadCrumb->addLink(($type === 's' ? _AM_SYSTEM_AVATAR_SYSTEM : _AM_SYSTEM_AVATAR_CUSTOM));
        $xoBreadCrumb->addHelp(system_adminVersion('avatars', 'help') . '#' . $type);
        $xoBreadCrumb->addTips(_AM_SYSTEM_AVATAR_TIPS);
        $xoBreadCrumb->render();
        // Get avatar handler
        /* @var  $avt_handler XoopsAvatarHandler */
        $avt_handler = xoops_getHandler('avatar');
        // Count avatars
        $savatar_count = $avt_handler->getCount(new Criteria('avatar_type', 'S'));
        $cavatar_count = $avt_handler->getCount(new Criteria('avatar_type', 'C'));
        // Assign Template variables
        $xoopsTpl->assign('type', $type);
        $xoopsTpl->assign('count_system', $savatar_count);
        $xoopsTpl->assign('count_custom', $cavatar_count);
        // Filter avatars
        $criteria = new Criteria('avatar_type', $type);
        $avtcount = $avt_handler->getCount($criteria);
        // Get avatar list
        $criteria->setStart($start);
        $criteria->setLimit(xoops_getModuleOption('avatars_pager', 'system'));
        $avatars = $avt_handler->getObjects($criteria, true);
        // Construct avatars array
        $avatar_list = array();
        $i           = 0;
        foreach (array_keys($avatars) as $i) {
            $avatar_list[$i]          = $avatars[$i]->toArray();
            $avatar_list[$i]['type']  = $type;
            $avatar_list[$i]['count'] = count($avt_handler->getUser($avatars[$i]));
            if ($type === 'c') {
                $user = $avt_handler->getUser($avatars[$i]);
                if (is_array($user) && isset($user[0])) {
                    $avatar_list[$i]['user'] = $user[0];
                }
            }
        }
        $xoopsTpl->assign('avatars_list', $avatar_list);
        // Display Page Navigation
        if ($avtcount > xoops_getModuleOption('avatars_pager')) {
            $nav = new XoopsPageNav($avtcount, xoops_getModuleOption('avatars_pager', 'system'), $start, 'start', 'fct=avatars&amp;type=' . $type . '&amp;op=listavt');
            $xoopsTpl->assign('nav_menu', $nav->renderNav(4));
        }
        // Call Footer
        xoops_cp_footer();
        break;

    case 'edit':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_avatars.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MANAGER, system_adminVersion('avatars', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_EDIT);
        $xoBreadCrumb->addHelp(system_adminVersion('avatars', 'help') . '#edit');
        $xoBreadCrumb->addTips(_AM_SYSTEM_AVATAR_TIPS);
        $xoBreadCrumb->render();
        // User language
        xoops_loadLanguage('user');
        // Get avatar handler
        $avt_handler = xoops_getModuleHandler('avatar');
        $avatar_id   = system_CleanVars($_REQUEST, 'avatar_id', 0, 'int');
        if ($avatar_id > 0) {
            $avatar = $avt_handler->get($avatar_id);
            // Create form
            $form = $avatar->getForm();
            // Assign form
            $xoopsTpl->assign('form', $form->render());
        } else {
            redirect_header('admin.php?fct=avatars', 1, _AM_SYSTEM_DBERROR);
        }
        // Call Footer
        xoops_cp_footer();
        break;

    case 'save':
        // Check security
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=avatars', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler  = xoops_getHandler('config');
        $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
        // Upload class
        include_once $GLOBALS['xoops']->path('/class/uploader.php');

        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH . '/avatars', array(
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/x-png',
            'image/png'), $xoopsConfigUser['avatar_maxsize'], $xoopsConfigUser['avatar_width'], $xoopsConfigUser['avatar_height']);
        // Get avatar handler
        $avt_handler = xoops_getHandler('avatar');
        // Get avatar id
        $avatar_id = system_CleanVars($_POST, 'avatar_id', 0, 'int');
        if ($avatar_id > 0) {
            $avatar = $avt_handler->get($avatar_id);
        } else {
            $avatar = $avt_handler->create();
        }
        $err = array();
        if ($_FILES['avatar_file']['error'] != UPLOAD_ERR_NO_FILE) {
            if ($uploader->fetchMedia('avatar_file')) {
                $uploader->setPrefix('savt');
                if (!$uploader->upload()) {
                    $err[] =& $uploader->getErrors();
                } else {
                    $avatar->setVars($_POST);
                    $avatar->setVar('avatar_file', 'avatars/' . $uploader->getSavedFileName());
                    $avatar->setVar('avatar_mimetype', $uploader->getMediaType());
                    $avatar->setVar('avatar_type', 's');
                    if (!$avt_handler->insert($avatar)) {
                        $err[] = sprintf(_FAILSAVEIMG, $avatar->getVar('avatar_name'));
                    }
                }
            }else{
                $err[] = $uploader->getErrors();
            }
        } else {
            $file = system_CleanVars($_REQUEST, 'avatar_file', 'blank.gif', 'string');
            $avatar->setVars($_REQUEST);
            $avatar->setVar('avatar_file', 'avatars/' . $file);
            if (!$avt_handler->insert($avatar)) {
                $err[] = sprintf(_FAILSAVEIMG, $avatar->getVar('avatar_name'));
            }
        }
        if (count($err) > 0) {
            // Define main template
            $GLOBALS['xoopsOption']['template_main'] = 'system_header.tpl';
            // Call header
            xoops_cp_header();
            // Define Stylesheet
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
            // Define Breadcrumb and tips
            $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MANAGER, system_adminVersion('avatars', 'adminpath'));
            $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_ERROR);
            $xoBreadCrumb->render();
            // Dsiplay errors
            xoops_error($err);
            // Call Footer
            xoops_cp_footer();
            exit();
        }
        redirect_header('admin.php?fct=avatars', 2, _AM_SYSTEM_DBUPDATED);
        break;

    case 'display':
        // Get avatar handler
        $avt_handler = xoops_getHandler('avatar');
        // Get avatar id
        $avatar_id = system_CleanVars($_POST, 'avatar_id', 0, 'int');
        if ($avatar_id > 0) {
            // Get avatar
            $avatar = $avt_handler->get($avatar_id);
            $old    = $avatar->getVar('avatar_display');
            // Set value
            $avatar->setVar('avatar_display', !$old);
            if (!$avt_handler->insert($avatar)) {
                $error = true;
            }
        }
        break;

    case 'delfile':
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_avatars.tpl';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MANAGER, system_adminVersion('avatars', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_DELETE);
        $xoBreadCrumb->addHelp(system_adminVersion('avatars', 'help') . '#delete');
        $xoBreadCrumb->render();
        // Get variables
        $user_id   = system_CleanVars($_REQUEST, 'user_id', 0, 'int');
        $avatar_id = system_CleanVars($_REQUEST, 'avatar_id', 0, 'int');
        // Get avatar handler
        $avt_handler = xoops_getHandler('avatar');
        if ($avatar_id > 0) {
            $avatar = $avt_handler->get($avatar_id);
            $msg    = '<div class="spacer"><img src="' . XOOPS_UPLOAD_URL . '/' . $avatar->getVar('avatar_file', 's') . '" alt="" /></div><div class="txtcenter bold">' . $avatar->getVar('avatar_name', 's') . '</div>' . _AM_SYSTEM_AVATAR_SUREDEL;
            // Display message
            xoops_confirm(array('op' => 'delfileok', 'avatar_id' => $avatar_id, 'fct' => 'avatars', 'user_id' => $user_id), 'admin.php', $msg);
        } else {
            redirect_header('admin.php?fct=avatars', 1, _AM_SYSTEM_DBERROR);
        }
        // Call footer
        xoops_cp_footer();
        break;

    case 'delfileok':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=avatars', 1, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $avatar_id = system_CleanVars($_POST, 'avatar_id', 0, 'int');
        if ($avatar_id <= 0) {
            redirect_header('admin.php?fct=avatars', 1, _AM_SYSTEM_DBERROR);
        }
        $avt_handler = xoops_getHandler('avatar');
        $avatar      = $avt_handler->get($avatar_id);
        if (!is_object($avatar)) {
            redirect_header('admin.php?fct=avatars', 1, _AM_SYSTEM_DBERROR);
        }
        if (!$avt_handler->delete($avatar)) {
            // Call Header
            xoops_cp_header();
            // Display errors
            xoops_error(sprintf(_AM_SYSTEM_AVATAR_FAILDEL, $avatar->getVar('avatar_id')));
            // Call Footer
            xoops_cp_footer();
            exit();
        }
        $file = $avatar->getVar('avatar_file');
        // Delete file
        @unlink(XOOPS_UPLOAD_PATH . '/' . $file);
        // Update member profil
        if (isset($user_id) && $avatar->getVar('avatar_type') === 'C') {
            $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('users') . " SET user_avatar='blank.gif' WHERE uid=" . (int)$user_id);
        } else {
            $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('users') . " SET user_avatar='blank.gif' WHERE user_avatar='" . $file . "'");
        }
        redirect_header('admin.php?fct=avatars', 2, _AM_SYSTEM_DBUPDATED);
        break;
    
    case 'multiupload':
    
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_avatars.tpl';
        // Call Header
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
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MANAGER, system_adminVersion('avatars', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_AVATAR_MULTIUPLOAD);
        $xoBreadCrumb->render();
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler  = xoops_getHandler('config');
        $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
        
        $xoopsTpl->assign('multiupload', true);
        $xoopsTpl->assign('imgcat_maxsize', $xoopsConfigUser['avatar_maxsize']);
        $xoopsTpl->assign('imgcat_maxwidth', $xoopsConfigUser['avatar_width']);
        $xoopsTpl->assign('imgcat_maxheight', $xoopsConfigUser['avatar_height']);
        $payload = array(
            'aud' => 'ajaxfineupload.php',
            'cat' => '',
            'uid' => $xoopsUser instanceof \XoopsUser ? $xoopsUser->id() : 0,
            'handler' => 'fineavataruploadhandler',
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

        break;
}
