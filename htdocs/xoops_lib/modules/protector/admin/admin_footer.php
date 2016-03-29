<?php
/**
 * XoopsPartner module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package   Xoops Partner
 * @since     2.5.0
 * @author    XOOPS Development Team
 **/

global $moduleInfo;

echo "<div align=\"center\"><a href=\"http://www.xoops.org\" target=\"_blank\"><img src=" . XOOPS_URL . '/' . $moduleInfo->getInfo('dirmoduleadmin') . '/icons/32/xoopsmicrobutton.gif' . ' ' . " alt=\"XOOPS\" title=\"XOOPS\"></a></div>";
echo "<div class='center smallsmall italic pad5'><strong>" . $xoopsModule->getVar('name') . "</strong> is maintained by the <a class='tooltip' rel='external' href='http://www.xoops.org/' title='Visit XOOPS Community'>XOOPS Community</a></div>";

xoops_cp_footer();
