<?php
/**
 * XOOPS message detail
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
$xoopsPreload->triggerEvent('core.viewpmsg.start');

xoops_loadLanguage('pmsg');

if (!is_object($xoopsUser)) {
    $errormessage = _PM_SORRY . '<br>' . _PM_PLZREG . '';
    redirect_header('user.php', 2, $errormessage);
} else {
    $pm_handler = xoops_getHandler('privmessage');
    if (isset($_POST['delete_messages']) && (isset($_POST['msg_id']) || isset($_POST['msg_ids']))) {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            echo implode('<br>', $GLOBALS['xoopsSecurity']->getErrors());
            exit();
        } elseif (empty($_REQUEST['ok'])) {
            include $GLOBALS['xoops']->path('header.php');
            xoops_confirm(array(
                              'ok'              => 1,
                              'delete_messages' => 1,
                              'msg_ids'         => json_encode(array_map('intval', $_POST['msg_id']))), $_SERVER['REQUEST_URI'], _PM_SURE_TO_DELETE);
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }
        $clean_msg_id = json_decode($_POST['msg_ids'], true, 2);
        if (!empty($clean_msg_id)) {
            $clean_msg_id = array_map('intval', $clean_msg_id);
        }
        $size = count($clean_msg_id);
        $msg  =& $clean_msg_id;
        for ($i = 0; $i < $size; ++$i) {
            $pm = $pm_handler->get((int)$msg[$i]);
            if ($pm->getVar('to_userid') == $xoopsUser->getVar('uid')) {
                $pm_handler->delete($pm);
            }
            unset($pm);
        }
        redirect_header('viewpmsg.php', 1, _PM_DELETED);
    }
    include $GLOBALS['xoops']->path('header.php');
    $criteria = new Criteria('to_userid', $xoopsUser->getVar('uid'));
    $criteria->setOrder('DESC');
    $pm_arr = $pm_handler->getObjects($criteria);
    echo "<h4 class='txtcenter'>" . _PM_PRIVATEMESSAGE . "</h4><br><a href='userinfo.php?uid=" . $xoopsUser->getVar('uid') . "'>" . _PM_PROFILE . "</a>&nbsp;<span style='font-weight:bold;'>&raquo;</span>&nbsp;" . _PM_INBOX . '<br><br>';
    echo "<form name='prvmsg' method='post' action='viewpmsg.php'>";
    echo "<table cellspacing='1' cellpadding='4' class='outer width100 bnone'>\n";
    echo "<tr align='center' valign='middle'><th><input name='allbox' id='allbox' onclick='xoopsCheckAll(\"prvmsg\", \"allbox\");' type='checkbox' value='Check All' /></th><th><img class'bnone' src='images/download.gif' alt=''/></th><th>&nbsp;</th><th>" . _PM_FROM . '</th><th>' . _PM_SUBJECT . "</th><th class='txtcenter'>" . _PM_DATE . "</th></tr>\n";
    $total_messages = count($pm_arr);
    if ($total_messages == 0) {
        echo "<tr><td class='even txcenter' colspan='6'>" . _PM_YOUDONTHAVE . '</td></tr> ';
        $display = 0;
    } else {
        $display = 1;
    }
    for ($i = 0; $i < $total_messages; ++$i) {
        $class = ($i % 2 == 0) ? 'even' : 'odd';
        echo "<tr class='$class txtleft'><td class='aligntop width2 txtcenter'><input type='checkbox' id='msg_id[]' name='msg_id[]' value='" . $pm_arr[$i]->getVar('msg_id') . "' /></td>\n";
        if ($pm_arr[$i]->getVar('read_msg') == 1) {
            echo "<td class='aligntop width5 txtcenter'><img src='images/email_read.png' alt='" . _PM_READ . "' title='" . _PM_READ . "' /></td>\n";
        } else {
            echo "<td class='aligntop width5 txtcenter'><img src='images/email_notread.png' alt='" . _PM_NOTREAD . "' title='" . _PM_NOTREAD . "' /></td>\n";
        }
        $iconName = htmlspecialchars($pm_arr[$i]->getVar('msg_image', 'E'), ENT_QUOTES);
        if ($iconName != '') {
            echo "<td class='aligntop width5 txtcenter'><img src='images/subject/" . $iconName . "' alt='' /></td>\n";
        } else {
            echo "<td class='aligntop width5 txtcenter'></td>\n";
        }
        $postername = XoopsUser::getUnameFromId($pm_arr[$i]->getVar('from_userid'));
        echo "<td class='alignmiddle width10'>";
        // no need to show deleted users
        if ($postername) {
            echo "<a href='userinfo.php?uid=" . $pm_arr[$i]->getVar('from_userid') . "' title=''>" . $postername . '</a>';
        } else {
            echo $xoopsConfig['anonymous'];
        }
        echo "</td>\n";
        echo "<td class='alignmiddle'><a href='readpmsg.php?start=" . ($total_messages - $i - 1), "&amp;total_messages=$total_messages'>" . $pm_arr[$i]->getVar('subject') . '</a></td>';
        echo "<td class='alignmiddle txtcenter width20'>" . formatTimestamp($pm_arr[$i]->getVar('msg_time')) . '</td></tr>';
    }
    if ($display == 1) {
        echo "<tr class='foot txtleft'><td colspan='6' align='left'><input type='button' class='formButton' onclick='openWithSelfMain(\"" . XOOPS_URL . "/pmlite.php?send=1\",\"pmlite\",565,500);' value='" . _PM_SEND . "' />&nbsp;<input type='submit' class='formButton' name='delete_messages' value='" . _PM_DELETE . "' />" . $GLOBALS['xoopsSecurity']->getTokenHTML() . '</td></tr></table></form>';
    } else {
        echo "<tr class='bg2 txtleft'><td class='txtleft' colspan='6'><input type='button' class='formButton' onclick='openWithSelfMain(\"" . XOOPS_URL . "/pmlite.php?send=1\",\"pmlite\",565,500);' value='" . _PM_SEND . "' /></td></tr></table></form>";
    }
    include $GLOBALS['xoops']->path('footer.php');
}
