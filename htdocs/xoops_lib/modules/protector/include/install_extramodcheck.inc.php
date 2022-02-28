<?php

/**
 * @param $xoops_root_path
 * @param $mytrustdirname
 *
 * @return array
 */

use XoopsModules\Protector;

require_once dirname(__DIR__) . '/preloads/autoloader.php';

function get_writeoks_from_protector( $xoops_root_path , $mytrustdirname )
{
    return array( __DIR__ . '/configs' ) ;
}
