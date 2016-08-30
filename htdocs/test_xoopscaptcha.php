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
 * CAPTCHA tests
 *
 * @author      Grégory Mage
 * @copyright   2016 XOOPS Project (http://xoops.org)
 * @license     GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link        http://xoops.org
 */
 
 /* IMPORTANT!!! 
 * please modify htdocs\class\captcha\config.php like this for test:
     return $config = array(
        'disabled'    => false,  // Disable CAPTCHA
        'mode'        => 'recaptcha_2',  // default mode, you can choose 'text', 'image', 'recaptcha'(requires api key)
        'name'        => 'xoopscaptcha',  // captcha name
        'skipmember'  => false,  // Skip CAPTCHA check for members
        'maxattempts' => 10,  // Maximum attempts for each session
    );
 */

include __DIR__ . '/mainfile.php';
include_once XOOPS_ROOT_PATH.'/header.php';

XoopsLoad::load('XoopsRequest');
// Get Action type
$op = XoopsRequest::getCmd('op', 'form');
switch ($op) {    
    // form
    case 'form':
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        echo '<h2>Test xoops captcha</h2>';
        $form = new XoopsThemeForm('Test xoops captcha', 'form', $_SERVER['REQUEST_URI'], 'post', true);
        $form->addElement(new XoopsFormText('Test text', 'text', 50, 255, ''), true);
        $form->addElement(new XoopsFormCaptcha('Test captcha', 'captcha', false), true);
        $form->addElement(new XoopsFormHidden('op', 'save'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        echo $form->render();
    break;

    // save
    case 'save':
        echo '<h2>Test xoops captcha answer</h2>';
        xoops_load('xoopscaptcha');
        $xoopsCaptcha = XoopsCaptcha::getInstance();
        if (! $xoopsCaptcha->verify() ) {
            echo 'Errors: ' . $xoopsCaptcha->getMessage();
        } else {
            echo 'Good!';
        }
    break;
}
include XOOPS_ROOT_PATH.'/footer.php';