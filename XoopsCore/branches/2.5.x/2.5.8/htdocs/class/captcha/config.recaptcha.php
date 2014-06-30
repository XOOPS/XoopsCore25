<?php
/**
 * CAPTCHA configurations for Recaptcha mode
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         class
 * @subpackage      CAPTCHA
 * @since           2.5.2
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

return $config = array(
    'private_key' => 'YourPrivateApiKey',
    'public_key'  => 'YourPublicApiKey',
    'theme'       => 'white', // 'red' | 'white' | 'blackglass' | 'clean' | 'custom'
    'lang'        => _LANGCODE
    );
