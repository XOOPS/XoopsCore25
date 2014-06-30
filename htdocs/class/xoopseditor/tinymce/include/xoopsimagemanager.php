<?php
/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         class
 * @subpackage      editor
 * @since           2.3.0
 * @author          Laurent JEN <dugris@frxoops.org>
 * @version         $Id$
 */

if (!defined("XOOPS_ROOT_PATH")) { die("XOOPS root path not defined"); }

// check categories readability by group
$groups = is_object( $GLOBALS["xoopsUser"] ) ? $GLOBALS["xoopsUser"]->getGroups() : array( XOOPS_GROUP_ANONYMOUS );
$imgcat_handler =& xoops_gethandler('imagecategory');
if ( count($imgcat_handler->getList($groups, 'imgcat_read', 1)) == 0 ) {
    return false;
}
return true;
