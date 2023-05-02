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
 * TinyMCE5 adapter for XOOPS
 *
 * @category  XoopsEditor
 * @package   TinyMCE5
 * @author    Gregory Mage
 * @copyright 2020 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
return array(
	'theme' => 'silver',
	'mode' => 'exact',
	'plugins' => 'advlist,anchor,autolink,charmap,code,hr,image,imagetools,lists,link,media,preview,searchreplace,table,xoopsemoticons,xoopscode,xoopsimagemanager',
	'toolbar' => 'undo redo | styleselect | bold italic | forecolor backcolor removeformat  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | insertfile image media link | hr | xoopsemoticons xoopscode | code',
	'image_title' => true,
	'table_use_colgroups' => true,
	/*'image_class_list' => [
		{title: 'None', value: ''},
		{title: 'Width auto', value: 'col-12'}
	],*/
	//'image_advtab' => true, dot work !!
	// This option prevents tinyeditor from deleting empty tags
	'valid_elements' => '*[*]',
	// Use of relative urls?
	'relative_urls' => false,
	'body_class' => 'tinymce5-body',
	'menubar' => false
);