<?php
/**
 * XOOPS Kernel Class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @since           2.3.0
 * @version         $Id$
 */
/**
 * XOOPS Block legacy Instance handler
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Xoops Block Instance
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 *
 * @package kernel
 */
class XoopsBlockInstance
{
    /**
     * Constructor
     *
     */
    function __construct()
    {
    }

    /**
     * Call Magic Function
     *
     * @param string $name
     * @param array $args
     * @return null
     */
    function __call($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the method '{$name}' is not executed") . "!", E_USER_WARNING);
        return null;
    }

    /**
     * Set Magic Function
     *
     * @param string $name
     * @param array $args
     * @return null
     */
    function __set($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not set") . "!", E_USER_WARNING);
        return false;
    }

    /**
     * Get Magic Function
     *
     * @param string $name
     * @return null
     */
    function __get($name)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not vailable") . "!", E_USER_WARNING);
        return null;
    }
}

/**
 * XOOPS Block Instance Handler Class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @package kernel
 * @subpackage block
 */
class XoopsBlockInstanceHandler
{
    /**
     * Enter description here...
     *
     */
    function __construct()
    {
    }

    /**
     * Call Magic Function
     *
     * @param string $name
     * @param array $args
     * @return null
     */
    function __call($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the method '{$name}' is not executed") . "!", E_USER_WARNING);
        return null;
    }

    /**
     * Set Magic Function
     *
     * @param string $name
     * @param array $args
     * @return null
     */
    function __set($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not set") . "!", E_USER_WARNING);
        return false;
    }

    /**
     * Get Magic Function
     *
     * @param string $name
     * @return null
     */
    function __get($name)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not vailable") . "!", E_USER_WARNING);
        return null;
    }
}
