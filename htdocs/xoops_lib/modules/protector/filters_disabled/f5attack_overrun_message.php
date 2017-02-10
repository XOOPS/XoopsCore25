<?php

/**
 * Class protector_f5attack_overrun_message
 */
class Protector_f5attack_overrun_message extends ProtectorFilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have reloaded a page too many times'; // write any message as you like
        exit;
    }
}
