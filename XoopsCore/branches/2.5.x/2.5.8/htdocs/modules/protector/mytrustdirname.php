<?php
// edit for xoops 2.4 core by phppp and trabis
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

$mytrustdirname = 'protector';

include_once XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/class/registry.php';
$registry =& ProtectorRegistry::getInstance();
$registry->setEntry('mydirname',  basename(dirname( __FILE__ )));
$registry->setEntry('mydirpath',  dirname( __FILE__ ));
$registry->setEntry('mytrustdirname', $mytrustdirname);
$registry->setEntry('language', empty($xoopsConfig['language']) ? "english" : $xoopsConfig['language']);
