<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * System Menu Administration
 *
 * @copyright 2001-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2+ (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since        2.5.12
 * @author       XOOPS Development Team
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

require_once XOOPS_ROOT_PATH . '/kernel/menuscategory.php';
require_once XOOPS_ROOT_PATH . '/kernel/menusitems.php';
require_once XOOPS_ROOT_PATH . '/modules/system/class/SystemMenusTree.php';

use Xmf\Request;

// --- Constants ---
const MENUS_MAX_DEPTH = 3;
const MENUS_ADMIN_URL = 'admin.php?fct=menus';

// --- Access Control ---
if (!is_object($xoopsUser) || !is_object($xoopsModule)
    || !$xoopsUser->isAdmin($xoopsModule->mid())
) {
    exit(_NOPERM);
}

// --- Helper Functions ---

/**
 * Send a JSON response and exit.
 *
 * @param bool                 $success Whether the operation succeeded
 * @param array<string, mixed> $extra   Additional data to include
 *
 * @throws \JsonException If JSON encoding fails
 */
function menus_send_json(bool $success, array $extra = []): void
{
    // Refresh token as HTML input so JS can extract name+value for next request
    $token = $GLOBALS['xoopsSecurity']->getTokenHTML();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array_merge(
        ['success' => $success, 'token' => $token],
        $extra
    ), JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Check if the current operation returns JSON instead of HTML.
 *
 * @param string $op The operation name
 *
 * @return bool
 */
function menus_is_ajax(string $op): bool
{
    return in_array($op, ['saveorder', 'saveorderitems', 'toggleactivecat', 'toggleactiveitem'], true);
}

/**
 * Prepare the output buffer for an AJAX response.
 *
 * @return void
 */
function menus_prepare_ajax(): void
{
    $GLOBALS['xoopsLogger']->activated = false;
    while (ob_get_level()) {
        ob_end_clean();
    }
}

/**
 * Validate CSRF token. On failure, send JSON error or redirect.
 *
 * @param bool $isAjax Whether this is an AJAX request
 *
 * @return void
 */
function menus_require_token(bool $isAjax): void
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $message = defined('_BADTOKEN') ? _BADTOKEN : 'Security token mismatch';
        if ($isAjax) {
            menus_send_json(false, ['message' => $message]);
        }
        redirect_header(MENUS_ADMIN_URL, 3, $message);
    }
}

/**
 * Reject javascript: URLs. Return trimmed URL or empty string.
 *
 * @param string $url The URL to sanitize
 *
 * @return string Safe URL
 */
function menus_sanitize_url(string $url): string
{
    $url = trim($url);
    if (preg_match('/^\s*javascript\s*:/i', $url)) {
        return '';
    }
    return $url;
}

/**
 * Get the category handler (cached).
 *
 * @return XoopsMenusCategoryHandler
 */
function menus_cat_handler(): XoopsMenusCategoryHandler
{
    static $handler = null;
    return $handler ??= xoops_getHandler('menuscategory');
}

/**
 * Get the item handler (cached).
 *
 * @return XoopsMenusItemsHandler
 */
function menus_item_handler(): XoopsMenusItemsHandler
{
    static $handler = null;
    return $handler ??= xoops_getHandler('menusitems');
}

/**
 * Check whether an item can be enabled by walking its full parent chain.
 *
 * Both the owning category and every ancestor item must be active before
 * an item may be toggled on.
 *
 * @param XoopsMenusItemsHandler $itemHandler Item handler
 * @param XoopsMenusItems        $item        The item to check
 *
 * @return bool True when every ancestor is active
 */
function menus_item_can_be_enabled(XoopsMenusItemsHandler $itemHandler, XoopsMenusItems $item): bool
{
    // Category must be active
    $cat = menus_cat_handler()->get((int) $item->getVar('items_cid'));
    if (is_object($cat) && 0 === (int) $cat->getVar('category_active')) {
        return false;
    }

    // Walk the full parent chain
    $parentId = (int) $item->getVar('items_pid');
    while ($parentId > 0) {
        $parent = $itemHandler->get($parentId);
        if (!is_object($parent)) {
            break;
        }
        if (0 === (int) $parent->getVar('items_active')) {
            return false;
        }
        $parentId = (int) $parent->getVar('items_pid');
    }

    return true;
}

