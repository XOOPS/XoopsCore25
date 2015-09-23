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
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package             core
 * @since               2.5.7
 * @author              luciorota <lucio.rota@gmail.com>, Joe Lencioni <joe@shiftingpixel.com>
 * @version             $Id: image.php 13082 2015-06-06 21:59:41Z beckmi $
 *
 * Enhanced image access/edit
 * This enhanced version is very useful in many cases, for example when you need a
 * smallest version of an image. This script uses Xoops cache to minimize server load.
 *
 *
 * Parameters need to be passed in through the URL's query string:
 * @param   int         id              Xoops image id;
 * @param   string      url             relative to XOOPS_MAIN_PATH, path of local image starting with "/" (e.g. /images/toast.jpg);
 * @param   string      src             relative to XOOPS_MAIN_PATH, path of local image starting with "/" (e.g. /images/toast.jpg);
 * @param   int         width           (optional) maximum width of final image in pixels (e.g. 700);
 * @param   int         height          (optional) maximum height of final image in pixels (e.g. 700);
 * @param   string      color           (optional) background hex color for filling transparent PNGs (e.g. 900 or 16a942);
 * @param   string      cropratio       (optional) ratio of width to height to crop final image (e.g. 1:1 or 3:2);
 * @param   boolean     nocache         (optional) don't read image from the cache;
 * @param   boolean     noservercache   (optional) don't read image from the server cache;
 * @param   boolean     nobrowsercache  (optional) don't read image from the browser cache;
 * @param   int         quality         (optional, 0-100, default: 90) quality of output image;
 * @param   mixed       filter          (optional, imagefilter 2nd, 3rd, 4th, 5th arguments, more info on php.net manual) a filter or an array of filters;
 * @param   int         radius          (optional, 1, 2, 3 or 4 integer values, CW) round corner radius
 * @param   float       angle           (optional), rotation angle)
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
 * <img src="/image.php?url=/path/to/image.png&filter=IMG_FILTER_COLORIZE,128,60,256" alt="Don't forget your alt text" />
 * Apply more filters (array) :
 * <img src="/image.php?url=/path/to/image.png&filter[]=IMG_FILTER_GRAYSCALE&filter[]=IMG_FILTER_COLORIZE,128,60,256" alt="Don't forget your alt text" />
 * Round the image corners:
 * All corners with same radius:
 * <img src="/image.php?url=/path/to/image.png&radius=20" alt="Don't forget your alt text" />
 * Left and right corners with different radius (20 for left corners and 40 for right corners)
 * <img src="/image.php?url=/path/to/image.png&radius=20,40" alt="Don't forget your alt text" />
 * 4 corners, 4 radius, clockwise order
 * <img src="/image.php?url=/path/to/image.png&radius=20,40,0,10" alt="Don't forget your alt text" />
 *
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('NWLINE') or define('NWLINE', "\n");
define('MEMORY_TO_ALLOCATE', '100M');
define('DEFAULT_IMAGE_QUALITY', 90);
define('DEFAULT_BACKGROUND_COLOR', '000000');
define('ONLY_LOCAL_IMAGES', true);
define('ENABLE_IMAGEFILTER', true); // Set to false to avoid excessive server load
define('ENABLE_ROUNDCORNER', true); // Set to false to avoid excessive server load
define('ENABLE_IMAGEROTATE', true); // Set to false to avoid excessive server load

error_reporting(false);
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    set_magic_quotes_runtime(0);
}
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}

$xoopsOption['nocommon'] = true;
require_once __DIR__ . DS . 'mainfile.php';

include_once XOOPS_ROOT_PATH . DS . 'include' . DS . 'defines.php';
include_once XOOPS_ROOT_PATH . DS . 'include' . DS . 'functions.php';
include_once XOOPS_ROOT_PATH . DS . 'include' . DS . 'version.php';
include_once XOOPS_ROOT_PATH . DS . 'kernel' . DS . 'object.php';
include_once XOOPS_ROOT_PATH . DS . 'class' . DS . 'xoopsload.php';
include_once XOOPS_ROOT_PATH . DS . 'class' . DS . 'preload.php';
include_once XOOPS_ROOT_PATH . DS . 'class' . DS . 'module.textsanitizer.php';
include_once XOOPS_ROOT_PATH . DS . 'class' . DS . 'database' . DS . 'databasefactory.php';
require_once XOOPS_ROOT_PATH . DS . 'class' . DS . 'criteria.php';
XoopsLoad::load('xoopslogger');
$xoopsLogger = XoopsLogger::getInstance();
$xoopsLogger->startTime();

