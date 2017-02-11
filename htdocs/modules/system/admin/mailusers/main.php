<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
//  Check is active
if (!xoops_getModuleOption('active_mailusers', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}

// Parameters
$limit = 100;
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'list', 'string');
// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_mailusers.tpl';
// Call Header
xoops_cp_header();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('modules/system/js/admin.js');

switch ($op) {

    case 'list':
    default:
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MAILUSERS_MANAGER, system_adminVersion('mailusers', 'adminpath'));
        $xoBreadCrumb->addHelp(system_adminVersion('mailusers', 'help'));
        $xoBreadCrumb->render();

        $display_criteria = 1;
        $form             = new XoopsThemeForm(_AM_SYSTEM_MAILUSERS_LIST, 'mailusers', 'admin.php?fct=mailusers', 'post', true);
        if (!empty($_POST['memberslist_id'])) {
            $user_count    = count($_POST['memberslist_id']);
            $display_names = '';
            for ($i = 0; $i < $user_count; ++$i) {
                $uid_hidden = new XoopsFormHidden('mail_to_user[]', $_POST['memberslist_id'][$i]);
                $form->addElement($uid_hidden);
                $display_names .= "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $_POST['memberslist_id'][$i] . "' rel='external'>" . XoopsUser::getUnameFromId($_POST['memberslist_id'][$i]) . '</a>, ';
                unset($uid_hidden);
            }
            $users_label = new XoopsFormLabel(_AM_SYSTEM_MAILUSERS_SENDTOUSERS2, substr($display_names, 0, -2));
            $form->addElement($users_label);
            $display_criteria = 0;
        }
        if (!empty($display_criteria)) {
            $selected_groups = array();
            $group_select    = new XoopsFormSelectGroup('<div class="bold spacer">' . _AM_SYSTEM_MAILUSERS_GROUPIS . '<span class="bold green">*</span></div>', 'mail_to_group', false, $selected_groups, 5, true);

            $lastlog_min = new XoopsFormTextDateSelect(_AM_SYSTEM_MAILUSERS_LASTLOGMIN . '<span class="bold green">*</span>', 'mail_lastlog_min');
            $lastlog_min->setValue('');
            $lastlog_max = new XoopsFormTextDateSelect(_AM_SYSTEM_MAILUSERS_LASTLOGMAX . '<span class="bold green">*</span>', 'mail_lastlog_max');
            $lastlog_max->setValue('');

            $date = new XoopsFormElementTray('<div class="bold spacer">' . _AM_SYSTEM_MAILUSERS_DATE . '</div>', '');
            $date->addElement($lastlog_min);
            $date->addElement($lastlog_max);

            $idle_more = new XoopsFormText(_AM_SYSTEM_MAILUSERS_IDLEMORE . '<span class="bold green">*</span>', 'mail_idle_more', 10, 5);
            $idle_less = new XoopsFormText(_AM_SYSTEM_MAILUSERS_IDLELESS . '<span class="bold green">*</span>', 'mail_idle_less', 10, 5);

            $idle = new XoopsFormElementTray('<div class="bold spacer">' . _AM_SYSTEM_MAILUSERS_DAY . '</div>', '');
            $idle->addElement($idle_more);
            $idle->addElement($idle_less);

            $regd_min = new XoopsFormTextDateSelect(_AM_SYSTEM_MAILUSERS_REGDMIN . '<span class="bold green">*</span>', 'mail_regd_min');
            $regd_min->setValue('');
            $regd_max = new XoopsFormTextDateSelect(_AM_SYSTEM_MAILUSERS_REGDMAX . '<span class="bold green">*</span>', 'mail_regd_max');
            $regd_max->setValue('');

            $regdate = new XoopsFormElementTray('<div class="bold spacer">' . _AM_SYSTEM_MAILUSERS_REGDATE . '</div>', '');
            $regdate->addElement($regd_min);
            $regdate->addElement($regd_max);

            $mailok_cbox = new XoopsFormCheckBox('', 'mail_mailok');
            $mailok_cbox->addOption(1, _AM_SYSTEM_MAILUSERS_MAILOK . '<span class="bold green">*</span>');
            $inactive_cbox = new XoopsFormCheckBox('', 'mail_inactive');
            $inactive_cbox->addOption(1, _AM_SYSTEM_MAILUSERS_INACTIVE . '<span class="bold green">*</span>');
            $inactive_cbox->setExtra("onclick='javascript:disableElement(\"mail_lastlog_min\");disableElement(\"mail_lastlog_max\");disableElement(\"mail_idle_more\");disableElement(\"mail_idle_less\");disableElement(\"mail_to_group[]\");'");
            $criteria_tray = new XoopsFormElementTray(_AM_SYSTEM_MAILUSERS_SENDTOUSERS, '<br><br>');
            $criteria_tray->setDescription('<span class="bold green">*</span>' . _AM_SYSTEM_MAILUSERS_OPTIONAL);
            $criteria_tray->addElement($group_select);
            //$criteria_tray->addElement($lastlog);
            $criteria_tray->addElement($date);
            //$criteria_tray->addElement($lastlog_max);
            $criteria_tray->addElement($idle);
            //$criteria_tray->addElement($idle_less);
            $criteria_tray->addElement($regdate);

            $criteria_tray->addElement($mailok_cbox);
            $criteria_tray->addElement($inactive_cbox);

            //$criteria_tray->addElement($regd_max);
            $form->addElement($criteria_tray);
        }
        $fname_text      = new XoopsFormText(_AM_SYSTEM_MAILUSERS_MAILFNAME, 'mail_fromname', 30, 255, htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
        $fromemail       = !empty($xoopsConfig['adminmail']) ? $xoopsConfig['adminmail'] : $xoopsUser->getVar('email', 'E');
        $femail_text     = new XoopsFormText(_AM_SYSTEM_MAILUSERS_MAILFMAIL, 'mail_fromemail', 30, 255, $fromemail);
        $subject_caption = _AM_SYSTEM_MAILUSERS_MAILSUBJECT . "<br><br><span style='font-size:x-small;font-weight:bold;'>" . _AM_SYSTEM_MAILUSERS_MAILTAGS . "</span><br><span style='font-size:x-small;font-weight:normal;'>" . _AM_SYSTEM_MAILUSERS_MAILTAGS2 . '</span>';
        $subject_text    = new XoopsFormText($subject_caption, 'mail_subject', 50, 255);
        $body_caption    = _AM_SYSTEM_MAILUSERS_MAILBODY . "<br><br><span style='font-size:x-small;font-weight:bold;'>" . _AM_SYSTEM_MAILUSERS_MAILTAGS . "</span><br><span style='font-size:x-small;font-weight:normal;'>" . _AM_SYSTEM_MAILUSERS_MAILTAGS1 . '<br>' . _AM_SYSTEM_MAILUSERS_MAILTAGS2 . '<br>' . _AM_SYSTEM_MAILUSERS_MAILTAGS3 . '<br>' . _AM_SYSTEM_MAILUSERS_MAILTAGS4 . '</span>';
        $body_text       = new XoopsFormTextArea($body_caption, 'mail_body', '', 10);
        $to_checkbox     = new XoopsFormCheckBox(_AM_SYSTEM_MAILUSERS_SENDTO, 'mail_send_to', 'mail');
        $to_checkbox->addOption('mail', _AM_SYSTEM_MAILUSERS_EMAIL);
        $to_checkbox->addOption('pm', _AM_SYSTEM_MAILUSERS_PM);
        $start_hidden  = new XoopsFormHidden('mail_start', 0);
        $op_hidden     = new XoopsFormHidden('op', 'send');
        $submit_button = new XoopsFormButton('', 'mail_submit', _SEND, 'submit');

        $form->addElement($fname_text);
        $form->addElement($femail_text);
        $form->addElement($subject_text);
        $form->addElement($body_text);
        $form->addElement($to_checkbox);
        $form->addElement($op_hidden);
        $form->addElement($start_hidden);
        $form->addElement($submit_button);
        $form->setRequired($subject_text);
        $form->setRequired($body_text);
        // Assign form
        $xoopsTpl->assign('form', $form->render());
        break;

    // Send
    case 'send':
        // Define Breadcrumb and tips

        $xoBreadCrumb->addLink(_AM_SYSTEM_MAILUSERS_MANAGER, system_adminVersion('mailusers', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MAILUSERS_LIST);
        $xoBreadCrumb->render();

        if (!empty($_POST['mail_send_to'])) {
            $added          = array();
            $added_id       = array();
            $criteria       = array();
            $count_criteria = 0; // user count via criteria;
            if (!empty($_POST['mail_inactive'])) {
                $criteria[] = 'level = 0';
            } else {
                if (!empty($_POST['mail_mailok'])) {
                    $criteria[] = 'user_mailok = 1';
                }
                if (!empty($_POST['mail_lastlog_min'])) {
                    $time = strtotime(trim($_POST['mail_lastlog_min']));
                    if ($time > 0) {
                        $criteria[] = "last_login > $time";
                    }
                }
                if (!empty($_POST['mail_lastlog_max'])) {
                    $time = strtotime(trim($_POST['mail_lastlog_max']));
                    if ($time > 0) {
                        $criteria[] = "last_login < $time";
                    }
                }
                if (!empty($_POST['mail_idle_more']) && is_numeric($_POST['mail_idle_more'])) {
                    $f_mail_idle_more = (int)trim($_POST['mail_idle_more']);
                    $time             = 60 * 60 * 24 * $f_mail_idle_more;
                    $time             = time() - $time;
                    if ($time > 0) {
                        $criteria[] = "last_login < $time";
                    }
                }
                if (!empty($_POST['mail_idle_less']) && is_numeric($_POST['mail_idle_less'])) {
                    $f_mail_idle_less = (int)trim($_POST['mail_idle_less']);
                    $time             = 60 * 60 * 24 * $f_mail_idle_less;
                    $time             = time() - $time;
                    if ($time > 0) {
                        $criteria[] = "last_login > $time";
                    }
                }
            }
            if (!empty($_POST['mail_regd_min'])) {
                $time = strtotime(trim($_POST['mail_regd_min']));
                if ($time > 0) {
                    $criteria[] = "user_regdate > $time";
                }
            }
            if (!empty($_POST['mail_regd_max'])) {
                $time = strtotime(trim($_POST['mail_regd_max']));
                if ($time > 0) {
                    $criteria[] = "user_regdate < $time";
                }
            }
            if (!empty($criteria) || !empty($_POST['mail_to_group'])) {
                $criteria_object = new CriteriaCompo();
                $criteria_object->setStart(@$_POST['mail_start']);
                $criteria_object->setLimit($limit);
                foreach ($criteria as $c) {
                    list($field, $op, $value) = explode(' ', $c);
                    $crit         = new Criteria($field, $value, $op);
                    $crit->prefix = 'u';
                    $criteria_object->add($crit, 'AND');
                }
                /* @var $member_handler XoopsMemberHandler */
                $member_handler = xoops_getHandler('member');
                $groups         = empty($_POST['mail_to_group']) ? array() : array_map('intval', $_POST['mail_to_group']);
                $getusers       = $member_handler->getUsersByGroupLink($groups, $criteria_object, true);
                $count_criteria = $member_handler->getUserCountByGroupLink($groups, $criteria_object);
                foreach ($getusers as $getuser) {
                    if (!in_array($getuser->getVar('uid'), $added_id)) {
                        $added[]    = $getuser;
                        $added_id[] = $getuser->getVar('uid');
                    }
                }
            }
            if (!empty($_POST['mail_to_user'])) {
                foreach ($_POST['mail_to_user'] as $to_user) {
                    if (!in_array($to_user, $added_id)) {
                        $added[]    = new XoopsUser($to_user);
                        $added_id[] = $to_user;
                    }
                }
            }
            $added_count = count($added);

            //openTable();
            if ($added_count > 0) {
                $myts        = MyTextSanitizer::getInstance();
                $xoopsMailer =& xoops_getMailer();
                for ($i = 0; $i < $added_count; ++$i) {
                    $xoopsMailer->setToUsers($added[$i]);
                }
                $xoopsMailer->setFromName($myts->stripSlashesGPC($_POST['mail_fromname']));
                $xoopsMailer->setFromEmail($myts->stripSlashesGPC($_POST['mail_fromemail']));
                $xoopsMailer->setSubject($myts->stripSlashesGPC($_POST['mail_subject']));
                $xoopsMailer->setBody($myts->stripSlashesGPC($_POST['mail_body']));
                if (in_array('mail', $_POST['mail_send_to'])) {
                    $xoopsMailer->useMail();
                }
                if (in_array('pm', $_POST['mail_send_to']) && empty($_POST['mail_inactive'])) {
                    $xoopsMailer->usePM();
                }
                $xoopsMailer->send(true);
                $xoopsTpl->assign('Sucess', $xoopsMailer->getSuccess());
                $xoopsTpl->assign('Errors', $xoopsMailer->getErrors());
                //echo $xoopsMailer->getSuccess();
                //echo $xoopsMailer->getErrors();

                if ($count_criteria > $limit) {
                    $form = new XoopsThemeForm(_AM_SYSTEM_MAILUSERS_LIST, 'mailusers', 'admin.php?fct=mailusers', 'post', true);
                    if (!empty($_POST['mail_to_group'])) {
                        foreach ($_POST['mail_to_group'] as $mailgroup) {
                            $group_hidden = new XoopsFormHidden('mail_to_group[]', $mailgroup);
                            $form->addElement($group_hidden);
                        }
                    }
                    $inactive_hidden    = new XoopsFormHidden('mail_inactive', @$_POST['mail_inactive']);
                    $lastlog_min_hidden = new XoopsFormHidden('mail_lastlog_min', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_lastlog_min'])));
                    $lastlog_max_hidden = new XoopsFormHidden('mail_lastlog_max', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_lastlog_max'])));
                    $regd_min_hidden    = new XoopsFormHidden('mail_regd_min', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_regd_min'])));
                    $regd_max_hidden    = new XoopsFormHidden('mail_regd_max', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_regd_max'])));
                    $idle_more_hidden   = new XoopsFormHidden('mail_idle_more', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_idle_more'])));
                    $idle_less_hidden   = new XoopsFormHidden('mail_idle_less', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_idle_less'])));
                    $fname_hidden       = new XoopsFormHidden('mail_fromname', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_fromname'])));
                    $femail_hidden      = new XoopsFormHidden('mail_fromemail', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_fromemail'])));
                    $subject_hidden     = new XoopsFormHidden('mail_subject', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_subject'])));
                    $body_hidden        = new XoopsFormHidden('mail_body', $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['mail_body'])));
                    $start_hidden       = new XoopsFormHidden('mail_start', $_POST['mail_start'] + $limit);
                    $mail_mailok_hidden = new XoopsFormHidden('mail_mailok', $myts->htmlSpecialChars($myts->stripSlashesGPC(@$_POST['mail_mailok'])));
                    $op_hidden          = new XoopsFormHidden('op', 'send');
                    $submit_button      = new XoopsFormButton('', 'mail_submit', _AM_SYSTEM_MAILUSERS_SENDNEXT, 'submit');
                    $sent_label         = new XoopsFormLabel(_AM_SYSTEM_MAILUSERS_SENT, sprintf(_AM_SYSTEM_MAILUSERS_SENTNUM, $_POST['mail_start'] + 1, $_POST['mail_start'] + $limit, $count_criteria + $added_count - $limit));
                    $form->addElement($sent_label);
                    $form->addElement($inactive_hidden);
                    $form->addElement($lastlog_min_hidden);
                    $form->addElement($lastlog_max_hidden);
                    $form->addElement($regd_min_hidden);
                    $form->addElement($regd_max_hidden);
                    $form->addElement($idle_more_hidden);
                    $form->addElement($idle_less_hidden);
                    $form->addElement($fname_hidden);
                    $form->addElement($femail_hidden);
                    $form->addElement($subject_hidden);
                    $form->addElement($body_hidden);
                    $form->addElement($op_hidden);
                    $form->addElement($start_hidden);
                    $form->addElement($mail_mailok_hidden);
                    if (isset($_POST['mail_send_to']) && is_array($_POST['mail_send_to'])) {
                        foreach ($_POST['mail_send_to'] as $v) {
                            $form->addElement(new XoopsFormHidden('mail_send_to[]', $v));
                        }
                    } else {
                        $to_hidden = new XoopsFormHidden('mail_send_to', 'mail');
                        $form->addElement($to_hidden);
                    }
                    $form->addElement($submit_button);
                    $form->display();
                } else {
                    echo '<h4>' . _AM_SYSTEM_MAILUSERS_SENDCOMP . '</h4>';
                }
            } else {
                echo '<h4>' . _AM_SYSTEM_MAILUSERS_NOUSERMATCH . '</h4>';
            }
        }
        break;
}
// Call Footer
xoops_cp_footer();
