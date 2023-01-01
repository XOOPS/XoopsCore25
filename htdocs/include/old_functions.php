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
 * @copyright       (c) 2000-2023 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

trigger_error('Functions in ' . __FILE__ . ' are deprecated, should not be used any more', E_USER_WARNING);

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
