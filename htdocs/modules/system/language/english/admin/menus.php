<?php
/**
 * @copyright       (c) 2000-2026 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * _LANGCODE    en
 * _CHARSET     UTF-8
 */
// Navigation
define('_AM_SYSTEM_MENUS_NAV_MAIN', 'Menus Management');
define('_AM_SYSTEM_MENUS_NAV_CATEGORY', 'Category Management');

// Tips
define('_AM_SYSTEM_MENUS_NAV_TIPS', '
<ul>
    <li>This page allows you to manage the main menus of your site.</li>
    <li>You can create categories to organize your menus, and add submenu items under each category.</li>
    <li>For each menu item, you can specify a title, URL, position, and whether it is active or not.</li>
    <li>You can also use language constants for the menu titles. If a constant is used, its value will be displayed in parentheses next to the title.</li>
    <li>File used for the basic menus. If you wish to add language constants, you must add them to the menus.dist.php file, which must be renamed menus.php.</li>
    <li>The file is located here: "modules/system/language/%s/menus/menus.php".</li>
</ul>');
// Main
define('_AM_SYSTEM_MENUS_ACTIVE', 'Active');
define('_AM_SYSTEM_MENUS_ACTIVE_NO', 'Disabled');
define('_AM_SYSTEM_MENUS_ACTIVE_YES', 'Enabled');
define('_AM_SYSTEM_MENUS_ADDCAT', 'Add Category');
define('_AM_SYSTEM_MENUS_ADDITEM', 'Adding a submenu item');
define('_AM_SYSTEM_MENUS_DELCAT', 'Delete Category');
define('_AM_SYSTEM_MENUS_DELITEM', 'Delete a submenu item');
define('_AM_SYSTEM_MENUS_EDITCAT', 'Edit Category');
define('_AM_SYSTEM_MENUS_EDITITEM', 'Edit a submenu item');
define('_AM_SYSTEM_MENUS_ERROR_ITEMDISABLE', 'You cannot delete a menu that is disabled. Please enable the menu first, then try deleting it again.');
define('_AM_SYSTEM_MENUS_ERROR_ITEMEDIT', 'You cannot edit a menu that is disabled. Please enable the menu first, then try editing it again.');
define('_AM_SYSTEM_MENUS_ERROR_ITEMPROTECTED', 'You cannot delete a protected menu item.');
define('_AM_SYSTEM_MENUS_ERROR_ITEMPARENT', 'You cannot select a menu as its own parent.');
define('_AM_SYSTEM_MENUS_ERROR_ITEMCYCLE', 'You cannot select a descendant as the parent — this would create a cycle.');
define('_AM_SYSTEM_MENUS_ERROR_ITEMDEPTH', 'Maximum nesting depth (3 levels) exceeded.');
define('_AM_SYSTEM_MENUS_ERROR_CATPROTECTED', 'You cannot delete a protected menu category.');
define('_AM_SYSTEM_MENUS_ERROR_NOCATEGORY', 'There are no menu categories. You must create one before adding menus.');
define('_AM_SYSTEM_MENUS_ERROR_NOITEM', 'There are no submenu items.');
define('_AM_SYSTEM_MENUS_ERROR_NOITEMS', 'There are no submenu items in this category.');
define('_AM_SYSTEM_MENUS_ACTION', 'Action');
define('_AM_SYSTEM_MENUS_ERROR_PARENTINACTIVE', 'You cannot modify this item while its parent is inactive!');
define('_AM_SYSTEM_MENUS_LISTCAT', 'List Categories');
define('_AM_SYSTEM_MENUS_LISTITEM', 'List items');
define('_AM_SYSTEM_MENUS_PID', 'Upper level menu');
define('_AM_SYSTEM_MENUS_POSITIONCAT', 'Position of the menu category');
define('_AM_SYSTEM_MENUS_POSITIONITEM', 'Position of the submenu item');
define('_AM_SYSTEM_MENUS_PREFIXCAT', 'Prefix for the menu category title');
define('_AM_SYSTEM_MENUS_PREFIXCAT_DESC', 'Optional — Text to display before the menu category title. HTML is allowed.');
define('_AM_SYSTEM_MENUS_PREFIXITEM', 'Prefix for the submenu item title');
define('_AM_SYSTEM_MENUS_PREFIXITEM_DESC', 'Optional — Text to display before the submenu item title. HTML is allowed.');
define('_AM_SYSTEM_MENUS_SUFFIXCAT', 'Suffix for the menu category title');
define('_AM_SYSTEM_MENUS_SUFFIXCAT_DESC', 'Optional — Text to display after the menu category title. HTML is allowed.');
define('_AM_SYSTEM_MENUS_SUFFIXITEM', 'Suffix for the submenu item title');
define('_AM_SYSTEM_MENUS_SUFFIXITEM_DESC', 'Optional — Text to display after the submenu item title. HTML is allowed.');
define('_AM_SYSTEM_MENUS_SUREDELCAT', 'Are you sure you want to delete this menu category "%s" and all of its submenu items?');
define('_AM_SYSTEM_MENUS_SUREDELITEM', 'Are you sure you want to delete this submenu item "%s" and all of its child submenu items?');
define('_AM_SYSTEM_MENUS_TARGET', 'Target');
define('_AM_SYSTEM_MENUS_TARGET_SELF', 'Same Window');
define('_AM_SYSTEM_MENUS_TARGET_BLANK', 'New Window');
define('_AM_SYSTEM_MENUS_TITLECAT', 'Name of the menu category');
define('_AM_SYSTEM_MENUS_TITLECAT_DESC', 'You can use a constant for the title. If you do, the constant value will be shown in parentheses next to the title in admin side.');
define('_AM_SYSTEM_MENUS_TITLEITEM', 'Name of the submenu item');
define('_AM_SYSTEM_MENUS_TITLEITEM_DESC', 'You can use a constant for the title. If you do, the constant value will be shown in parentheses next to the title in admin side.');
define('_AM_SYSTEM_MENUS_URLCAT', 'URL of the menu category');
define('_AM_SYSTEM_MENUS_URLCATDESC', 'Optional — Only if you want the category title to be a link.<br>Example: "http://www.example.com" for external links or "index.php?option=value" for internal links.');
define('_AM_SYSTEM_MENUS_URLITEM', 'URL of the submenu item');

// permissions
define('_AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY', 'Permission to view category');
define('_AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY_DESC', 'Select groups that are allowed to view this category.<br>Note: If a category is not viewable, its submenu items will not be viewable either, regardless of their individual permissions.');
define('_AM_SYSTEM_MENUS_PERMISSION_VIEW_ITEM', 'Permission to view submenu item');
define('_AM_SYSTEM_MENUS_PERMISSION_VIEW_ITEM_DESC', 'Select groups that are allowed to view this submenu item.<br>Note: If a submenu item is not viewable, it will not be visible to users in the frontend, regardless of their individual permissions.');

// Menus
define('MENUS_HOME', 'Home');
define('MENUS_ADMIN', 'Administration');
define('MENUS_ACCOUNT', 'Account');
define('MENUS_ACCOUNT_EDIT', 'Edit Account');
define('MENUS_ACCOUNT_LOGIN', 'Login');
define('MENUS_ACCOUNT_LOGOUT', 'Logout');
define('MENUS_ACCOUNT_MESSAGES', 'Messages');
define('MENUS_ACCOUNT_NOTIFICATIONS', 'Notifications');
define('MENUS_ACCOUNT_REGISTER', 'Sign Up');
define('MENUS_ACCOUNT_TOOLBAR', 'Toolbar');
