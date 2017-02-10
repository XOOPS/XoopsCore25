<?php

/**
 * Class protector_precommon_bwlimit_errorlog
 */
class Protector_precommon_bwlimit_errorlog extends ProtectorFilterAbstract
{
    public function execute()
    {
        header('HTTP/1.0 503 Service unavailable');
        header('Retry-After: 600');

        echo _MD_PROTECTOR_BANDWIDTHLIMITED;
        error_log('Protector: bwlimit ' . @$_SERVER['REMOTE_ADDR'], 0);
        exit;
    }
}
