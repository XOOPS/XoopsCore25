<?php
/**
 * MySQL access using MySQLi extension
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
 * @package             class
 * @subpackage          database
 * @since               1.0.0
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @author              Rodney Fulk <redheadedrod@hotmail.com>
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

include_once XOOPS_ROOT_PATH . '/class/database/database.php';

/**
 * Connection to a MySQL database using MySQLi extension
 */
abstract class XoopsMySQLDatabase extends XoopsDatabase
{
    /**
     * Strict guard is active in dev or when XOOPS debug mode is on.
     */
    private function isStrict(): bool
    {
        // Respect environment switch and also auto-enable when XOOPS debug is on
        $envStrict  = (defined('XOOPS_DB_STRICT') && XOOPS_DB_STRICT);
        $xoopsDebug = !empty($GLOBALS['xoopsConfig']['debug_mode']);
        return $envStrict || $xoopsDebug;
    }
    /**
     * Database connection
     *
     * @var XoopsDatabase|mysqli
     */
    public $conn;

    /**
     * connect to the database
     *
     * @param bool $selectdb select the database now?
     * @return bool successful?
     */
    public function connect($selectdb = true)
    {
        if (!extension_loaded('mysqli')) {
            throw new \Exception('notrace:mysqli extension not loaded');

            return false;
        }

        $this->allowWebChanges = ($_SERVER['REQUEST_METHOD'] !== 'GET');

        if ($selectdb) {
            $dbname = constant('XOOPS_DB_NAME');
        } else {
            $dbname = '';
        }
        mysqli_report(MYSQLI_REPORT_OFF);
        if (XOOPS_DB_PCONNECT == 1) {
            $this->conn = new mysqli('p:' . XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, $dbname);
        } else {
            $this->conn = new mysqli(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, $dbname);
        }

        // errno is 0 if connect was successful
        if (0 !== $this->conn->connect_errno) {
            return false;
        }

        if (defined('XOOPS_DB_CHARSET') && ('' !== XOOPS_DB_CHARSET)) {
            // $this->queryF("SET NAMES '" . XOOPS_DB_CHARSET . "'");
            $this->conn->set_charset(XOOPS_DB_CHARSET);
        }
        $this->queryF('SET SQL_BIG_SELECTS = 1');

        return true;
    }

    /**
     * generate an ID for a new row
     *
     * This is for compatibility only. Will always return 0, because MySQL supports
     * autoincrement for primary keys.
     *
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because mysql has support for autoincrement
     */
    public function genId($sequence)
    {
        return 0; // will use auto_increment
    }

