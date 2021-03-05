<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.10 to 2.5.11
 *
 * @copyright    (c) 2000-2021 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          Upgrade
 * @since            2.5.11
 * @author           XOOPS Team
 */
class Upgrade_2511 extends XoopsUpgrade
{
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array(
            'bannerintsize',
            'qmail',
        );
        $this->usedFiles = array();
    }


    /**
     * Determine if columns are declared mediumint, and if
     * so, queue ddl to alter to int.
     *
     * @param $migrate           \Xmf\Database\Tables
     * @param $bannerTableName   string
     * @param $bannerColumnNames string[] array of columns to check
     *
     * @return integer count of queue items added
     */
    protected function fromMediumToInt(Tables $migrate, $bannerTableName, $bannerColumnNames)
    {
        $migrate->useTable($bannerTableName);
        $count = 0;
        foreach ($bannerColumnNames as $column) {
            $attributes = $migrate->getColumnAttributes($bannerTableName, $column);
            if (0 === strpos(trim($attributes), 'mediumint')) {
                $count++;
                $migrate->alterColumn($bannerTableName, $column, 'int(10) UNSIGNED NOT NULL DEFAULT \'0\'');
            }
        }
        return $count;
    }

    private $bannerTableName = 'banner';
    private $bannerColumnNames = array('impmade', 'clicks');

    /**
     * Increase count columns from mediumint to int
     *
     * @return bool true if patch IS applied, false if NOT applied
     */
    public function check_bannerintsize()
    {
        $migrate = new Tables();
        $count = $this->fromMediumToInt($migrate, $this->bannerTableName, $this->bannerColumnNames);

        return $count==0;
    }

    /**
     * Increase count columns from mediumint to int (Think BIG!)
     *
     * @return bool true if applied, false if failed
     */
    public function apply_bannerintsize()
    {
        $migrate = new \Xmf\Database\Tables();

        $count = $this->fromMediumToInt($migrate, $this->bannerTableName, $this->bannerColumnNames);

        $result = $migrate->executeQueue(true);
        if (false === $result) {
            $this->logs[] = sprintf('Migration of %s table failed. Error: %s - %s' .
                $this->bannerTableName,
                $migrate->getLastErrNo(),
                $migrate->getLastError()
            );
            return false;
        }

        return $count!==0;
    }

    /**
     * Add qmail as valid mailmethod
     *
     * @return bool
     */
    public function check_qmail()
    {
        /* @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $table = $db->prefix('configoption');

        $sql = sprintf(
            'SELECT count(*) FROM `%s` '
            . "WHERE `conf_id` = 64 AND `confop_name` = 'qmail'",
            $db->escape($table)
        );

        /** @var mysqli_result $result */
        $result = $db->query($sql);
        if ($result) {
            $row = $db->fetchRow($result);
            if ($row) {
                $count = $row[0];
                return (0 === (int) $count) ? false : true;
            }
        }
        return false;
    }

    /**
     * Add qmail as valid mailmethod
     *
     * phpMailer has qmail support, similar to but slightly different than sendmail
     * This will allow webmasters to utilize qmail if it is provisioned on server.
     *
     * @return bool
     */
    public function apply_qmail()
    {
        $migrate = new Tables();
        $migrate->useTable('configoption');
        $migrate->insert(
            'configoption',
            array('confop_name' => 'qmail', 'confop_value' => 'qmail', 'conf_id' => 64)
        );
        return $migrate->executeQueue(true);
    }
}

return new Upgrade_2511();
