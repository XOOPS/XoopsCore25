<?php
/**
 * XOOPS Editor usage guide
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
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Edit form with selected editor
 */
$sample_form = new XoopsThemeForm('', 'sample_form', 'action.php');
$sample_form->setExtra('enctype="multipart/form-data"');
// Not required but for user-friendly concern
$editor = !empty($_REQUEST['editor']) ? $_REQUEST['editor'] : '';
if (!empty($editor)) {
    setcookie('editor', $editor); // save to cookie
} else {
    // Or use user pre-selected editor through profile
    if (is_object($xoopsUser)) {
        $editor = @ $xoopsUser->getVar('editor'); // Need set through user profile
    }
    // Add the editor selection box
    // If dohtml is disabled, set $noHtml = true
    $sample_form->addElement(new XoopsFormSelectEditor($sample_form, 'editor', $editor, $noHtml = false));
    // options for the editor
    // required configs
    $options['editor'] = $editor;
    $options['name']   = 'required_element';
    $options['value']  = empty($_REQUEST['message']) ? '' : $_REQUEST['message'];
    // optional configs
    $options['rows']   = 25; // default value = 5
    $options['cols']   = 60; // default value = 50
    $options['width']  = '100%'; // default value = 100%
    $options['height'] = '400px'; // default value = 400px

    // "textarea": if the selected editor with name of $editor can not be created, the editor "textarea" will be used
    // if no $onFailure is set, then the first available editor will be used
    // If dohtml is disabled, set $noHtml to true
    $sample_form->addElement(new XoopsFormEditor(_MD_MESSAGEC, $options['name'], $options, $nohtml = false, $onfailure = 'textarea'), true);
    $sample_form->addElement(new XoopsFormText('SOME REQUIRED ELEMENTS', 'required_element2', 50, 255, $required_element2), true);
    $sample_form->addElement(new XoopsFormButton('', 'save', _SUBMIT, 'submit'));
    $sample_form->display();
}
