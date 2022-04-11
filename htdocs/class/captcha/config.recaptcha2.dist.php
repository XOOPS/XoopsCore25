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
 * @copyright   2016-2021 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

return $config = array(
    'website_key' => 'YourWebsiteKey', //https://www.google.com/recaptcha/intro/index.html YourWebsiteKey
    'secret_key'  => 'YourSecretKey',  //https://www.google.com/recaptcha/intro/index.html YourSecretKey
);
