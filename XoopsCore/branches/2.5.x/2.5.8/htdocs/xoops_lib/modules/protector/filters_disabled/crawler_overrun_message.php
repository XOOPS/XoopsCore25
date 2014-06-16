<?php

/**
 * Class protector_crawler_overrun_message
 */
class protector_crawler_overrun_message extends ProtectorFilterAbstract
{
    function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have accessed too many times while short term' ; // write any message as you like
        exit ;
    }

}
