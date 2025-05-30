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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since               2.3.0
 * @package             xoopseditor
 */
return $config = [
    'class' => 'FormTextArea',
    'file' => XOOPS_ROOT_PATH . '/class/xoopseditor/textarea/textarea.php',
    'title' => _XOOPS_EDITOR_TEXTAREA, // display to end user
    'order' => 1, // 0 will disable the editor
    'nohtml' => 1, // For forms that have "dohtml" disabled
];
