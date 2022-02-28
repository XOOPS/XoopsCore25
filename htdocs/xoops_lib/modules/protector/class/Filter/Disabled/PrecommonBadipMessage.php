<?php namespace XoopsModules\Protector\Filter\Disabled;

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
        exit;
    }
}
