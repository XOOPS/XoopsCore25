<?php
/**
 * Xoops MultiMailer Base Class
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
 * @package             Kernel
 * @subpackage          mail
 * @since               2.0.0
 * @author              Author: Jochen Büînagel (job@buennagel.com)
 */

/**
 *
 * @package    class
 * @subpackage mail
 * @filesource
 * @author     Jochen Büînagel <jb@buennagel.com>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
/**
 * load the base class
 */
if (!file_exists($file = XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.phpmailer.php')) {
    trigger_error('Required File  ' . str_replace(XOOPS_ROOT_PATH, '', $file) . ' was not found in file ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);

    return false;
}
require_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/PHPMailerAutoload.php';
//include_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.phpmailer.php';

/**
 * Mailer Class.
 *
 * At the moment, this does nothing but send email through PHP "mail()" function,
 * but it has the ability to do much more.
 *
 * If you have problems sending mail with "mail()", you can edit the member variables
 * to suit your setting. Later this will be possible through the admin panel.
 *
 * @todo       Make a page in the admin panel for setting mailer preferences.
 * @package    class
 * @subpackage mail
 * @author     Jochen Buennagel <job@buennagel.com>
 */
class XoopsMultiMailer extends PHPMailer
{
    /**
     * 'from' address
     *
     * @var string
     * @access private
     */
    public $From = '';

    /**
     * 'from' name
     *
     * @var string
     * @access private
     */
    public $FromName = '';

    // can be 'smtp', 'sendmail', or 'mail'
    /**
     * Method to be used when sending the mail.
     *
     * This can be:
     * <li>mail (standard PHP function 'mail()') (default)
     * <li>smtp    (send through any SMTP server, SMTPAuth is supported.
     * You must set {@link $Host}, for SMTPAuth also {@link $SMTPAuth},
     * {@link $Username}, and {@link $Password}.)
     * <li>sendmail (manually set the path to your sendmail program
     * to something different than 'mail()' uses in {@link $Sendmail})
     *
     * @var string
     * @access private
     */
    public $Mailer = 'mail';

    /**
     * set if $Mailer is 'sendmail'
     *
     * Only used if {@link $Mailer} is set to 'sendmail'.
     * Contains the full path to your sendmail program or replacement.
     *
     * @var string
     * @access private
     */
    public $Sendmail = '/usr/sbin/sendmail';

    /**
     * SMTP Host.
     *
     * Only used if {@link $Mailer} is set to 'smtp'
     *
     * @var string
     * @access private
     */
    public $Host = '';

    /**
     * Does your SMTP host require SMTPAuth authentication?
     *
     * @var boolean
     * @access private
     */
    public $SMTPAuth = false;

    /**
     * Username for authentication with your SMTP host.
     *
     * Only used if {@link $Mailer} is 'smtp' and {@link $SMTPAuth} is TRUE
     *
     * @var string
     * @access private
     */
    public $Username = '';

    /**
     * Password for SMTPAuth.
     *
     * Only used if {@link $Mailer} is 'smtp' and {@link $SMTPAuth} is TRUE
     *
     * @var string
     * @access private
     */
    public $Password = '';

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler    = xoops_getHandler('config');
        $xoopsMailerConfig = $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
        $this->From        = $xoopsMailerConfig['from'];
        if ($this->From == '') {
            $this->From = $GLOBALS['xoopsConfig']['adminmail'];
        }
        $this->Sender = $this->From;
        if ($xoopsMailerConfig['mailmethod'] === 'smtpauth') {
            $this->Mailer   = 'smtp';
            $this->SMTPAuth = true;
            // TODO: change value type of xoopsConfig 'smtphost' from array to text
            $this->Host     = implode(';', $xoopsMailerConfig['smtphost']);
            $this->Username = $xoopsMailerConfig['smtpuser'];
            $this->Password = $xoopsMailerConfig['smtppass'];
        } else {
            $this->Mailer   = $xoopsMailerConfig['mailmethod'];
            $this->SMTPAuth = false;
            $this->Sendmail = $xoopsMailerConfig['sendmailpath'];
            $this->Host     = implode(';', $xoopsMailerConfig['smtphost']);
        }
        $this->CharSet = strtolower(_CHARSET);
        $xoopsLanguage = preg_replace('/[^a-zA-Z0-9_-]/', '', $GLOBALS['xoopsConfig']['language']);
        if (file_exists(XOOPS_ROOT_PATH . '/language/' . $xoopsLanguage . '/phpmailer.php')) {
            include XOOPS_ROOT_PATH . '/language/' . $xoopsLanguage . '/phpmailer.php';
            $this->language = $PHPMAILER_LANG;
        } else {
            $this->setLanguage('en', XOOPS_ROOT_PATH . '/class/mail/phpmailer/language/');
        }
        //$this->pluginDir = XOOPS_ROOT_PATH . '/class/mail/phpmailer/';
    }


}
