<?php
/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Laurent JEN <dugris@frxoops.org>
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

// check categories readability by group
$groups         = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
$imgcat_handler = xoops_getHandler('imagecategory');

return !(count($imgcat_handler->getList($groups, 'imgcat_read', 1)) == 0);
