<?php

/**
 * Class protector_spamcheck_overrun_message
 */
class Protector_spamcheck_overrun_message extends ProtectorFilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'Your post looks like SPAM'; // write any message as you like
        exit;
    }
}
