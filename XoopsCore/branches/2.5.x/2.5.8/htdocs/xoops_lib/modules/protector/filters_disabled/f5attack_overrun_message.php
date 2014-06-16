<?php

/**
 * Class protector_f5attack_overrun_message
 */
class protector_f5attack_overrun_message extends ProtectorFilterAbstract
{
    function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have reloaded a page too many times' ; // write any message as you like
        exit ;
    }

}
