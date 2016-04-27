<?php
/**
 * XOOPS message processing
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
$xoopsPreload->triggerEvent('core.pmlite.start');

xoops_loadLanguage('pmsg');
XoopsLoad::load('XoopsRequest');

include $GLOBALS['xoops']->path('class/xoopsformloader.php');
$icon          = '';
$icons_radio   = new XoopsFormRadio(_MESSAGEICON, 'icon', $icon);
$subject_icons = XoopsLists::getSubjectsList();

$op = XoopsRequest::getCmd('op', '', 'POST');

$reply     = XoopsRequest::getBool('reply', 0, 'GET');
$send      = XoopsRequest::getBool('send', 0, 'GET');
$send2     = XoopsRequest::getBool('send2', 0, 'GET');
$to_userid = XoopsRequest::getInt('to_userid', 0, 'GET');
$msg_id    = XoopsRequest::getInt('msg_id', 0, 'GET');
if (empty($_GET['refresh']) && $op !== 'submit') {
    $jump = 'pmlite.php?refresh=' . time() . '';
    if ($send == 1) {
        $jump .= '&amp;send=' . $send . '';
    } elseif ($send2 == 1) {
        $jump .= '&amp;send2=' . $send2 . '&amp;to_userid=' . $to_userid . '';
    } elseif ($reply == 1) {
        $jump .= '&amp;reply=' . $reply . '&amp;msg_id=' . $msg_id . '';
    } else {
    }
    echo "<html><head><meta http-equiv='Refresh' content='0; url=" . $jump . "' /></head><body></body></html>";
    exit();
}

xoops_header();

$method      = XoopsRequest::getMethod();
$safeMethods = array('GET', 'HEAD');
if (!in_array($method, $safeMethods)) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        echo '<br /><br /><div><h4>' . _ERRORS . '</h4><br />';
        echo "[ <a href='javascript:history.go(-1)' title=''>" . _PM_GOBACK . '</a> ]</div>';
        xoops_footer();
        exit;
    }
}

if (is_object($xoopsUser)) {
    $myts = MyTextSanitizer::getInstance();
    if ($op === 'submit') {
        $res = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('users') . ' WHERE uid=' . XoopsRequest::getInt('to_userid', 0, 'POST') . '');
        list($count) = $xoopsDB->fetchRow($res);
        if ($count != 1) {
            echo '<br /><br /><div><h4>' . _PM_USERNOEXIST . '<br />';
            echo _PM_PLZTRYAGAIN . '</h4><br />';
            echo "[ <a href='javascript:history.go(-1)' title=''>" . _PM_GOBACK . '</a> ]</div>';
        } else {
            $pm_handler = xoops_getHandler('privmessage');
            $pm         = $pm_handler->create();
            $msg_image  = XoopsRequest::getCmd('icon', null, 'POST');
            if (in_array($msg_image, $subject_icons)) {
                $pm->setVar('msg_image', $msg_image);
            }
            $pm->setVar('subject', XoopsRequest::getString('subject', null, 'POST'));
            $pm->setVar('msg_text', XoopsRequest::getString('message', null, 'POST'));
            $pm->setVar('to_userid', XoopsRequest::getInt('to_userid', 0, 'POST'));
            $pm->setVar('from_userid', $xoopsUser->getVar('uid'));
            if (!$pm_handler->insert($pm)) {
                echo $pm->getHtmlErrors();
                echo "<br /><a href='javascript:history.go(-1)' title=''>" . _PM_GOBACK . '</a>';
            } else {
                echo "<br /><br /><div style='text-align:center;'><h4>" . _PM_MESSAGEPOSTED . "</h4><br /><a href=\"javascript:window.opener.location='" . XOOPS_URL . "/viewpmsg.php';window.close();\" title=\"\">" . _PM_CLICKHERE . "</a><br /><br /><a href=\"javascript:window.close();\" title=\"\">" . _PM_ORCLOSEWINDOW . '</a></div>';
            }
        }
    } elseif ($reply == 1 || $send == 1 || $send2 == 1) {
        include_once $GLOBALS['xoops']->path('include/xoopscodes.php');
        if ($reply == 1) {
            $pm_handler = xoops_getHandler('privmessage');
            $pm         = $pm_handler->get($msg_id);
            if ($pm->getVar('to_userid') == $xoopsUser->getVar('uid')) {
                $pm_uname = XoopsUser::getUnameFromId($pm->getVar('from_userid'));
                $message  = "[quote]\n";
                $message .= sprintf(_PM_USERWROTE, $pm_uname);
                $message .= "\n" . $pm->getVar('msg_text', 'E') . "\n[/quote]";
            } else {
                unset($pm);
                $reply = $send2 = 0;
            }
        }
        echo "<form action='pmlite.php' method='post' name='coolsus'>\n";
        echo "<table style=' text-align:left;' class='outer'><tr><td class='head txtright' style='width:25%;'>" . _PM_TO . '</td>';
        if ($reply == 1) {
            echo "<td class='even'><input type='hidden' name='to_userid' value='" . $pm->getVar('from_userid') . "' />" . $pm_uname . '</td>';
        } elseif ($send2 == 1) {
            $to_username = XoopsUser::getUnameFromId($to_userid);
            echo "<td class='even'><input type='hidden' name='to_userid' value='" . $to_userid . "' />" . $to_username . '</td>';
        } else {
            //require_once $GLOBALS['xoops']->path('class/xoopsform/formelement.php');
            //require_once $GLOBALS['xoops']->path('class/xoopsform/formselect.php');
            //require_once $GLOBALS['xoops']->path('class/xoopsform/formlabel.php');
            //require_once $GLOBALS['xoops']->path('class/xoopsform/formselectuser.php');
            $user_sel = new XoopsFormSelectUser('', 'to_userid');
            echo "<td class='even'>" . $user_sel->render();
            echo '</td>';
        }
        echo '</tr>';
        echo "<tr><td class='head txtright' style='width:25%;'>" . _PM_SUBJECTC . '</td>';
        if ($reply == 1) {
            $subject = $pm->getVar('subject', 'E');
            //TODO Fix harcoded string
            if (!preg_match('/^' . _RE . '/i', $subject)) {
                $subject = _RE . ' ' . $subject;
            }
            echo "<td class='even'><input type='text' name='subject' value='" . $subject . "' size='30' maxlength='100' /></td>";
        } else {
            echo "<td class='even'><input type='text' name='subject' size='30' maxlength='100' /></td>";
        }
        echo '</tr>';

        echo '<tr>';
        echo "<td class='head txtright' style='width:25%;'>" . _MESSAGEICON . '</td>';
        foreach ($subject_icons as $iconfile) {
            $icons_radio->addOption($iconfile, '<img src="' . XOOPS_URL . '/images/subject/' . $iconfile . '" alt="" />');
        }
        echo "<td class='even'>" . $icons_radio->render();
        echo '</td>';
        echo '</tr>';

        echo "<tr style='vertical-align:top;'><td style='width:25%;' class='head txtright'>" . _PM_MESSAGEC . '</td>';
        echo "<td class='even'>";
        xoopsCodeTarea('message', 37, 8);
        xoopsSmilies('message');

        echo '</td>';
        echo '</tr>';
        echo "<tr><td class='head'>&nbsp;</td><td class='even'>
        <input type='hidden' name='op' value='submit' />" . $GLOBALS['xoopsSecurity']->getTokenHTML() . "
        <input type='submit' class='formButton' name='submit' value='" . _PM_SUBMIT . "' />&nbsp;
        <input type='reset' class='formButton' value='" . _PM_CLEAR . "' />
        &nbsp;<input type='button' class='formButton' name='cancel' value='" . _PM_CANCELSEND . "' onclick='window.close();' />
        </td></tr></table>\n";
        echo "</form>\n";
    }
} else {
    echo _PM_SORRY . "<br /><br /><a href='" . XOOPS_URL . "/register.php' title=''>" . _PM_REGISTERNOW . '</a>.';
}

xoops_footer();
