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

// defined('XOOPS_ROOT_PATH') || exit("XOOPS root path not defined");

/**
 * Get {@link XoopsThemeForm} for adding/editing fields
 *
 * @param ProfileField $field  {@link ProfileField} object to get edit form for
 * @param mixed        $action URL to submit to - or false for $_SERVER['REQUEST_URI']
 *
 * @return object
 */
function profile_getFieldForm(ProfileField $field, $action = false)
{
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    $title = $field->isNew() ? sprintf(_PROFILE_AM_ADD, _PROFILE_AM_FIELD) : sprintf(_PROFILE_AM_EDIT, _PROFILE_AM_FIELD);

    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    $form = new XoopsThemeForm($title, 'form', $action, 'post', true);

    $form->addElement(new XoopsFormText(_PROFILE_AM_TITLE, 'field_title', 35, 255, $field->getVar('field_title', 'e')));
    $form->addElement(new XoopsFormTextArea(_PROFILE_AM_DESCRIPTION, 'field_description', $field->getVar('field_description', 'e')));

    $fieldcat_id = 0;
    if (!$field->isNew()) {
        $fieldcat_id = $field->getVar('cat_id');
    }
    $category_handler = xoops_getModuleHandler('category');
    $cat_select       = new XoopsFormSelect(_PROFILE_AM_CATEGORY, 'field_category', $fieldcat_id);
    $cat_select->addOption(0, _PROFILE_AM_DEFAULT);
    $cat_select->addOptionArray($category_handler->getList());
    $form->addElement($cat_select);
    $form->addElement(new XoopsFormText(_PROFILE_AM_WEIGHT, 'field_weight', 10, 10, $field->getVar('field_weight', 'e')));
    if ($field->getVar('field_config') || $field->isNew()) {
        if (!$field->isNew()) {
            $form->addElement(new XoopsFormLabel(_PROFILE_AM_NAME, $field->getVar('field_name')));
            $form->addElement(new XoopsFormHidden('id', $field->getVar('field_id')));
        } else {
            $form->addElement(new XoopsFormText(_PROFILE_AM_NAME, 'field_name', 35, 255, $field->getVar('field_name', 'e')));
        }

        //autotext and theme left out of this one as fields of that type should never be changed (valid assumption, I think)
        $fieldtypes = array(
            'checkbox'     => _PROFILE_AM_CHECKBOX,
            'date'         => _PROFILE_AM_DATE,
            'datetime'     => _PROFILE_AM_DATETIME,
            'longdate'     => _PROFILE_AM_LONGDATE,
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
            'yesno'        => _PROFILE_AM_YESNO);

        $element_select = new XoopsFormSelect(_PROFILE_AM_TYPE, 'field_type', $field->getVar('field_type', 'e'));
        $element_select->addOptionArray($fieldtypes);

        $form->addElement($element_select);

        switch ($field->getVar('field_type')) {
            case 'textbox':
                $valuetypes = array(
                    XOBJ_DTYPE_TXTBOX          => _PROFILE_AM_TXTBOX,
                    XOBJ_DTYPE_EMAIL           => _PROFILE_AM_EMAIL,
                    XOBJ_DTYPE_INT             => _PROFILE_AM_INT,
                    XOBJ_DTYPE_FLOAT           => _PROFILE_AM_FLOAT,
                    XOBJ_DTYPE_DECIMAL         => _PROFILE_AM_DECIMAL,
                    XOBJ_DTYPE_TXTAREA         => _PROFILE_AM_TXTAREA,
                    XOBJ_DTYPE_URL             => _PROFILE_AM_URL,
                    XOBJ_DTYPE_OTHER           => _PROFILE_AM_OTHER,
                    XOBJ_DTYPE_ARRAY           => _PROFILE_AM_ARRAY,
                    XOBJ_DTYPE_UNICODE_ARRAY   => _PROFILE_AM_UNICODE_ARRAY,
                    XOBJ_DTYPE_UNICODE_TXTBOX  => _PROFILE_AM_UNICODE_TXTBOX,
                    XOBJ_DTYPE_UNICODE_TXTAREA => _PROFILE_AM_UNICODE_TXTAREA,
                    XOBJ_DTYPE_UNICODE_EMAIL   => _PROFILE_AM_UNICODE_EMAIL,
                    XOBJ_DTYPE_UNICODE_URL     => _PROFILE_AM_UNICODE_URL);

                $type_select = new XoopsFormSelect(_PROFILE_AM_VALUETYPE, 'field_valuetype', $field->getVar('field_valuetype', 'e'));
                $type_select->addOptionArray($valuetypes);
                $form->addElement($type_select);
                break;

            case 'select':
            case 'radio':
                $valuetypes = array(
                    XOBJ_DTYPE_TXTBOX          => _PROFILE_AM_TXTBOX,
                    XOBJ_DTYPE_EMAIL           => _PROFILE_AM_EMAIL,
                    XOBJ_DTYPE_INT             => _PROFILE_AM_INT,
                    XOBJ_DTYPE_FLOAT           => _PROFILE_AM_FLOAT,
                    XOBJ_DTYPE_DECIMAL         => _PROFILE_AM_DECIMAL,
                    XOBJ_DTYPE_TXTAREA         => _PROFILE_AM_TXTAREA,
                    XOBJ_DTYPE_URL             => _PROFILE_AM_URL,
                    XOBJ_DTYPE_OTHER           => _PROFILE_AM_OTHER,
                    XOBJ_DTYPE_ARRAY           => _PROFILE_AM_ARRAY,
                    XOBJ_DTYPE_UNICODE_ARRAY   => _PROFILE_AM_UNICODE_ARRAY,
                    XOBJ_DTYPE_UNICODE_TXTBOX  => _PROFILE_AM_UNICODE_TXTBOX,
                    XOBJ_DTYPE_UNICODE_TXTAREA => _PROFILE_AM_UNICODE_TXTAREA,
                    XOBJ_DTYPE_UNICODE_EMAIL   => _PROFILE_AM_UNICODE_EMAIL,
                    XOBJ_DTYPE_UNICODE_URL     => _PROFILE_AM_UNICODE_URL);

                $type_select = new XoopsFormSelect(_PROFILE_AM_VALUETYPE, 'field_valuetype', $field->getVar('field_valuetype', 'e'));
                $type_select->addOptionArray($valuetypes);
                $form->addElement($type_select);
                break;
        }

        //$form->addElement(new XoopsFormRadioYN(_PROFILE_AM_NOTNULL, 'field_notnull', $field->getVar('field_notnull', 'e') ));

        if ($field->getVar('field_type') === 'select' || $field->getVar('field_type') === 'select_multi' || $field->getVar('field_type') === 'radio' || $field->getVar('field_type') === 'checkbox') {
            $options = $field->getVar('field_options');
            if (count($options) > 0) {
                $remove_options          = new XoopsFormCheckBox(_PROFILE_AM_REMOVEOPTIONS, 'removeOptions');
                $remove_options->columns = 3;
                asort($options);
                foreach (array_keys($options) as $key) {
                    $options[$key] .= "[{$key}]";
                }
                $remove_options->addOptionArray($options);
                $form->addElement($remove_options);
            }

            $option_text = "<table  cellspacing='1'><tr><td class='width20'>" . _PROFILE_AM_KEY . '</td><td>' . _PROFILE_AM_VALUE . '</td></tr>';
            for ($i = 0; $i < 3; ++$i) {
                $option_text .= "<tr><td><input type='text' name='addOption[{$i}][key]' id='addOption[{$i}][key]' size='15' /></td><td><input type='text' name='addOption[{$i}][value]' id='addOption[{$i}][value]' size='35' /></td></tr>";
                $option_text .= "<tr height='3px'><td colspan='2'> </td></tr>";
            }
            $option_text .= '</table>';
            $form->addElement(new XoopsFormLabel(_PROFILE_AM_ADDOPTION, $option_text));
        }
    }

    if ($field->getVar('field_edit')) {
        switch ($field->getVar('field_type')) {
            case 'textbox':
            case 'textarea':
            case 'dhtml':
                $form->addElement(new XoopsFormText(_PROFILE_AM_MAXLENGTH, 'field_maxlength', 35, 35, $field->getVar('field_maxlength', 'e')));
                $form->addElement(new XoopsFormTextArea(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
                break;

            case 'checkbox':
            case 'select_multi':
                $def_value = $field->getVar('field_default', 'e') != null ? unserialize($field->getVar('field_default', 'n')) : null;
                $element   = new XoopsFormSelect(_PROFILE_AM_DEFAULT, 'field_default', $def_value, 8, true);
                $options   = $field->getVar('field_options');
                asort($options);
                // If options do not include an empty element, then add a blank option to prevent any default selection
                //                if (!in_array('', array_keys($options))) {
                if (!array_key_exists('', $options)) {
                    $element->addOption('', _NONE);
                }
                $element->addOptionArray($options);
                $form->addElement($element);
                break;

            case 'select':
            case 'radio':
                $def_value = $field->getVar('field_default', 'e') != null ? $field->getVar('field_default') : null;
                $element   = new XoopsFormSelect(_PROFILE_AM_DEFAULT, 'field_default', $def_value);
                $options   = $field->getVar('field_options');
                asort($options);
                // If options do not include an empty element, then add a blank option to prevent any default selection
                //                if (!in_array('', array_keys($options))) {
                if (!array_key_exists('', $options)) {
                    $element->addOption('', _NONE);
                }
                $element->addOptionArray($options);
                $form->addElement($element);
                break;

            case 'date':
                $form->addElement(new XoopsFormTextDateSelect(_PROFILE_AM_DEFAULT, 'field_default', 15, $field->getVar('field_default', 'e')));
                break;

            case 'longdate':
                $form->addElement(new XoopsFormTextDateSelect(_PROFILE_AM_DEFAULT, 'field_default', 15, strtotime($field->getVar('field_default', 'e'))));
                break;

            case 'datetime':
                $form->addElement(new XoopsFormDateTime(_PROFILE_AM_DEFAULT, 'field_default', 15, $field->getVar('field_default', 'e')));
                break;

            case 'yesno':
                $form->addElement(new XoopsFormRadioYN(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
                break;

            case 'timezone':
                $form->addElement(new XoopsFormSelectTimezone(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
                break;

            case 'language':
                $form->addElement(new XoopsFormSelectLang(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
                break;

            case 'group':
                $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_DEFAULT, 'field_default', true, $field->getVar('field_default', 'e')));
                break;

            case 'group_multi':
                $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_DEFAULT, 'field_default', true, unserialize($field->getVar('field_default', 'n')), 5, true));
                break;

            case 'theme':
                $form->addElement(new XoopsFormSelectTheme(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
                break;

            case 'autotext':
                $form->addElement(new XoopsFormTextArea(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
                break;
        }
    }
    /* @var $groupperm_handler XoopsGroupPermHandler  */
    $groupperm_handler = xoops_getHandler('groupperm');
    $searchable_types  = array(
        'textbox',
        'select',
        'radio',
        'yesno',
        'date',
        'datetime',
        'timezone',
        'language');
    if (in_array($field->getVar('field_type'), $searchable_types)) {
        $search_groups = $groupperm_handler->getGroupIds('profile_search', $field->getVar('field_id'), $GLOBALS['xoopsModule']->getVar('mid'));
        $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_PROF_SEARCH, 'profile_search', true, $search_groups, 5, true));
    }
    if ($field->getVar('field_edit') || $field->isNew()) {
        $editable_groups = array();
        if (!$field->isNew()) {
            //Load groups
            $editable_groups = $groupperm_handler->getGroupIds('profile_edit', $field->getVar('field_id'), $GLOBALS['xoopsModule']->getVar('mid'));
        }
        $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_PROF_EDITABLE, 'profile_edit', false, $editable_groups, 5, true));
        $form->addElement(new XoopsFormRadioYN(_PROFILE_AM_REQUIRED, 'field_required', $field->getVar('field_required', 'e')));
        $regstep_select = new XoopsFormSelect(_PROFILE_AM_PROF_REGISTER, 'step_id', $field->getVar('step_id', 'e'));
        $regstep_select->addOption(0, _NO);
        $regstep_handler = xoops_getModuleHandler('regstep');
        $regstep_select->addOptionArray($regstep_handler->getList());
        $form->addElement($regstep_select);
    }
    $form->addElement(new XoopsFormHidden('op', 'save'));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

    return $form;
}

/**
 * Get {@link XoopsThemeForm} for registering new users
 *
 * @param XoopsUser $user
 * @param           $profile
 * @param XoopsUser $user {@link XoopsUser} to register
 * @param int       $step Which step we are at
 *
 * @internal param \profileRegstep $next_step
 * @return object
 */
function profile_getRegisterForm(XoopsUser $user, $profile, $step = null)
{
    global $opkey; // should be set in register.php
    if (empty($opkey)) {
        $opkey = 'profile_opname';
    }
    $next_opname      = 'op' . mt_rand(10000, 99999);
    $_SESSION[$opkey] = $next_opname;

    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
    if (empty($GLOBALS['xoopsConfigUser'])) {
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler             = xoops_getHandler('config');
        $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    }
    $action    = $_SERVER['REQUEST_URI'];
    $step_no   = $step['step_no'];
    $use_token = $step['step_no'] > 0;// ? true : false;
    $reg_form  = new XoopsThemeForm($step['step_name'], 'regform', $action, 'post', $use_token);

    if ($step['step_desc']) {
        $reg_form->addElement(new XoopsFormLabel('', $step['step_desc']));
    }

    if ($step_no == 1) {
        //$uname_size = $GLOBALS['xoopsConfigUser']['maxuname'] < 35 ? $GLOBALS['xoopsConfigUser']['maxuname'] : 35;

        $elements[0][] = array(
            'element'  => new XoopsFormText(_US_NICKNAME, 'uname', 35, $GLOBALS['xoopsConfigUser']['maxuname'], $user->getVar('uname', 'e')),
            'required' => true);
        $weights[0][]  = 0;

        $elements[0][] = array('element' => new XoopsFormText(_US_EMAIL, 'email', 35, 255, $user->getVar('email', 'e')), 'required' => true);
        $weights[0][]  = 0;

        $elements[0][] = array('element' => new XoopsFormPassword(_US_PASSWORD, 'pass', 35, 32, ''), 'required' => true);
        $weights[0][]  = 0;

        $elements[0][] = array('element' => new XoopsFormPassword(_US_VERIFYPASS, 'vpass', 35, 32, ''), 'required' => true);
        $weights[0][]  = 0;
    }

    // Dynamic fields
    $profile_handler              = xoops_getModuleHandler('profile');
    $fields                       = $profile_handler->loadFields();
    $_SESSION['profile_required'] = array();
    foreach (array_keys($fields) as $i) {
        if ($fields[$i]->getVar('step_id') == $step['step_id']) {
            $fieldinfo['element'] = $fields[$i]->getEditElement($user, $profile);
            //assign and check (=)
            if ($fieldinfo['required'] = $fields[$i]->getVar('field_required')) {
                $_SESSION['profile_required'][$fields[$i]->getVar('field_name')] = $fields[$i]->getVar('field_title');
            }

            $key              = $fields[$i]->getVar('cat_id');
            $elements[$key][] = $fieldinfo;
            $weights[$key][]  = $fields[$i]->getVar('field_weight');
        }
    }
    ksort($elements);

    // Get categories
    $cat_handler = xoops_getModuleHandler('category');
    $categories  = $cat_handler->getObjects(null, true, false);

    foreach (array_keys($elements) as $k) {
        array_multisort($weights[$k], SORT_ASC, array_keys($elements[$k]), SORT_ASC, $elements[$k]);
        //$title = isset($categories[$k]) ? $categories[$k]['cat_title'] : _PROFILE_MA_DEFAULT;
        //$desc = isset($categories[$k]) ? $categories[$k]['cat_description'] : "";
        //$reg_form->insertBreak("<p>{$title}</p>{$desc}");
        //$reg_form->addElement(new XoopsFormLabel("<h2>".$title."</h2>", $desc), false);
        foreach (array_keys($elements[$k]) as $i) {
            $reg_form->addElement($elements[$k][$i]['element'], $elements[$k][$i]['required']);
        }
    }
    //end of Dynamic User fields

    if ($step_no == 1 && $GLOBALS['xoopsConfigUser']['reg_dispdsclmr'] != 0 && $GLOBALS['xoopsConfigUser']['reg_disclaimer'] != '') {
        $disc_tray = new XoopsFormElementTray(_US_DISCLAIMER, '<br>');
        $disc_text = new XoopsFormLabel('', "<div class=\"pad5\">" . $GLOBALS['myts']->displayTarea($GLOBALS['xoopsConfigUser']['reg_disclaimer'], 1) . '</div>');
        $disc_tray->addElement($disc_text);
        $agree_chk = new XoopsFormCheckBox('', 'agree_disc');
        $agree_chk->addOption(1, _US_IAGREE);
        $disc_tray->addElement($agree_chk);
        $reg_form->addElement($disc_tray);
    }
    global $xoopsModuleConfig;
    $useCaptchaAfterStep2 = $xoopsModuleConfig['profileCaptchaAfterStep1'] + 1;

    if ($step_no <= $useCaptchaAfterStep2) {
        $reg_form->addElement(new XoopsFormCaptcha(), true);
    }

    $reg_form->addElement(new XoopsFormHidden($next_opname, 'register'));
    $reg_form->addElement(new XoopsFormHidden('uid', $user->getVar('uid')));
    $reg_form->addElement(new XoopsFormHidden('step', $step_no));
    $reg_form->addElement(new XoopsFormButton('', 'submitButton', _SUBMIT, 'submit'));

    return $reg_form;
}

/**
 * Get {@link XoopsThemeForm} for editing a user
 *
 * @param XoopsUser           $user {@link XoopsUser} to edit
 * @param ProfileProfile|XoopsObject|null $profile
 * @param bool                $action
 *
 * @return object
 */
function profile_getUserForm(XoopsUser $user, ProfileProfile $profile = null, $action = false)
{
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    if (empty($GLOBALS['xoopsConfigUser'])) {
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler             = xoops_getHandler('config');
        $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    }

    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

    $title = $user->isNew() ? _PROFILE_AM_ADDUSER : _US_EDITPROFILE;

    $form = new XoopsThemeForm($title, 'userinfo', $action, 'post', true);
    /* @var $profile_handler ProfileProfileHandler */
    $profile_handler = xoops_getModuleHandler('profile');
    // Dynamic fields
    if (!$profile) {
        /* @var $profile_handler ProfileProfileHandler */
        $profile_handler = xoops_getModuleHandler('profile', 'profile');
        $profile         = $profile_handler->get($user->getVar('uid'));
    }
    // Get fields
    $fields = $profile_handler->loadFields();
    // Get ids of fields that can be edited
    /* @var  $gperm_handler XoopsGroupPermHandler */
    $gperm_handler   = xoops_getHandler('groupperm');
    $editable_fields = $gperm_handler->getItemIds('profile_edit', $GLOBALS['xoopsUser']->getGroups(), $GLOBALS['xoopsModule']->getVar('mid'));

    if ($user->isNew() || $GLOBALS['xoopsUser']->isAdmin()) {
        $elements[0][] = array(
            'element'  => new XoopsFormText(_US_NICKNAME, 'uname', 25, $GLOBALS['xoopsUser']->isAdmin() ? 60 : $GLOBALS['xoopsConfigUser']['maxuname'], $user->getVar('uname', 'e')),
            'required' => 1);
        $email_text    = new XoopsFormText('', 'email', 30, 60, $user->getVar('email'));
    } else {
        $elements[0][] = array('element' => new XoopsFormLabel(_US_NICKNAME, $user->getVar('uname')), 'required' => 0);
        $email_text    = new XoopsFormLabel('', $user->getVar('email'));
    }
    $email_tray = new XoopsFormElementTray(_US_EMAIL, '<br>');
    $email_tray->addElement($email_text, ($user->isNew() || $GLOBALS['xoopsUser']->isAdmin()) ? 1 : 0);
    $weights[0][]  = 0;
    $elements[0][] = array('element' => $email_tray, 'required' => 0);
    $weights[0][]  = 0;

    if ($GLOBALS['xoopsUser']->isAdmin() && $user->getVar('uid') != $GLOBALS['xoopsUser']->getVar('uid')) {
        //If the user is an admin and is editing someone else
        $pwd_text  = new XoopsFormPassword('', 'password', 10, 32);
        $pwd_text2 = new XoopsFormPassword('', 'vpass', 10, 32);
        $pwd_tray  = new XoopsFormElementTray(_US_PASSWORD . '<br>' . _US_TYPEPASSTWICE);
        $pwd_tray->addElement($pwd_text);
        $pwd_tray->addElement($pwd_text2);
        $elements[0][] = array('element' => $pwd_tray, 'required' => 0); //cannot set an element tray required
        $weights[0][]  = 0;

        $level_radio = new XoopsFormRadio(_PROFILE_MA_USERLEVEL, 'level', $user->getVar('level'));
        $level_radio->addOption(1, _PROFILE_MA_ACTIVE);
        $level_radio->addOption(0, _PROFILE_MA_INACTIVE);
        //$level_radio->addOption(-1, _PROFILE_MA_DISABLED);
        $elements[0][] = array('element' => $level_radio, 'required' => 0);
        $weights[0][]  = 0;
    }

    $elements[0][] = array('element' => new XoopsFormHidden('uid', $user->getVar('uid')), 'required' => 0);
    $weights[0][]  = 0;
    $elements[0][] = array('element' => new XoopsFormHidden('op', 'save'), 'required' => 0);
    $weights[0][]  = 0;

    $cat_handler    = xoops_getModuleHandler('category');
    $categories     = array();
    $all_categories = $cat_handler->getObjects(null, true, false);
    $count_fields   = count($fields);

    foreach (array_keys($fields) as $i) {
        if (in_array($fields[$i]->getVar('field_id'), $editable_fields)) {
            // Set default value for user fields if available
            if ($user->isNew()) {
                $default = $fields[$i]->getVar('field_default');
                if ($default !== '' && $default !== null) {
                    $user->setVar($fields[$i]->getVar('field_name'), $default);
                }
            }

            if ($profile->getVar($fields[$i]->getVar('field_name'), 'n') === null) {
                $default = $fields[$i]->getVar('field_default', 'n');
                $profile->setVar($fields[$i]->getVar('field_name'), $default);
            }

            $fieldinfo['element']  = $fields[$i]->getEditElement($user, $profile);
            $fieldinfo['required'] = $fields[$i]->getVar('field_required');

            $key              = @$all_categories[$fields[$i]->getVar('cat_id')]['cat_weight'] * $count_fields + $fields[$i]->getVar('cat_id');
            $elements[$key][] = $fieldinfo;
            $weights[$key][]  = $fields[$i]->getVar('field_weight');
            $categories[$key] = @$all_categories[$fields[$i]->getVar('cat_id')];
        }
    }

    if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser']->isAdmin()) {
        xoops_loadLanguage('admin', 'profile');
        /* @var  $gperm_handler XoopsGroupPermHandler */
        $gperm_handler = xoops_getHandler('groupperm');
        //If user has admin rights on groups
        include_once $GLOBALS['xoops']->path('modules/system/constants.php');
        if ($gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_GROUP, $GLOBALS['xoopsUser']->getGroups(), 1)) {
            //add group selection
            $group_select  = new XoopsFormSelectGroup(_US_GROUPS, 'groups', false, $user->getGroups(), 5, true);
            $elements[0][] = array('element' => $group_select, 'required' => 0);
            //set as latest;
            $weights[0][] = $count_fields + 1;
        }
    }

    ksort($elements);
    foreach (array_keys($elements) as $k) {
        array_multisort($weights[$k], SORT_ASC, array_keys($elements[$k]), SORT_ASC, $elements[$k]);
        $title = isset($categories[$k]) ? $categories[$k]['cat_title'] : _PROFILE_MA_DEFAULT;
        $desc  = isset($categories[$k]) ? $categories[$k]['cat_description'] : '';
        $form->addElement(new XoopsFormLabel("<h3>{$title}</h3>", $desc), false);
        foreach (array_keys($elements[$k]) as $i) {
            $form->addElement($elements[$k][$i]['element'], $elements[$k][$i]['required']);
        }
    }

    $form->addElement(new XoopsFormHidden('uid', $user->getVar('uid')));
    $form->addElement(new XoopsFormButton('', 'submit', _US_SAVECHANGES, 'submit'));

    return $form;
}

/**
 * Get {@link XoopsThemeForm} for editing a step
 *
 * @param ProfileRegstep|null $step {@link ProfileRegstep} to edit
 * @param bool                $action
 *
 * @return object
 */
function profile_getStepForm(ProfileRegstep $step = null, $action = false)
{
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    if (empty($GLOBALS['xoopsConfigUser'])) {
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler             = xoops_getHandler('config');
        $GLOBALS['xoopsConfigUser'] = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    }
    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

    $form = new XoopsThemeForm(_PROFILE_AM_STEP, 'stepform', 'step.php', 'post', true);

    if (!$step->isNew()) {
        $form->addElement(new XoopsFormHidden('id', $step->getVar('step_id')));
    }
    $form->addElement(new XoopsFormHidden('op', 'save'));
    $form->addElement(new XoopsFormText(_PROFILE_AM_STEPNAME, 'step_name', 25, 255, $step->getVar('step_name', 'e')));
    $form->addElement(new XoopsFormText(_PROFILE_AM_STEPINTRO, 'step_desc', 25, 255, $step->getVar('step_desc', 'e')));
    $form->addElement(new XoopsFormText(_PROFILE_AM_STEPORDER, 'step_order', 10, 10, $step->getVar('step_order', 'e')));
    $form->addElement(new XoopsFormRadioYN(_PROFILE_AM_STEPSAVE, 'step_save', $step->getVar('step_save', 'e')));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

    return $form;
}
