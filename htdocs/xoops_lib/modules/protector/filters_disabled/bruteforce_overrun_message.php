<?php

/**
 * Class protector_bruteforce_overrun_message
 */
class Protector_bruteforce_overrun_message extends ProtectorFilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'You have tried too many wrong loggin in'; // write any message as you like
        exit;
    }
}
