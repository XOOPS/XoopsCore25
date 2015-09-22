<?php

// define it as you like :-)

define('PROTECTOR_BADIP_REDIRECTION_URI' , 'http://yahoo.com/' ) ;

/**
 * Class protector_precommon_badip_redirection
 */
class protector_precommon_badip_redirection extends ProtectorFilterAbstract
{
    function execute()
    {
        header( 'Location: '.PROTECTOR_BADIP_REDIRECTION_URI ) ;
        exit ;
    }

}
