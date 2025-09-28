<?php
/**
 * Protector module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @package             XoopsPartners
 * @since               2.5.0
 * @author              Mage, Mamba
 **/

include XOOPS_ROOT_PATH . '/include/cp_header.php';
include XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
include __DIR__ . '/admin_header.php';
xoops_cp_header();

$aboutAdmin = new ModuleAdmin();

echo $aboutAdmin->addNavigation(basename(__FILE__));
echo $aboutAdmin->renderAbout('xoopsfoundation@gmail.com', false);

include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
