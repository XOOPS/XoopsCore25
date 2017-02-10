<?php

/**
 * Class protector_prepurge_exit_message
 */
class Protector_prepurge_exit_message extends ProtectorFilterAbstract
{
    public function execute()
    {
        // header( 'Location: http://google.com/' ) ; // redirect somewhere
        echo 'Protector detects attacking actions'; // write any message as you like
        exit;
    }
}
