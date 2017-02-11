<?php
/**
 * XOOPS comment
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
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once $GLOBALS['xoops']->path('include/comment_constants.php');

xoops_loadLanguage('comment');

if ('system' === $xoopsModule->getVar('dirname')) {
    $com_id = isset($_POST['com_id']) ? (int)$_POST['com_id'] : 0;
    if (empty($com_id)) {
        exit();
    }
    /* @var  $comment_handler XoopsCommentHandler */
    $comment_handler = xoops_getHandler('comment');
    $comment         = $comment_handler->get($com_id);
    $module_handler  = xoops_getHandler('module');
    $module          = $module_handler->get($comment->getVar('com_modid'));
    $comment_config  = $module->getInfo('comments');
    $com_modid       = $module->getVar('mid');
    $redirect_page   = XOOPS_URL . '/modules/system/admin.php?fct=comments&com_modid=' . $com_modid . '&com_itemid';
    $moddir          = $module->getVar('dirname');
    unset($comment);
} else {
    $com_id = isset($_POST['com_id']) ? (int)$_POST['com_id'] : 0;
    if (XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) {
        exit();
    }
    $comment_config = $xoopsModule->getInfo('comments');
    $com_modid      = $xoopsModule->getVar('mid');
    $redirect_page  = $comment_config['pageName'] . '?';
    if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
        $extra_params = '';
        foreach ($comment_config['extraParams'] as $extra_param) {
            $extra_params .= isset($_POST[$extra_param]) ? $extra_param . '=' . htmlspecialchars($_POST[$extra_param]) . '&amp;' : $extra_param . '=&amp;';
        }
        $redirect_page .= $extra_params;
    }
    $redirect_page .= $comment_config['itemName'];
    $comment_url = $redirect_page;
    $moddir      = $xoopsModule->getVar('dirname');
}

$op            = '';
$error_message = '';
$com_user      = '';
$com_email     = '';
$com_url       = '';

