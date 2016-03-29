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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Gregory Mage (AKA Mage)
 * @package             system
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * System Banner Finish
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemBannerFinish extends XoopsObject
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('bid', XOBJ_DTYPE_INT, null, false, 5);
        $this->initVar('cid', XOBJ_DTYPE_INT, null, false, 5);
        $this->initVar('impressions', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('clicks', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('datestart', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('dateend', XOBJ_DTYPE_INT, null, false, 10);
    }
}

/**
 * System banner finish handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 * @subpackage          banner
 */
class SystemBannerfinishHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'bannerfinish', 'SystemBannerFinish', 'bid', 'cid');
    }
}
