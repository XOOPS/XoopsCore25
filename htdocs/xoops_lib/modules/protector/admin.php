<?php
// start hack by Trabis
if (!class_exists('ProtectorRegistry')) {
    exit('Registry not found');
}

$registry  = ProtectorRegistry::getInstance();
$mydirname = $registry->getEntry('mydirname');
$mydirpath = $registry->getEntry('mydirpath');
$language  = $registry->getEntry('language');
// end hack by Trabis

$mytrustdirname = basename(__DIR__);
$mytrustdirpath = __DIR__;

// environment
require_once XOOPS_ROOT_PATH . '/class/template.php';
/* @var $module_handler XoopsModuleHandler  */
$module_handler    = xoops_getHandler('module');
$xoopsModule       = $module_handler->getByDirname($mydirname);
/* @var $config_handler XoopsConfigHandler  */
$config_handler    = xoops_getHandler('config');
$xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

// check permission of 'module_admin' of this module
/* @var $moduleperm_handler XoopsGroupPermHandler  */
$moduleperm_handler = xoops_getHandler('groupperm');
if (!is_object(@$xoopsUser) || !$moduleperm_handler->checkRight('module_admin', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
    die('only admin can access this area');
}

$xoopsOption['pagetype'] = 'admin';
require XOOPS_ROOT_PATH . '/include/cp_functions.php';

// language files (admin.php)
//$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;  //hack by Trabis
if (file_exists("$mydirpath/language/$language/admin.php")) {
    // user customized language file
    include_once "$mydirpath/language/$language/admin.php";
} elseif (file_exists("$mytrustdirpath/language/$language/admin.php")) {
    // default language file
    include_once "$mytrustdirpath/language/$language/admin.php";
} else {
    // fallback english
    include_once "$mytrustdirpath/language/english/admin.php";
}

// language files (main.php)
//$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;  //hack by Trabis
if (file_exists("$mydirpath/language/$language/main.php")) {
    // user customized language file
    include_once "$mydirpath/language/$language/main.php";
} elseif (file_exists("$mytrustdirpath/language/$language/main.php")) {
    // default language file
    include_once "$mytrustdirpath/language/$language/main.php";
} else {
    // fallback english
    include_once "$mytrustdirpath/language/english/main.php";
}

if (!empty($_GET['lib'])) {
    // common libs (eg. altsys)
    $lib  = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['lib']);
    $page = preg_replace('/[^a-zA-Z0-9_-]/', '', @$_GET['page']);

    if (file_exists(XOOPS_TRUST_PATH . '/libs/' . $lib . '/' . $page . '.php')) {
        include XOOPS_TRUST_PATH . '/libs/' . $lib . '/' . $page . '.php';
    } elseif (file_exists(XOOPS_TRUST_PATH . '/libs/' . $lib . '/index.php')) {
        include XOOPS_TRUST_PATH . '/libs/' . $lib . '/index.php';
    } else {
        die('wrong request');
    }
} else {
    // fork each pages of this module
    $page = preg_replace('/[^a-zA-Z0-9_-]/', '', @$_GET['page']);

    if (file_exists("$mytrustdirpath/admin/$page.php")) {
        include "$mytrustdirpath/admin/$page.php";
    } elseif (file_exists("$mytrustdirpath/admin/index.php")) {
        include "$mytrustdirpath/admin/index.php";
    } else {
        die('wrong request');
    }
}