/**
 * @param $etag
 * @param $last_modified
 * @return null
 */
function doConditionalGet($etag, $last_modified)
{
    header("Last-Modified: $last_modified");
    header("ETag: \"{$etag}\"");
    $if_none_match     = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;
    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
    if (!$if_modified_since && !$if_none_match) {
        return null;
    }
    if ($if_none_match && $if_none_match != $etag && $if_none_match != '"' . $etag . '"') {
        return null;
    } // etag is there but doesn't match
    if ($if_modified_since && $if_modified_since != $last_modified) {
        return null;
    } // if-modified-since is there but doesn't match
    // Nothing has changed since their last request - serve a 304 and exit
    header('HTTP/1.1 304 Not Modified');
    exit();
}

/**
 * @param int    $radius
 * @param int    $rotate
 * @param string $color
 *
 * @return resource
 */
function roundImageCorner($radius = 0, $rotate = 0, $color = DEFAULT_BACKGROUND_COLOR)
{
    $corner_image = imagecreatetruecolor($radius, $radius);
    $clear_color  = imagecolorallocate($corner_image, 0, 0, 0);
    $solid_color  = imagecolorallocate($corner_image, intval(substr($color, 0, 2), 16), intval(substr($color, 2, 2), 16), intval(substr($color, 4, 2), 16));
    imagecolortransparent($corner_image, $clear_color);
    imagefill($corner_image, 0, 0, $solid_color);
    imagefilledellipse($corner_image, $radius, $radius, $radius * 2, $radius * 2, $clear_color);
    if ($rotate != 0) {
        $corner_image = imagerotate($corner_image, $rotate, 0);
    }

    return $corner_image;
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
    $a      = 52;
    $b      = -0.27810650887573124;
    $c      = .00047337278106508946;
    $result = $a + $b * $final + $c * $final * $final;

    return max(round($result), 0);
}

/*
 * Get image
 */
