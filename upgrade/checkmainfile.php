<?php
/**
 * Load mainfile.php wedge that checks for needed upgrades.
 *
 * This should be included instead of mainfile.php
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright 2017 XOOPS Project (www.xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package   upgrader
 * @since     2.5.9
 * @author    Richard Griffith <richard@geekwright.com>
 */

$loadCommon = !isset($xoopsOption['nocommon']);
$xoopsOption['nocommon'] = true;
include_once __DIR__ . '/../mainfile.php';

$mainfileKeys = array(
    // in mainfile.php
    'XOOPS_ROOT_PATH'       => null,
    'XOOPS_PATH'            => null,
    'XOOPS_VAR_PATH'        => null, // *
    'XOOPS_TRUST_PATH'      => null,
    'XOOPS_URL'             => null,
    'XOOPS_COOKIE_DOMAIN'   => null, // *
    'XOOPS_PROT'            => null, // *
    'XOOPS_GROUP_ADMIN'     => null,
    'XOOPS_GROUP_USERS'     => null,
    'XOOPS_GROUP_ANONYMOUS' => null,
    // in data/secure.php
    'XOOPS_DB_TYPE'         => null,
    'XOOPS_DB_CHARSET'      => null,
    'XOOPS_DB_PREFIX'       => null,
    'XOOPS_DB_HOST'         => null,
    'XOOPS_DB_USER'         => null,
    'XOOPS_DB_PASS'         => null,
    'XOOPS_DB_NAME'         => null,
    'XOOPS_DB_PCONNECT'     => null,
);

$needMainfileRewrite = false;
foreach ($mainfileKeys as $key => $unused) {
    if (defined($key)) {
        $mainfileKeys[$key] = constant($key);
    } else {
        $needMainfileRewrite = true;
    }
}

// this is a generated define in a current mainfile, so just define it if it doesn't exist
unset ($mainfileKeys['XOOPS_PROT']);
if (!defined('XOOPS_PROT')) {
    $parts = parse_url(XOOPS_URL);
    $http = (empty($parts['scheme']) ? 'http' : $parts['scheme']) . '://';
    define('XOOPS_PROT', $http);
    unset($parts, $http);
}

// we have what we need so continue
if ($loadCommon) {
    unset($xoopsOption['nocommon']);
    include XOOPS_ROOT_PATH . '/include/common.php';
}

unset($loadCommon);
