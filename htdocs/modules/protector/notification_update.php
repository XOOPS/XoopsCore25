<?php
require dirname(__DIR__, 2) . '/mainfile.php';
defined('XOOPS_TRUST_PATH') || exit('set XOOPS_TRUST_PATH in mainfile.php');

$mydirname = basename(__DIR__);
$mydirpath = __DIR__;
$mydirurl  = XOOPS_URL . '/modules/' . $mydirname;

require $mydirpath . '/mytrustdirname.php'; // set $mytrustdirname

\Xmf\Request::setVar('page', basename(__FILE__, '.php'), 'GET');

require XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/main.php';
