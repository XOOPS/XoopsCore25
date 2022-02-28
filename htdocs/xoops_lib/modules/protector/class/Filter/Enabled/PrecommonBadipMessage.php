<?php namespace XoopsModules\Protector\Filter\Enabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class PrecommonBadipMessage
 */
class PrecommonBadipMessage extends FilterAbstract
{
    public function execute()
    {
        echo _MD_PROTECTOR_YOUAREBADIP;
        $protector = Guardian::getInstance();
        if ($protector->ip_matched_info) {
            printf(_MD_PROTECTOR_FMT_JAILINFO, date(_MD_PROTECTOR_FMT_JAILTIME, $protector->ip_matched_info));
        }
        exit;
    }
}
