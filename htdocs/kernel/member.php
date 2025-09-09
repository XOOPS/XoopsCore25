<?php
/**
 * XOOPS Kernel Class - Enhanced Member Handler
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         kernel
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version         2.0.0 - Enhanced with security hardening and PHP 7.4-8.4 compatibility
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new RuntimeException('Restricted access');
}

require_once __DIR__ . '/user.php';
require_once __DIR__ . '/group.php';

/**
 * XOOPS member handler class.
 * This class provides simple interface (a facade class) for handling groups/users/
 * membership data with enhanced security and performance optimizations.
 *
 * @package kernel
 */
class XoopsMemberHandler
{
    // Security constants
    private const BIDI_CONTROL_REGEX = '/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u';
    private const SENSITIVE_PARAMS = [
        'token', 'access_token', 'id_token', 'password', 'pass', 'pwd',
        'secret', 'key', 'api_key', 'apikey', 'auth', 'authorization',
        'session', 'sid', 'code', 'csrf', 'nonce'
    ];
    private const MAX_BATCH_SIZE = 1000;
    private const LOG_CONTEXT_MAX_LENGTH = 256;

    /**
     * @var XoopsGroupHandler Reference to group handler(DAO) class
     */
    protected $groupHandler;

    /**
     * @var XoopsUserHandler Reference to user handler(DAO) class
     */
    protected $userHandler;

    /**
     * @var XoopsMembershipHandler Reference to membership handler(DAO) class
     */
    protected $membershipHandler;

    /**
     * @var array<int,XoopsUser> Temporary user objects cache
     */
    protected $membersWorkingList = [];

    /**
     * Constructor
     * @param XoopsDatabase $db Database connection object
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->groupHandler = new XoopsGroupHandler($db);
        $this->userHandler = new XoopsUserHandler($db);
        $this->membershipHandler = new XoopsMembershipHandler($db);
    }

    /**
     * Create a new group
     * @return XoopsGroup Reference to the new group
     */
    public function &createGroup()
    {
        $inst = $this->groupHandler->create();
        return $inst;
    }

    /**
     * Create a new user
     * @return XoopsUser Reference to the new user
     */
    public function createUser()
    {
        $inst = $this->userHandler->create();
        return $inst;
    }

    /**
     * Retrieve a group
     * @param int $id ID for the group
     * @return XoopsGroup|false XoopsGroup reference to the group or false
     */
    public function getGroup($id)
    {
        return $this->groupHandler->get($id);
    }

    /**
     * Retrieve a user (with caching)
     * @param int $id ID for the user
     * @return XoopsUser|false Reference to the user or false
     */
    public function getUser($id)
    {
        $id = (int)$id;
        if (!isset($this->membersWorkingList[$id])) {
            $this->membersWorkingList[$id] = $this->userHandler->get($id);
        }
        return $this->membersWorkingList[$id];
    }

    /**
     * Delete a group
     * @param XoopsGroup $group Reference to the group to delete
     * @return bool TRUE on success, FALSE on failure
     */
    public function deleteGroup(XoopsGroup $group)
    {
        $criteria = $this->createSafeInCriteria('groupid', $group->getVar('groupid'));
        $s1 = $this->membershipHandler->deleteAll($criteria);
        $s2 = $this->groupHandler->delete($group);
        return ($s1 && $s2);
    }

    /**
     * Delete a user
     * @param XoopsUser $user Reference to the user to delete
     * @return bool TRUE on success, FALSE on failure
     */
    public function deleteUser(XoopsUser $user)
    {
        $criteria = $this->createSafeInCriteria('uid', $user->getVar('uid'));
        $s1 = $this->membershipHandler->deleteAll($criteria);
        $s2 = $this->userHandler->delete($user);
        return ($s1 && $s2);
    }

    /**
     * Insert a group into the database
     * @param XoopsGroup $group Reference to the group to insert
     * @return bool TRUE on success, FALSE on failure
     */
    public function insertGroup(XoopsGroup $group)
    {
        return $this->groupHandler->insert($group);
    }

