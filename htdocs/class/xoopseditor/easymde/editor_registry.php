<?php
/**
 * EasyMDE Markdown Editor for XOOPS
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xoopseditor
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

return $config = [
    'name'   => 'easymde',
    'class'  => 'FormEasyMDE',
    'file'   => XOOPS_ROOT_PATH . '/class/xoopseditor/easymde/easymde.php',
    'title'  => _XOOPS_EDITOR_EASYMDE,
    'order'  => 8,
    'nohtml' => 1,
];
