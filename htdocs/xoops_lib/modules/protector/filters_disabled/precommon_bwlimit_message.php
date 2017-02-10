<?php

/**
 * Class protector_precommon_bwlimit_message
 */
class Protector_precommon_bwlimit_message extends ProtectorFilterAbstract
{
    public function execute()
    {
        header('HTTP/1.0 503 Service unavailable');
        header('Retry-After: 600');

        echo _MD_PROTECTOR_BANDWIDTHLIMITED;
        exit;
    }
}
