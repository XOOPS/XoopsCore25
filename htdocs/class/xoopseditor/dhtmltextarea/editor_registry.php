<?php
/**
 * XOOPS editor
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
 * @author              Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since               2.3.0
 * @package             xoopseditor
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

return $config = array(
    'class'  => 'FormDhtmlTextArea',
    'file'   => XOOPS_ROOT_PATH . '/class/xoopseditor/dhtmltextarea/dhtmltextarea.php',
    'title'  => _XOOPS_EDITOR_DHTMLTEXTAREA,
    'order'  => 2,
    'nohtml' => 1);
