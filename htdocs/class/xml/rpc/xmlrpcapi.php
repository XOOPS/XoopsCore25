<?php
// 
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//          Copyright (c) 2000-2016 XOOPS Project (www.xoops.org)            //
//                         <http://xoops.org/>                               //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

/**
 * Class XoopsXmlRpcApi
 */
class XoopsXmlRpcApi
{
    // reference to method parameters
    public $params;

    // reference to xmlrpc document class object
    public $response;

    // reference to module class object
    public $module;

    // map between xoops tags and blogger specific tags
    public $xoopsTagMap = array();

    // user class object
    public $user;

    public $isadmin = false;

    /**
     * @param $params
     * @param $response
     * @param $module
     */
    public function __construct(&$params, &$response, &$module)
    {
        $this->params   =& $params;
        $this->response =& $response;
        $this->module   =& $module;
    }

    /**
     * @param      $user
     * @param bool $isadmin
     */
    public function _setUser(&$user, $isadmin = false)
    {
        if (is_object($user)) {
            $this->user    =& $user;
            $this->isadmin = $isadmin;
        }
    }

    /**
     * @param $username
     * @param $password
     *
     * @return bool
     */
    public function _checkUser($username, $password)
    {
        if (isset($this->user)) {
            return true;
        }
        $member_handler = xoops_getHandler('member');
        $this->user     = $member_handler->loginUser(addslashes($username), addslashes($password));
        if (!is_object($this->user)) {
            unset($this->user);

            return false;
        }
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
        if (!isset($this->user)) {
            return false;
        }
        if (!$this->user->isAdmin($this->module->getVar('mid'))) {
            return false;
        }
        $this->isadmin = true;

        return true;
    }

    /**
     * @param null $post_id
     * @param null $blog_id
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
     * @param $xoopstag
     * @param $blogtag
     */
    public function _setXoopsTagMap($xoopstag, $blogtag)
    {
        if (trim($blogtag) != '') {
            $this->xoopsTagMap[$xoopstag] = $blogtag;
        }
    }

    /**
     * @param $xoopstag
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
     * @param      $text
     * @param      $tag
     * @param bool $remove
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
     * @param $params
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