    /**
     * Insert a user into the database
     * @param XoopsUser $user Reference to the user to insert
     * @param bool $force Force insertion even if user already exists
     * @return bool TRUE on success, FALSE on failure
     */
    public function insertUser(XoopsUser $user, $force = false)
    {
        return $this->userHandler->insert($user, $force);
    }

    /**
     * Retrieve groups from the database
     * @param CriteriaElement|null $criteria {@link CriteriaElement}
     * @param bool $id_as_key Use the group's ID as key for the array?
     * @return array Array of {@link XoopsGroup} objects
     */
    public function getGroups($criteria = null, $id_as_key = false)
    {
        return $this->groupHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * Retrieve users from the database
     * @param CriteriaElement|null $criteria {@link CriteriaElement}
     * @param bool $id_as_key Use the user's ID as key for the array?
     * @return array Array of {@link XoopsUser} objects
     */
    public function getUsers($criteria = null, $id_as_key = false)
    {
        return $this->userHandler->getObjects($criteria, $id_as_key);
    }

    /**
     * Get a list of groupnames and their IDs
     * @param CriteriaElement|null $criteria {@link CriteriaElement} object
     * @return array Associative array of group-IDs and names
     */
    public function getGroupList($criteria = null)
    {
        $groups = $this->groupHandler->getObjects($criteria, true);
        $ret = [];
        foreach (array_keys($groups) as $i) {
            $ret[$i] = $groups[$i]->getVar('name');
        }
        return $ret;
    }

    /**
     * Get a list of usernames and their IDs
     * @param CriteriaElement|null $criteria {@link CriteriaElement} object
     * @return array Associative array of user-IDs and names
     */
    public function getUserList($criteria = null)
    {
        $users = $this->userHandler->getObjects($criteria, true);
        $ret = [];
        foreach (array_keys($users) as $i) {
            $ret[$i] = $users[$i]->getVar('uname');
        }
        return $ret;
    }

    /**
     * Add a user to a group
     * @param int $group_id ID of the group
     * @param int $user_id ID of the user
     * @return XoopsMembership|bool XoopsMembership object on success, FALSE on failure
     */
    public function addUserToGroup($group_id, $user_id)
    {
        $mship = $this->membershipHandler->create();
        $mship->setVar('groupid', (int)$group_id);
        $mship->setVar('uid', (int)$user_id);
        $result = $this->membershipHandler->insert($mship);
        return $result ? $mship : false;
    }

    /**
     * Remove a list of users from a group
     * @param int $group_id ID of the group
     * @param array $user_ids Array of user-IDs
     * @return bool TRUE on success, FALSE on failure
     */
    public function removeUsersFromGroup($group_id, $user_ids = [])
    {
        $ids = $this->sanitizeIds($user_ids);
        if (empty($ids)) {
            return true; // No-op success
        }

        // Handle large batches
        if (count($ids) > self::MAX_BATCH_SIZE) {
            $batches = array_chunk($ids, self::MAX_BATCH_SIZE);
            foreach ($batches as $batch) {
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('groupid', (int)$group_id));
                $criteria->add(new Criteria('uid', '(' . implode(',', $batch) . ')', 'IN'));
                if (!$this->membershipHandler->deleteAll($criteria)) {
                    return false;
                }
            }
            return true;
        }

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('groupid', (int)$group_id));
        $criteria->add(new Criteria('uid', '(' . implode(',', $ids) . ')', 'IN'));
        return $this->membershipHandler->deleteAll($criteria);
    }

    /**
     * Get a list of users belonging to a group
     * @param int $group_id ID of the group
     * @param bool $asobject Return the users as objects?
     * @param int $limit Number of users to return
     * @param int $start Index of the first user to return
     * @return array Array of {@link XoopsUser} objects or user IDs
     */
    public function getUsersByGroup($group_id, $asobject = false, $limit = 0, $start = 0)
    {
        $user_ids = $this->membershipHandler->getUsersByGroup($group_id, $limit, $start);
        if (!$asobject || empty($user_ids)) {
            return $user_ids;
        }

        // Batch fetch users for better performance
        $criteria = new Criteria('uid', '(' . implode(',', array_map('intval', $user_ids)) . ')', 'IN');
        $users = $this->userHandler->getObjects($criteria, true);

        $ret = [];
        foreach ($user_ids as $uid) {
            if (isset($users[$uid])) {
                $ret[] = $users[$uid];
            }
        }
        return $ret;
    }

    /**
     * Get a list of groups that a user is member of
     * @param int $user_id ID of the user
     * @param bool $asobject Return groups as {@link XoopsGroup} objects or arrays?
     * @return array Array of objects or arrays
     */
    public function getGroupsByUser($user_id, $asobject = false)
    {
        $group_ids = $this->membershipHandler->getGroupsByUser($user_id);
        if (!$asobject || empty($group_ids)) {
            return $group_ids;
        }

        // Batch fetch groups for better performance
        $criteria = new Criteria('groupid', '(' . implode(',', array_map('intval', $group_ids)) . ')', 'IN');
        $groups = $this->groupHandler->getObjects($criteria, true);

        $ret = [];
        foreach ($group_ids as $gid) {
            if (isset($groups[$gid])) {
                $ret[] = $groups[$gid];
            }
        }
        return $ret;
    }

    /**
     * Log in a user with enhanced security
     * @param string $uname Username as entered in the login form
     * @param string $pwd Password entered in the login form
     * @return XoopsUser|false Logged in XoopsUser, FALSE if failed to log in
     */
    public function loginUser($uname, $pwd)
    {
        // Use Criteria for safe querying (no manual escaping of password)
        $criteria = new Criteria('uname', (string)$uname);
        $criteria->setLimit(2); // Fetch at most 2 to detect duplicates

        $users = $this->userHandler->getObjects($criteria, false);
        if (!$users || count($users) != 1) {
            return false;
        }

        /** @var XoopsUser $user */
        $user = $users[0];
        $hash = $user->pass();

        // Check if password uses modern hashing (PHP 7.4 compatible check)
        $isModernHash = (isset($hash[0]) && $hash[0] === '$');

        if ($isModernHash) {
            // Modern password hash
            if (!password_verify($pwd, $hash)) {
                return false;
            }
            $rehash = password_needs_rehash($hash, PASSWORD_DEFAULT);
        } else {
            // Legacy MD5 hash - use timing-safe comparison
            $expectedHash = md5($pwd);
            if (!$this->hashEquals($expectedHash, $hash)) {
                return false;
            }
            $rehash = true; // Always upgrade from MD5
        }

        // Upgrade password hash if needed
        if ($rehash) {
            $columnLength = $this->getColumnCharacterLength('users', 'pass');
            if ($columnLength === null || $columnLength < 255) {
                $this->logSecurityEvent('Password column too small for modern hashes', [
                    'table' => 'users',
                    'column' => 'pass',
                    'current_length' => $columnLength
                ]);
            } else {
                $newHash = password_hash($pwd, PASSWORD_DEFAULT);
                $user->setVar('pass', $newHash);
                $this->userHandler->insert($user);
            }
        }

        return $user;
    }

    /**
     * Get maximum character length for a table column
     * @param string $table Database table name
     * @param string $column Table column name
     * @return int|null Max length or null on error
     */
    public function getColumnCharacterLength($table, $column)
    {
        /** @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $dbname = constant('XOOPS_DB_NAME');
        $table = $db->prefix($table);

        // Use quoteString if available, otherwise fall back to escape
        $quoteFn = method_exists($db, 'quoteString') ? 'quoteString' : 'escape';

        $sql = sprintf(
            'SELECT `CHARACTER_MAXIMUM_LENGTH` FROM `information_schema`.`COLUMNS` 
             WHERE `TABLE_SCHEMA` = %s AND `TABLE_NAME` = %s AND `COLUMN_NAME` = %s',
            $db->$quoteFn($dbname),
            $db->$quoteFn($table),
            $db->$quoteFn($column)
        );

        /** @var mysqli_result|resource|false $result */
        $result = $db->query($sql);
        if ($db->isResultSet($result)) {
            $row = $db->fetchRow($result);
            if ($row) {
                return (int)$row[0];
            }
        }
        return null;
    }

    /**
     * Count users matching certain conditions
     * @param CriteriaElement|null $criteria {@link CriteriaElement} object
     * @return int Number of users
     */
    public function getUserCount($criteria = null)
    {
        return $this->userHandler->getCount($criteria);
    }

    /**
     * Count users belonging to a group
     * @param int $group_id ID of the group
     * @return int Number of users in the group
     */
    public function getUserCountByGroup($group_id)
    {
        return $this->membershipHandler->getCount(new Criteria('groupid', (int)$group_id));
    }

    /**
     * Update a single field in a user's record
     * @param XoopsUser $user Reference to the {@link XoopsUser} object
     * @param string $fieldName Name of the field to update
     * @param mixed $fieldValue Updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    public function updateUserByField(XoopsUser $user, $fieldName, $fieldValue)
    {
        $user->setVar($fieldName, $fieldValue);
        return $this->insertUser($user);
    }

    /**
     * Update a single field for multiple users
     * @param string $fieldName Name of the field to update
     * @param mixed $fieldValue Updated value for the field
     * @param CriteriaElement|null $criteria {@link CriteriaElement} object
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    public function updateUsersByField($fieldName, $fieldValue, $criteria = null)
    {
        return $this->userHandler->updateAll($fieldName, $fieldValue, $criteria);
    }

    /**
     * Activate a user
     * @param XoopsUser $user Reference to the {@link XoopsUser} object
     * @return bool TRUE on success, FALSE on failure
     */
    public function activateUser(XoopsUser $user)
    {
        if ($user->getVar('level') != 0) {
            return true;
        }
        $user->setVar('level', 1);

        // Generate more secure activation key
        $actkey = $this->generateSecureToken(8);
        $user->setVar('actkey', $actkey);

        return $this->userHandler->insert($user, true);
    }

    /**
     * Get a list of users belonging to certain groups and matching criteria
     * @param int|array $groups IDs of groups
     * @param CriteriaElement|null $criteria {@link CriteriaElement} object
     * @param bool $asobject Return the users as objects?
     * @param bool $id_as_key Use the UID as key for the array if $asobject is TRUE
     * @return array Array of {@link XoopsUser} objects or user IDs
     */
    public function getUsersByGroupLink($groups, $criteria = null, $asobject = false, $id_as_key = false)
    {
        $groups = (array)$groups;
        $validGroups = $this->sanitizeIds($groups);

        // If groups were specified but none are valid, return empty array
        if (!empty($groups) && empty($validGroups)) {
            return [];
        }

        $isDebug = $this->isDebugAllowed() && !$this->isProductionEnvironment();

        /** @var XoopsMySQLDatabase $db */
        $db = $this->userHandler->db;
        $select = $asobject ? 'u.*' : 'u.uid';
        $sql = "SELECT {$select} FROM " . $db->prefix('users') . ' u';
        $whereParts = [];

        if (!empty($validGroups)) {
            $linkTable = $db->prefix('groups_users_link');
            $group_in = '(' . implode(', ', $validGroups) . ')';
            $whereParts[] = "EXISTS (SELECT 1 FROM {$linkTable} m WHERE m.uid = u.uid AND m.groupid IN {$group_in})";
        }

        $limit = 0;
        $start = 0;
        $orderBy = '';

        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $criteriaCompo = new CriteriaCompo();
            $criteriaCompo->add($criteria, 'AND');
            $sqlCriteria = preg_replace('/^\s*WHERE\s+/i', '', trim($criteriaCompo->render()));
            if ($sqlCriteria !== '') {
                $whereParts[] = $sqlCriteria;
            }

            $limit = (int)$criteria->getLimit();
            $start = (int)$criteria->getStart();

            // Apply safe sorting
            $sort = trim((string)$criteria->getSort());
            $order = trim((string)$criteria->getOrder());
            if ($sort !== '') {
                $allowed = $this->getAllowedSortFields();
                if (isset($allowed[$sort])) {
                    $orderBy = ' ORDER BY ' . $allowed[$sort];
                    $orderBy .= (strtoupper($order) === 'DESC') ? ' DESC' : ' ASC';
                }
            }
        }

        if (!empty($whereParts)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }
        $sql .= $orderBy;

        $result = $db->query($sql, $limit, $start);
        if (!$db->isResultSet($result)) {
            $this->logDatabaseError('Query failed in getUsersByGroupLink', $sql, [
                'groups_count' => count($validGroups),
                'has_criteria' => isset($criteria)
            ], $isDebug);
            return [];
        }

        $ret = [];
        /** @var array $myrow */
        while (false !== ($myrow = $db->fetchArray($result))) {
            if ($asobject) {
                $user = new XoopsUser();
                $user->assignVars($myrow);
                if ($id_as_key) {
                    $ret[(int)$myrow['uid']] = $user;
                } else {
                    $ret[] = $user;
                }
            } else {
                $ret[] = (int)$myrow['uid'];
            }
        }

        return $ret;
    }

    /**
     * Get count of users belonging to certain groups and matching criteria
     * @param array $groups IDs of groups
     * @param CriteriaElement|null $criteria {@link CriteriaElement} object
     * @return int Count of users
     */
    public function getUserCountByGroupLink(array $groups, $criteria = null)
    {
        $validGroups = $this->sanitizeIds($groups);

        // If groups were specified but none are valid, return 0
        if (!empty($groups) && empty($validGroups)) {
            return 0;
        }

        $isDebug = $this->isDebugAllowed() && !$this->isProductionEnvironment();

        /** @var XoopsMySQLDatabase $db */
        $db = $this->userHandler->db;
        $sql = 'SELECT COUNT(*) FROM ' . $db->prefix('users') . ' u';
        $whereParts = [];

        if (!empty($validGroups)) {
            $linkTable = $db->prefix('groups_users_link');
            $group_in = '(' . implode(', ', $validGroups) . ')';
            $whereParts[] = "EXISTS (SELECT 1 FROM {$linkTable} m WHERE m.uid = u.uid AND m.groupid IN {$group_in})";
        }

        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $criteriaCompo = new CriteriaCompo();
            $criteriaCompo->add($criteria, 'AND');
            $sqlCriteria = preg_replace('/^\s*WHERE\s+/i', '', trim($criteriaCompo->render()));
            if ($sqlCriteria !== '') {
                $whereParts[] = $sqlCriteria;
            }
        }

        if (!empty($whereParts)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }

        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            $this->logDatabaseError('Query failed in getUserCountByGroupLink', $sql, [
                'groups_count' => count($validGroups),
                'has_criteria' => isset($criteria)
            ], $isDebug);
            return 0;
        }

        list($count) = $db->fetchRow($result);
        return (int)$count;
    }

    // =========================================================================
    // Private Helper Methods
    // =========================================================================

    /**
     * Create a safe IN criteria for IDs
     * @param string $field Field name
     * @param mixed $value Single value or array of values
     * @return CriteriaElement
     */
    private function createSafeInCriteria($field, $value)
    {
        $ids = $this->sanitizeIds((array)$value);
        $inClause = !empty($ids) ? '(' . implode(',', $ids) . ')' : '(0)';
        return new Criteria($field, $inClause, 'IN');
    }

    /**
     * Sanitize an array of IDs
     * @param array $ids Array of potential IDs
     * @return array Array of valid integer IDs
     */
    private function sanitizeIds(array $ids)
    {
        return array_values(array_filter(
            array_map('intval', array_filter($ids, function($v) {
                return is_int($v) || (is_string($v) && ctype_digit($v));
            })),
            function($v) { return $v > 0; }
        ));
    }

    /**
     * Get allowed sort fields for SQL queries
     * @return array Map of allowed field names to SQL columns
     */
    private function getAllowedSortFields()
    {
        return [
            // Non-prefixed (backward compatibility)
            'uid' => 'u.uid',
            'uname' => 'u.uname',
            'email' => 'u.email',
            'user_regdate' => 'u.user_regdate',
            'last_login' => 'u.last_login',
            'user_avatar' => 'u.user_avatar',
            'name' => 'u.name',
            'posts' => 'u.posts',
            'level' => 'u.level',
            // Prefixed (explicit)
            'u.uid' => 'u.uid',
            'u.uname' => 'u.uname',
            'u.email' => 'u.email',
            'u.user_regdate' => 'u.user_regdate',
            'u.last_login' => 'u.last_login',
            'u.user_avatar' => 'u.user_avatar',
            'u.name' => 'u.name',
            'u.posts' => 'u.posts',
            'u.level' => 'u.level'
        ];
    }

    /**
     * Timing-safe string comparison (PHP 7.4 compatible)
     * @param string $expected Expected string
     * @param string $actual Actual string
     * @return bool TRUE if strings are equal
     */
    private function hashEquals($expected, $actual)
    {
        // Use hash_equals if available (PHP 5.6+)
        if (function_exists('hash_equals')) {
            return hash_equals($expected, $actual);
        }

        // Fallback implementation
        $expected = (string)$expected;
        $actual = (string)$actual;
        $expectedLength = strlen($expected);
        $actualLength = strlen($actual);
        $diff = $expectedLength ^ $actualLength;

        for ($i = 0; $i < $actualLength; $i++) {
            $diff |= ord($expected[$i % $expectedLength]) ^ ord($actual[$i]);
        }

        return $diff === 0;
    }

    /**
     * Generate a secure random token (hex-encoded).
     * @param int $length Desired token length in HEX characters (4 bits per char). Clamped to [8, 32].
     * @return string Random token
     */
    private function generateSecureToken($length = 32)
    {
        $length = max(8, min(128, (int)$length));
        $bytes  = (int)ceil($length / 2);

        // Prefer the OS CSPRNG
        try {
            if (function_exists('random_bytes')) {
                return substr(bin2hex(random_bytes($bytes)), 0, $length);
            }
        } catch (\Throwable $e) {
            // fall through to OpenSSL
        }

        // OpenSSL fallback with mandatory checks
        if (function_exists('openssl_random_pseudo_bytes')) {
            $crypto_strong = false;                // initialize for static analyzers
            $raw = openssl_random_pseudo_bytes($bytes, $crypto_strong);
            if ($raw !== false && $crypto_strong === true) {
                return substr(bin2hex($raw), 0, $length);
            }
        }

        // Best practice: fail closed (or return false) rather than a weak fallback
        throw new \RuntimeException('No CSPRNG available to generate a secure token.');
    }



    /**
     * Check if debug output is allowed
     * @return bool TRUE if debugging is allowed
     */
    private function isDebugAllowed()
    {
        $mode = (int)($GLOBALS['xoopsConfig']['debug_mode'] ?? 0);
        if (!in_array($mode, [1, 2], true)) {
            return false;
        }

        $level = (int)($GLOBALS['xoopsConfig']['debugLevel'] ?? 0);
        $user = $GLOBALS['xoopsUser'] ?? null;
        $isAdmin = (bool)($GLOBALS['xoopsUserIsAdmin'] ?? false);

        switch ($level) {
            case 2:
                return $isAdmin;
            case 1:
                return $user !== null;
            default:
                return true;
        }
    }

    /**
     * Check if running in production environment
     * @return bool TRUE if in production
     */
    private function isProductionEnvironment()
    {
        // Check explicit production flag
        if (defined('XOOPS_PRODUCTION') && XOOPS_PRODUCTION) {
            return true;
        }

        // Check environment variable
        if (getenv('XOOPS_ENV') === 'production') {
            return true;
        }

        // Check for development indicators
        if ((defined('XOOPS_DEBUG') && XOOPS_DEBUG) ||
            (php_sapi_name() === 'cli') ||
            (isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1'], true))) {
            return false;
        }

        // Default to production for safety
        return true;
    }

    /**
     * Redact sensitive information from SQL queries
     * @param string $sql SQL query string
     * @return string Redacted SQL query
     */
    private function redactSql($sql)
    {
        // Redact quoted strings
        $sql = preg_replace("/'[^']*'/", "'?'", $sql);
        $sql = preg_replace('/"[^"]*"/', '"?"', $sql);
        $sql = preg_replace("/x'[0-9A-Fa-f]+'/", "x'?'", $sql);
        return $sql;
    }

    /**
     * Sanitize string for logging
     * @param string $str String to sanitize
     * @return string Sanitized string
     */
    private function sanitizeForLog($str)
    {
        // Remove control characters
        $str = preg_replace('/[\x00-\x1F\x7F]/', '', $str);
        // Remove BIDI control characters
        $str = preg_replace(self::BIDI_CONTROL_REGEX, '', $str);
        // Normalize whitespace
        $str = preg_replace('/\s+/', ' ', $str);
        // Limit length
        if (function_exists('mb_substr')) {
            return mb_substr($str, 0, self::LOG_CONTEXT_MAX_LENGTH);
        }
        return substr($str, 0, self::LOG_CONTEXT_MAX_LENGTH);
    }

    /**
     * Log database errors with context
     * @param string $message Error message
     * @param string $sql SQL query that failed
     * @param array $context Additional context
     * @param bool $isDebug Whether debug mode is enabled
     */
    private function logDatabaseError($message, $sql, array $context, $isDebug)
    {
        /** @var XoopsMySQLDatabase $db */
        $db = $this->userHandler->db;

        $errorInfo = [
            'message' => $message,
            'user_id' => isset($GLOBALS['xoopsUser']) ? (int)$GLOBALS['xoopsUser']->getVar('uid') : 'anonymous',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Add request context
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $errorInfo['method'] = $this->sanitizeForLog($_SERVER['REQUEST_METHOD']);
            $errorInfo['uri'] = $this->sanitizeRequestUri();
        }

        // Add provided context
        $errorInfo = array_merge($errorInfo, $context);

        // Add database error if available
        if (method_exists($db, 'error')) {
            $errorInfo['db_error'] = $db->error();
        }
        if (method_exists($db, 'errno')) {
            $errorInfo['db_errno'] = $db->errno();
        }

        // Build log message
        $logMessage = $message . ' | Context: ' . json_encode($errorInfo, JSON_UNESCAPED_SLASHES);

        // Add SQL in debug mode only
        if ($isDebug) {
            $logMessage .= ' | SQL: ' . $this->redactSql($sql);
        }

        // Log the error
        if (class_exists('XoopsLogger')) {
            XoopsLogger::getInstance()->handleError(E_USER_WARNING, $logMessage, __FILE__, __LINE__);
        } else {
            error_log($logMessage);
        }
    }

    /**
     * Sanitize request URI for logging
     * @return string Sanitized URI
     */
    private function sanitizeRequestUri()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return 'cli';
        }

        $parts = parse_url($_SERVER['REQUEST_URI']);
        $path = $this->sanitizeForLog($parts['path'] ?? '/');

        if (isset($parts['query'])) {
            parse_str($parts['query'], $queryParams);
            foreach ($queryParams as $key => &$value) {
                if (in_array(strtolower($key), self::SENSITIVE_PARAMS, true)) {
                    $value = 'REDACTED';
                } else {
                    $value = is_array($value) ? '[ARRAY]' : $this->sanitizeForLog((string)$value);
                }
            }
            unset($value);
            $queryString = http_build_query($queryParams);
            return $queryString ? $path . '?' . $queryString : $path;
        }

        return $path;
    }

    /**
     * Log security-related events
     * @param string $event Event description
     * @param array $context Additional context
     */
    private function logSecurityEvent($event, array $context = [])
    {
        $logData = [
            'event' => $event,
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => isset($GLOBALS['xoopsUser']) ? (int)$GLOBALS['xoopsUser']->getVar('uid') : 'anonymous',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];

        $logData = array_merge($logData, $context);
        $message = 'Security Event: ' . json_encode($logData, JSON_UNESCAPED_SLASHES);

        if (class_exists('XoopsLogger')) {
            XoopsLogger::getInstance()->handleError(E_USER_NOTICE, $message, __FILE__, __LINE__);
        } else {
            error_log($message);
        }
    }
}
