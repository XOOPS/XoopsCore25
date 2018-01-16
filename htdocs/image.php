<?php
/**
 * XOOPS image access/edit
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
 * @package             core
 * @since               2.5.7
 * @author              luciorota <lucio.rota@gmail.com>, Joe Lencioni <joe@shiftingpixel.com>
 *
 * Enhanced image access/edit
 * This enhanced version is very useful in many cases, for example when you need a
 * smallest version of an image. This script uses Xoops cache to minimize server load.
 *
 *
 * Parameters need to be passed in through the URL's query string:
 * @param int      id             Xoops image id;
 * @param string   url            relative to XOOPS_ROOT_PATH, path of local image starting with "/"
 *                                 (e.g. /images/toast.jpg);
 * @param string   src            relative to XOOPS_ROOT_PATH, path of local image starting with "/"
 * @param int      width          (optional) maximum width of final image in pixels (e.g. 700);
 * @param int      height         (optional) maximum height of final image in pixels (e.g. 700);
 * @param string   color          (optional) background hex color for filling transparent PNGs (e.g. 900 or 16a942);
 * @param string   cropratio      (optional) ratio of width to height to crop final image (e.g. 1:1 or 3:2);
 * @param boolean  nocache        (optional) don't read image from the cache;
 * @param boolean  noservercache  (optional) don't read image from the server cache;
 * @param boolean  nobrowsercache (optional) don't read image from the browser cache;
 * @param int      quality        (optional, 0-100, default: 90) quality of output image;
 * @param mixed    filter         (optional, imagefilter 2nd, 3rd, 4th, 5th arguments, more info on php.net
 *                                 manual) a filter or an array of filters;
 * @param int      radius         (optional, 1, 2, 3 or 4 integer values, CW) round corner radius
 * @param float    angle          (optional), rotation angle)
 *
 */

/* @example         image.php
 * Resizing a JPEG:
 * <img src="/image.php?url=image-name.jpg&width=100&height=100" alt="Don't forget your alt text" />
 * Resizing and cropping a JPEG into a square:
 * <img src="/image.php?url=image-name.jpg?width=100&height=100&cropratio=1:1" alt="Don't forget your alt text" />
 * Matting a PNG with #990000:
 * <img src="/image.php?url=image-name.png?color=900&image=/path/to/image.png" alt="Don't forget your alt text" />
 * Apply a filter:
 * <img src="/image.php?url=/path/to/image.png&filter=IMG_FILTER_COLORIZE,128,60,256" alt="Don't forget the alt text" />
 * Apply more filters (array) :
 * <img src="/image.php?url=/path/to/image.png&filter[]=IMG_FILTER_GRAYSCALE&filter[]=IMG_FILTER_COLORIZE,128,60,256" />
 * Round the image corners:
 * All corners with same radius:
 * <img src="/image.php?url=/path/to/image.png&radius=20" alt="Don't forget your alt text" />
 * Left and right corners with different radius (20 for left corners and 40 for right corners)
 * <img src="/image.php?url=/path/to/image.png&radius=20,40" alt="Don't forget your alt text" />
 * 4 corners, 4 radius, clockwise order
 * <img src="/image.php?url=/path/to/image.png&radius=20,40,0,10" alt="Don't forget your alt text" />
 *
 */
define('MEMORY_TO_ALLOCATE', '100M');
define('DEFAULT_IMAGE_QUALITY', 90);
define('DEFAULT_BACKGROUND_COLOR', '000000');
define('ONLY_LOCAL_IMAGES', true);
define('ENABLE_IMAGEFILTER', true); // Set to false to avoid excessive server load
define('ENABLE_ROUNDCORNER', true); // Set to false to avoid excessive server load
define('ENABLE_IMAGEROTATE', true); // Set to false to avoid excessive server load

if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(false); // will never get called on PHP 5.4+
}
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}

$xoopsOption['nocommon'] = true;
require_once __DIR__ . '/mainfile.php';

