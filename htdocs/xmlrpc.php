<?php
/**
 * XOOPS XMLRPC
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         core
 * @since           2.0.0
 * @version         $Id$
 */

define('XOOPS_XMLRPC', 1);

include __DIR__. DIRECTORY_SEPARATOR . 'mainfile.php';

error_reporting(0);

include_once $GLOBALS['xoops']->path('class/xml/rpc/xmlrpctag.php');
include_once $GLOBALS['xoops']->path('class/xml/rpc/xmlrpcparser.php');

$GLOBALS['xoopsLogger']->activate(false);

$response = new XoopsXmlRpcResponse();
$parser = new XoopsXmlRpcParser(rawurlencode($GLOBALS['HTTP_RAW_POST_DATA']));
if (!$parser->parse()) {
    $response->add(new XoopsXmlRpcFault(102));
} else {
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname('news');
    if (!is_object($module)) {
        $response->add(new XoopsXmlRpcFault(110));
    } else {
        $methods = explode('.', $parser->getMethodName());
        switch ($methods[0]) {
            case 'blogger':
                include_once $GLOBALS['xoops']->path('class/xml/rpc/bloggerapi.php');
                break;
            case 'metaWeblog':
            	include_once $GLOBALS['xoops']->path('class/xml/rpc/metaweblogapi.php');
                $rpc_api = new MetaWeblogApi($parser->getParam(), $response, $module);
                break;
            case 'mt':
                include_once $GLOBALS['xoops']->path('class/xml/rpc/movabletypeapi.php');
                $rpc_api = new MovableTypeApi($parser->getParam(), $response, $module);
                break;
            case 'xoops':
            default:
            	include_once $GLOBALS['xoops']->path('class/xml/rpc/xoopsapi.php');
                $rpc_api = new XoopsApi($parser->getParam(), $response, $module);
                break;
        }
        $method = $methods[1];
        if (!method_exists($rpc_api, $method)) {
            $response->add(new XoopsXmlRpcFault(107));
        } else {
            $rpc_api->$method();
        }
    }
}
$payload =& $response->render();
header('Server: XOOPS XML-RPC Server');
header('Content-type: text/xml');
header('Content-Length: ' . strlen($payload));
echo $payload;
