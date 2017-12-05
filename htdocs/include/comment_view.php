<?php
/**
 * XOOPS comment view
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
/* @var  $xoopsUser XoopsUser */
/* @var $xoopsConfig XoopsConfigItem */

if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    die('Restricted access');
}

include_once $GLOBALS['xoops']->path('include/comment_constants.php');

if (XOOPS_COMMENT_APPROVENONE != $xoopsModuleConfig['com_rule']) {
    xoops_load('XoopsLists');
    xoops_load('XoopsFormLoader');

    include_once $GLOBALS['xoops']->path('modules/system/constants.php');
    /* @var  $gperm_handler XoopsGroupPermHandler */
    $gperm_handler = xoops_getHandler('groupperm');
    $groups        = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $xoopsTpl->assign('xoops_iscommentadmin', $gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $groups));

    xoops_loadLanguage('comment');

    $comment_config = $xoopsModule->getInfo('comments');
    $com_itemid     = (trim($comment_config['itemName']) != '' && isset($_GET[$comment_config['itemName']])) ? (int)$_GET[$comment_config['itemName']] : 0;
    if ($com_itemid > 0) {
        $com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
        if ($com_mode == '') {
            if (is_object($xoopsUser)) {
                $com_mode = $xoopsUser->getVar('umode');
            }
            $com_mode = empty($com_mode) ? $xoopsConfig['com_mode'] : $com_mode;
        }
        $xoopsTpl->assign('comment_mode', $com_mode);
        if (!isset($_GET['com_order'])) {
            if (is_object($xoopsUser)) {
                $com_order = $xoopsUser->getVar('uorder');
            } else {
                $com_order = $xoopsConfig['com_order'];
            }
        } else {
            $com_order = (int)$_GET['com_order'];
        }
        if ($com_order != XOOPS_COMMENT_OLD1ST) {
            $xoopsTpl->assign(array(
                                  'comment_order' => XOOPS_COMMENT_NEW1ST,
                                  'order_other'   => XOOPS_COMMENT_OLD1ST));
            $com_dborder = 'DESC';
        } else {
            $xoopsTpl->assign(array(
                                  'comment_order' => XOOPS_COMMENT_OLD1ST,
                                  'order_other'   => XOOPS_COMMENT_NEW1ST));
            $com_dborder = 'ASC';
        }
        // admins can view all comments and IPs, others can only view approved(active) comments
        $admin_view = false;
        if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $admin_view = true;
        }

        $com_id          = isset($_GET['com_id']) ? (int)$_GET['com_id'] : 0;
        $com_rootid      = isset($_GET['com_rootid']) ? (int)$_GET['com_rootid'] : 0;
        /* @var  $comment_handler XoopsCommentHandler */
        $comment_handler = xoops_getHandler('comment');
        if ($com_mode === 'flat') {
            $comments = $comment_handler->getByItemId($xoopsModule->getVar('mid'), $com_itemid, $com_dborder);
            include_once $GLOBALS['xoops']->path('class/commentrenderer.php');
            $renderer = XoopsCommentRenderer::instance($xoopsTpl);
            $renderer->setComments($comments);
            $renderer->renderFlatView($admin_view);
        } elseif ($com_mode === 'thread') {
            // RMV-FIX... added extraParam stuff here
            $comment_url = $comment_config['pageName'] . '?';
            if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
                $extra_params = '';
                foreach ($comment_config['extraParams'] as $extra_param) {
                    // This page is included in the module hosting page -- param could be from anywhere
                    if (isset(${$extra_param})) {
                        $extra_params .= $extra_param . '=' . ${$extra_param} . '&amp;';
                    } elseif (isset($_POST[$extra_param])) {
                        $extra_params .= $extra_param . '=' . $_POST[$extra_param] . '&amp;';
                    } elseif (isset($_GET[$extra_param])) {
                        $extra_params .= $extra_param . '=' . $_GET[$extra_param] . '&amp;';
                    } else {
                        $extra_params .= $extra_param . '=&amp;';
                    }
                    //$extra_params .= isset(${$extra_param}) ? $extra_param .'='.${$extra_param}.'&amp;' : $extra_param .'=&amp;';
                }
                $comment_url .= $extra_params;
            }
            $xoopsTpl->assign('comment_url', $comment_url . $comment_config['itemName'] . '=' . $com_itemid . '&amp;com_mode=thread&amp;com_order=' . $com_order);
            if (!empty($com_id) && !empty($com_rootid) && ($com_id != $com_rootid)) {
                // Show specific thread tree
                $comments = $comment_handler->getThread($com_rootid, $com_id);
//                if (false != $comments) {
                if (!empty($comments)) {  // getThread always returns array - changed in 2.5.9
                    include_once $GLOBALS['xoops']->path('class/commentrenderer.php');
                    $renderer = XoopsCommentRenderer::instance($xoopsTpl);
                    $renderer->setComments($comments);
                    $renderer->renderThreadView($com_id, $admin_view);
                }
            } else {
                // Show all threads
                $top_comments = $comment_handler->getTopComments($xoopsModule->getVar('mid'), $com_itemid, $com_dborder);
                $c_count      = count($top_comments);
                if ($c_count > 0) {
                    for ($i = 0; $i < $c_count; ++$i) {
                        $comments = $comment_handler->getThread($top_comments[$i]->getVar('com_rootid'), $top_comments[$i]->getVar('com_id'));
//                        if (false != $comments) {
                        if (!empty($comments)) {  // $getThread always returns array - changed in 2.5.9
                            include_once $GLOBALS['xoops']->path('class/commentrenderer.php');
                            $renderer = XoopsCommentRenderer::instance($xoopsTpl);
                            $renderer->setComments($comments);
                            $renderer->renderThreadView($top_comments[$i]->getVar('com_id'), $admin_view);
                        }
                        unset($comments);
                    }
                }
            }
        } else {
            // Show all threads
            $top_comments = $comment_handler->getTopComments($xoopsModule->getVar('mid'), $com_itemid, $com_dborder);
            $c_count      = count($top_comments);
            if ($c_count > 0) {
                for ($i = 0; $i < $c_count; ++$i) {
                    $comments = $comment_handler->getThread($top_comments[$i]->getVar('com_rootid'), $top_comments[$i]->getVar('com_id'));
                    include_once $GLOBALS['xoops']->path('class/commentrenderer.php');
                    $renderer = XoopsCommentRenderer::instance($xoopsTpl);
                    $renderer->setComments($comments);
                    $renderer->renderNestView($top_comments[$i]->getVar('com_id'), $admin_view);
                }
            }
        }

        $commentTpl = new \XoopsTpl();
        //$commentTpl->template = "db:system_comment_controls.tpl";
        //$commentTpl->init();
        $commentTpl->assign('pageName', $comment_config['pageName']);

        $commentModeSelect = new XoopsFormSelect('', 'com_mode', $com_mode);
        $commentModeSelect->addOption('flat', _FLAT);
        $commentModeSelect->addOption('thread', _THREADED);
        $commentModeSelect->addOption('nest', _NESTED);
        $commentTpl->assign('commentModeSelect', $commentModeSelect);

        $commentOrderSelect = new XoopsFormSelect('', 'com_order', $com_order);
        $commentOrderSelect->addOption(XOOPS_COMMENT_OLD1ST, _OLDESTFIRST);
        $commentOrderSelect->addOption(XOOPS_COMMENT_NEW1ST, _NEWESTFIRST);
        $commentTpl->assign('commentOrderSelect', $commentOrderSelect);

        $commentRefreshButton = new XoopsFormButton('', 'com_refresh', _CM_REFRESH, 'submit');
        $commentTpl->assign('commentRefreshButton', $commentRefreshButton);

        unset($postcomment_link);
        if (!empty($xoopsModuleConfig['com_anonpost']) || is_object($xoopsUser)) {
            $postcomment_link = 'comment_new.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode;

            $xoopsTpl->assign('anon_canpost', true); // to main template
        }

        $commentBarHidden = '';
        $commentBarHidden .= '<input type="hidden" name="' . $comment_config['itemName']
            . '" value="' . $com_itemid . '" />';
        $link_extra = '';
        if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
            foreach ($comment_config['extraParams'] as $extra_param) {
                if (isset(${$extra_param})) {
                    $link_extra .= '&amp;' . $extra_param . '=' . ${$extra_param};
                    $hidden_value    = htmlspecialchars(${$extra_param}, ENT_QUOTES);
                    $extra_param_val = ${$extra_param};
                } elseif (isset($_POST[$extra_param])) {
                    $extra_param_val = $_POST[$extra_param];
                } elseif (isset($_GET[$extra_param])) {
                    $extra_param_val = $_GET[$extra_param];
                }
                if (isset($extra_param_val)) {
                    $link_extra .= '&amp;' . $extra_param . '=' . $extra_param_val;
                    $hidden_value = htmlspecialchars($extra_param_val, ENT_QUOTES);
                    $commentBarHidden .= '<input type="hidden" name="' . $extra_param . '" value="' . $hidden_value . '" />';
                }
            }
        }

        $commentPostButton = false;
        if (!empty($xoopsModuleConfig['com_anonpost']) || is_object($xoopsUser)) {
            $commentPostButton = new XoopsFormButton('', 'com_post', _CM_POSTCOMMENT, 'button');
            $commentPostButton->setExtra(' onclick="self.location.href=\'' . $postcomment_link . $link_extra . '\'"');
        }
        $commentTpl->assign('commentPostButton', $commentPostButton);
        $commentTpl->assign('commentPostHidden', $commentBarHidden);

        $navbar = $commentTpl->fetch('db:system_comments_controls.tpl');

        if (!empty($xoopsModuleConfig['com_anonpost']) || is_object($xoopsUser)) {
            if (file_exists($GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/comment_fast.php'))) {
                include_once $GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/comment_fast.php');
            }
            if (isset($com_replytitle)) {
                $myts      = MyTextSanitizer::getInstance();
                $com_title = $myts->htmlSpecialChars($com_replytitle);
                if (!preg_match('/^' . _RE . '/i', $com_title)) {
                    $com_title = _RE . ' ' . xoops_substr($com_title, 0, 56);
                }
            } else {
                $com_title = '';
            }

            // set form
            $cform = new XoopsThemeForm(_CM_POSTCOMMENT, 'commentfastform', 'comment_post.php', 'post', true);
            $cform->addElement(new XoopsFormElementTray(''));
            if (isset($xoopsModuleConfig['com_rule'])) {
                include_once $GLOBALS['xoops']->path('include/comment_constants.php');
                switch ($xoopsModuleConfig['com_rule']) {
                    case XOOPS_COMMENT_APPROVEALL:
                        $rule_text = _CM_COMAPPROVEALL;
                        break;
                    case XOOPS_COMMENT_APPROVEUSER:
                        $rule_text = _CM_COMAPPROVEUSER;
                        break;
                    case XOOPS_COMMENT_APPROVEADMIN:
                    default:
                        $rule_text = _CM_COMAPPROVEADMIN;
                        break;
                }
                $cform->addElement(new XoopsFormLabel(_CM_COMRULES, $rule_text));
            }
            $cform->addElement(new XoopsFormText(_CM_TITLE, 'com_title', 50, 255, $com_title), true);
            if (!$xoopsUser) {
                $cform->addElement(new XoopsFormText(_CM_USER, 'com_user', 50, 60, ''), true);
                $cform->addElement(new XoopsFormText(_CM_EMAIL, 'com_email', 50, 60, ''), true);
                $cform->addElement(new XoopsFormText(_CM_URL, 'com_url', 50, 60, ''), false);
            }
            $cform->addElement(new XoopsFormTextArea(_CM_MESSAGE, 'com_text', '', 10, 65), true);
            if (!$xoopsUser) {
                $cform->addElement(new XoopsFormCaptcha());
            }

            $cform->addElement(new XoopsFormHidden('com_id', 0));
            $cform->addElement(new XoopsFormHidden('com_pid', 0));
            $cform->addElement(new XoopsFormHidden('com_rootid', 0));
            $cform->addElement(new XoopsFormHidden('com_order', 0));
            $cform->addElement(new XoopsFormHidden('com_itemid', $com_itemid));
            $cform->addElement(new XoopsFormHidden('com_mode', $com_mode));
            $cform->addElement(new xoopsFormHidden('dohtml', 0));
            $cform->addElement(new xoopsFormHidden('dobr', 0));
            $cform->addElement(new xoopsFormHidden('dosmiley', 0));
            $cform->addElement(new xoopsFormHidden('doxcode', 0));

            // add module specific extra params
            if ('system' !== $xoopsModule->getVar('dirname')) {
                $comment_config = $xoopsModule->getInfo('comments');
                if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
                    $myts = MyTextSanitizer::getInstance();
                    foreach ($comment_config['extraParams'] as $extra_param) {
                        // This routine is included from forms accessed via both GET and POST
                        $hidden_value = '';
                        if (isset($_POST[$extra_param])) {
                            $hidden_value = $myts->stripSlashesGPC($_POST[$extra_param]);
                        } elseif (isset($_GET[$extra_param])) {
                            $hidden_value = $myts->stripSlashesGPC($_GET[$extra_param]);
                        }
                        $cform->addElement(new XoopsFormHidden($extra_param, $hidden_value));
                    }
                }
            }

            $button_tray = new XoopsFormElementTray('', '&nbsp;');
            $button_tray->addElement(new XoopsFormButton('', 'com_dopost', _CM_POSTCOMMENT, 'submit'));
            $cform->addElement($button_tray);
            $xoopsTpl->assign('commentform', $cform->render());
        } else {
            $xoopsTpl->assign('commentform', '');
        }
        // End add by voltan

        $xoopsTpl->assign(array(
                              'commentsnav'        => $navbar,
                              'editcomment_link'   => 'comment_edit.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '' . $link_extra,
                              'deletecomment_link' => 'comment_delete.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '' . $link_extra,
                              'replycomment_link'  => 'comment_reply.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '' . $link_extra));

        // assign some lang variables
        $xoopsTpl->assign(array(
                              'lang_from'    => _CM_FROM,
                              'lang_joined'  => _CM_JOINED,
                              'lang_posts'   => _CM_POSTS,
                              'lang_poster'  => _CM_POSTER,
                              'lang_thread'  => _CM_THREAD,
                              'lang_edit'    => _EDIT,
                              'lang_delete'  => _DELETE,
                              'lang_reply'   => _REPLY,
                              'lang_subject' => _CM_REPLIES,
                              'lang_posted'  => _CM_POSTED,
                              'lang_updated' => _CM_UPDATED,
                              'lang_notice'  => _CM_NOTICE));
    }
}
