<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use Xmf\Request;
use Xmf\IPAddress;

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
 * Class XoopsCaptchaRecaptcha2
 */
class XoopsCaptchaRecaptcha2 extends XoopsCaptchaMethod
{
    /**
     * XoopsCaptchaRecaptcha2::isActive()
     *
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * XoopsCaptchaRecaptcha2::render()
     *
     * @return string
     */
    public function render()
    {
        $form = '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $form .= '<div class="form-group"><div class="g-recaptcha" data-sitekey="'
            . $this->config['website_key'] . '"></div></div>';
        return $form;
    }

    /**
     * XoopsCaptchaRecaptcha2::verify()
     *
     * @param string|null $sessionName unused for recaptcha
     *
     * @return bool
     */
    public function verify($sessionName = null)
    {
        $isValid = false;
        $recaptchaResponse = Request::getString('g-recaptcha-response', '');
        $recaptchaVerifyURL = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $this->config['secret_key']
            . '&response=' .  $recaptchaResponse . '&remoteip=' . IPAddress::fromRequest()->asReadable();
        $usedCurl = false;
        if (function_exists('curl_init') && false !== ($curlHandle  = curl_init())) {
            curl_setopt($curlHandle, CURLOPT_URL, $recaptchaVerifyURL);
            curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);
            $curlReturn = curl_exec($curlHandle);
            if (false === $curlReturn) {
                trigger_error(curl_error($curlHandle));
            } else {
                $usedCurl = true;
                $recaptchaCheck = json_decode($curlReturn, true);
            }
            curl_close($curlHandle);
        }
        if (false === $usedCurl) {
            $recaptchaCheck = file_get_contents($recaptchaVerifyURL);
            $recaptchaCheck = json_decode($recaptchaCheck, true);
        }
        if (isset($recaptchaCheck['success']) && $recaptchaCheck['success'] === true) {
            $isValid = true;
        } else {
            /** @var \XoopsCaptcha $captchaInstance */
            $captchaInstance = \XoopsCaptcha::getInstance();
            /** @var array $recaptchaCheck */
            foreach ($recaptchaCheck['error-codes'] as $msg) {
                $captchaInstance->message[] = $msg;
            }
        }

        return $isValid;
    }
}
