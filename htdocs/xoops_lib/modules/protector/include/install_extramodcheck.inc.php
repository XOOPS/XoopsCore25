<?php

/**
 * @param $xoops_root_path
 * @param $mytrustdirname
 *
 * @return array
 */
function get_writeoks_from_protector( $xoops_root_path , $mytrustdirname )
{
    return array( dirname(dirname( __FILE__ )) . '/configs' ) ;
}
