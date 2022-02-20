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

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
require_once XOOPS_ROOT_PATH . '/class/xml/rpc/xmlrpcapi.php';

/**
 * Class MetaWeblogApi
 */
class MetaWeblogApi extends XoopsXmlRpcApi
{
    /**
     * @param array $params
     * @param \XoopsXmlRpcResponse $response
     * @param \XoopsModule $module
     */
    public function __construct(&$params, $response, $module)
    {
        parent::__construct($params, $response, $module);
        $this->_setXoopsTagMap('storyid', 'postid');
        $this->_setXoopsTagMap('published', 'dateCreated');
        $this->_setXoopsTagMap('uid', 'userid');
        //$this->_setXoopsTagMap('hometext', 'description');
    }

    public function newPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields(null, $this->params[0])) {
                $this->response->add(new XoopsXmlRpcFault(106));
            } else {
                $missing = array();
                $post    = array();
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getXoopsTagMap($tag);
                    if (!isset($this->params[3][$maptag])) {
                        $data = $this->_getTagCdata($this->params[3]['description'], $maptag, true);
                        if (trim($data) == '') {
                            if ($detail['required']) {
                                $missing[] = $maptag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] = $this->params[3][$maptag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<' . $m . '> ';
                        echo $m;
                    }
                    $this->response->add(new XoopsXmlRpcFault(109, $msg));
                } else {
                    $newparams    = array();
                    $newparams[0] = $this->params[0];
                    $newparams[1] = $this->params[1];
                    $newparams[2] = $this->params[2];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    $newparams[3]['xoops_text'] = $this->params[3]['description'];
                    if (isset($this->params[3]['categories']) && is_array($this->params[3]['categories'])) {
                        foreach ($this->params[3]['categories'] as $k => $v) {
                            $newparams[3]['categories'][$k] = $v;
                        }
                    }
                    $newparams[4] = $this->params[4];
                    $xoopsapi     =& $this->_getXoopsApi($newparams);
                    $xoopsapi->_setUser($this->user, $this->isadmin);
                    $xoopsapi->newPost();
                }
            }
        }
    }

    public function editPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields($this->params[0])) {
            } else {
                $missing = array();
                $post    = array();
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getXoopsTagMap($tag);
                    if (!isset($this->params[3][$maptag])) {
                        $data = $this->_getTagCdata($this->params[3]['description'], $maptag, true);
                        if (trim($data) == '') {
                            if ($detail['required']) {
                                $missing[] = $tag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] = $this->params[3][$maptag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<' . $m . '> ';
                    }
                    $this->response->add(new XoopsXmlRpcFault(109, $msg));
                } else {
                    $newparams    = array();
                    $newparams[0] = $this->params[0];
                    $newparams[1] = $this->params[1];
                    $newparams[2] = $this->params[2];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    if (isset($this->params[3]['categories']) && is_array($this->params[3]['categories'])) {
                        foreach ($this->params[3]['categories'] as $k => $v) {
                            $newparams[3]['categories'][$k] = $v;
                        }
                    }
                    $newparams[3]['xoops_text'] = $this->params[3]['description'];
                    $newparams[4]               = $this->params[4];
                    $xoopsapi                   =& $this->_getXoopsApi($newparams);
                    $xoopsapi->_setUser($this->user, $this->isadmin);
                    $xoopsapi->editPost();
                }
            }
        }
    }

    public function getPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getPost();
            if (is_array($ret)) {
                $struct  = new XoopsXmlRpcStruct();
                $content = '';
                foreach ($ret as $key => $value) {
                    $maptag = $this->_getXoopsTagMap($key);
                    switch ($maptag) {
                        case 'userid':
                            $struct->add('userid', new XoopsXmlRpcString($value));
                            break;
                        case 'dateCreated':
                            $struct->add('dateCreated', new XoopsXmlRpcDatetime($value));
                            break;
                        case 'postid':
                            $struct->add('postid', new XoopsXmlRpcString($value));
                            $struct->add('link', new XoopsXmlRpcString(XOOPS_URL . '/modules/xoopssections/item.php?item=' . $value));
                            $struct->add('permaLink', new XoopsXmlRpcString(XOOPS_URL . '/modules/xoopssections/item.php?item=' . $value));
                            break;
                        case 'title':
                            $struct->add('title', new XoopsXmlRpcString($value));
                            break;
                        default :
                            $content .= '<' . $key . '>' . trim($value) . '</' . $key . '>';
                            break;
                    }
                }
                $struct->add('description', new XoopsXmlRpcString($content));
                $this->response->add($struct);
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }

    public function getRecentPosts()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getRecentPosts();
            if (is_array($ret)) {
                $arr   = new XoopsXmlRpcArray();
                $count = count($ret);
                if ($count == 0) {
                    $this->response->add(new XoopsXmlRpcFault(106, 'Found 0 Entries'));
                } else {
                    for ($i = 0; $i < $count; ++$i) {
                        $struct  = new XoopsXmlRpcStruct();
                        $content = '';
                        foreach ($ret[$i] as $key => $value) {
                            $maptag = $this->_getXoopsTagMap($key);
                            switch ($maptag) {
                                case 'userid':
                                    $struct->add('userid', new XoopsXmlRpcString($value));
                                    break;
                                case 'dateCreated':
                                    $struct->add('dateCreated', new XoopsXmlRpcDatetime($value));
                                    break;
                                case 'postid':
                                    $struct->add('postid', new XoopsXmlRpcString($value));
                                    $struct->add('link', new XoopsXmlRpcString(XOOPS_URL . '/modules/news/article.php?item_id=' . $value));
                                    $struct->add('permaLink', new XoopsXmlRpcString(XOOPS_URL . '/modules/news/article.php?item_id=' . $value));
                                    break;
                                case 'title':
                                    $struct->add('title', new XoopsXmlRpcString($value));
                                    break;
                                default :
                                    $content .= '<' . $key . '>' . trim($value) . '</' . $key . '>';
                                    break;
                            }
                        }
                        $struct->add('description', new XoopsXmlRpcString($content));
                        $arr->add($struct);
                        unset($struct);
                    }
                    $this->response->add($arr);
                }
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }

    public function getCategories()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getCategories();
            if (is_array($ret)) {
                $arr = new XoopsXmlRpcArray();
                foreach ($ret as $id => $detail) {
                    $struct = new XoopsXmlRpcStruct();
                    $struct->add('description', new XoopsXmlRpcString($detail));
                    $struct->add('htmlUrl', new XoopsXmlRpcString(XOOPS_URL . '/modules/news/index.php?storytopic=' . $id));
                    $struct->add('rssUrl', new XoopsXmlRpcString(''));
                    $catstruct = new XoopsXmlRpcStruct();
                    $catstruct->add($detail['title'], $struct);
                    $arr->add($catstruct);
                    unset($struct, $catstruct);
                }
                $this->response->add($arr);
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }
}
