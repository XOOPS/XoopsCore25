<?php
/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         class
 * @subpackage      editor
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id$
 */

return $config = array(
        "name"      =>  "tinymce",
        "class"     =>  "XoopsFormTinymce",
        "file"      =>  XOOPS_ROOT_PATH . "/class/xoopseditor/tinymce/formtinymce.php",
        "title"     =>  _XOOPS_EDITOR_TINYMCE,
        "order"     =>  5,
        "nohtml"    =>  0
    );
