<?php

/**
 * Class protector_precommon_badip_errorlog
 */
class Protector_precommon_badip_errorlog extends ProtectorFilterAbstract
{
    public function execute()
    {
        echo _MD_PROTECTOR_YOUAREBADIP;
        $protector = Protector::getInstance();
        if ($protector->ip_matched_info) {
            printf(_MD_PROTECTOR_FMT_JAILINFO, date(_MD_PROTECTOR_FMT_JAILTIME, $protector->ip_matched_info));
        }
        error_log('Protector: badip ' . @$_SERVER['REMOTE_ADDR'], 0);
        exit;
    }
}
