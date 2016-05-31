<?php
/**
 * XOOPS Deprecated Old Functions
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
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

trigger_error('Functions in ' . __FILE__ . ' are deprecated, should not be used any more', E_USER_WARNING);
// #################### Block functions from here ##################
/*
 * Purpose : Builds the blocks on both sides
 * Input   : $side = On wich side should the block are displayed?
 *             0, l, left : On the left side
 *             1, r, right: On the right side
 *             other:   Only on one side (
 *                          Call from theme.php makes all blocks on the left side
 *                          and from theme.php for the right site)
 */
/**
 * @param $side
 */
function make_sidebar($side)
{
    global $xoopsUser;
    $xoopsblock = new XoopsBlock();
    if ($side === 'left') {
        $side = XOOPS_SIDEBLOCK_LEFT;
    } elseif ($side === 'right') {
        $side = XOOPS_SIDEBLOCK_RIGHT;
    } else {
        $side = XOOPS_SIDEBLOCK_BOTH;
    }
    if (is_object($xoopsUser)) {
        $block_arr = $xoopsblock->getAllBlocksByGroup($xoopsUser->getGroups(), true, $side, XOOPS_BLOCK_VISIBLE);
    } else {
        $block_arr = $xoopsblock->getAllBlocksByGroup(XOOPS_GROUP_ANONYMOUS, true, $side, XOOPS_BLOCK_VISIBLE);
    }

    $block_count = count($block_arr);
    if (!isset($GLOBALS['xoopsTpl']) || !is_object($GLOBALS['xoopsTpl'])) {
        include_once $GLOBALS['xoops']->path('class/template.php');
        $xoopsTpl = new XoopsTpl();
    } else {
        $xoopsTpl =& $GLOBALS['xoopsTpl'];
    }
    $xoopsLogger = XoopsLogger::getInstance();
    for ($i = 0; $i < $block_count; ++$i) {
        $bcachetime = (int)$block_arr[$i]->getVar('bcachetime');
        if (empty($bcachetime)) {
            $xoopsTpl->caching = 0;
        } else {
            $xoopsTpl->caching        = 2;
            $xoopsTpl->cache_lifetime = $bcachetime;
        }
        $btpl = $block_arr[$i]->getVar('template');
        if ($btpl != '') {
            if (empty($bcachetime) || !$xoopsTpl->is_cached('db:' . $btpl)) {
                $xoopsLogger->addBlock($block_arr[$i]->getVar('name'));
                $bresult =& $block_arr[$i]->buildBlock();
                if (!$bresult) {
                    continue;
                }
                $xoopsTpl->assign_by_ref('block', $bresult);
                $bcontent =& $xoopsTpl->fetch('db:' . $btpl);
                $xoopsTpl->clear_assign('block');
            } else {
                $xoopsLogger->addBlock($block_arr[$i]->getVar('name'), true, $bcachetime);
                $bcontent =& $xoopsTpl->fetch('db:' . $btpl);
            }
        } else {
            $bid = $block_arr[$i]->getVar('bid');
            if (empty($bcachetime) || !$xoopsTpl->is_cached('db:system_dummy.tpl', 'blk_' . $bid)) {
                $xoopsLogger->addBlock($block_arr[$i]->getVar('name'));
                $bresult = &$block_arr[$i]->buildBlock();
                if (!$bresult) {
                    continue;
                }
                $xoopsTpl->assign_by_ref('dummy_content', $bresult['content']);
                $bcontent =& $xoopsTpl->fetch('db:system_dummy.tpl', 'blk_' . $bid);
                $xoopsTpl->clear_assign('block');
            } else {
                $xoopsLogger->addBlock($block_arr[$i]->getVar('name'), true, $bcachetime);
                $bcontent =& $xoopsTpl->fetch('db:system_dummy.tpl', 'blk_' . $bid);
            }
        }
        switch ($block_arr[$i]->getVar('side')) {
            case XOOPS_SIDEBLOCK_LEFT:
                themesidebox($block_arr[$i]->getVar('title'), $bcontent);
                break;
            case XOOPS_SIDEBLOCK_RIGHT:
                if (function_exists('themesidebox_right')) {
                    themesidebox_right($block_arr[$i]->getVar('title'), $bcontent);
                } else {
                    themesidebox($block_arr[$i]->getVar('title'), $bcontent);
                }
                break;
        }
        unset($bcontent);
    }
}

