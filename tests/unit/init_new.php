<?php

// declare(strict_types=1);

//namespace Xoops\Tests\Database;

if (defined('XOOPS_TU_ROOT_PATH')) return;

use PHPUnit\Framework\TestCase;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('XOOP check: PHP version require 7.4.0 or more');
}

//if (version_compare(PHP_VERSION, '8.2.0', '<')) {
//    die("XOOP check: PHP version require 8.2.0 or more");
//}




// needed for phpunit => initializing $_SERVER values
if (empty($_SERVER['HTTP_HOST'])) {
    define('IS_PHPUNIT', true);
}

if (defined('IS_PHPUNIT')) {
    require_once __DIR__ . '/common_phpunit.php';
} else {
    // Avoid check proxy to define constant XOOPS_DB_PROXY
    // because it implies a readonly database connection
    $_SERVER['REQUEST_METHOD'] = 'POST';
    define('XOOPS_XMLRPC', 0);
}

define('XOOPS_ROOT_PATH', realpath(dirname(__DIR__, 2) . '/htdocs'));

// For forward compatibility
// Physical path to the XOOPS library directory WITHOUT trailing slash
define('XOOPS_PATH', realpath(dirname(__DIR__, 2) . '/htdocs/xoops_lib'));
// Physical path to the XOOPS datafiles (writable) directory WITHOUT trailing slash
define('XOOPS_VAR_PATH', realpath(dirname(__DIR__, 2) . '/htdocs/xoops_data'));
// Alias of XOOPS_PATH, for compatibility, temporary solution
define('XOOPS_TRUST_PATH', XOOPS_PATH);

//define('XOOPS_TU_ROOT_PATH', realpath(__DIR__.'/../../htdocs'));

define('XOOPS_TU_ROOT_PATH', realpath(dirname(__DIR__, 2) . '/htdocs'));

// echo 'XOOPS_TU_ROOT_PATH = ' . XOOPS_TU_ROOT_PATH. "\n";

//temporary patch, we still need mainfile until we have a config file
$xoopsOption['nocommon'] = true; // don't include common.php file

//require_once(XOOPS_TU_ROOT_PATH . '/mainfile.php');

//require_once(XOOPS_TU_ROOT_PATH . '/class/XoopsBaseConfig.php');

//\XoopsBaseConfig::bootstrapTransition();
//
//\Xoops\Locale::loadLocale();
