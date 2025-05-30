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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             system
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * System Block
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @package             system
 */
class SystemBlockLinkModule extends XoopsObject
{
    //PHP 8.2 Dynamic properties deprecated
    public $block_id;
    public $module_id;
    
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('block_id', XOBJ_DTYPE_INT);
        $this->initVar('module_id', XOBJ_DTYPE_INT);
    }
}

/**
 * System block handler class. (Singleton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @package             system
 * @subpackage          avatar
 */
class SystemBlockLinkModuleHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'block_module_link', 'SystemBlockLinkModule', 'block_id', 'module_id');
    }
}
