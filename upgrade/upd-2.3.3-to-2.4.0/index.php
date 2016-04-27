<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Upgrader from 2.3.3 to 2.4.0
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.4.0
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           trabis <lusopoemas@gmail.com>
 * @version          $Id: index.php 13082 2015-06-06 21:59:41Z beckmi $
 */
class Upgrade_240 extends XoopsUpgrade
{
    public $tasks = array('keys', 'version');

    /**
     * @return bool
     */
    public function check_version()
    {
        if (defined('XOOPS_LICENSE_KEY')) {
            return true; // skip setup if license.php was included
        }
        if (defined('XOOPS_LICENSE_KEY') == false) {
            return false;
        } elseif (XOOPS_LICENSE_KEY == '000000-000000-000000-000000-000000') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool|string
     */
    public function apply_version()
    {
        set_time_limit(120);
        chmod('../include/license.php', 0777);
        if (!is_writable('../include/license.php')) {
            echo "<p><span style='text-colour:#ff0000;'>&nbsp;include/license.php - is not writeable</span> - Windows Read Only (Off) / UNIX chmod 0777</p>";

            return false;
        }

        return @$this->xoops_putLicenseKey($this->xoops_buildLicenceKey(), XOOPS_ROOT_PATH . '/include/license.php', __DIR__ . '/license.dist.php');
    }

    /**
     * *#@+
     * Xoops Write Licence System Key
     */
    public function xoops_putLicenseKey($system_key, $licensefile, $license_file_dist = 'license.dist.php')
    {
        chmod($licensefile, 0777);
        $fver     = fopen($licensefile, 'w');
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

        return 'Written License Key: ' . $system_key;
    }

    /**
     * *#@+
     * Xoops Build Licence System Key
     */
    public function xoops_buildLicenceKey()
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

        foreach ($xoops_serdat as $key => $data) {
            $xoops_key .= $data;
        }
        while (strlen($xoops_key) > 40) {
            $lpos      = mt_rand(18, strlen($xoops_key));
            $xoops_key = substr($xoops_key, 0, $lpos) . substr($xoops_key, $lpos + 1, strlen($xoops_key) - ($lpos + 1));
        }

        return $this->xoops_stripeKey($xoops_key);
    }

    /**
     * *#@+
     * Xoops Stripe Licence System Key
     */
    public function xoops_stripeKey($xoops_key)
    {
        $uu     = 0;
        $num    = 6;
        $length = 30;
        $strip  = floor(strlen($xoops_key) / 6);
        for ($i = 0; $i < strlen($xoops_key); ++$i) {
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
     * Check if keys already exist
     *
     */
    public function check_keys()
    {
        $tables['modules']       = array('isactive', 'weight', 'hascomments');
        $tables['users']         = array('level');
        $tables['online']        = array('online_updated', 'online_uid');
        $tables['config']        = array('conf_order');
        $tables['xoopscomments'] = array('com_status');

        foreach ($tables as $table => $keys) {
            $sql = 'SHOW KEYS FROM `' . $GLOBALS['xoopsDB']->prefix($table) . '`';
            if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
                continue;
            }
            $existing_keys = array();
            while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
                $existing_keys[] = $row['Key_name'];
            }
            foreach ($keys as $key) {
                if (!in_array($key, $existing_keys)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Apply keys that are missing
     *
     */
    public function apply_keys()
    {
        $tables['modules']       = array('isactive', 'weight', 'hascomments');
        $tables['users']         = array('level');
        $tables['online']        = array('online_updated', 'online_uid');
        $tables['config']        = array('conf_order');
        $tables['xoopscomments'] = array('com_status');

        foreach ($tables as $table => $keys) {
            $sql = 'SHOW KEYS FROM `' . $GLOBALS['xoopsDB']->prefix($table) . '`';
            if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
                continue;
            }
            $existing_keys = array();
            while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
                $existing_keys[] = $row['Key_name'];
            }
            foreach ($keys as $key) {
                if (!in_array($key, $existing_keys)) {
                    $sql = 'ALTER TABLE `' . $GLOBALS['xoopsDB']->prefix($table) . "` ADD INDEX `{$key}` (`{$key}`)";
                    if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function __construct()
    {
        parent::__construct(basename(__DIR__));
    }
}

$upg = new Upgrade_240();
return $upg;
