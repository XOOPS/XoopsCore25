<?php

/**
 * Class protector_precommon_badip_message
 */
class protector_precommon_badip_message extends ProtectorFilterAbstract
{
    function execute()
    {
        echo _MD_PROTECTOR_YOUAREBADIP ;
        $protector =& Protector::getInstance() ;
        if ($protector->ip_matched_info) {
            printf( _MD_PROTECTOR_FMT_JAILINFO , date( _MD_PROTECTOR_FMT_JAILTIME , $protector->ip_matched_info ) ) ;
        }
        exit ;
    }

}
