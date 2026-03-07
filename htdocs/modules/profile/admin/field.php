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
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
use Xmf\Request;

include_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$indexAdmin = new ModuleAdmin();

$indexAdmin->addItemButton(_ADD . ' ' . _PROFILE_AM_FIELD, 'field.php?op=new', 'add', '');

echo $indexAdmin->addNavigation(basename(__FILE__));
echo $indexAdmin->renderButton('right', '');

$op = Request::getCmd('op', Request::hasVar('id') ? 'edit' : 'list');
/** @var XoopsModuleHandler $profilefield_handler */
$profilefield_handler = xoops_getModuleHandler('field');

switch ($op) {
    default:
    case 'list':
        $fields = $profilefield_handler->getObjects(null, true, false);

        /** @var XoopsModuleHandler $module_handler */
        $module_handler = xoops_getHandler('module');
        $modules        = $module_handler->getObjects(null, true);

        /** @var XoopsModuleHandler $cat_handler */
        $cat_handler = xoops_getModuleHandler('category');
        $criteria    = new CriteriaCompo();
        $criteria->setSort('cat_weight');
        $cats = $cat_handler->getObjects($criteria, true);
        unset($criteria);

        $categories[0] = _PROFILE_AM_DEFAULT;
        if (count($cats) > 0) {
            foreach (array_keys($cats) as $i) {
                $categories[$cats[$i]->getVar('cat_id')] = $cats[$i]->getVar('cat_title');
            }
        }
        $GLOBALS['xoopsTpl']->assign('categories', $categories);
        unset($categories);
        $valuetypes = [
            XOBJ_DTYPE_ARRAY   => _PROFILE_AM_ARRAY,
            XOBJ_DTYPE_EMAIL   => _PROFILE_AM_EMAIL,
            XOBJ_DTYPE_INT     => _PROFILE_AM_INT,
            XOBJ_DTYPE_TXTAREA => _PROFILE_AM_TXTAREA,
            XOBJ_DTYPE_TXTBOX  => _PROFILE_AM_TXTBOX,
            XOBJ_DTYPE_URL     => _PROFILE_AM_URL,
            XOBJ_DTYPE_OTHER   => _PROFILE_AM_OTHER,
            XOBJ_DTYPE_MTIME   => _PROFILE_AM_DATE,
        ];

        $fieldtypes = [
            'checkbox'     => _PROFILE_AM_CHECKBOX,
            'group'        => _PROFILE_AM_GROUP,
            'group_multi'  => _PROFILE_AM_GROUPMULTI,
            'language'     => _PROFILE_AM_LANGUAGE,
            'radio'        => _PROFILE_AM_RADIO,
            'select'       => _PROFILE_AM_SELECT,
            'select_multi' => _PROFILE_AM_SELECTMULTI,
            'textarea'     => _PROFILE_AM_TEXTAREA,
            'dhtml'        => _PROFILE_AM_DHTMLTEXTAREA,
            'textbox'      => _PROFILE_AM_TEXTBOX,
            'timezone'     => _PROFILE_AM_TIMEZONE,
            'yesno'        => _PROFILE_AM_YESNO,
            'date'         => _PROFILE_AM_DATE,
            'datetime'     => _PROFILE_AM_DATETIME,
            'longdate'     => _PROFILE_AM_LONGDATE,
            'theme'        => _PROFILE_AM_THEME,
            'autotext'     => _PROFILE_AM_AUTOTEXT,
            'rank'         => _PROFILE_AM_RANK,
        ];

        foreach (array_keys($fields) as $i) {
            $fields[$i]['canEdit']               = $fields[$i]['field_config'] || $fields[$i]['field_show'] || $fields[$i]['field_edit'];
            $fields[$i]['canDelete']             = $fields[$i]['field_config'];
            $fields[$i]['fieldtype']             = $fieldtypes[$fields[$i]['field_type']];
            $fields[$i]['valuetype']             = $valuetypes[$fields[$i]['field_valuetype']];
            $categories[$fields[$i]['cat_id']][] = $fields[$i];
            $weights[$fields[$i]['cat_id']][]    = $fields[$i]['field_weight'];
        }
        //sort fields order in categories
        foreach (array_keys($categories) as $i) {
            array_multisort($weights[$i], SORT_ASC, array_keys($categories[$i]), SORT_ASC, $categories[$i]);
        }
        ksort($categories);
        $GLOBALS['xoopsTpl']->assign('fieldcategories', $categories);
        $GLOBALS['xoopsTpl']->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
        $template_main = 'profile_admin_fieldlist.tpl';
        break;

    case 'new':
        include_once dirname(__DIR__) . '/include/forms.php';
        $obj  = $profilefield_handler->create();
        $form = profile_getFieldForm($obj);
        $form->display();
        break;

    case 'edit':
        $obj = $profilefield_handler->get(Request::getInt('id', 0));
        if (!$obj->getVar('field_config') && !$obj->getVar('field_show') && !$obj->getVar('field_edit')) { //If no configs exist
            redirect_header('field.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
        }
        include_once dirname(__DIR__) . '/include/forms.php';
        $form = profile_getFieldForm($obj);
        $form->display();
        break;

    case 'edit-option-strings':
        $obj = $profilefield_handler->get(Request::getInt('id', 0));
        $fieldOptions = $obj->getVar('field_options');
        if (empty($fieldOptions)) { //If no option strings exist
            redirect_header('field.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
        }
        include_once dirname(__DIR__) . '/include/forms.php';
        $form = profile_getFieldOptionForm($obj);
        $form->display();
        break;

    case 'reorder':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $field_ids = Request::getArray('field_ids', [], 'POST');
        if (count($field_ids) > 0) {
            $oldweight = Request::getArray('oldweight', [], 'POST');
            $oldcat    = Request::getArray('oldcat', [], 'POST');
            $category  = Request::getArray('category', [], 'POST');
            $weight    = Request::getArray('weight', [], 'POST');
            $ids       = [];
            foreach ($field_ids as $field_id) {
                if ($oldweight[$field_id] != $weight[$field_id] || $oldcat[$field_id] != $category[$field_id]) {
                    //if field has changed
                    $ids[] = (int) $field_id;
                }
            }
            if (count($ids) > 0) {
                $errors = [];
                //if there are changed fields, fetch the fieldcategory objects
                /** @var XoopsModuleHandler $field_handler */
                $field_handler = xoops_getModuleHandler('field');
                $fields        = $field_handler->getObjects(new Criteria('field_id', '(' . implode(',', $ids) . ')', 'IN'), true);
                foreach ($ids as $i) {
                    $fields[$i]->setVar('field_weight', (int) $weight[$i]);
                    $fields[$i]->setVar('cat_id', (int) $category[$i]);
                    if (!$field_handler->insert($fields[$i])) {
                        $errors = array_merge($errors, $fields[$i]->getErrors());
                    }
                }
                if (count($errors) == 0) {
                    //no errors
                    redirect_header('field.php', 2, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_FIELDS));
                } else {
                    redirect_header('field.php', 3, implode('<br>', $errors));
                }
            }
        }
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $redirect_to_edit = false;
        $fieldId = Request::getInt('id', 0, 'POST');
        if ($fieldId > 0) {
            $obj = $profilefield_handler->get($fieldId);
            if (!$obj->getVar('field_config') && !$obj->getVar('field_show') && !$obj->getVar('field_edit')) { //If no configs exist
                redirect_header('admin.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
            }
        } else {
            $obj = $profilefield_handler->create();
            $obj->setVar('field_name', Request::getCmd('field_name', '', 'POST'));
            $obj->setVar('field_moduleid', $GLOBALS['xoopsModule']->getVar('mid'));
            $obj->setVar('field_show', 1);
            $obj->setVar('field_edit', 1);
            $obj->setVar('field_config', 1);
            $redirect_to_edit = true;
        }
        $obj->setVar('field_title', Request::getString('field_title', '', 'POST'));
        $obj->setVar('field_description', Request::getString('field_description', '', 'POST'));
        if ($obj->getVar('field_config')) {
            $obj->setVar('field_type', Request::getCmd('field_type', '', 'POST'));
            if (Request::hasVar('field_valuetype', 'POST')) {
                $obj->setVar('field_valuetype', Request::getInt('field_valuetype', 0, 'POST'));
            }
            $options = $obj->getVar('field_options');

            $removeOptions = Request::getArray('removeOptions', [], 'POST');
            if (!empty($removeOptions)) {
                foreach ($removeOptions as $index) {
                    unset($options[$index]);
                }
                $redirect_to_edit = true;
            }

            $addOption = Request::getArray('addOption', [], 'POST');
            if (!empty($addOption)) {
                foreach ($addOption as $option) {
                    if (empty($option['value'])) {
                        continue;
                    }
                    $options[$option['key']] = $option['value'];
                    $redirect_to_edit        = true;
                }
            }
            $obj->setVar('field_options', $options);
        }
        if ($obj->getVar('field_edit')) {
            $required = Request::getInt('field_required', 0, 'POST');
            $obj->setVar('field_required', $required); //0 = no, 1 = yes
            if (Request::hasVar('field_maxlength', 'POST')) {
                $obj->setVar('field_maxlength', Request::getInt('field_maxlength', 0, 'POST'));
            }
            if (Request::hasVar('field_default', 'POST')) {
                $field_default = $obj->getValueForSave(Request::getString('field_default', '', 'POST'));
                //Check for multiple selections
                if (is_array($field_default)) {
                    $obj->setVar('field_default', serialize($field_default));
                } else {
                    $obj->setVar('field_default', $field_default);
                }
            }
        }

        if ($obj->getVar('field_show')) {
            $obj->setVar('field_weight', Request::getInt('field_weight', 0, 'POST'));
            $obj->setVar('cat_id', Request::getInt('field_category', 0, 'POST'));
        }
        if (Request::hasVar('step_id', 'POST')) {
            $obj->setVar('step_id', Request::getInt('step_id', 0, 'POST'));
        }
        if ($profilefield_handler->insert($obj)) {
            /** @var XoopsGroupPermHandler $groupperm_handler */
            $groupperm_handler = xoops_getHandler('groupperm');

            $perm_arr = [];
            if ($obj->getVar('field_show')) {
                $perm_arr[] = 'profile_show';
                $perm_arr[] = 'profile_visible';
            }
            if ($obj->getVar('field_edit')) {
                $perm_arr[] = 'profile_edit';
            }
            if ($obj->getVar('field_edit') || $obj->getVar('field_show')) {
                $perm_arr[] = 'profile_search';
            }
            if (count($perm_arr) > 0) {
                foreach ($perm_arr as $perm) {
                    $criteria = new CriteriaCompo(new Criteria('gperm_name', $perm));
                    $criteria->add(new Criteria('gperm_itemid', (int) $obj->getVar('field_id')));
                    $criteria->add(new Criteria('gperm_modid', (int) $GLOBALS['xoopsModule']->getVar('mid')));
                    $permGroups = Request::getArray($perm, [], 'POST');
                    if (!empty($permGroups)) {
                        $perms = $groupperm_handler->getObjects($criteria);
                        if (count($perms) > 0) {
                            foreach (array_keys($perms) as $i) {
                                $groups[$perms[$i]->getVar('gperm_groupid')] = & $perms[$i];
                            }
                        } else {
                            $groups = [];
                        }
                        foreach ($permGroups as $groupid) {
                            $groupid = (int) $groupid;
                            if (!isset($groups[$groupid])) {
                                $perm_obj = $groupperm_handler->create();
                                $perm_obj->setVar('gperm_name', $perm);
                                $perm_obj->setVar('gperm_itemid', (int) $obj->getVar('field_id'));
                                $perm_obj->setVar('gperm_modid', $GLOBALS['xoopsModule']->getVar('mid'));
                                $perm_obj->setVar('gperm_groupid', $groupid);
                                $groupperm_handler->insert($perm_obj);
                                unset($perm_obj);
                            }
                        }
                        $removed_groups = array_diff(array_keys($groups), $permGroups);
                        if (count($removed_groups) > 0) {
                            $criteria->add(new Criteria('gperm_groupid', '(' . implode(',', $removed_groups) . ')', 'IN'));
                            $groupperm_handler->deleteAll($criteria);
                        }
                        unset($groups);
                    } else {
                        $groupperm_handler->deleteAll($criteria);
                    }
                    unset($criteria);
                }
            }
            $url = $redirect_to_edit ? 'field.php?op=edit&amp;id=' . $obj->getVar('field_id') : 'field.php';
            redirect_header($url, 3, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_FIELD));
        }
        include_once dirname(__DIR__) . '/include/forms.php';
        echo $obj->getHtmlErrors();
        $form = profile_getFieldForm($obj);
        $form->display();
        break;

    case 'save-option-strings':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $obj = $profilefield_handler->get(Request::getInt('id', 0, 'POST'));
        $fieldOptions = Request::getArray('field_options', [], 'POST');
        if (empty($fieldOptions)) { //If no option strings exist
            redirect_header('field.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
        }
        $obj->setVar('field_options', $fieldOptions);
        if ($profilefield_handler->insert($obj)) {
            redirect_header('field.php', 2, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_FIELD));
        }
        redirect_header('field.php', 2, implode(',', $obj->getErrors()));
        break;

    case 'delete':
        $obj = $profilefield_handler->get(Request::getInt('id', 0));
        if (!$obj->getVar('field_config')) {
            redirect_header('index.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
        }
        if (Request::getInt('ok', 0) === 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($profilefield_handler->delete($obj)) {
                redirect_header('field.php', 3, sprintf(_PROFILE_AM_DELETEDSUCCESS, _PROFILE_AM_FIELD));
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                [
                    'ok' => 1,
                    'id' => Request::getInt('id', 0),
                    'op' => 'delete',
                ],
                $_SERVER['REQUEST_URI'],
                sprintf(_PROFILE_AM_RUSUREDEL, $obj->getVar('field_title')),
            );
        }
        break;

    case 'toggle':
        $field_id = Request::getInt('field_id', 0);
        if ($field_id > 0) {
            $field_required = Request::getInt('field_required', 0);
            profile_visible_toggle($field_id, $field_required);
        }
        break;
}

if (isset($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}

/**
 * @param $field_id
 * @param $field_required
 */
function profile_visible_toggle($field_id, $field_required)
{
    $field_required = ($field_required == 1) ? 0 : 1;
    $this_handler   = xoops_getModuleHandler('field', 'profile');
    $obj            = $this_handler->get($field_id);
    $obj->setVar('field_required', $field_required);
    if ($this_handler->insert($obj, true)) {
        redirect_header('field.php', 1, _PROFILE_AM_REQUIRED_TOGGLE_SUCCESS);
    } else {
        redirect_header('field.php', 1, _PROFILE_AM_REQUIRED_TOGGLE_FAILED);
    }
}

include_once __DIR__ . '/admin_footer.php';
