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

//there is no way to override current tabs when using system menu
//this dirty hack will have to do it
$_SERVER['REQUEST_URI'] = 'admin/permissions.php';

xoops_cp_header();

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'visibility';

$visibility_handler = xoops_getModuleHandler('visibility');
$field_handler      = xoops_getModuleHandler('field');
$fields             = $field_handler->getList();

if (isset($_REQUEST['submit'])) {
    $visibility = $visibility_handler->create();
    $visibility->setVar('field_id', $_REQUEST['field_id']);
    $visibility->setVar('user_group', $_REQUEST['ug']);
    $visibility->setVar('profile_group', $_REQUEST['pg']);
    $visibility_handler->insert($visibility, true);
    redirect_header('visibility.php', 2, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_PROF_VISIBLE));
}
if ($op === 'del') {
    $criteria = new CriteriaCompo(new Criteria('field_id', (int)$_REQUEST['field_id']));
    $criteria->add(new Criteria('user_group', (int)$_REQUEST['ug']));
    $criteria->add(new Criteria('profile_group', (int)$_REQUEST['pg']));
    $visibility_handler->deleteAll($criteria, true);
    redirect_header('visibility.php', 2, sprintf(_PROFILE_AM_DELETEDSUCCESS, _PROFILE_AM_PROF_VISIBLE));
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

$criteria = new CriteriaCompo();
//$criteria->setGroupBy('field_id, user_group, profile_group');
$criteria->setSort('field_id, user_group, profile_group');
$criteria->setOrder('DESC');

$visibilities = $visibility_handler->getAllByFieldId($criteria);

/* @var $member_handler XoopsMemberHandler */
$member_handler = xoops_getHandler('member');
$groups         = $member_handler->getGroupList();
$groups[0]      = _PROFILE_AM_FIELDVISIBLETOALL;
asort($groups);

$GLOBALS['xoopsTpl']->assign('fields', $fields);
$GLOBALS['xoopsTpl']->assign('visibilities', $visibilities);
$GLOBALS['xoopsTpl']->assign('groups', $groups);

$add_form = new XoopsSimpleForm('', 'addform', 'visibility.php');

$sel_field = new XoopsFormSelect(_PROFILE_AM_FIELDVISIBLE, 'field_id');
$sel_field->setExtra("style='width: 200px;'");
$sel_field->addOptionArray($fields);
$add_form->addElement($sel_field);

$sel_ug = new XoopsFormSelect(_PROFILE_AM_FIELDVISIBLEFOR, 'ug');
$sel_ug->addOptionArray($groups);
$add_form->addElement($sel_ug);

unset($groups[XOOPS_GROUP_ANONYMOUS]);
$sel_pg = new XoopsFormSelect(_PROFILE_AM_FIELDVISIBLEON, 'pg');
$sel_pg->addOptionArray($groups);
$add_form->addElement($sel_pg);

$add_form->addElement(new XoopsFormButton('', 'submit', _ADD, 'submit'));
$add_form->assign($GLOBALS['xoopsTpl']);

$GLOBALS['xoopsTpl']->display('db:profile_admin_visibility.tpl');

include_once __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
