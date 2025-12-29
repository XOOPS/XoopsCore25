<?php
/**
 * Abstract base class for XOOPS Database access classes
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
 * @subpackage          database
 * @since               1.0.0
 * @author              Kazumi Ono <onokazu@xoops.org>
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('Restricted access');
}

/**
 * make sure this is only included once!
 */
if (defined('XOOPS_C_DATABASE_INCLUDED')) {
    return null;
}

define('XOOPS_C_DATABASE_INCLUDED', 1);

/**
 * Abstract base class for Database access classes
 *
 * @abstract
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @package    kernel
 * @subpackage database
 */
abstract class XoopsDatabase
{
    /**
     * Prefix for tables in the database
     *
     * @var string
     */
    public $prefix = '';

    /**
     * reference to a {@link XoopsLogger} object
     *
     * @see XoopsLogger
     * @var object XoopsLogger
     */
    public $logger;

    /**
     * If statements that modify the database are selected
     *
     * @var boolean
     */
    public $allowWebChanges = false;

    /**
     * XoopsDatabase constructor.
     */
    public function __construct()
    {
        // exit('Cannot instantiate this class directly');
    }

    /**
     * assign a {@link XoopsLogger} object to the database
     *
     * @see XoopsLogger
     * @param XoopsLogger $logger reference to a {@link XoopsLogger} object
     */

    public function setLogger(XoopsLogger $logger)
    {
        $this->logger = &$logger;
    }

    /**
     * set the prefix for tables in the database
     *
     * @param string $value table prefix
     */
    public function setPrefix($value)
    {
        $this->prefix = $value;
    }

    /**
     * attach the prefix.'_' to a given tablename
     *
     * if tablename is empty, only prefix will be returned
     *
     * @param  string $tablename tablename
     * @return string prefixed tablename, just prefix if tablename is empty
     */
    public function prefix($tablename = '')
    {
        if ($tablename != '') {
            return $this->prefix . '_' . $tablename;
        } else {
            return $this->prefix;
        }
    }

    /**
     * Test the passed result to determine if it is a valid result set
     *
     * @param mixed $result value to test
     *
     * @return bool true if $result is a database result set, otherwise false
     */
    abstract public function isResultSet($result);

    /**
     * Return a human-readable description of the last DB error.
     * Subclasses must override to provide engine-specific details.
     *
     * @return string Error message or empty string if no error.
     */
    abstract public function error();

    /**
     * Return an engine-specific error code for the last DB error.
     * Subclasses must override to provide engine-specific details.
     *
     * @return int Error code (e.g., MySQL errno) or 0 if no error.
     */
    abstract public function errno();

    /**
     * Executes a SELECT-like statement and returns a result handle.
     * If $limit is null, no LIMIT/OFFSET is applied.
     * If $limit is not null and $start is null, $start defaults to 0.
     *
     * @param string   $sql
     * @param int|null $limit
     * @param int|null $start
     * @return mixed   driver result/resource|false
     */
    abstract public function query(string $sql, ?int $limit = null, ?int $start = null);

    /**
     * Executes a mutating statement (INSERT/UPDATE/DELETE/DDL).
     * New code should prefer exec().
     */
    abstract public function exec(string $sql);

    /**
     * Legacy alias for writes; kept for BC.
     * @deprecated
     */
    public function queryF(string $sql)
    {
        // Deprecated: delegate to exec().
        // Optional: behind a dev flag, emit a deprecation warning.
//        if (is_object($GLOBALS['xoopsLogger'])) {
//            $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated since XOOPS 2.5.12, please use 'exec()' instead.");
//        }

        return $this->exec($sql);
    }


    /**
         * Canonical quoting API — returns a single-quoted SQL literal.
         * MUST include surrounding single quotes and perform driver-appropriate escaping.
         *
         * @param string $str Raw, unescaped string
         * @return string Quoted SQL literal, e.g. 'O''Reilly' for input "O'Reilly"
         */

        abstract public function quote(string $str);

        /**
          Legacy alias. Returns a single-quoted SQL literal.
         * @deprecated Use quote()
         */
    public function quoteString(string $str)
    {
           if (is_object($GLOBALS['xoopsLogger'])) {
             $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ ." is deprecated since XOOPS 2.5.12, use quote() instead.");
         }
        return $this->quote($str);
    }

    /**
     * Helper: normalize pagination semantics for drivers.
     * - limit=null  => [null, null]  (no pagination)
     * - limit>=0    => [limit, max(0, start??0)]
     */
    final protected function normalizeLimitStart(?int $limit, ?int $start): array
    {
        if ($limit === null) {
            return [null, null];
        }
        $limit = max(0, $limit);
        $start = max(0, $start ?? 0);
        return [$limit, $start];
    }
}

/**
 * Only for backward compatibility
 *
 * @deprecated
 */
class Database
{
    /**
     * @return object
     */
    public function getInstance()
    {
        if (is_object($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . " is deprecated since XOOPS 2.5.4, please use 'XoopsDatabaseFactory::getDatabaseConnection();' instead.");
        }
        $inst = XoopsDatabaseFactory::getDatabaseConnection();

        return $inst;
    }
}
