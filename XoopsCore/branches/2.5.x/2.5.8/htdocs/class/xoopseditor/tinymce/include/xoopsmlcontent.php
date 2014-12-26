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

defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

// Xlanguage
if ( $GLOBALS["module_handler"]->getByDirname("xlanguage") && defined("XLANGUAGE_LANG_TAG") ) {
    return true;
}

// Easiest Multi-Language Hack (EMLH)
if ( defined('EASIESTML_LANGS') && defined('EASIESTML_LANGNAMES') ) {
    return true;
}

return false;
