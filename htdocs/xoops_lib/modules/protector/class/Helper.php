<?php

namespace XoopsModules\Protector;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      XOOPS Development Team
 */

use XoopsModules\Protector;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class Helper
 */
class Helper extends \Xmf\Module\Helper
{
    public $debug;

    /**
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
        $moduleDirName = \basename(\dirname(__DIR__));
        parent::__construct($moduleDirName);
    }

    /**
     * @param bool $debug
     *
     * @return  Protector\Helper
     */
    public static function getInstance($debug = false)
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($debug);
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getDirname()
    {
        return $this->dirname;
    }

    /**
     * Get an Object Handler
     *
     * @param string $name name of handler to load
     *
     * @return bool|\XoopsObjectHandler|\XoopsPersistableObjectHandler
     */
    public function getHandler($name)
    {
        $ret = false;

        $class = __NAMESPACE__ . '\\' . ucfirst($name) . 'Handler';
        if (!class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found");
        }
        /** @var \XoopsMySQLDatabase $db */
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $helper = self::getInstance();
        $ret = new $class($db, $helper);
        $this->addLog("Getting handler '{$name}'");

        return $ret;
    }
}
