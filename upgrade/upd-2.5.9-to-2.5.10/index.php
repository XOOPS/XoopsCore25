<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.9 to 2.5.10
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2019 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          Upgrade
 * @since            2.5.10
 * @author           XOOPS Team
 */
class Upgrade_2510 extends XoopsUpgrade
{
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('metarobots', 'protectordata');
        $this->usedFiles = array();
    }

    /**
     * Make meta_robots a textbox instead of a select
     *
     * @return bool
     */
    public function check_metarobots()
    {
        /** @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $table = $db->prefix('config');

        $sql = sprintf(
            'SELECT count(*) FROM `%s` '
            . "WHERE `conf_formtype` = 'select' AND `conf_name` = 'meta_robots' AND `conf_modid` = 0",
            $db->escape($table)
        );

        /** @var mysqli_result $result */
        $result = $db->query($sql);
        if ($db->isResultSet($result)) {
            $row = $db->fetchRow($result);
            if ($row) {
                $count = $row[0];
                return (0 === (int) $count) ? true : false;
            }
        }
        return false;
    }

    /**
     * Make meta_robots a textbox instead of a select
     *
     * This will allow webmasters to utilize current robots meta tag standards
     * @link https://developers.google.com/search/reference/robots_meta_tag
     *
     * @return bool
     */
    public function apply_metarobots()
    {
        // UPDATE `x569_config` SET `conf_formtype` = 'textbox' WHERE `conf_name` = 'meta_robots' and `conf_modid` = 0

        $migrate = new Tables();
        $migrate->useTable('config');
        $migrate->update('config', array('conf_formtype' => 'textbox'), "WHERE `conf_name` = 'meta_robots' AND `conf_modid` = 0");
        return $migrate->executeQueue(true);
    }

    /**
     * Do we need to move protector writable data?
     *
     * @return bool
     */
    public function check_protectordata()
    {
        $destinationPath = XOOPS_VAR_PATH . '/protector/';
        return file_exists($destinationPath);
    }

    /**
     * Move protector configs to xoops_data to segregate writable data for containerization
     *
     * @return bool
     */
    public function apply_protectordata()
    {
        $returnResult = false;
        $sourcePath = XOOPS_PATH . '/modules/protector/configs/';
        $destinationPath = XOOPS_VAR_PATH . '/protector/';

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath);
        }
        $directory = dir($sourcePath);
        if (false !== $directory) {
            $returnResult = true;
            while (false !== ($entry = $directory->read())) {
                if ('.' !== $entry && '..' !== $entry) {
                    $src = $sourcePath . $entry;
                    $dest = $destinationPath . $entry;
                    $result = copy($src, $dest);
                    if (false === $result) {
                        $returnResult = false;
                        $this->logs[] = sprintf('Protector file copy %s failed', $entry);
                    }
                }
            }
            $directory->close();
        }
        return $returnResult;
    }
}

$upg = new Upgrade_2510();
return $upg;