/*
 * Function to display center block
 */
function make_cblock()
{
    global $xoopsUser, $xoopsOption;
    $xoopsblock = new XoopsBlock();
    $cc_block   = $cl_block = $cr_block = '';
    $arr        = array();
    if ($xoopsOption['theme_use_smarty'] == 0) {
        if (!isset($GLOBALS['xoopsTpl']) || !is_object($GLOBALS['xoopsTpl'])) {
            include_once $GLOBALS['xoops']->path('class/template.php');
            $xoopsTpl = new XoopsTpl();
        } else {
            $xoopsTpl =& $GLOBALS['xoopsTpl'];
        }
        if (is_object($xoopsUser)) {
            $block_arr = $xoopsblock->getAllBlocksByGroup($xoopsUser->getGroups(), true, XOOPS_CENTERBLOCK_ALL, XOOPS_BLOCK_VISIBLE);
        } else {
            $block_arr = $xoopsblock->getAllBlocksByGroup(XOOPS_GROUP_ANONYMOUS, true, XOOPS_CENTERBLOCK_ALL, XOOPS_BLOCK_VISIBLE);
        }
        $block_count = count($block_arr);
        $xoopsLogger = XoopsLogger::getInstance();
        for ($i = 0; $i < $block_count; ++$i) {
            $bcachetime = (int)$block_arr[$i]->getVar('bcachetime');
            if (empty($bcachetime)) {
                $xoopsTpl->caching = 0;
            } else {
                $xoopsTpl->caching        = 2;
                $xoopsTpl->cache_lifetime = $bcachetime;
            }
            $btpl = $block_arr[$i]->getVar('template');
            if ($btpl != '') {
                if (empty($bcachetime) || !$xoopsTpl->is_cached('db:' . $btpl)) {
                    $xoopsLogger->addBlock($block_arr[$i]->getVar('name'));
                    $bresult =& $block_arr[$i]->buildBlock();
                    if (!$bresult) {
                        continue;
                    }
                    $xoopsTpl->assign_by_ref('block', $bresult);
                    $bcontent =& $xoopsTpl->fetch('db:' . $btpl);
                    $xoopsTpl->clear_assign('block');
                } else {
                    $xoopsLogger->addBlock($block_arr[$i]->getVar('name'), true, $bcachetime);
                    $bcontent =& $xoopsTpl->fetch('db:' . $btpl);
                }
            } else {
                $bid = $block_arr[$i]->getVar('bid');
                if (empty($bcachetime) || !$xoopsTpl->is_cached('db:system_dummy.tpl', 'blk_' . $bid)) {
                    $xoopsLogger->addBlock($block_arr[$i]->getVar('name'));
                    $bresult =& $block_arr[$i]->buildBlock();
                    if (!$bresult) {
                        continue;
                    }
                    $xoopsTpl->assign_by_ref('dummy_content', $bresult['content']);
                    $bcontent =& $xoopsTpl->fetch('db:system_dummy.tpl', 'blk_' . $bid);
                    $xoopsTpl->clear_assign('block');
                } else {
                    $xoopsLogger->addBlock($block_arr[$i]->getVar('name'), true, $bcachetime);
                    $bcontent =& $xoopsTpl->fetch('db:system_dummy.tpl', 'blk_' . $bid);
                }
            }
            $title = $block_arr[$i]->getVar('title');
            switch ($block_arr[$i]->getVar('side')) {
                case XOOPS_CENTERBLOCK_CENTER:
                    if ($title != '') {
                        $cc_block .= '<tr valign="top"><td colspan="2"><strong>' . $title . '</strong><hr />' . $bcontent . '<br><br></td></tr>' . "\n";
                    } else {
                        $cc_block .= '<tr><td colspan="2">' . $bcontent . '<br><br></td></tr>' . "\n";
                    }
                    break;
                case XOOPS_CENTERBLOCK_LEFT:
                    if ($title != '') {
                        $cl_block .= '<p><strong>' . $title . '</strong><hr />' . $bcontent . '</p>' . "\n";
                    } else {
                        $cl_block .= '<p>' . $bcontent . '</p>' . "\n";
                    }
                    break;
                case XOOPS_CENTERBLOCK_RIGHT:
                    if ($title != '') {
                        $cr_block .= '<p><strong>' . $title . '</strong><hr />' . $bcontent . '</p>' . "\n";
                    } else {
                        $cr_block .= '<p>' . $bcontent . '</p>' . "\n";
                    }
                    break;
                default:
                    break;
            }
            unset($bcontent, $title);
        }
        echo '<table width="100%">' . $cc_block . '<tr valign="top"><td width="50%">' . $cl_block . '</td><td width="50%">' . $cr_block . '</td></tr></table>' . "\n";
    }
}

