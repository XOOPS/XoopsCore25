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
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             pm
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    include_once dirname(__DIR__, 2) . '/mainfile.php';
} else {
    chdir(XOOPS_ROOT_PATH . '/modules/pm/');
    xoops_loadLanguage('main', 'pm');
}

XoopsLoad::load('XoopsRequest');

$subject_icons = XoopsLists::getSubjectsList();

/**
 * Resolve the PM module object once per request.
 *
 * @return XoopsModule|false
 */
function pmGetModule(): XoopsModule|false
{
    static $pmModule = null;
    if ($pmModule === null) {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname('pm');
        $pmModule      = ($module instanceof XoopsModule) ? $module : false;
    }

    return $pmModule;
}

/**
 * Groups allowed to use the PM module.
 *
 * Mirrors module_read checks by always allowing the admin group.
 *
 * @return array
 */
function pmGetAllowedRecipientGroups(): array
{
    static $groups = null;
    if ($groups === null) {
        $module = pmGetModule();
        if ($module instanceof XoopsModule) {
            /** @var XoopsGroupPermHandler $grouppermHandler */
            $grouppermHandler = xoops_getHandler('groupperm');
            $groups           = array_values(array_unique(array_map('intval', $grouppermHandler->getGroupIds('module_read', $module->getVar('mid')))));
            if (!in_array(XOOPS_GROUP_ADMIN, $groups, true)) {
                $groups[] = XOOPS_GROUP_ADMIN;
            }
        } else {
            // Fail closed: no users shown — matches pmCanMessageUser which rejects all
            $groups = [0];
        }
    }

    return $groups;
}

/**
 * Check whether a user can access the PM module and receive PMs.
 *
 * @param int $uid
 *
 * @return bool
 */
function pmCanMessageUser(int $uid): bool
{
    if ($uid <= 0) {
        return false;
    }
    $module = pmGetModule();
    if (!$module instanceof XoopsModule) {
        return false;
    }
    /** @var XoopsMemberHandler $memberHandler */
    $memberHandler = xoops_getHandler('member');
    $userGroups    = $memberHandler->getGroupsByUser($uid);
    if (empty($userGroups)) {
        return false;
    }
    /** @var XoopsGroupPermHandler $grouppermHandler */
    $grouppermHandler = xoops_getHandler('groupperm');

    return $grouppermHandler->checkRight('module_read', $module->getVar('mid'), $userGroups);
}

/**
 * Safe fallbacks for PM language constants that may not be loaded.
 */
function pmSafeTryAgain(): string
{
    return defined('_PM_PLZTRYAGAIN') ? _PM_PLZTRYAGAIN : 'Please try again.';
}

function pmSafeGoBack(): string
{
    return defined('_PM_GOBACK') ? _PM_GOBACK : 'Go back';
}

/**
 * Render the "user does not exist" error block.
 */
function pmRenderUserNotFound(): void
{
    echo '<br><br><div><h4>' . _PM_USERNOEXIST . '<br>';
    echo pmSafeTryAgain() . '</h4><br>';
    echo "[ <a href='javascript:history.go(-1)'>" . pmSafeGoBack() . '</a> ]</div>';
}

/**
 * Render the standard invalid-recipient message.
 */
function pmRenderInvalidRecipient(): void
{
    $noPermMsg = defined('_PM_USERNOPERM') ? _PM_USERNOPERM : 'The selected user cannot receive private messages.';

    echo '<br><br><div><h4>' . $noPermMsg . '<br>';
    echo pmSafeTryAgain() . '</h4><br>';
    echo "[ <a href='javascript:history.go(-1)'>" . pmSafeGoBack() . '</a> ]</div>';
}

$op = XoopsRequest::getCmd('op', '', 'POST');

$reply     = XoopsRequest::getBool('reply', 0, 'GET');
$send      = XoopsRequest::getBool('send', 0, 'GET');
$send2     = XoopsRequest::getBool('send2', 0, 'GET');
$sendmod   = XoopsRequest::getBool('sendmod', 0, 'POST'); // send from other modules with post data
$to_userid = XoopsRequest::getInt('to_userid', 0, 'GET');
$msg_id    = XoopsRequest::getInt('msg_id', 0, 'GET');