/**
 * Initialize all template variables to safe defaults.
 *
 * @param XoopsTpl $tpl The template engine
 * @param string   $op  Current operation name
 */
function menus_assign_template_defaults(\XoopsTpl $tpl, string $op): void
{
    $tpl->assign('op', $op);
    $tpl->assign('xoops_token', $GLOBALS['xoopsSecurity']->getTokenHTML());
    $tpl->assign('category', []);
    $tpl->assign('category_count', 0);
    $tpl->assign('category_id', 0);
    $tpl->assign('cat_title', '');
    $tpl->assign('items', []);
    $tpl->assign('items_count', 0);
    $tpl->assign('nav_menu', '');
    $tpl->assign('form', '');
    $tpl->assign('error_message', '');
    $tpl->assign('token', '');
}

// --- Operation Dispatch ---
$op = Request::getCmd('op', 'list', 'REQUEST');
$isAjax = menus_is_ajax($op);

if ($isAjax) {
    menus_prepare_ajax();
} else {
    // Tell XOOPS which template to render before opening the admin page
    $GLOBALS['xoopsOption']['template_main'] = 'system_menus.tpl';
    xoops_cp_header();
    menus_assign_template_defaults($xoopsTpl, $op);

    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/menus.css');
    $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
    $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
    $xoTheme->addScript('modules/system/js/admin.js');
    $xoTheme->addScript('modules/system/js/nestedSortable.js');
    $xoTheme->addScript('modules/system/js/menus.js');

    $xoBreadCrumb->addLink(_AM_SYSTEM_CONFIG, XOOPS_URL . '/modules/system/admin.php');
}

