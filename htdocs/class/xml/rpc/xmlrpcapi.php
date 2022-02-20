<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */


/**
 * Class XoopsXmlRpcApi
 */
class XoopsXmlRpcApi
{
    // reference to method parameters
    public $params = array();

    // reference to xmlrpc document class object
    public $response;

    // reference to module class object
    public $module;

    // map between xoops tags and blogger specific tags
    public $xoopsTagMap = array();


    /**
     * @var \XoopsUser $user user class object
     */
    public $user;

    public $isadmin = false;

    /**
     * @param array $params
     * @param \XoopsXmlRpcResponse $response
     * @param \XoopsModule $module
     */
    public function __construct(&$params, $response, $module)
    {
        $this->params   =& $params;
        $this->response = $response;
        $this->module   = $module;
    }

    /**
     * @param XoopsUser $user
     * @param bool      $isadmin
     */
    public function _setUser($user, $isadmin = false)
    {
        if (is_object($user)) {
            $this->user    = $user;
            $this->isadmin = $isadmin;
        }
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function _checkUser($username, $password)
    {
//        if (isset($this->user)) {
        if (is_object($this->user) && $this->user instanceof \XoopsUser) {
            return true;
        }
        /* @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        $this->user     = $member_handler->loginUser(addslashes($username), addslashes($password));
        if (!is_object($this->user)) {
            unset($this->user);

            return false;
        }
        /* @var  XoopsGroupPermHandler $moduleperm_handler */
        $moduleperm_handler = xoops_getHandler('groupperm');
        if (!$moduleperm_handler->checkRight('module_read', $this->module->getVar('mid'), $this->user->getGroups())) {
            unset($this->user);

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function _checkAdmin()
    {
        if ($this->isadmin) {
            return true;
        }
//        if (!isset($this->user)) {
        if (!is_object($this->user) || !$this->user instanceof \XoopsUser) {
            return false;
        }

        if (!$this->user->isAdmin($this->module->getVar('mid'))) {
            return false;
        }
        $this->isadmin = true;

        return true;
    }

    /**
     * @param int|null $post_id
     * @param int|null $blog_id
     *
     * @return array
     */
    public function &_getPostFields($post_id = null, $blog_id = null)
    {
        $ret               = array();
        $ret['title']      = array('required' => true, 'form_type' => 'textbox', 'value_type' => 'text');
        $ret['hometext']   = array('required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea');
        $ret['moretext']   = array('required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea');
        $ret['categories'] = array('required' => false, 'form_type' => 'select_multi', 'data_type' => 'array');

        /*
        if (!isset($blog_id)) {
            if (!isset($post_id)) {
                return false;
            }
            $itemman =& $this->mf->get(MANAGER_ITEM);
            $item =& $itemman->get($post_id);
            $blog_id = $item->getVar('sect_id');
        }
        $sectman =& $this->mf->get(MANAGER_SECTION);
        $this->section =& $sectman->get($blog_id);
        $ret =& $this->section->getVar('sect_fields');
        */

        return $ret;
    }

    /**
     * @param string $xoopstag
     * @param string $blogtag
     */
    public function _setXoopsTagMap($xoopstag, $blogtag)
    {
        if (trim($blogtag) != '') {
            $this->xoopsTagMap[$xoopstag] = $blogtag;
        }
    }

    /**
     * @param string $xoopstag
     *
     * @return mixed
     */
    public function _getXoopsTagMap($xoopstag)
    {
        if (isset($this->xoopsTagMap[$xoopstag])) {
            return $this->xoopsTagMap[$xoopstag];
        }

        return $xoopstag;
    }

    /**
     * @param string $text
     * @param string $tag
     * @param bool   $remove
     *
     * @return string
     */
    public function _getTagCdata(&$text, $tag, $remove = true)
    {
        $ret   = '';
        $match = array();
        if (preg_match("/\<" . $tag . "\>(.*)\<\/" . $tag . "\>/is", $text, $match)) {
            if ($remove) {
                $text = str_replace($match[0], '', $text);
            }
            $ret = $match[1];
        }

        return $ret;
    }

    // kind of dirty method to load XOOPS API and create a new object thereof
    // returns itself if the calling object is XOOPS API
    /**
     * @param array $params
     *
     * @return $this|XoopsApi
     */
    public function &_getXoopsApi(&$params)
    {
        if (strtolower(get_class($this)) !== 'xoopsapi') {
            require_once(XOOPS_ROOT_PATH . '/class/xml/rpc/xoopsapi.php');

            return new XoopsApi($params, $this->response, $this->module);
        } else {
            return $this;
        }
    }
}
