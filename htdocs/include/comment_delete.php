<?php
/**
 * XOOPS comment delete
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

if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    die('Restricted access');
}

include_once $GLOBALS['xoops']->path('include/comment_constants.php');

$op = 'delete';

$filters = array(
    'com_mode' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'op' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'com_order' => FILTER_VALIDATE_INT,
    'com_id' => FILTER_VALIDATE_INT);

if (!empty($_POST)) {
    $result = filter_input_array(INPUT_POST, $filters);
} else {
    $result = filter_input_array(INPUT_GET, $filters);
}
$com_mode  = $result['com_mode'] ?: 'flat';
$com_order = $result['com_order'] ?: XOOPS_COMMENT_OLD1ST;
$com_id    = $result['com_id'] ?: 0;
if ($result['op']) {
    $op = $result['op'];
}

if ('system' === $xoopsModule->getVar('dirname')) {
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
    if (XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) {
        exit();
    }
    $comment_config        = $xoopsModule->getInfo('comments');
    $com_modid             = $xoopsModule->getVar('mid');
    $redirect_page         = $comment_config['pageName'] . '?';
    $comment_confirm_extra = array();
    if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
        foreach ($comment_config['extraParams'] as $extra_param) {
            if (isset(${$extra_param})) {
                $redirect_page .= $extra_param . '=' . ${$extra_param} . '&amp;';

                // for the confirmation page
                $comment_confirm_extra[$extra_param] = ${$extra_param};
            } elseif (isset($_GET[$extra_param])) {
                //                $redirect_page .= $extra_param . '=' . $_GET[$extra_param] . '&amp;';
                $redirect_page .= $extra_param . '=' . filter_input(INPUT_GET, $extra_param, FILTER_SANITIZE_STRING) . '&amp;';

                // for the confirmation page
                //                $comment_confirm_extra[$extra_param] = $_GET[$extra_param];
                $comment_confirm_extra[$extra_param] = filter_input(INPUT_GET, $extra_param, FILTER_SANITIZE_STRING);
            }
        }
    }
    $redirect_page .= $comment_config['itemName'];
    $moddir = $xoopsModule->getVar('dirname');
}
/* @var  $xoopsUser XoopsUser */
$accesserror = false;
if (!is_object($xoopsUser)) {
    $accesserror = true;
} else {
    if (!$xoopsUser->isAdmin($com_modid)) {
        include_once $GLOBALS['xoops']->path('modules/system/constants.php');
        /* @var  $sysperm_handler XoopsGroupPermHandler */
        $sysperm_handler = xoops_getHandler('groupperm');
        if (!$sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $xoopsUser->getGroups())) {
            $accesserror = true;
        }
    }
}

if (false !== $accesserror) {
    $ref = xoops_getenv('HTTP_REFERER');
    if ($ref != '') {
        redirect_header($ref, 2, _NOPERM);
    } else {
        redirect_header($redirect_page . '?' . $comment_config['itemName'] . '=' . (int)$com_itemid, 2, _NOPERM);
    }
    exit();
}

