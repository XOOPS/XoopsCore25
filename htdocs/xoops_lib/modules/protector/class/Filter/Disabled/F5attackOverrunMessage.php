<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class F5attackOverrunMessage
 */
class F5attackOverrunMessage extends FilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have reloaded a page too many times'; // write any message as you like
        exit;
    }
}
