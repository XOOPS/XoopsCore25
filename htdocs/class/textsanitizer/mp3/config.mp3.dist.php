<?php
/**
 * TextSanitizer extension
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2021 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          textsanitizer
 * @since               2.5.11
 * @author              mamba <mambax7@gmail.com>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

return $config = [
    'enable_mp3_entry' => true, // Set to false to disable the MP3 button in the textarea editor
    'enable_selection_handling' => true, // Set to true to use selected text as the default value in prompt
];
