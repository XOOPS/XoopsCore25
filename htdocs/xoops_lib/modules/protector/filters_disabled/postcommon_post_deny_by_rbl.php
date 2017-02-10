<?php

/**
 * Class protector_postcommon_post_deny_by_rbl
 */
class Protector_postcommon_post_deny_by_rbl extends ProtectorFilterAbstract
{
    /**
     * @return bool
     */
    public function execute()
    {
        // RBL servers (don't enable too many servers)
        $rbls = array(
            'sbl-xbl.spamhaus.org',
            #            'niku.2ch.net' ,
            #            'list.dsbl.org' ,
            #            'bl.spamcop.net' ,
            #            'all.rbl.jp' ,
            #            'opm.blitzed.org' ,
            #            'bsb.empty.us' ,
            #            'bsb.spamlookup.net' ,
        );

        global $xoopsUser;

        $rev_ip = implode('.', array_reverse(explode('.', @$_SERVER['REMOTE_ADDR'])));

        foreach ($rbls as $rbl) {
            $host = $rev_ip . '.' . $rbl;
            if (gethostbyname($host) != $host) {
                $this->protector->message .= "DENY by $rbl\n";
                $uid = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
                $this->protector->output_log('RBL SPAM', $uid, false, 128);
                die(_MD_PROTECTOR_DENYBYRBL);
            }
        }

        return true;
    }
}
