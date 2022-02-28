<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class SpamcheckOverrunMessage
 */
class SpamcheckOverrunMessage extends FilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'Your post looks like SPAM'; // write any message as you like
        exit;
    }
}
