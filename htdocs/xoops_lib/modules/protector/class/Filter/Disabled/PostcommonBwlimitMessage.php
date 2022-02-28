<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class PostcommonBwlimitMessage
 */
class PostcommonBwlimitMessage extends FilterAbstract
{
    public function execute()
    {
        header('HTTP/1.0 503 Service unavailable');
        header('Retry-After: 600');

        echo _MD_PROTECTOR_BANDWIDTHLIMITED;
        exit;
    }
}
