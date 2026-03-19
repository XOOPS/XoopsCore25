<?php
/**
 * System Menu Admin Language Constants
 *
 * @category  Language
 * @author    XOOPS Core Team
 * @copyright 2001-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2+ (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

// Navigation
define('_AM_SYSTEM_MENUS_NAV_MAIN', 'Menu Manager');
define('_AM_SYSTEM_MENUS_NAV_BACK', 'Back to Menu List');
define('_AM_SYSTEM_MENUS_NAV_TIPS', '<strong>Tips:</strong><ul>'
    . '<li>Use language constants (e.g. MENUS_HOME) as titles so they can be translated.</li>'
    . '<li>Create a custom menus.php in your language folder to override constant values.</li>'
    . '<li>Drag items to reorder. Nesting is supported up to 3 levels deep.</li>'
    . '</ul>');

// Common
define('_AM_SYSTEM_MENUS_ACTIVE', 'Active');
define('_AM_SYSTEM_MENUS_SAVED', 'Saved successfully');
define('_AM_SYSTEM_MENUS_DELETED', 'Deleted successfully');
define('_AM_SYSTEM_MENUS_ORDER_SAVED', 'Order saved');

// Category
define('_AM_SYSTEM_MENUS_ADDCAT', 'Add Category');
define('_AM_SYSTEM_MENUS_EDITCAT', 'Edit Category');
define('_AM_SYSTEM_MENUS_DELCAT', 'Delete Category');
define('_AM_SYSTEM_MENUS_CATTITLE', 'Category Title');
define('_AM_SYSTEM_MENUS_CATPREFIX', 'Prefix (HTML)');
define('_AM_SYSTEM_MENUS_CATSUFFIX', 'Suffix (HTML)');
define('_AM_SYSTEM_MENUS_CATURL', 'URL');
define('_AM_SYSTEM_MENUS_CATTARGET', 'Link Target');
define('_AM_SYSTEM_MENUS_CATPOSITION', 'Position');
define('_AM_SYSTEM_MENUS_DELCAT_CONFIRM', 'Are you sure you want to delete the category "%s" and all its items?');

// Item
define('_AM_SYSTEM_MENUS_ADDITEM', 'Add Item');
define('_AM_SYSTEM_MENUS_EDITITEM', 'Edit Item');
define('_AM_SYSTEM_MENUS_DELITEM', 'Delete Item');
define('_AM_SYSTEM_MENUS_ITEMTITLE', 'Item Title');
define('_AM_SYSTEM_MENUS_ITEMPREFIX', 'Prefix (HTML)');
define('_AM_SYSTEM_MENUS_ITEMSUFFIX', 'Suffix (HTML)');
define('_AM_SYSTEM_MENUS_ITEMURL', 'URL');
define('_AM_SYSTEM_MENUS_ITEMTARGET', 'Link Target');
define('_AM_SYSTEM_MENUS_ITEMPOSITION', 'Position');
define('_AM_SYSTEM_MENUS_ITEMPARENT', 'Parent Item');
define('_AM_SYSTEM_MENUS_ITEMCATEGORY', 'Category');
define('_AM_SYSTEM_MENUS_DELITEM_CONFIRM', 'Are you sure you want to delete the item "%s" and its sub-items?');

// Target options
define('_AM_SYSTEM_MENUS_TARGET_SELF', 'Same window');
define('_AM_SYSTEM_MENUS_TARGET_BLANK', 'New window');

// Permissions
define('_AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY', 'Groups that can see this category');
define('_AM_SYSTEM_MENUS_PERMISSION_VIEW_ITEM', 'Groups that can see this item');

// Errors
define('_AM_SYSTEM_MENUS_ERROR_CATNOTFOUND', 'Category not found');
define('_AM_SYSTEM_MENUS_ERROR_CATPROTECTED', 'Cannot delete a protected category');
define('_AM_SYSTEM_MENUS_ERROR_CATINACTIVE', 'Cannot activate: the category is inactive');
define('_AM_SYSTEM_MENUS_ERROR_ITEMNOTFOUND', 'Item not found');
define('_AM_SYSTEM_MENUS_ERROR_ITEMPROTECTED', 'Cannot delete a protected item');
define('_AM_SYSTEM_MENUS_ERROR_ITEMPARENT', 'Invalid parent item selected');
define('_AM_SYSTEM_MENUS_ERROR_ITEMCYCLE', 'Cannot set parent: it would create a circular reference');
define('_AM_SYSTEM_MENUS_ERROR_ITEMDEPTH', 'Maximum nesting depth (3 levels) exceeded');
define('_AM_SYSTEM_MENUS_ERROR_PARENTINACTIVE', 'Cannot activate: the parent item is inactive');

// Menu content constants (used in seeded data)
define('MENUS_HOME', 'Home');
define('MENUS_ADMIN', 'Administration');
define('MENUS_ACCOUNT', 'Account');
define('MENUS_ACCOUNT_EDIT', 'Edit Account');
define('MENUS_ACCOUNT_LOGIN', 'Login');
define('MENUS_ACCOUNT_LOGOUT', 'Logout');
define('MENUS_ACCOUNT_REGISTER', 'Sign Up');
define('MENUS_ACCOUNT_MESSAGES', 'Messages');
define('MENUS_ACCOUNT_NOTIFICATIONS', 'Notifications');
define('MENUS_ACCOUNT_TOOLBAR', 'Toolbar');
