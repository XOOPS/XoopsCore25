<?php

use XoopsModules\Protector;
use XoopsModules\Protector\Registry;

require __DIR__ . '/preloads/autoloader.php';

// start hack by Trabis
if (!class_exists('XoopsModules\Protector\Registry')) {
    exit('Registry not found');
}

$registry  = Registry::getInstance();
$mydirname = $registry->getEntry('mydirname');
$mydirpath = $registry->getEntry('mydirpath');
$language  = $registry->getEntry('language');
// end hack by Trabis

$mytrustdirname = basename(__DIR__);
$mytrustdirpath = __DIR__;

// check permission of 'module_read' of this module
// (already checked by common.php)

// language files
// $language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ; //hack by Trabis
if (file_exists("$mydirpath/language/$language/main.php")) {
    // user customized language file (already read by common.php)
    // require_once "$mydirpath/language/$language/main.php" ;
} elseif (file_exists("$mytrustdirpath/language/$language/main.php")) {
    // default language file
    require_once "$mytrustdirpath/language/$language/main.php";
} else {
    // fallback english
    require_once "$mytrustdirpath/language/english/main.php";
}

// fork each pages
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', @$_GET['page']);

if (file_exists("$mytrustdirpath/main/$page.php")) {
    include "$mytrustdirpath/main/$page.php";
} else {
    include "$mytrustdirpath/main/index.php";
}
