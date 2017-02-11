<?php
/**
 * XOOPS comment reply
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
/* @var $xoopsModule XoopsModule */
/* @var $xoopsConfig XoopsConfigItem */


defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once $GLOBALS['xoops']->path('include/comment_constants.php');

if (('system' !== $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) || (!is_object($xoopsUser) && !$xoopsModuleConfig['com_anonpost']) || !is_object($xoopsModule)) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

xoops_loadLanguage('comment');
$com_id   = isset($_GET['com_id']) ? (int)$_GET['com_id'] : 0;
$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
if ($com_mode == '') {
    if (is_object($xoopsUser)) {
        $com_mode = $xoopsUser->getVar('umode');
    } else {
        $com_mode = $xoopsConfig['com_mode'];
    }
}
if (!isset($_GET['com_order'])) {
    if (is_object($xoopsUser)) {
        $com_order = $xoopsUser->getVar('uorder');
    } else {
        $com_order = $xoopsConfig['com_order'];
    }
} else {
    $com_order = (int)$_GET['com_order'];
}
$comment_handler = xoops_getHandler('comment');
$comment         = $comment_handler->get($com_id);

// Start edit by voltan
if ($comment->getVar('com_uid') == 0 && $comment->getVar('com_user') != '') {
    $r_name = $comment->getVar('com_user');
} else {
    $r_name = XoopsUser::getUnameFromId($comment->getVar('com_uid'));
}
// End edit by voltan

$r_text    = _CM_POSTER . ': <strong>' . $r_name . '</strong>&nbsp;&nbsp;' . _CM_POSTED . ': <strong>' . formatTimestamp($comment->getVar('com_created')) . '</strong><br><br>' . $comment->getVar('com_text');
$com_title = $comment->getVar('com_title', 'E');
if (!preg_match('/^' . _RE . '/i', $com_title)) {
    $com_title = _RE . ' ' . xoops_substr($com_title, 0, 56);
}
$com_pid    = $com_id;
$com_text   = '';
$com_id     = 0;
$dosmiley   = 1;
$dohtml     = 0;
$doxcode    = 1;
$dobr       = 1;
$doimage    = 1;
$com_icon   = '';
$com_rootid = $comment->getVar('com_rootid');
$com_itemid = $comment->getVar('com_itemid');
// Start Add by voltan
$com_user  = '';
$com_email = '';
$com_url   = '';
// End Add by voltan

include_once $GLOBALS['xoops']->path('header.php');
echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer">
      <tr><td class="head">' . $comment->getVar('com_title') . '</td></tr>
      <tr><td><br>' . $r_text . '<br></td></tr>
      </table>';
include_once $GLOBALS['xoops']->path('include/comment_form.php');
include_once $GLOBALS['xoops']->path('footer.php');
