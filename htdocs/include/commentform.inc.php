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
/* @var  $xoopsUser XoopsUser */
/* @var $xoopsConfig XoopsConfigItem */
/* @var $xoopsModule XoopsModule*/

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once $GLOBALS['xoops']->path('class/xoopslists.php');
include $GLOBALS['xoops']->path('class/xoopsformloader.php');

$cform = new XoopsThemeForm(_CM_POSTCOMMENT, 'commentform', 'postcomment.php', 'post', true);
if (!preg_match('/^' . _RE . '/i', $subject)) {
    $subject = _RE . ' ' . xoops_substr($subject, 0, 56);
}

$cform->addElement(new XoopsFormText(_CM_TITLE, 'subject', 50, 255, $subject), true);
$icons_radio   = new XoopsFormRadio(_MESSAGEICON, 'icon', $icon);
$subject_icons = XoopsLists::getSubjectsList();
foreach ($subject_icons as $iconfile) {
    $icons_radio->addOption($iconfile, '<img src="' . XOOPS_URL . '/images/subject/' . $iconfile . '" alt="" />');
}
$cform->addElement($icons_radio);
$cform->addElement(new XoopsFormDhtmlTextArea(_CM_MESSAGE, 'message', $message, 10, 50), true);
$option_tray = new XoopsFormElementTray(_OPTIONS, '<br>');
if ($xoopsUser) {
    if ($xoopsConfig['anonpost'] == 1) {
        $noname_checkbox = new XoopsFormCheckBox('', 'noname', $noname);
        $noname_checkbox->addOption(1, _POSTANON);
        $option_tray->addElement($noname_checkbox);
    }
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $nohtml_checkbox = new XoopsFormCheckBox('', 'nohtml', $nohtml);
        $nohtml_checkbox->addOption(1, _DISABLEHTML);
        $option_tray->addElement($nohtml_checkbox);
    }
}
$smiley_checkbox = new XoopsFormCheckBox('', 'nosmiley', $nosmiley);
$smiley_checkbox->addOption(1, _DISABLESMILEY);
$option_tray->addElement($smiley_checkbox);

$cform->addElement($option_tray);

if (!$xoopsUser) {
    $cform->addElement(new XoopsFormCaptcha());
}

$cform->addElement(new XoopsFormHidden('pid', (int)$pid));
$cform->addElement(new XoopsFormHidden('comment_id', (int)$comment_id));
$cform->addElement(new XoopsFormHidden('item_id', (int)$item_id));
$cform->addElement(new XoopsFormHidden('order', (int)$order));
$button_tray = new XoopsFormElementTray('', '&nbsp;');
$button_tray->addElement(new XoopsFormButton('', 'preview', _PREVIEW, 'submit'));
$button_tray->addElement(new XoopsFormButton('', 'post', _CM_POSTCOMMENT, 'submit'));
$cform->addElement($button_tray);
$cform->display();
