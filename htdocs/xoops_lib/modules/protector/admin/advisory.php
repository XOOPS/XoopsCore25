<?php
include '../../../include/cp_header.php';
include 'admin_header.php';
$db = XoopsDatabaseFactory::getDatabaseConnection();

// beggining of Output
xoops_cp_header();
include __DIR__ . '/mymenu.php';

// open table for ADVISORY
echo "<style>dd {margin-left: 32px;}</style>\n";
echo "<br>\n<div style='border: 2px solid #2F5376;padding:8px;width:95%;' class='bg4'>\n";

// calculate the relative path between XOOPS_ROOT_PATH and XOOPS_TRUST_PATH
$root_paths  = explode('/', XOOPS_ROOT_PATH);
$trust_paths = explode('/', XOOPS_TRUST_PATH);
foreach ($root_paths as $i => $rpath) {
    if ($rpath != $trust_paths[$i]) {
        break;
    }
}
$relative_path = str_repeat('../', count($root_paths) - $i) . implode('/', array_slice($trust_paths, $i));

// the path of XOOPS_TRUST_PATH accessible check
echo "<dl><dt>'XOOPS_TRUST_PATH' : ";
echo "<img src='" . XOOPS_URL . '/' . htmlspecialchars($relative_path) . "/modules/protector/public_check.png' width='40' height='20' alt='' style='border:1px solid black;' /><br><a href='" . XOOPS_URL . '/' . htmlspecialchars($relative_path) . "/modules/protector/public_check.php'>" . _AM_ADV_TRUSTPATHPUBLICLINK . "</a></dt>\n";
echo '<dd>' . _AM_ADV_TRUSTPATHPUBLIC . '</b><br><br></dd></dl>';

// register_globals
echo "<dl><dt>'register_globals' : ";
$safe = !ini_get('register_globals');
if ($safe) {
    echo "off &nbsp; <span style='color:green;font-weight:bold;'>OK</span></dt>\n";
} else {
    echo "on  &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
    echo '<dd><br><br>' . _AM_ADV_REGISTERGLOBALS . '<br><br>
            ' . XOOPS_ROOT_PATH . '/.htaccess<br><br>
            ' . _AM_ADV_REGISTERGLOBALS2 . '<br><br>
            <b>php_flag &nbsp; register_globals &nbsp; off
        </dd>';
}
echo "</b><br><br></dl>\n";

// allow_url_fopen
echo "<dl><dt>'allow_url_fopen' : ";
$safe = !ini_get('allow_url_fopen');
if ($safe) {
    echo "off &nbsp; <span style='color:green;font-weight:bold;'>OK</span></dt>\n";
} else {
    echo "on  &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
    echo '<dd>' . _AM_ADV_ALLOWURLFOPEN . '</dd>';
}
echo "</b><br><br></dl>\n";

// register_long_arrays -- enabling deprecated feature opens potential attack surface
// This option was removed in PHP 5.4, and is no longer supported in XOOPS.
// Any code still using the the long arrays ($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS)
// should be considered "suspect."
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    echo "<dl><dt>'register_long_arrays' : ";
    $safe = !ini_get('register_long_arrays');
    if ($safe) {
        echo "off &nbsp; <span style='color:green;font-weight:bold;'>OK</span></dt>\n";
    } else {
        echo "on  &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
        echo '<dd>' . 'register_long_arrays deprecated/removed' . '</dd>';
    }
    echo "</b><br><br></dl>\n";
}

// session.use_trans_sid
echo "<dl><dt>'session.use_trans_sid' : ";
$safe = !ini_get('session.use_trans_sid');
if ($safe) {
    echo "off &nbsp; <span style='color:green;font-weight:bold;'>OK</span></dt>\n";
} else {
    echo "on  &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
    echo '<dd>' . _AM_ADV_USETRANSSID . '</dd>';
}
echo "</b><br><br></dl>\n";

// XOOPS_DB_PREFIX
echo "<dl><dt>'XOOPS_DB_PREFIX' : ";
$safe = strtolower(XOOPS_DB_PREFIX) !== 'xoops';
if ($safe) {
    echo XOOPS_DB_PREFIX . " &nbsp; <span style='color:green;font-weight:bold;'>OK</span></dt>\n<dd>";
} else {
    echo XOOPS_DB_PREFIX . " &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
    echo '<dd>' . _AM_ADV_DBPREFIX . "<br>\n";
}
echo "<a href='center.php?page=prefix_manager'>" . _AM_ADV_LINK_TO_PREFIXMAN . '</a></dd>';
echo "</b><br><br></dl>\n";

// patch to mainfile.php
echo "<dl><dt>'mainfile.php' : ";
if (!defined('PROTECTOR_PRECHECK_INCLUDED')) {
    echo "missing precheck &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
    echo '<dd>' . _AM_ADV_MAINUNPATCHED . '</dd>';
} elseif (!defined('PROTECTOR_POSTCHECK_INCLUDED')) {
    echo "missing postcheck &nbsp; <span style='color:red;font-weight:bold;'>" . _AM_ADV_NOTSECURE . "</span></dt>\n";
    echo '<dd>' . _AM_ADV_MAINUNPATCHED . '</dd>';
} else {
    echo "patched &nbsp; <span style='color:green;font-weight:bold;'>OK</span></dt>\n";
}
echo "</b><br><br></dl>\n";

// patch to databasefactory.php
echo "<dl><dt>'databasefactory.php' : ";
$db = XoopsDatabaseFactory::getDatabaseConnection();
if (substr(@XOOPS_VERSION, 6, 3) < 2.4 && strtolower(get_class($db)) !== 'protectormysqldatabase') {
    echo "<span style='color:red;font-weight:bold;'>" . _AM_ADV_DBFACTORYUNPATCHED . "</span></dt>\n";
} else {
    echo _AM_ADV_DBFACTORYPATCHED . "<span style='color:green;font-weight:bold;'> OK</span></dt>\n";
}
echo "</dl>\n";

// close table for ADVISORY
echo "</div><br>\n";

// open table for PROTECTION CHECK
echo "<br>\n<div style='border: 2px solid #2F5376;padding:8px;width:95%;' class='bg4'>\n";

echo '<h3>' . _AM_ADV_SUBTITLECHECK . "</h3>\n";
// Check contaminations
$uri_contami = XOOPS_URL . '/index.php?xoopsConfig%5Bnocommon%5D=1';
echo '<dl><dt>' . _AM_ADV_CHECKCONTAMI . ":</dt>\n";
echo "<dd><a href='$uri_contami' target='_blank'>$uri_contami</a></dd>";
echo "</dl>\n";

// Check isolated comments
$uri_isocom = XOOPS_URL . '/index.php?cid=' . urlencode(',password /*');
echo '<dl><dt>' . _AM_ADV_CHECKISOCOM . ":</dt>\n";
echo "<dd><a href='$uri_isocom' target='_blank'>$uri_isocom</a></dd>";
echo "</dl>\n";
// close table for PROTECTION CHECK
echo "</div>\n";

xoops_cp_footer();