switch ($op) {
    // --- LIST ALL CATEGORIES ---
    case 'list':
    default:
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN);
        $xoBreadCrumb->addTips(_AM_SYSTEM_MENUS_NAV_TIPS);
        $xoBreadCrumb->render();

        $catHandler  = menus_cat_handler();
        $itemHandler = menus_item_handler();

        $criteria = new CriteriaCompo();
        $criteria->setSort('category_position');
        $criteria->setOrder('ASC');
        $categories = $catHandler->getObjects($criteria);

        $catData = [];
        foreach ($categories as $cat) {
            $cid          = (int) $cat->getVar('category_id');
            $itemCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $cid));
            $itemCriteria->setSort('items_position');
            $itemCriteria->setOrder('ASC');
            $items = $itemHandler->getObjects($itemCriteria);

            $flatItems = [];
            foreach ($items as $item) {
                $flatItems[] = [
                    'id'        => (int) $item->getVar('items_id'),
                    'pid'       => (int) $item->getVar('items_pid'),
                    'title'     => htmlspecialchars($item->getAdminTitle(), ENT_QUOTES, 'UTF-8'),
                    'prefix'    => $item->getVar('items_prefix', 'n'),
                    'suffix'    => $item->getVar('items_suffix', 'n'),
                    'url'       => htmlspecialchars((string) $item->getVar('items_url', 'n'), ENT_QUOTES, 'UTF-8'),
                    'active'    => (int) $item->getVar('items_active'),
                    'protected' => (int) $item->getVar('items_protected'),
                ];
            }

            $catData[] = [
                'id'          => $cid,
                'title'       => htmlspecialchars($cat->getAdminTitle(), ENT_QUOTES, 'UTF-8'),
                'prefix'      => $cat->getVar('category_prefix', 'n'),
                'suffix'      => $cat->getVar('category_suffix', 'n'),
                'url'         => htmlspecialchars((string) $cat->getVar('category_url', 'n'), ENT_QUOTES, 'UTF-8'),
                'target'      => (int) $cat->getVar('category_target') === 1 ? '_blank' : '_self',
                'active'      => (int) $cat->getVar('category_active'),
                'protected'   => (int) $cat->getVar('category_protected'),
                'position'    => (int) $cat->getVar('category_position'),
                'items_count' => count($flatItems),
                'items'       => $flatItems,
            ];
        }

        $xoopsTpl->assign('category', $catData);
        $xoopsTpl->assign('category_count', count($catData));
        $xoopsTpl->assign('token', $GLOBALS['xoopsSecurity']->createToken());
        break;

    // --- ADD CATEGORY FORM ---
    case 'addcat':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_ADDCAT);
        $xoBreadCrumb->render();

        $cat  = new XoopsMenusCategory();
        $form = $cat->getFormCat(MENUS_ADMIN_URL);
        $xoopsTpl->assign('form', $form->render());
        break;

    // --- EDIT CATEGORY FORM ---
    case 'editcat':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_EDITCAT);
        $xoBreadCrumb->render();

        $catId = Request::getInt('category_id', 0, 'GET');
        $cat   = menus_cat_handler()->get($catId);
        if (!is_object($cat) || $cat->isNew()) {
            redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_CATNOTFOUND);
        }
        $form = $cat->getFormCat(MENUS_ADMIN_URL);
        $xoopsTpl->assign('form', $form->render());
        break;

    // --- SAVE CATEGORY ---
    case 'savecat':
        menus_require_token(false);
        $catHandler = menus_cat_handler();
        $catId      = Request::getInt('category_id', 0, 'POST');

        if ($catId > 0) {
            $cat = $catHandler->get($catId);
            if (!is_object($cat) || $cat->isNew()) {
                redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_CATNOTFOUND);
            }
        } else {
            $cat = $catHandler->create();
        }

        $isProtected = (bool) $cat->getVar('category_protected');

        if (!$isProtected) {
            $cat->setVar('category_title', Request::getString('category_title', '', 'POST'));
            $cat->setVar('category_prefix', Request::getText('category_prefix', '', 'POST'));
            $cat->setVar('category_suffix', Request::getText('category_suffix', '', 'POST'));
            $cat->setVar('category_url', menus_sanitize_url(Request::getString('category_url', '', 'POST')));
        }
        $cat->setVar('category_target', Request::getInt('category_target', 0, 'POST'));
        $cat->setVar('category_position', Request::getInt('category_position', 0, 'POST'));
        $cat->setVar('category_active', Request::getInt('category_active', 1, 'POST'));

        if (!$catHandler->insert($cat)) {
            $xoopsTpl->assign('error_message', $cat->getHtmlErrors());
            break;
        }

        // Save permissions using XMF helper
        $permHelper = new \Xmf\Module\Helper\Permission();
        $permHelper->savePermissionForItem(
            'menus_category_view',
            (int) $cat->getVar('category_id'),
            Request::getArray('menus_category_view_perms', [], 'POST')
        );

        redirect_header(MENUS_ADMIN_URL, 2, _AM_SYSTEM_MENUS_SAVED);
        break;

    // --- DELETE CATEGORY ---
    case 'delcat':
        $catId   = Request::getInt('category_id', 0, 'REQUEST');
        $confirm = Request::getInt('confirm', 0, 'POST');
        $cat     = menus_cat_handler()->get($catId);

        if (!is_object($cat) || $cat->isNew()) {
            redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_CATNOTFOUND);
        }
        if ((int) $cat->getVar('category_protected') === 1) {
            redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_CATPROTECTED);
        }

        if ($confirm) {
            menus_require_token(false);

            // Delete all items in this category
            $itemHandler  = menus_item_handler();
            $itemCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $catId));
            $items        = $itemHandler->getObjects($itemCriteria);

            // Delete item permissions and items
            $permHelper = new \Xmf\Module\Helper\Permission();
            foreach ($items as $item) {
                $permHelper->deletePermissionForItem('menus_items_view', (int) $item->getVar('items_id'));
                $itemHandler->delete($item);
            }

            // Delete category permissions
            $permHelper->deletePermissionForItem('menus_category_view', $catId);
            menus_cat_handler()->delete($cat);

            redirect_header(MENUS_ADMIN_URL, 2, _AM_SYSTEM_MENUS_DELETED);
        } else {
            // Show confirmation with list of items that will be deleted
            $itemHandler  = menus_item_handler();
            $itemCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $catId));
            $itemCriteria->setSort('items_position');
            $allItems = $itemHandler->getObjects($itemCriteria);
            $flatList = SystemMenusTree::flattenForDisplay($allItems);

            xoops_confirm(
                [
                    'op'          => 'delcat',
                    'category_id' => $catId,
                    'confirm'     => 1,
                ],
                MENUS_ADMIN_URL,
                sprintf(_AM_SYSTEM_MENUS_DELCAT_CONFIRM, htmlspecialchars($cat->getAdminTitle(), ENT_QUOTES, 'UTF-8'))
            );
        }
        break;

    // --- VIEW CATEGORY (show items) ---
    case 'viewcat':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_CATEGORY);
        $xoBreadCrumb->render();

        $catId = Request::getInt('category_id', 0, 'GET');
        $cat   = menus_cat_handler()->get($catId);
        if (!is_object($cat) || $cat->isNew()) {
            redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_CATNOTFOUND);
        }

        $itemHandler  = menus_item_handler();
        $itemCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $catId));
        $itemCriteria->setSort('items_position');
        $itemCriteria->setOrder('ASC');
        $items = $itemHandler->getObjects($itemCriteria);

        // Build flat item data for template
        $itemData = [];
        foreach ($items as $item) {
            $itemData[] = [
                'id'        => (int) $item->getVar('items_id'),
                'pid'       => (int) $item->getVar('items_pid'),
                'title'     => htmlspecialchars($item->getAdminTitle(), ENT_QUOTES, 'UTF-8'),
                'prefix'    => $item->getVar('items_prefix', 'n'),
                'suffix'    => $item->getVar('items_suffix', 'n'),
                'url'       => htmlspecialchars((string) $item->getVar('items_url', 'n'), ENT_QUOTES, 'UTF-8'),
                'active'    => (int) $item->getVar('items_active'),
                'protected' => (int) $item->getVar('items_protected'),
            ];
        }

        $xoopsTpl->assign('category_id', $catId);
        $xoopsTpl->assign('cat_title', htmlspecialchars($cat->getAdminTitle(), ENT_QUOTES, 'UTF-8'));
        $xoopsTpl->assign('items_count', count($itemData));
        $xoopsTpl->assign('items', $itemData);
        $xoopsTpl->assign('token', $GLOBALS['xoopsSecurity']->createToken());
        break;

    // --- ADD ITEM FORM ---
    case 'additem':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_ADDITEM);
        $xoBreadCrumb->render();

        $catId    = Request::getInt('category_id', 0, 'GET');
        $parentId = Request::getInt('items_pid', 0, 'GET');
        $xoopsTpl->assign('category_id', $catId);
        $item  = new XoopsMenusItems();
        $item->setVar('items_cid', $catId);
        $item->setVar('items_pid', $parentId);
        $form = $item->getFormItems($catId, MENUS_ADMIN_URL);
        $xoopsTpl->assign('form', $form->render());
        break;

    // --- EDIT ITEM FORM ---
    case 'edititem':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_CATEGORY);
        $xoBreadCrumb->render();

        $itemId = Request::getInt('item_id', 0, 'GET');
        $item   = menus_item_handler()->get($itemId);
        if (!is_object($item) || $item->isNew()) {
            redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_ITEMNOTFOUND);
        }
        $catId = (int) $item->getVar('items_cid');
        $xoopsTpl->assign('category_id', $catId);
        if (0 === (int) $item->getVar('items_active')) {
            redirect_header(MENUS_ADMIN_URL . '#cat_' . $catId, 5, _AM_SYSTEM_MENUS_ERROR_ITEMEDIT);
        }
        $form = $item->getFormItems($catId, MENUS_ADMIN_URL);
        $xoopsTpl->assign('form', $form->render());
        break;

    // --- SAVE ITEM ---
    case 'saveitem':
        menus_require_token(false);
        $itemHandler = menus_item_handler();
        $itemId      = Request::getInt('items_id', 0, 'POST');
        $catId       = Request::getInt('items_cid', 0, 'POST');
        $xoopsTpl->assign('category_id', $catId);

        if ($itemId > 0) {
            $item = $itemHandler->get($itemId);
            if (!is_object($item) || $item->isNew()) {
                redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_ITEMNOTFOUND);
            }
        } else {
            $item = $itemHandler->create();
        }

        $isProtected = (bool) $item->getVar('items_protected');
        $parentId    = Request::getInt('items_pid', 0, 'POST');

        // Validate parent
        if ($parentId > 0) {
            $allItemRows    = [];
            $allItemsCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $catId));
            $allItemsObj    = $itemHandler->getObjects($allItemsCriteria);
            foreach ($allItemsObj as $obj) {
                $allItemRows[] = [
                    'items_id'  => (int) $obj->getVar('items_id'),
                    'items_pid' => (int) $obj->getVar('items_pid'),
                    'items_cid' => (int) $obj->getVar('items_cid'),
                ];
            }

            $validation = SystemMenusTree::validateParent(
                $itemId ?: PHP_INT_MAX, // New items can't be their own descendant
                $catId,
                $parentId,
                $allItemRows,
                MENUS_MAX_DEPTH
            );

            if ($validation !== true) {
                $errorMap = [
                    'self_parent'      => _AM_SYSTEM_MENUS_ERROR_ITEMPARENT,
                    'parent_not_found' => _AM_SYSTEM_MENUS_ERROR_ITEMPARENT,
                    'cross_category'   => _AM_SYSTEM_MENUS_ERROR_ITEMPARENT,
                    'cycle'            => _AM_SYSTEM_MENUS_ERROR_ITEMCYCLE,
                    'max_depth'        => _AM_SYSTEM_MENUS_ERROR_ITEMDEPTH,
                ];
                $msg = $errorMap[$validation] ?? _AM_SYSTEM_MENUS_ERROR_ITEMPARENT;
                redirect_header(MENUS_ADMIN_URL . '#cat_' . $catId, 3, $msg);
            }
        }

        $item->setVar('items_cid', $catId);
        $item->setVar('items_pid', $parentId);

        if (!$isProtected) {
            $item->setVar('items_title', Request::getString('items_title', '', 'POST'));
            $item->setVar('items_prefix', Request::getText('items_prefix', '', 'POST'));
            $item->setVar('items_suffix', Request::getText('items_suffix', '', 'POST'));
            $item->setVar('items_url', menus_sanitize_url(Request::getString('items_url', '', 'POST')));
        }
        $item->setVar('items_target', Request::getInt('items_target', 0, 'POST'));
        $item->setVar('items_position', Request::getInt('items_position', 0, 'POST'));
        $item->setVar('items_active', Request::getInt('items_active', 1, 'POST'));

        if (!$itemHandler->insert($item)) {
            $xoopsTpl->assign('error_message', $item->getHtmlErrors());
            break;
        }

        // Save permissions using XMF helper
        $permHelper = new \Xmf\Module\Helper\Permission();
        $permHelper->savePermissionForItem(
            'menus_items_view',
            (int) $item->getVar('items_id'),
            Request::getArray('menus_items_view_perms', [], 'POST')
        );

        redirect_header(MENUS_ADMIN_URL . '#cat_' . $catId, 2, _AM_SYSTEM_MENUS_SAVED);
        break;

    // --- DELETE ITEM ---
    case 'delitem':
        $itemId  = Request::getInt('item_id', 0, 'REQUEST');
        $confirm = Request::getInt('confirm', 0, 'POST');
        $item    = menus_item_handler()->get($itemId);

        if (!is_object($item) || $item->isNew()) {
            redirect_header(MENUS_ADMIN_URL, 3, _AM_SYSTEM_MENUS_ERROR_ITEMNOTFOUND);
        }
        $catId = (int) $item->getVar('items_cid');

        if ((int) $item->getVar('items_protected') === 1) {
            redirect_header(MENUS_ADMIN_URL . '#cat_' . $catId, 3, _AM_SYSTEM_MENUS_ERROR_ITEMPROTECTED);
        }
        if (0 === (int) $item->getVar('items_active')) {
            redirect_header(MENUS_ADMIN_URL . '#cat_' . $catId, 5, _AM_SYSTEM_MENUS_ERROR_ITEMDISABLE);
        }

        if ($confirm) {
            menus_require_token(false);

            // Collect and delete all descendants
            $itemHandler = menus_item_handler();
            $allCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $catId));
            $allItems    = $itemHandler->getObjects($allCriteria);

            $allRows = [];
            foreach ($allItems as $obj) {
                $allRows[] = [
                    'items_id'  => (int) $obj->getVar('items_id'),
                    'items_pid' => (int) $obj->getVar('items_pid'),
                    'items_cid' => (int) $obj->getVar('items_cid'),
                ];
            }
            $descendantIds = SystemMenusTree::collectDescendantIds($allRows, $itemId);

            $permHelper = new \Xmf\Module\Helper\Permission();

            // Delete descendants
            foreach ($descendantIds as $descId) {
                $descItem = $itemHandler->get($descId);
                if (is_object($descItem) && !(int) $descItem->getVar('items_protected')) {
                    $permHelper->deletePermissionForItem('menus_items_view', $descId);
                    $itemHandler->delete($descItem);
                }
            }

            // Delete the item itself
            $permHelper->deletePermissionForItem('menus_items_view', $itemId);
            $itemHandler->delete($item);

            redirect_header(MENUS_ADMIN_URL . '#cat_' . $catId, 2, _AM_SYSTEM_MENUS_DELETED);
        } else {
            xoops_confirm(
                [
                    'op'       => 'delitem',
                    'item_id' => $itemId,
                    'confirm'  => 1,
                ],
                MENUS_ADMIN_URL,
                sprintf(_AM_SYSTEM_MENUS_DELITEM_CONFIRM, htmlspecialchars($item->getAdminTitle(), ENT_QUOTES, 'UTF-8'))
            );
        }
        break;

    // --- SAVE CATEGORY ORDER (AJAX) ---
    case 'saveorder':
        menus_require_token(true);
        $order      = Request::getArray('order', [], 'POST');
        $catHandler = menus_cat_handler();

        foreach ($order as $position => $catId) {
            $cat = $catHandler->get((int) $catId);
            if (is_object($cat) && !$cat->isNew()) {
                $cat->setVar('category_position', $position);
                $catHandler->insert($cat);
            }
        }

        menus_send_json(true, ['message' => _AM_SYSTEM_MENUS_ORDER_SAVED]);
        break;

    // --- SAVE ITEM TREE ORDER (AJAX) ---
    case 'saveorderitems':
        menus_require_token(true);
        $catId    = Request::getInt('category_id', 0, 'POST');
        $treeJson = Request::getString('tree', '', 'POST');

        if ($catId <= 0 || $treeJson === '') {
            menus_send_json(false, ['message' => 'Invalid data']);
        }

        $tree = json_decode($treeJson, true);
        if (!is_array($tree)) {
            menus_send_json(false, ['message' => 'Invalid JSON']);
        }

        // Validate depth
        $depth = SystemMenusTree::computeDepth($tree);
        if ($depth > MENUS_MAX_DEPTH) {
            menus_send_json(false, ['message' => _AM_SYSTEM_MENUS_ERROR_ITEMDEPTH]);
        }

        // Walk the tree and update positions
        $itemHandler = menus_item_handler();
        $position    = 0;

        $walkTree = function (array $nodes, int $parentId) use (&$walkTree, $itemHandler, $catId, &$position): void {
            foreach ($nodes as $node) {
                $nodeItemId = (int) ($node['id'] ?? 0);
                if ($nodeItemId <= 0) {
                    continue;
                }
                $nodeItem = $itemHandler->get($nodeItemId);
                if (!is_object($nodeItem) || $nodeItem->isNew()) {
                    continue;
                }
                // Verify same category
                if ((int) $nodeItem->getVar('items_cid') !== $catId) {
                    continue;
                }
                $nodeItem->setVar('items_pid', $parentId);
                $nodeItem->setVar('items_position', $position++);
                $itemHandler->insert($nodeItem);

                if (!empty($node['children'])) {
                    $walkTree($node['children'], $nodeItemId);
                }
            }
        };

        $walkTree($tree, 0);
        menus_send_json(true, ['message' => _AM_SYSTEM_MENUS_ORDER_SAVED]);
        break;

    // --- TOGGLE CATEGORY ACTIVE (AJAX) ---
    case 'toggleactivecat':
        menus_require_token(true);
        $catId = Request::getInt('category_id', 0, 'POST');
        $cat   = menus_cat_handler()->get($catId);

        if (!is_object($cat) || $cat->isNew()) {
            menus_send_json(false, ['message' => _AM_SYSTEM_MENUS_ERROR_CATNOTFOUND]);
        }

        $newActive = $cat->getVar('category_active') ? 0 : 1;
        $cat->setVar('category_active', $newActive);
        menus_cat_handler()->insert($cat);

        // If deactivating, cascade to all items
        if ($newActive === 0) {
            $itemHandler = menus_item_handler();
            $criteria    = new CriteriaCompo(new Criteria('items_cid', (string) $catId));
            $items       = $itemHandler->getObjects($criteria);
            foreach ($items as $item) {
                $item->setVar('items_active', 0);
                $itemHandler->insert($item);
            }
        }

        menus_send_json(true, ['active' => $newActive]);
        break;

    // --- TOGGLE ITEM ACTIVE (AJAX) ---
    case 'toggleactiveitem':
        menus_require_token(true);
        $itemId = Request::getInt('item_id', 0, 'POST');
        $item   = menus_item_handler()->get($itemId);

        if (!is_object($item) || $item->isNew()) {
            menus_send_json(false, ['message' => _AM_SYSTEM_MENUS_ERROR_ITEMNOTFOUND]);
        }

        $newActive = $item->getVar('items_active') ? 0 : 1;

        // If activating, check the full parent chain (category + all ancestor items)
        if ($newActive === 1 && !menus_item_can_be_enabled(menus_item_handler(), $item)) {
            menus_send_json(false, ['message' => _AM_SYSTEM_MENUS_ERROR_PARENTINACTIVE]);
        }

        $item->setVar('items_active', $newActive);
        menus_item_handler()->insert($item);

        // If deactivating, cascade to descendants
        if ($newActive === 0) {
            $allCriteria = new CriteriaCompo(new Criteria('items_cid', (string) $item->getVar('items_cid')));
            $allItems    = menus_item_handler()->getObjects($allCriteria);
            $allRows     = [];
            foreach ($allItems as $obj) {
                $allRows[] = [
                    'items_id'  => (int) $obj->getVar('items_id'),
                    'items_pid' => (int) $obj->getVar('items_pid'),
                    'items_cid' => (int) $obj->getVar('items_cid'),
                ];
            }
            $descIds = SystemMenusTree::collectDescendantIds($allRows, $itemId);
            foreach ($descIds as $descId) {
                $descItem = menus_item_handler()->get($descId);
                if (is_object($descItem)) {
                    $descItem->setVar('items_active', 0);
                    menus_item_handler()->insert($descItem);
                }
            }
        }

        menus_send_json(true, ['active' => $newActive]);
        break;
} // end switch

if (!menus_is_ajax($op)) {
    xoops_cp_footer();
}
