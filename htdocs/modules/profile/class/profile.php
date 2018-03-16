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
 * @package             kernel
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class ProfileProfile extends XoopsObject
{
    /**
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->initVar('profile_id', XOBJ_DTYPE_INT, null, true);
        $this->init($fields);
    }

    /**
     * Initiate variables
     * @param array $fields field information array of {@link XoopsProfileField} objects
     */
    public function init($fields)
    {
        if (is_array($fields) && count($fields) > 0) {
            foreach (array_keys($fields) as $key) {
                $this->initVar($key, $fields[$key]->getVar('field_valuetype'), $fields[$key]->getVar('field_default', 'n'), $fields[$key]->getVar('field_required'), $fields[$key]->getVar('field_maxlength'));
            }
        }
    }
}

/**
 * @package             kernel
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class ProfileProfileHandler extends XoopsPersistableObjectHandler
{
    /**
     * holds reference to {@link profileFieldHandler} object
     */
    public $_fHandler;

    /**
     * Array of {@link XoopsProfileField} objects
     * @var array
     */
    public $_fields = array();

    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'profile_profile', 'profileprofile', 'profile_id');
        $this->_fHandler = xoops_getModuleHandler('field', 'profile');
    }

    /**
     * create a new {@link ProfileProfile}
     *
     * @param bool $isNew Flag the new objects as "new"?
     *
     * @return object {@link ProfileProfile}
     */
    public function create($isNew = true)
    {
        $obj          = new $this->className($this->loadFields());
        $obj->handler = $this;
        if ($isNew === true) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * Get a ProfileProfile object for a user id.
     *
     * We will create an empty profile if none exists. This behavior allows user objects
     * created outside of profile to be edited correctly in the profile module.
     *
     * @param integer|null  $uid
     * @param string[]|null $fields array of field names to fetch, null for all
     *
     * @return object {@link ProfileProfile}
     *
     * @internal This was get($uid, $createOnFailure = true). No callers found using the extra parameter.
     * @internal Modified to match parent signature.
     */
    public function get($uid = null, $fields = null)
    {
        $obj = parent::get($uid, $fields);
        if (!is_object($obj)) {
            $obj = $this->create();
        }

        return $obj;
    }

    /**
     * Create new {@link ProfileField} object
     *
     * @param bool $isNew
     *
     * @return ProfileField
     */
    public function createField($isNew = true)
    {
        $return = $this->_fHandler->create($isNew);

        return $return;
    }

    /**
     * Load field information
     *
     * @return array
     */
    public function loadFields()
    {
        if (count($this->_fields) == 0) {
            $this->_fields = $this->_fHandler->loadFields();
        }

        return $this->_fields;
    }

    /**
     * Fetch fields
     *
     * @param CriteriaElement $criteria  {@link CriteriaElement} object
     * @param bool            $id_as_key return array with field IDs as key?
     * @param bool            $as_object return array of objects?
     *
     * @return array
     **/
    public function getFields(CriteriaElement $criteria, $id_as_key = true, $as_object = true)
    {
        return $this->_fHandler->getObjects($criteria, $id_as_key, $as_object);
    }

    /**
     * Insert a field in the database
     *
     * @param ProfileField $field
     * @param bool         $force
     *
     * @return bool
     */
    public function insertField(ProfileField $field, $force = false)
    {
        return $this->_fHandler->insert($field, $force);
    }

    /**
     * Delete a field from the database
     *
     * @param ProfileField $field
     * @param bool         $force
     *
     * @return bool
     */
    public function deleteField(ProfileField $field, $force = false)
    {
        return $this->_fHandler->delete($field, $force);
    }

    /**
     * Save a new field in the database
     *
     * @param array $vars array of variables, taken from $module->loadInfo('profile')['field']
     * @param int   $weight
     *
     * @internal param int $categoryid ID of the category to add it to
     * @internal param int $type valuetype of the field
     * @internal param int $moduleid ID of the module, this field belongs to
     * @return string
     */
    public function saveField($vars, $weight = 0)
    {
        $field = $this->createField();
        $field->setVar('field_name', $vars['name']);
        $field->setVar('field_valuetype', $vars['valuetype']);
        $field->setVar('field_type', $vars['type']);
        $field->setVar('field_weight', $weight);
        if (isset($vars['title'])) {
            $field->setVar('field_title', $vars['title']);
        }
        if (isset($vars['description'])) {
            $field->setVar('field_description', $vars['description']);
        }
        if (isset($vars['required'])) {
            $field->setVar('field_required', $vars['required']); //0 = no, 1 = yes
        }
        if (isset($vars['maxlength'])) {
            $field->setVar('field_maxlength', $vars['maxlength']);
        }
        if (isset($vars['default'])) {
            $field->setVar('field_default', $vars['default']);
        }
        if (isset($vars['notnull'])) {
            $field->setVar('field_notnull', $vars['notnull']);
        }
        if (isset($vars['show'])) {
            $field->setVar('field_show', $vars['show']);
        }
        if (isset($vars['edit'])) {
            $field->setVar('field_edit', $vars['edit']);
        }
        if (isset($vars['config'])) {
            $field->setVar('field_config', $vars['config']);
        }
        if (isset($vars['options'])) {
            $field->setVar('field_options', $vars['options']);
        } else {
            $field->setVar('field_options', array());
        }
        if ($this->insertField($field)) {
            $msg = '&nbsp;&nbsp;Field <strong>' . $vars['name'] . '</strong> added to the database';
        } else {
            $msg = '&nbsp;&nbsp;<span class="red">ERROR: Could not insert field <strong>' . $vars['name'] . '</strong> into the database. ' . implode(' ', $field->getErrors()) . $this->db->error() . '</span>';
        }
        unset($field);

        return $msg;
    }

    /**
     * insert a new object in the database
     *
     * @param XoopsObject|ProfileProfile $obj   reference to the object
     * @param bool                       $force whether to force the query execution despite security settings
     *
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(XoopsObject $obj, $force = false)
    {
        if (!($obj instanceof $this->className)) {
            return false;
        }
        $uservars = $this->getUserVars();
        foreach ($uservars as $var) {
            unset($obj->vars[$var]);
        }
        if (count($obj->vars) == 0) {
            return true;
        }

        return parent::insert($obj, $force);
    }

    /**
     * Get array of standard variable names (user table)
     *
     * @return array
     */
    public function getUserVars()
    {
        return $this->_fHandler->getUserVars();
    }

    /**
     * Search profiles and users
     *
     * @param CriteriaElement $criteria   CriteriaElement
     * @param array           $searchvars Fields to be fetched
     * @param array           $groups     for Usergroups is selected (only admin!)
     *
     * @return array
     */
    public function search(CriteriaElement $criteria, $searchvars = array(), $groups = null)
    {
        $uservars = $this->getUserVars();

        $searchvars_user    = array_intersect($searchvars, $uservars);
        $searchvars_profile = array_diff($searchvars, $uservars);
        $sv                 = array('u.uid, u.uname, u.email, u.user_viewemail');
        if (!empty($searchvars_user)) {
            $sv[0] .= ',u.' . implode(', u.', $searchvars_user);
        }
        if (!empty($searchvars_profile)) {
            $sv[] = 'p.' . implode(', p.', $searchvars_profile);
        }

        $sql_select = 'SELECT ' . (empty($searchvars) ? 'u.*, p.*' : implode(', ', $sv));
        $sql_from   = ' FROM ' . $this->db->prefix('users') . ' AS u LEFT JOIN ' . $this->table . ' AS p ON u.uid=p.profile_id' . (empty($groups) ? '' : ' LEFT JOIN ' . $this->db->prefix('groups_users_link') . ' AS g ON u.uid=g.uid');
        $sql_clause = ' WHERE 1=1';
        $sql_order  = '';

        $limit = $start = 0;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql_clause .= ' AND ' . $criteria->render();
            if ($criteria->getSort() !== '') {
                $sql_order = ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        if (!empty($groups)) {
            $sql_clause .= ' AND g.groupid IN (' . implode(', ', $groups) . ')';
        }

        $sql_users = $sql_select . $sql_from . $sql_clause . $sql_order;
        $result    = $this->db->query($sql_users, $limit, $start);

        if (!$result) {
            return array(array(), array(), 0);
        }
        $user_handler = xoops_getHandler('user');
        $uservars     = $this->getUserVars();
        $users        = array();
        $profiles     = array();
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $profile = $this->create(false);
            $user    = $user_handler->create(false);

            foreach ($myrow as $name => $value) {
                if (in_array($name, $uservars)) {
                    $user->assignVar($name, $value);
                } else {
                    $profile->assignVar($name, $value);
                }
            }
            $profiles[$myrow['uid']] = $profile;
            $users[$myrow['uid']]    = $user;
        }

        $count = count($users);
        if ((!empty($limit) && $count >= $limit) || !empty($start)) {
            $sql_count = 'SELECT COUNT(*)' . $sql_from . $sql_clause;
            $result    = $this->db->query($sql_count);
            list($count) = $this->db->fetchRow($result);
        }

        return array($users, $profiles, (int)$count);
    }
}
