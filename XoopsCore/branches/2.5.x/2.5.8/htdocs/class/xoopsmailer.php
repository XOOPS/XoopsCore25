<?php
/**
 * XOOPS mailer
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
 * @package         kernel
 * @since           2.0.0
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version         $Id$
 * @deprecated      use {@link XoopsMultiMailer} instead.
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_loadLanguage('mail');

/**
 * The new Multimailer class that will carry out the actual sending and will later replace this class.
 * If you're writing new code, please use that class instead.
 */
include_once $GLOBALS['xoops']->path('class/mail/xoopsmultimailer.php');

/**
 * Class for sending mail.
 *
 * Changed to use the facilities of  {@link XoopsMultiMailer}
 *
 * @package class
 * @subpackage mail
 * @author Kazumi Ono <onokazu@xoops.org>
 */
class XoopsMailer
{
    /**
     * reference to a {@link XoopsMultiMailer}
     *
     * @var XoopsMultiMailer
     * @access private
     * @since 21.02.2003 14:14:13
     */
    var $multimailer;
    // sender email address
    // private
    var $fromEmail;
    // sender name
    // private
    var $fromName;
    // RMV-NOTIFY
    // sender UID
    // private
    var $fromUser;
    // array of user class objects
    // private
    var $toUsers;
    // array of email addresses
    // private
    var $toEmails;
    // custom headers
    // private
    var $headers;
    // subjet of mail
    // private
    var $subject;
    // body of mail
    // private
    var $body;
    // error messages
    // private
    var $errors;
    // messages upon success
    // private
    var $success;
    // private
    var $isMail;
    // private
    var $isPM;
    // private
    var $assignedTags;
    // private
    var $template;
    // private
    var $templatedir;
    // protected
    var $charSet = 'iso-8859-1';
    // protected
    var $encoding = '8bit';

    /**
     * Constructor
     *
     * @return XoopsMailer
     */
    function XoopsMailer()
    {
        $this->multimailer = new XoopsMultiMailer();
        $this->reset();
    }

    // public     // reset all properties to default
    /**
     * @param bool $value
     */
    function setHTML($value = true)
    {
        $this->multimailer->isHTML($value);
    }

    // public
    // reset all properties to default
    function reset()
    {
        $this->fromEmail = "";
        $this->fromName = "";
        $this->fromUser = null; // RMV-NOTIFY
        $this->priority = '';
        $this->toUsers = array();
        $this->toEmails = array();
        $this->headers = array();
        $this->subject = "";
        $this->body = "";
        $this->errors = array();
        $this->success = array();
        $this->isMail = false;
        $this->isPM = false;
        $this->assignedTags = array();
        $this->template = "";
        $this->templatedir = "";
        // Change below to \r\n if you have problem sending mail
        $this->LE = "\n";
    }

    // public
    /**
     * @param null $value
     */
    function setTemplateDir($value = null)
    {
        if ($value === null && is_object($GLOBALS['xoopsModule'])) {
            $value = $GLOBALS['xoopsModule']->getVar('dirname', 'n');
        } else {
            $value = str_replace(DIRECTORY_SEPARATOR, "/", $value);
        }
        $this->templatedir = $value;
    }

    // private
    /**
     * @return bool|string
     */
    function getTemplatePath()
    {
        if (! $path = $this->templatedir) {
            $path = XOOPS_ROOT_PATH . "/language/";
        } elseif (false === strpos($path, '/')) {
            $path = XOOPS_ROOT_PATH . "/modules/" . $path . "/language/";
        } elseif (substr($path, - 1, 1) != "/") {
            $path .= "/";
        }
        if (file_exists($path . $GLOBALS['xoopsConfig']['language'] . "/mail_template/" . $this->template)) {
            return $path . $GLOBALS['xoopsConfig']['language'] . "/mail_template/" . $this->template;
        } elseif (file_exists($path . "english/mail_template/" . $this->template)) {
            return $path . "english/mail_template/" . $this->template;
        } elseif (file_exists($path . $this->template)) {
            return $path . $this->template;
        } else {
            return false;
        }
    }

    // public
    /**
     * @param $value
     */
    function setTemplate($value)
    {
        $this->template = $value;
    }

    // pupblic
    /**
     * @param $value
     */
    function setFromEmail($value)
    {
        $this->fromEmail = trim($value);
    }

    // public
    /**
     * @param $value
     */
    function setFromName($value)
    {
        $this->fromName = trim($value);
    }

    // RMV-NOTIFY
    // public
    /**
     * @param $user
     */
    function setFromUser(&$user)
    {
        if (strtolower(get_class($user)) == "xoopsuser") {
            $this->fromUser = &$user;
        }
    }

    // public
    /**
     * @param $value
     */
    function setPriority($value)
    {
        $this->priority = trim($value);
    }