// Get id (Xoops image) or url or src (standard image)
$image_id  = isset($_GET['id']) ? (int)$_GET['id'] : false;
$image_url = isset($_GET['url']) ? (string)$_GET['url'] : isset($_GET['src']) ? (string)$_GET['src'] : false;
if (!empty($image_id)) {
    // If image is a Xoops image
    $image_handler =& xoops_getHandler('image');
    $criteria      = new CriteriaCompo(new Criteria('i.image_display', true));
    $criteria->add(new Criteria('i.image_id', $image_id));
    $images = $image_handler->getObjects($criteria, false, true);
    if (count($images) != 1) {
        // No Xoops images or to many Xoops images
        header('Content-type: image/gif');
        readfile(XOOPS_UPLOAD_PATH . '/blank.gif');
        exit();
    }
    $image = $images[0];
    // Get image category
    $imgcat_id      = $image->getVar('imgcat_id');
    $imgcat_handler =& xoops_getHandler('imagecategory');
    if (!$imgcat = $imgcat_handler->get($imgcat_id)) {
        // No Image category
        header('Content-type: image/gif');
        readfile(XOOPS_UPLOAD_PATH . '/blank.gif');
        exit();
    }
    // Get image data
    $image_filename     = $image->getVar('image_name'); // image filename
    $image_mimetype     = $image->getVar('image_mimetype');
    $image_created_time = $image->getVar('image_created'); // image creation date
    if ($imgcat->getVar('imgcat_storetype') === 'db') {
        $image_path = null;
        $image_data = $image->getVar('image_body');
    } else {
        $image_path = XOOPS_UPLOAD_PATH . '/' . $image->getVar('image_name');
        $image_data = file_get_contents($image_path);
    }
    $source_image = imagecreatefromstring($image_data);
    $image_width  = imagesx($source_image);
    $image_height = imagesy($source_image);
} elseif (!empty($image_url)) {
    // If image is a standard image
    if (ONLY_LOCAL_IMAGES) {
        // Images must be local files, so for convenience we strip the domain if it's there
        $image_url = str_replace(XOOPS_URL, '', (string)$image_url);
        // For security, directories cannot contain ':', images cannot contain '..' or '<', and images must start with '/'
        if ($image_url{0} !== '/' || strpos(dirname($image_url), ':') || preg_match('/(\.\.|<|>)/', $image_url)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Error: malformed image path. Image urls must begin with \'/\'';
            exit();
        }
        // If the image doesn't exist, or we haven't been told what it is, there's nothing that we can do
        if (!$image_url) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Error: no image was specified';
            exit();
        }
        // Strip the possible trailing slash off the document root
        $image_path = XOOPS_ROOT_PATH . $image_url;
        if (!file_exists($image_path)) {
            header('HTTP/1.1 404 Not Found');
            echo 'Error: image does not exist: ' . $image_path;
            exit();
        }
    } else {
        if ($image_url{0} === '/') {
            $image_url = substr($image_url, 0, 1);
        }
        $image_path = $image_url;
    }
    // Get the size and MIME type of the requested image
    $image_filename     = basename($image_path);  // image filename
    $imagesize          = getimagesize($image_path);
    $image_width        = $imagesize[0];
    $image_height       = $imagesize[1];
    $image_mimetype     = $imagesize['mime'];
    $image_created_time = filemtime($image_path); // image creation date
    $image_data         = file_get_contents($image_path);
    switch ($image_mimetype) {
        case 'image/gif' :
            $source_image = imagecreatefromgif($image_path);
            break;
        case 'image/x-png' :
        case 'image/png' :
            $source_image = imagecreatefrompng($image_path);
            break;
        default :
            $source_image = imagecreatefromjpeg($image_path);
            break;
    }
} else {
    // No id, no url, no src parameters
    header('Content-type: image/gif');
    readfile(XOOPS_ROOT_PATH . '/uploads/blank.gif');
    exit();
}

// Make sure that the requested file is actually an image
if (!empty($image_mimetype) && substr($image_mimetype, 0, 6) !== 'image/') {
    header('HTTP/1.1 400 Bad Request');
    echo 'Error: requested file is not an accepted type';
    exit();
}

/*
 * Use Xoops cache
 */
// Get image_data from the Xoops cache only if the edited image has been cached after the latest modification of the original image
xoops_load('XoopsCache');
$edited_image_filename = 'editedimage_' . md5($_SERVER['REQUEST_URI']) . '_' . $image_filename;
$cached_image          = XoopsCache::read($edited_image_filename);
if (!isset($_GET['nocache']) && !isset($_GET['noservercache']) && !empty($cached_image) && ($cached_image['cached_time'] >= $image_created_time)) {
    header("Content-type: {$image_mimetype}");
    header("Content-Length: " . strlen($cached_image['image_data']));
    echo $cached_image['image_data'];
    exit();
}

/*
 * Get/check editing parameters
 */
// width, height
$max_width  = (isset($_GET['width'])) ? (int)$_GET['width'] : false;
$max_height = (isset($_GET['height'])) ? (int)$_GET['height'] : false;
// If either a max width or max height are not specified, we default to something large so the unspecified dimension isn't a constraint on our resized image.
// If neither are specified but the color is, we aren't going to be resizing at all, just coloring.
if (!$max_width && $max_height) {
    $max_width = PHP_INT_MAX;
} elseif ($max_width && !$max_height) {
    $max_height = PHP_INT_MAX;
} elseif (!$max_width && !$max_height) {
    $max_width  = $image_width;
    $max_height = $image_height;
}

// color
$color = (isset($_GET['color'])) ? preg_replace('/[^0-9a-fA-F]/', '', (string)$_GET['color']) : false;

