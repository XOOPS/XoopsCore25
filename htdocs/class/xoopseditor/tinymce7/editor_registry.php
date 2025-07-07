<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * TinyMCE7 adapter for XOOPS
 *
 * @category  XoopsEditor
 * @package   TinyMCE7
 * @author    Gregory Mage
 * @author    Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright 2000-2025 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */

return $config = [
    'name'   => 'tinymce7',
    'class'  => 'XoopsFormTinymce7',
    'file'   => XOOPS_ROOT_PATH . '/class/xoopseditor/tinymce7/formtinymce.php',
    'title'  => _XOOPS_EDITOR_TINYMCE7,
    'order'  => 5,
    'nohtml' => 0,
];
