<?php

require '../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname(__DIR__) ) ;
$mydirpath = dirname(__DIR__) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

//require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin.php' ;
require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin/center.php' ;
