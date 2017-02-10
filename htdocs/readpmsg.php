<?php
/**
 * XOOPS message list
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
 * @package             core
 * @since               2.0.0
 */

include __DIR__ . '/mainfile.php';
$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.readpmsg.start');

xoops_loadLanguage('pmsg');

if (!is_object($xoopsUser)) {
    redirect_header('user.php', 0);
} else {
    $pm_handler = xoops_getHandler('privmessage');
    if (!empty($_POST['delete'])) {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            echo implode('<br>', $GLOBALS['xoopsSecurity']->getErrors());
            exit();
        } elseif (empty($_REQUEST['ok'])) {
            include $GLOBALS['xoops']->path('header.php');
            xoops_confirm(array('ok' => 1, 'delete' => 1, 'msg_id' => (int)$_POST['msg_id']), $_SERVER['REQUEST_URI'], _PM_SURE_TO_DELETE);
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }
        $pm = $pm_handler->get((int)$_POST['msg_id']);
        if (!is_object($pm) || $pm->getVar('to_userid') != $xoopsUser->getVar('uid') || !$pm_handler->delete($pm)) {
            exit();
        } else {
            redirect_header('viewpmsg.php', 1, _PM_DELETED);
        }
    }
    $start          = !empty($_GET['start']) ? (int)$_GET['start'] : 0;
    $total_messages = !empty($_GET['total_messages']) ? (int)$_GET['total_messages'] : 0;
    include $GLOBALS['xoops']->path('header.php');
    $criteria = new Criteria('to_userid', $xoopsUser->getVar('uid'));
    $criteria->setLimit(1);
    $criteria->setStart($start);
    $criteria->setSort('msg_time');
    $pm_arr = $pm_handler->getObjects($criteria);
    echo '<div><h4>' . _PM_PRIVATEMESSAGE . "</h4></div><br><a href='userinfo.php?uid=" . $xoopsUser->getVar('uid') . "' title=''>" . _PM_PROFILE . "</a>&nbsp;<span class='bold'>&raquo;</span>&nbsp;<a href='viewpmsg.php' title=''>" . _PM_INBOX . "</a>&nbsp;<span class='bold'>&raquo;</span>&nbsp;\n";
    if (empty($pm_arr)) {
        echo '<br><br>' . _PM_YOUDONTHAVE;
    } else {
        if (!$pm_handler->setRead($pm_arr[0])) {
            //echo "failed";
        }
        echo $pm_arr[0]->getVar('subject') . "<br><form action='readpmsg.php' method='post' name='delete" . $pm_arr[0]->getVar('msg_id') . "'><table cellpadding='4' cellspacing='1' class='outer width100 bnone'><tr><th colspan='2'>" . _PM_FROM . "</th></tr><tr class='even'>\n";
        $poster = new XoopsUser($pm_arr[0]->getVar('from_userid'));
        if (!$poster->isActive()) {
            $poster = false;
        }
        echo "<td valign='top'>";
        if ($poster != false) { // we need to do this for deleted users
            echo "<a href='userinfo.php?uid=" . $poster->getVar('uid') . "' title=''>" . $poster->getVar('uname') . "</a><br>\n";
            if ($poster->getVar('user_avatar') != '') {
                echo "<img src='uploads/" . $poster->getVar('user_avatar') . "' alt='' /><br>\n";
            }
            if ($poster->getVar('user_from') != '') {
                echo _PM_FROMC . '' . $poster->getVar('user_from') . "<br><br>\n";
            }
            if ($poster->isOnline()) {
                echo "<span class='red bold'>" . _PM_ONLINE . "</span><br><br>\n";
            }
        } else {
            echo $xoopsConfig['anonymous']; // we need to do this for deleted users
        }
        $iconName = htmlspecialchars($pm_arr[0]->getVar('msg_image', 'E'), ENT_QUOTES);
        if ($iconName != '') {
            echo "</td><td><img src='images/subject/" . $iconName . "' alt='' />&nbsp;" . _PM_SENTC . '' . formatTimestamp($pm_arr[0]->getVar('msg_time'));
        } else {
            echo '</td><td>' . _PM_SENTC . '' . formatTimestamp($pm_arr[0]->getVar('msg_time'));
        }

        echo '<hr /><br><strong>' . $pm_arr[0]->getVar('subject') . "</strong><br><br>\n";
        echo $pm_arr[0]->getVar('msg_text') . "<br><br></td></tr><tr class='foot'><td class='width20 txtleft' colspan='2'>";
        // we don't want to reply to a deleted user!
        if ($poster != false) {
            echo "<button type='button' class='btn btn-default' onclick='openWithSelfMain(\"" . XOOPS_URL . '/pmlite.php?reply=1&amp;msg_id=' . $pm_arr[0]->getVar('msg_id') . "\",\"pmlite\",565,500);' title='" . _PM_REPLY . "'><span class='fa fa-fw fa-reply'></span></button>\n";
        }
        echo "<input type='hidden' name='delete' value='1' />";
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "<input type='hidden' name='msg_id' value='" . $pm_arr[0]->getVar('msg_id') . "' />";
        echo "<button type='button' class='btn btn-default' onclick='document.delete" . $pm_arr[0]->getVar('msg_id') . ".submit();' title='" . _PM_DELETE . "'><span class='fa fa-fw fa-remove'></span></button>";
        echo "</td></tr><tr><td class='txtright' colspan='2'>";
        $previous = $start - 1;
        $next     = $start + 1;
        if ($previous >= 0) {
            echo "<a href='readpmsg.php?start=" . $previous . '&amp;total_messages=' . $total_messages . "' title=''>" . _PM_PREVIOUS . '</a> | ';
        } else {
            echo _PM_PREVIOUS . ' | ';
        }
        if ($next < $total_messages) {
            echo "<a href='readpmsg.php?start=" . $next . '&amp;total_messages=' . $total_messages . "' title=''>" . _PM_NEXT . '</a>';
        } else {
            echo _PM_NEXT;
        }
        echo "</td></tr></table></form>\n";
    }
    include $GLOBALS['xoops']->path('footer.php');
}
