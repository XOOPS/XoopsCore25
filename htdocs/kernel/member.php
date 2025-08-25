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
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

require_once __DIR__ . '/user.php';
require_once __DIR__ . '/group.php';

/**
 * XOOPS member handler class.
 * This class provides simple interface (a facade class) for handling groups/users/
 * membership data.
 *
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @package             kernel
 */
class XoopsMemberHandler
{
    /**
     * holds reference to group handler(DAO) class
     * @access private
     */
    protected $groupHandler;

    /**
     * holds reference to user handler(DAO) class
     */
    protected $userHandler;

    /**
     * holds reference to membership handler(DAO) class
     */
    protected $membershipHandler;

    /**
     * holds temporary user objects
     */
    protected $membersWorkingList = [];

    /**
     * constructor
     * @param XoopsDatabase|null| $db
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->groupHandler = new XoopsGroupHandler($db);
        $this->userHandler = new XoopsUserHandler($db);
        $this->membershipHandler = new XoopsMembershipHandler($db);
    }

    /**
     * create a new group
     *
     * @return XoopsGroup XoopsGroup reference to the new group
     */
    public function &createGroup()
    {
        $inst = $this->groupHandler->create();

        return $inst;
    }

    /**
     * create a new user
     *
     * @return XoopsUser reference to the new user
     */
    public function createUser()
    {
        $inst = $this->userHandler->create();

        return $inst;
    }

    /**
     * retrieve a group
     *
     * @param  int $id ID for the group
     * @return XoopsGroup|false XoopsGroup reference to the group
     */
    public function getGroup($id)
    {
        return $this->groupHandler->get($id);
    }

    /**
     * retrieve a user
     *
     * @param  int $id ID for the user
     * @return XoopsUser reference to the user
     */
    public function getUser($id)
    {
        if (!isset($this->membersWorkingList[$id])) {
            $this->membersWorkingList[$id] = $this->userHandler->get($id);
        }

        return $this->membersWorkingList[$id];
    }

    /**
     * delete a group
     *
     * @param  XoopsGroup $group reference to the group to delete
     * @return bool   FALSE if failed
     */
    public function deleteGroup(XoopsGroup $group)
    {
        $s1 = $this->membershipHandler->deleteAll(new Criteria('groupid', $group->getVar('groupid')));
        $s2 = $this->groupHandler->delete($group);

        return ($s1 && $s2);// ? true : false;
    }

    /**
     * delete a user
     *
     * @param  XoopsUser $user reference to the user to delete
     * @return bool   FALSE if failed
     */
    public function deleteUser(XoopsUser $user)
    {
        $s1 = $this->membershipHandler->deleteAll(new Criteria('uid', $user->getVar('uid')));
        $s2 = $this->userHandler->delete($user);

        return ($s1 && $s2);// ? true : false;
    }

    /**
     * insert a group into the database
     *
     * @param  XoopsGroup $group reference to the group to insert
     * @return bool       TRUE if already in database and unchanged
     *                           FALSE on failure
     */
    public function insertGroup(XoopsGroup $group)
    {
        return $this->groupHandler->insert($group);
    }

    /**
     * insert a user into the database
     *
     * @param XoopsUser $user reference to the user to insert
     * @param bool      $force
     *
     * @return bool TRUE if already in database and unchanged
     *              FALSE on failure
     */
    public function insertUser(XoopsUser $user, $force = false)
    {
        return $this->userHandler->insert($user, $force);
    }

    /**
     * retrieve groups from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement}
     * @param  bool            $id_as_key use the group's ID as key for the array?
     * @return array           array of {@link XoopsGroup} objects
     */
    public function getGroups(?CriteriaElement $criteria = null, $id_as_key = false)
    {
        return $this->groupHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * retrieve users from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement}
     * @param  bool            $id_as_key use the group's ID as key for the array?
     * @return array           array of {@link XoopsUser} objects
     */
    public function getUsers(?CriteriaElement $criteria = null, $id_as_key = false)
    {
        return $this->userHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * get a list of groupnames and their IDs
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} object
     * @return array           associative array of group-IDs and names
     */
    public function getGroupList(?CriteriaElement $criteria = null)
    {
        $groups = $this->groupHandler->getObjects($criteria, true);
        $ret    = [];
        foreach (array_keys($groups) as $i) {
            $ret[$i] = $groups[$i]->getVar('name');
        }

        return $ret;
    }

