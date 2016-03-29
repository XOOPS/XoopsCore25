<?php
/**
 * Private Messages module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL (http://www.gnu.org/licenses/gpl-2.0.html/)
 * @package             XoopsPartners
 * @since               2.5.0
 * @author              Mage, Mamba
 **/

include_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
include_once dirname(dirname(dirname(__DIR__))) . '/class/xoopsformloader.php';
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$aboutAdmin = new ModuleAdmin();

echo $aboutAdmin->addNavigation(basename(__FILE__));
echo $aboutAdmin->renderAbout('6KJ7RW5DR3VTJ', false);

include_once __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