include_once __DIR__ . '/include/defines.php';
include_once __DIR__ . '/include/functions.php';
include_once __DIR__ . '/include/version.php';
include_once __DIR__ . '/kernel/object.php';
include_once __DIR__ . '/class/xoopsload.php';
include_once __DIR__ . '/class/preload.php';
include_once __DIR__ . '/class/module.textsanitizer.php';
include_once __DIR__ . '/class/database/databasefactory.php';
require_once __DIR__ . '/class/criteria.php';
XoopsLoad::load('xoopslogger');
$xoopsLogger = XoopsLogger::getInstance();
$xoopsLogger->startTime();
error_reporting(0);

/**
 * @param $etag
 * @param $lastModified
 * @return null
 */
function doConditionalGet($etag, $lastModified)
{
    header("Last-Modified: $lastModified");
    header("ETag: \"{$etag}\"");
    $ifNoneMatch = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;
    $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
        ? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
    if (!$ifModifiedSince && !$ifNoneMatch) {
        return null;
    }
    if ($ifNoneMatch && $ifNoneMatch != $etag && $ifNoneMatch != '"' . $etag . '"') {
        return null;
    } // etag is there but doesn't match
    if ($ifModifiedSince && $ifModifiedSince != $lastModified) {
        return null;
    } // if-modified-since is there but doesn't match
    // Nothing has changed since their last request - serve a 304 and exit
    header('HTTP/1.1 304 Not Modified');
    exit();
}

/**
 * ref: http://www.tricksofit.com/2014/08/round-corners-on-image-using-php-and-gd-library
 *
 * @param resource $sourceImage GD Image resource
 * @param int[]    $radii       array(top left, top right, bottom left, bottom right) of pixel radius
 *                               for each corner. A 0 disables rounding on a corner.
 *
 * @return resource
 */
function imageCreateCorners($sourceImage, $radii)
{
    $q = 2; // quality - improve alpha blending by using larger (*$q) image size

    // find a unique color
    $tryCounter = 0;
    do {
        if (++$tryCounter > 255) {
            $r = 2;
            $g = 254;
            $b = 0;
            break;
        }
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);
    } while (imagecolorexact($sourceImage, $r, $g, $b) < 0);

    $imageWidth = imagesx($sourceImage);
    $imageHeight = imagesy($sourceImage);

    $workingWidth = $imageWidth * $q;
    $workingHeight = $imageHeight * $q;

    $workingImage= imagecreatetruecolor($workingWidth, $workingHeight);
    $alphaColor = imagecolorallocatealpha($workingImage, $r, $g, $b, 127);
    imagealphablending($workingImage, false);
    imagesavealpha($workingImage, true);
    imagefilledrectangle($workingImage, 0, 0, $workingWidth, $workingHeight, $alphaColor);

    imagefill($workingImage, 0, 0, $alphaColor);
    imagecopyresampled($workingImage, $sourceImage, 0, 0, 0, 0, $workingWidth, $workingHeight, $imageWidth, $imageHeight);
    if (0 < ($radius = $radii[0] * $q)) { // left top
        imagearc($workingImage, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphaColor);
        imagefilltoborder($workingImage, 0, 0, $alphaColor, $alphaColor);
    }
    if (0 < ($radius = $radii[1] * $q)) { // right top
        imagearc($workingImage, $workingWidth - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphaColor);
        imagefilltoborder($workingImage, $workingWidth - 1, 0, $alphaColor, $alphaColor);
    }
    if (0 < ($radius = $radii[2] * $q)) { // left bottom
        imagearc($workingImage, $radius - 1, $workingHeight - $radius, $radius * 2, $radius * 2, 90, 180, $alphaColor);
        imagefilltoborder($workingImage, 0, $workingHeight - 1, $alphaColor, $alphaColor);
    }
    if (0 < ($radius = $radii[3] * $q)) { // right bottom
        imagearc($workingImage, $workingWidth - $radius, $workingHeight - $radius, $radius * 2, $radius * 2, 0, 90, $alphaColor);
        imagefilltoborder($workingImage, $workingWidth - 1, $workingHeight - 1, $alphaColor, $alphaColor);
    }
    imagealphablending($workingImage, true);
    imagecolortransparent($workingImage, $alphaColor);

    // scale back down to original size
    $destinationImage = imagecreatetruecolor($imageWidth, $imageHeight);
    imagealphablending($destinationImage, false);
    imagesavealpha($destinationImage, true);
    imagefilledrectangle($destinationImage, 0, 0, $imageWidth, $imageHeight, $alphaColor);
    imagecopyresampled($destinationImage, $workingImage, 0, 0, 0, 0, $imageWidth, $imageHeight, $workingWidth, $workingHeight);

    // imagedestroy($sourceImage);
    imagedestroy($workingImage);

    return $destinationImage;
}

