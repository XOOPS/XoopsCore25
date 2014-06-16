<?php

require_once dirname(__FILE__).'/precheck_functions.php' ;

if( class_exists( 'Database' ) ) {
	require dirname(__FILE__).'/postcheck.inc.php' ;
	return ;
}

define('PROTECTOR_PRECHECK_INCLUDED' , 1 ) ;
define('PROTECTOR_VERSION' , file_get_contents( dirname(__FILE__).'/version.txt' ) ) ;

// set $_SERVER['REQUEST_URI'] for IIS
if ( empty( $_SERVER[ 'REQUEST_URI' ] ) ) {		 // Not defined by IIS
	// Under some configs, IIS makes SCRIPT_NAME point to php.exe :-(
	if ( !( $_SERVER[ 'REQUEST_URI' ] = @$_SERVER['PHP_SELF'] ) ) {
		$_SERVER[ 'REQUEST_URI' ] = $_SERVER['SCRIPT_NAME'];
	}
	if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
		$_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
	}
}

protector_prepare() ;
