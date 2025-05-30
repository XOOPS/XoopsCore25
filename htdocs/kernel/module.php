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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A Module
 *
 * @package kernel
 * @author  Kazumi Ono <onokazu@xoops.org>
 */
class XoopsModule extends XoopsObject
{
    /**
     *
     * @var string
     */
    public $modinfo;
    /**
     *
     * @var array
     */
    public $adminmenu;
    /**
     *
     * @var array
     */
    public $_msg;

    //PHP 8.2 Dynamic properties deprecated
    public $mid;
    public $name;
    public $version;
    public $last_update;
    public $weight;
    public $isactive;
    public $dirname;
    public $hasmain;
    public $hasadmin;
    public $hassearch;
    public $hasconfig;
    public $hascomments;
    // RMV-NOTIFY
    public $hasnotification;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('mid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        $this->initVar('version', XOBJ_DTYPE_TXTBOX, null, false);
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
    public function loadInfoAsVar($dirname, $verbose = true)
    {
        $dirname = basename($dirname);
        if (!isset($this->modinfo)) {
            $this->loadInfo($dirname, $verbose);
        }
        $this->setVar('name', $this->modinfo['name'], true);
        $this->setVar('version', $this->modinfo['version'], true);
        $this->setVar('dirname', $this->modinfo['dirname'], true);
        $hasmain     = (isset($this->modinfo['hasMain']) && $this->modinfo['hasMain'] == 1) ? 1 : 0;
        $hasadmin    = (isset($this->modinfo['hasAdmin']) && $this->modinfo['hasAdmin'] == 1) ? 1 : 0;
        $hassearch   = (isset($this->modinfo['hasSearch']) && $this->modinfo['hasSearch'] == 1) ? 1 : 0;
        $hasconfig   = ((isset($this->modinfo['config']) && \is_array($this->modinfo['config'])) || !empty($this->modinfo['hasComments'])) ? 1 : 0;
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
    public function setMessage($str)
    {
        $this->_msg[] = trim($str);
    }

    /**
     * return the messages for this object as an array
     *
     * @return array an array of messages
     * @access public
     */
    public function getMessages()
    {
        return $this->_msg;
    }

    /**
     * Set module info
     *
     * @param  string $name
     * @param  mixed  $value
     * @return bool
     **/
    public function setInfo($name, $value)
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
     * @return array|string    Array of module information.
     *                     If {@link $name} is set, returns a single module information item as string.
     */
    public function &getInfo($name = null)
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return substr(strrchr($this->getVar('version'), '-'), 1);
    }

    /**
     * Compares two "XOOPS-standardized" version number strings.
     *
     * @param  string $version1
     * @param  string $version2
     * @param  string $operator
     * @return boolean The function will return true if the relationship is the one specified by the operator, false otherwise.
     */
    public function versionCompare($version1 = '', $version2 = '', $operator = '<')
    {
        $version1 = strtolower($version1);
        $version2 = strtolower($version2);
        if (false !== strpos($version2, '-stable')) {
            $version2 = substr($version2, 0, strpos($version2, '-stable'));
        }
        if (false !== strpos($version1, '-stable')) {
            $version1 = substr($version1, 0, strpos($version1, '-stable'));
        }
        return version_compare($version1, $version2, $operator);
    }

    /**
     * Get a link to the modules main page
     *
     * @return string FALSE on fail
     */
    public function mainLink()
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
     * @return array
     */
    public function subLink()
    {
        $ret = [];
        if ($this->getInfo('sub') && \is_array($this->getInfo('sub'))) {
            foreach ($this->getInfo('sub') as $submenu) {
                $ret[] = [
                    'id' => $submenu['id']?? '',
                    'name' => $submenu['name'],
                    'url'  => $submenu['url'],
                    'icon' => $submenu['icon']?? '',
                ];
            }
        }

        return $ret;
    }

    /**
     * Load the admin menu for the module
     */
    public function loadAdminMenu()
    {
        $adminmenu = [];
        if ($this->getInfo('adminmenu') && $this->getInfo('adminmenu') != '' && file_exists(XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/' . $this->getInfo('adminmenu'))) {
            include XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/' . $this->getInfo('adminmenu');
        }
        $this->adminmenu = & $adminmenu;
    }

    /**
     * Get the admin menu for the module
     *
     * @return array
     */
    public function &getAdminMenu()
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
    public function loadInfo($dirname, $verbose = true)
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
            if (false !== (bool) $verbose) {
                echo "Module File for $dirname Not Found!";
            }

            return false;
        }
        include $file;
        $modVersions[$dirname] = $modversion;
        $this->modinfo         = $modVersions[$dirname];

        return true;
    }