/**
 * @param $orig
 * @param $final
 *
 * @return mixed
 */
function findSharp($orig, $final)
{
    // Function from Ryan Rud (http://adryrun.com)
    $final *= (750.0 / $orig);
    $a = 52;
    $b = -0.27810650887573124;
    $c = .00047337278106508946;
    $result = $a + $b * $final + $c * $final * $final;

    return max(round($result), 0);
}

/**
 * issue an error for bad request
 *
 * Many different issues end up here, so message is generic 404. This keeps us from leaking info by probing
 */
function exit404BadReq()
{
    header('HTTP/1.1 404 Not Found');
    exit();
}

/**
 * check local image url for possible issues
 *
 * @param string $imageUrl url to local image starting at site root with a '/'
 *
 * @return bool true if name is acceptable, exit if not
 */
function imageFilenameCheck($imageUrl)
{
    if ($imageUrl[0] !== '/') { // must start with slash
        exit404BadReq();
    }

    if ($imageUrl === '/') { // can't be empty
        exit404BadReq();
    }

    if (preg_match('/(\.\.|<|>|\:|[[:cntrl:]])/', $imageUrl)) { // no "..", "<", ">", ":" or controls
        exit404BadReq();
    }

    $fullPath = XOOPS_ROOT_PATH . $imageUrl;
    if (strpos($fullPath, XOOPS_VAR_PATH) === 0) { // no access to data (shouldn't be in root, but...)
        exit404BadReq();
    }
    if (strpos($fullPath, XOOPS_PATH) === 0) { // no access to lib (shouldn't be in root, but...)
        exit404BadReq();
    }

    return true;
}

/*
 * Get image
 */
