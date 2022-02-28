<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class CrawlerOverrunMessage
 */
class CrawlerOverrunMessage extends FilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have accessed too many times while short term'; // write any message as you like
        exit;
    }
}