// filter, radius, angle
$filter = (isset($_GET['filter'])) ? $_GET['filter'] : false;
$radius = (isset($_GET['radius'])) ? (string)$_GET['radius'] : false;
$angle  = (isset($_GET['angle'])) ? (float)$_GET['angle'] : false;

// If we don't have a width or height or color or filter or radius or rotate we simply output the original image and exit
if (empty($_GET['width']) && empty($_GET['height']) && empty($_GET['color']) && empty($_GET['filter']) && empty($_GET['radius']) && empty($_GET['angle'])) {
    $last_modified_string = gmdate('D, d M Y H:i:s', $image_created_time) . ' GMT';
    $etag                 = md5($image_data);
    doConditionalGet($etag, $last_modified_string);
    header("Content-type: {$image_mimetype}");
    header('Content-Length: ' . strlen($image_data));
    echo $image_data;
    exit();
}

// cropratio
$offset_x = 0;
$offset_y = 0;
if (isset($_GET['cropratio'])) {
    $crop_ratio = explode(':', (string)$_GET['cropratio']);
    if (count($crop_ratio) == 2) {
        $ratio_computed      = $image_width / $image_height;
        $crop_radio_computed = (float)$crop_ratio[0] / (float)$crop_ratio[1];
        if ($ratio_computed < $crop_radio_computed) {
            // Image is too tall so we will crop the top and bottom
            $orig_height  = $image_height;
            $image_height = $image_width / $crop_radio_computed;
            $offset_y     = ($orig_height - $image_height) / 2;
        } elseif ($ratio_computed > $crop_radio_computed) {
            // Image is too wide so we will crop off the left and right sides
            $orig_width  = $image_width;
            $image_width = $image_height * $crop_radio_computed;
            $offset_x    = ($orig_width - $image_width) / 2;
        }
    }
}
// Setting up the ratios needed for resizing. We will compare these below to determine how to resize the image (based on height or based on width)
$xRatio = $max_width / $image_width;
$yRatio = $max_height / $image_height;
if ($xRatio * $image_height < $max_height) {
    // Resize the image based on width
    $tn_height = ceil($xRatio * $image_height);
    $tn_width  = $max_width;
} else {
    // Resize the image based on height
    $tn_width  = ceil($yRatio * $image_width);
    $tn_height = $max_height;
}

// quality
$quality = (isset($_GET['quality'])) ? (int)$_GET['quality'] : DEFAULT_IMAGE_QUALITY;

/*
 * Start image editing
 */
// We don't want to run out of memory
ini_set('memory_limit', MEMORY_TO_ALLOCATE);

// Set up a blank canvas for our resized image (destination)
$destination_image = imagecreatetruecolor($tn_width, $tn_height);

// Set up the appropriate image handling functions based on the original image's mime type
switch ($file_mimetype) {
    case 'image/gif' :
        // We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
        // This is maybe not the ideal solution, but IE6 can suck it
        $output_function = 'ImagePng';
        $image_mimetype  = 'image/png'; // We need to convert GIFs to PNGs
        $do_sharpen      = false;
        $quality         = round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
        break;
    case 'image/x-png':
    case 'image/png':
        $output_function = 'ImagePng';
        $do_sharpen      = false;
        $quality         = round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
        break;
    default:
        $output_function = 'ImageJpeg';
        $do_sharpen      = true;
        break;
}

// Resample the original image into the resized canvas we set up earlier
imagecopyresampled($destination_image, $source_image, 0, 0, $offset_x, $offset_y, $tn_width, $tn_height, $image_width, $image_height);

// Set background color
if (in_array($file_mimetype, array('image/gif', 'image/png'))) {
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
            $background = imagecolorallocate($destination_image, intval($color[0] . $color[1], 16), intval($color[2] . $color[3], 16), intval($color[4] . $color[5], 16));
        } elseif (strlen($color) == 3) {
            $background = imagecolorallocate($destination_image, intval($color[0] . $color[0], 16), intval($color[1] . $color[1], 16), intval($color[2] . $color[2], 16));
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
        $background = imagecolorallocate($destination_image, intval($color[0] . $color[1], 16), intval($color[2] . $color[3], 16), intval($color[4] . $color[5], 16));
    } elseif (strlen($color) == 3) {
        $background = imagecolorallocate($destination_image, intval($color[0] . $color[0], 16), intval($color[1] . $color[1], 16), intval($color[2] . $color[2], 16));
    }
    if ($background) {
        imagefill($destination_image, 0, 0, $background);
    }
}