    // public
    /**
     * @param $value
     */
    function setSubject($value)
    {
        $this->subject = trim($value);
    }

    // public
    /**
     * @param $value
     */
    function setBody($value)
    {
        $this->body = trim($value);
    }

    // public
    function useMail()
    {
        $this->isMail = true;
    }

    // public
    function usePM()
    {
        $this->isPM = true;
    }

    // public
    /**
     * @param bool $debug
     *
     * @return bool
     */
    function send($debug = false)
    {
        global $xoopsConfig;
        if ($this->body == "" && $this->template == "") {
            if ($debug) {
                $this->errors[] = _MAIL_MSGBODY;
            }

            return false;
        } elseif ($this->template != "") {
            $path = $this->getTemplatePath();
            if (! ($fd = @fopen($path, 'r'))) {
                if ($debug) {
                    $this->errors[] = _MAIL_FAILOPTPL;
                }

                return false;
            }
            $this->setBody(fread($fd, filesize($path)));
        }
        // for sending mail only
        if ($this->isMail || ! empty($this->toEmails)) {
            if (! empty($this->priority)) {
                $this->headers[] = "X-Priority: " . $this->priority;
            }
            // $this->headers[] = "X-Mailer: PHP/".phpversion();
            // $this->headers[] = "Return-Path: ".$this->fromEmail;
            $headers = join($this->LE, $this->headers);
        }
        // TODO: we should have an option of no-reply for private messages and emails
        // to which we do not accept replies.  e.g. the site admin doesn't want a
        // a lot of message from people trying to unsubscribe.  Just make sure to
        // give good instructions in the message.
        // add some standard tags (user-dependent tags are included later)
        global $xoopsConfig;

        $this->assign('X_ADMINMAIL', $xoopsConfig['adminmail']);
        $this->assign('X_SITENAME', $xoopsConfig['sitename']);
        $this->assign('X_SITEURL', XOOPS_URL . "/");
        // TODO: also X_ADMINNAME??
        // TODO: X_SIGNATURE, X_DISCLAIMER ?? - these are probably best
        // done as includes if mail templates ever get this sophisticated
        // replace tags with actual values
        foreach ($this->assignedTags as $k => $v) {
            $this->body = str_replace("{" . $k . "}", $v, $this->body);
            $this->subject = str_replace("{" . $k . "}", $v, $this->subject);
        }
        $this->body = str_replace("\r\n", "\n", $this->body);
        $this->body = str_replace("\r", "\n", $this->body);
        $this->body = str_replace("\n", $this->LE, $this->body);
        // send mail to specified mail addresses, if any
        foreach ($this->toEmails as $mailaddr) {
            if (! $this->sendMail($mailaddr, $this->subject, $this->body, $headers)) {
                if ($debug) {
                    $this->errors[] = sprintf(_MAIL_SENDMAILNG, $mailaddr);
                }
            } else {
                if ($debug) {
                    $this->success[] = sprintf(_MAIL_MAILGOOD, $mailaddr);
                }
            }
        }
        // send message to specified users, if any
        // NOTE: we don't send to LIST of recipients, because the tags
        // below are dependent on the user identity; i.e. each user
        // receives (potentially) a different message
        foreach ($this->toUsers as $user) {
            // set some user specific variables
            $subject = str_replace("{X_UNAME}", $user->getVar("uname"), $this->subject);
            $text = str_replace("{X_UID}", $user->getVar("uid"), $this->body);
            $text = str_replace("{X_UEMAIL}", $user->getVar("email"), $text);
            $text = str_replace("{X_UNAME}", $user->getVar("uname"), $text);
            $text = str_replace("{X_UACTLINK}", XOOPS_URL . "/register.php?op=actv&id=" . $user->getVar("uid") . "&actkey=" . $user->getVar('actkey'), $text);
            // send mail
            if ($this->isMail) {
                if (! $this->sendMail($user->getVar("email"), $subject, $text, $headers)) {
                    if ($debug) {
                        $this->errors[] = sprintf(_MAIL_SENDMAILNG, $user->getVar("uname"));
                    }
                } else {
                    if ($debug) {
                        $this->success[] = sprintf(_MAIL_MAILGOOD, $user->getVar("uname"));
                    }
                }
            }
            // send private message
            if ($this->isPM) {
                if (! $this->sendPM($user->getVar("uid"), $subject, $text)) {
                    if ($debug) {
                        $this->errors[] = sprintf(_MAIL_SENDPMNG, $user->getVar("uname"));
                    }
                } else {
                    if ($debug) {
                        $this->success[] = sprintf(_MAIL_PMGOOD, $user->getVar("uname"));
                    }
                }
            }
            flush();
        }
        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    // private
    /**
     * @param $uid
     * @param $subject
     * @param $body
     *
     * @return bool
     */
    function sendPM($uid, $subject, $body)
    {
        global $xoopsUser;
        $pm_handler = &xoops_gethandler('privmessage');
        $pm = &$pm_handler->create();
        $pm->setVar("subject", $subject);
        // RMV-NOTIFY
        $pm->setVar('from_userid', ! empty($this->fromUser) ? $this->fromUser->getVar('uid') : (empty($xoopsUser) ? 1 : $xoopsUser->getVar('uid')));
        $pm->setVar("msg_text", $body);
        $pm->setVar("to_userid", $uid);
        if (! $pm_handler->insert($pm)) {
            return false;
        }

        return true;
    }


    /**
     * Send email
     *
     * Uses the new XoopsMultiMailer
     *
     * @param $email
     * @param $subject
     * @param $body
     * @param $headers
     *
     * @return bool
     */
    function sendMail($email, $subject, $body, $headers)
    {
        $subject = $this->encodeSubject($subject);
        $this->encodeBody($body);
        $this->multimailer->ClearAllRecipients();
        $this->multimailer->AddAddress($email);
        $this->multimailer->Subject = $subject;
        $this->multimailer->Body = $body;
        $this->multimailer->CharSet = $this->charSet;
        $this->multimailer->Encoding = $this->encoding;
        if (! empty($this->fromName)) {
            $this->multimailer->FromName = $this->encodeFromName($this->fromName);
        }
        if (! empty($this->fromEmail)) {
            $this->multimailer->Sender = $this->multimailer->From = $this->fromEmail;
        }

        $this->multimailer->ClearCustomHeaders();
        foreach ($this->headers as $header) {
            $this->multimailer->AddCustomHeader($header);
        }
        if (! $this->multimailer->Send()) {
            $this->errors[] = $this->multimailer->ErrorInfo;

            return false;
        }

        return true;
    }

    // public
    /**
     * @param bool $ashtml
     *
     * @return string
     */
    function getErrors($ashtml = true)
    {
        if (! $ashtml) {
            return $this->errors;
        } else {
            if (! empty($this->errors)) {
                $ret = "<h4>" . _ERRORS . "</h4>";
                foreach ($this->errors as $error) {
                    $ret .= $error . "<br />";
                }
            } else {
                $ret = "";
            }

            return $ret;
        }
    }

    // public
    /**
     * @param bool $ashtml
     *
     * @return string
     */
    function getSuccess($ashtml = true)
    {
        if (! $ashtml) {
            return $this->success;
        } else {
            $ret = "";
            if (! empty($this->success)) {
                foreach ($this->success as $suc) {
                    $ret .= $suc . "<br />";
                }
            }

            return $ret;
        }
    }

    // public
    /**
     * @param      $tag
     * @param null $value
     */
    function assign($tag, $value = null)
    {
        if (is_array($tag)) {
            foreach ($tag as $k => $v) {
                $this->assign($k, $v);
            }
        } else {
            if (! empty($tag) && isset($value)) {
                $tag = strtoupper(trim($tag));
                // RMV-NOTIFY
                // TEMPORARY FIXME: until the X_tags are all in here
                // if ( substr($tag, 0, 2) != "X_" ) {
                $this->assignedTags[$tag] = $value;
                // }
            }
        }
    }

    // public
    /**
     * @param $value
     */
    function addHeaders($value)
    {
        $this->headers[] = trim($value) . $this->LE;
    }

    // public
    /**
     * @param $email
     */
    function setToEmails($email)
    {
        if (! is_array($email)) {
            if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $email)) {
                array_push($this->toEmails, $email);
            }
        } else {
            foreach ($email as $e) {
                $this->setToEmails($e);
            }
        }
    }

    // public
    /**
     * @param $user
     */
    function setToUsers(&$user)
    {
        if (! is_array($user)) {
            if (strtolower(get_class($user)) == "xoopsuser") {
                array_push($this->toUsers, $user);
            }
        } else {
            foreach ($user as $u) {
                $this->setToUsers($u);
            }
        }
    }

    // public
    /**
     * @param $group
     */
    function setToGroups($group)
    {
        if (! is_array($group)) {
            if (strtolower(get_class($group)) == "xoopsgroup") {
                $member_handler = &xoops_gethandler('member');
                $this->setToUsers($member_handler->getUsersByGroup($group->getVar('groupid'), true));
            }
        } else {
            foreach ($group as $g) {
                $this->setToGroups($g);
            }
        }
    }

    // abstract
    // to be overridden by lang specific mail class, if needed
    /**
     * @param $text
     *
     * @return mixed
     */
    function encodeFromName($text)
    {
        return $text;
    }

    // abstract
    // to be overridden by lang specific mail class, if needed
    /**
     * @param $text
     *
     * @return mixed
     */
    function encodeSubject($text)
    {
        return $text;
    }

    // abstract
    // to be overridden by lang specific mail class, if needed
    /**
     * @param $text
     */
    function encodeBody(&$text)
    {
    }
}
