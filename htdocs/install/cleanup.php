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
 * @copyright   (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         installer
 * @since           2.5.7
 * @author          Richard Griffith <richard@geekwright.com>
 */

require_once __DIR__ . '/include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

$install_rename_suffix = $_POST['instsuffix'];
if (preg_match('/^[a-f0-9]{24}\.[0-9]{8}$/', $install_rename_suffix)) {
    $installer_modified = 'install_remove_' . $install_rename_suffix;
    install_finalize($installer_modified);
    echo 'OK';
} else {
    echo 'FAILED';
}
exit;
