<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

// define it as you like :-)

define('PROTECTOR_BADIP_REDIRECTION_URI', 'http://yahoo.com/');

/**
 * Class PostcommonBadipRedirection
 */
class PrecommonBadipRedirection extends FilterAbstract
{
    public function execute()
    {
        header('Location: ' . PROTECTOR_BADIP_REDIRECTION_URI);
        exit;
    }
}
