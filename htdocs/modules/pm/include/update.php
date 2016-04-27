<?php
/**
 * Private Message
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             pm
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

$path = dirname(dirname(dirname(__DIR__)));
require_once $path . '/include' . '/cp_header.php';

/**
 * @param      $module
 * @param null $oldversion
 * @return bool
 */
/**
 * @param      $module
 * @param null $oldversion
 * @return bool
 */
function xoops_module_update_pm(XoopsModule $module, $oldversion = null)
{
    global $xoopsDB;
    if ($oldversion <= 100) {
        // Check pm table version
        $sql = 'SHOW COLUMNS FROM ' . $xoopsDB->prefix('priv_msgs');
        if (!$result = $xoopsDB->queryF($sql)) {
            return false;
        }
        // Migrate from existent pm module
        if (($rows = $xoopsDB->getRowsNum($result)) == 12) {
            return true;
        } elseif ($rows == 8) {
            return $xoopsDB->queryFromFile(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/sql/mysql.upgrade.sql');
        } else {
            return false;
        }
    }

    if ($oldversion < 110) {
        // remove old html template files
        $templateDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/templates/';
        $template_list     = array_diff(scandir($templateDirectory), array('..', '.'));
        foreach ($template_list as $k => $v) {
            $fileinfo = new SplFileInfo($templateDirectory . $v);
            if ($fileinfo->getExtension() === 'html' && $fileinfo->getFilename() !== 'index.html') {
                @unlink($templateDirectory . $v);
            }
        }

        xoops_load('xoopsfile');
        //remove /images directory
        $imagesDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname', 'n') . '/images/';
        $folderHandler   = XoopsFile::getHandler('folder', $imagesDirectory);
        $folderHandler->delete($imagesDirectory);
        //delete .html entries from the tpl table
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('tplfile') . " WHERE `tpl_module` = '" . $module->getVar('dirname', 'n') . "' AND `tpl_file` LIKE '%.html%'";
        $xoopsDB->queryF($sql);
    }

    return true;
}
