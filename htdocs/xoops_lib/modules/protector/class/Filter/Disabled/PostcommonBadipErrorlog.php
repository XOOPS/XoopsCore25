<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class PostcommonBadipErrorlog
 */
class PostcommonBadipErrorlog extends FilterAbstract
{
    public function execute()
    {
        echo _MD_PROTECTOR_YOUAREBADIP;
        $protector = Guardian::getInstance();
        if ($protector->ip_matched_info) {
            printf(_MD_PROTECTOR_FMT_JAILINFO, date(_MD_PROTECTOR_FMT_JAILTIME, $protector->ip_matched_info));
        }
        error_log('Protector: badip ' . @$_SERVER['REMOTE_ADDR'], 0);
        exit;
    }
}
