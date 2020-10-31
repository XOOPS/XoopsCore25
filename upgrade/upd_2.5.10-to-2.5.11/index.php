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
        $this->tasks = array('qmail', 'smtpsecure');
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
        $migrate->insert('configoption',
            array('confop_name' => 'qmail', 'confop_value' => 'qmail', 'conf_id' => 64));
        return $migrate->executeQueue(true);
    }
    
    /**
     * Add smtpsecure support
     *
     * phpMailer has SMTPSecure support
     * This will allow webmasters to set SMTPSecure if it is provisioned on server.
     *
     * @return bool
     */
    public function check_smtpsecure()
    {
        /* @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $table = $db->prefix('config');

        $sql = sprintf(
            'SELECT count(*) FROM `%s` '
            . "WHERE `conf_modid` = 0 AND `conf_catid` = 0 AND `confop_name` = 'smtpsecure'",
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
     * Add smtpsecure support
     *
     * phpMailer has SMTPSecure support
     * This will allow webmasters to set SMTPSecure if it is provisioned on server.
     *
     * @return bool
     */
    public function apply_smtpsecure()
    {
        /* @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        
        $configTable = $db->prefix('config');
        $sql = "INSERT INTO `{$configTable}`";
        $sql .= " (`conf_modid`, `conf_catid`, `conf_name`, `conf_title`, `conf_value`, `conf_desc`, `conf_formtype`, `conf_valuetype`, `conf_order`)";
        $sql .= " VALUES";
        $sql .= " (0, 6, 'smtpsecure', '_MD_AM_SMTPSECURE', '', '_MD_AM_SMTPSECUREDESC', 'select', 'text', 9)";
        if (!$db->queryF($sql)) {
            return false;
        }
        $conf_id = $db->getInsertId();
        
        $configoptionTable = $db->prefix('configconfigoption');
        $sql = "INSERT INTO `{$configoptionTable}`";
        $sql .= " (`confop_name`, `confop_value`, `conf_id`)";
        $sql .= " VALUES";
        $sql .= " ('', '', {$conf_id})";
        if (!$db->queryF($sql)) {
            return false;
        }
        $sql = "INSERT INTO `{$configoptionTable}`";
        $sql .= " (`confop_name`, `confop_value`, `conf_id`)";
        $sql .= " VALUES";
        $sql .= " ('SSL', 'ssl', {$conf_id})";
        if (!$db->queryF($sql)) {
            return false;
        }
        $sql = "INSERT INTO `{$configoptionTable}`";
        $sql .= " (`confop_name`, `confop_value`, `conf_id`)";
        $sql .= " VALUES";
        $sql .= " ('TLS', 'tls', {$conf_id})";
        if (!$db->queryF($sql)) {
            return false;
        }

        return true;        
    }
}

$upg = new Upgrade_2511();
return $upg;
