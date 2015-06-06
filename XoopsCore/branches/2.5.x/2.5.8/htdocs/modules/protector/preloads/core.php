<?php
/**
 * Protector
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         protector
 * @since           2.4.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 */

// defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * Protector core preloads
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          trabis <lusopoemas@gmail.com>
 */
class ProtectorCorePreload extends XoopsPreloadItem
{
    /**
     * @param $args
     */
    static function eventCoreIncludeCommonStart($args)
    {
        include XOOPS_TRUST_PATH . '/modules/protector/include/precheck.inc.php';
    }

    /**
     * @param $args
     */
    function eventCoreIncludeCommonEnd($args)
    {
            include XOOPS_TRUST_PATH . '/modules/protector/include/postcheck.inc.php';
        }

    /**
     * @param $args
     */
    static function eventCoreClassDatabaseDatabasefactoryConnection($args)
    {
        if (defined('XOOPS_DB_ALTERNATIVE') && class_exists(XOOPS_DB_ALTERNATIVE)) {
            $args[0] = XOOPS_DB_ALTERNATIVE;
        }
    }

}
