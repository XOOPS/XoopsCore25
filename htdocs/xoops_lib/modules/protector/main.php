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

// check permission of 'module_read' of this module
// (already checked by common.php)

// language files
// $language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ; //hack by Trabis
if (file_exists("$mydirpath/language/$language/main.php")) {
    // user customized language file (already read by common.php)
    // include_once "$mydirpath/language/$language/main.php" ;
} elseif (file_exists("$mytrustdirpath/language/$language/main.php")) {
    // default language file
    include_once "$mytrustdirpath/language/$language/main.php";
} else {
    // fallback english
    include_once "$mytrustdirpath/language/english/main.php";
}

// fork each pages
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', @$_GET['page']);

if (file_exists("$mytrustdirpath/main/$page.php")) {
    include "$mytrustdirpath/main/$page.php";
} else {
    include "$mytrustdirpath/main/index.php";
}