// Get id (Xoops image) or url or src (standard image)
$imageId = isset($_GET['id']) ? (int)$_GET['id'] : false;
$imageUrl = isset($_GET['url']) ? (string)$_GET['url'] : (isset($_GET['src']) ? (string)$_GET['src'] : false);
if (!empty($imageId)) {
    // If image is a Xoops image
    /* @var $imageHandler XoopsImageHandler */
    $imageHandler = xoops_getHandler('image');
    $criteria = new CriteriaCompo(new Criteria('i.image_display', true));
    $criteria->add(new Criteria('i.image_id', $imageId));
    $images = $imageHandler->getObjects($criteria, false, true);
    if (count($images) != 1) {
        // No Xoops images or to many Xoops images
        header('Content-type: image/gif');
        readfile(XOOPS_UPLOAD_PATH . '/blank.gif');
        exit();
    }
    $image = $images[0];
    // Get image category
    $imgcatId = $image->getVar('imgcat_id');
    $imgcatHandler = xoops_getHandler('imagecategory');
    if (!$imgcat = $imgcatHandler->get($imgcatId)) {
        // No Image category
        header('Content-type: image/gif');
        readfile(XOOPS_UPLOAD_PATH . '/blank.gif');
        exit();
    }
    // Get image data
    $imageFilename = $image->getVar('image_name'); // image filename
    $imageMimetype = $image->getVar('image_mimetype');
    $imageCreatedTime = $image->getVar('image_created'); // image creation date
    if ($imgcat->getVar('imgcat_storetype') === 'db') {
        $imagePath = null;
        $imageData = $image->getVar('image_body');
    } else {
        $imagePath = XOOPS_UPLOAD_PATH . '/' . $image->getVar('image_name');
        $imageData = file_get_contents($imagePath);
    }
    $sourceImage = imagecreatefromstring($imageData);
    $imageWidth = imagesx($sourceImage);
    $imageHeight = imagesy($sourceImage);
} elseif (!empty($imageUrl)) {
    // If image is a standard image
    if (ONLY_LOCAL_IMAGES) {
        // Images must be local files, so for convenience we strip the domain if it's there
        $imageUrl = str_replace(XOOPS_URL, '', $imageUrl);

        // will exit on any unacceptable urls
        imageFilenameCheck($imageUrl);

        $imagePath = XOOPS_ROOT_PATH . $imageUrl;
        if (!file_exists($imagePath)) {
            exit404BadReq();
        }
    } else {
        if ($imageUrl{0} === '/') {
            $imageUrl = substr($imageUrl, 0, 1);
        }
        $imagePath = $imageUrl;
    }
    // Get the size and MIME type of the requested image
    $imageFilename = basename($imagePath);  // image filename
    $imagesize = getimagesize($imagePath);
    $imageWidth = $imagesize[0];
    $imageHeight = $imagesize[1];
    $imageMimetype = $imagesize['mime'];
    $imageCreatedTime = filemtime($imagePath); // image creation date
    $imageData = file_get_contents($imagePath);
    switch ($imageMimetype) {
        case 'image/gif':
            $sourceImage = imagecreatefromgif($imagePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($imagePath);
            break;
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($imagePath);
            break;
        default:
            exit404BadReq();
            break;
    }
} else {
    // No id, no url, no src parameters
    header('Content-type: image/gif');
    readfile(XOOPS_ROOT_PATH . '/uploads/blank.gif');
    exit();
}

/*
 * Use Xoops cache
 */
// Get image_data from the Xoops cache only if the edited image has been cached after the latest modification
// of the original image
xoops_load('XoopsCache');
$edited_image_filename = 'editedimage_' . md5($_SERVER['REQUEST_URI']) . '_' . $imageFilename;
$cached_image = XoopsCache::read($edited_image_filename);
if (!isset($_GET['nocache']) && !isset($_GET['noservercache']) && !empty($cached_image)
    && ($cached_image['cached_time'] >= $imageCreatedTime)) {
    header("Content-type: {$imageMimetype}");
    header('Content-Length: ' . strlen($cached_image['image_data']));
    echo $cached_image['image_data'];
    exit();
}

/*
 * Get/check editing parameters
 */
// width, height
$max_width = isset($_GET['width']) ? (int)$_GET['width'] : false;
$max_height = isset($_GET['height']) ? (int)$_GET['height'] : false;
// If either a max width or max height are not specified, we default to something large so the unspecified
// dimension isn't a constraint on our resized image.
// If neither are specified but the color is, we aren't going to be resizing at all, just coloring.
if (!$max_width && $max_height) {
    $max_width = PHP_INT_MAX;
} elseif ($max_width && !$max_height) {
    $max_height = PHP_INT_MAX;
} elseif (!$max_width && !$max_height) {
    $max_width = $imageWidth;
    $max_height = $imageHeight;
}

// color
$color = isset($_GET['color']) ? preg_replace('/[^0-9a-fA-F]/', '', (string)$_GET['color']) : false;

// filter, radius, angle
$filter = isset($_GET['filter']) ? $_GET['filter'] : false;
$radius = isset($_GET['radius']) ? (string)$_GET['radius'] : false;
$angle = isset($_GET['angle']) ? (float)$_GET['angle'] : false;

// If we don't have a width or height or color or filter or radius or rotate we simply output the original
// image and exit
if (empty($_GET['width']) && empty($_GET['height']) && empty($_GET['color']) && empty($_GET['filter'])
    && empty($_GET['radius']) && empty($_GET['angle'])) {
    $last_modified_string = gmdate('D, d M Y H:i:s', $imageCreatedTime) . ' GMT';
    $etag = md5($imageData);
    doConditionalGet($etag, $last_modified_string);
    header("Content-type: {$imageMimetype}");
    header('Content-Length: ' . strlen($imageData));
    echo $imageData;
    exit();
}

// cropratio
$offset_x = 0;
$offset_y = 0;
if (isset($_GET['cropratio'])) {
    $crop_ratio = explode(':', (string)$_GET['cropratio']);
    if (count($crop_ratio) == 2) {
        $ratio_computed = $imageWidth / $imageHeight;
        $crop_radio_computed = (float)$crop_ratio[0] / (float)$crop_ratio[1];
        if ($ratio_computed < $crop_radio_computed) {
            // Image is too tall so we will crop the top and bottom
            $orig_height = $imageHeight;
            $imageHeight = $imageWidth / $crop_radio_computed;
            $offset_y = ($orig_height - $imageHeight) / 2;
        } elseif ($ratio_computed > $crop_radio_computed) {
            // Image is too wide so we will crop off the left and right sides
            $orig_width = $imageWidth;
            $imageWidth = $imageHeight * $crop_radio_computed;
            $offset_x = ($orig_width - $imageWidth) / 2;
        }
    }
}
// Setting up the ratios needed for resizing. We will compare these below to determine how to resize the image
// (based on height or based on width)
$xRatio = $max_width / $imageWidth;
$yRatio = $max_height / $imageHeight;
if ($xRatio * $imageHeight < $max_height) {
    // Resize the image based on width
    $tn_height = ceil($xRatio * $imageHeight);
    $tn_width = $max_width;
} else {
    // Resize the image based on height
    $tn_width = ceil($yRatio * $imageWidth);
    $tn_height = $max_height;
}

// quality
$quality = isset($_GET['quality']) ? (int)$_GET['quality'] : DEFAULT_IMAGE_QUALITY;

/*
 * Start image editing
 */
// We don't want to run out of memory
ini_set('memory_limit', MEMORY_TO_ALLOCATE);

// Set up a blank canvas for our resized image (destination)
$destination_image = imagecreatetruecolor($tn_width, $tn_height);

imagealphablending($destination_image, false);
imagesavealpha($destination_image, true);
$transparent = imagecolorallocatealpha($destination_image, 255, 255, 255, 127);
imagefilledrectangle($destination_image, 0, 0, $tn_width, $tn_height, $transparent);

// Set up the appropriate image handling functions based on the original image's mime type
switch ($imageMimetype) {
    case 'image/gif':
        // We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
        // This is maybe not the ideal solution, but IE6 can suck it
        $output_function = 'imagepng';
        $imageMimetype = 'image/png'; // We need to convert GIFs to PNGs
        $do_sharpen = false;
        $quality = round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression
                                                // level of 0 (no compression) through 9 (max)
        break;
    case 'image/png':
    case 'image/x-png':
        $output_function = 'imagepng';
        $do_sharpen = false;
        $quality = round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
        break;
    case 'image/jpeg':
    case 'image/pjpeg':
        $output_function = 'imagejpeg';
        $do_sharpen = true;
        break;
    default:
        exit404BadReq();
        break;
}

// Resample the original image into the resized canvas we set up earlier
imagecopyresampled($destination_image, $sourceImage, 0, 0, $offset_x, $offset_y, $tn_width, $tn_height, $imageWidth, $imageHeight);

// Set background color
if (in_array($imageMimetype, array('image/gif', 'image/png'))) {
    if (!$color) {
        // If this is a GIF or a PNG, we need to set up transparency
        imagealphablending($destination_image, false);
        imagesavealpha($destination_image, true);
        $png_transparency = imagecolorallocatealpha($destination_image, 0, 0, 0, 127);
        imagefill($destination_image, 0, 0, $png_transparency);
    } else {
        // Fill the background with the specified color for matting purposes
        if ($color[0] === '#') {
            $color = substr($color, 1);
        }
        $background = false;
        if (strlen($color) == 6) {
            $background = imagecolorallocate(
                $destination_image,
                intval($color[0] . $color[1], 16),
                intval($color[2] . $color[3], 16),
                intval($color[4] . $color[5], 16)
            );
        } elseif (strlen($color) == 3) {
            $background = imagecolorallocate(
                $destination_image,
                intval($color[0] . $color[0], 16),
                intval($color[1] . $color[1], 16),
                intval($color[2] . $color[2], 16)
            );
        }
        if ($background) {
            imagefill($destination_image, 0, 0, $background);
        }
    }
} else {
    if (!$color) {
        $color = DEFAULT_BACKGROUND_COLOR;
    }
    // Fill the background with the specified color for matting purposes
    if ($color[0] === '#') {
        $color = substr($color, 1);
    }
    $background = false;
    if (strlen($color) == 6) {
        $background = imagecolorallocate(
            $destination_image,
            intval($color[0] . $color[1], 16),
            intval($color[2] . $color[3], 16),
            intval($color[4] . $color[5], 16)
        );
    } elseif (strlen($color) == 3) {
        $background = imagecolorallocate(
            $destination_image,
            intval($color[0] . $color[0], 16),
            intval($color[1] . $color[1], 16),
            intval($color[2] . $color[2], 16)
        );
    }
    if ($background) {
        imagefill($destination_image, 0, 0, $background);
    }
}

// Imagefilter
if (ENABLE_IMAGEFILTER && !empty($filter)) {
    $filterSet = (array) $filter;
    foreach ($filterSet as $currentFilter) {
        $rawFilterArgs = explode(',', $currentFilter);
        $filterConst = constant(array_shift($rawFilterArgs));
        if (null !== $filterConst) { // skip if unknown constant
            $filterArgs = array();
            $filterArgs[] = $destination_image;
            $filterArgs[] = $filterConst;
            foreach ($rawFilterArgs as $tempValue) {
                $filterArgs[] = trim($tempValue);
            }
            call_user_func_array('imagefilter', $filterArgs);
        }
    }
}

// Round corners
if (ENABLE_ROUNDCORNER && !empty($radius)) {
    $radii = explode(',', $radius);
    switch (count($radii)) {
        case 1:
            $radii[3] = $radii[2] = $radii[1] = $radii[0];
            break;
        case 2:
            $radii[3] = $radii[0];
            $radii[2] = $radii[1];
            break;
        case 3:
            $radii[3] = $radii[0];
            break;
        case 4:
            // NOP
            break;
    }

    $destination_image = imageCreateCorners($destination_image, $radii);
    // we need png to support the alpha corners correctly
    if ($imageMimetype === 'image/jpeg') {
        $output_function = 'imagepng';
        $imageMimetype = 'image/png';
        $do_sharpen = false;
        $quality = round(10 - ($quality / 10));
    }
}

// Imagerotate
if (ENABLE_IMAGEROTATE && !empty($angle)) {
    $destination_image = imagerotate($destination_image, $angle, $background, 0);
}

if ($do_sharpen) {
    // Sharpen the image based on two things:
    // (1) the difference between the original size and the final size
    // (2) the final size
    $sharpness = findSharp($imageWidth, $tn_width);
    $sharpen_matrix = array(
        array(-1, -2, -1),
        array(-2, $sharpness + 12, -2),
        array(-1, -2, -1));
    $divisor = $sharpness;
    $offset = 0;
    imageconvolution($destination_image, $sharpen_matrix, $divisor, $offset);
}

// Put the data of the resized image into a variable
ob_start();
$output_function($destination_image, null, $quality);
$imageData = ob_get_contents();
ob_end_clean();
// Update $image_created_time
$imageCreatedTime = time();

// Clean up the memory
imagedestroy($sourceImage);
imagedestroy($destination_image);

/*
 * Write the just edited image into the Xoops cache
 */
$cached_image['edited_image_filename'] = $edited_image_filename;
$cached_image['image_data'] = $imageData;
$cached_image['cached_time'] = $imageCreatedTime;
XoopsCache::write($edited_image_filename, $cached_image);

/*
 * Send the edited image to the browser
 */
// See if the browser already has the image
$last_modified_string = gmdate('D, d M Y H:i:s', $imageCreatedTime) . ' GMT';
$etag = md5($imageData);
doConditionalGet($etag, $last_modified_string);

header('HTTP/1.1 200 OK');
// if image is cacheable
if (!isset($_GET['nocache']) && !isset($_GET['nobrowsercache'])) {
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $imageCreatedTime) . 'GMT');
    header('Cache-control: max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . 'GMT');
} else {
    // "Kill" the browser cache
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // past date
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache'); // HTTP/1.0
}
header("Content-type: {$imageMimetype}");
header("Content-disposition: filename={$imageFilename}");
header('Content-Length: ' . strlen($imageData));
echo $imageData;
