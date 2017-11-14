<?php
/**
 * Check register attempt for "spaminess" on stopforumspam.com
 * Please see http://www.stopforumspam.com/usage before enabling for restrictions and conditions
 *
 * Assumes registration by POST with variables email and uname. This is true of the register scripts in
 * core and the profile module.
 *
 * If the registrant is determined to be a spammer, the account is not created. The determination is
 * made by inspecting the confidence level returned by the stopforumspam API. If that confidence, for
 * any of user name, email or IP address, exceeds the configured $minimumConfidence, the registration
 * is denied.
 */
class Protector_postcommon_register_stopforumspam extends ProtectorFilterAbstract
{
    /** @var float $minimumConfidence
     * This is a percentage confidence as reported by stopforumspam api.
     * When the reported confidence for any entry is above this, the registration will be denied.
     */
    protected $minimumConfidence = 65.0; // set at your desired threshold

    /**
     * @return bool
     */
    public function execute()
    {
        // we only check the registration main post which should not match these conditions
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || !isset($_POST['email'])
            || !isset($_POST['uname'])
        ) {
            return true;
        }

        $report = array();
        $report['email'] = isset($_POST['email']) ? $_POST['email'] : null;
        $report['ip'] = $_SERVER['REMOTE_ADDR'];
        $report['uname'] = isset($_POST['uname']) ? $_POST['uname'] : null;
        $result = $this->protector->stopForumSpamLookup($report['email'], $report['ip'], $report['uname']);
        if (false === $result || isset($result['http_code'])) {
            // the look up failed at the http level, log it for now?
            $report['result'] = $result;
            $this->protector->message = json_encode($report);
            $this->protector->output_log('SFS-UNKNOWN');
            return true;
        }
        foreach ($result as $entry) {
            if (isset($entry['confidence']) && ((float) $entry['confidence'] > $this->minimumConfidence)) {
                $report['result'] = $result;
                $this->protector->message = json_encode($report);
                $this->protector->output_log('SFS SPAM Registration');
                // write any message as you like
                echo 'This registration attempt has been denied. '
                    . 'If you feel this is in error, please contact the site administrator.';
                exit;
            }
        }
    }
}
