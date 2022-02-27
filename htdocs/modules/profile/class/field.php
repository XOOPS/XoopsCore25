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
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

// defined('XOOPS_ROOT_PATH') || exit("XOOPS root path not defined");

/**
 * @package             kernel
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class ProfileField extends XoopsObject
{
    /**
     *
     */
    public function __construct()
    {
        $this->initVar('field_id', XOBJ_DTYPE_INT, null);
        $this->initVar('cat_id', XOBJ_DTYPE_INT, null, true);
        $this->initVar('field_type', XOBJ_DTYPE_TXTBOX);
        $this->initVar('field_valuetype', XOBJ_DTYPE_INT, null, true);
        $this->initVar('field_name', XOBJ_DTYPE_TXTBOX, null, true);
        $this->initVar('field_title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('field_description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('field_required', XOBJ_DTYPE_INT, 0); //0 = no, 1 = yes
        $this->initVar('field_maxlength', XOBJ_DTYPE_INT, 0);
        $this->initVar('field_weight', XOBJ_DTYPE_INT, 0);
        $this->initVar('field_default', XOBJ_DTYPE_TXTAREA, '');
        $this->initVar('field_notnull', XOBJ_DTYPE_INT, 1);
        $this->initVar('field_edit', XOBJ_DTYPE_INT, 0);
        $this->initVar('field_show', XOBJ_DTYPE_INT, 0);
        $this->initVar('field_config', XOBJ_DTYPE_INT, 0);
        $this->initVar('field_options', XOBJ_DTYPE_ARRAY, array());
        $this->initVar('step_id', XOBJ_DTYPE_INT, 0);
    }

    /**
     * Extra treatment dealing with non latin encoding
     * Tricky solution
     * @param string $key
     * @param mixed  $value
     * @param bool   $not_gpc
     */
    public function setVar($key, $value, $not_gpc = false)
    {
        if ($key === 'field_options' && is_array($value)) {
            foreach (array_keys($value) as $idx) {
                $value[$idx] = base64_encode($value[$idx]);
            }
        }
        parent::setVar($key, $value, $not_gpc);
    }

    /**
     * @param string $key
     * @param string $format
     *
     * @return mixed
     */
    public function getVar($key, $format = 's')
    {
        $value = parent::getVar($key, $format);
        if ($key === 'field_options' && !empty($value)) {
            foreach (array_keys($value) as $idx) {
                $value[$idx] = base64_decode($value[$idx]);
            }
        }

        return $value;
    }

    /**
     * Returns a {@link XoopsFormElement} for editing the value of this field
     *
     * @param XoopsUser      $user    {@link XoopsUser} object to edit the value of
     * @param ProfileProfile $profile {@link ProfileProfile} object to edit the value of
     *
     * @return XoopsFormElement
     **/
    public function getEditElement($user, $profile)
    {
        $value = in_array($this->getVar('field_name'), $this->getUserVars()) ? $user->getVar($this->getVar('field_name'), 'e') : $profile->getVar($this->getVar('field_name'), 'e');

        $caption = $this->getVar('field_title');
        $caption = defined($caption) ? constant($caption) : $caption;
        $name    = $this->getVar('field_name', 'e');
        $options = $this->getVar('field_options');
        if (is_array($options)) {
            //asort($options);

            foreach (array_keys($options) as $key) {
                $optval = defined($options[$key]) ? constant($options[$key]) : $options[$key];
                $optkey = defined($key) ? constant($key) : $key;
                unset($options[$key]);
                $options[$optkey] = $optval;
            }
        }
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        switch ($this->getVar('field_type')) {
            default:
            case 'autotext':
                //autotext is not for editing
                $element = new XoopsFormLabel($caption, $this->getOutputValue($user, $profile));
                break;

            case 'textbox':
                $element = new XoopsFormText($caption, $name, 35, $this->getVar('field_maxlength'), $value);
                break;

            case 'textarea':
                $element = new XoopsFormTextArea($caption, $name, $value, 4, 30);
                break;

            case 'dhtml':
                $element = new XoopsFormDhtmlTextArea($caption, $name, $value, 10, 30);
                break;

            case 'select':
                $element = new XoopsFormSelect($caption, $name, $value);
                // If options do not include an empty element, then add a blank option to prevent any default selection
//                if (!in_array('', array_keys($options))) {
                if (!array_key_exists('', $options)) {
                    $element->addOption('', _NONE);

                    $eltmsg                          = empty($caption) ? sprintf(_FORM_ENTER, $name) : sprintf(_FORM_ENTER, $caption);
                    $eltmsg                          = str_replace('"', '\"', stripslashes($eltmsg));
                    $element->customValidationCode[] = "\nvar hasSelected = false; var selectBox = myform.{$name};" . "for (i = 0; i < selectBox.options.length; i++) { if (selectBox.options[i].selected == true && selectBox.options[i].value != '') { hasSelected = true; break; } }" . "if (!hasSelected) { window.alert(\"{$eltmsg}\"); selectBox.focus(); return false; }";
                }
                $element->addOptionArray($options);
                break;

            case 'select_multi':
                $element = new XoopsFormSelect($caption, $name, $value, 5, true);
                $element->addOptionArray($options);
                break;

            case 'radio':
                $element = new XoopsFormRadio($caption, $name, $value);
                $element->addOptionArray($options);
                break;

            case 'checkbox':
                $element = new XoopsFormCheckBox($caption, $name, $value);
                $element->addOptionArray($options);
                break;

            case 'yesno':
                $element = new XoopsFormRadioYN($caption, $name, $value);
                break;

            case 'group':
                $element = new XoopsFormSelectGroup($caption, $name, true, $value);
                break;

            case 'group_multi':
                $element = new XoopsFormSelectGroup($caption, $name, true, $value, 5, true);
                break;

            case 'language':
                $element = new XoopsFormSelectLang($caption, $name, $value);
                break;

            case 'date':
                $element = new XoopsFormTextDateSelect($caption, $name, 15, $value);
                break;

            case 'longdate':
                $element = new XoopsFormTextDateSelect($caption, $name, 15, str_replace('-', '/', $value));
                break;

            case 'datetime':
                $element = new XoopsFormDateTime($caption, $name, 15, $value);
                break;

            case 'timezone':
                $element = new XoopsFormSelectTimezone($caption, $name, $value);
                //$element->setExtra("style='width: 280px;'");
                break;

            case 'rank':
                $element = new XoopsFormSelect($caption, $name, $value);

                include_once $GLOBALS['xoops']->path('class/xoopslists.php');
                $ranks = XoopsLists::getUserRankList();
                $element->addOption('0', '--------------');
                $element->addOptionArray($ranks);
                break;

            case 'theme':
                $element = new XoopsFormSelectTheme($caption, $name, $value, 1, true);
                break;
        }
        if ($this->getVar('field_description') !== '') {
            $element->setDescription($this->getVar('field_description'));
        }

        return $element;
    }

    /**
     * Returns a value for output of this field
     *
     * @param XoopsUser      $user    {@link XoopsUser} object to get the value of
     * @param ProfileProfile $profile object to get the value of
     *
     * @return mixed
     **/
    public function getOutputValue($user, $profile)
    {
        xoops_loadLanguage('modinfo', 'profile');

        $value = in_array($this->getVar('field_name'), $this->getUserVars()) ? $user->getVar($this->getVar('field_name')) : $profile->getVar($this->getVar('field_name'));

        switch ($this->getVar('field_type')) {
            default:
            case 'textbox':
                $value = is_array($value) ? $value[0] : $value;
                if ($this->getVar('field_name') === 'url' && $value !== '') {
                    return '<a href="' . formatURL($value) . '" rel="external">' . $value . '</a>';
                } else {
                    return $value;
                }

            case 'textarea':
            case 'dhtml':
            case 'theme':
            case 'language':
                return $value;

            case 'select':
            case 'radio':
                $value = is_array($value) ? $value[0] : $value;
                $options = $this->getVar('field_options');
                if (isset($options[$value])) {
                    $value = htmlspecialchars(defined($options[$value]) ? constant($options[$value]) : $options[$value], ENT_QUOTES | ENT_HTML5);
                } else {
                    $value = '';
                }

                return $value;

            case 'select_multi':
            case 'checkbox':
                $options = $this->getVar('field_options');
                $ret     = array();
                if (count($options) > 0) {
                    foreach (array_keys($options) as $key) {
                        if (in_array($key, $value)) {
                            $ret[$key] = htmlspecialchars(defined($options[$key]) ? constant($options[$key]) : $options[$key], ENT_QUOTES | ENT_HTML5);
                        }
                    }
                }

                return $ret;

            case 'group':
                /** @var XoopsMemberHandler $member_handler */
                $member_handler = xoops_getHandler('member');
                $options        = $member_handler->getGroupList();
                $ret            = isset($options[$value]) ? $options[$value] : '';

                return $ret;

            case 'group_multi':
                /** @var XoopsMemberHandler $member_handler */
                $member_handler = xoops_getHandler('member');
                $options        = $member_handler->getGroupList();
                $ret            = array();
                foreach (array_keys($options) as $key) {
                    if (in_array($key, $value)) {
                        $ret[$key] = htmlspecialchars($options[$key], ENT_QUOTES | ENT_HTML5);
                    }
                }

                return $ret;

            case 'longdate':
                //return YYYY/MM/DD format - not optimal as it is not using local date format, but how do we do that
                //when we cannot convert it to a UNIX timestamp?
                return str_replace('-', '/', $value);

            case 'date':
                return formatTimestamp($value, 's');

            case 'datetime':
                if (!empty($value)) {
                    return formatTimestamp($value, 'm');
                } else {
                    return $value = _PROFILE_MI_NEVER_LOGGED_IN;
                }

            case 'autotext':
                $value = $user->getVar($this->getVar('field_name'), 'n'); //autotext can have HTML in it
                $value = str_replace('{X_UID}', $user->getVar('uid'), $value);
                $value = str_replace('{X_URL}', XOOPS_URL, $value);
                $value = str_replace('{X_UNAME}', $user->getVar('uname'), $value);

                return $value;

            case 'rank':
                $userrank       = $user->rank();
                $user_rankimage = '';
                if (isset($userrank['image']) && $userrank['image'] !== '') {
                    $user_rankimage = '<img src="' . XOOPS_UPLOAD_URL . '/' . $userrank['image'] . '" alt="' . $userrank['title'] . '" /><br>';
                }

                return $user_rankimage . $userrank['title'];

            case 'yesno':
                return $value ? _YES : _NO;

            case 'timezone':
                include_once $GLOBALS['xoops']->path('class/xoopslists.php');
                $timezones = XoopsLists::getTimeZoneList();
                $value     = empty($value) ? '0' : (string)$value;

                return $timezones[str_replace('.0', '', $value)];
        }
    }

    /**
     * Returns a value ready to be saved in the database
     *
     * @param mixed $value Value to format
     *
     * @return mixed
     */
    public function getValueForSave($value)
    {
        switch ($this->getVar('field_type')) {
            default:
            case 'textbox':
            case 'textarea':
            case 'dhtml':
            case 'yesno':
            case 'timezone':
            case 'theme':
            case 'language':
            case 'select':
            case 'radio':
            case 'select_multi':
            case 'group':
            case 'group_multi':
            case 'longdate':
                return $value;

            case 'checkbox':
                return (array)$value;

            case 'date':
                if ($value !== '') {
                    return strtotime($value);
                }

                return $value;

            case 'datetime':
                if (!empty($value)) {
                    return strtotime($value['date']) + (int)$value['time'];
                }

                return $value;
        }
    }

    /**
     * Get names of user variables
     *
     * @return array
     */
    public function getUserVars()
    {
        /** @var ProfileProfileHandler $profile_handler */
        $profile_handler = xoops_getModuleHandler('profile', 'profile');

        return $profile_handler->getUserVars();
    }
}