    /**
     * get a list of usernames and their IDs
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} object
     * @return array           associative array of user-IDs and names
     */
    public function getUserList(?CriteriaElement $criteria = null)
    {
        $users = & $this->userHandler->getObjects($criteria, true);
        $ret   = [];
        foreach (array_keys($users) as $i) {
            $ret[$i] = $users[$i]->getVar('uname');
        }

        return $ret;
    }

    /**
     * add a user to a group
     *
     * @param  int $group_id ID of the group
     * @param  int $user_id  ID of the user
     * @return XoopsMembership XoopsMembership
     */
    public function addUserToGroup($group_id, $user_id)
    {
        $mship = $this->membershipHandler->create();
        $mship->setVar('groupid', $group_id);
        $mship->setVar('uid', $user_id);

        return $this->membershipHandler->insert($mship);
    }

    /**
     * remove a list of users from a group
     *
     * @param  int   $group_id ID of the group
     * @param  array $user_ids array of user-IDs
     * @return bool  success?
     */
    public function removeUsersFromGroup($group_id, $user_ids = [])
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('groupid', $group_id));
        $criteria2 = new CriteriaCompo();
        foreach ($user_ids as $uid) {
            $criteria2->add(new Criteria('uid', $uid), 'OR');
        }
        $criteria->add($criteria2);

        return $this->membershipHandler->deleteAll($criteria);
    }

    /**
     * get a list of users belonging to a group
     *
     * @param  int  $group_id ID of the group
     * @param  bool $asobject return the users as objects?
     * @param  int  $limit    number of users to return
     * @param  int  $start    index of the first user to return
     * @return array Array of {@link XoopsUser} objects (if $asobject is TRUE)
     *                        or of associative arrays matching the record structure in the database.
     */
    public function getUsersByGroup($group_id, $asobject = false, $limit = 0, $start = 0)
    {
        $user_ids = $this->membershipHandler->getUsersByGroup($group_id, $limit, $start);
        if (!$asobject) {
            return $user_ids;
        } else {
            $ret = [];
            foreach ($user_ids as $u_id) {
                $user = $this->getUser($u_id);
                if (is_object($user)) {
                    $ret[] = &$user;
                }
                unset($user);
            }

            return $ret;
        }
    }

    /**
     * get a list of groups that a user is member of
     *
     * @param  int  $user_id  ID of the user
     * @param  bool $asobject return groups as {@link XoopsGroup} objects or arrays?
     * @return array array of objects or arrays
     */
    public function getGroupsByUser($user_id, $asobject = false)
    {
        $group_ids = $this->membershipHandler->getGroupsByUser($user_id);
        if (!$asobject) {
            return $group_ids;
        } else {
            $ret = [];
            foreach ($group_ids as $g_id) {
                $ret[] = $this->getGroup($g_id);
            }

            return $ret;
        }
    }

    /**
     * log in a user
     *
     * @param  string    $uname username as entered in the login form
     * @param  string    $pwd   password entered in the login form
     *
     * @return XoopsUser|false logged in XoopsUser, FALSE if failed to log in
     */
    public function loginUser($uname, $pwd)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $uname = $db->escape($uname);
        $pwd = $db->escape($pwd);
        $criteria = new Criteria('uname', $uname);
        $user = & $this->userHandler->getObjects($criteria, false);
        if (!$user || count($user) != 1) {
            return false;
        }

        $hash = $user[0]->pass();
        $type = substr($user[0]->pass(), 0, 1);
        // see if we have a crypt like signature, old md5 hash is just hex digits
        if ($type === '$') {
            if (!password_verify($pwd, $hash)) {
                return false;
            }
            // check if hash uses the best algorithm (i.e. after a PHP upgrade)
            $rehash = password_needs_rehash($hash, PASSWORD_DEFAULT);
        } else {
            if ($hash != md5($pwd)) {
                return false;
            }
            $rehash = true; // automatically update old style
        }
        // hash used an old algorithm, so make it stronger
        if ($rehash) {
            if ($this->getColumnCharacterLength('users', 'pass') < 255) {
                error_log('Upgrade required on users table!');
            } else {
                $user[0]->setVar('pass', password_hash($pwd, PASSWORD_DEFAULT));
                $this->userHandler->insert($user[0]);
            }
        }
        return $user[0];
    }

    /**
     * Get maximum character length for a table column
     *
     * @param string $table  database table
     * @param string $column table column
     *
     * @return int|null max length or null on error
     */
    public function getColumnCharacterLength($table, $column)
    {
        /** @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $dbname = constant('XOOPS_DB_NAME');
        $table = $db->prefix($table);

        $sql = sprintf(
            'SELECT `CHARACTER_MAXIMUM_LENGTH` FROM `information_schema`.`COLUMNS` '
            . "WHERE TABLE_SCHEMA = '%s'AND TABLE_NAME = '%s' AND COLUMN_NAME = '%s'",
            $db->escape($dbname),
            $db->escape($table),
            $db->escape($column),
        );

        /** @var mysqli_result $result */
        $result = $db->query($sql);
        if ($db->isResultSet($result)) {
            $row = $db->fetchRow($result);
            if ($row) {
                $columnLength = $row[0];
                return (int) $columnLength;
            }
        }
        return null;
    }

    /**
     * count users matching certain conditions
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} object
     * @return int
     */
    public function getUserCount(?CriteriaElement $criteria = null)
    {
        return $this->userHandler->getCount($criteria);
    }

    /**
     * count users belonging to a group
     *
     * @param  int $group_id ID of the group
     * @return int
     */
    public function getUserCountByGroup($group_id)
    {
        return $this->membershipHandler->getCount(new Criteria('groupid', $group_id));
    }

    /**
     * updates a single field in a users record
     *
     * @param  XoopsUser $user       reference to the {@link XoopsUser} object
     * @param  string    $fieldName  name of the field to update
     * @param  string    $fieldValue updated value for the field
     * @return bool      TRUE if success or unchanged, FALSE on failure
     */
    public function updateUserByField(XoopsUser $user, $fieldName, $fieldValue)
    {
        $user->setVar($fieldName, $fieldValue);

        return $this->insertUser($user);
    }

    /**
     * updates a single field in a users record
     *
     * @param  string          $fieldName  name of the field to update
     * @param  string          $fieldValue updated value for the field
     * @param  CriteriaElement $criteria   {@link CriteriaElement} object
     * @return bool            TRUE if success or unchanged, FALSE on failure
     */
    public function updateUsersByField($fieldName, $fieldValue, ?CriteriaElement $criteria = null)
    {
        return $this->userHandler->updateAll($fieldName, $fieldValue, $criteria);
    }

    /**
     * activate a user
     *
     * @param  XoopsUser $user reference to the {@link XoopsUser} object
     * @return mixed      successful? false on failure
     */
    public function activateUser(XoopsUser $user)
    {
        if ($user->getVar('level') != 0) {
            return true;
        }
        $user->setVar('level', 1);
        $actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
        $user->setVar('actkey', $actkey);

        return $this->userHandler->insert($user, true);
    }

    protected function allowedSortMap()
    {
        // Maps both prefixed and non-prefixed column names for flexibility
        // This allows sorting by 'uid' or 'u.uid' while maintaining security
        return [
            'uid'            => 'u.uid',
            'uname'          => 'u.uname',
            'email'          => 'u.email',
            'user_regdate'   => 'u.user_regdate',
            'last_login'     => 'u.last_login',
            'user_avatar'    => 'u.user_avatar',
            'name'           => 'u.name',
            // Prefixed versions for explicit table references
            'u.uid'          => 'u.uid',
            'u.uname'        => 'u.uname',
            'u.email'        => 'u.email',
            'u.user_regdate' => 'u.user_regdate',
            'u.last_login'   => 'u.last_login',
            'u.user_avatar'  => 'u.user_avatar',
            'u.name'         => 'u.name',
        ];
    }


    /**
     * Get a list of users belonging to certain groups and matching criteria
     * Temporary solution
     *
     * @param  array           $groups    IDs of groups
     * @param  CriteriaElement $criteria  {@link CriteriaElement} object
     * @param  bool            $asobject  return the users as objects?
     * @param  bool            $id_as_key use the UID as key for the array if $asobject is TRUE
     * @return array           Array of {@link XoopsUser} objects (if $asobject is TRUE)
     *                                    or of associative arrays matching the record structure in the database.
     */

    public function getUsersByGroupLink(
        $groups,
        $criteria = null,
        $asobject = false,
        $id_as_key = false
    ) {
        // Type coercion for backwards compatibility
        $groups = is_array($groups) ? $groups : [$groups];
        $asobject = (bool)$asobject;
        $id_as_key = (bool)$id_as_key;

        // Debug configuration using only current XOOPS debug system
        // Check XOOPS debug mode - we only want PHP debugging (1=inline, 2=popup)
        $xoopsDebugMode = isset($GLOBALS['xoopsConfig']['debug_mode']) ? (int)$GLOBALS['xoopsConfig']['debug_mode'] : 0;
        $xoopsPhpDebugEnabled = ($xoopsDebugMode === 1 || $xoopsDebugMode === 2);

        // Check if debug is allowed for current user based on debugLevel
        $xoopsDebugAllowed = $xoopsPhpDebugEnabled;
        if ($xoopsPhpDebugEnabled && isset($GLOBALS['xoopsConfig']['debugLevel'])) {
            $debugLevel = (int)$GLOBALS['xoopsConfig']['debugLevel'];
            $xoopsUser = $GLOBALS['xoopsUser'] ?? null;
            $xoopsUserIsAdmin = isset($GLOBALS['xoopsUserIsAdmin']) ? $GLOBALS['xoopsUserIsAdmin'] : false;

            // Apply XOOPS debug level restrictions
            switch ($debugLevel) {
                case 2: // Admins only
                    $xoopsDebugAllowed = $xoopsUserIsAdmin;
                    break;
                case 1: // Members only
                    $xoopsDebugAllowed = ($xoopsUser !== null);
                    break;
                case 0: // All users
                default:
                    $xoopsDebugAllowed = true;
                    break;
            }
        }

        // Production safety check - use secure environment detection
        // Note: SERVER_NAME can be spoofed via Host header, so it's not secure for production detection
        // For security, set XOOPS_ENV=production in your server environment or use a config constant
        $isProd = false;

        if (defined('XOOPS_PRODUCTION') && XOOPS_PRODUCTION) {
            // Most secure: use a defined constant set in configuration
            $isProd = true;
        } elseif (getenv('XOOPS_ENV') === 'production') {
            // Secure: use environment variable (not spoofable by clients)
            $isProd = true;
        } else {
            // Fallback: assume production unless explicitly in known development environments
            // This is more secure than the old approach - defaults to restrictive mode
            $isProd = true;
            // Only allow debug in explicitly known safe development indicators
            if ((defined('XOOPS_DEBUG') && XOOPS_DEBUG) ||
                (php_sapi_name() === 'cli') ||
                (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] === '127.0.0.1')) {
                $isProd = false;
            }
        }

        // Enable SQL logging only if XOOPS PHP debug is allowed and not in production
        $isDebug = $xoopsDebugAllowed && !$isProd;

        /**
         * Redact sensitive SQL literals in debug logs while preserving query structure
         * @param string $sql The SQL query to redact
         * @return string Redacted SQL query
         */
        $redactSql = static function (string $sql): string {
            // Replace quoted strings with placeholders
            $sql = preg_replace("/'[^']*'/", "'?'", $sql);
            $sql = preg_replace('/"[^"]*"/', '"?"', $sql);
            // Replace hex literals
            $sql = preg_replace("/x'[0-9A-Fa-f]+'/", "x'?'", $sql);
            // Replace large numbers (potential IDs) but keep small ones
            $sql = preg_replace('/\b\d{6,}\b/', '[ID]', $sql);
            return $sql;
        };

        $ret           = [];
        $criteriaCompo = new CriteriaCompo();
        $select        = $asobject ? 'u.*' : 'u.uid';
        $sql = "SELECT {$select} FROM " . $this->userHandler->db->prefix('users') . ' u';
        $whereParts = [];
        $limit = 0;
        $start = 0;

        // Sanitize and validate groups once - clean and efficient
            $validGroups = array_values(array_unique(array_filter(
            array_map('intval', $groups),
                static fn($id) => $id > 0
            )));

        // Build group filtering with EXISTS subquery (no re-validation needed)
        if (!empty($validGroups)) {
            $group_in = '(' . implode(', ', $validGroups) . ')';
            $whereParts[] = 'EXISTS (SELECT 1 FROM ' . $this->membershipHandler->db->prefix('groups_users_link')
                            . " m WHERE m.uid = u.uid AND m.groupid IN {$group_in})";
        }

        // Handle criteria - compatible with CriteriaElement and subclasses
        if ($criteria instanceof \CriteriaElement) {
            $criteriaCompo->add($criteria, 'AND');
            $sqlCriteria = trim($criteriaCompo->render());

            // Remove WHERE keyword if present
            $sqlCriteria = preg_replace('/^\s*WHERE\s+/i', '', $sqlCriteria ?? '');

            if ('' !== $sqlCriteria) {
                $whereParts[] = $sqlCriteria;
        }

            $limit = (int)$criteria->getLimit();
            $start = (int)$criteria->getStart();
        }

        // Build WHERE clause
        if (!empty($whereParts)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }

        // Handle ORDER BY with enhanced security whitelist
        if ($criteria instanceof \CriteriaElement) {
            $sort = trim($criteria->getSort());
            $order = trim($criteria->getOrder());
            if ('' !== $sort) {
                // Use the whitelist method for safe sorting columns
                $allowedSorts = $this->allowedSortMap();

                if (isset($allowedSorts[$sort])) {
                    $orderDirection = ('DESC' === strtoupper($order)) ? ' DESC' : ' ASC';
                    $sql .= ' ORDER BY ' . $allowedSorts[$sort] . $orderDirection;
                }
            }
        }

        // Execute query with comprehensive error handling
        $result = $this->userHandler->db->query($sql, $limit, $start);

        if (!$this->userHandler->db->isResultSet($result)) {
            // Enhanced error logging with security considerations
            $logger = class_exists('XoopsLogger') ? \XoopsLogger::getInstance() : null;
                $error = $this->userHandler->db->error();

            $msg = "Database query failed in " . __METHOD__ . ": {$error}";

            if ($isDebug) {
                // Add correlation context for easier debugging
                $context = [
                    'user_id' => isset($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']
                        ? $GLOBALS['xoopsUser']->getVar('uid') : 'anonymous',
                    'uri' => $_SERVER['REQUEST_URI'] ?? 'cli',
                    'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
                    'groups_count' => count($validGroups)
                ];
                $msg .= ' Context: ' . json_encode($context, JSON_UNESCAPED_SLASHES);
                $msg .= ' SQL: ' . $redactSql($sql);
            }

            if ($logger) {
                $logger->handleError(E_USER_WARNING, $msg, __FILE__, __LINE__);
            } else {
                // Enhanced fallback logging with file/line info
                error_log($msg . " in " . __FILE__ . " on line " . __LINE__);
            }

            return $ret;
        }

        // Process results with enhanced type safety
        while (false !== ($myrow = $this->userHandler->db->fetchArray($result))) {
            if ($asobject) {
                $user = new XoopsUser();
                $user->assignVars($myrow);
                if ($id_as_key) {
                    $ret[(int)$myrow['uid']] = $user;
                } else {
                    $ret[] = $user;
                }
            } else {
                // Ensure consistent integer return for UIDs
                $ret[] = (int)$myrow['uid'];
            }
        }

        return $ret;
    }

    /**
     * Get count of users belonging to certain groups and matching criteria
     * Temporary solution
     *
     * @param  array           $groups IDs of groups
     * @param  CriteriaElement $criteria
     * @return int             count of users
     */
    public function getUserCountByGroupLink(array $groups, ?CriteriaElement $criteria = null)
    {
        $ret           = 0;
        $criteriaCompo = new CriteriaCompo();
        $sql = "SELECT COUNT(*) FROM " . $this->userHandler->db->prefix('users') . " u WHERE ";
        if (!empty($groups)) {
            $group_in = is_array($groups) ? '(' . implode(', ', $groups) . ')' : (array) $groups;
            $sql .= " EXISTS (SELECT * FROM " . $this->membershipHandler->db->prefix('groups_users_link')
                . " m " . "WHERE m.groupid IN {$group_in} and m.uid = u.uid) ";
        }

        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $criteriaCompo->add($criteria, 'AND');
        }
        $sql_criteria = $criteriaCompo->render();

        if ($sql_criteria) {
            $sql .= ' AND ' . $sql_criteria;
        }
        $result = $this->userHandler->db->query($sql);
        if (!$this->userHandler->db->isResultSet($result)) {
            return $ret;
        }
        [$ret] = $this->userHandler->db->fetchRow($result);

        return (int) $ret;
    }
}