if (XoopsRequest::getString('refresh', '', 'GET') === '' && $op !== 'submit') {
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

$myts = \MyTextSanitizer::getInstance();
if ($op === 'submit') {
    $recipientId = \Xmf\Request::getInt('to_userid', 0, 'POST');
    /** @var XoopsMemberHandler $member_handler */
    $member_handler = xoops_getHandler('member');
    $count          = $member_handler->getUserCount(new Criteria('uid', $recipientId));
    if ($count != 1) {
        echo '<br><br><div><h4>' . _PM_USERNOEXIST . '<br>';
        echo _PM_PLZTRYAGAIN . '</h4><br>';
        echo "[ <a href='javascript:history.go(-1)'>" . _PM_GOBACK . '</a> ]</div>';
    } elseif (!pmCanMessageUser($recipientId)) {
        pmRenderInvalidRecipient();
    } elseif ($GLOBALS['xoopsSecurity']->check()) {
        $pm_handler = xoops_getModuleHandler('message', 'pm');
        $pm         = $pm_handler->create();
        $pm->setVar('msg_time', time());
        $msg_image = \Xmf\Request::getString('msg_image', '', 'POST');
        if (in_array($msg_image, $subject_icons)) {
            $pm->setVar('msg_image', $msg_image);
        }
        $pm->setVar('subject', \Xmf\Request::getString('subject', '', 'POST'));
        $pm->setVar('msg_text', \Xmf\Request::getString('message', '', 'POST'));
        $pm->setVar('to_userid', $recipientId);
        $pm->setVar('from_userid', $GLOBALS['xoopsUser']->getVar('uid'));
        if (\Xmf\Request::getBool('savecopy', false, 'POST')) {
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
    $subject = '';
    $message = '';
    if ($reply == 1) {
        $pm_handler = xoops_getModuleHandler('message', 'pm');
        $pm         = $pm_handler->get($msg_id);
        if ($pm->getVar('to_userid') == $GLOBALS['xoopsUser']->getVar('uid')) {
            $pm_uname = XoopsUser::getUnameFromId($pm->getVar('from_userid'));
            $message  = "[quote]\n";
            $message .= sprintf(_PM_USERWROTE, $pm_uname);
            $message .= "\n" . $pm->getVar('msg_text', 'E') . "\n[/quote]";
            if (!pmCanMessageUser($pm->getVar('from_userid'))) {
                pmRenderInvalidRecipient();
                xoops_footer();
                return;
            }
        } else {
            unset($pm);
            $reply = $send2 = 0;
        }
    }

    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    $pmform = new XoopsThemeForm('', 'pmform', 'pmlite.php', 'post', true);

    if ($reply == 1) {
        $subject = $pm->getVar('subject', 'E');
        if (!preg_match('/^' . _RE . '/i', $subject)) {
            $subject = _RE . ' ' . $subject;
        }
        $pmform->addElement(new XoopsFormLabel(_PM_TO, $pm_uname));
        $pmform->addElement(new XoopsFormHidden('to_userid', $pm->getVar('from_userid')));
    } elseif ($sendmod == 1) {
        $sendModRecipient = \Xmf\Request::getInt('to_userid', 0, 'POST');
        $tmpUname = XoopsUser::getUnameFromId($sendModRecipient);
        if (empty($tmpUname)) {
            pmRenderUserNotFound();
            xoops_footer();
            return;
        }
        if (!pmCanMessageUser($sendModRecipient)) {
            pmRenderInvalidRecipient();
            xoops_footer();
            return;
        }
        $pmform->addElement(new XoopsFormHidden('to_userid', $sendModRecipient));
        $pmform->addElement(new XoopsFormLabel(_PM_TO, $tmpUname));
        $subject = $myts->htmlSpecialChars(\Xmf\Request::getString('subject', '', 'POST'));
        $message = $myts->htmlSpecialChars(\Xmf\Request::getString('message', '', 'POST'));
    } else {
        if ($send2 == 1) {
            $tmpUname = XoopsUser::getUnameFromId($to_userid, false);
            if (empty($tmpUname)) {
                pmRenderUserNotFound();
                xoops_footer();
                return;
            }
            if (!pmCanMessageUser($to_userid)) {
                pmRenderInvalidRecipient();
                xoops_footer();
                return;
            }
            $pmform->addElement(new XoopsFormLabel(_PM_TO, $tmpUname));
            $pmform->addElement(new XoopsFormHidden('to_userid', $to_userid));
        } else {
            $to_username = new XoopsFormSelectUser(_PM_TO, 'to_userid', false, null, 1, false, pmGetAllowedRecipientGroups(), ['module_read' => 'pm']);
            $pmform->addElement($to_username);
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

    $pmform->assign($GLOBALS['xoopsHeadTpl']);
    $GLOBALS['xoopsHeadTpl']->assign('radio_icons', $subject_icons);
    $GLOBALS['xoopsHeadTpl']->display('db:pm_pmlite.tpl');
}
xoops_footer();
