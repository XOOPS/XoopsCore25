<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Upgrade interface class
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.3.0
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 */
class XoopsUpgrade
{
    public $usedFiles      = array();
    public $tasks          = array();
    public $languageFolder = null;
    public $logs           = array();

    /**
     * @param null $dirname
     */
    public function __construct($dirname = null)
    {
        if ($dirname) {
            $this->loadLanguage($dirname);
        }
    }

    /**
     * Run the check_* tasks to see if the patch is needed.
     *
     * The check_* methods should return:
     *  - true if the patch has been applied
     *  - false if the patch needs to be performed
     *
     * @return PatchStatus
     */
    public function isApplied()
    {
        return new PatchStatus($this);
        /*
        $step = get_class($this);
        if (!isset($_SESSION['xoops_upgrade'][$step]) || !is_array($_SESSION['xoops_upgrade'][$step])) {
            $_SESSION['xoops_upgrade'][$step] = array();
        }
        foreach ($this->tasks as $task) {
            if (!in_array($task, $_SESSION['xoops_upgrade'][$step])) {
                if (!$res = $this->{"check_{$task}"}()) {
                    $_SESSION['xoops_upgrade'][$step][] = $task;
                }
            }
        }

        return empty($_SESSION['xoops_upgrade'][$step]) ? true : false;
        */
    }

    /**
     * @return bool
     */
    public function apply()
    {
        $patchStatus  = $this->isApplied();
        $tasks = $patchStatus->tasks;
        foreach ($tasks as $task) {
            $res = $this->{"apply_{$task}"}();
            if (!$res) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $dirname
     */
    public function loadLanguage($dirname)
    {
        global $upgradeControl;

        $upgradeControl->loadLanguage($dirname);
    }

    /**
     * @return string
     */
    public function message()
    {
        return empty($this->logs) ? '' : implode('<br>', $this->logs);
    }

    /**
     * Relocated here from upgrade/index.php
     *
     * @param XoopsDatabase $db
     * @param        $table
     * @param        $field
     * @param string $condition
     *
     * @return bool
     */
    protected function getDbValue(XoopsDatabase $db, $table, $field, $condition = '')
    {
        $table = $db->prefix($table);
        $sql   = "SELECT `{$field}` FROM `{$table}`";
        if ($condition) {
            $sql .= " WHERE {$condition}";
        }
        $result = $db->query($sql);
        if ($db->isResultSet($result)) {
            $row = $db->fetchRow($result);
            if ($row) {
                return $row[0];
            }
        }

        return false;
    }
}