// Imagefilter
if (ENABLE_IMAGEFILTER && !empty($filter)) {
    if (!is_array($filter)) {
        eval("imagefilter({$destination_image}, {$filter});");
    } else {
        foreach ($filter as $i => $value) {
            eval("imagefilter({$destination_image}, {$value});");
        }
    }
}

// Roundcorner
if (ENABLE_ROUNDCORNER && !empty($radius)) {
    $radiuses = explode(',', $radius);
    switch (count($radiuses)) {
        case 1 :
            $radiuses[3] = $radiuses[2] = $radiuses[1] = $radiuses[0];
            break;
        case 2 :
            $radiuses[3] = $radiuses[0];
            $radiuses[2] = $radiuses[1];
            break;
        case 3 :
            $radiuses[3] = $radiuses[0];
            break;
        case 4 :
            // NOP
            break;
    }
    $source_width  = imagesx($destination_image);
    $source_height = imagesy($destination_image);
    // top left corner
    if ($radiuses[0]) {
        imagecopymerge($destination_image, roundImageCorner($radiuses[0], 0, $color), 0, 0, 0, 0, $radiuses[0], $radiuses[0], 100);
    }
    // top right corner
    if ($radiuses[1]) {
        imagecopymerge($destination_image, roundImageCorner($radiuses[1], 270, $color), $source_width - $radiuses[1], 0, 0, 0, $radiuses[1], $radiuses[1], 100);
    }
    // bottom right corner
    if ($radiuses[2]) {
        imagecopymerge($destination_image, roundImageCorner($radiuses[2], 180, $color), $source_width - $radiuses[2], $source_height - $radiuses[2], 0, 0, $radiuses[2], $radiuses[2], 100);
    }
    // bottom left corner
    if ($radiuses[3]) {
        imagecopymerge($destination_image, roundImageCorner($radiuses[3], 90, $color), 0, $source_height - $radiuses[3], 0, 0, $radiuses[3], $radiuses[3], 100);
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
    $sharpness      = findSharp($image_width, $tn_width);
    $sharpen_matrix = array(
        array(-1, -2, -1),
        array(-2, $sharpness + 12, -2),
        array(-1, -2, -1));
    $divisor        = $sharpness;
    $offset         = 0;
    imageconvolution($destination_image, $sharpen_matrix, $divisor, $offset);
}

// Put the data of the resized image into a variable
ob_start();
$output_function($destination_image, null, $quality);
$image_data = ob_get_contents();
ob_end_clean();
// Update $image_created_time
$image_created_time = time();

// Clean up the memory
imagedestroy($source_image);
imagedestroy($destination_image);

/*
 * Write the just edited image into the Xoops cache
 */
$cached_image['edited_image_filename'] = $edited_image_filename;
$cached_image['image_data']            = $image_data;
$cached_image['cached_time']           = $image_created_time;
XoopsCache::write($edited_image_filename, $cached_image);

/*
 * Send the edited image to the browser
 */
// See if the browser already has the image
$last_modified_string = gmdate('D, d M Y H:i:s', $image_created_time) . ' GMT';
$etag                 = md5($image_data);
doConditionalGet($etag, $last_modified_string);

header("HTTP/1.1 200 OK");
// if image is cacheable
if (!isset($_GET['nocache']) && !isset($_GET['nobrowsercache'])) {
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $image_created_time) . 'GMT');
    header('Cache-control: max-age=31536000');
    header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 31536000) . 'GMT');
} else {
    // "Kill" the browser cache
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // past date
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache"); // HTTP/1.0
}
header("Content-type: {$image_mimetype}");
header("Content-disposition: filename={$image_name}");
header("Content-Length: " . strlen($image_data));
echo $image_data;
