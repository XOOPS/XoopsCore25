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
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         class
 * @subpackage      database
 * @since           1.0.0
 * @author          Kazumi Ono <onokazu@xoops.org>
 * @author			Rodney Fulk <redheadedrod@hotmail.com>
 * @version         $Id: mysqldatabase.php 8066 2011-11-06 05:09:33Z beckmi $
 */
defined('XOOPS_ROOT_PATH') or die('Restricted access');

include_once XOOPS_ROOT_PATH . '/class/database/database.php';

/**
 * connection to a mysql database using MySQLi extension
 *
 * @abstract
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2014 XOOPS.org
 * @package class
 * @subpackage database
 */
class XoopsMySQLDatabase extends XoopsDatabase
{
    /**
     * Database connection
     *
     * @var resource
     */
    var $conn;

    /**
     * connect to the database
     *
     * @param bool $selectdb select the database now?
     * @return bool successful?
     */
    public function connect($selectdb = TRUE)
    {
        static $db_charset_set;
        if (!extension_loaded('mysqli')) {
            trigger_error('notrace:mysqli extension not loaded', E_USER_ERROR);
            return FALSE;
        }

        $this->allowWebChanges = ($_SERVER['REQUEST_METHOD'] != 'GET');

        if ($selectdb) {
            $dbname = constant('XOOPS_DB_NAME');
        } else {
            $dbname = '';
        }
        if (XOOPS_DB_PCONNECT == 1) {
            $this->conn = mysqli_connect("p:".XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, $dbname);
        } else {
            $this->conn = mysqli_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, $dbname);
        }

