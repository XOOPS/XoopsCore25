<?php
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package     installer
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 * @version     $Id$
 */

function install_acceptUser($hash = '')
{
    $GLOBALS['xoopsUser'] = null;
    $hash_data = @explode("-", $_COOKIE['xo_install_user'], 2);
    list($uname, $hash_login) = array($hash_data[0], strval( @$hash_data[1] ));
    if (empty($uname) || empty($hash_login)) {
        return false;
    }
    $memebr_handler =& xoops_gethandler('member');
    $user = array_pop($memebr_handler->getUsers(new Criteria('uname', $uname)));
    if ($hash_login != md5($user->getVar('pass') . XOOPS_DB_NAME . XOOPS_DB_PASS . XOOPS_DB_PREFIX)) {
        return false;
    }
    $myts = MyTextsanitizer::getInstance();
    if (is_object($GLOBALS['xoops']) && method_exists($GLOBALS['xoops'], 'acceptUser')) {
        $res = $GLOBALS['xoops']->acceptUser($uname, true, $msg);

        return $res;
    }
    $GLOBALS['xoopsUser'] = $user;
    $_SESSION['xoopsUserId'] = $GLOBALS['xoopsUser']->getVar('uid');
    $_SESSION['xoopsUserGroups'] = $GLOBALS['xoopsUser']->getGroups();

    return true;
}

/**
 * @param $installer_modified
 */
