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
 * @copyright 2000-2025 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */

return [
	'theme' => 'silver',
	'selector' => 'textarea#summary, textarea#body, textarea#message', // keep if targeting multiple fields
	'plugins' => 'advlist anchor autolink charmap code hr image imagetools lists link media preview searchreplace table xoopsemoticons xoopscode', // removed 'xoopsimagemanager'
	'toolbar' => 'undo redo | styleselect | bold italic | forecolor backcolor removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | insertfile image media link | hr | xoopsemoticons xoopscode | code',
	'image_title' => true,
	'table_use_colgroups' => true,
    // This option prevents tinyeditor from deleting empty tags
	'valid_elements' => '*[*]',
	'relative_urls' => false,
	'body_class' => 'tinymce7-body',
	'menubar' => false,
//	'content_css' => 'https://diversitybridge.net/themes/xswatch4/style.css', // newly added
	'content_css' => XOOPS_THEME_URL . '/' . $GLOBALS['xoopsConfig']['theme_set'] . '/style.css',
	'width' => '100%', // newly added
	'height' => '400px', // newly added
	'license_key' => 'gpl', // newly added (mandatory)
	'language' => 'en', // newly added
];
