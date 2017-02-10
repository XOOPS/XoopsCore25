<?php

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
 * 1. Setup your client-side code, as documented on http://docs.fineuploader.com.
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
 */

include __DIR__ . '/../../mainfile.php';
$xoopsLogger->activated = false;

// Include the upload handler class
require_once "handler.php";

// Get imgcat_id
$imgcat_id = isset($_GET['imgcat_id']) ? (int)$_GET['imgcat_id'] : 0;

$imgcat_handler = xoops_getHandler('imagecategory');
$imgcat         = $imgcat_handler->get($imgcat_id);
$error          = false;
if (!is_object($imgcat)) {
	$error = true;
} else {
	$imgcatperm_handler = xoops_getHandler('groupperm');
	if (is_object($xoopsUser)) {
		if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, $xoopsUser->getGroups())) {
			$error = true;
		}
	} else {
		if (!$imgcatperm_handler->checkRight('imgcat_write', $imgcat_id, XOOPS_GROUP_ANONYMOUS)) {
			$error = true;
		}
	}
}
if ($error != false) {
	// erreur
	exit();
}


$uploader = new UploadHandler();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
$uploader->allowedExtensions = array(); // all files types allowed by default

// Specify max file size in bytes.
$uploader->sizeLimit = null;

// Specify the input name set in the javascript.
$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
$uploader->chunksFolder = dirname(dirname(__DIR__)) . '/uploads/fine-uploader/files';

$method = get_request_method();

// This will retrieve the "intended" request method.  Normally, this is the
// actual method of the request.  Sometimes, though, the intended request method
// must be hidden in the parameters of the request.  For example, when attempting to
// delete a file using a POST request. In that case, "DELETE" will be sent along with
// the request in a "_method" parameter.
function get_request_method() {
    global $HTTP_RAW_POST_DATA;

    if(isset($HTTP_RAW_POST_DATA)) {
    	parse_str($HTTP_RAW_POST_DATA, $_POST);
    }

    if (isset($_POST["_method"]) && $_POST["_method"] != null) {
        return $_POST["_method"];
    }

    return $_SERVER["REQUEST_METHOD"];
}

if ($method == "POST") {
    header("Content-Type: text/plain");

    // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
    // For example: /myserver/handlers/endpoint.php?done
    if (isset($_GET["done"])) {
        $result = $uploader->combineChunks(dirname(dirname(__DIR__)) . '/uploads/images');
    }
    // Handles upload requests
    else {
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(dirname(dirname(__DIR__)) . '/uploads/images', null, 'img_');
        // To return a name used for uploaded file you can use the following line.
        $result["uploadName"] = $uploader->getUploadName();
    }
    echo json_encode($result);
	
	$nicename = substr($uploader->getName(), 0, strrpos ($uploader->getName(), '.'));
    $mimetype = mime_content_type(dirname(dirname(__DIR__)) . '/uploads/images/' . $uploader->getUploadName());
	
    //save image	
	$image_handler = xoops_getHandler('image');
	$image         = $image_handler->create();
	$image->setVar('image_name', 'images/' . $uploader->getUploadName());
	$image->setVar('image_nicename', $nicename);
	$image->setVar('image_mimetype', $mimetype);
	$image->setVar('image_created', time());
	$image->setVar('image_display', 1);
	$image->setVar('image_weight', 0);
	$image->setVar('imgcat_id', $imgcat_id);
	$image_handler->insert($image);
	
	
}
// for delete file requests
else if ($method == "DELETE") {
    $result = $uploader->handleDelete("files");
    echo json_encode($result);
}
else {
    header("HTTP/1.0 405 Method Not Allowed");
}

?>
