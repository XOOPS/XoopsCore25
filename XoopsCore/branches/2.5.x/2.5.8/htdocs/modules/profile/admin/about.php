<?php
/**
 * XOOPS Profile module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright    The XOOPS Project (http://www.xoops.org)
 * @license      GNU GPL (http://www.gnu.org/licenses/gpl-2.0.html/)
 * @package    xoopsPoll
 * @since        2.5.0
 * @author     Mage, Mamba
 * @version    $Id $
 **/

include_once dirname(__FILE__) . '/../../../include/cp_header.php';
include_once dirname(__FILE__) . '/admin_header.php';
xoops_cp_header();

$module_info =& $module_handler->get($xoopsModule->getVar("mid"));

$aboutAdmin = new ModuleAdmin();

echo $aboutAdmin->addNavigation('about.php');
echo $aboutAdmin->renderabout('6KJ7RW5DR3VTJ', false);

include_once dirname(__FILE__) . '/admin_footer.php';
