<?php

/**
 * Class protector_precommon_badip_message
 */
class Protector_precommon_badip_message extends ProtectorFilterAbstract
{
    public function execute()
    {
        echo _MD_PROTECTOR_YOUAREBADIP;
        exit;
    }
}
