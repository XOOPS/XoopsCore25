<?php
/**
 * XOOPS Kernel Class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @since           2.0.0
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * A Module
 *
 * @package kernel
 * @author Kazumi Ono <onokazu@xoops.org>
 */
class XoopsModule extends XoopsObject
{
    /**
     *
     * @var string
     */
    var $modinfo;
    /**
     *
     * @var string
     */
    var $adminmenu;
    /**
    *
    * @var array
    */
    var $_msg;

    /**
     * Constructor
     */
    function XoopsModule()
    {
        $this->XoopsObject();
        $this->initVar('mid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        $this->initVar('version', XOBJ_DTYPE_INT, 100, false);
        $this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('isactive', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dirname', XOBJ_DTYPE_OTHER, null, true);
        $this->initVar('hasmain', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasadmin', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hassearch', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasconfig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hascomments', XOBJ_DTYPE_INT, 0, false);
        // RMV-NOTIFY
        $this->initVar('hasnotification', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Load module info
     *
     * @param string  $dirname Directory Name
     * @param boolean $verbose
     */
    function loadInfoAsVar($dirname, $verbose = true)
    {
            $dirname = basename($dirname);
        if (!isset($this->modinfo)) {
            $this->loadInfo($dirname, $verbose);
        }
        $this->setVar('name', $this->modinfo['name'], true);
        $this->setVar('version', intval(100 * ($this->modinfo['version'] + 0.001)), true);
        $this->setVar('dirname', $this->modinfo['dirname'], true);
        $hasmain = (isset($this->modinfo['hasMain']) && $this->modinfo['hasMain'] == 1) ? 1 : 0;
        $hasadmin = (isset($this->modinfo['hasAdmin']) && $this->modinfo['hasAdmin'] == 1) ? 1 : 0;
        $hassearch = (isset($this->modinfo['hasSearch']) && $this->modinfo['hasSearch'] == 1) ? 1 : 0;
        $hasconfig = ((isset($this->modinfo['config']) && is_array($this->modinfo['config'])) || ! empty($this->modinfo['hasComments'])) ? 1 : 0;
        $hascomments = (isset($this->modinfo['hasComments']) && $this->modinfo['hasComments'] == 1) ? 1 : 0;
        // RMV-NOTIFY
        $hasnotification = (isset($this->modinfo['hasNotification']) && $this->modinfo['hasNotification'] == 1) ? 1 : 0;
        $this->setVar('hasmain', $hasmain);
        $this->setVar('hasadmin', $hasadmin);
        $this->setVar('hassearch', $hassearch);
        $this->setVar('hasconfig', $hasconfig);
        $this->setVar('hascomments', $hascomments);
        // RMV-NOTIFY
        $this->setVar('hasnotification', $hasnotification);
    }

    /**
     * add a message
     *
     * @param string $str message to add
     * @access public
     */
    function setMessage($str)
    {
        $this->_msg[] = trim($str);
    }

    /**
     * return the messages for this object as an array
     *
     * @return array an array of messages
     * @access public
     */
    function getMessages()
    {
        return $this->_msg;
    }

    /**
     * Set module info
     *
     * @param  string $name
     * @param  mix    $value
     * @return bool
     **/
    function setInfo($name, $value)
    {
        if (empty($name)) {
            $this->modinfo = $value;
        } else {
            $this->modinfo[$name] = $value;
        }

        return true;
    }

    /**
     * Get module info
     *
     * @param  string $name
     * @return array  |string    Array of module information.
     *                     If {@link $name} is set, returns a single module information item as string.
     */
    function &getInfo($name = null)
    {
        if (!isset($this->modinfo)) {
            $this->loadInfo($this->getVar('dirname'));
        }
        if (isset($name)) {
            if (isset($this->modinfo[$name])) {
                return $this->modinfo[$name];
            }
            $return = false;

            return $return;
        }

        return $this->modinfo;
    }

    /**
     * Get a link to the modules main page
     *
     * @return string FALSE on fail
     */
    function mainLink()
    {
        if ($this->getVar('hasmain') == 1) {
            $ret = '<a href="' . XOOPS_URL . '/modules/' . $this->getVar('dirname') . '/">' . $this->getVar('name') . '</a>';

            return $ret;
        }

        return false;
    }

    /**
     * Get links to the subpages
     *
     * @return string
     */
    function subLink()
    {
        $ret = array();
        if ($this->getInfo('sub') && is_array($this->getInfo('sub'))) {
            foreach ($this->getInfo('sub') as $submenu) {
                $ret[] = array(
                    'name' => $submenu['name'] ,
                    'url' => $submenu['url']);
            }
        }

        return $ret;
    }

    /**
     * Load the admin menu for the module
     */
    function loadAdminMenu()
    {
        if ($this->getInfo('adminmenu') && $this->getInfo('adminmenu') != '' && file_exists(XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/' . $this->getInfo('adminmenu'))) {
            $adminmenu = array();
            include XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/' . $this->getInfo('adminmenu');
            $this->adminmenu =& $adminmenu;
        }
    }

    /**
     * Get the admin menu for the module
     *
     * @return string
     */
    function &getAdminMenu()
    {
        if (!isset($this->adminmenu)) {
            $this->loadAdminMenu();
        }

        return $this->adminmenu;
    }

    /**
     * Load the module info for this module
     *
     * @param string $dirname Module directory
     * @param bool   $verbose Give an error on fail?
     *
     * @return bool true if loaded
     */
    function loadInfo($dirname, $verbose = true)
    {
        static $modVersions;
        $dirname = basename($dirname);
        if (isset($modVersions[$dirname])) {
            $this->modinfo = $modVersions[$dirname];

            return true;
        }
        global $xoopsConfig;
        if (file_exists($file = $GLOBALS['xoops']->path('modules/' . $dirname . '/language/' . $xoopsConfig['language'] . '/modinfo.php'))) {
            include_once $file;
        } elseif (file_exists($file = $GLOBALS['xoops']->path('modules/' . $dirname . '/language/english/modinfo.php'))) {
            include_once $file;
        }

        if (!file_exists($file = $GLOBALS['xoops']->path('modules/' . $dirname . '/xoops_version.php'))) {
            if (false != $verbose) {
                echo "Module File for $dirname Not Found!";
            }

            return false;
        }
        include $file;
        $modVersions[$dirname] = $modversion;
        $this->modinfo = $modVersions[$dirname];

        return true;
    }

    /**
     * Search contents within a module
     *
     * @param  string  $term
     * @param  string  $andor  'AND' or 'OR'
     * @param  integer $limit
     * @param  integer $offset
     * @param  integer $userid
     * @return mixed   Search result.
     */
    function search($term = '', $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        if ($this->getVar('hassearch') != 1) {
            return false;
        }
        $search =& $this->getInfo('search');
        if ($this->getVar('hassearch') != 1 || ! isset($search['file']) || ! isset($search['func']) || $search['func'] == '' || $search['file'] == '') {
            return false;
        }
        if (file_exists($file = $GLOBALS['xoops']->path('modules/' . $this->getVar('dirname') . '/' . $search['file']))) {
            include_once $file;
        } else {
            return false;
        }
        if (function_exists($search['func'])) {
            $func = $search['func'];

            return $func($term, $andor, $limit, $offset, $userid);
        }

        return false;
    }

    /**
     * Returns Class Base Variable mid
     */
    function id($format = 'N')
    {
        return $this->getVar('mid', $format);
    }

    /**
     * Returns Class Base Variable mid
     */
    function mid($format = '')
    {
        return $this->getVar('mid', $format);
    }

    /**
     * Returns Class Base Variable name
     */
    function name($format = '')
    {
        return $this->getVar('name', $format);
    }

    /**
     * Returns Class Base Variable version
     */
    function version($format = '')
    {
        return $this->getVar('version', $format);
    }

    /**
     * Returns Class Base Variable last_update
     */
    function last_update($format = '')
    {
        return $this->getVar('last_update', $format);
    }

    /**
     * Returns Class Base Variable weight
     */
    function weight($format = '')
    {
        return $this->getVar('weight', $format);
    }

    /**
     * Returns Class Base Variable isactive
     */
    function isactive($format = '')
    {
        return $this->getVar('isactive', $format);
    }

    /**
     * Returns Class Base Variable dirname
     */
    function dirname($format = '')
    {
        return $this->getVar('dirname', $format);
    }

    /**
     * Returns Class Base Variable hasmain
     */
    function hasmain($format = '')
    {
        return $this->getVar('hasmain', $format);
    }

    /**
     * Returns Class Base Variable hasadmin
     */
    function hasadmin($format = '')
    {
        return $this->getVar('hasadmin', $format);
    }

    /**
     * Returns Class Base Variable hassearch
     */
    function hassearch($format = '')
    {
        return $this->getVar('hassearch', $format);
    }

    /**
     * Returns Class Base Variable hasconfig
     */
    function hasconfig($format = '')
    {
        return $this->getVar('hasconfig', $format);
    }

    /**
     * Returns Class Base Variable hascomments
     */
    function hascomments($format = '')
    {
        return $this->getVar('hascomments', $format);
    }

    /**
     * Returns Class Base Variable hasnotification
     */
    function hasnotification($format = '')
    {
        return $this->getVar('hasnotification', $format);
    }

    /**
     * @param $dirname
     *
     * @return mixed
     */
    static function &getByDirName($dirname)
    {
        $modhandler =& xoops_gethandler('module');
        $inst =& $modhandler->getByDirname($dirname);

        return $inst;
    }

    ##################### Deprecated Methods ######################

    /**#@+
     * @deprecated
     */
    function checkAccess()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    function loadLanguage($type = "main")
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function loadErrorMessages()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function getCurrentPage()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param array $admingroups
     * @param array $accessgroups
     *
     * @return bool
     */
    function install($admingroups = array(), $accessgroups = array())
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function update()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function insert()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function executeSQL()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function insertTemplates()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param      $template
     * @param bool $block
     *
     * @return bool
     */
    function gettemplate($template, $block = false)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function insertBlocks()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function insertConfigCategories()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function insertConfig()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    function insertProfileFields()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param     $type
     * @param int $state
     *
     * @return bool
     */
    function executeScript($type, $state = 2)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param $groups
     * @param $type
     *
     * @return bool
     */
    function insertGroupPermissions($groups, $type)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}

/**
 * XOOPS module handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS module class objects.
 *
 * @package kernel
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright (c) 2000-2014 The Xoops Project - www.xoops.org
 */
class XoopsModuleHandler extends XoopsObjectHandler
{
    /**
     * holds an array of cached module references, indexed by module id
     *
     * @var array
     * @access private
     */
    var $_cachedModule_mid = array();

    /**
     * holds an array of cached module references, indexed by module dirname
     *
     * @var array
     * @access private
     */
    var $_cachedModule_dirname = array();

    /**
     * Create a new {@link XoopsModule} object
     *
     * @param  boolean $isNew Flag the new object as "new"
     * @return object
     */
    function &create($isNew = true)
    {
        $module = new XoopsModule();
        if ($isNew) {
            $module->setNew();
        }

        return $module;
    }

    /**
     * Load a module from the database
     *
     * @param  int    $id ID of the module
     * @return object FALSE on fail
     */
    function &get($id)
    {
        static $_cachedModule_dirname;
        static $_cachedModule_mid;
        $id = intval($id);
        $module = false;
        if ($id > 0) {
            if (!empty($_cachedModule_mid[$id])) {
                return $_cachedModule_mid[$id];
            } else {
                $sql = 'SELECT * FROM ' . $this->db->prefix('modules') . ' WHERE mid = ' . $id;
                if (!$result = $this->db->query($sql)) {
                    return $module;
                }
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                    $module = new XoopsModule();
                    $myrow = $this->db->fetchArray($result);
                    $module->assignVars($myrow);
                    $_cachedModule_mid[$id] = &$module;
                    $_cachedModule_dirname[$module->getVar('dirname')] = &$module;

                    return $module;
                }
            }
        }

        return $module;
    }

    /**
     * Load a module by its dirname
     *
     * @param  string $dirname
     * @return object FALSE on fail
     */
    function &getByDirname($dirname)
    {
        $dirname = basename($dirname);
        //could not we check for spaces instead??
        if (strpos(strtolower($dirname), ' union ')) {
            return false;
        }
        static $_cachedModule_mid;
        static $_cachedModule_dirname;
        if (!empty($_cachedModule_dirname[$dirname])) {
            return $_cachedModule_dirname[$dirname];
        } else {
            $module = false;
            $sql = "SELECT * FROM " . $this->db->prefix('modules') . " WHERE dirname = '" . trim($dirname) . "'";
            if (!$result = $this->db->query($sql)) {
                return $module;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $module = new XoopsModule();
                $myrow = $this->db->fetchArray($result);
                $module->assignVars($myrow);
                $_cachedModule_dirname[$dirname] =& $module;
                $_cachedModule_mid[$module->getVar('mid')] =& $module;
            }

            return $module;
        }
    }

    /**
     * Write a module to the database
     *
     * @param object $module
     *
     * @internal param $object $ &$module reference to a {@link XoopsModule}
     * @return bool
     */
    function insert(&$module)
    {
        /**
         *
         * @TODO : Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
         */
        if (!is_a($module, 'xoopsmodule')) {
            return false;
        }
        if (!$module->isDirty()) {
            return true;
        }
        if (!$module->cleanVars()) {
            return false;
        }
        foreach ($module->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($module->isNew()) {
            $mid = $this->db->genId('modules_mid_seq');
            $sql = sprintf("INSERT INTO %s (mid, name, version, last_update, weight, isactive, dirname, hasmain, hasadmin, hassearch, hasconfig, hascomments, hasnotification) VALUES (%u, %s, %u, %u, %u, %u, %s, %u, %u, %u, %u, %u, %u)", $this->db->prefix('modules'), $mid, $this->db->quoteString($name), $version, time(), $weight, 1, $this->db->quoteString($dirname), $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification);
        } else {
            $sql = sprintf("UPDATE %s SET name = %s, dirname = %s, version = %u, last_update = %u, weight = %u, isactive = %u, hasmain = %u, hasadmin = %u, hassearch = %u, hasconfig = %u, hascomments = %u, hasnotification = %u WHERE mid = %u", $this->db->prefix('modules'), $this->db->quoteString($name), $this->db->quoteString($dirname), $version, time(), $weight, $isactive, $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification, $mid);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($mid)) {
            $mid = $this->db->getInsertId();
        }
        $module->assignVar('mid', $mid);
        if (!empty($this->_cachedModule_dirname[$dirname])) {
            unset($this->_cachedModule_dirname[$dirname]);
        }
        if (!empty($this->_cachedModule_mid[$mid])) {
            unset($this->_cachedModule_mid[$mid]);
        }

        return true;
    }

    /**
     * Delete a module from the database
     *
     * @param  object $module
     * @internal param $object $ &$module
     * @return bool
     */
    function delete(&$module)
    {
        /**
         *
         * @TODO : Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
         */
        if (!is_a($module, 'xoopsmodule')) {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE mid = %u", $this->db->prefix('modules'), $module->getVar('mid'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        // delete admin permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_admin' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);
        // delete read permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_read' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);

        $sql = sprintf("SELECT block_id FROM %s WHERE module_id = %u", $this->db->prefix('block_module_link'), $module->getVar('mid'));
        if ($result = $this->db->query($sql)) {
            $block_id_arr = array();
            while ($myrow = $this->db->fetchArray($result)) {
                array_push($block_id_arr, $myrow['block_id']);
            }
        }
        // loop through block_id_arr
        if (isset($block_id_arr)) {
            foreach ($block_id_arr as $i) {
                $sql = sprintf("SELECT block_id FROM %s WHERE module_id != %u AND block_id = %u", $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                if ($result2 = $this->db->query($sql)) {
                    if (0 < $this->db->getRowsNum($result2)) {
                        // this block has other entries, so delete the entry for this module
                        $sql = sprintf("DELETE FROM %s WHERE (module_id = %u) AND (block_id = %u)", $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                        $this->db->query($sql);
                    } else {
                        // this block doesnt have other entries, so disable the block and let it show on top page only. otherwise, this block will not display anymore on block admin page!
                        $sql = sprintf("UPDATE %s SET visible = 0 WHERE bid = %u", $this->db->prefix('newblocks'), $i);
                        $this->db->query($sql);
                        $sql = sprintf("UPDATE %s SET module_id = -1 WHERE module_id = %u", $this->db->prefix('block_module_link'), $module->getVar('mid'));
                        $this->db->query($sql);
                    }
                }
            }
        }

        if (!empty($this->_cachedModule_dirname[$module->getVar('dirname')])) {
            unset($this->_cachedModule_dirname[$module->getVar('dirname')]);
        }
        if (!empty($this->_cachedModule_mid[$module->getVar('mid')])) {
            unset($this->_cachedModule_mid[$module->getVar('mid')]);
        }

        return true;
    }

    /**
     * Load some modules
     *
     * @param  object  $criteria  {@link CriteriaElement}
     * @param  boolean $id_as_key Use the ID as key into the array
     * @return array
     */
    function getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM ' . $this->db->prefix('modules');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' ORDER BY weight ' . $criteria->getOrder() . ', mid ASC';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $module = new XoopsModule();
            $module->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $module;
            } else {
                $ret[$myrow['mid']] =& $module;
            }
            unset($module);
        }

        return $ret;
    }

    /**
     * Count some modules
     *
     * @param  object $criteria {@link CriteriaElement}
     * @return int
     */
    function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('modules');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list ($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * returns an array of module names
     *
     * @param  bool    $criteria
     * @param  boolean $dirname_as_key if true, array keys will be module directory names
     *                                 if false, array keys will be module id
     * @return array
     */
    function getList($criteria = null, $dirname_as_key = false)
    {
        $ret = array();
        $modules = $this->getObjects($criteria, true);
        foreach (array_keys($modules) as $i) {
            if (!$dirname_as_key) {
                $ret[$i] = $modules[$i]->getVar('name');
            } else {
                $ret[$modules[$i]->getVar('dirname')] = $modules[$i]->getVar('name');
            }
        }

        return $ret;
    }
}
