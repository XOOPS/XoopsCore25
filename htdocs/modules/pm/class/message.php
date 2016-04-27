<?php
/**
 * Private message module
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
 * @package             pm
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

/**
 * {description}
 *
 * @package         pm
 *
 * @author          Kazumi Ono    <onokazu@xoops.org>
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */
class PmMessage extends XoopsObject
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('msg_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('msg_image', XOBJ_DTYPE_OTHER, 'icon1.gif', false, 100);
        $this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('from_userid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('to_userid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('msg_time', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('msg_text', XOBJ_DTYPE_TXTAREA, null, true);
        $this->initVar('read_msg', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('from_delete', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('to_delete', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('from_save', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('to_save', XOBJ_DTYPE_INT, 0, false);
    }
}

/**
 * Class PmMessageHandler
 */
class PmMessageHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'priv_msgs', 'PmMessage', 'msg_id', 'subject');
    }

    /**
     * Mark a message as read
     *
     * @param PmMessage $pm {@link PmMessage} object
     * @param int    $val
     *
     * @return bool
     */
    public function setRead(PmMessage $pm, $val = 1)
    {
        return $this->updateAll('read_msg', (int)$val, new Criteria('msg_id', $pm->getVar('msg_id')), true);
    }

    /**
     * Mark a message as from_delete = 1 or removes it if the recipient has also deleted it
     * @param  PmMessage $pm {@link PmMessage} object
     * @param  int    $val
     * @return bool
     */
    public function setFromdelete(PmMessage $pm, $val = 1)
    {
        if ($pm->getVar('to_delete') == 0) {
            return $this->updateAll('from_delete', (int)$val, new Criteria('msg_id', $pm->getVar('msg_id')));
        } else {
            return parent::delete($pm);
        }
    }

    /**
     * Mark a message as to_delete = 1 or removes it if the sender has also deleted it or sent by anonymous
     * @param  PmMessage $pm {@link PmMessage} object
     * @param  int    $val
     * @return bool
     */
    public function setTodelete(PmMessage $pm, $val = 1)
    {
        if ($pm->getVar('from_delete') == 0 || $pm->getVar('from_userid') == 0) {
            return $this->updateAll('to_delete', (int)$val, new Criteria('msg_id', $pm->getVar('msg_id')));
        } else {
            return parent::delete($pm);
        }
    }

    /**
     * Mark a message as from_save = 1
     * @param  PmMessage $pm {@link PmMessage} object
     * @param  int    $val
     * @return bool
     */
    public function setFromsave(PmMessage $pm, $val = 1)
    {
        return $this->updateAll('from_save', (int)$val, new Criteria('msg_id', $pm->getVar('msg_id')));
    }

    /**
     * Mark a message as to_save = 1
     * @param  PmMessage $pm {@link PmMessage} object
     * @param  int    $val
     * @return bool
     */
    public function setTosave(PmMessage $pm, $val = 1)
    {
        return $this->updateAll('to_save', (int)$val, new Criteria('msg_id', $pm->getVar('msg_id')));
    }

    /**
     * get user's message count in savebox
     * @param  XoopsUser $user
     * @return int
     **/
    public function getSavecount(XoopsUser $user = null)
    {
        if (!is_object($user)) {
            $user =& $GLOBALS['xoopsUser'];
        }
        $crit_to = new CriteriaCompo(new Criteria('to_delete', 0));
        $crit_to->add(new Criteria('to_save', 1));
        $crit_to->add(new Criteria('to_userid', $user->getVar('uid')));
        $crit_from = new CriteriaCompo(new Criteria('from_delete', 0));
        $crit_from->add(new Criteria('from_save', 1));
        $crit_from->add(new Criteria('from_userid', $user->getVar('uid')));
        $criteria = new CriteriaCompo($crit_to);
        $criteria->add($crit_from, 'OR');

        return $this->getCount($criteria);
    }

    /**
     * Send a message to user's email
     * @param  XoopsPrivmessage $pm {@link XoopsPrivmessage} object
     * @param  XoopsUser $user
     * @return bool
     **/
    public function sendEmail(XoopsPrivmessage $pm, XoopsUser $user)
    {
        global $xoopsConfig;

        if (!is_object($user)) {
            $user =& $GLOBALS['xoopsUser'];
        }
        $msg = sprintf(_PM_EMAIL_DESC, $user->getVar('uname'));
        $msg .= "\n\n";
        $msg .= formatTimestamp($pm->getVar('msg_time'));
        $msg .= "\n";
        $from = new XoopsUser($pm->getVar('from_userid'));
        $to   = new XoopsUser($pm->getVar('to_userid'));
        $msg .= sprintf(_PM_EMAIL_FROM, $from->getVar('uname') . ' (' . XOOPS_URL . '/userinfo.php?uid=' . $pm->getVar('from_userid') . ')');
        $msg .= "\n";
        $msg .= sprintf(_PM_EMAIL_TO, $to->getVar('uname') . ' (' . XOOPS_URL . '/userinfo.php?uid=' . $pm->getVar('to_userid') . ')');
        $msg .= "\n";
        $msg .= _PM_EMAIL_MESSAGE . ":\n";
        $msg .= "\n" . $pm->getVar('subject') . "\n";
        $msg .= "\n" . strip_tags(str_replace(array('<p>', '</p>', '<br />', '<br />'), "\n", $pm->getVar('msg_text'))) . "\n\n";
        $msg .= "--------------\n";
        $msg .= $xoopsConfig['sitename'] . ': ' . XOOPS_URL . "\n";

        $xoopsMailer =& xoops_getMailer();
        $xoopsMailer->useMail();
        $xoopsMailer->setToEmails($user->getVar('email'));
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject(sprintf(_PM_EMAIL_SUBJECT, $pm->getVar('subject')));
        $xoopsMailer->setBody($msg);

        return $xoopsMailer->send();
    }

    /**
     * Get {@link XoopsForm} for setting prune criteria
     *
     * @return XoopsForm
     **/
    public function getPruneForm()
    {
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm(_PM_AM_PRUNE, 'form', 'prune.php', 'post', true);

        $form->addElement(new XoopsFormDateTime(_PM_AM_PRUNEAFTER, 'after'));
        $form->addElement(new XoopsFormDateTime(_PM_AM_PRUNEBEFORE, 'before'));
        $form->addElement(new XoopsFormRadioYN(_PM_AM_ONLYREADMESSAGES, 'onlyread', 1));
        $form->addElement(new XoopsFormRadioYN(_PM_AM_INCLUDESAVE, 'includesave', 0));
        $form->addElement(new XoopsFormRadioYN(_PM_AM_NOTIFYUSERS, 'notifyusers', 0));

        $form->addElement(new XoopsFormHidden('op', 'prune'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}
