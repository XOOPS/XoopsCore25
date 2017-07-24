<?php
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 */

if (!defined('XOOPS_INSTALL')) {
    die('XOOPS Custom Installation die');
}

$configs = array();

// setup config site info
$configs['db_types'] = array('mysql' => 'mysql');

// setup config site info
$configs['conf_names'] = array(
    'sitename',
    'slogan',
    'allow_register',
    'meta_keywords',
    'meta_description',
    'meta_author',
    'meta_copyright',
    'closesite',
    'debug_mode',
);

// languages config files
$configs['language_files'] = array(
    'global');

// extension_loaded
$configs['extensions'] = array(
    'mbstring' => array('MBString', sprintf(PHP_EXTENSION, CHAR_ENCODING)),
    'intl'     => array('Intl', sprintf(PHP_EXTENSION, INTL_SUPPORT)),
//  'iconv'    => array('Iconv', sprintf(PHP_EXTENSION, ICONV_CONVERSION)),
    'xml'      => array('XML', sprintf(PHP_EXTENSION, XML_PARSING)),
    'zlib'     => array('Zlib', sprintf(PHP_EXTENSION, ZLIB_COMPRESSION)),
    'gd'       => array(
        (function_exists('gd_info') && $gdlib = @gd_info()) ? 'GD ' . $gdlib['GD Version'] : '',
        sprintf(PHP_EXTENSION, IMAGE_FUNCTIONS)),
    'exif'     => array('Exif', sprintf(PHP_EXTENSION, IMAGE_METAS)),
    'curl'     => array('Curl', sprintf(PHP_EXTENSION, CURL_HTTP)),
);

// Writable files and directories
$configs['writable'] = array(
    'uploads/',
    'uploads/avatars/',
    'uploads/images/',
    'uploads/ranks/',
    'uploads/smilies/',
    'xoops_lib/modules/protector/configs/',
    //'mainfile.php',
    //'include/license.php',
    );

// Modules to be installed by default
$configs['modules'] = array();

// xoops_lib, xoops_data directories
$configs['xoopsPathDefault'] = array(
    'lib'  => 'xoops_lib',
    'data' => 'xoops_data');

// writable xoops_lib, xoops_data directories
$configs['dataPath'] = array(
    'caches'  => array(
        'xoops_cache',
        'smarty_cache',
        'smarty_compile'),
    'configs' => null,
    'data'    => null);
