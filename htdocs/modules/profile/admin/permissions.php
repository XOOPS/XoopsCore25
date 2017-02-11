<?php
/**
 * Extended User Profile
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
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation(basename(__FILE__));

$op = \Xmf\Request::getCmd('op', 'edit');

$perm_desc = '';
switch ($op) {
    case 'visibility':
        //redirect_header("visibility.php", 0, _PROFILE_AM_PROF_VISIBLE);
        header('Location: visibility.php');
        break;

    case 'edit':
        $title_of_form = _PROFILE_AM_PROF_EDITABLE;
        $perm_name     = 'profile_edit';
        $restriction   = 'field_edit';
        $anonymous     = false;
        break;

    case 'search':
        $title_of_form = _PROFILE_AM_PROF_SEARCH;
        $perm_name     = 'profile_search';
        $restriction   = '';
        $anonymous     = true;
        break;

    case 'access':
        $title_of_form = _PROFILE_AM_PROF_ACCESS;
        $perm_name     = 'profile_access';
        $perm_desc     = _PROFILE_AM_PROF_ACCESS_DESC;
        $restriction   = '';
        $anonymous     = true;
        break;
}

include_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');
$opform    = new XoopsSimpleForm('', 'opform', 'permissions.php', 'get');
$op_select = new XoopsFormSelect('', 'op', $op);
$op_select->setExtra('onchange="document.forms.opform.submit()"');
$op_select->addOption('visibility', _PROFILE_AM_PROF_VISIBLE);
$op_select->addOption('edit', _PROFILE_AM_PROF_EDITABLE);
$op_select->addOption('search', _PROFILE_AM_PROF_SEARCH);
$op_select->addOption('access', _PROFILE_AM_PROF_ACCESS);
$opform->addElement($op_select);
$opform->display();

$module_id = $GLOBALS['xoopsModule']->getVar('mid');
include_once $GLOBALS['xoops']->path('/class/xoopsform/grouppermform.php');
$form = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc, 'admin/permissions.php?op=' . $op, $anonymous);
if ($op === 'access') {
    /* @var $member_handler XoopsMemberHandler */
    $member_handler = xoops_getHandler('member');
    $glist          = $member_handler->getGroupList();
    foreach (array_keys($glist) as $i) {
        if ($i != XOOPS_GROUP_ANONYMOUS) {
            $form->addItem($i, $glist[$i]);
        }
    }
} else {
    $profile_handler = xoops_getModuleHandler('profile');
    $fields          = $profile_handler->loadFields();

    if ($op !== 'search') {
        foreach (array_keys($fields) as $i) {
            if ($restriction == '' || $fields[$i]->getVar($restriction)) {
                $form->addItem($fields[$i]->getVar('field_id'), xoops_substr($fields[$i]->getVar('field_title'), 0, 25));
            }
        }
    } else {
        $searchable_types = array(
            'textbox',
            'select',
            'radio',
            'yesno',
            'date',
            'datetime',
            'timezone',
            'language');
        foreach (array_keys($fields) as $i) {
            if (in_array($fields[$i]->getVar('field_type'), $searchable_types)) {
                $form->addItem($fields[$i]->getVar('field_id'), xoops_substr($fields[$i]->getVar('field_title'), 0, 25));
            }
        }
    }
}
$form->display();
include_once __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
