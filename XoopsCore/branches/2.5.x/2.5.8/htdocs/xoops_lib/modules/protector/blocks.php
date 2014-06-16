<?php
// start hack by Trabis
if (!class_exists('ProtectorRegistry')) exit('Registry not found');

$registry   =& ProtectorRegistry::getInstance();
$mydirname  = $registry->getEntry('mydirname');
$mydirpath  = $registry->getEntry('mydirpath');
$language   = $registry->getEntry('language');
// end hack by Trabis

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// language files
//$language = empty( $GLOBALS['xoopsConfig']['language'] ) ? 'english' : $GLOBALS['xoopsConfig']['language'] ;  //hack by Trabis
if( file_exists( "$mydirpath/language/$language/blocks.php" ) ) {
	// user customized language file (already read by class/xoopsblock.php etc)
	// include_once "$mydirpath/language/$language/blocks.php" ;
} else if( file_exists( "$mytrustdirpath/language/$language/blocks_common.php" ) ) {
	// default language file
	include_once "$mytrustdirpath/language/$language/blocks_common.php" ;
	include "$mytrustdirpath/language/$language/blocks_each.php" ;
} else {
	// fallback english
	include_once "$mytrustdirpath/language/english/blocks_common.php" ;
	include "$mytrustdirpath/language/english/blocks_each.php" ;
}

require_once "$mytrustdirpath/blocks/block_functions.php" ;
