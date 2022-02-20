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
 * Class MovableTypeApi
 */
class MovableTypeApi extends XoopsXmlRpcApi
{
    /**
     * @param array $params
     * @param \XoopsXmlRpcResponse $response
     * @param \XoopsModule $module
     */
    public function __construct(&$params, $response, $module)
    {
        parent::__construct($params, $response, $module);
    }

    public function getCategoryList()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getCategories(false);
            if (is_array($ret)) {
                $arr = new XoopsXmlRpcArray();
                foreach ($ret as $id => $name) {
                    $struct = new XoopsXmlRpcStruct();
                    $struct->add('categoryId', new XoopsXmlRpcString($id));
                    $struct->add('categoryName', new XoopsXmlRpcString($name['title']));
                    $arr->add($struct);
                    unset($struct);
                }
                $this->response->add($arr);
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }

    public function getPostCategories()
    {
        $this->response->add(new XoopsXmlRpcFault(107));
    }

    public function setPostCategories()
    {
        $this->response->add(new XoopsXmlRpcFault(107));
    }

    public function supportedMethods()
    {
        $this->response->add(new XoopsXmlRpcFault(107));
    }
}
