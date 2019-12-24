<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.10 to 2.5.11
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2019 XOOPS Project (https://xoops.org)
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
        $this->tasks = array('qmail');
        $this->usedFiles = array();
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

        /** @var mysqli_result|false $result */
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
        $migrate->insert('configoption',
            array('confop_name' => 'qmail', 'confop_value' => 'qmail', 'conf_id' => 64));
        return $migrate->executeQueue(true);
    }
}

$upg = new Upgrade_2511();
return $upg;
