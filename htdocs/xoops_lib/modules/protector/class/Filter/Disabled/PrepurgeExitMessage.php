<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class PrepurgeExitMessage
 */
class PrepurgeExitMessage extends FilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'Protector detects attacking actions'; // write any message as you like
        exit;
    }
}
