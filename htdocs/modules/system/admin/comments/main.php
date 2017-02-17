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
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    exit(_NOPERM);
}

//  Check is active
if (!xoops_getModuleOption('active_comments', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'default', 'string');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_comments.tpl';
xoops_cp_header();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
// Define scripts
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
//$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
$xoTheme->addScript('modules/system/js/admin.js');
// Define Breadcrumb and tips
$xoBreadCrumb->addLink(_AM_SYSTEM_COMMENTS_NAV_MANAGER, system_adminVersion('comments', 'adminpath'));

include_once $GLOBALS['xoops']->path('/include/comment_constants.php');
xoops_loadLanguage('comment');

$limit_array     = array(20, 50, 100);
$status_array    = array(XOOPS_COMMENT_PENDING => _CM_PENDING, XOOPS_COMMENT_ACTIVE => _CM_ACTIVE, XOOPS_COMMENT_HIDDEN => _CM_HIDDEN);
$status_array2   = array(
    XOOPS_COMMENT_PENDING => '<span style="text-decoration: none; font-weight: bold; color: #008000;">' . _CM_PENDING . '</span>',
    XOOPS_COMMENT_ACTIVE  => '<span style="text-decoration: none; font-weight: bold; color: #ff0000;">' . _CM_ACTIVE . '</span>',
    XOOPS_COMMENT_HIDDEN  => '<span style="text-decoration: none; font-weight: bold; color: #0000ff;">' . _CM_HIDDEN . '</span>');
$start           = 0;
$status_array[0] = _AM_SYSTEM_COMMENTS_FORM_ALL_STATUS;

$comments = array();
//$status   = (!isset($_REQUEST['status']) || !in_array((int)($_REQUEST['status']), array_keys($status_array))) ? 0 : (int)($_REQUEST['status']);
$status = (!isset($_REQUEST['status']) || !array_key_exists((int)$_REQUEST['status'], $status_array)) ? 0 : (int)$_REQUEST['status'];

$module          = !isset($_REQUEST['module']) ? 0 : (int)$_REQUEST['module'];
$modules_Handler = xoops_getHandler('module');
$module_array    = $modules_Handler->getList(new Criteria('hascomments', 1));
$module_array[0] = _AM_SYSTEM_COMMENTS_FORM_ALL_MODS;
/* @var  $comment_handler XoopsCommentHandler */
$comment_handler = xoops_getHandler('comment');

switch ($op) {

    case 'comments_jump':
        $com_id = system_CleanVars($_GET, 'com_id', 0, 'int');
        if ($com_id > 0) {
            $comment = $comment_handler->get($com_id);
            if (is_object($comment)) {
                /* @var $module_handler XoopsModuleHandler */
                $module_handler = xoops_getHandler('module');
                $module         = $module_handler->get($comment->getVar('com_modid'));
                $comment_config = $module->getInfo('comments');
                header('Location: ' . XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $comment_config['pageName'] . '?' . $comment_config['itemName'] . '=' . $comment->getVar('com_itemid') . '&com_id=' . $comment->getVar('com_id') . '&com_rootid=' . $comment->getVar('com_rootid') . '&com_mode=thread&' . str_replace('&amp;', '&', $comment->getVar('com_exparams')) . '#comment' . $comment->getVar('com_id'));
                exit();
            }
        }
        redirect_header('admin.php?fct=comments', 1, _AM_SYSTEM_COMMENTS_NO_COMMENTS);
        break;

    case 'comments_form_purge':
        //Affichage des coms
        $xoBreadCrumb->addLink(_AM_SYSTEM_COMMENTS_NAV_PURGE);
        $xoBreadCrumb->addHelp(system_adminVersion('comments', 'help') . '#purge');
        $xoBreadCrumb->addTips(_AM_SYSTEM_COMMENTS_NAV_TIPS);
        $xoBreadCrumb->render();

        //Affichage du formulaire de purge
        $form_purge = new XoopsThemeForm(_AM_SYSTEM_COMMENTS_FORM_PURGE, 'form', 'admin.php?fct=comments', 'post', true);

        $form_purge->addElement(new XoopsFormTextDateSelect(_AM_SYSTEM_COMMENTS_FORM_PURGE_DATE_AFTER, 'comments_after', '15'));
        $form_purge->addElement(new XoopsFormTextDateSelect(_AM_SYSTEM_COMMENTS_FORM_PURGE_DATE_BEFORE, 'comments_before', '15'));

        //user
        $form_purge->addElement(new XoopsFormSelectUser(_AM_SYSTEM_COMMENTS_FORM_PURGE_USER, 'comments_userid', false, @$_REQUEST['comments_userid'], 5, true));

        //groups
        $groupe_select = new XoopsFormSelectGroup(_AM_SYSTEM_COMMENTS_FORM_PURGE_GROUPS, 'comments_groupe', false, '', 5, true);
        $groupe_select->setExtra("style=\"width:170px;\" ");
        $form_purge->addElement($groupe_select);

        //Status
        $status  = new XoopsFormSelect(_AM_SYSTEM_COMMENTS_FORM_PURGE_STATUS, 'comments_status', '');
        $options = $status_array;
        $status->addOptionArray($options);
        $form_purge->addElement($status, true);

        //Modules
        $modules = new XoopsFormSelect(_AM_SYSTEM_COMMENTS_FORM_PURGE_MODULES, 'comments_modules', '');
        $options = $module_array;
        $modules->addOptionArray($options);
        $form_purge->addElement($modules, true);
        $form_purge->addElement(new XoopsFormHidden('op', 'comments_purge'));
        $form_purge->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $xoopsTpl->assign('form', $form_purge->render());
        break;

    case 'comments_purge':
        $criteria = new CriteriaCompo();
        $verif    = false;
        if (isset($_POST['comments_after']) && isset($_POST['comments_before'])) {
            if ($_POST['comments_after'] != $_POST['comments_before']) {
                $com_after  = system_CleanVars($_POST, 'comments_after', time(), 'date');
                $com_before = system_CleanVars($_POST, 'comments_before', time(), 'date');
                if ($com_after) {
                    $criteria->add(new Criteria('com_created', $com_after, '>'));
                }
                if ($com_before) {
                    $criteria->add(new Criteria('com_created', $com_before, '<'));
                }
                $verif = true;
            }
        }
        $com_modid = system_CleanVars($_POST, 'comments_modules', 0, 'int');
        if ($com_modid > 0) {
            $criteria->add(new Criteria('com_modid', $com_modid));
            $verif = true;
        }
        $comments_status = system_CleanVars($_POST, 'comments_status', 0, 'int');
        if ($comments_status > 0) {
            $criteria->add(new Criteria('com_status', $_POST['comments_status']));
            $verif = true;
        }
        $comments_userid = system_CleanVars($_POST, 'comments_userid', '', 'string');
        if ($comments_userid != '') {
            foreach ($_REQUEST['comments_userid'] as $del) {
                $criteria->add(new Criteria('com_uid', $del), 'OR');
            }
            $verif = true;
        }
        $comments_groupe = system_CleanVars($_POST, 'comments_groupe', '', 'string');
        if ($comments_groupe != '') {
            foreach ($_POST['comments_groupe'] as $del => $u_name) {
                /* @var $member_handler XoopsMemberHandler */
                $member_handler = xoops_getHandler('member');
                $members        = $member_handler->getUsersByGroup($u_name, true);
                $mcount         = count($members);
                if ($mcount > 4000) {
                    redirect_header('admin.php?fct=comments', 2, _MP_DELETECOUNT);
                }
                for ($i = 0; $i < $mcount; ++$i) {
                    $criteria->add(new Criteria('com_uid', $members[$i]->getVar('uid')), 'OR');
                }
            }
            $verif = true;
        }
        if (isset($_POST['commentslist_id'])) {
            $commentslist_count = (!empty($_POST['commentslist_id']) && is_array($_POST['commentslist_id'])) ? count($_POST['commentslist_id']) : 0;
            if ($commentslist_count > 0) {
                for ($i = 0; $i < $commentslist_count; ++$i) {
                    $criteria->add(new Criteria('com_id', $_REQUEST['commentslist_id'][$i]), 'OR');
                }
            }
            $verif = true;
        }
        if ($verif === true) {
            if ($comment_handler->deleteAll($criteria)) {
                redirect_header('admin.php?fct=comments', 3, _AM_SYSTEM_DBUPDATED);
            }
        } else {
            redirect_header('admin.php?fct=comments', 3, _AM_SYSTEM_DBUPDATED);
        }
        break;

    default:
        // Display comments
        $xoBreadCrumb->addLink(_AM_SYSTEM_COMMENTS_NAV_MAIN);
        $xoBreadCrumb->addHelp(system_adminVersion('comments', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_COMMENTS_NAV_TIPS);
        $xoBreadCrumb->render();

        $myts             = MyTextSanitizer::getInstance();
        /* @var  $comments_Handler XoopsCommentHandler */
        $comments_Handler = xoops_getHandler('comment');
        $comments_module  = '';
        $comments_status  = '';

        $criteria        = new CriteriaCompo();
        $comments_module = system_CleanVars($_REQUEST, 'comments_module', 0, 'int');
        if ($comments_module > 0) {
            $criteria->add(new Criteria('com_modid', $comments_module));
            $comments_module = $_REQUEST['comments_module'];
        }
        $comments_status = system_CleanVars($_REQUEST, 'comments_status', 0, 'int');
        if ($comments_status > 0) {
            $criteria->add(new Criteria('com_status', $comments_status));
            $comments_status = $_REQUEST['comments_status'];
        }

        $criteria->setSort('com_created');
        $criteria->setOrder('DESC');

        $comments_count = $comments_Handler->getCount($criteria);

        $xoopsTpl->assign('comments_count', $comments_count);

        if ($comments_count > 0) {
            $comments_start = system_CleanVars($_REQUEST, 'comments_start', 0, 'int');
            $comments_limit = system_CleanVars($_REQUEST, 'comments_limit', 0, 'int');
            if (!in_array($comments_limit, $limit_array)) {
                $comments_limit = xoops_getModuleOption('comments_pager', 'system');
            }
            $criteria->setLimit($comments_limit);
            $criteria->setStart($comments_start);

            $comments_arr = $comments_Handler->getObjects($criteria, true);
        }

        $form = '<form action="admin.php?fct=comments" method="post">
                <select name="comments_module">';

        foreach ($module_array as $k => $v) {
            $sel = '';
            if ($k == $module) {
                $sel = ' selected';
            }
            $form .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
        }
        $form .= '</select>&nbsp;<select name="comments_status">';

        foreach ($status_array as $k => $v) {
            $sel = '';
            if (isset($status) && $k == $status) {
                $sel = ' selected';
            }
            $form .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
        }

        $form .= '</select>&nbsp;<select name="comments_limit">';
        foreach ($limit_array as $k) {
            $sel = '';
            if (isset($limit) && $k == $limit) {
                $sel = ' selected';
            }
            $form .= '<option value="' . $k . '"' . $sel . '>' . $k . '</option>';
        }
        $form .= '</select>&nbsp;<input type="hidden" name="fct" value="comments" /><input type="submit" value="' . _GO . '" name="selsubmit" /></form>';

        $xoopsTpl->assign('form_sort', $form);
        $xoopsTpl->assign('php_selft', XOOPS_URL . '/modules/system/admin.php?fct=comments&op=comments_purge');

        if ($comments_count > 0) {
            foreach (array_keys($comments_arr) as $i) {
                $com_id                = $comments_arr[$i]->getVar('com_id');
                $comments_poster_uname = $xoopsConfig['anonymous'];
                // Start edit by voltan
                if ($comments_arr[$i]->getVar('com_uid') > 0) {
                    $poster = $member_handler->getUser($comments_arr[$i]->getVar('com_uid'));
                    if (is_object($poster)) {
                        $comments_poster_uname = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $comments_arr[$i]->getVar('com_uid') . '">' . $poster->getVar('uname') . '</a>';
                    }
                } elseif ($comments_arr[$i]->getVar('com_uid') == 0 && $comments_arr[$i]->getVar('com_user') != '') {
                    if ($comments_arr[$i]->getVar('com_url') != '') {
                        $comments_poster_uname = '<div class="pad2 marg2"><a href="' . $comments_arr[$i]->getVar('com_url') . '">' . $comments_arr[$i]->getVar('com_user') . '</a> ( <a href="mailto:' . $comments_arr[$i]->getVar('com_email') . '">' . $comments_arr[$i]->getVar('com_email') . '</a> ) ' . '</div>';
                    } else {
                        $comments_poster_uname = '<div class="pad2 marg2">' . $comments_arr[$i]->getVar('com_user') . ' ( <a href="mailto:' . $comments_arr[$i]->getVar('com_email') . '">' . $comments_arr[$i]->getVar('com_email') . '</a> ) ' . '</div>';
                    }
                }
                // End edit by voltan
                $comments_icon = ($comments_arr[$i]->getVar('com_icon') == '') ? '/images/icons/no_posticon.gif' : '/images/subject/' . htmlspecialchars($comments_arr[$i]->getVar('com_icon'), ENT_QUOTES);
                $comments_icon = '<img src="' . XOOPS_URL . $comments_icon . '" alt="" />';

                $comments['comments_id']           = $com_id;
                $comments['comments_poster']       = $comments_poster_uname;
                $comments['comments_icon']         = $comments_icon;
                $comments['comments_title'] = $myts->htmlSpecialChars($comments_arr[$i]->getVar('com_title'));
                $comments['comments_ip']           = $comments_arr[$i]->getVar('com_ip');
                $comments['comments_date']         = formatTimestamp($comments_arr[$i]->getVar('com_created'));
                $comments['comments_text'] = $myts->htmlSpecialChars($comments_arr[$i]->getVar('com_text'));
                $comments['comments_status']       = @$status_array2[$comments_arr[$i]->getVar('com_status')];
                $comments['comments_date_created'] = formatTimestamp($comments_arr[$i]->getVar('com_created'), 'm');
                $comments['comments_modid']        = @$module_array[$comments_arr[$i]->getVar('com_modid')];
                //$comments['comments_view_edit_delete'] = '<img class="cursorpointer" onclick="display_dialog('.$com_id.', true, true, \'slide\', \'slide\', 300, 500);" src="images/icons/view.png" alt="'._AM_SYSTEM_COMMENTS_VIEW.'" title="'._AM_SYSTEM_COMMENTS_VIEW.'" /><a href="admin/comments/comment_edit.php?com_id='.$com_id.'"><img src="./images/icons/edit.png" border="0" alt="'._EDIT.'" title="'._EDIT.'"></a><a href="admin/comments/comment_delete.php?com_id='.$com_id.'"><img src="./images/icons/delete.png" border="0" alt="'._DELETE.'" title="'._DELETE.'"></a>';

                $xoopsTpl->append_by_ref('comments', $comments);
                $xoopsTpl->append_by_ref('comments_popup', $comments);
                unset($comments);
            }

            if ($comments_count > $comments_limit) {
                include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
                $nav = new XoopsPageNav($comments_count, $comments_limit, $comments_start, 'comments_start', 'fct=comments&amp;comments_module=' . $comments_module . '&amp;comments_status=' . $comments_status);
                $xoopsTpl->assign('nav', $nav->renderNav());
            }
        }
        break;
}
// Call Footer
xoops_cp_footer();
