<?php
require dirname(dirname(__DIR__)) . '/mainfile.php';
defined('XOOPS_TRUST_PATH') || exit('set XOOPS_TRUST_PATH in mainfile.php');

$mydirname = basename(__DIR__);
$mydirpath = __DIR__;
$mydirurl  = XOOPS_URL . '/modules/' . $mydirname;

require $mydirpath . '/mytrustdirname.php'; // set $mytrustdirname

$_GET['page'] = basename(__FILE__, '.php');

require XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/main.php';
