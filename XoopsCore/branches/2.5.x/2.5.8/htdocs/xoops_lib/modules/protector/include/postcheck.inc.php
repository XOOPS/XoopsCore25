<?php

require_once __DIR__.'/postcheck_functions.php' ;

if( ! defined( 'PROTECTOR_PRECHECK_INCLUDED' ) ) {
	require __DIR__.'/precheck.inc.php' ;
	return ;
}

define('PROTECTOR_POSTCHECK_INCLUDED' , 1 ) ;
if( ! class_exists( 'Database' ) ) return ;
protector_postcommon() ;
