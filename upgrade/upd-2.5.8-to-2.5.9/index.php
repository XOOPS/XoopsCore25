<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.8 to 2.5.9
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          Upgrade
 * @since            2.5.9
 * @author           XOOPS Team
 */
class Upgrade_259 extends XoopsUpgrade
{
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('sess_id', 'mainfile', 'zaplegacy');
        $this->usedFiles = array(
            'mainfile.php',
            XOOPS_VAR_PATH . '/data/secure.php',
            'modules/system/themes/legacy/legacy.php'
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

    /**
     * Copy a configuration file from template, then rewrite with actual configuration values
     *
     * @param string[] $vars       config values
     * @param string   $path       directory path where files reside
     * @param string   $sourceName template file name
     * @param string   $fileName   configuration file name
     *
     * @return true|string true on success, error message on failure
     */
    protected function writeConfigurationFile($vars, $path, $sourceName, $fileName)
    {
        $path .= '/';
        clearstatcache();
        if (!$inFile = fopen($path . $sourceName, 'r')) {
            return sprintf(_FILE_ACCESS_ERROR, $sourceName);
        } else {
            $content = fread($inFile, filesize($path . $sourceName));
            fclose($inFile);

            foreach ($vars as $key => $val) {
                if (is_int($val) && preg_match("/(define\()([\"'])({$key})\\2,\s*(\d+)\s*\)/", $content)) {
                    $content = preg_replace("/(define\()([\"'])({$key})\\2,\s*(\d+)\s*\)/", "define('{$key}', {$val})", $content);
                } elseif (preg_match("/(define\()([\"'])({$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", $content)) {
                    $val = str_replace('$', '\$', addslashes($val));
                    $content = preg_replace("/(define\()([\"'])({$key})\\2,\s*([\"'])(.*?)\\4\s*\)/", "define('{$key}', '{$val}')", $content);
                }
            }
            $outFile = fopen($path . $fileName, 'w');
            if (false === $outFile) {
                return sprintf(_FILE_ACCESS_ERROR, $fileName);
            }
            $writeResult = fwrite($outFile, $content);
            fclose($outFile);
            if (false === $writeResult) {
                return sprintf(_FILE_ACCESS_ERROR, $fileName);
            }
        }
        return true;
    }

    /**
     * Do we need to rewrite mainfile and secure?
     *
     * @return bool
     */
    public function check_mainfile()
    {
        /** @var UpgradeControl $upgradeControl */
        global $upgradeControl;
        return !$upgradeControl->needMainfileRewrite;
    }

    /**
     * Rewrite mainfile and secure file with current templates
     *
     * @return bool
     */
    public function apply_mainfile()
    {
        /** @var UpgradeControl $upgradeControl */
        global $upgradeControl;

        if (null === $upgradeControl->mainfileKeys['XOOPS_COOKIE_DOMAIN']) {
            $upgradeControl->mainfileKeys['XOOPS_COOKIE_DOMAIN'] = xoops_getBaseDomain(XOOPS_URL);
        }
        $result = $this->writeConfigurationFile(
            $upgradeControl->mainfileKeys,
            XOOPS_ROOT_PATH,
            'mainfile.dist.php',
            'mainfile.php'
        );
        if ($result !== true) {
            $this->logs[] = $result;
        } else {
            $result = $this->writeConfigurationFile(
                $upgradeControl->mainfileKeys,
                XOOPS_VAR_PATH . '/data',
                'secure.dist.php',
                'secure.php'
            );
            if ($result !== true) {
                $this->logs[] = $result;
            }
        }
        return ($result === true);
    }

    //modules/system/themes/legacy/legacy.php
    /**
     * Do we need to rewrite mainfile and secure?
     *
     * @return bool
     */
    public function check_zaplegacy()
    {
        return !file_exists('../modules/system/themes/legacy/legacy.php');
    }

    /**
     * Rewrite mainfile and secure file with current templates
     *
     * @return bool
     */
    public function apply_zaplegacy()
    {
        $fileName = 'modules/system/themes/legacy/legacy.php';
        $result = rename('../' . $fileName, '../' . $fileName . '.bak');
        if (false === $result) {
            return sprintf(_FILE_ACCESS_ERROR, $fileName);
        }
        return true;
    }
}

$upg = new Upgrade_259();
return $upg;