/**
 * @package             kernel
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class ProfileFieldHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'profile_field', 'profilefield', 'field_id', 'field_title');
    }

    /**
     * Read field information from cached storage
     *
     * @param bool $force_update read fields from database and not cached storage
     *
     * @return array
     */
    public function loadFields($force_update = false)
    {
        static $fields = array();
        if (!empty($force_update) || count($fields) === 0) {
            $this->table_link = $this->db->prefix('profile_category'); //mb TODO: this is not used. Remove ?
            $criteria         = new Criteria('o.field_id', 0, '!=');
            $criteria->setSort('l.cat_weight ASC, o.field_weight');
            $field_objs =& $this->getByLink($criteria, array('o.*'), true, 'cat_id', 'cat_id');
            foreach (array_keys($field_objs) as $i) {
                $fields[$field_objs[$i]->getVar('field_name')] = $field_objs[$i];
            }
        }

        return $fields;
    }

    /**
     * save a profile field in the database
     *
     * @param XoopsObject|ProfileField $object reference to the object
     * @param bool                     $force whether to force the query execution despite security settings
     *
     * @internal param bool $checkObject check if the object is dirty and clean the attributes
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(XoopsObject $object, $force = false)
    {
        if (!($object instanceof $this->className)) {
            return false;
        }
         /** @var ProfileProfileHandler $profile_handler */
        $profile_handler = xoops_getModuleHandler('profile', 'profile');
        $object->setVar('field_name', str_replace(' ', '_', $object->getVar('field_name')));
        $object->cleanVars();
        $defaultstring = '';
        switch ($object->getVar('field_type')) {
            case 'datetime':
            case 'date':
                $object->setVar('field_valuetype', XOBJ_DTYPE_INT);
                $object->setVar('field_maxlength', 10);
                break;

            case 'longdate':
                $object->setVar('field_valuetype', XOBJ_DTYPE_MTIME);
                break;

            case 'yesno':
                $object->setVar('field_valuetype', XOBJ_DTYPE_INT);
                $object->setVar('field_maxlength', 1);
                break;

            case 'textbox':
                if ($object->getVar('field_valuetype') !== XOBJ_DTYPE_INT) {
                    $object->setVar('field_valuetype', XOBJ_DTYPE_TXTBOX);
                }
                break;

            case 'autotext':
                if ($object->getVar('field_valuetype') !== XOBJ_DTYPE_INT) {
                    $object->setVar('field_valuetype', XOBJ_DTYPE_TXTAREA);
                }
                break;

            case 'group_multi':
            case 'select_multi':
            case 'checkbox':
                $object->setVar('field_valuetype', XOBJ_DTYPE_ARRAY);
                break;

            case 'language':
            case 'timezone':
            case 'theme':
                $object->setVar('field_valuetype', XOBJ_DTYPE_TXTBOX);
                break;

            case 'dhtml':
            case 'textarea':
                $object->setVar('field_valuetype', XOBJ_DTYPE_TXTAREA);
                break;
        }

        if ($object->getVar('field_valuetype') === '') {
            $object->setVar('field_valuetype', XOBJ_DTYPE_TXTBOX);
        }

        if ((!in_array($object->getVar('field_name'), $this->getUserVars())) && isset($_REQUEST['field_required'])) {
            if ($object->isNew()) {
                //add column to table
                $changetype = 'ADD';
            } else {
                //update column information
                $changetype = 'MODIFY COLUMN';
            }
            $maxlengthstring = $object->getVar('field_maxlength') > 0 ? '(' . $object->getVar('field_maxlength') . ')' : '';

            //set type
            switch ($object->getVar('field_valuetype')) {
                default:
                case XOBJ_DTYPE_ARRAY:
                case XOBJ_DTYPE_UNICODE_ARRAY:
                    $type = 'mediumtext';
                    $maxlengthstring = '';
                    break;
                case XOBJ_DTYPE_UNICODE_EMAIL:
                case XOBJ_DTYPE_UNICODE_TXTBOX:
                case XOBJ_DTYPE_UNICODE_URL:
                case XOBJ_DTYPE_EMAIL:
                case XOBJ_DTYPE_TXTBOX:
                case XOBJ_DTYPE_URL:
                    $type = 'varchar';
                    // varchars must have a maxlength
                    if (!$maxlengthstring) {
                        //so set it to max if maxlength is not set - or should it fail?
                        $maxlengthstring = '(255)';
                        $object->setVar('field_maxlength', 255);
                    }
                    break;

                case XOBJ_DTYPE_INT:
                    $type = 'int';
                    break;

                case XOBJ_DTYPE_DECIMAL:
                    $type = 'decimal(14,6)';
                    break;

                case XOBJ_DTYPE_FLOAT:
                    $type = 'float(15,9)';
                    break;

                case XOBJ_DTYPE_OTHER:
                case XOBJ_DTYPE_UNICODE_TXTAREA:
                case XOBJ_DTYPE_TXTAREA:
                    $type            = 'text';
                    $maxlengthstring = '';
                    break;

                case XOBJ_DTYPE_MTIME:
                    $type            = 'date';
                    $maxlengthstring = '';
                    break;
            }

            $sql    = 'ALTER TABLE `' . $profile_handler->table . '` ' . $changetype . ' `' . $object->cleanVars['field_name'] . '` ' . $type . $maxlengthstring . ' NULL';
            $result = $force ? $this->db->queryF($sql) : $this->db->query($sql);
            if (!$result) {
                $object->setErrors($this->db->error());
                return false;
            }
        }

        //change this to also update the cached field information storage
        $object->setDirty();
        if (!parent::insert($object, $force)) {
            return false;
        }

        return $object->getVar('field_id');
    }

    /**
     * delete a profile field from the database
     *
     * @param XoopsObject|ProfileField $object reference to the object to delete
     * @param bool   $force
     * @return bool FALSE if failed.
     **/
    public function delete(XoopsObject $object, $force = false)
    {
        if (!($object instanceof $this->className)) {
            return false;
        }
         /** @var ProfileProfileHandler $profile_handler */
        $profile_handler = xoops_getModuleHandler('profile', 'profile');
        // remove column from table
        $sql = 'ALTER TABLE ' . $profile_handler->table . ' DROP `' . $object->getVar('field_name', 'n') . '`';
        if ($this->db->query($sql)) {
            //change this to update the cached field information storage
            if (!parent::delete($object, $force)) {
                return false;
            }

            if ($object->getVar('field_show') || $object->getVar('field_edit')) {
                /** @var XoopsModuleHandler $module_handler */
                $module_handler = xoops_getHandler('module');
                $profile_module = $module_handler->getByDirname('profile');
                if (is_object($profile_module)) {
                    // Remove group permissions
                    /** @var XoopsGroupPermHandler $groupperm_handler */
                    $groupperm_handler = xoops_getHandler('groupperm');
                    $criteria          = new CriteriaCompo(new Criteria('gperm_modid', $profile_module->getVar('mid')));
                    $criteria->add(new Criteria('gperm_itemid', $object->getVar('field_id')));

                    return $groupperm_handler->deleteAll($criteria);
                }
            }
        }

        return false;
    }

    /**
     * Get array of standard variable names (user table)
     *
     * @return array
     */
    public function getUserVars()
    {
        return array(
            'uid',
            'uname',
            'name',
            'email',
            'url',
            'user_avatar',
            'user_regdate',
            'user_icq',
            'user_from',
            'user_sig',
            'user_viewemail',
            'actkey',
            'user_aim',
            'user_yim',
            'user_msnm',
            'pass',
            'posts',
            'attachsig',
            'rank',
            'level',
            'theme',
            'timezone_offset',
            'last_login',
            'umode',
            'uorder',
            'notify_method',
            'notify_mode',
            'user_occ',
            'bio',
            'user_intrest',
            'user_mailok',
        );
    }
}
