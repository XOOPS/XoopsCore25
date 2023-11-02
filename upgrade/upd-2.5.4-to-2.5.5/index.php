<?php

/**
 * Upgrader from 2.5.4 to 2.5.5
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.5.5
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           trabis <lusopoemas@gmail.com>
 */
class Upgrade_255 extends XoopsUpgrade
{
    /**
     * Check if keys already exist
     *
     * @return bool
     */
    public function check_keys()
    {
        $tables['groups_users_link'] = array('uid');

        foreach ($tables as $table => $keys) {
            $sql = 'SHOW KEYS FROM `' . $GLOBALS['xoopsDB']->prefix($table) . '`';
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
                continue;
            }
            $existing_keys = array();
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $existing_keys[] = $row['Key_name'];
            }
            foreach ($keys as $key) {
                if (!in_array($key, $existing_keys)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Apply keys that are missing
     *
     * @return bool
     */
    public function apply_keys()
    {
        $tables['groups_users_link'] = array('uid');

        foreach ($tables as $table => $keys) {
            $sql = 'SHOW KEYS FROM `' . $GLOBALS['xoopsDB']->prefix($table) . '`';
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
                continue;
            }
            $existing_keys = array();
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $existing_keys[] = $row['Key_name'];
            }
            foreach ($keys as $key) {
                if (!in_array($key, $existing_keys)) {
                    $sql = 'ALTER TABLE `' . $GLOBALS['xoopsDB']->prefix($table) . "` ADD INDEX `{$key}` (`{$key}`)";
                    if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check imptotal
     *
     * @return bool
     */
    public function check_imptotal()
    {
        $sql = 'SELECT `imptotal` FROM `' . $GLOBALS['xoopsDB']->prefix('banner') . '` WHERE `bid` = 1';
        if ($result = $GLOBALS['xoopsDB']->queryF($sql)) {
            $fieldInfo = mysqli_fetch_field_direct($result, 0);
            $length = $fieldInfo->length;

            return ($length != 8);
        }
        return null;
    }

    /**
     * Apply imptotal
     *
     * @return bool
     */
    public function apply_imptotal()
    {
        $sql = 'ALTER TABLE `' . $GLOBALS['xoopsDB']->prefix('banner') . "` CHANGE `imptotal` `imptotal` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
        if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
            return false;
        }

        return true;
    }

    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('keys', 'imptotal');
    }
}

$upg = new Upgrade_255();
return $upg;
