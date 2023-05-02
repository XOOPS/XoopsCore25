<?php

use Xmf\Jwt\TokenReader;

/**
 * PHP Server-Side Example for Fine Uploader (traditional endpoint handler).
 * Maintained by Widen Enterprises.
 *
 * This example:
 *  - handles chunked and non-chunked requests
 *  - supports the concurrent chunking feature
 *  - assumes all upload requests are multipart encoded
 *  - supports the delete file feature
 *
 * Follow these steps to get up and running with Fine Uploader in a PHP environment:
 *
 * 1. Set up your client-side code, as documented on http://docs.fineuploader.com.
 *
 * 2. Copy this file and handler.php to your server.
 *
 * 3. Ensure your php.ini file contains appropriate values for
 *    max_input_time, upload_max_filesize and post_max_size.
 *
 * 4. Ensure your "chunks" and "files" folders exist and are writable.
 *    "chunks" is only needed if you have enabled the chunking feature client-side.
 *
 * 5. If you have chunking enabled in Fine Uploader, you MUST set a value for the `chunking.success.endpoint` option.
 *    This will be called by Fine Uploader when all chunks for a file have been successfully uploaded, triggering the
 *    PHP server to combine all parts into one file. This is particularly useful for the concurrent chunking feature,
 *    but is now required in all cases if you are making use of this PHP example.
 *
 *
 * @license   MIT License (MIT)
 * @copyright Copyright (c) 2015-present, Widen Enterprises, Inc.
 * @link      https://github.com/FineUploader/php-traditional-server
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015-present, Widen Enterprises, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

if (isset($_POST['Authorization'])) {
    define('PROTECTOR_SKIP_DOS_CHECK', 1);
}
include __DIR__ . '/mainfile.php';
$xoopsLogger->activated = false;

/**
 * Get our expected claims from the JSON Web Token.
 *
 * This is the list of claims which should be included:
 *
 *  aud     audience (asserted as our php script name)
 *  cat     category id the user has chosen and is authorized for
 *  uid     user id (asserted as the session specified user)
 *  handler handler class
 *  moddir  module directory for handler
 *
 * We will assert that aud and uid agree with our expectations (for security)
 */
$assert = array(
    'aud' => basename(__FILE__),
    'uid' => $xoopsUser instanceof \XoopsUser ? $xoopsUser->id() : 0,
);
$claims = TokenReader::fromRequest('fineuploader', 'Authorization', $assert);

if ($claims === false) {
    echo json_encode(array('error' => "Invalid request token"));
    exit;
}

// Include the base upload handler class
XoopsLoad::load('fineuploadhandler', 'system');

$handler = (property_exists($claims, 'handler')) ? $claims->handler : '';
$moddir  = (property_exists($claims, 'moddir'))  ? $claims->moddir  : '';

if ($handler === '' || $moddir === '') {
    header("HTTP/1.0 400 Bad Request");
    exit;
}

/**
 * The handler claim can be specified as either:
 * - a fully qualified and autoloading namespaced name,
 * - a legacy handler name
 */
$className = $handler;
if (false === strpos($handler, '\\')) {
    XoopsLoad::load($handler, $moddir);
    $className = $moddir . $handler;
}
/** @var SystemFineUploadHandler $uploader */
$uploader = new $className($claims);

$method = get_request_method();

if ($method === "POST") {
    header("Content-Type: text/plain");

    // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
    // For example: /myserver/handlers/endpoint.php?done
    if (isset($_GET["done"])) {
        $result = $uploader->combineChunks(XOOPS_ROOT_PATH . "/uploads");
    } else { // Handle upload requests
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(XOOPS_ROOT_PATH . "/uploads");

        // To return a name used for uploaded file you can use the following line.
        $result["uploadName"] = $uploader->getUploadName();
    }

    echo json_encode($result);
} elseif ($method == "DELETE") { // for delete file requests
    $result = $uploader->handleDelete("files");
    echo json_encode($result);
} else {
    header("HTTP/1.0 405 Method Not Allowed");
}

/**
 * This will retrieve the "intended" request method.  Normally, this is the
 * actual method of the request.  Sometimes, though, the intended request method
 * must be hidden in the parameters of the request.  For example, when attempting to
 * delete a file using a POST request. In that case, "DELETE" will be sent along with
 * the request in a "_method" parameter.
 *
 * @return string
 */
function get_request_method()
{
    if (isset($_POST["_method"]) && $_POST["_method"] != null) {
        return $_POST["_method"];
    }
    return $_SERVER["REQUEST_METHOD"];
}
