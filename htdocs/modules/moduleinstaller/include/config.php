<?php declare(strict_types=1);

/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 */
if (!defined('XOOPS_INSTALL')) {
    exit('XOOPS Custom Installation die');
}

$configs = [];

// setup config site info
$configs['db_types'] = ['mysql'];

// setup config site info
$configs['conf_names'] = [
    'sitename',
    'slogan',
    'allow_register',
    'meta_keywords',
    'meta_description',
    'meta_author',
    'meta_copyright',
];

// languages config files
$configs['language_files'] = [
    'global',
];

// extension_loaded
$configs['extensions'] = [
    'mbstring' => ['MBString', sprintf(PHP_EXTENSION, CHAR_ENCODING)],
    'iconv'    => ['Iconv', sprintf(PHP_EXTENSION, ICONV_CONVERSION)],
    'xml'      => ['XML', sprintf(PHP_EXTENSION, XML_PARSING)],
    'zlib'     => ['Zlib', sprintf(PHP_EXTENSION, ZLIB_COMPRESSION)],
    'gd'       => [
        (function_exists('gd_info') && $gdlib = @gd_info()) ? 'GD ' . $gdlib['GD Version'] : '',
        sprintf(PHP_EXTENSION, IMAGE_FUNCTIONS),
    ],
    'exif'     => ['Exif', sprintf(PHP_EXTENSION, IMAGE_METAS)],
];

// Writable files and directories
$configs['writable'] = [
    'uploads/',
    'uploads/avatars/',
    'uploads/images/',
    'uploads/ranks/',
    'uploads/smilies/',
    'mainfile.php',
    'include/license.php',
    'xoops_data/data/secure.php',
];

// Modules to be installed by default
$configs['modules'] = [];

// xoops_lib, xoops_data directories
$configs['xoopsPathDefault'] = [
    'lib'  => 'xoops_lib',
    'data' => 'xoops_data',
];

// writable xoops_lib, xoops_data directories
$configs['dataPath'] = [
    'caches'  => [
        'xoops_cache',
        'smarty_cache',
        'smarty_compile',
    ],
    'configs' => null,
];
