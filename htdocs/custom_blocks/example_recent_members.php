<?php
/**
 * Example Custom Block: Recent Members with Avatars
 *
 * Demonstrates a custom block that queries the database using
 * XOOPS handlers to display recent members with their avatars.
 *
 * To use this block:
 *   1. Go to System Admin > Blocks > Add New Block
 *   2. Select content type "PHP Script (file-based)"
 *   3. Enter in the content field: example_recent_members.php|b_custom_recent_members_show
 *   4. Save the block
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Display a list of the 5 most recent members with avatars
 *
 * @return string HTML content for the block
 */
function b_custom_recent_members_show()
{
    /** @var XoopsMemberHandler $member_handler */
    $member_handler = xoops_getHandler('member');

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('level', 0, '>'));
    $criteria->setSort('user_regdate');
    $criteria->setOrder('DESC');
    $criteria->setLimit(5);

    $users = $member_handler->getUsers($criteria);

    if (empty($users)) {
        return '<p>No members found.</p>';
    }

    $html = '<ul class="custom-recent-members" style="list-style:none; padding:0;">';
    foreach ($users as $user) {
        $uid    = $user->getVar('uid');
        $uname  = htmlspecialchars($user->getVar('uname'), ENT_QUOTES | ENT_HTML5);
        $avatar = $user->getVar('user_avatar');
        $date   = date('M j', $user->getVar('user_regdate'));

        $avatarUrl = XOOPS_URL . '/uploads/' . $avatar;
        if (empty($avatar) || $avatar === 'avatars/blank.gif') {
            $avatarUrl = XOOPS_URL . '/uploads/avatars/blank.gif';
        }

        $html .= '<li style="margin-bottom:8px; display:flex; align-items:center; gap:8px;">';
        $html .= '<img src="' . $avatarUrl . '" alt="' . $uname . '" '
              . 'style="width:32px; height:32px; border-radius:50%;" />';
        $html .= '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $uid . '">' . $uname . '</a>';
        $html .= ' <small style="color:#888;">(' . $date . ')</small>';
        $html .= '</li>';
    }
    $html .= '</ul>';

    return $html;
}