xoops_loadLanguage('comment');
switch ($op) {
    case 'delete_one':
        /* @var  $comment_handler XoopsCommentHandler */
        $comment_handler = xoops_getHandler('comment');
        $comment         = $comment_handler->get($com_id);
        if (!$comment_handler->delete($comment)) {
            include_once $GLOBALS['xoops']->path('header.php');
            xoops_error(_CM_COMDELETENG . ' (ID: ' . $comment->getVar('com_id') . ')');
            include_once $GLOBALS['xoops']->path('footer');
            exit();
        }

        $com_itemid = $comment->getVar('com_itemid');
        // execute updateStat callback function if set
        if (isset($comment_config['callback']['update']) && trim($comment_config['callback']['update']) != '') {
            $skip = false;
            if (!function_exists($comment_config['callback']['update'])) {
                if (isset($comment_config['callbackFile'])) {
                    $callbackfile = trim($comment_config['callbackFile']);
                    if ($callbackfile != '' && file_exists($file = $GLOBALS['xoops']->path('modules/' . $moddir . '/' . $callbackfile))) {
                        include_once $file;
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
                $comment_config['callback']['update']($com_itemid, $comment_count);
            }
        }

        // update user posts if its not an anonymous post
        if ($comment->getVar('com_uid') != 0) {
            /* @var $member_handler XoopsMemberHandler */
            $member_handler = xoops_getHandler('member');
            $com_poster     = $member_handler->getUser($comment->getVar('com_uid'));
            if (is_object($com_poster)) {
                $member_handler->updateUserByField($com_poster, 'posts', $com_poster->getVar('posts') - 1);
            }
        }

        // get all comments posted later within the same thread
        $thread_comments =& $comment_handler->getThread($comment->getVar('com_rootid'), $com_id);
        include_once $GLOBALS['xoops']->path('class/tree.php');
        $xot            = new XoopsObjectTree($thread_comments, 'com_id', 'com_pid', 'com_rootid');
        $child_comments =& $xot->getFirstChild($com_id);
        // now set new parent ID for direct child comments
        $new_pid = $comment->getVar('com_pid');
        $errs    = array();
        foreach (array_keys($child_comments) as $i) {
            $child_comments[$i]->setVar('com_pid', $new_pid);
            // if the deleted comment is a root comment, need to change root id to own id
            if (false !== $comment->isRoot()) {
                $new_rootid = $child_comments[$i]->getVar('com_id');
                $child_comments[$i]->setVar('com_rootid', $child_comments[$i]->getVar('com_id'));
                if (!$comment_handler->insert($child_comments[$i])) {
                    $errs[] = 'Could not change comment parent ID from <strong>' . $com_id . '</strong> to <strong>' . $new_pid . '</strong>. (ID: ' . $new_rootid . ')';
                } else {
                    // need to change root id for all its child comments as well
                    $c_child_comments = &$xot->getAllChild($new_rootid);
                    $cc_count         = count($c_child_comments);
                    foreach (array_keys($c_child_comments) as $j) {
                        $c_child_comments[$j]->setVar('com_rootid', $new_rootid);
                        if (!$comment_handler->insert($c_child_comments[$j])) {
                            $errs[] = 'Could not change comment root ID from <strong>' . $com_id . '</strong> to <strong>' . $new_rootid . '</strong>.';
                        }
                    }
                }
            } else {
                if (!$comment_handler->insert($child_comments[$i])) {
                    $errs[] = 'Could not change comment parent ID from <strong>' . $com_id . '</strong> to <strong>' . $new_pid . '</strong>.';
                }
            }
        }
        if (count($errs) > 0) {
            include_once $GLOBALS['xoops']->path('header.php');
            xoops_error($errs);
            include_once $GLOBALS['xoops']->path('footer.php');
            exit();
        }
        redirect_header($redirect_page . '=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode, 1, _CM_COMDELETED);
        break;

    case 'delete_all':
        /* @var  $comment_handler XoopsCommentHandler */
        $comment_handler = xoops_getHandler('comment');
        $comment         = $comment_handler->get($com_id);
        $com_rootid      = $comment->getVar('com_rootid');

        // get all comments posted later within the same thread
        $thread_comments =& $comment_handler->getThread($com_rootid, $com_id);

        // construct a comment tree
        include_once $GLOBALS['xoops']->path('class/tree.php');
        $xot            = new XoopsObjectTree($thread_comments, 'com_id', 'com_pid', 'com_rootid');
        $child_comments =& $xot->getAllChild($com_id);
        // add itself here
        $child_comments[$com_id] = &$comment;
        $msgs                    = array();
        $deleted_num             = array();
        /* @var $member_handler XoopsMemberHandler */
        $member_handler          = xoops_getHandler('member');
        foreach (array_keys($child_comments) as $i) {
            if (!$comment_handler->delete($child_comments[$i])) {
                $msgs[] = _CM_COMDELETENG . ' (ID: ' . $child_comments[$i]->getVar('com_id') . ')';
            } else {
                $msgs[] = _CM_COMDELETED . ' (ID: ' . $child_comments[$i]->getVar('com_id') . ')';
                // store poster ID and deleted post number into array for later use
                $poster_id = $child_comments[$i]->getVar('com_uid');
                if ($poster_id > 0) {
                    $deleted_num[$poster_id] = !isset($deleted_num[$poster_id]) ? 1 : ($deleted_num[$poster_id] + 1);
                }
            }
        }
        foreach ($deleted_num as $user_id => $post_num) {
            // update user posts
            $com_poster = $member_handler->getUser($user_id);
            if (is_object($com_poster)) {
                $member_handler->updateUserByField($com_poster, 'posts', $com_poster->getVar('posts') - $post_num);
            }
        }

        $com_itemid = $comment->getVar('com_itemid');
        // execute updateStat callback function if set
        if (isset($comment_config['callback']['update']) && trim($comment_config['callback']['update']) != '') {
            $skip = false;
            if (!function_exists($comment_config['callback']['update'])) {
                if (isset($comment_config['callbackFile'])) {
                    $callbackfile = trim($comment_config['callbackFile']);
                    if ($callbackfile != '' && file_exists($file = $GLOBALS['xoops']->path('modules/' . $moddir . '/' . $callbackfile))) {
                        include_once $file;
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
                $comment_config['callback']['update']($com_itemid, $comment_count);
            }
        }
        include_once $GLOBALS['xoops']->path('header.php');
        xoops_result($msgs);
        echo '<br><a href="' . $redirect_page . '=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '">' . _BACK . '</a>';
        include_once $GLOBALS['xoops']->path('footer.php');
        break;

    case 'delete':
    default:
        include_once $GLOBALS['xoops']->path('header.php');
        $comment_confirm = array(
            'com_id'    => $com_id,
            'com_mode'  => $com_mode,
            'com_order' => $com_order,
            'op'        => array(
                _CM_DELETEONE => 'delete_one',
                _CM_DELETEALL => 'delete_all'));
        if (!empty($comment_confirm_extra) && is_array($comment_confirm_extra)) {
            $comment_confirm += $comment_confirm_extra;
        }
        xoops_confirm($comment_confirm, 'comment_delete.php', _CM_DELETESELECT);
        include_once $GLOBALS['xoops']->path('footer.php');
        break;
}
