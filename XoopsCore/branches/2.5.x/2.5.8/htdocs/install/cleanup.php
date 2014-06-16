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
 * Cleanup from install
 *
 * This is intended to be called using Ajax after the page_end.php script has loaded.
 * This eliminates the problem of assets (.js, .css, .png, etc.) not being available
 * because the install folder has been renamed. All assets should be loaded by this
 * point, and no further installer action is expected.
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package     installer
 * @since       2.5.7
 * @author      Richard Griffith <richard@geekwright.com>
 * @version     $Id: page_end.php 12051 2013-09-15 01:45:10Z beckmi $
 */

require_once './include/common.inc.php';
defined('XOOPS_INSTALL') or die('XOOPS Installation wizard die');

$install_rename_suffix = $_POST['instsuffix'];
if (preg_match('/^[a-f0-9]{23}$/', $install_rename_suffix)) {
    $installer_modified = "install_remove_" . $install_rename_suffix;
    install_finalize($installer_modified);
    echo "OK";
} else {
    echo "FAILED";
}
exit;
