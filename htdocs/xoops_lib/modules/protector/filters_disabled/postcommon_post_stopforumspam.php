<?php
/**
 * Check post attempts for "spaminess" on stopforumspam.com
 * Please see http://www.stopforumspam.com/usage before enabling for restrictions and conditions
 *
 * We can check at registration, but this will repeat the check for subsequent posts. The more time that passes
 * between registration and posting, the more likely it is that the spammer is caught before doing damage.
 *
 * If the poster is determined to be a spammer, the account is deactivated. The determination is made by
 * inspecting the confidence level returned by the stopforumspam API. If that confidence, for either
 * email or IP address, exceeds the configured $minimumConfidence, the post is denied and the account
 * is deactivated.
 */
class Protector_postcommon_post_stopforumspam extends ProtectorFilterAbstract
{
    /** @var int after this number of posts by the user, skip this filter */
    protected $minPosts = 5;

    /** @var float $minimumConfidence
     * This is a percentage confidence as reported by stopforumspam api.
     * When the reported confidence for any entry is above this, the post will be denied.
     */
    protected $minimumConfidence = 65.0; // set at your desired threshold

    /**
     * @return bool
     */
    public function execute()
    {
        /* @var $xoopsUser XoopsUser */
        global $xoopsUser;

        // we only check POST transactions
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_object($xoopsUser)) {
            return true;
        }

        // don't process for admin and experienced users
        if (is_object($xoopsUser) && ($xoopsUser->isAdmin() || $this->minPosts < $xoopsUser->posts())) {
            return true;
        }

        $report = array();
        $report['email'] = $xoopsUser->email();
        $report['ip'] = $_SERVER['REMOTE_ADDR'];
        $result = $this->protector->stopForumSpamLookup($report['email'], $report['ip'], null);
        if (false === $result || isset($result['http_code'])) {
            return true;
        }
        if (!is_array($result)) {
            // not sure what this would be, but log it just in case.
            $this->protector->message = json_encode($result);
            $this->protector->output_log('SFS-UNKNOWN');
            return true;
        }
        foreach ($result as $entry) {
            if (isset($entry['confidence']) && ((float) $entry['confidence'] > $this->minimumConfidence)) {
                $report['result'] = $result;
                $this->protector->message = json_encode($report);
                $this->protector->output_log('SFS SPAMMER Check', $xoopsUser->uid());
                $this->protector->deactivateCurrentUser();
                // write any message as you like
                echo 'Your post has been denied. '
                    . 'If you feel this is in error, please contact the site administrator.';
                exit;
            }
        }
    }
}
