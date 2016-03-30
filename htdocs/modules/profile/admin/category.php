<?php
// 
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//          Copyright (c) 2000-2016 XOOPS Project (www.xoops.org)            //
//                         <http://xoops.org/>                               //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: XOOPS Foundation                                                  //
// URL: http://www.xoops.org/                                                //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$indexAdmin = new ModuleAdmin();

$indexAdmin->addItemButton(_ADD . ' ' . _PROFILE_AM_CATEGORY, 'category.php?op=new', 'add', '');

echo $indexAdmin->addNavigation(basename(__FILE__));
echo $indexAdmin->renderButton('right', '');

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : (isset($_REQUEST['id']) ? 'edit' : 'list');

$handler = xoops_getModuleHandler('category');
switch ($op) {
    default:
    case 'list':
        $criteria = new CriteriaCompo();
        $criteria->setSort('cat_weight');
        $criteria->setOrder('ASC');
        $GLOBALS['xoopsTpl']->assign('categories', $handler->getObjects($criteria, true, false));
        $template_main = 'profile_admin_categorylist.tpl';
        break;

    case 'new':
        include_once dirname(__DIR__) . '/include/forms.php';
        $obj  = $handler->create();
        $form = $obj->getForm();
        $form->display();
        break;

    case 'edit':
        include_once dirname(__DIR__) . '/include/forms.php';
        $obj  = $handler->get($_REQUEST['id']);
        $form = $obj->getForm();
        $form->display();
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('category.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (isset($_REQUEST['id'])) {
            $obj = $handler->get($_REQUEST['id']);
        } else {
            $obj = $handler->create();
        }
        $obj->setVar('cat_title', $_REQUEST['cat_title']);
        $obj->setVar('cat_description', $_REQUEST['cat_description']);
        $obj->setVar('cat_weight', $_REQUEST['cat_weight']);
        if ($handler->insert($obj)) {
            redirect_header('category.php', 3, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_CATEGORY));
        }
        include_once dirname(__DIR__) . '/include/forms.php';
        echo $obj->getHtmlErrors();
        $form =& $obj->getForm();
        $form->display();
        break;

    case 'delete':
        $obj = $handler->get($_REQUEST['id']);
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('category.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($handler->delete($obj)) {
                redirect_header('category.php', 3, sprintf(_PROFILE_AM_DELETEDSUCCESS, _PROFILE_AM_CATEGORY));
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array(
                              'ok' => 1,
                              'id' => $_REQUEST['id'],
                              'op' => 'delete'), $_SERVER['REQUEST_URI'], sprintf(_PROFILE_AM_RUSUREDEL, $obj->getVar('cat_title')));
        }
        break;
}
if (isset($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
include_once __DIR__ . '/admin_footer.php';
