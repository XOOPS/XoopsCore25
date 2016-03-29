<?php
/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

return $config = array(
    'name' => 'tinymce',
    'class' => 'XoopsFormTinymce',
    'file' => XOOPS_ROOT_PATH . '/class/xoopseditor/tinymce/formtinymce.php',
    'title' => _XOOPS_EDITOR_TINYMCE,
    'order' => 5,
    'nohtml' => 0);
