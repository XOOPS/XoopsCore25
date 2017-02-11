<?php
/**
 * XOOPS comment form
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
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    die('Restricted access');
}

$com_modid = $xoopsModule->getVar('mid');

xoops_load('XoopsLists');
xoops_load('XoopsFormLoader');

$cform = new XoopsThemeForm(_CM_POSTCOMMENT, 'commentform', 'comment_post.php', 'post', true);
if (isset($xoopsModuleConfig['com_rule'])) {
    include_once $GLOBALS['xoops']->path('include/comment_constants.php');
    switch ($xoopsModuleConfig['com_rule']) {
        case XOOPS_COMMENT_APPROVEALL:
            $rule_text = _CM_COMAPPROVEALL;
            break;
        case XOOPS_COMMENT_APPROVEUSER:
            $rule_text = _CM_COMAPPROVEUSER;
            break;
        case XOOPS_COMMENT_APPROVEADMIN:
        default:
            $rule_text = _CM_COMAPPROVEADMIN;
            break;
    }
    $cform->addElement(new XoopsFormLabel(_CM_COMRULES, $rule_text));
}

$cform->addElement(new XoopsFormText(_CM_TITLE, 'com_title', 50, 255, $com_title), true);
// Start add by voltan
if (!($com_user == '' && $com_email == '') || !$xoopsUser) {
    $cform->addElement(new XoopsFormText(_CM_USER, 'com_user', 50, 60, $com_user), true);
    $cform->addElement(new XoopsFormText(_CM_EMAIL, 'com_email', 50, 60, $com_email), true);
    $cform->addElement(new XoopsFormText(_CM_URL, 'com_url', 50, 60, $com_url), false);
}
// End add by voltan
$icons_radio   = new XoopsFormRadio(_MESSAGEICON, 'com_icon', $com_icon);
$subject_icons = XoopsLists::getSubjectsList();
foreach ($subject_icons as $iconfile) {
    $icons_radio->addOption($iconfile, '<img src="' . XOOPS_URL . '/images/subject/' . $iconfile . '" alt="" />');
}
$cform->addElement($icons_radio);
// editor
$editor = xoops_getModuleOption('comments_editor', 'system');
if (class_exists('XoopsFormEditor')) {
    $configs = array(
        'name'   => 'com_text',
        'value'  => $com_text,
        'rows'   => 25,
        'cols'   => 90,
        'width'  => '100%',
        'height' => '400px',
        'editor' => $editor);
    $cform->addElement(new XoopsFormEditor(_CM_MESSAGE, 'com_text', $configs, false, $onfailure = 'textarea'));
} else {
    $cform->addElement(new XoopsFormDhtmlTextArea(_CM_MESSAGE, 'com_text', $com_text, 10, 50), true);
}
$option_tray = new XoopsFormElementTray(_OPTIONS, '<br>');
$button_tray = new XoopsFormElementTray('', '&nbsp;');

if (is_object($xoopsUser)) {
    /* @var  $xoopsUser XoopsUser */
    if (isset($xoopsModuleConfig['com_anonpost'])) {
        if ($xoopsModuleConfig['com_anonpost'] == 1) {
            $noname          = !empty($noname) ? 1 : 0;
            $noname_checkbox = new XoopsFormCheckBox('', 'noname', $noname);
            $noname_checkbox->addOption(1, _POSTANON);
            $option_tray->addElement($noname_checkbox);
        }
    }
    if (false !== $xoopsUser->isAdmin($com_modid)) {
        // show status change box when editing (comment id is not empty)
        if (!empty($com_id)) {
            include_once $GLOBALS['xoops']->path('include/comment_constants.php');
            $status_select = new XoopsFormSelect(_CM_STATUS, 'com_status', $com_status);
            $status_select->addOptionArray(array(
                                               XOOPS_COMMENT_PENDING => _CM_PENDING,
                                               XOOPS_COMMENT_ACTIVE  => _CM_ACTIVE,
                                               XOOPS_COMMENT_HIDDEN  => _CM_HIDDEN));
            $cform->addElement($status_select);
            $button_tray->addElement(new XoopsFormButton('', 'com_dodelete', _DELETE, 'submit'));
        }
        if (isset($editor) && in_array($editor, array('textarea', 'dhtmltextarea'))) {
            $html_checkbox = new XoopsFormCheckBox('', 'dohtml', $dohtml);
            $html_checkbox->addOption(1, _CM_DOHTML);
            $option_tray->addElement($html_checkbox);
        }
    }
}
if (isset($editor) && in_array($editor, array('textarea', 'dhtmltextarea'))) {
}
$smiley_checkbox = new XoopsFormCheckBox('', 'dosmiley', $dosmiley);
$smiley_checkbox->addOption(1, _CM_DOSMILEY);
$option_tray->addElement($smiley_checkbox);
$xcode_checkbox = new XoopsFormCheckBox('', 'doxcode', $doxcode);
$xcode_checkbox->addOption(1, _CM_DOXCODE);
$option_tray->addElement($xcode_checkbox);
if (isset($editor) && in_array($editor, array('textarea', 'dhtmltextarea'))) {
    $br_checkbox = new XoopsFormCheckBox('', 'dobr', $dobr);
    $br_checkbox->addOption(1, _CM_DOAUTOWRAP);
    $option_tray->addElement($br_checkbox);
} else {
    $cform->addElement(new xoopsFormHidden('dohtml', 1));
    $cform->addElement(new xoopsFormHidden('dobr', 0));
}
$cform->addElement($option_tray);
if (!$xoopsUser) {
    $cform->addElement(new XoopsFormCaptcha());
}
$cform->addElement(new XoopsFormHidden('com_pid', (int)$com_pid));
$cform->addElement(new XoopsFormHidden('com_rootid', (int)$com_rootid));
$cform->addElement(new XoopsFormHidden('com_id', $com_id));
$cform->addElement(new XoopsFormHidden('com_itemid', $com_itemid));
$cform->addElement(new XoopsFormHidden('com_order', $com_order));
$cform->addElement(new XoopsFormHidden('com_mode', $com_mode));

// add module specific extra params
if ('system' !== $xoopsModule->getVar('dirname')) {
    $comment_config = $xoopsModule->getInfo('comments');
    if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
        $myts = MyTextSanitizer::getInstance();
        foreach ($comment_config['extraParams'] as $extra_param) {
            // This routine is included from forms accessed via both GET and POST
            $hidden_value = '';
            if (isset($_POST[$extra_param])) {
                $hidden_value = $myts->stripSlashesGPC($_POST[$extra_param]);
            } elseif (isset($_GET[$extra_param])) {
                $hidden_value = $myts->stripSlashesGPC($_GET[$extra_param]);
            }
            $cform->addElement(new XoopsFormHidden($extra_param, $hidden_value));
        }
    }
}
$button_tray->addElement(new XoopsFormButton('', 'com_dopreview', _PREVIEW, 'submit'));
$button_tray->addElement(new XoopsFormButton('', 'com_dopost', _CM_POSTCOMMENT, 'submit'));
$cform->addElement($button_tray);
$cform->display();