    /**
     * Get a result row as an enumerated array
     *
     * @param \mysqli_result $result
     *
     * @return array|false false on end of data
     */
    public function fetchRow($result)
    {
        $row = @mysqli_fetch_row($result);
        return $row ?? false;
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param \mysqli_result $result
     *
     * @return array|false false on end of data
     */
    public function fetchArray($result)
    {
        $row = @mysqli_fetch_assoc($result);
        return $row ?? false;

    }

    /**
     * Fetch a result row as an associative array
     *
     * @param \mysqli_result $result
     *
     * @return array|false false on end of data
     */
    public function fetchBoth($result)
    {
        $row = @mysqli_fetch_array($result, MYSQLI_BOTH);
        return $row ?? false;
    }

    /**
     * XoopsMySQLDatabase::fetchObject()
     *
     * @param \mysqli_result $result
     * @return stdClass|false false on end of data
     */
    public function fetchObject($result)
    {
        $row = @mysqli_fetch_object($result);
        return $row ?? false;
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int|string
     */
    public function getInsertId()
    {
        return mysqli_insert_id($this->conn);
    }

    /**
     * Get number of rows in result
     *
     * @param \mysqli_result $result
     *
     * @return int
     */
    public function getRowsNum($result)
    {
        return (int)@mysqli_num_rows($result);
    }

    /**
     * Get number of affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return (int)mysqli_affected_rows($this->conn);
    }

    /**
     * Close MySQL connection
     *
     * @return void
     */
    public function close()
    {
        mysqli_close($this->conn);
    }

    /**
     * will free all memory associated with the result identifier result.
     *
     * @param \mysqli_result $result result
     *
     * @return void
     */
    public function freeRecordSet($result)
    {
        mysqli_free_result($result);
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     *
     * @return string Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred.
     */
    public function error()
    {
        return @mysqli_error($this->conn);
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation
     *
     * @return int Returns the error number from the last MySQL function, or 0 (zero) if no error occurred.
     */
    public function errno()
    {
        return @mysqli_errno($this->conn);
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     *
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     * @deprecated : delegate to exec().
     */
    public function quoteString($str)
    {

        if (is_object($this->logger)) {
            $this->logger->addDeprecated(__METHOD__ . " is deprecated since XOOPS 2.5.12, please use 'quote()' instead.");
        }

        return $this->quote($str);
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string $string string to quote/escape for use in query
     *
     * @return string
     */
    public function quote($string)
    {
        $quoted = $this->escape($string);
        return "'{$quoted}'";
    }

    /**
     * Escapes a string for use in a query. Does not add surrounding quotes.
     *
     * @param string $string string to escape
     *
     * @return string
     */
    public function escape($string)
    {
        return mysqli_real_escape_string($this->conn, (string) $string);
    }

    /**
     * perform a query on the database
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     * @return mysqli_result|bool query result or FALSE if successful
     *                      or TRUE if successful and no result
     */
    public function queryF($sql, $limit = 0, $start = 0)
    {
        if (!empty($limit)) {
            if (empty($start)) {
                $start = 0;
            }
            $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
        }
        $this->logger->startTime('query_time');
        $result = mysqli_query($this->conn, $sql);
        $this->logger->stopTime('query_time');
        $t = $this->logger->dumpTime('query_time', true);

        if ($result) {
            $this->logger->addQuery($sql, null, null, $t);
            return $result;             // mysqli_result for SELECT, true for writes
        } else {
            $this->logger->addQuery($sql, $this->error(), $this->errno(), $t);
            return false;
        }
    }

    /**
     * perform a query
     *
     * This method is empty and does nothing! It should therefore only be
     * used if nothing is exactly what you want done! ;-)
     *
     * @param string $sql   a valid MySQL query
     * @param int|null    $limit number of records to return
     * @param int|null    $start offset of first record to return
     *
     * @return \mysqli_result|bool false on failure; true only if a write slipped through (BC)
     */
    public function query(string $sql, ?int $limit = null, ?int $start = null)
    {
        // Dev-only guard: query() should be read-like
        if ($this->isStrict()) {
            if (!preg_match('/^\s*(SELECT|WITH|SHOW|DESCRIBE|EXPLAIN)\b/i', $sql)) {
                if (is_object($this->logger)) {
                    $this->logger->addExtra('DB', 'query() called with a mutating statement; use exec()');
                }
                trigger_error('query() called with a mutating statement; use exec()', E_USER_WARNING);
                // continue for BC
            }
        }

        // Pagination if requested (null = no pagination)
        if ($limit !== null) {
            $start = max(0, $start ?? 0);
            $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . $start;
        }

        // Connection type guard for static analyzers and safety
        if (!($this->conn instanceof \mysqli)) {
            trigger_error('Invalid or uninitialized mysqli connection', E_USER_WARNING);
            return false;
        }

        // Timing + execution
        if (is_object($this->logger)) $this->logger->startTime('query_time');
        $res = \mysqli_query($this->conn, $sql);
        if (is_object($this->logger)) {
            $this->logger->stopTime('query_time');
            $t = $this->logger->dumpTime('query_time', true);
        } else {
            $t = 0;
        }

        if ($res === false) {
            if (is_object($this->logger)) {
                $this->logger->addQuery($sql, $this->error(), $this->errno(), $t);
            }
            return false;
        }

        // Log success
        if (is_object($this->logger)) {
            $this->logger->addQuery($sql, null, null, $t);
        }

        // If a write slipped into query() (rare), mysqli returns true — keep BC
        return $res;
    }


    /**
     * perform queries from SQL dump file in a batch
     *
     * @param string $file file path to an SQL dump file
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
     */
    public function queryFromFile($file)
    {
        if (false !== ($fp = fopen($file, 'r'))) {
            include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
            $sql_queries = trim(fread($fp, filesize($file)));
            SqlUtility::splitMySqlFile($pieces, $sql_queries);
            foreach ($pieces as $query) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery(trim($query), $this->prefix());
                if ($prefixed_query != false) {
                    $this->query($prefixed_query[0]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get field name
     *
     * @param \mysqli_result $result query result
     * @param int           $offset numerical field index
     *
     * @return string
     */
    public function getFieldName($result, $offset)
    {
        return $result->fetch_field_direct($offset)->name;
    }

    /**
     * Get field type
     *
     * @param \mysqli_result $result query result
     * @param int           $offset numerical field index
     *
     * @return string
     */
    public function getFieldType($result, $offset)
    {
        $typecode = $result->fetch_field_direct($offset)->type;
        switch ($typecode) {
            case MYSQLI_TYPE_DECIMAL:
            case MYSQLI_TYPE_NEWDECIMAL:
                $type = 'decimal';
                break;
            case MYSQLI_TYPE_BIT:
                $type = 'bit';
                break;
            case MYSQLI_TYPE_TINY:
            case MYSQLI_TYPE_CHAR:
                $type = 'tinyint';
                break;
            case MYSQLI_TYPE_SHORT:
                $type = 'smallint';
                break;
            case MYSQLI_TYPE_LONG:
                $type = 'int';
                break;
            case MYSQLI_TYPE_FLOAT:
                $type = 'float';
                break;
            case MYSQLI_TYPE_DOUBLE:
                $type = 'double';
                break;
            case MYSQLI_TYPE_NULL:
                $type = 'NULL';
                break;
            case MYSQLI_TYPE_TIMESTAMP:
                $type = 'timestamp';
                break;
            case MYSQLI_TYPE_LONGLONG:
                $type = 'bigint';
                break;
            case MYSQLI_TYPE_INT24:
                $type = 'mediumint';
                break;
            case MYSQLI_TYPE_NEWDATE:
            case MYSQLI_TYPE_DATE:
                $type = 'date';
                break;
            case MYSQLI_TYPE_TIME:
                $type = 'time';
                break;
            case MYSQLI_TYPE_DATETIME:
                $type = 'datetime';
                break;
            case MYSQLI_TYPE_YEAR:
                $type = 'year';
                break;
            case MYSQLI_TYPE_INTERVAL:
                $type = 'interval';
                break;
            case MYSQLI_TYPE_ENUM:
                $type = 'enum';
                break;
            case MYSQLI_TYPE_SET:
                $type = 'set';
                break;
            case MYSQLI_TYPE_TINY_BLOB:
                $type = 'tinyblob';
                break;
            case MYSQLI_TYPE_MEDIUM_BLOB:
                $type = 'mediumblob';
                break;
            case MYSQLI_TYPE_LONG_BLOB:
                $type = 'longblob';
                break;
            case MYSQLI_TYPE_BLOB:
                $type = 'blob';
                break;
            case MYSQLI_TYPE_VAR_STRING:
                $type = 'varchar';
                break;
            case MYSQLI_TYPE_STRING:
                $type = 'char';
                break;
            case MYSQLI_TYPE_GEOMETRY:
                $type = 'geometry';
                break;
            default:
                $type = 'unknown';
                break;
        }

        return $type;
    }

    /**
     * Get number of fields in result
     *
     * @param \mysqli_result $result query result
     *
     * @return int
     */
    public function getFieldsNum($result)
    {
        return mysqli_num_fields($result);
    }

    /**
     * getServerVersion get version of the mysql server
     *
     * @return string
     */
    public function getServerVersion()
    {
        return mysqli_get_server_info($this->conn);
    }

    /**
     * Test the passed result to determine if it is a valid result set
     *
     * @param mixed $result value to test
     *
     * @return bool true if $result is a database result set, otherwise false
     */
    public function isResultSet($result)
    {
        return is_a($result, 'mysqli_result');
    }

    public function exec(string $sql): bool
    {
        // Dev-only guard: exec() should be write-like
        if ($this->isStrict()) {
            if (preg_match('/^\s*(SELECT|WITH|SHOW|DESCRIBE|EXPLAIN)\b/i', $sql)) {
                if (is_object($this->logger)) {
                    $this->logger->addExtra('DB', 'exec() called with a read-only statement');
                }
                trigger_error('exec() called with a read-only statement', E_USER_WARNING);
                // continue for BC
            }
        }

        if (!($this->conn instanceof \mysqli)) {
            trigger_error('Invalid or uninitialized mysqli connection', E_USER_WARNING);
            return false;
        }

        // Timing + execution
        if (is_object($this->logger)) $this->logger->startTime('query_time');
        $res = \mysqli_query($this->conn, $sql);
        if (is_object($this->logger)) {
            $this->logger->stopTime('query_time');
            $t = $this->logger->dumpTime('query_time', true);
        } else {
            $t = 0;
        }

        if ($res === false) {
            if (is_object($this->logger)) {
                $this->logger->addQuery($sql, $this->error(), $this->errno(), $t);
        }
            return false;
        }

        // If someone passes a SELECT by mistake, mysqli returns a result; free it and treat as success (BC)
        if ($res instanceof \mysqli_result) {
                \mysqli_free_result($res);
        }

        if (is_object($this->logger)) {
            $this->logger->addQuery($sql, null, null, $t);
    }
        return true;
    }
}

/**
 * Safe Connection to a MySQL database.
 *
 * Delegates to parent; signature matches parent for LSP.
 */
class XoopsMySQLDatabaseSafe extends XoopsMySQLDatabase
{
    /**
     * perform a query on the database
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     * @return mysqli_result|bool query result or FALSE if successful
     *                      or TRUE if successful and no result
     */
    public function query($sql, $limit = 0, $start = 0)
    {
        return parent::query($sql, $limit ?: null, $start ?: null);
    }
}

/**
 * Read-Only connection to a MySQL database.
 *
 * This class allows only SELECT queries to be performed through its
 * {@link query()} method for security reasons.
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @package             class
 * @subpackage          database
 */
class XoopsMySQLDatabaseProxy extends XoopsMySQLDatabase
{
    /**
     * perform a query on the database
     *
     * this method allows only SELECT queries for safety.
     *
     * @param string $sql   a valid MySQL query
     * @param int    $limit number of records to return
     * @param int    $start offset of first record to return
     *
     * @return mysqli_result|bool query result or FALSE if successful
     *                      or TRUE if successful and no result
     */
    public function query(string $sql, ?int $limit = null, ?int $start = null)
    {
        $sql = ltrim($sql);
        if (!$this->allowWebChanges && stripos($sql, 'select') !== 0) {
            trigger_error('Database updates are not allowed during processing of a GET request', E_USER_WARNING);

            return false;
        }
        // Execute via queryF() to preserve legacy path (and LIMIT semantics)
        if ($limit !== null) {
            $start = max(0, $start ?? 0);
            return $this->queryF($sql, (int)$limit, (int)$start);
    }
        return $this->queryF($sql);
    }

}