if (!empty($_POST)) {
    if (isset($_POST['com_dopost'])) {
        $op = 'post';
    } elseif (isset($_POST['com_dopreview'])) {
        $op = 'preview';
    }
    if (isset($_POST['com_dodelete'])) {
        $op = 'delete';
    }
    if ($op === 'preview' || $op === 'post') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            $op = '';
        }
    }
    if ($op === 'post' && !is_object($xoopsUser)) {
        xoops_load('XoopsCaptcha');
        $xoopsCaptcha = XoopsCaptcha::getInstance();
        if (!$xoopsCaptcha->verify()) {
            $error_message .= $xoopsCaptcha->getMessage() . '<br>';
        }

        // Start add by voltan
        xoops_load('XoopsUserUtility');
        xoops_loadLanguage('user');
        $myts = MyTextSanitizer::getInstance();

        // Check user name
        $search_arr  = array(
            '&nbsp;',
            "\t",
            "\r\n",
            "\r",
            "\n",
            ',',
            '.',
            "'",
            ';',
            ':',
            ')',
            '(',
            '"',
            '?',
            '!',
            '{',
            '}',
            '[',
            ']',
            '<',
            '>',
            '/',
            '+',
            '-',
            '_',
            '\\',
            '*',
            '=',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&');
        $replace_arr = array(
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            '');
        $com_user    = trim($_POST['com_user']);
        $com_user    = $myts->stripSlashesGPC($com_user);
        $com_user    = $myts->xoopsCodeDecode($com_user);
        $com_user    = $myts->filterXss($com_user);
        $com_user    = strip_tags($com_user);
        $com_user    = strtolower($com_user);
        $com_user    = htmlentities($com_user, ENT_COMPAT, 'utf-8');
        $com_user    = preg_replace('`\[.*\]`U', ' ', $com_user);
        $com_user    = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', ' ', $com_user);
        $com_user    = preg_replace('`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\\1', $com_user);
        $com_user    = str_replace($search_arr, $replace_arr, $com_user);

        // Check Url
        if (!empty($_POST['com_url'])) {
            $com_url = trim($_POST['com_url']);
            $com_url = filter_var($com_url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
        }

        // Check Email
        $com_email = $myts->stripSlashesGPC(trim($_POST['com_email']));
        $com_email = htmlspecialchars(trim($com_email), ENT_QUOTES);
        $com_email = filter_var($com_email, FILTER_VALIDATE_EMAIL);
        // Invalid email address
        if (!checkEmail($com_email)) {
            $error_message .= _US_INVALIDMAIL . '<br>';
        }
        if (strrpos($com_email, ' ') > 0) {
            $error_message .= _US_EMAILNOSPACES . '<br>';
        }
        // Check forbidden email address if current operator is not an administrator
        if (!$xoopsUser_isAdmin) {
            foreach ($xoopsConfigUser['bad_emails'] as $be) {
                if (!empty($be) && preg_match('/' . $be . '/i', $com_email)) {
                    $error_message .= _US_INVALIDMAIL . '<br>';
                    break;
                }
            }
        }
        if (!empty($error_message)) {
            $op = 'preview';
        }
        // End add by voltan
    }

    $com_mode   = isset($_POST['com_mode']) ? htmlspecialchars(trim($_POST['com_mode']), ENT_QUOTES) : 'flat';
    $com_order  = isset($_POST['com_order']) ? (int)$_POST['com_order'] : XOOPS_COMMENT_OLD1ST;
    $com_itemid = isset($_POST['com_itemid']) ? (int)$_POST['com_itemid'] : 0;
    $com_pid    = isset($_POST['com_pid']) ? (int)$_POST['com_pid'] : 0;
    $com_rootid = isset($_POST['com_rootid']) ? (int)$_POST['com_rootid'] : 0;
    $com_status = isset($_POST['com_status']) ? (int)$_POST['com_status'] : 0;
    $dosmiley   = (isset($_POST['dosmiley']) && (int)$_POST['dosmiley'] > 0) ? 1 : 0;
    $doxcode    = (isset($_POST['doxcode']) && (int)$_POST['doxcode'] > 0) ? 1 : 0;
    $dobr       = (isset($_POST['dobr']) && (int)$_POST['dobr'] > 0) ? 1 : 0;
    $dohtml     = (isset($_POST['dohtml']) && (int)$_POST['dohtml'] > 0) ? 1 : 0;
    $doimage    = (isset($_POST['doimage']) && (int)$_POST['doimage'] > 0) ? 1 : 0;
    $com_icon   = isset($_POST['com_icon']) ? trim($_POST['com_icon']) : '';
} else {
    exit();
}
/* @var  $xoopsUser XoopsUser */
switch ($op) {
    case 'delete':
        include_once $GLOBALS['xoops']->path('include/comment_delete.php');
        break;

    case 'preview':
        $myts      = MyTextSanitizer::getInstance();
        $doimage   = 1;
        $com_title = $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['com_title']));
        if ($dohtml != 0) {
            if (is_object($xoopsUser)) {
                if (!$xoopsUser->isAdmin($com_modid)) {
                    include_once $GLOBALS['xoops']->path('modules/system/constants.php');
                    /* @var $sysperm_handler XoopsGroupPermHandler  */
                    $sysperm_handler = xoops_getHandler('groupperm');
                    if (!$sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $xoopsUser->getGroups())) {
                        $dohtml = 0;
                    }
                }
            } else {
                $dohtml = 0;
            }
        }
        $p_comment =& $myts->previewTarea($_POST['com_text'], $dohtml, $dosmiley, $doxcode, $doimage, $dobr);
        $noname    = isset($noname) ? (int)$noname : 0;
        $com_text  = $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['com_text']));
        if ($xoopsModule->getVar('dirname') !== 'system') {
            include_once $GLOBALS['xoops']->path('header.php');
            if (!empty($error_message)) {
                xoops_error($error_message);
            }
            echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer">
                  <tr><td class="head">' . $com_title . '</td></tr>
                  <tr><td><br>' . $p_comment . '<br></td></tr>
                  </table>';
            include_once $GLOBALS['xoops']->path('include/comment_form.php');
            include_once $GLOBALS['xoops']->path('footer.php');
        } else {
            xoops_cp_header();
            echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer">
                  <tr><td class="head">' . $com_title . '</td></tr>
                  <tr><td><br>' . $p_comment . '<br></td></tr>
                  </table>';
            include_once $GLOBALS['xoops']->path('include/comment_form.php');
            xoops_cp_footer();
        }
        break;

    case 'post':
        XoopsLoad::load('XoopsRequest');
        $doimage         = 1;
        $comment_handler = xoops_getHandler('comment');
        // Start add by voltan
        $myts = MyTextSanitizer::getInstance();
        // Edit add by voltan
        $add_userpost     = false;
        $call_approvefunc = false;
        $call_updatefunc  = false;
        // RMV-NOTIFY - this can be set to 'comment' or 'comment_submit'
        $notify_event = false;
        if (!empty($com_id)) {
            $comment     = $comment_handler->get($com_id);
            $accesserror = false;

            if (is_object($xoopsUser)) {
                include_once $GLOBALS['xoops']->path('modules/system/constants.php');
                /* @var $sysperm_handler XoopsGroupPermHandler  */
                $sysperm_handler = xoops_getHandler('groupperm');
                if ($xoopsUser->isAdmin($com_modid) || $sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $xoopsUser->getGroups())) {
                    if (!empty($com_status) && $com_status != XOOPS_COMMENT_PENDING) {
                        $old_com_status = $comment->getVar('com_status');
                        $comment->setVar('com_status', $com_status);
                        // if changing status from pending state, increment user post
                        if (XOOPS_COMMENT_PENDING == $old_com_status) {
                            $add_userpost = true;
                            if (XOOPS_COMMENT_ACTIVE == $com_status) {
                                $call_updatefunc  = true;
                                $call_approvefunc = true;
                                // RMV-NOTIFY
                                $notify_event = 'comment';
                            }
                        } elseif (XOOPS_COMMENT_HIDDEN == $old_com_status && XOOPS_COMMENT_ACTIVE == $com_status) {
                            $call_updatefunc = true;
                            // Comments can not be directly posted hidden,
                            // no need to send notification here
                        } elseif (XOOPS_COMMENT_ACTIVE == $old_com_status && XOOPS_COMMENT_HIDDEN == $com_status) {
                            $call_updatefunc = true;
                        }
                    }
                } else {
                    $dohtml = 0;
                    if ($comment->getVar('com_uid') != $xoopsUser->getVar('uid')) {
                        $accesserror = true;
                    }
                }
            } else {
                $dohtml      = 0;
                $accesserror = true;
            }
            if (false !== $accesserror) {
                redirect_header($redirect_page . '=' . $com_itemid . '&amp;com_id=' . $com_id . '&amp;com_mode=' . $com_mode . '&amp;com_order=' . $com_order, 1, _NOPERM);
            }
        } else {
            $comment = $comment_handler->create();
            $comment->setVar('com_created', time());
            $comment->setVar('com_pid', $com_pid);
            $comment->setVar('com_itemid', $com_itemid);
            $comment->setVar('com_rootid', $com_rootid);
            $comment->setVar('com_ip', \Xmf\IPAddress::fromRequest()->asReadable());
            if (is_object($xoopsUser)) {
                include_once $GLOBALS['xoops']->path('modules/system/constants.php');
                /* @var $sysperm_handler XoopsGroupPermHandler  */
                $sysperm_handler = xoops_getHandler('groupperm');
                if ($xoopsUser->isAdmin($com_modid) || $sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $xoopsUser->getGroups())) {
                    $comment->setVar('com_status', XOOPS_COMMENT_ACTIVE);
                    $add_userpost     = true;
                    $call_approvefunc = true;
                    $call_updatefunc  = true;
                    // RMV-NOTIFY
                    $notify_event = 'comment';
                } else {
                    $dohtml = 0;
                    switch ($xoopsModuleConfig['com_rule']) {
                        case XOOPS_COMMENT_APPROVEALL:
                        case XOOPS_COMMENT_APPROVEUSER:
                            $comment->setVar('com_status', XOOPS_COMMENT_ACTIVE);
                            $add_userpost     = true;
                            $call_approvefunc = true;
                            $call_updatefunc  = true;
                            // RMV-NOTIFY
                            $notify_event = 'comment';
                            break;
                        case XOOPS_COMMENT_APPROVEADMIN:
                        default:
                            $comment->setVar('com_status', XOOPS_COMMENT_PENDING);
                            $notify_event = 'comment_submit';
                            break;
                    }
                }
                if (!empty($xoopsModuleConfig['com_anonpost']) && !empty($noname)) {
                    $uid = 0;
                } else {
                    $uid = $xoopsUser->getVar('uid');
                }
            } else {
                $dohtml = 0;
                $uid    = 0;
                if ($xoopsModuleConfig['com_anonpost'] != 1) {
                    redirect_header($redirect_page . '=' . $com_itemid . '&amp;com_id=' . $com_id . '&amp;com_mode=' . $com_mode . '&amp;com_order=' . $com_order, 1, _NOPERM);
                }
            }
            if ($uid == 0) {
                switch ($xoopsModuleConfig['com_rule']) {
                    case XOOPS_COMMENT_APPROVEALL:
                        $comment->setVar('com_status', XOOPS_COMMENT_ACTIVE);
                        $add_userpost     = true;
                        $call_approvefunc = true;
                        $call_updatefunc  = true;
                        // RMV-NOTIFY
                        $notify_event = 'comment';
                        break;
                    case XOOPS_COMMENT_APPROVEADMIN:
                    case XOOPS_COMMENT_APPROVEUSER:
                    default:
                        $comment->setVar('com_status', XOOPS_COMMENT_PENDING);
                        // RMV-NOTIFY
                        $notify_event = 'comment_submit';
                        break;
                }
            }
            $comment->setVar('com_uid', $uid);
        }
        $comment->setVar('com_title', XoopsRequest::getString('com_title', _NOTITLE, 'POST'));
        $comment->setVar('com_text', XoopsRequest::getString('com_text', '', 'POST'));
        $comment->setVar('dohtml', $dohtml);
        $comment->setVar('dosmiley', $dosmiley);
        $comment->setVar('doxcode', $doxcode);
        $comment->setVar('doimage', $doimage);
        $comment->setVar('dobr', $dobr);
        $comment->setVar('com_icon', $com_icon);
        $comment->setVar('com_modified', time());
        $comment->setVar('com_modid', $com_modid);
        // Start add by voltan
        $comment->setVar('com_user', $com_user);
        $comment->setVar('com_email', $com_email);
        $comment->setVar('com_url', $com_url);
        // End add by voltan
        if (isset($extra_params)) {
            $comment->setVar('com_exparams', $extra_params);
        }
        if (false !== $comment_handler->insert($comment)) {
            $newcid = $comment->getVar('com_id');
            // set own id as root id if this is a top comment
            if ($com_rootid == 0) {
                $com_rootid = $newcid;
                if (!$comment_handler->updateByField($comment, 'com_rootid', $com_rootid)) {
                    $comment_handler->delete($comment);
                    include $GLOBALS['xoops']->path('header.php');
                    xoops_error();
                    include $GLOBALS['xoops']->path('footer.php');
                }
            }
            // call custom approve function if any
            if (false !== $call_approvefunc && isset($comment_config['callback']['approve']) && trim($comment_config['callback']['approve']) != '') {
                $skip = false;
                if (!function_exists($comment_config['callback']['approve'])) {
                    if (isset($comment_config['callbackFile'])) {
                        $callbackfile = trim($comment_config['callbackFile']);
                        if ($callbackfile != '' && file_exists($GLOBALS['xoops']->path('modules/' . $moddir . '/' . $callbackfile))) {
                            include_once $GLOBALS['xoops']->path('modules/' . $moddir . '/' . $callbackfile);
                        }
                        if (!function_exists($comment_config['callback']['approve'])) {
                            $skip = true;
                        }
                    } else {
                        $skip = true;
                    }
                }
                if (!$skip) {
                    $comment_config['callback']['approve']($comment);
                }
            }

            // call custom update function if any
            if (false !== $call_updatefunc && isset($comment_config['callback']['update']) && trim($comment_config['callback']['update']) != '') {
                $skip = false;
                if (!function_exists($comment_config['callback']['update'])) {
                    if (isset($comment_config['callbackFile'])) {
                        $callbackfile = trim($comment_config['callbackFile']);
                        if ($callbackfile != '' && file_exists($GLOBALS['xoops']->path('modules/' . $moddir . '/' . $callbackfile))) {
                            include_once $GLOBALS['xoops']->path('modules/' . $moddir . '/' . $callbackfile);
                        }
                        if (!function_exists($comment_config['callback']['update'])) {
                            $skip = true;
                        }
                    } else {
                        $skip = true;
                    }
                }
                if (!$skip) {
                    $criteria = new CriteriaCompo(new Criteria('com_modid', $com_modid));
                    $criteria->add(new Criteria('com_itemid', $com_itemid));
                    $criteria->add(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
                    $comment_count = $comment_handler->getCount($criteria);
                    $func          = $comment_config['callback']['update'];
                    call_user_func_array($func, array(
                        $com_itemid,
                        $comment_count,
                        $comment->getVar('com_id')));
                }
            }

            // increment user post if needed
            $uid = $comment->getVar('com_uid');
            if ($uid > 0 && false !== $add_userpost) {
                /* @var $member_handler XoopsMemberHandler */
                $member_handler = xoops_getHandler('member');
                $poster         = $member_handler->getUser($uid);
                if (is_object($poster)) {
                    $member_handler->updateUserByField($poster, 'posts', $poster->getVar('posts') + 1);
                }
            }

            // RMV-NOTIFY
            // trigger notification event if necessary
            if ($notify_event) {
                $not_modid = $com_modid;
                include_once $GLOBALS['xoops']->path('include/notification_functions.php');
                $not_catinfo  =& notificationCommentCategoryInfo($not_modid);
                $not_category = $not_catinfo['name'];
                $not_itemid   = $com_itemid;
                $not_event    = $notify_event;
                // Build an ABSOLUTE URL to view the comment.  Make sure we
                // point to a viewable page (i.e. not the system administration
                // module).
                $comment_tags = array();
                if ('system' === $xoopsModule->getVar('dirname')) {
                    /* @var $module_handler XoopsModuleHandler */
                    $module_handler = xoops_getHandler('module');
                    $not_module     = $module_handler->get($not_modid);
                } else {
                    $not_module =& $xoopsModule;
                }
                if (!isset($comment_url)) {
                    $com_config  =& $not_module->getInfo('comments');
                    $comment_url = $com_config['pageName'] . '?';
                    if (isset($com_config['extraParams']) && is_array($com_config['extraParams'])) {
                        $extra_params = '';
                        foreach ($com_config['extraParams'] as $extra_param) {
                            $extra_params .= isset($_POST[$extra_param]) ? $extra_param . '=' . htmlspecialchars($_POST[$extra_param]) . '&amp;' : $extra_param . '=&amp;';
                        }
                        $comment_url .= $extra_params;
                    }
                    $comment_url .= $com_config['itemName'];
                }
                $comment_tags['X_COMMENT_URL'] = XOOPS_URL . '/modules/' . $not_module->getVar('dirname') . '/' . $comment_url . '=' . $com_itemid . '&amp;com_id=' . $newcid . '&amp;com_rootid=' . $com_rootid . '&amp;com_mode=' . $com_mode . '&amp;com_order=' . $com_order . '#comment' . $newcid;
                /* @var  $notification_handler XoopsNotificationHandler */
                $notification_handler          = xoops_getHandler('notification');
                $notification_handler->triggerEvent($not_category, $not_itemid, $not_event, $comment_tags, false, $not_modid);
            }
            if (!isset($comment_post_results)) {
                // if the comment is active, redirect to posted comment
                if ($comment->getVar('com_status') == XOOPS_COMMENT_ACTIVE) {
                    redirect_header($redirect_page . '=' . $com_itemid . '&amp;com_id=' . $newcid . '&amp;com_rootid=' . $com_rootid . '&amp;com_mode=' . $com_mode . '&amp;com_order=' . $com_order . '#comment' . $newcid, 1, _CM_THANKSPOST);
                } else {
                    // not active, so redirect to top comment page
                    redirect_header($redirect_page . '=' . $com_itemid . '&amp;com_mode=' . $com_mode . '&amp;com_order=' . $com_order . '#comment' . $newcid, 1, _CM_THANKSPOST);
                }
            }
        } else {
            if (!isset($purge_comment_post_results)) {
                include_once $GLOBALS['xoops']->path('header.php');
                xoops_error($comment->getHtmlErrors());
                include_once $GLOBALS['xoops']->path('footer.php');
            } else {
                $comment_post_results = $comment->getErrors();
            }
        }
        break;
    default:
        redirect_header(XOOPS_URL . '/', 1, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        break;
}
