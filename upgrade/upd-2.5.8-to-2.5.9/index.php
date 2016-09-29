<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.7 to 2.5.8
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          Upgrade
 * @since            2.5.9
 * @author           XOOPS Team
 */
class Upgrade_259 extends XoopsUpgrade
{
    public $tasks = array(
        'sess_id',
    );

    /**
     * __construct
     *
     * make sure we have XMF active
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
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
        if ($result) {
            $row = $db->fetchRow($result);
            if ($row) {
                $columnLength = $row[0];
                return (int) $columnLength;
            }
        }
        return 0;
    }

    /**
     * In PHP 7.1 Session ID length could be any length between 22 and 256
     *
     * @return bool
     */
    public function check_sess_id()
    {
        return (bool) ($this->getColumnLength('session', 'sess_id') >= 256);
    }

    /**
     * Expand session id column to varchar(256) to accommodate expanded size possible in PHP 7.1
     * Force ascii character set to prevent key length issues.
     *
     * @return bool
     */
    public function apply_sess_id()
    {
        $migrate = new Tables();
        $migrate->useTable('session');
        $migrate->alterColumn('session', 'sess_id', "varchar(256) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT ''");
        return $migrate->executeQueue(true);
    }
}

$upg = new Upgrade_259();
return $upg;
