<?php

require_once __DIR__ . '/precheck_functions.php';

if (class_exists('Database')) {
    require __DIR__ . '/postcheck.inc.php';

    return null;
}

define('PROTECTOR_PRECHECK_INCLUDED', 1);
define('PROTECTOR_VERSION', (float) file_get_contents(__DIR__ . '/version.txt'));

// set $_SERVER['REQUEST_URI'] for IIS
if (empty($_SERVER['REQUEST_URI'])) {         // Not defined by IIS
    // Under some configs, IIS makes SCRIPT_NAME point to php.exe :-(
    if (!($_SERVER['REQUEST_URI'] = @$_SERVER['PHP_SELF'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    }
    if (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}

protector_prepare();
