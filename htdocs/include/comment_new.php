<?php
/**
 * XOOPS comment creation
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

if (('system' !== $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) || (!is_object($xoopsUser) && !$xoopsModuleConfig['com_anonpost']) || !is_object($xoopsModule)) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

xoops_loadLanguage('comment');

$com_itemid = isset($_GET['com_itemid']) ? (int)$_GET['com_itemid'] : 0;
if ($com_itemid > 0) {
    include_once $GLOBALS['xoops']->path('header.php');
    if (isset($com_replytitle)) {
        if (isset($com_replytext)) {
            echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer">
                  <tr><td class="head">' . $com_replytitle . '</td></tr>
                  <tr><td><br>' . $com_replytext . '<br></td></tr>
                  </table>';
        }
        $myts      = MyTextSanitizer::getInstance();
        $com_title = $myts->htmlSpecialChars($com_replytitle);
        if (!preg_match('/^' . _RE . '/i', $com_title)) {
            $com_title = _RE . ' ' . xoops_substr($com_title, 0, 56);
        }
    } else {
        $com_title = '';
    }
    $com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
    /* @var  $xoopsUser XoopsUser */
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
    $com_id     = 0;
    $noname     = 0;
    $dosmiley   = 1;
    $dohtml     = 0;
    $dobr       = 1;
    $doxcode    = 1;
    $com_icon   = '';
    $com_pid    = 0;
    $com_rootid = 0;
    $com_text   = '';
    // Start Add by voltan
    $com_user  = '';
    $com_email = '';
    $com_url   = '';
    // End Add by voltan
    include_once $GLOBALS['xoops']->path('include/comment_form.php');
    include_once $GLOBALS['xoops']->path('footer.php');
}
