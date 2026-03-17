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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      system
 * @subpackage   menus
 * @since        2.5.12
 * @author       XOOPS Development Team, Grégory Mage (AKA GregMage)
 */

use Xmf\Request;
use Xmf\Module\Helper;

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}

// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_menus.tpl';

// Get Action type
$op = Request::getCmd('op', 'list');

// Call Header
if ($op !== 'saveorder' && $op !== 'saveorderitems' && $op !== 'toggleactivecat' && $op !== 'toggleactiveitem') {
    xoops_cp_header();
    $xoopsTpl->assign('op', $op);
    $xoopsTpl->assign('xoops_token', $GLOBALS['xoopsSecurity']->getTokenHTML());

    // Define Stylesheet
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/menus.css');
    // Define scripts
    $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
    $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
    $xoTheme->addScript('modules/system/js/nestedSortable.js');
    $xoTheme->addScript('modules/system/js/admin.js');
    $xoTheme->addScript('modules/system/js/menus.js');
}

$helper = Helper::getHelper('system');
$nb_limit = $helper->getConfig('avatars_pager', 15);

switch ($op) {
    case 'list':
    default:
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addTips(sprintf(_AM_SYSTEM_MENUS_NAV_TIPS, $GLOBALS['xoopsConfig']['language']));
        $xoBreadCrumb->render();
        $start = Request::getInt('start', 0);
        /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
        $menuscategoryHandler = xoops_getHandler('menuscategory');
        $criteria = new CriteriaCompo();
        $criteria->setSort('category_position');
        $criteria->setOrder('ASC');
        $criteria->setStart($start);
        $criteria->setLimit($nb_limit);
        $category_arr = $menuscategoryHandler->getall($criteria);
        $category_count = $menuscategoryHandler->getCount($criteria);
        $xoopsTpl->assign('category_count', $category_count);
        if ($category_count > 0) {
            /** @var \XoopsMenusItemsHandler $menusitemsHandler */
            $menusitemsHandler = xoops_getHandler('menusitems');
            xoops_load('SystemMenusTree', 'system');
            foreach (array_keys($category_arr) as $i) {
                $cid = $category_arr[$i]->getVar('category_id');
                $category = array();
                $category['id']              = $cid;
                $category['title']           = $category_arr[$i]->getAdminTitle();
                $category['prefix']          = $category_arr[$i]->getVar('category_prefix');
                $category['suffix']          = $category_arr[$i]->getVar('category_suffix');
                $category['url']             = $category_arr[$i]->getVar('category_url');
                $category['target']          = ($category_arr[$i]->getVar('category_target') == 1) ? '_blank' : '_self';
                $category['position']        = $category_arr[$i]->getVar('category_position');
                $category['active']          = $category_arr[$i]->getVar('category_active');
                $category['protected']       = $category_arr[$i]->getVar('category_protected');
                // Fetch items for this category
                $crit = new CriteriaCompo();
                $crit->add(new Criteria('items_cid', $cid));
                $crit->setSort('items_position');
                $crit->setOrder('ASC');
                $items_arr = $menusitemsHandler->getall($crit);
                $items_count = $menusitemsHandler->getCount($crit);
                $category['items_count'] = $items_count;
                $category['items'] = [];
                if ($items_count > 0) {
                    foreach (array_keys($items_arr) as $j) {
                        $item = [];
                        $item['id']        = $items_arr[$j]->getVar('items_id');
                        $item['title']     = $items_arr[$j]->getAdminTitle();
                        $item['prefix']    = $items_arr[$j]->getVar('items_prefix');
                        $item['suffix']    = $items_arr[$j]->getVar('items_suffix');
                        $item['url']       = $items_arr[$j]->getVar('items_url');
                        $item['target']    = ($items_arr[$j]->getVar('items_target') == 1) ? '_blank' : '_self';
                        $item['active']    = $items_arr[$j]->getVar('items_active');
                        $item['protected'] = $items_arr[$j]->getVar('items_protected');
                        $item['pid']       = (int)$items_arr[$j]->getVar('items_pid');
                        $category['items'][] = $item;
                    }
                }
                $xoopsTpl->append('category', $category);
                unset($category);
            }
            // Display Page Navigation
            if ($category_count > $nb_limit) {
                $nav = new XoopsPageNav($category_count, $nb_limit, $start, 'start');
                $xoopsTpl->assign('nav_menu', $nav->renderNav(4));
            }
        } else {
            $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
        }
        break;

    case 'addcat':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->render();
        // Form
        /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
        $menuscategoryHandler = xoops_getHandler('menuscategory');
        /** @var \XoopsMenusCategory $obj */
        $obj                  = $menuscategoryHandler->create();
        $form = $obj->getFormCat();
        $xoopsTpl->assign('form', $form->render());
        break;

    case 'editcat':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->render();
        // Form
        $category_id = Request::getInt('category_id', 0);
        if ($category_id == 0) {
            $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
        } else {
            /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
            $menuscategoryHandler = xoops_getHandler('menuscategory');
            /** @var \XoopsMenusCategory $obj */
            $obj = $menuscategoryHandler->get($category_id);
            if (!is_object($obj)) {
                $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
            } else {
                $form = $obj->getFormCat();
                $xoopsTpl->assign('form', $form->render());
            }
        }
        break;

    case 'savecat':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=menus', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
        $menuscategoryHandler = xoops_getHandler('menuscategory');
        $id = Request::getInt('category_id', 0);
        $isProtected = false;
        /** @var \XoopsMenusCategory $obj */
        if ($id > 0) {
            $obj = $menuscategoryHandler->get($id);
            if (!is_object($obj)) {
                redirect_header('admin.php?fct=menus', 3, _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
            }
            $isProtected = (int)$obj->getVar('category_protected') === 1;
        } else {
            $obj = $menuscategoryHandler->create();
        }
        // Server-side lock: protected categories keep immutable label and rendering fields.
        if (!$isProtected) {
            $obj->setVar('category_title', Request::getString('category_title', ''));
            $obj->setVar('category_prefix', Request::getText('category_prefix', ''));
            $obj->setVar('category_suffix', Request::getText('category_suffix', ''));
            $obj->setVar('category_url', Request::getString('category_url', ''));
        }
        $obj->setVar('category_target', Request::getInt('category_target', 0));
        $obj->setVar('category_position', Request::getInt('category_position', 0));
        $obj->setVar('category_active', Request::getInt('category_active', 1));
        /** @var \XoopsMenusCategory $obj */
        if ($menuscategoryHandler->insert($obj)) {
            // permissions
            if ($obj->get_new_enreg() == 0) {
                $perm_id = $obj->getVar('category_id');
            } else {
                $perm_id = $obj->get_new_enreg();
            }
            $permHelper = new \Xmf\Module\Helper\Permission();
            // permission view
            $groups_view = Request::getArray('menus_category_view_perms', [], 'POST');
            $permHelper->savePermissionForItem('menus_category_view', $perm_id, $groups_view);
            redirect_header('admin.php?fct=menus', 2, _AM_SYSTEM_DBUPDATED);
        } else {
            $xoopsTpl->assign('error_message', $obj->getHtmlErrors());
        }
        break;

    case 'delcat':
        $category_id = Request::getInt('category_id', 0);
        if ($category_id == 0) {
            redirect_header('admin.php?fct=menus', 3, _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
        } else {
            $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
            $xoBreadCrumb->render();
            $surdel = Request::getBool('surdel', false);
            /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
            $menuscategoryHandler = xoops_getHandler('menuscategory');
            /** @var \XoopsMenusItemsHandler $menusitemsHandler */
            $menusitemsHandler = xoops_getHandler('menusitems');
            /** @var \XoopsMenusCategory $obj */
            $obj = $menuscategoryHandler->get($category_id);
            if (!is_object($obj)) {
                redirect_header('admin.php?fct=menus', 3, _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
            }
            if ((int)$obj->getVar('category_protected') === 1) {
                redirect_header('admin.php?fct=menus', 3, _AM_SYSTEM_MENUS_ERROR_CATPROTECTED);
            }
            if ($surdel === true) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('admin.php?fct=menus', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                if ($menuscategoryHandler->delete($obj)) {
                    // Del permissions category
                    $permHelper = new \Xmf\Module\Helper\Permission();
                    $permHelper->deletePermissionForItem('menus_category_view', $category_id);
                    // delete items in this category
                    $criteria = new CriteriaCompo();
                    $criteria->add(new Criteria('items_cid', $category_id));
                    $items_arr = $menusitemsHandler->getall($criteria);
                    foreach (array_keys($items_arr) as $i) {
                        $permHelper = new \Xmf\Module\Helper\Permission();
                        $permHelper->deletePermissionForItem('menus_items_view', $items_arr[$i]->getVar('items_id'));
                        $menusitemsHandler->delete($items_arr[$i]);
                    }
                    redirect_header('admin.php?fct=menus', 2, _AM_SYSTEM_DBUPDATED);
                } else {
                    $xoopsTpl->assign('error_message', $obj->getHtmlErrors());
                }
            } else {
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('items_cid', $category_id));
                $items_arr = $menusitemsHandler->getall($criteria);
                $items = '<br>';
                foreach (array_keys($items_arr) as $i) {
                        $items .= '#' . $items_arr[$i]->getVar('items_id') . ': ' . $items_arr[$i]->getVar('items_title') . '<br>';
                }
                xoops_confirm([
                    'surdel'      => true,
                    'category_id' => $category_id,
                    'op'          => 'delcat'
                ], $_SERVER['REQUEST_URI'], sprintf(_AM_SYSTEM_MENUS_SUREDELCAT, (string)$obj->getVar('category_title')) . $items);
            }
        }
        break;

    case 'delitem':
        $item_id = Request::getInt('item_id', 0);
        $category_id = Request::getInt('category_id', 0);
        if ($item_id == 0) {
            redirect_header('admin.php?fct=menus', 3, _AM_SYSTEM_MENUS_ERROR_NOITEM);
        } else {
            $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
            $xoBreadCrumb->render();
            include_once $GLOBALS['xoops']->path('class/tree.php');
            $surdel = Request::getBool('surdel', false);
            /** @var \XoopsMenusItemsHandler $menusitemsHandler */
            $menusitemsHandler = xoops_getHandler('menusitems');
            /** @var \XoopsMenusItems $obj */
            $obj = $menusitemsHandler->get($item_id);
            if (!is_object($obj)) {
                redirect_header('admin.php?fct=menus&op=viewcat&category_id=' . $category_id, 3, _AM_SYSTEM_MENUS_ERROR_NOITEM);
            }
            if ((int)$obj->getVar('items_protected') === 1) {
                redirect_header('admin.php?fct=menus&op=viewcat&category_id=' . $category_id, 5, _AM_SYSTEM_MENUS_ERROR_ITEMPROTECTED);
            }
            if ($obj->getVar('items_active') == 0) {
                redirect_header('admin.php?fct=menus&op=viewcat&category_id=' . $category_id, 5, _AM_SYSTEM_MENUS_ERROR_ITEMDISABLE);
            }
            if ($surdel === true) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('admin.php?fct=menus', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                if ($menusitemsHandler->delete($obj)) {
                    $permHelper = new \Xmf\Module\Helper\Permission();
                    $permHelper->deletePermissionForItem('menus_items_view', $item_id);
                    // delete subitems of this item
                    $criteria = new CriteriaCompo();
                    $criteria->add(new Criteria('items_cid', (int)$obj->getVar('items_cid')));
                    $items_arr = $menusitemsHandler->getall($criteria);
                    $myTree = new XoopsObjectTree($items_arr, 'items_id', 'items_pid');
                    $items_arr = $myTree->getAllChild($item_id);
                    foreach (array_keys($items_arr) as $i) {
                        $permHelper = new \Xmf\Module\Helper\Permission();
                        $permHelper->deletePermissionForItem('menus_items_view', $items_arr[$i]->getVar('items_id'));
                        $menusitemsHandler->delete($items_arr[$i]);
                    }
                    redirect_header('admin.php?fct=menus&op=viewcat&category_id=' . $category_id, 2, _AM_SYSTEM_DBUPDATED);
                } else {
                    $xoopsTpl->assign('error_message', $obj->getHtmlErrors());
                }
            } else {
                $objCid = (int)$obj->getVar('items_cid');
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('items_cid', $objCid));
                $items_arr = $menusitemsHandler->getall($criteria);
                $myTree = new XoopsObjectTree($items_arr, 'items_id', 'items_pid');
                $items_arr = $myTree->getAllChild($item_id);
                $items = '<br>';
                foreach (array_keys($items_arr) as $i) {
                        $items .= '#' . $items_arr[$i]->getVar('items_id') . ': ' . $items_arr[$i]->getVar('items_title') . '<br>';
                }
                xoops_confirm([
                    'surdel'      => true,
                    'item_id'     => $item_id,
                    'category_id' => $objCid,
                    'op'          => 'delitem'
                ], $_SERVER['REQUEST_URI'], sprintf(_AM_SYSTEM_MENUS_SUREDELITEM, (string)$obj->getVar('items_title')) . $items);
            }
        }
        break;

    case 'saveorder':
        if (isset($GLOBALS['xoopsLogger']) && is_object($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->activated = false;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        if (!$GLOBALS['xoopsSecurity']->check()) {
            header('Content-Type: application/json');
            $errors = $GLOBALS['xoopsSecurity']->getErrors();
            echo json_encode([
                'success' => false,
                'message' => implode(' ', $errors),
                'token'   => $GLOBALS['xoopsSecurity']->getTokenHTML()
            ]);
            exit;
        }

        $order = Request::getArray('order', []);
        if (count($order) === 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No order provided', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
            exit;
        }

        /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
        $menuscategoryHandler = xoops_getHandler('menuscategory');

        $pos = 1;
        $errors = [];
        foreach ($order as $id) {
            $id = (int)$id;
            if ($id <= 0) continue;
            $obj = $menuscategoryHandler->get($id);
            if (is_object($obj)) {
                $obj->setVar('category_position', $pos);
                if (!$menuscategoryHandler->insert($obj, true)) {
                    $errors[] = "Failed to update id {$id}";
                }
            } else {
                $errors[] = "Not found id {$id}";
            }
            $pos++;
        }

        header('Content-Type: application/json');
        if (empty($errors)) {
            echo json_encode(['success' => true, 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
        } else {
            echo json_encode(['success' => false, 'message' => implode('; ', $errors), 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
        }
        exit;
        break;

    case 'saveorderitems':
        if (isset($GLOBALS['xoopsLogger']) && is_object($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->activated = false;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        if (!$GLOBALS['xoopsSecurity']->check()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => implode(' ', $GLOBALS['xoopsSecurity']->getErrors()),
                'token'   => $GLOBALS['xoopsSecurity']->getTokenHTML()
            ]);
            exit;
        }

        // nestedSortable sends serialized data: item[id]=parentId (or null for root)
        $serialized = Request::getString('item', '', 'POST');
        if (empty($serialized)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No order provided', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
            exit;
        }

        $parsed = [];
        parse_str($serialized, $parsed);
        $itemOrder = $parsed['item'] ?? [];

        /** @var \XoopsMenusItemsHandler $menusitemsHandler */
        $menusitemsHandler = xoops_getHandler('menusitems');

        // Build proposed parent map and validate items exist with same category
        $parentMap = [];
        $itemObjects = [];
        $errors = [];
        $referenceCid = null;
        foreach ($itemOrder as $id => $parentId) {
            $id = (int)$id;
            if ($id <= 0) continue;
            $obj = $menusitemsHandler->get($id);
            if (!is_object($obj)) {
                $errors[] = "Item not found id {$id}";
                continue;
            }
            $itemObjects[$id] = $obj;
            $newPid = !empty($parentId) ? (int)$parentId : 0;
            if ($newPid === $id) {
                $errors[] = "Item {$id} cannot be its own parent";
                continue;
            }
            if ($newPid > 0 && !isset($itemOrder[$newPid])) {
                // Parent not in payload — check it exists in DB with same category
                $parentObj = $menusitemsHandler->get($newPid);
                if (!is_object($parentObj)) {
                    $errors[] = "Parent {$newPid} not found for item {$id}";
                    continue;
                }
                if ((int)$parentObj->getVar('items_cid') !== (int)$obj->getVar('items_cid')) {
                    $errors[] = "Parent {$newPid} belongs to a different category than item {$id}";
                    continue;
                }
            }
            $parentMap[$id] = $newPid;
        }

        // Validate full graph: no cycles, depth <= 3
        if (empty($errors)) {
            foreach ($parentMap as $id => $pid) {
                $visited = [$id];
                $current = $pid;
                $depth = 0;
                while ($current > 0) {
                    if (in_array($current, $visited)) {
                        $errors[] = "Cycle detected involving item {$id}";
                        break;
                    }
                    $visited[] = $current;
                    // Use proposed parent if in map, otherwise fall back to DB
                    if (isset($parentMap[$current])) {
                        $current = $parentMap[$current];
                    } else {
                        $dbObj = $menusitemsHandler->get($current);
                        $current = is_object($dbObj) ? (int)$dbObj->getVar('items_pid') : 0;
                    }
                    $depth++;
                    if ($depth >= 3) {
                        $errors[] = "Depth limit exceeded for item {$id}";
                        break;
                    }
                }
            }
        }

        // Only write if all validations passed
        if (empty($errors)) {
            $pos = 1;
            foreach ($itemOrder as $id => $parentId) {
                $id = (int)$id;
                if ($id <= 0 || !isset($itemObjects[$id]) || !isset($parentMap[$id])) {
                    $pos++;
                    continue;
                }
                $obj = $itemObjects[$id];
                $obj->setVar('items_position', $pos);
                $obj->setVar('items_pid', $parentMap[$id]);
                if (!$menusitemsHandler->insert($obj, true)) {
                    $errors[] = "Failed to update item id {$id}";
                }
                $pos++;
            }
        }

        header('Content-Type: application/json');
        if (empty($errors)) {
            echo json_encode(['success' => true, 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
        } else {
            echo json_encode(['success' => false, 'message' => implode('; ', $errors), 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
        }
        exit;
        break;

    case 'viewcat':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_CATEGORY);
        $xoBreadCrumb->render();
        $category_id = Request::getInt('category_id', 0);
        $xoopsTpl->assign('category_id', $category_id);
        if ($category_id == 0) {
            $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
        } else {
            /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
            $menuscategoryHandler = xoops_getHandler('menuscategory');
            /** @var \XoopsMenusCategory $category */
            $category = $menuscategoryHandler->get($category_id);
            if (!is_object($category)) {
                $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
                break;
            }
            $xoopsTpl->assign('category_id', $category->getVar('category_id'));
            $xoopsTpl->assign('cat_title', $category->getAdminTitle());

            /** @var \XoopsMenusItemsHandler $menusitemsHandler */
            $menusitemsHandler = xoops_getHandler('menusitems');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('items_cid', $category_id));
            $criteria->setSort('items_position');
            $criteria->setOrder('ASC');
            $items_arr = $menusitemsHandler->getall($criteria);
            $items_count = $menusitemsHandler->getCount($criteria);
            $xoopsTpl->assign('items_count', $items_count);
            if ($items_count > 0) {
                foreach (array_keys($items_arr) as $i) {
                    $item = [];
                    $item['id']        = $items_arr[$i]->getVar('items_id');
                    $item['title']     = $items_arr[$i]->getAdminTitle();
                    $item['prefix']    = $items_arr[$i]->getVar('items_prefix');
                    $item['suffix']    = $items_arr[$i]->getVar('items_suffix');
                    $item['url']       = $items_arr[$i]->getVar('items_url');
                    $item['target']    = ($items_arr[$i]->getVar('items_target') == 1) ? '_blank' : '_self';
                    $item['active']    = $items_arr[$i]->getVar('items_active');
                    $item['protected'] = $items_arr[$i]->getVar('items_protected');
                    $item['pid']       = (int)$items_arr[$i]->getVar('items_pid');
                    $item['cid']       = $items_arr[$i]->getVar('items_cid');
                    $xoopsTpl->append('items', $item);
                    unset($item);
                }
            }
        }
        break;

    case 'additem':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_CATEGORY);
        $xoBreadCrumb->render();
        $category_id = Request::getInt('category_id', 0);
        $xoopsTpl->assign('category_id', $category_id);
        // Form
        /** @var \XoopsMenusItemsHandler $menusitemsHandler */
        $menusitemsHandler = xoops_getHandler('menusitems');
        /** @var \XoopsMenusItems $obj */
        $obj = $menusitemsHandler->create();
        $form = $obj->getFormItems($category_id);
        $xoopsTpl->assign('form', $form->render());
        break;

    case 'saveitem':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=menus', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_CATEGORY);
        $xoBreadCrumb->render();

        /** @var \XoopsMenusItemsHandler $menusitemsHandler */
        $menusitemsHandler = xoops_getHandler('menusitems');
        $id = Request::getInt('items_id', 0);
        $isProtected = false;
        /** @var \XoopsMenusItems $obj */
        if ($id > 0) {
            $obj = $menusitemsHandler->get($id);
            if (!is_object($obj)) {
                redirect_header('admin.php?fct=menus', 3, _AM_SYSTEM_MENUS_ERROR_NOITEM);
            }
            $isProtected = (int)$obj->getVar('items_protected') === 1;
        } else {
            $obj = $menusitemsHandler->create();
        }
        $items_cid = Request::getInt('items_cid', 0);
        $obj->setVar('items_cid', $items_cid);
        $error_message = '';
        if (!$isProtected) {
            $itempid = Request::getInt('items_pid', 0);
            if ($itempid != 0 && $itempid == $id) {
                $error_message .= _AM_SYSTEM_MENUS_ERROR_ITEMPARENT;
            } elseif ($itempid != 0) {
                // Verify parent exists and belongs to same category
                $parentItem = $menusitemsHandler->get($itempid);
                if (!is_object($parentItem)) {
                    $error_message .= _AM_SYSTEM_MENUS_ERROR_ITEMPARENT;
                } elseif ((int)$parentItem->getVar('items_cid') !== $items_cid) {
                    $error_message .= _AM_SYSTEM_MENUS_ERROR_ITEMPARENT;
                } else {
                    // Walk ancestor chain to detect cycles and enforce depth limit
                    $depth = 1;
                    $ancestorId = (int)$parentItem->getVar('items_pid');
                    $isCycle = false;
                    while ($ancestorId > 0) {
                        if ($id > 0 && $ancestorId === $id) {
                            $isCycle = true;
                            break;
                        }
                        $ancestor = $menusitemsHandler->get($ancestorId);
                        if (!is_object($ancestor)) {
                            break;
                        }
                        $ancestorId = (int)$ancestor->getVar('items_pid');
                        $depth++;
                    }
                    if ($isCycle) {
                        $error_message .= _AM_SYSTEM_MENUS_ERROR_ITEMCYCLE;
                    } elseif ($depth >= 3) {
                        $error_message .= _AM_SYSTEM_MENUS_ERROR_ITEMDEPTH;
                    } else {
                        $obj->setVar('items_pid', $itempid);
                    }
                }
            } else {
                $obj->setVar('items_pid', 0);
            }
        }
        if (!$isProtected) {
            $obj->setVar('items_title', Request::getString('items_title', ''));
            $obj->setVar('items_prefix', Request::getText('items_prefix', ''));
            $obj->setVar('items_suffix', Request::getText('items_suffix', ''));
            $obj->setVar('items_url', Request::getString('items_url', ''));
        }
        $obj->setVar('items_position', Request::getInt('items_position', 0));
        $obj->setVar('items_target', Request::getInt('items_target', 0));
        $obj->setVar('items_active', Request::getInt('items_active', 1));
        /** @var \XoopsMenusItems $obj */
        if ($error_message == '') {
            if ($menusitemsHandler->insert($obj)) {
                // permissions
                if ($obj->get_new_enreg() == 0) {
                    $perm_id = $obj->getVar('items_id');
                } else {
                    $perm_id = $obj->get_new_enreg();
                }
                $permHelper = new \Xmf\Module\Helper\Permission();
                $groups_view = Request::getArray('menus_items_view_perms', [], 'POST');
                $permHelper->savePermissionForItem('menus_items_view', $perm_id, $groups_view);
                redirect_header('admin.php?fct=menus&op=viewcat&category_id=' . $items_cid, 2, _AM_SYSTEM_DBUPDATED);
            } else {
                $xoopsTpl->assign('category_id', $items_cid);
                $htmlErrors = $obj->getHtmlErrors();
                if (empty($htmlErrors) || $htmlErrors === 'None' || trim(strip_tags($htmlErrors)) === 'None') {
                    $htmlErrors = $GLOBALS['xoopsDB']->error();
                }
                $xoopsTpl->assign('error_message', $htmlErrors);
                /** @var \XoopsMenusItems $obj */
                $form = $obj->getFormItems($items_cid);
                $xoopsTpl->assign('form', $form->render());
            }
        } else {
            /** @var \XoopsMenusItems $obj */
            $form = $obj->getFormItems($items_cid);
            $xoopsTpl->assign('form', $form->render());
            $xoopsTpl->assign('error_message', $error_message);
        }
        break;

    case 'edititem':
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_MAIN, system_adminVersion('menus', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MENUS_NAV_CATEGORY);
        $xoBreadCrumb->render();
        $item_id = Request::getInt('item_id', 0);
        $category_id = Request::getInt('category_id', 0);
        $xoopsTpl->assign('category_id', $category_id);
        if ($item_id == 0 || $category_id == 0) {
            if ($item_id == 0) {
                $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOITEM);
            }
            if ($category_id == 0) {
                $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOCATEGORY);
            }
        } else {
            /** @var \XoopsMenusItemsHandler $menusitemsHandler */
            $menusitemsHandler = xoops_getHandler('menusitems');
            /** @var \XoopsMenusItems $obj */
            $obj = $menusitemsHandler->get($item_id);
            if (!is_object($obj)) {
                $xoopsTpl->assign('error_message', _AM_SYSTEM_MENUS_ERROR_NOITEM);
            } elseif ($obj->getVar('items_active') == 0) {
                redirect_header('admin.php?fct=menus&op=viewcat&category_id=' . $category_id, 5, _AM_SYSTEM_MENUS_ERROR_ITEMEDIT);
            } else {
                $form = $obj->getFormItems($category_id);
                $xoopsTpl->assign('form', $form->render());
            }
        }
        break;

    case 'toggleactivecat':
        if (isset($GLOBALS['xoopsLogger']) && is_object($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->activated = false;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        if (!$GLOBALS['xoopsSecurity']->check()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => implode(' ', $GLOBALS['xoopsSecurity']->getErrors()),
                'token'   => $GLOBALS['xoopsSecurity']->getTokenHTML()
            ]);
            exit;
        }

        $category_id = Request::getInt('category_id', 0);
        if ($category_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid id', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
            exit;
        }

        /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
        $menuscategoryHandler = xoops_getHandler('menuscategory');

        /** @var \XoopsMenusCategory $obj */
        $obj = $menuscategoryHandler->get($category_id);
        if (!is_object($obj)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not found', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
            exit;
        }
        $new = $obj->getVar('category_active') ? 0 : 1;
        $obj->setVar('category_active', $new);
        $res = $menuscategoryHandler->insert($obj, true);

        // Only cascade on deactivation; preserve child state on activation
        $updatedItems = [];
        if ($res && $new === 0) {
            /** @var \XoopsMenusItemsHandler $menusitemsHandler */
            $menusitemsHandler = xoops_getHandler('menusitems');
            $critCat = new Criteria('items_cid', $category_id);
            $allItems = $menusitemsHandler->getAll($critCat);
            foreach ($allItems as $itm) {
                if ((int)$itm->getVar('items_active') !== 0) {
                    $itm->setVar('items_active', 0);
                    if ($menusitemsHandler->insert($itm, true)) {
                        $updatedItems[] = $itm->getVar('items_id');
                    }
                }
            }
        }

        header('Content-Type: application/json');
        if ($res) {
            $response = ['success' => true, 'active' => (int)$new, 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()];
            if (!empty($updatedItems)) {
                $response['updated'] = array_values($updatedItems);
            }
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Save failed', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
        }
        exit;
        break;

    case 'toggleactiveitem':
        if (isset($GLOBALS['xoopsLogger']) && is_object($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->activated = false;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!$GLOBALS['xoopsSecurity']->check()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => implode(' ', $GLOBALS['xoopsSecurity']->getErrors()),
                'token'   => $GLOBALS['xoopsSecurity']->getTokenHTML()
            ]);
            exit;
        }

        $item_id = Request::getInt('item_id', 0);
        if ($item_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid id', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
            exit;
        }

        /** @var \XoopsMenusItemsHandler $menusitemsHandler */
        $menusitemsHandler = xoops_getHandler('menusitems');

        /** @var \XoopsMenusItems $obj */
        $obj = $menusitemsHandler->get($item_id);
        if (!is_object($obj)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not found', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
            exit;
        }

        $current = (int)$obj->getVar('items_active');
        $new = $current ? 0 : 1;
        // if activating, ensure owning category and ancestors are active
        if ($new) {
            /** @var \XoopsMenusCategoryHandler $menuscategoryHandler */
            $menuscategoryHandler = xoops_getHandler('menuscategory');
            $ownerCat = $menuscategoryHandler->get((int)$obj->getVar('items_cid'));
            if (is_object($ownerCat) && (int)$ownerCat->getVar('category_active') === 0) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => _AM_SYSTEM_MENUS_ERROR_PARENTINACTIVE,
                    'token'   => $GLOBALS['xoopsSecurity']->getTokenHTML()
                ]);
                exit;
            }
            $parentId = (int)$obj->getVar('items_pid');
            while ($parentId > 0) {
                $parentObj = $menusitemsHandler->get($parentId);
                if (!is_object($parentObj)) {
                    break;
                }
                if ((int)$parentObj->getVar('items_active') === 0) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => _AM_SYSTEM_MENUS_ERROR_PARENTINACTIVE,
                        'token'   => $GLOBALS['xoopsSecurity']->getTokenHTML()
                    ]);
                    exit;
                }
                $parentId = (int)$parentObj->getVar('items_pid');
            }
        }
        $obj->setVar('items_active', $new);
        $res = $menusitemsHandler->insert($obj, true);

        // Only cascade on deactivation; preserve child state on activation
        $updatedChildren = [];
        if ($res && $new === 0) {
            $propagateDeactivation = function ($handler, $parentId, array &$updated) use (&$propagateDeactivation) {
                $criteria = new Criteria('items_pid', (int)$parentId);
                $children = $handler->getAll($criteria);
                foreach ($children as $child) {
                    $childId = $child->getVar('items_id');
                    if ((int)$child->getVar('items_active') !== 0) {
                        $child->setVar('items_active', 0);
                        if ($handler->insert($child, true)) {
                            $updated[] = $childId;
                        }
                    }
                    $propagateDeactivation($handler, $childId, $updated);
                }
            };

            $propagateDeactivation($menusitemsHandler, $item_id, $updatedChildren);
        }

        header('Content-Type: application/json');
        if ($res) {
            $response = ['success' => true, 'active' => (int)$new, 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()];
            if (!empty($updatedChildren)) {
                $response['updated'] = array_values($updatedChildren);
            }
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Save failed', 'token' => $GLOBALS['xoopsSecurity']->getTokenHTML()]);
        }
        exit;
        break;

}
if ($op !== 'saveorder' && $op !== 'toggleactivecat' && $op !== 'toggleactiveitem') {
    // Call Footer
    xoops_cp_footer();
}
