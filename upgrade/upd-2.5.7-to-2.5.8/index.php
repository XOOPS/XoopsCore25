<?php

use Xmf\Database\Tables;
use Xmf\Key\Basic;
use Xmf\Key\FileStorage;

/**
 * Upgrade from 2.5.7 to 2.5.8
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.5.8
 * @author           XOOPS Team
 */
class Upgrade_258 extends XoopsUpgrade
{
    /**
     * __construct
     *
     * make sure we have XMF active
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array(
            'users_pass',
            'com_ip',
            'sess_ip',
            'online_ip',
        );
    }

    /**
     * Return the length of a database table column
     *
     * @param string $table  table name
     * @param string $column column name
     *
     * @return int column length or zero on error
     */
    private function getColumnLength($table, $column)
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
            $db->escape($column)
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
        return 0;
    }

    /**
     * Expand users password column to varchar(255) to accommodate bcrypt password_hash
     *
     * @return bool
     */
    public function check_users_pass()
    {
        return (bool) ($this->getColumnLength('users', 'pass') >= 255);
    }

    /**
     * @return bool
     */
    public function apply_users_pass()
    {
        $migrate = new Tables();
        $migrate->useTable('users');
        // kill any indexes based on pass column
        $indexes = $migrate->getTableIndexes('users');
        foreach ($indexes as $name => $def) {
            if (preg_match('/\b(pass)\b/', $def['columns'])) {
                $migrate->dropIndex($name, 'users');
            }
        }

        $migrate->alterColumn('users', 'pass', "varchar(255) NOT NULL DEFAULT ''");
        return $migrate->executeQueue(true);
    }

    /**
     * Expand xoopscomments IP address column varchar(45) to accommodate IPV6
     *
     * @return bool
     */
    public function check_com_ip()
    {
        return (bool) ($this->getColumnLength('xoopscomments', 'com_ip') >= 45);
    }

    /**
     * @return bool
     */
    public function apply_com_ip()
    {
        $migrate = new Tables();
        $migrate->useTable('xoopscomments');
        $migrate->alterColumn('xoopscomments', 'com_ip', "varchar(45) NOT NULL DEFAULT ''");
        return $migrate->executeQueue(true);
    }

    /**
     * Expand session IP address column varchar(45) to accommodate IPV6
     *
     * @return bool
     */
    public function check_sess_ip()
    {
        return (bool) ($this->getColumnLength('session', 'sess_ip') >= 45);
    }

    /**
     * @return bool
     */
    public function apply_sess_ip()
    {
        $migrate = new Tables();
        $migrate->useTable('session');
        $migrate->alterColumn('session', 'sess_ip', "varchar(45) NOT NULL DEFAULT ''");
        return $migrate->executeQueue(true);
    }

    /**
     * Expand online IP address column varchar(45) to accommodate IPV6
     *
     * @return bool
     */
    public function check_online_ip()
    {
        return (bool) ($this->getColumnLength('online', 'online_ip') >= 45);
    }

    /**
     * @return bool
     */
    public function apply_online_ip()
    {
        $migrate = new Tables();
        $migrate->useTable('online');
        $migrate->alterColumn('online', 'online_ip', "varchar(45) NOT NULL DEFAULT ''");
        return $migrate->executeQueue(true);
    }
}

$upg = new Upgrade_258();
return $upg;
