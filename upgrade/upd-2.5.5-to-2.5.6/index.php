<?php

/**
 * Upgrader from 2.5.5 to 2.5.6
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.5.6
 * @author           XOOPS Team
 * @version          $Id: index.php 9043 2012-02-22 02:51:38Z beckmi $
 */
class Upgrade_256 extends XoopsUpgrade
{
    public $tasks = array(
        'com_user',
        'com_email',
        'com_url');

    /**
     *
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
    }

    /**
     * Check if Fast Comment fields already exist
     *
     */
    public function check_com_user()
    {
        $result = $GLOBALS['xoopsDB']->queryF('SHOW COLUMNS FROM ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . " LIKE 'com_user'");

        return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0);

        return true;
    }

    /**
     * @return bool
     */
    public function check_com_email()
    {
        $result = $GLOBALS['xoopsDB']->queryF('SHOW COLUMNS FROM ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . " LIKE 'com_email'");

        return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0);

        return true;
    }

    /**
     * @return bool
     */
    public function check_com_url()
    {
        $result = $GLOBALS['xoopsDB']->queryF('SHOW COLUMNS FROM ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . " LIKE 'com_url'");

        return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0);

        return true;
    }

    /**
     * @return bool
     */
    public function apply_com_user()
    {
        $sql = 'ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . ' ADD `com_user` VARCHAR( 60 ) NOT NULL AFTER `com_uid`, ADD INDEX ( `com_user` )';
        if (!$GLOBALS['xoopsDB']->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function apply_com_email()
    {
        $sql = 'ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . ' ADD `com_email` VARCHAR( 60 ) NOT NULL AFTER `com_user`, ADD INDEX ( `com_email` )';
        if (!$GLOBALS['xoopsDB']->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function apply_com_url()
    {

        //$this->query( "ALTER TABLE `xoopscomments` ADD `com_user` VARCHAR( 60 ) NOT NULL AFTER `com_uid`, ADD INDEX ( `com_url` )" );

        $sql = 'ALTER TABLE ' . $GLOBALS['xoopsDB']->prefix('xoopscomments') . ' ADD `com_url` VARCHAR( 60 ) NOT NULL AFTER `com_email` ';
        if (!$GLOBALS['xoopsDB']->queryF($sql)) {
            return false;
        }

        return true;
    }
}

$upg = new Upgrade_256();
return $upg;
