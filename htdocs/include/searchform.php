<?php
/**
 * XOOPS Search Form
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
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

// create form
$search_form = new XoopsThemeForm(_SR_SEARCH, 'search', 'search.php', 'get');

// create form elements
$search_form->addElement(new XoopsFormText(_SR_KEYWORDS, 'query', 30, 255, htmlspecialchars(stripslashes(implode(' ', $queries)), ENT_QUOTES)), true);
$type_select = new XoopsFormSelect(_SR_TYPE, 'andor', $andor);
$type_select->addOptionArray(array(
                                 'AND'   => _SR_ALL,
                                 'OR'    => _SR_ANY,
                                 'exact' => _SR_EXACT));
$search_form->addElement($type_select);
if (!empty($mids)) {
    $mods_checkbox = new XoopsFormCheckBox(_SR_SEARCHIN, 'mids[]', $mids);
} else {
    $mods_checkbox = new XoopsFormCheckBox(_SR_SEARCHIN, 'mids[]', $mid);
}
if (empty($modules)) {
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('hassearch', 1));
    $criteria->add(new Criteria('isactive', 1));
    if (!empty($available_modules)) {
        $criteria->add(new Criteria('mid', '(' . implode(',', $available_modules) . ')', 'IN'));
    }
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $mods_checkbox->addOptionArray($module_handler->getList($criteria));
} else {
    foreach ($modules as $mid => $module) {
        $module_array[$mid] = $module->getVar('name');
    }
    $mods_checkbox->addOptionArray($module_array);
}
$search_form->addElement($mods_checkbox);
if ($xoopsConfigSearch['keyword_min'] > 0) {
    $search_form->addElement(new XoopsFormLabel(_SR_SEARCHRULE, sprintf(_SR_KEYIGNORE, $xoopsConfigSearch['keyword_min'])));
}
$search_form->addElement(new XoopsFormHidden('action', 'results'));
$search_form->addElement(new XoopsFormHiddenToken('id'));
$search_form->addElement(new XoopsFormButton('', 'submit', _SR_SEARCH, 'submit'));
