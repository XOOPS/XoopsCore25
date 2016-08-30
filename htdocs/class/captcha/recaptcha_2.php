<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
 
/**
 * CAPTCHA for Recaptcha mode
 *
 * @package     class
 * @subpackage  CAPTCHA
 * @author      Grégory Mage
 * @copyright   2016 XOOPS Project (http://xoops.org)
 * @license     GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link        http://xoops.org
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XoopsCaptchaRecaptcha_2
 */
class XoopsCaptchaRecaptcha_2 extends XoopsCaptchaMethod
{
    /**
     * XoopsCaptchaRecaptcha_2::isActive()
     *
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * XoopsCaptchaRecaptcha_2::render()
     *
     * @return string
     */
    public function render()
    {
        $form = "<script src='https://www.google.com/recaptcha/api.js'></script>";
        $form .= "<div class=\"form-group\">
                  <div class=\"g-recaptcha\" data-sitekey=\"" . $this->config['website_key'] . "\"></div>
                  </div>";
        return $form;
    }

    /**
     * XoopsCaptchaRecaptcha_2::verify()
     *
     * @param mixed|null $sessionName
     *
     * @return bool
     */
    public function verify($sessionName = NULL)
    {
        $is_valid = false;
        XoopsLoad::load('XoopsRequest');
        $recaptcha_response = XoopsRequest::getString('g-recaptcha-response', '');
        if ($recaptcha_response == '') {
            $is_valid = false;
        } else {
            $recaptcha_check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $this->config['secret_key'] . '&response=' .  $recaptcha_response . '&remoteip=' . getenv('REMOTE_ADDR'));
            $recaptcha_check = json_decode($recaptcha_check , true);
            if ($recaptcha_check['success'] == true) {
                $is_valid = true;
            } else {
                foreach (array_keys($recaptcha_check['error-codes']) as $i) {
                    echo $recaptcha_check['error-codes'][$i] . '<br>';
                }
            }
        }
        return $is_valid;
    }
}