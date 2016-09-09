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
 * CAPTCHA configurations for Recaptcha 2 mode
 *
 * @package     class
 * @subpackage  CAPTCHA
 * @author      Grégory Mage
 * @copyright   2016 XOOPS Project (http://xoops.org)
 * @license     GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link        http://xoops.org
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

return $config = array(
    'website_key' => 'YourWebsiteKey', //https://www.google.com/recaptcha/intro/index.html YourWebsiteKey
    'secret_key'  => 'YourSecretKey',  //https://www.google.com/recaptcha/intro/index.html YourSecretKey
);
