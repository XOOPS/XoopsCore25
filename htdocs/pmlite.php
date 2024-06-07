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
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             core
 * @since               2.0.0
 */

include __DIR__ . '/mainfile.php';
$xoopsPreload = XoopsPreload::getInstance();
$xoopsPreload->triggerEvent('core.pmlite.start');

xoops_loadLanguage('pmsg');
XoopsLoad::load('XoopsRequest');

$subject_icons = XoopsLists::getSubjectsList();

$op = XoopsRequest::getCmd('op', '', 'POST');

$reply     = XoopsRequest::getBool('reply', 0, 'GET');
$send      = XoopsRequest::getBool('send', 0, 'GET');
$send2     = XoopsRequest::getBool('send2', 0, 'GET');
$to_userid = XoopsRequest::getInt('to_userid', 0, 'GET');
$msg_id    = XoopsRequest::getInt('msg_id', 0, 'GET');
if (empty($_GET['refresh']) && 'submit' !== $op) {
    $jump = 'pmlite.php?refresh=' . time() . '';
    if (1 == $send) {
        $jump .= '&amp;send=' . $send . '';
    } elseif (1 == $send2) {
        $jump .= '&amp;send2=' . $send2 . '&amp;to_userid=' . $to_userid . '';
    } elseif (1 == $reply) {
        $jump .= '&amp;reply=' . $reply . '&amp;msg_id=' . $msg_id . '';
    } else {
    }
    echo "<html><head><meta http-equiv='Refresh' content='0; url=" . $jump . "' /></head><body></body></html>";
    exit();
}

xoops_header();

$method      = XoopsRequest::getMethod();
$safeMethods = ['GET', 'HEAD'];
if (!in_array($method, $safeMethods)) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        echo '<br><br><div><h4>' . _ERRORS . '</h4><br>';
        echo "[ <a href='javascript:history.go(-1)' title=''>" . _PM_GOBACK . '</a> ]</div>';
        xoops_footer();
        exit;
    }
}

if (is_object($xoopsUser)) {
    $myts = \MyTextSanitizer::getInstance();
    if ('submit' === $op) {
        $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('users') . ' WHERE uid=' . XoopsRequest::getInt('to_userid', 0, 'POST') . '';
        $result = $xoopsDB->query($sql);
        if (!$xoopsDB->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $xoopsDB->error(),
                E_USER_ERROR,
            );
        }
        [$count] = $xoopsDB->fetchRow($result);
        if (1 != $count) {
            echo '<br><br><div><h4>' . _PM_USERNOEXIST . '<br>';
            echo _PM_PLZTRYAGAIN . '</h4><br>';
            echo "[ <a href='javascript:history.go(-1)' title=''>" . _PM_GOBACK . '</a> ]</div>';
        } else {
            $pm_handler = xoops_getHandler('privmessage');
            $pm         = $pm_handler->create();
            $msg_image  = XoopsRequest::getString('msg_image', null, 'POST');
            if (in_array($msg_image, $subject_icons)) {
                $pm->setVar('msg_image', $msg_image);
            }
            $pm->setVar('subject', XoopsRequest::getString('subject', null, 'POST'));
            $pm->setVar('msg_text', XoopsRequest::getString('message', null, 'POST'));
            $pm->setVar('to_userid', XoopsRequest::getInt('to_userid', 0, 'POST'));
            $pm->setVar('from_userid', $xoopsUser->getVar('uid'));
            if (!$pm_handler->insert($pm)) {
                echo $pm->getHtmlErrors();
                echo "<br><a href='javascript:history.go(-1)' title=''>" . _PM_GOBACK . '</a>';
            } else {
                echo "<br><br><div style='text-align:center;'><h4>" . _PM_MESSAGEPOSTED . "</h4><br><a href=\"javascript:window.opener.location='" . XOOPS_URL . "/viewpmsg.php';window.close();\" title=\"\">" . _PM_CLICKHERE . '</a><br><br><a href="javascript:window.close();" title="">' . _PM_ORCLOSEWINDOW . '</a></div>';
            }
        }
    } elseif (1 == $reply || 1 == $send || 1 == $send2) {
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

        $subject = '';
        $message = '';

        if (1 == $reply) {
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
        $pmform = new XoopsThemeForm('', 'coolsus', 'pmlite.php', 'post', true);
        if (1 == $reply) {
            $pmform->addElement(new XoopsFormLabel(_PM_TO, $pm_uname));
            $pmform->addElement(new XoopsFormHidden('to_userid', $pm->getVar('from_userid')));
        } elseif (1 == $send2) {
            $to_username = XoopsUser::getUnameFromId($to_userid);
            $pmform->addElement(new XoopsFormHidden('to_userid', $to_userid));
            $pmform->addElement(new XoopsFormLabel(_PM_TO, $to_username));
        } else {
            $pmform->addElement(new XoopsFormSelectUser(_PM_TO, 'to_userid', $to_userid));
        }

        if (1 == $reply) {
            $subject = $pm->getVar('subject', 'E');
            //TODO Fix harcoded string
            if (!preg_match('/^' . _RE . '/i', $subject)) {
                $subject = _RE . ' ' . $subject;
            }
        }
        $pmform->addElement(new XoopsFormText(_PM_SUBJECTC, 'subject', 30, 100, $subject), true);

        $msg_image   = '';
        $icons_radio = new XoopsFormRadio(_MESSAGEICON, 'msg_image', $msg_image);
        $subjectImages = [];
        foreach ($subject_icons as $name => $value) {
            $subjectImages[$name] = '<img src="' . XOOPS_URL . '/images/subject/' . $value . '">';
        }
        $icons_radio->addOptionArray($subjectImages);
        $pmform->addElement($icons_radio);

        $pmform->addElement(new XoopsFormDhtmlTextArea(_PM_MESSAGEC, 'message', $message, 8, 37), true);

        $pmform->addElement(new XoopsFormHidden('op', 'submit'));
        $elementTray = new XoopsFormElementTray('', '', 'tray');
        $elementTray->addElement(new XoopsFormButton('', 'submit', _PM_SUBMIT, 'submit'));
        $elementTray->addElement(new XoopsFormButton('', 'reset', _PM_CLEAR, 'reset'));

        $cancel_send = new XoopsFormButton('', 'cancel', _PM_CANCELSEND, 'button');
        $cancel_send->setExtra("onclick='javascript:window.close();'");
        $elementTray->addElement($cancel_send);
        $pmform->addElement($elementTray);

        $pmform->display();

    }
} else {
    echo _PM_SORRY . "<br><br><a href='" . XOOPS_URL . "/register.php' title=''>" . _PM_REGISTERNOW . '</a>.';
}

xoops_footer();
