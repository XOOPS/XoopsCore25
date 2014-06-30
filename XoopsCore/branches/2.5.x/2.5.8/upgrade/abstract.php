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
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright    (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package     upgrader
 * @since       2.3.0
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @version     $Id$
 */

class xoopsUpgrade
{
    var $usedFiles = array( );
    var $tasks = array( );
    var $languageFolder = null;
    var $logs = array();

    /**
     * @param null $dirname
     */
    function xoopsUpgrade($dirname = null)
    {
        if ($dirname) {
            $this->loadLanguage($dirname);
        }
    }

    /**
     * @return bool
     */
    function isApplied()
    {
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
    }

    /**
     * @return bool
     */
    function apply()
    {
        $step = get_class($this);
        $tasks = $_SESSION['xoops_upgrade'][$step];
        foreach ($tasks as $task) {
            $res = $this->{"apply_{$task}"}();
            if (!$res) return false;
            array_shift($_SESSION['xoops_upgrade'][$step]);
        }

        return true;
    }

    /**
     * @param $dirname
     */
    function loadLanguage($dirname)
    {
        global $xoopsConfig, $upgrade_language;

        if (file_exists("./{$dirname}/language/{$upgrade_language}.php")) {
            include_once "./{$dirname}/language/{$upgrade_language}.php";
        } else if (file_exists("./{$dirname}/language/english.php")) {
            include_once "./{$dirname}/language/english.php";
        }
    }

    /**
     * @return string
     */
    function message()
    {
        return empty($this->logs) ? "" : implode("<br />", $this->logs);
    }
}
