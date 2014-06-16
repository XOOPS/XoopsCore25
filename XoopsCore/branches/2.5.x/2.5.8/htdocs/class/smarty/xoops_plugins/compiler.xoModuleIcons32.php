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
 * xoModuleIcons32 Smarty compiler plug-in
 *
 * @copyright      The XOOPS project http://www.xoops.org/
 * @license        http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author         Andricq Nicolas (AKA MusS)
 * @since          2.5.2
 * @version        $Id$
 */

function smarty_compiler_xoModuleIcons32($argStr, &$smarty)
{
    global $xoops, $xoTheme;

    if (file_exists($xoops->path('Frameworks/moduleclasses/icons/32/index.html'))) {
        $url = $xoops->url('Frameworks/moduleclasses/icons/32/' . $argStr);

    } else {
        if (file_exists($xoops->path('modules/system/images/icons/default/' . $argStr))) {
            $url = $xoops->url('modules/system/images/icons/default/' . $argStr);
        } else {
            $url = $xoops->url('modules/system/images/icons/default/xoops/xoops.png');
        }
    }
    return "\necho '" . addslashes($url) . "';";
}
