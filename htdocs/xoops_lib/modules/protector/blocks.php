<?php

use XoopsModules\Protector;
use XoopsModules\Protector\Registry;

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

// language files
//$language = empty( $GLOBALS['xoopsConfig']['language'] ) ? 'english' : $GLOBALS['xoopsConfig']['language'] ;  //hack by Trabis
if (file_exists("$mydirpath/language/$language/blocks.php")) {
    // user customized language file (already read by class/xoopsblock.php etc)
    // require_once "$mydirpath/language/$language/blocks.php" ;
} elseif (file_exists("$mytrustdirpath/language/$language/blocks_common.php")) {
    // default language file
    require_once "$mytrustdirpath/language/$language/blocks_common.php";
    require "$mytrustdirpath/language/$language/blocks_each.php";
} else {
    // fallback english
    require_once "$mytrustdirpath/language/english/blocks_common.php";
    require "$mytrustdirpath/language/english/blocks_each.php";
}

require_once "$mytrustdirpath/blocks/block_functions.php";
