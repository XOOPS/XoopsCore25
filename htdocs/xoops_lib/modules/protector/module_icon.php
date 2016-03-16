<?php
// start hack by Trabis
if (!class_exists('ProtectorRegistry')) {
    exit('Registry not found');
}

$registry  = ProtectorRegistry::getInstance();
$mydirname = $registry->getEntry('mydirname');
$mydirpath = $registry->getEntry('mydirpath');
$language  = $registry->getEntry('language');
// end hack by Trabis
date_default_timezone_set(@date_default_timezone_get());

$icon_cache_limit = 3600; // default 3600sec == 1hour

session_cache_limiter('public');
header('Expires: ' . date('r', (int)(time() / $icon_cache_limit) * $icon_cache_limit + $icon_cache_limit));
header("Cache-Control: public, max-age=$icon_cache_limit");
header('Last-Modified: ' . date('r', (int)(time() / $icon_cache_limit) * $icon_cache_limit));
header('Content-type: image/png');

// file name
$file_base = 'module_icon';
if (!empty($_GET['file'])) {
    $file_base = preg_replace('/[^0-9a-z_]/', '', $_GET['file']);
}

$draw_dirname = true;

// icon files must be PNG
$file = $file_base . '.png';

// custom icon
if (file_exists($mydirpath . '/' . $file)) {
    $draw_dirname  = false;
    $icon_fullpath = $mydirpath . '/module_icon.png';
} else {
    $icon_fullpath = __DIR__ . '/images/' . $file;
}

if ($draw_dirname && function_exists('imagecreatefrompng') && function_exists('imagecolorallocate') && function_exists('imagestring') && function_exists('imagepng')) {
    $im = imagecreatefrompng($icon_fullpath);

    $color = imagecolorallocate($im, 0, 0, 0); // black
    $px    = (92 - 6 * strlen($mydirname)) / 2;
    imagestring($im, 3, $px, 34, $mydirname, $color);
    imagepng($im);
    imagedestroy($im);
} else {
    readfile($icon_fullpath);
}
