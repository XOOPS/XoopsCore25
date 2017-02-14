<?php
/**
 * Private message module
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
 * @package             pm
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    include_once dirname(dirname(__DIR__)) . '/mainfile.php';
} else {
    chdir(XOOPS_ROOT_PATH . '/modules/pm/');
    xoops_loadLanguage('main', 'pm');
}

XoopsLoad::load('XoopsRequest');

$subject_icons = XoopsLists::getSubjectsList();

$op = XoopsRequest::getCmd('op', '', 'POST');

$reply     = XoopsRequest::getBool('reply', 0, 'GET');
$send      = XoopsRequest::getBool('send', 0, 'GET');
$send2     = XoopsRequest::getBool('send2', 0, 'GET');
$sendmod   = XoopsRequest::getBool('sendmod', 0, 'POST'); // send from other modules with post data
$to_userid = XoopsRequest::getInt('to_userid', 0, 'GET');
$msg_id    = XoopsRequest::getInt('msg_id', 0, 'GET');

if (empty($_GET['refresh']) && $op !== 'submit') {
    $jump = 'pmlite.php?refresh=' . time();
    if ($send == 1) {
        $jump .= "&send={$send}";
    } elseif ($send2 == 1) {
        $jump .= "&send2={$send2}&to_userid={$to_userid}";
    } elseif ($reply == 1) {
        $jump .= "&reply={$reply}&msg_id={$msg_id}";
    } else {
    }
    header('location: ' . $jump);
    exit();
}

if (!is_object($GLOBALS['xoopsUser'])) {
    redirect_header(XOOPS_URL, 3, _NOPERM);
}
xoops_header();

$myts = MyTextSanitizer::getInstance();
if ($op === 'submit') {
    /* @var $member_handler XoopsMemberHandler */
    $member_handler = xoops_getHandler('member');
    $count          = $member_handler->getUserCount(new Criteria('uid', XoopsRequest::getInt('to_userid', 0, 'POST')));
    if ($count != 1) {
        echo '<br><br><div><h4>' . _PM_USERNOEXIST . '<br>';
        echo _PM_PLZTRYAGAIN . '</h4><br>';
        echo "[ <a href='javascript:history.go(-1)'>" . _PM_GOBACK . '</a> ]</div>';
    } elseif ($GLOBALS['xoopsSecurity']->check()) {
        $pm_handler = xoops_getModuleHandler('message', 'pm');
        $pm         = $pm_handler->create();
        $pm->setVar('msg_time', time());
        $msg_image = XoopsRequest::getString('msg_image', null, 'POST');
        if (in_array($msg_image, $subject_icons)) {
            $pm->setVar('msg_image', $msg_image);
        }
        $pm->setVar('subject', XoopsRequest::getString('subject', null, 'POST'));
        $pm->setVar('msg_text', XoopsRequest::getString('message', null, 'POST'));
        $pm->setVar('to_userid', XoopsRequest::getInt('to_userid', 0, 'POST'));
        $pm->setVar('from_userid', $GLOBALS['xoopsUser']->getVar('uid'));
        if (XoopsRequest::getBool('savecopy', 0)) {
            //PMs are by default not saved in outbox
            $pm->setVar('from_delete', 0);
        }
        if (!$pm_handler->insert($pm)) {
            echo $pm->getHtmlErrors();
            echo "<br><a href='javascript:history.go(-1)'>" . _PM_GOBACK . '</a>';
        } else {
            // @todo: Send notification email if user has selected this in the profile

            echo "<br><br><div style='text-align:center;'><h4>" . _PM_MESSAGEPOSTED . "</h4><br><a href=\"javascript:window.opener.location='" . XOOPS_URL . "/viewpmsg.php';window.close();\">" . _PM_CLICKHERE . "</a><br><br><a href=\"javascript:window.close();\">" . _PM_ORCLOSEWINDOW . '</a></div>';
        }
    } else {
        echo implode('<br>', $GLOBALS['xoopsSecurity']->getErrors());
        echo "<br><a href=\"javascript:window.close();\">" . _PM_ORCLOSEWINDOW . '</a>';
    }
} elseif ($reply == 1 || $send == 1 || $send2 == 1 || $sendmod == 1) {
    if ($reply == 1) {
        $pm_handler = xoops_getModuleHandler('message', 'pm');
        $pm         = $pm_handler->get($msg_id);
        if ($pm->getVar('to_userid') == $GLOBALS['xoopsUser']->getVar('uid')) {
            $pm_uname = XoopsUser::getUnameFromId($pm->getVar('from_userid'));
            $message  = "[quote]\n";
            $message .= sprintf(_PM_USERWROTE, $pm_uname);
            $message .= "\n" . $pm->getVar('msg_text', 'E') . "\n[/quote]";
        } else {
            unset($pm);
            $reply = $send2 = 0;
        }
    }

    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    $pmform = new XoopsThemeForm('', 'pmform', 'pmlite.php', 'post', true);

    $subject = '';
    $message = '';
    if ($reply == 1) {
        $subject = $pm->getVar('subject', 'E');
        if (!preg_match('/^' . _RE . '/i', $subject)) {
            $subject = _RE . ' ' . $subject;
        }
        $pmform->addElement(new XoopsFormLabel(_PM_TO, $pm_uname));
        $pmform->addElement(new XoopsFormHidden('to_userid', $pm->getVar('from_userid')));
    } elseif ($sendmod == 1) {
        $tmpUname = XoopsUser::getUnameFromId(XoopsRequest::getInt('to_userid', 0, 'POST'));
        $pmform->addElement(new XoopsFormHidden('to_userid', XoopsRequest::getInt('to_userid', 0, 'POST')));
        $pmform->addElement(new XoopsFormLabel(_PM_TO, $tmpUname));
        $subject = $myts->htmlSpecialChars(XoopsRequest::getString('subject', '', 'POST'));
        $message = $myts->htmlSpecialChars(XoopsRequest::getString('message', '', 'POST'));
    } else {
        if ($send2 == 1) {
            $tmpUname = XoopsUser::getUnameFromId($to_userid, false);
            $pmform->addElement(new XoopsFormLabel(_PM_TO, $tmpUname));
            $pmform->addElement(new XoopsFormHidden('to_userid', $to_userid));
        } else {
            $to_username = new XoopsFormSelectUser(_PM_TO, 'to_userid');
            $pmform->addElement($to_username);
        }
    }
    $pmform->addElement(new XoopsFormText(_PM_SUBJECTC, 'subject', 30, 100, $subject), true);

    $msg_image   = '';
    $icons_radio = new XoopsFormRadio(_MESSAGEICON, 'msg_image', $msg_image);
    $subjectImages = array();
    foreach ($subject_icons as $name => $value) {
        $subjectImages[$name] = '<img src="' . XOOPS_URL . '/images/subject/' . $value .'">';
    }
    $icons_radio->addOptionArray($subjectImages);
    $pmform->addElement($icons_radio);

    $pmform->addElement(new XoopsFormDhtmlTextArea(_PM_MESSAGEC, 'message', $message, 8, 37), true);

    $saveCheckbox = new XoopsFormCheckBox('', 'savecopy', 0);
    $saveCheckbox->addOption('1', _PM_SAVEINOUTBOX);
    $pmform->addElement($saveCheckbox);

    $pmform->addElement(new XoopsFormHidden('op', 'submit'));
    $elementTray = new XoopsFormElementTray('', '', 'tray');
    $elementTray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $elementTray->addElement(new XoopsFormButton('', 'reset', _PM_CLEAR, 'reset'));

    $cancel_send = new XoopsFormButton('', 'cancel', _PM_CANCELSEND, 'button');
    $cancel_send->setExtra("onclick='javascript:window.close();'");
    $elementTray->addElement($cancel_send);
    $pmform->addElement($elementTray);

    $pmform->display();
}
xoops_footer();
