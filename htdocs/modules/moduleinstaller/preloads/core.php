<?php declare(strict_types=1);
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          XOOPS Project <www.xoops.org> <www.xoops.ir>
 */

/**
 * Class ModuleinstallerCorePreload
 */
class ModuleinstallerCorePreload extends \XoopsPreloadItem
{
    // to add PSR-4 autoloader
    /**
     * @param array $args
     */
    public static function eventCoreIncludeCommonEnd(array $args): void
    {
        require_once __DIR__ . '/autoloader.php';
    }
}
