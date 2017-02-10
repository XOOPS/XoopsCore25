<?php

// Don't enable this for site using single-byte
// Perhaps, japanese, schinese, tchinese, and korean can use it

/**
 * Class protector_postcommon_post_need_multibyte
 */
class Protector_postcommon_post_need_multibyte extends ProtectorFilterAbstract
{
    /**
     * @return bool
     */
    public function execute()
    {
        global $xoopsUser;

        if (!function_exists('mb_strlen')) {
            return true;
        }

        // registered users always pass this plugin
        if (is_object($xoopsUser)) {
            return true;
        }

        $lengths = array(
            0          => 100, // default value
            'message'  => 2,
            'com_text' => 2,
            'excerpt'  => 2);

        foreach ($_POST as $key => $data) {
            // dare to ignore arrays/objects
            if (!is_string($data)) {
                continue;
            }

            $check_length = isset($lengths[$key]) ? $lengths[$key] : $lengths[0];
            if (strlen($data) > $check_length) {
                if (strlen($data) == mb_strlen($data)) {
                    $this->protector->message .= "No multibyte character was found ($data)\n";
                    $this->protector->output_log('Singlebyte SPAM', 0, false, 128);
                    die('Protector rejects your post, because your post looks like SPAM');
                }
            }
        }

        return true;
    }
}
