<?php
/**
 * Factory Class for XOOPS Database
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      database
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * XoopsDatabaseFactory
 *
 * @package Kernel
 * @author Kazumi Ono <onokazu@xoops.org>
 * @access public
 */
class XoopsDatabaseFactory
{
    /**
     * XoopsDatabaseFactory::XoopsDatabaseFactory()
     */
    function XoopsDatabaseFactory()
    {
    }

    /**
     * Get a reference to the only instance of database class and connects to DB
     *
     * if the class has not been instantiated yet, this will also take
     * care of that
     *
     * @static
     * @staticvar object  The only instance of database class
     * @return object Reference to the only instance of database class
     */
    static function &getDatabaseConnection()
    {
        static $instance;
        if (!isset($instance)) {
            if (file_exists($file = XOOPS_ROOT_PATH . '/class/database/' . XOOPS_DB_TYPE . 'database.php')) {
                require_once $file;

                if (!defined('XOOPS_DB_PROXY')) {
                    $class = 'Xoops' . ucfirst(XOOPS_DB_TYPE) . 'DatabaseSafe';
                } else {
                    $class = 'Xoops' . ucfirst(XOOPS_DB_TYPE) . 'DatabaseProxy';
                }

                $xoopsPreload =& XoopsPreload::getInstance();
                $xoopsPreload->triggerEvent('core.class.database.databasefactory.connection', array(&$class));

                $instance = new $class();
                $instance->setLogger(XoopsLogger::getInstance());
                $instance->setPrefix(XOOPS_DB_PREFIX);
                if (!$instance->connect()) {
                    trigger_error('notrace:Unable to connect to database', E_USER_ERROR);
                }
            } else {
                trigger_error('notrace:Failed to load database of type: ' . XOOPS_DB_TYPE . ' in file: ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);
            }
        }
        return $instance;
    }

    /**
     * Gets a reference to the only instance of database class. Currently
     * only being used within the installer.
     *
     * @static
     * @staticvar object  The only instance of database class
     * @return object Reference to the only instance of database class
     */
    static function getDatabase()
    {
        static $database;
        if (!isset($database)) {
            if (file_exists($file = XOOPS_ROOT_PATH . '/class/database/' . XOOPS_DB_TYPE . 'database.php')) {
                include_once $file;
                if (!defined('XOOPS_DB_PROXY')) {
                    $class = 'Xoops' . ucfirst(XOOPS_DB_TYPE) . 'DatabaseSafe';
                } else {
                    $class = 'Xoops' . ucfirst(XOOPS_DB_TYPE) . 'DatabaseProxy';
                }
                unset($database);
                $database = new $class();
            } else {
                trigger_error('notrace:Failed to load database of type: ' . XOOPS_DB_TYPE . ' in file: ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);
            }
        }
        return $database;
    }
}
