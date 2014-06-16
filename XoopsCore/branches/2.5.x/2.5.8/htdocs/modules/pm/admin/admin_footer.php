<?php
/**
 * PM module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright::    The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license::   http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package::   PM
 * @since::     2.5.0
 * @author::    XOOPS Development Team
 * @version:   $Id $
 **/

echo "<div class='adminfooter'>\n"
    ."  <div style='text-align: center;'>\n"
    ."    <a href='http://www.xoops.org' rel='external'><img src='{$pathIcon32}/xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS'></a>\n"
    ."  </div>\n"
    ."  " . _AM_MODULEADMIN_ADMIN_FOOTER . "\n"
    ."</div>";

xoops_cp_footer();
