<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class BruteforceOverrunMessage
 */
class BruteforceOverrunMessage extends FilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have tried too many wrong loggin in'; // write any message as you like
        exit;
    }
}
