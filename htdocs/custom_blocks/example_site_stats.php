<?php
/**
 * Example Custom Block: Site Statistics
 *
 * Demonstrates a custom block that performs multiple database queries
 * to display site-wide statistics (total users, total posts, newest member).
 *
 * To use this block:
 *   1. Go to System Admin > Blocks > Add New Block
 *   2. Select content type "PHP Script (file-based)"
 *   3. Enter in the content field: example_site_stats.php|b_custom_site_stats_show
 *   4. Save the block
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Display site statistics (total users, total posts, newest member)
 *
 * @return string HTML content for the block
 */
function b_custom_site_stats_show()
{
    /** @var XoopsMemberHandler $member_handler */
    $member_handler = xoops_getHandler('member');

    // Count active users
    $criteria = new Criteria('level', 0, '>');
    $totalUsers = $member_handler->getUserCount($criteria);

    // Get newest member
    $newestCriteria = new CriteriaCompo();
    $newestCriteria->add(new Criteria('level', 0, '>'));
    $newestCriteria->setSort('user_regdate');
    $newestCriteria->setOrder('DESC');
    $newestCriteria->setLimit(1);
    $newest = $member_handler->getUsers($newestCriteria);
    $newestName = !empty($newest)
        ? htmlspecialchars($newest[0]->getVar('uname', 'n'), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        : 'N/A';
    $newestUid = !empty($newest) ? (int) $newest[0]->getVar('uid') : 0;

    // Count total posts (comments)
    /** @var XoopsCommentHandler $comment_handler */
    $comment_handler = xoops_getHandler('comment');
    $totalPosts = $comment_handler->getCount();

    $html  = '<table class="custom-stats-block" style="width:100%;">';
    $html .= '<tr><td>Total Members:</td><td style="text-align:right;"><strong>'
          . number_format($totalUsers) . '</strong></td></tr>';
    $html .= '<tr><td>Total Posts:</td><td style="text-align:right;"><strong>'
          . number_format($totalPosts) . '</strong></td></tr>';
    $html .= '<tr><td>Newest Member:</td><td style="text-align:right;">';
    if ($newestUid > 0) {
        $html .= '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $newestUid . '">'
              . $newestName . '</a>';
    } else {
        $html .= $newestName;
    }
    $html .= '</td></tr>';
    $html .= '</table>';

    return $html;
}