    /**
     * Search contents within a module
     *
     * @param  string  $term
     * @param  string  $andor 'AND' or 'OR'
     * @param  integer $limit
     * @param  integer $offset
     * @param  integer $userid
     * @return mixed   Search result.
     */
    public function search($term = '', $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        if ($this->getVar('hassearch') != 1) {
            return false;
        }
        $search = & $this->getInfo('search');
        if ($this->getVar('hassearch') != 1 || !isset($search['file']) || !isset($search['func']) || $search['func'] == '' || $search['file'] == '') {
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
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('mid', $format);
    }

    /**
     * Returns Class Base Variable mid
     * @param  string $format
     * @return mixed
     */
    public function mid($format = '')
    {
        return $this->getVar('mid', $format);
    }

    /**
     * Returns Class Base Variable name
     * @param  string $format
     * @return mixed
     */
    public function name($format = '')
    {
        return $this->getVar('name', $format);
    }

    /**
     * Returns Class Base Variable version
     * @param  string $format
     * @return mixed
     */
    public function version($format = '')
    {
        return $this->getVar('version', $format);
    }

    /**
     * Returns Class Base Variable last_update
     * @param  string $format
     * @return mixed
     */
    public function last_update($format = '')
    {
        return $this->getVar('last_update', $format);
    }

    /**
     * Returns Class Base Variable weight
     * @param  string $format
     * @return mixed
     */
    public function weight($format = '')
    {
        return $this->getVar('weight', $format);
    }

    /**
     * Returns Class Base Variable isactive
     * @param  string $format
     * @return mixed
     */
    public function isactive($format = '')
    {
        return $this->getVar('isactive', $format);
    }

    /**
     * Returns Class Base Variable dirname
     * @param  string $format
     * @return mixed
     */
    public function dirname($format = '')
    {
        return $this->getVar('dirname', $format);
    }

    /**
     * Returns Class Base Variable hasmain
     * @param  string $format
     * @return mixed
     */
    public function hasmain($format = '')
    {
        return $this->getVar('hasmain', $format);
    }

    /**
     * Returns Class Base Variable hasadmin
     * @param  string $format
     * @return mixed
     */
    public function hasadmin($format = '')
    {
        return $this->getVar('hasadmin', $format);
    }

    /**
     * Returns Class Base Variable hassearch
     * @param  string $format
     * @return mixed
     */
    public function hassearch($format = '')
    {
        return $this->getVar('hassearch', $format);
    }

    /**
     * Returns Class Base Variable hasconfig
     * @param  string $format
     * @return mixed
     */
    public function hasconfig($format = '')
    {
        return $this->getVar('hasconfig', $format);
    }

    /**
     * Returns Class Base Variable hascomments
     * @param  string $format
     * @return mixed
     */
    public function hascomments($format = '')
    {
        return $this->getVar('hascomments', $format);
    }

    /**
     * Returns Class Base Variable hasnotification
     * @param  string $format
     * @return mixed
     */
    public function hasnotification($format = '')
    {
        return $this->getVar('hasnotification', $format);
    }

    /**
     * @param $dirname
     *
     * @return mixed
     */
    public static function getByDirname($dirname)
    {
        /** @var XoopsModuleHandler $modhandler */
        $modhandler = xoops_getHandler('module');
        $inst       = $modhandler->getByDirname($dirname);

        return $inst;
    }


    /**
     * Returns Class Base Variable icon
     * @param  string $format
     * @return mixed
     */
    public function icon($format = '')
    {
        return $this->getVar('icon', $format);
    }


    ##################### Deprecated Methods ######################

    /**#@+
     * @deprecated
     */
    public function checkAccess()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function loadLanguage($type = 'main')
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function loadErrorMessages()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function getCurrentPage()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param array $admingroups
     * @param array $accessgroups
     *
     * @return bool
     */
    public function install($admingroups = [], $accessgroups = [])
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function insert()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function executeSQL()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function insertTemplates()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param      $template
     * @param bool $block
     *
     * @return bool
     */
    public function gettemplate($template, $block = false)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function insertBlocks()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function insertConfigCategories()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function insertConfig()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     */
    public function insertProfileFields()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param     $type
     * @param int $state
     *
     * @return bool
     */
    public function executeScript($type, $state = 2)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param $groups
     * @param $type
     *
     * @return bool
     */
    public function insertGroupPermissions($groups, $type)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

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
 * @package       kernel
 * @author        Kazumi Ono <onokazu@xoops.org>
 * @copyright (c) 2000-2016 XOOPS Project - www.xoops.org
 *
 * @todo Why is this not a XoopsPersistableObjectHandler?
 */
class XoopsModuleHandler extends XoopsObjectHandler
{
    /**
     * holds an array of cached module references, indexed by module id
     *
     * @var array
     * @access private
     */
    public $_cachedModule_mid = [];

    /**
     * holds an array of cached module references, indexed by module dirname
     *
     * @var array
     * @access private
     */
    public $_cachedModule_dirname = [];

    /**
     * Create a new {@link XoopsModule} object
     *
     * @param  boolean $isNew Flag the new object as "new"
     * @return XoopsModule
     */
    public function create($isNew = true)
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
     * @param  int $id ID of the module
     * @return XoopsObject|false false on fail
     */
    public function get($id)
    {
        static $_cachedModule_dirname;
        static $_cachedModule_mid;
        $id     = (int) $id;
        $module = false;
        if ($id > 0) {
            if (!empty($_cachedModule_mid[$id])) {
                return $_cachedModule_mid[$id];
            } else {
                $sql = 'SELECT * FROM ' . $this->db->prefix('modules') . ' WHERE mid = ' . $id;
                $result = $this->db->query($sql);
                if (!$this->db->isResultSet($result)) {
                    return $module;
                }
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                    $module = new XoopsModule();
                    $myrow  = $this->db->fetchArray($result);
                    $module->assignVars($myrow);
                    $_cachedModule_mid[$id]                            = &$module;
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
     * @return XoopsModule|FALSE on fail
     */
    public function getByDirname($dirname)
    {
        $dirname = basename($dirname);
        // Sanitize $dirname to prevent SQL injection
        $dirname = $this->db->escape(trim($dirname));

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
            $sql    = 'SELECT * FROM ' . $this->db->prefix('modules') . ' WHERE dirname = ?';
            $stmt   = $this->db->conn->prepare($sql);
            $stmt->bind_param('s', $dirname);
            $success = $stmt->execute();
            if (!$success) {
                return $module;
            }
            $result = $stmt->get_result();

            if (!$this->db->isResultSet($result)) {
                return $module;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $module = new XoopsModule();
                $myrow  = $this->db->fetchArray($result);
                $module->assignVars($myrow);
                $_cachedModule_dirname[$dirname]           = & $module;
                $_cachedModule_mid[$module->getVar('mid')] = & $module;
            }

            return $module;
        }
    }

    /**
     * Write a module to the database
     *
     * @param  XoopsObject|XoopsModule $module a XoopsModule object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $module)
    {
        $className = 'XoopsModule';
        if (!($module instanceof $className)) {
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
            $sql = sprintf('INSERT INTO %s (mid, name, version, last_update, weight, isactive, dirname, hasmain, hasadmin, hassearch, hasconfig, hascomments, hasnotification) VALUES (%u, %s, %s, %u, %u, %u, %s, %u, %u, %u, %u, %u, %u)', $this->db->prefix('modules'), $mid, $this->db->quoteString($name), $this->db->quoteString($version), time(), $weight, 1, $this->db->quoteString($dirname), $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification);
        } else {
            $sql = sprintf('UPDATE %s SET name = %s, dirname = %s, version = %s, last_update = %u, weight = %u, isactive = %u, hasmain = %u, hasadmin = %u, hassearch = %u, hasconfig = %u, hascomments = %u, hasnotification = %u WHERE mid = %u', $this->db->prefix('modules'), $this->db->quoteString($name), $this->db->quoteString($dirname), $this->db->quoteString($version), time(), $weight, $isactive, $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification, $mid);
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
     * @param  XoopsObject|XoopsModule $module a XoopsModule object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $module)
    {
        $className = 'XoopsModule';
        if (!($module instanceof $className)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE mid = %u', $this->db->prefix('modules'), $module->getVar('mid'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        // delete admin permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_admin' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);
        // delete read permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_read' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);

        $sql = sprintf('SELECT block_id FROM %s WHERE module_id = %u', $this->db->prefix('block_module_link'), $module->getVar('mid'));
        $result = $this->db->query($sql);
        if ($this->db->isResultSet($result)) {
            $block_id_arr = [];
            /** @var array $myrow */
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $block_id_arr[] = $myrow['block_id'];
            }
        }
        // loop through block_id_arr
        if (isset($block_id_arr)) {
            foreach ($block_id_arr as $i) {
                $sql = sprintf('SELECT block_id FROM %s WHERE module_id != %u AND block_id = %u', $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                $result2 = $this->db->query($sql);
                if ($this->db->isResultSet($result2)) {
                    if (0 < $this->db->getRowsNum($result2)) {
                        // this block has other entries, so delete the entry for this module
                        $sql = sprintf('DELETE FROM %s WHERE (module_id = %u) AND (block_id = %u)', $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                        $this->db->query($sql);
                    } else {
                        // this block doesn't have other entries, so disable the block and let it show on top page only. otherwise, this block will not display anymore on block admin page!
                        $sql = sprintf('UPDATE %s SET visible = 0 WHERE bid = %u', $this->db->prefix('newblocks'), $i);
                        $this->db->query($sql);
                        $sql = sprintf('UPDATE %s SET module_id = -1 WHERE module_id = %u', $this->db->prefix('block_module_link'), $module->getVar('mid'));
                        $this->db->query($sql);
                    }
                }
            }
        }

        $dirname = (string) $module->getVar('dirname');
        $mid = (int) $module->getVar('mid');

        if (!empty($this->_cachedModule_dirname[$dirname])) {
            unset($this->_cachedModule_dirname[$dirname]);
        }
        if (!empty($this->_cachedModule_mid[$mid])) {
            unset($this->_cachedModule_mid[$mid]);
        }

        return true;
    }

    /**
     * Load some modules
     *
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement}
     * @param  boolean         $id_as_key Use the ID as key into the array
     * @return array
     */
    public function getObjects(?CriteriaElement $criteria = null, $id_as_key = false)
    {
        if (func_num_args() > 0) {
            $criteria = func_get_arg(0);
        }

        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $criteria = $criteria ?? null;
        }

        if (func_num_args() > 0) {
            $criteria = func_get_arg(0);
        }

        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $criteria = $criteria ?? null; // Explicitly set to null if not provided
        }

        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('modules');
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' ORDER BY weight ' . $criteria->getOrder() . ', mid ASC';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$this->db->isResultSet($result)) {
            return $ret;
        }
        /** @var array $myrow */
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $module = new XoopsModule();
            $module->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = & $module;
            } else {
                $ret[$myrow['mid']] = & $module;
            }
            unset($module);
        }

        return $ret;
    }

    /**
     * Count some modules
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     * @return int
     */
    public function getCount(?CriteriaElement $criteria = null)
    {
        if (func_num_args() > 0) {
            $criteria = func_get_arg(0);
        }

        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $criteria = $criteria ?? null;
        }

        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('modules');
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$this->db->isResultSet($result)) {
            return 0;
        }
        [$count] = $this->db->fetchRow($result);

        return (int) $count;
    }

    /**
     * returns an array of module names
     *
     * @param  CriteriaElement $criteria
     * @param  boolean         $dirname_as_key if true, array keys will be module directory names
     *                                         if false, array keys will be module id
     * @return array
     */
    public function getList(?CriteriaElement $criteria = null, $dirname_as_key = false)
    {

        if (func_num_args() > 0) {
            $criteria = func_get_arg(0);
        }

        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $criteria = $criteria ?? null; // Explicitly set to null if not provided
        }


        $ret     = [];
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
