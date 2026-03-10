<?php
/**
 * Example Custom Block: Welcome Message
 *
 * Demonstrates a simple custom block that displays a personalized
 * welcome message for logged-in users or a generic greeting for guests.
 *
 * To use this block:
 *   1. Go to System Admin > Blocks > Add New Block
 *   2. Select content type "PHP Script (file-based)"
 *   3. Enter in the content field: example_welcome.php|b_custom_welcome_show
 *   4. Save the block
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Display a welcome message block
 *
 * @return string HTML content for the block
 */
function b_custom_welcome_show()
{
    global $xoopsUser, $xoopsConfig;

    $html = '<div class="custom-welcome-block">';
    if (is_object($xoopsUser)) {
        $uname = htmlspecialchars($xoopsUser->getVar('uname', 'n'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html .= '<p>Welcome back, <strong>' . $uname . '</strong>!</p>';
        $html .= '<p>You have been a member since '
              . date('F j, Y', (int) $xoopsUser->getVar('user_regdate')) . '.</p>';
    } else {
        $sitename = htmlspecialchars($xoopsConfig['sitename'] ?? 'XOOPS', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html .= '<p>Welcome to <strong>' . $sitename . '</strong>!</p>';
        $html .= '<p><a href="' . XOOPS_URL . '/register.php">Register</a> or '
              . '<a href="' . XOOPS_URL . '/user.php">Login</a> to get started.</p>';
    }
    $html .= '</div>';

    return $html;
}