        if (!$this->conn) {
            $this->logger->addQuery('', $this->error(), $this->errno());
            return FALSE;
        }
        if (!isset($db_charset_set) && defined('XOOPS_DB_CHARSET') && XOOPS_DB_CHARSET) {
            $this->queryF("SET NAMES '" . XOOPS_DB_CHARSET . "'");
        }
        $db_charset_set = 1;
        $this->queryF("SET SQL_BIG_SELECTS = 1");
        return TRUE;
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
     * @param resource $result
     * @return array
     */
    public function fetchRow($result)
    {
        return @mysqli_fetch_row($result);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param resource $result
     * @return array
     */
    public function fetchArray($result)
    {
        return @mysqli_fetch_assoc($result);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param resource $result
     * @return array
     */
    public function fetchBoth($result)
    {
        return @mysqli_fetch_array($result,  MYSQLI_BOTH);
    }

    /**
     * XoopsMySQLiDatabase::fetchObjected()
     *
     * @param mixed $result
     * @return
     */
    public function fetchObject($result)
    {
        return @mysqli_fetch_object($result);
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int
     */
    public function getInsertId()
    {
        return mysqli_insert_id($this->conn);
    }

    /**
     * Get number of rows in result
     *
     * @param resource $result
     * @return int
     */
    public function getRowsNum($result)
    {
        return @mysqli_num_rows($result);
    }

    /**
     * Get number of affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->conn);
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
     * @param resource $ query result
     * @return bool TRUE on success or FALSE on failure.
     */
    public function freeRecordSet($result)
    {
        return ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? TRUE : FALSE);
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     *
     * @return bool Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred.
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
     */
    public function quoteString($str)
    {
        return $this->quote($str);
    }

    /**
     * Quotes a string for use in a query.
     */
    public function quote($string)
    {
        return "'" . str_replace("\\\"", '"', str_replace("\\&quot;", '&quot;', mysqli_real_escape_string($this->conn, $string))) . "'";
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
        return mysqli_real_escape_string($this->conn, $string);
    }

    /**
     * perform a query on the database
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    public function queryF($sql, $limit = 0, $start = 0)
    {
        if (!empty($limit)) {
            if (empty($start)) {
                $start = 0;
            }
            $sql = $sql . ' LIMIT ' . (int) $start . ', ' . (int) $limit;
        }
        $this->logger->startTime('query_time');
        $result = mysqli_query( $this->conn, $sql);
        $this->logger->stopTime('query_time');
        $query_time = $this->logger->dumpTime('query_time', TRUE);
        if ($result) {
            $this->logger->addQuery($sql, NULL, NULL, $query_time);
            return $result;
        } else {
            $this->logger->addQuery($sql, $this->error(), $this->errno(), $query_time);
            return FALSE;
        }
    }

    /**
     * perform a query
     *
     * This method is empty and does nothing! It should therefore only be
     * used if nothing is exactly what you want done! ;-)
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @abstract
     */
    public function query($sql, $limit = 0, $start = 0)
    {
    }

    /**
     * perform queries from SQL dump file in a batch
     *
     * @param string $file file path to an SQL dump file
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
     */
    public function queryFromFile($file)
    {
        if (FALSE !== ($fp = fopen($file, 'r'))) {
            include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
            $sql_queries = trim(fread($fp, filesize($file)));
            SqlUtility::splitMySqlFile($pieces, $sql_queries);
            foreach ($pieces as $query) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery(trim($query), $this->prefix());
                if ($prefixed_query != FALSE) {
                    $this->query($prefixed_query[0]);
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get field name
     *
     * @param resource $result query result
     * @param int $ numerical field index
     * @return string
     */
    public function getFieldName($result, $offset)
    {
        return $result->fetch_field_direct($offset)->name;
    }

    /**
     * Get field type
     *
     * @param resource $result query result
     * @param int $offset numerical field index
     * @return string
     */
    public function getFieldType($result, $offset)
    {
        $typecode = $result->fetch_field_direct($offset)->type;
        switch($typecode) {
            case MYSQLI_TYPE_DECIMAL:
            case MYSQLI_TYPE_NEWDECIMAL:
                $type='decimal';
                break;
            case MYSQLI_TYPE_BIT:
                $type='bit';
                break;
            case MYSQLI_TYPE_TINY:
            case MYSQLI_TYPE_CHAR:
                $type='tinyint';
                break;
            case MYSQLI_TYPE_SHORT:
                $type='smallint';
                break;
            case MYSQLI_TYPE_LONG:
                $type='int';
                break;
            case MYSQLI_TYPE_FLOAT:
                $type='float';
                break;
            case MYSQLI_TYPE_DOUBLE:
                $type='double';
                break;
            case MYSQLI_TYPE_NULL:
                $type='NULL';
                break;
            case MYSQLI_TYPE_TIMESTAMP:
                $type='timestamp';
                break;
            case MYSQLI_TYPE_LONGLONG:
                $type='bigint';
                break;
            case MYSQLI_TYPE_INT24:
                $type='mediumint';
                break;
            case MYSQLI_TYPE_NEWDATE:
            case MYSQLI_TYPE_DATE:
                $type='date';
                break;
            case MYSQLI_TYPE_TIME:
                $type='time';
                break;
            case MYSQLI_TYPE_DATETIME:
                $type='datetime';
                break;
            case MYSQLI_TYPE_YEAR:
                $type='year';
                break;
            case MYSQLI_TYPE_INTERVAL:
                $type='interval';
                break;
            case MYSQLI_TYPE_ENUM:
                $type='enum';
                break;
            case MYSQLI_TYPE_SET:
                $type='set';
                break;
            case MYSQLI_TYPE_TINY_BLOB:
                $type='tinyblob';
                break;
            case MYSQLI_TYPE_MEDIUM_BLOB:
                $type='mediumblob';
                break;
            case MYSQLI_TYPE_LONG_BLOB:
                $type='longblob';
                break;
            case MYSQLI_TYPE_BLOB:
                $type='blob';
                break;
            case MYSQLI_TYPE_VAR_STRING:
                $type='varchar';
                break;
            case MYSQLI_TYPE_STRING:
                $type='char';
                break;
            case MYSQLI_TYPE_GEOMETRY:
                $type='geometry';
                break;
            default:
                $type='unknown';
                break;
        }
        return $type;
    }

    /**
     * Get number of fields in result
     *
     * @param resource $result query result
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
}

/**
 * Safe Connection to a MySQL database.
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 * @subpackage database
 */
class XoopsMySQLDatabaseSafe extends XoopsMySQLDatabase
{
    /**
     * perform a query on the database
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    public function query($sql, $limit = 0, $start = 0)
    {
        return $this->queryF($sql, $limit, $start);
    }
}

/**
 * Read-Only connection to a MySQL database.
 *
 * This class allows only SELECT queries to be performed through its
 * {@link query()} method for security reasons.
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package class
 * @subpackage database
 */
class XoopsMySQLDatabaseProxy extends XoopsMySQLDatabase
{
    /**
     * perform a query on the database
     *
     * this method allows only SELECT queries for safety.
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if unsuccessful
     */
    public function query($sql, $limit = 0, $start = 0)
    {
        $sql = ltrim($sql);
        if (!$this->allowWebChanges && strtolower(substr($sql, 0, 6)) != 'select') {
            trigger_error('Database updates are not allowed during processing of a GET request', E_USER_WARNING);
            return FALSE;
        }

        return $this->queryF($sql, $limit, $start);
    }
}

?>
