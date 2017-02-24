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
 * @param string $hash
 * @return bool
 */

function install_acceptUser($hash = '')
{
    $GLOBALS['xoopsUser'] = null;
    $assertClaims = array(
        'sub' => 'xoopsinstall',
    );
    $claims = \Xmf\Jwt\TokenReader::fromCookie('install', 'xo_install_user', $assertClaims);
    if (false === $claims || empty($claims->uname)) {
        return false;
    }
    $uname = $claims->uname;
    /* @var $memberHandler XoopsMemberHandler */
    $memberHandler = xoops_getHandler('member');
    $user = array_pop($memberHandler->getUsers(new Criteria('uname', $uname)));

    if (is_object($GLOBALS['xoops']) && method_exists($GLOBALS['xoops'], 'acceptUser')) {
        $res = $GLOBALS['xoops']->acceptUser($uname, true, '');

        return $res;
    }

    $GLOBALS['xoopsUser']        = $user;
    $_SESSION['xoopsUserId']     = $GLOBALS['xoopsUser']->getVar('uid');
    $_SESSION['xoopsUserGroups'] = $GLOBALS['xoopsUser']->getGroups();

    return true;
}

/**
 * @param $installer_modified
 */
function install_finalize($installer_modified)
{
    // Set mainfile.php readonly
    @chmod(XOOPS_ROOT_PATH . '/mainfile.php', 0444);
    // Set Secure file readonly
    @chmod(XOOPS_VAR_PATH . '/data/secure.php', 0444);
    // Rename installer folder
    @rename(XOOPS_ROOT_PATH . '/install', XOOPS_ROOT_PATH . '/' . $installer_modified);
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 */
function xoFormField($name, $value, $label, $help = '')
{
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo '<div class="form-group">';
    echo '<label class="xolabel" for="' . $name . '">' . $label . '</label>';
    if ($help) {
        echo '<div class="xoform-help alert alert-info">' . $help . '</div>';
    }
    echo '<input type="text" class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
    echo '</div>';
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 */
function xoPassField($name, $value, $label, $help = '')
{
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo '<div class="form-group">';
    echo '<label class="xolabel" for="' . $name . '">' . $label . '</label>';
    if ($help) {
        echo '<div class="xoform-help alert alert-info">' . $help . '</div>';
    }
    if ($name === 'adminpass') {
        echo '<input type="password" class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'"  onkeyup="passwordStrength(this.value)">';
    } else {
        echo '<input type="password" class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
    }
    echo '</div>';
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param array  $options
 * @param string $help
 * @param        $extra
 */
function xoFormSelect($name, $value, $label, $options, $help = '', $extra='')
{
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    echo '<div class="form-group">';
    echo '<label class="xolabel" for="' . $name . '">' . $label . '</label>';
    if ($help) {
        echo '<div class="xoform-help alert alert-info">' . $help . '</div>';
    }
    echo '<select class="form-control" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$extra.'>';
    foreach ($options as $optionValue => $optionReadable) {
        $selected = ($value === $optionValue) ? ' selected' : '';
        echo '<option value="'.$optionValue . '"' . $selected . '>' . $optionReadable . '</option>';
    }
    echo '</select>';
    echo '</div>';
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
            if ($file{0} !== '.' && is_dir($dirname . $file)) {
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
    $classes = array(-1 => 'fa fa-fw fa-ban text-danger', 0 => 'fa fa-fw fa-square-o text-warning', 1 => 'fa fa-fw fa-check text-success');
    $strings = array(-1 => FAILED, 0 => WARNING, 1 => SUCCESS);
    if (empty($str)) {
        $str = $strings[$status];
    }

    return '<span class="' . $classes[$status] . '"></span>' . $str;
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
    $setting = (bool) ini_get($name);
    if ($setting === (bool) $wanted) {
        return xoDiag(1, $setting ? 'ON' : 'OFF');
    } else {
        return xoDiag($severe ? -1 : 0, $setting ? 'ON' : 'OFF');
    }
}

/**
 * seems to only be used for license file?
 * @param string $path dir or file path
 *
 * @return string
 */
function xoDiagIfWritable($path)
{
    $path  = '../' . $path;
    $error = true;
    if (!is_dir($path)) {
        if (file_exists($path) && !is_writable($path)) {
            @chmod($path, 0664);
            $error = !is_writable($path);
        }
    } else {
        if (!is_writable($path)) {
            @chmod($path, 0775);
            $error = !is_writable($path);
        }
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
    //} elseif (version_compare(phpversion(), '5.3.7', '>=')) {
    //    return xoDiag(0, phpversion());
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

        return '<span class="pathmessage"><span class="fa fa-fw fa-check text-success"></span> ' . $msg . '</span>';
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
        $GLOBALS['error'] = true;
        return '<div class="alert alert-danger"><span class="fa fa-fw fa-ban text-danger"></span> ' . $msg . '</div>';
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
    if ($charsets) {
        return $charsets;
    }

    if ($result = mysqli_query($link, 'SHOW CHARSET')) {
        while ($row = mysqli_fetch_assoc($result)) {
            $charsets[$row['Charset']] = $row['Description'];
        }
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

    if ($result = mysqli_query($link, "SHOW COLLATION WHERE CHARSET = '" . mysqli_real_escape_string($link, $charset) . "'")) {
        while ($row = mysqli_fetch_assoc($result)) {
            $collations[$charset][$row['Collation']] = $row['Default'] ? 1 : 0;
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
        $collation = '';
    }
    if (empty($charset) && empty($collation)) {
        return $error;
    }

    $charsets = getDbCharsets($link);
    if (!isset($charsets[$charset])) {
        $error = sprintf(ERR_INVALID_DBCHARSET, $charset);
    } elseif (!empty($collation)) {
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
    if (empty($charset) || !$collations = getDbCollations($link, $charset)) {
        return '';
    }

    $options           = array();
    foreach ($collations as $key => $isDefault) {
        $options[$key] = $key . (($isDefault) ? ' (Default)' : '');
    }

    return xoFormSelect($name, $value, $label, $options, $help);
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
    return xoFormFieldCollation($name, $value, $label, $help, $link, $charset);
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
function xoFormFieldCharset($name, $value, $label, $help = '', $link)
{
    if (!$charsets = getDbCharsets($link)) {
        return '';
    }
    foreach ($charsets as $k => $v) {
        $charsets[$k] = $v . ' (' . $k . ')';
    }
    asort($charsets);
    $myts  = MyTextSanitizer::getInstance();
    $label = $myts->htmlspecialchars($label, ENT_QUOTES, _INSTALL_CHARSET, false);
    $name  = $myts->htmlspecialchars($name, ENT_QUOTES, _INSTALL_CHARSET, false);
    $value = $myts->htmlspecialchars($value, ENT_QUOTES);
    $extra = 'onchange="setFormFieldCollation(\'DB_COLLATION\', this.value)"';
    return xoFormSelect($name, $value, $label, $charsets, $help, $extra);
}

/**
 * *#@+
 * Xoops Write Licence System Key
 * @param        $system_key
 * @param        $licensefile
 * @param string $license_file_dist
 * @return string
 */
function xoPutLicenseKey($system_key, $licensefile, $license_file_dist = 'license.dist.php')
{
    //chmod($licensefile, 0777);
    $fver     = fopen($licensefile, 'w');
    $fver_buf = file($license_file_dist);
    foreach ($fver_buf as $line => $value) {
        $ret = $value;
        if (strpos($value, 'XOOPS_LICENSE_KEY') > 0) {
            $ret = 'define(\'XOOPS_LICENSE_KEY\', \'' . $system_key . "');";
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
    mt_srand(((float)('0' . substr(microtime(), strpos(microtime(), ' ') + 1, strlen(microtime()) - strpos(microtime(), ' ') + 1))) * mt_rand(30, 99999));
    mt_srand(((float)('0' . substr(microtime(), strpos(microtime(), ' ') + 1, strlen(microtime()) - strpos(microtime(), ' ') + 1))) * mt_rand(30, 99999));
    $checksums = array(1 => 'md5', 2 => 'sha1');
    $type      = mt_rand(1, 2);
    $func      = $checksums[$type];

    error_reporting(0);

    // Public Key
    if ($xoops_serdat['version'] = $func(XOOPS_VERSION)) {
        $xoops_serdat['version'] = substr($xoops_serdat['version'], 0, 6);
    }
    if ($xoops_serdat['licence'] = $func(XOOPS_LICENSE_CODE)) {
        $xoops_serdat['licence'] = substr($xoops_serdat['licence'], 0, 2);
    }
    if ($xoops_serdat['license_text'] = $func(XOOPS_LICENSE_TEXT)) {
        $xoops_serdat['license_text'] = substr($xoops_serdat['license_text'], 0, 2);
    }

    if ($xoops_serdat['domain_host'] = $func($_SERVER['HTTP_HOST'])) {
        $xoops_serdat['domain_host'] = substr($xoops_serdat['domain_host'], 0, 2);
    }

    // Private Key
    $xoops_serdat['file']     = $func(__FILE__);
    $xoops_serdat['basename'] = $func(basename(__FILE__));
    $xoops_serdat['path']     = $func(__DIR__);

    foreach ($_SERVER as $key => $data) {
        $xoops_serdat[$key] = substr($func(serialize($data)), 0, 4);
    }

    $xoops_key = '';
    foreach ($xoops_serdat as $key => $data) {
        $xoops_key .= $data;
    }
    while (strlen($xoops_key) > 40) {
        $lpos      = mt_rand(18, strlen($xoops_key));
        $xoops_key = substr($xoops_key, 0, $lpos) . substr($xoops_key, $lpos + 1, strlen($xoops_key) - ($lpos + 1));
    }

    return xoStripeKey($xoops_key);
}

/**
 * *#@+
 * Xoops Stripe Licence System Key
 * @param $xoops_key
 * @return mixed|string
 */
function xoStripeKey($xoops_key)
{
    $uu     = 0;
    $num    = 6;
    $length = 30;
    $strip  = floor(strlen($xoops_key) / 6);
    $strlen = strlen($xoops_key);
    $ret = '';
    for ($i = 0; $i < $strlen; ++$i) {
        if ($i < $length) {
            ++$uu;
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


/**
 * @return string
 */
function writeLicenseKey()
{
    return xoPutLicenseKey(xoBuildLicenceKey(), XOOPS_VAR_PATH . '/data/license.php', __DIR__ . '/license.dist.php');
}