/**
 * Enter description here...
 *
 * @param string|\unknown_type $width
 */
function openThread($width = '100%')
{
    echo "<table border='0' cellpadding='0' cellspacing='0' align='center' width='$width'><tr><td class='bg2'><table border='0' cellpadding='4' cellspacing='1' width='100%'><tr class='bg3' align='left'><td class='bg3' width='20%'>" . _CM_POSTER . "</td><td class='bg3'>" . _CM_THREAD . '</td></tr>';
}

/**
 * showThread :: DO NOT USE
 *
 * @param unknown_type         $color_number
 * @param unknown_type         $subject_image
 * @param unknown_type         $subject
 * @param unknown_type         $text
 * @param unknown_type         $post_date
 * @param unknown_type         $ip_image
 * @param unknown_type         $reply_image
 * @param unknown_type         $edit_image
 * @param unknown_type         $delete_image
 * @param string|\unknown_type $username
 * @param string|\unknown_type $rank_title
 * @param string|\unknown_type $rank_image
 * @param string|\unknown_type $avatar_image
 * @param string|\unknown_type $reg_date
 * @param string|\unknown_type $posts
 * @param string|\unknown_type $user_from
 * @param string|\unknown_type $online_image
 * @param string|\unknown_type $profile_image
 * @param string|\unknown_type $pm_image
 * @param string|\unknown_type $email_image
 * @param string|\unknown_type $www_image
 * @param string|\unknown_type $icq_image
 * @param string|\unknown_type $aim_image
 * @param string|\unknown_type $yim_image
 * @param string|\unknown_type $msnm_image
 */
function showThread($color_number, $subject_image, $subject, $text, $post_date, $ip_image, $reply_image, $edit_image, $delete_image, $username = '', $rank_title = '', $rank_image = '', $avatar_image = '', $reg_date = '', $posts = '', $user_from = '', $online_image = '', $profile_image = '', $pm_image = '', $email_image = '', $www_image = '', $icq_image = '', $aim_image = '', $yim_image = '', $msnm_image = '')
{
    $bg = 'bg3';
    if ($color_number == 1) {
        $bg = 'bg1';
    }
    echo "<tr align='left'><td valign='top' class='$bg' nowrap='nowrap'><strong>$username</strong><br>$rank_title<br>$rank_image<br>$avatar_image<br><br>$reg_date<br>$posts<br>$user_from<br><br>$online_image</td>";
    echo "<td valign='top' class='$bg'><table width='100%' border='0'><tr><td valign='top'>$subject_image&nbsp;<strong>$subject</strong></td><td align='right'>" . $ip_image . '' . $reply_image . '' . $edit_image . '' . $delete_image . '</td></tr>';
    echo "<tr><td colspan='2'><p>$text</p></td></tr></table></td></tr>";
    echo "<tr align='left'><td class='$bg' valign='middle'>$post_date</td><td class='$bg' valign='middle'>" . $profile_image . '' . $pm_image . '' . $email_image . '' . $www_image . '' . $icq_image . '' . $aim_image . '' . $yim_image . '' . $msnm_image . '</td></tr>';
}

/**
 * Enter description here...
 *
 */
function closeThread()
{
    echo '</table></td></tr></table>';
}
