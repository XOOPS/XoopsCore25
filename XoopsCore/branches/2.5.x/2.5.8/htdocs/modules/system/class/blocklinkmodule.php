<?php
/**
 * Block Link Module Class Manager
*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package     system
 * @version     $Id$
 */
// defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * System Block
 *
 * @copyright   copyright (c) 2000 XOOPS.org
 * @package     system
 */
class SystemBlockLinkModule extends XoopsObject
{
    /**
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->initVar('block_id', XOBJ_DTYPE_INT);
        $this->initVar('module_id', XOBJ_DTYPE_INT);
    }

}

/**
 * System block handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright   copyright (c) 2000 XOOPS.org
 * @package     system
 * @subpackage  avatar
 */
class SystemBlockLinkModuleHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|object $db
     */
    function __construct($db)
    {
        parent::__construct($db, 'block_module_link', 'SystemBlockLinkModule', 'block_id', 'module_id');
    }

}
