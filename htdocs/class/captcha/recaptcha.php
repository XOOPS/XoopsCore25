<?php

use Xmf\Request;

/**
 * CAPTCHA for Recaptcha mode
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          CAPTCHA
 * @since               2.5.2
 * @author              trabis <lusopoemas@gmail.com>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XoopsCaptchaRecaptcha
 */
class XoopsCaptchaRecaptcha extends XoopsCaptchaMethod
{
    /**
     * XoopsCaptchaRecaptcha::isActive()
     *
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * XoopsCaptchaRecaptcha::render()
     *
     * @return string
     */
    public function render()
    {
        trigger_error("recaptcha is outdated , use recaptcha_2 in \class\captcha\config.php", E_USER_WARNING);
        require_once __DIR__ . '/recaptcha/recaptchalib.php';
        $form = "<script type=\"text/javascript\">
            var RecaptchaOptions = {
            theme : '" . $this->config['theme'] . "',
            lang : '" . $this->config['lang'] . "'
            };
            </script>";
        $form .= recaptcha_get_html($this->config['public_key']);

        return $form;
    }

    /**
     * XoopsCaptchaRecaptcha::verify()
     *
     * @param mixed|null $sessionName
     *
     * @return bool
     */
    public function verify($sessionName = null)
    {
        $is_valid = false;
        require_once __DIR__ . '/recaptcha/recaptchalib.php';
        if (!empty(Request::getString('recaptcha_response_field', '', 'POST'))) {
            $resp = recaptcha_check_answer($this->config['private_key'], $_SERVER['REMOTE_ADDR'], Request::getString('recaptcha_challenge_field', '', 'POST'), Request::getString('recaptcha_response_field', '', 'POST'));
            if (!$resp->is_valid) {
                $this->message[] = $resp->error;
            } else {
                $is_valid = true;
            }
        }

        return $is_valid;
    }
}
