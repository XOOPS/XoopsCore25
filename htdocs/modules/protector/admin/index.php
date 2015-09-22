<?php

require dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
defined("XOOPS_TRUST_PATH") || die('set XOOPS_TRUST_PATH into mainfile.php');

$mydirname = basename( dirname(__DIR__) ) ;
$mydirpath = dirname(__DIR__) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin/index.php' ;
