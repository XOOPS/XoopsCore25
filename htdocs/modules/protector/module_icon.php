<?php

$xoopsOption['nocommon'] = true;
require dirname(dirname(__DIR__)) . '/mainfile.php';

defined('XOOPS_TRUST_PATH') || exit('set XOOPS_TRUST_PATH in mainfile.php');

$mydirname = basename(__DIR__);
$mydirpath = __DIR__;
require $mydirpath . '/mytrustdirname.php'; // set $mytrustdirname

require XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/module_icon.php';
