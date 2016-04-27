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

include dirname(dirname(dirname(dirname(__DIR__)))) . '/mainfile.php';
include $GLOBALS['xoops']->path('/include/cp_functions.php');
if (is_object($xoopsUser)) {
    $module_handler = xoops_getHandler('module');
    $xoopsModule    = $module_handler->getByDirname('system');
    if (!in_array(XOOPS_GROUP_ADMIN, $xoopsUser->getGroups())) {
        include_once $GLOBALS['xoops']->path('modules/system/constants.php');
        $sysperm_handler = xoops_getHandler('groupperm');
        if (!$sysperm_handler->checkRight('system_admin', XOOPS_SYSTEM_COMMENT, $xoopsUser->getGroups())) {
            redirect_header(XOOPS_URL . '/', 3, _NOPERM);
        }
    }
} else {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
}
