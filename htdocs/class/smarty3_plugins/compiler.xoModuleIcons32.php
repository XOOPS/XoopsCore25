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
 * @copyright       (c) 2000-2022 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author          Andricq Nicolas (AKA MusS)
 * @since           2.5.2
 * @param string[] $params
 * @param Smarty   $smarty
 * @return string
 */

function smarty_compiler_xoModuleIcons32($params, $smarty)
{
    global $xoops, $xoTheme;

    $argStr = reset($params);
    $argStr = trim($argStr,"' \t\n\r\0");

    if (file_exists($xoops->path('Frameworks/moduleclasses/icons/32/index.php'))) {
        $url = $xoops->url('Frameworks/moduleclasses/icons/32/' . $argStr);
    } else {
        if (file_exists($xoops->path('modules/system/images/icons/default/' . $argStr))) {
            $url = $xoops->url('modules/system/images/icons/default/' . $argStr);
        } else {
            $url = $xoops->url('modules/system/images/icons/default/xoops/xoops.png');
        }
    }

    return "<?php echo '" . addslashes($url) . "'; ?>";
}
