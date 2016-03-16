<?php
// edit for xoops 2.4 core by phppp and trabis
defined('XOOPS_TRUST_PATH') || exit('set XOOPS_TRUST_PATH in mainfile.php');

$mytrustdirname = 'protector';

include_once XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/class/registry.php';
$registry = ProtectorRegistry::getInstance();
$registry->setEntry('mydirname', basename(__DIR__));
$registry->setEntry('mydirpath', __DIR__);
$registry->setEntry('mytrustdirname', $mytrustdirname);
$registry->setEntry('language', empty($xoopsConfig['language']) ? 'english' : $xoopsConfig['language']);
