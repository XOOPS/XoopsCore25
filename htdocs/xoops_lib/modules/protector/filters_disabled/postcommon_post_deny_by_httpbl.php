<?php

// get your 12-character access key from http://www.projecthoneypot.org/
define('PROTECTOR_HTTPBL_KEY', '............');

/**
 * Class protector_postcommon_post_deny_by_httpbl
 */
class Protector_postcommon_post_deny_by_httpbl extends ProtectorFilterAbstract
{
    /**
     * @return bool
     */
    public function execute()
    {
        // http:bl servers (don't enable too many servers)
        $rbls = array(
            'http:BL' => PROTECTOR_HTTPBL_KEY . '.%s.dnsbl.httpbl.org');

        global $xoopsUser;

        $rev_ip = implode('.', array_reverse(explode('.', @$_SERVER['REMOTE_ADDR'])));
        // test
        // $rev_ip = '162.142.248.125' ;

        foreach ($rbls as $rbl_name => $rbl_fmt) {
            $host = sprintf($rbl_fmt, $rev_ip);
            if (gethostbyname($host) != $host) {
                $this->protector->message .= "DENY by $rbl_name\n";
                $uid = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
                $this->protector->output_log('RBL SPAM', $uid, false, 128);
                die(_MD_PROTECTOR_DENYBYRBL);
            }
        }

        return true;
    }
}