function install_finalize($installer_modified)
{
    // Set mainfile.php readonly
    @chmod(XOOPS_ROOT_PATH . "/mainfile.php", 0444);
    // Set Secure file readonly
    @chmod(XOOPS_VAR_PATH . "/data/secure.php", 0444);
    // Rename installer folder
    @rename(XOOPS_ROOT_PATH . "/install", XOOPS_ROOT_PATH . "/" . $installer_modified);
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 */
function xoFormField( $name, $value, $label, $help = '' )
{
    $myts = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo "<label class='xolabel' for='$name'>$label</label>\n";
    if ($help) {
        echo '<div class="xoform-help">' . $help . "</div>\n";
    }
    if ($name == "adminname") {
        echo "<input type='text' name='$name' id='$name' value='$value' maxlength='25' />";
    } else {
        echo "<input type='text' name='$name' id='$name' value='$value' />";
    }
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 */
function xoPassField($name, $value, $label, $help = '')
{
    $myts = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars( $label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name = $myts->htmlspecialchars( $name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars( $value, ENT_QUOTES );
    echo "<label class='xolabel' for='{$name}'>{$label}</label>\n";
    if ($help) {
        echo '<div class="xoform-help">' . $help . "</div>\n";
    }

    if ($name == "adminpass") {
        echo "<input type='password' name='{$name}' id='{$name}' value='{$value}' onkeyup='passwordStrength(this.value)' />";
    } else {
        echo "<input type='password' name='{$name}' id='{$name}' value='{$value}' />";
    }
}

/*
 * gets list of name of directories inside a directory
 */
/**
 * @param $dirname
 *
 * @return array
 */
function getDirList($dirname)
{
    $dirlist = array();
    if ($handle = opendir($dirname)) {
        while ($file = readdir($handle)) {
            if ($file{0} != '.' && is_dir($dirname . $file)) {
                $dirlist[] = $file;
            }
        }
        closedir($handle);
        asort($dirlist);
        reset($dirlist);
    }

    return $dirlist;
}

/**
 * @param        $status
 * @param string $str
 *
 * @return string
 */
function xoDiag($status = -1, $str = '')
{
    if ($status == -1) {
        $GLOBALS['error'] = true;
    }
    $classes = array(-1 => 'error', 0 => 'warning', 1 => 'success');
    $strings = array(-1 => FAILED, 0 => WARNING, 1 => SUCCESS);
    if (empty($str)) {
        $str = $strings[$status];
    }

    return '<span class="' . $classes[$status] . '">' . $str . '</span>';
}

/**
 * @param      $name
 * @param bool $wanted
 * @param bool $severe
 *
 * @return string
 */
function xoDiagBoolSetting($name, $wanted = false, $severe = false)
{
    $setting = strtolower(ini_get($name));
    $setting = (empty($setting) || $setting == 'off' || $setting == 'false') ? false : true;
    if ($setting == $wanted) {
        return xoDiag(1, $setting ? 'ON' : 'OFF');
    } else {
        return xoDiag($severe ? -1 : 0, $setting ? 'ON' : 'OFF');
    }
}

/**
 * @param $path
 *
 * @return string
 */
function xoDiagIfWritable($path)
{
    $path = "../" . $path;
    $error = true;
    if (!is_dir($path)) {
        if (file_exists($path)) {
            @chmod($path, 0666);
            $error = !is_writeable($path);
        }
    } else {
        @chmod($path, 0777);
        $error = !is_writeable($path);
    }

    return xoDiag($error ? -1 : 1, $error ? ' ' : ' ');

}

/**
 * @return string
 */
function xoPhpVersion()
{
    if (version_compare(phpversion(), '5.3.7', '>=')) {
        return xoDiag(1, phpversion());
    } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
        return xoDiag(0, phpversion());
    } else {
        return xoDiag(-1, phpversion());
    }
}

/**
 * @param $path
 * @param $valid
 *
 * @return string
 */
function genPathCheckHtml($path, $valid)
{
    if ($valid) {
        switch ($path) {
            case 'root':
            $msg = sprintf(XOOPS_FOUND, XOOPS_VERSION);
            break;

            case 'lib':
            case 'data':
            default:
            $msg = XOOPS_PATH_FOUND;
            break;
        }

        return '<span class="pathmessage"><img src="img/yes.png" alt="Success" />' . $msg . '</span>';
    } else {
        switch ($path) {
            case 'root':
            $msg = ERR_NO_XOOPS_FOUND;
            break;

            case 'lib':
            case 'data':
            default:
            $msg = ERR_COULD_NOT_ACCESS;
            break;
        }

        return '<span class="pathmessage"><img src="img/no.png" alt="Error" /> ' . $msg . '</span>';
    }
}

/**
 * @param $link
 *
 * @return mixed
 */
function getDbCharsets($link)
{
    static $charsets = array();
    if ($charsets) return $charsets;

    $charsets["utf8"] = "UTF-8 Unicode";
    $ut8_available = false;
    if ($result = mysql_query("SHOW CHARSET", $link)) {
        while ($row = mysql_fetch_assoc($result)) {
            $charsets[$row["Charset"]] = $row["Description"];
            if ($row["Charset"] == "utf8") {
                $ut8_available = true;
            }
        }
    }
    if (!$ut8_available) {
        unset($charsets["utf8"]);
    }

    return $charsets;
}

/**
 * @param $link
 * @param $charset
 *
 * @return mixed
 */
function getDbCollations($link, $charset)
{
    static $collations = array();
    if (!empty($collations[$charset])) {
        return $collations[$charset];
    }

    if ($result = mysql_query("SHOW COLLATION WHERE CHARSET = '" . mysql_real_escape_string($charset) . "'", $link)) {
        while ($row = mysql_fetch_assoc($result)) {
            $collations[$charset][$row["Collation"]] = $row["Default"] ? 1 : 0;
        }
    }

    return $collations[$charset];
}

/**
 * @param $link
 * @param $charset
 * @param $collation
 *
 * @return null|string
 */
function validateDbCharset($link, &$charset, &$collation)
{
    $error = null;

    if (empty($charset)) {
        $collation = "";
    }
    if (version_compare(mysql_get_server_info($link), "4.1.0", "lt")) {
        $charset = $collation = "";
    }
    if (empty($charset) && empty($collation)) {
        return $error;
    }

    $charsets = getDbCharsets($link);
    if (!isset($charsets[$charset])) {
        $error = sprintf(ERR_INVALID_DBCHARSET, $charset);
    } else if (!empty($collation)) {
        $collations = getDbCollations($link, $charset);
        if (!isset($collations[$collation])) {
            $error = sprintf(ERR_INVALID_DBCOLLATION, $collation);
        }
    }

    return $error;
}

/**
 * @param $name
 * @param $value
 * @param $label
 * @param $help
 * @param $link
 * @param $charset
 *
 * @return string
 */
function xoFormFieldCollation($name, $value, $label, $help, $link, $charset)
{
    if (version_compare(mysql_get_server_info($link), "4.1.0", "lt")) {
        return "";
    }
    if (empty($charset) || !$collations = getDbCollations($link, $charset)) {
        return "";
    }

    $myts = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);

    $field = "<label class='xolabel' for='{$name}'>{$label}</label>\n";
    if ($help) {
        $field .= '<div class="xoform-help">' . $help . "</div>\n";
    }
    $field .= "<select name='{$name}' id='{$name}'\">";

    $collation_default = "";
    $options = "";
    foreach ($collations as $key => $isDefault) {
        if ($isDefault) {
            $collation_default = $key;
            continue;
        }
        $options .= "<option value='{$key}'" . (($value == $key) ? " selected='selected'" : "") . ">{$key}</option>";
    }
    if ($collation_default) {
        $field .= "<option value='{$collation_default}'" . ( ($value == $collation_default || empty($value)) ? " 'selected'" : "" ) . ">{$collation_default} (Default)</option>";
    }
    $field .= $options;
    $field .= "</select>";

    return $field;
}

/**
 * @param $name
 * @param $value
 * @param $label
 * @param $help
 * @param $link
 * @param $charset
 *
 * @return string
 */
function xoFormBlockCollation($name, $value, $label, $help, $link, $charset)
{
    $block = '<div id="' . $name . '_div">';
    $block .= xoFormFieldCollation($name, $value, $label, $help, $link, $charset);
    $block .= '</div>';

    return $block;
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 * @param        $link
 *
 * @return string
 */
function xoFormFieldCharset( $name, $value, $label, $help = '', $link )
{
    if (version_compare(mysql_get_server_info($link), "4.1.0", "lt")) {
        return "";
    }
    if (!$chars = getDbCharsets($link)) {
        return "";
    }

    $charsets = array();
    if (isset($chars["utf8"])) {
        $charsets["utf8"] = $chars["utf8"];
        unset ($chars["utf8"]);
    }
    ksort($chars);
    $charsets = array_merge($charsets, $chars);

    $myts = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);

    $field = "<label class='xolabel' for='{$name}'>{$label}</label>\n";
    if ($help) {
        $field .= '<div class="xoform-help">' . $help . "</div>\n";
    }
    $field .= "<select name='{$name}' id='{$name}' onchange=\"setFormFieldCollation('DB_COLLATION_div', this.value)\">";
    $field .= "<option value=''>None</option>";
    foreach ($charsets as $key => $desc) {
        $field .= "<option value='{$key}'" . (($value == $key) ? " selected='selected'" : "") . ">{$key} - {$desc}</option>";
    }
    $field .= "</select>";

    return $field;
}

/**
 * *#@+
 * Xoops Write Licence System Key
 */
function xoPutLicenseKey($system_key, $licensefile, $license_file_dist = 'license.dist.php')
{
    chmod($licensefile, 0777);
    $fver = fopen($licensefile, 'w');
    $fver_buf = file($license_file_dist);
    foreach ($fver_buf as $line => $value) {
        if (strpos($value, 'XOOPS_LICENSE_KEY') > 0) {
            $ret = 'define(\'XOOPS_LICENSE_KEY\', \'' . $system_key . "');";
        } else {
            $ret = $value;
        }
        fwrite($fver, $ret, strlen($ret));
    }
    fclose($fver);
    chmod($licensefile, 0444);

    return sprintf(WRITTEN_LICENSE, XOOPS_LICENSE_CODE, $system_key);
}

/**
 * *#@+
 * Xoops Build Licence System Key
 */
function xoBuildLicenceKey()
{
    $xoops_serdat = array();
    srand((((float) ('0' . substr(microtime(), strpos(microtime(), ' ') + 1, strlen(microtime()) - strpos(microtime(), ' ') + 1))) * mt_rand(30, 99999)));
    srand((((float) ('0' . substr(microtime(), strpos(microtime(), ' ') + 1, strlen(microtime()) - strpos(microtime(), ' ') + 1))) * mt_rand(30, 99999)));
    $checksums = array(1 => 'md5', 2 => 'sha1');
    $type = rand(1, 2);
    $func = $checksums[$type];

    error_reporting(0);

    // Public Key
    if ($xoops_serdat['version'] = $func(XOOPS_VERSION)) {
        $xoops_serdat['version'] = substr($xoops_serdat['version'],0, 6);
    }
    if ($xoops_serdat['licence'] = $func(XOOPS_LICENSE_CODE)) {
        $xoops_serdat['licence'] = substr($xoops_serdat['licence'],0, 2);
    }
    if ($xoops_serdat['license_text'] = $func(XOOPS_LICENSE_TEXT)) {
        $xoops_serdat['license_text'] = substr($xoops_serdat['license_text'],0, 2);
    }

    if ($xoops_serdat['domain_host'] = $func($_SERVER['HTTP_HOST'])) {
        $xoops_serdat['domain_host'] = substr($xoops_serdat['domain_host'],0, 2);
    }

    // Private Key
    $xoops_serdat['file'] = $func(__FILE__);
    $xoops_serdat['basename'] = $func(basename(__FILE__));
    $xoops_serdat['path'] = $func(dirname(__FILE__));

    foreach ($_SERVER as $key => $data) {
        $xoops_serdat[$key] = substr($func(serialize($data)),0, 4);
    }

    foreach ($xoops_serdat as $key => $data) {
        $xoops_key .= $data;
    }
    while (strlen($xoops_key) > 40) {
        $lpos = rand(18, strlen($xoops_key));
        $xoops_key = substr($xoops_key, 0, $lpos).substr($xoops_key, $lpos + 1 , strlen($xoops_key) - ($lpos + 1));
    }

    return xoStripeKey($xoops_key);
}

/**
 * *#@+
 * Xoops Stripe Licence System Key
 */
function xoStripeKey($xoops_key)
{
    $uu = 0;
    $num = 6;
    $length = 30;
    $strip = floor(strlen($xoops_key) / 6);
    for ($i = 0; $i < strlen($xoops_key); $i++) {
        if ($i < $length) {
            $uu++;
            if ($uu == $strip) {
                $ret .= substr($xoops_key, $i, 1) . '-';
                $uu = 0;
            } else {
                if (substr($xoops_key, $i, 1) != '-') {
                    $ret .= substr($xoops_key, $i, 1);
                } else {
                    $uu--;
                }
            }
        }
    }
    $ret = str_replace('--', '-', $ret);
    if (substr($ret, 0, 1) == '-') {
        $ret = substr($ret, 2, strlen($ret));
    }
    if (substr($ret, strlen($ret) - 1, 1) == '-') {
        $ret = substr($ret, 0, strlen($ret) - 1);
    }

    return $ret;
}
