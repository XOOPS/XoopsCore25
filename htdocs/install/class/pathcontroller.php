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
 **/
class PathStuffController
{
    public $xoopsPath = array(
        'root' => '',
        'lib'  => '',
        'data' => '');

    public $xoopsPathDefault = array(
        'lib'  => 'xoops_lib',
        'data' => 'xoops_data');

    public $dataPath = array(
        'caches' => array(
            'xoops_cache',
            'smarty_cache',
            'smarty_compile'),
        'configs');

    public $path_lookup = array(
        'root' => 'ROOT_PATH',
        'data' => 'VAR_PATH',
        'lib'  => 'PATH');

    public $xoopsUrl = '';

    public $validPath = array(
        'root' => 0,
        'data' => 0,
        'lib'  => 0);

    public $validUrl = false;

    public $permErrors = array(
        'root' => null,
        'data' => null);

    /**
     * @param $xoopsPathDefault
     * @param $dataPath
     */
    public function __construct($xoopsPathDefault, $dataPath)
    {
        $this->xoopsPathDefault = $xoopsPathDefault;
        $this->dataPath         = $dataPath;

        if (isset($_SESSION['settings']['ROOT_PATH'])) {
            foreach ($this->path_lookup as $req => $sess) {
                $this->xoopsPath[$req] = $_SESSION['settings'][$sess];
            }
        } else {
            $path = str_replace("\\", '/', realpath('../'));
            if (substr($path, -1) === '/') {
                $path = substr($path, 0, -1);
            }
            if (file_exists("$path/mainfile.dist.php")) {
                $this->xoopsPath['root'] = $path;
            }
            // Firstly, locate XOOPS lib folder out of XOOPS root folder
            $this->xoopsPath['lib'] = dirname($path) . '/' . $this->xoopsPathDefault['lib'];
            // If the folder is not created, re-locate XOOPS lib folder inside XOOPS root folder
            if (!is_dir($this->xoopsPath['lib'] . '/')) {
                $this->xoopsPath['lib'] = $path . '/' . $this->xoopsPathDefault['lib'];
            }
            // Firstly, locate XOOPS data folder out of XOOPS root folder
            $this->xoopsPath['data'] = dirname($path) . '/' . $this->xoopsPathDefault['data'];
            // If the folder is not created, re-locate XOOPS data folder inside XOOPS root folder
            if (!is_dir($this->xoopsPath['data'] . '/')) {
                $this->xoopsPath['data'] = $path . '/' . $this->xoopsPathDefault['data'];
            }
        }
        if (isset($_SESSION['settings']['URL'])) {
            $this->xoopsUrl = $_SESSION['settings']['URL'];
        } else {
            $path           = $GLOBALS['wizard']->baseLocation();
            $this->xoopsUrl = substr($path, 0, strrpos($path, '/'));
        }
    }

    public function execute()
    {
        $this->readRequest();
        $valid = $this->validate();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($this->path_lookup as $req => $sess) {
                $_SESSION['settings'][$sess] = $this->xoopsPath[$req];
            }
            $_SESSION['settings']['URL'] = $this->xoopsUrl;
            if ($valid) {
                $GLOBALS['wizard']->redirectToPage('+1');
            } else {
                $GLOBALS['wizard']->redirectToPage('+0');
            }
        }
    }

    public function readRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = $_POST;
            foreach ($this->path_lookup as $req => $sess) {
                if (isset($request[$req])) {
                    $request[$req] = str_replace("\\", '/', trim($request[$req]));
                    if (substr($request[$req], -1) === '/') {
                        $request[$req] = substr($request[$req], 0, -1);
                    }
                    $this->xoopsPath[$req] = $request[$req];
                }
            }
            if (isset($request['URL'])) {
                $request['URL'] = trim($request['URL']);
                if (substr($request['URL'], -1) === '/') {
                    $request['URL'] = substr($request['URL'], 0, -1);
                }
                $this->xoopsUrl = $request['URL'];
            }
        }
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach (array_keys($this->xoopsPath) as $path) {
            if ($this->checkPath($path)) {
                $this->checkPermissions($path);
            }
        }
        $this->validUrl = !empty($this->xoopsUrl);
        $validPaths     = (array_sum(array_values($this->validPath)) == count(array_keys($this->validPath))) ? 1 : 0;
        $validPerms     = true;
        foreach ($this->permErrors as $key => $errs) {
            if (empty($errs)) {
                continue;
            }
            foreach ($errs as $path => $status) {
                if (empty($status)) {
                    $validPerms = false;
                    break;
                }
            }
        }

        return ($validPaths && $this->validUrl && $validPerms);
    }

    /**
     * @param string $PATH
     *
     * @return int
     */
    public function checkPath($PATH = '')
    {
        $ret = 1;
        if ($PATH === 'root' || empty($PATH)) {
            $path = 'root';
            if (is_dir($this->xoopsPath[$path]) && is_readable($this->xoopsPath[$path])) {
                @include_once "{$this->xoopsPath[$path]}/include/version.php";
                if (file_exists("{$this->xoopsPath[$path]}/mainfile.dist.php") && defined('XOOPS_VERSION')) {
                    $this->validPath[$path] = 1;
                }
            }
            $ret *= $this->validPath[$path];
        }
        if ($PATH === 'lib' || empty($PATH)) {
            $path = 'lib';
            if (is_dir($this->xoopsPath[$path]) && is_readable($this->xoopsPath[$path])) {
                $this->validPath[$path] = 1;
            }
            $ret *= $this->validPath[$path];
        }
        if ($PATH === 'data' || empty($PATH)) {
            $path = 'data';
            if (is_dir($this->xoopsPath[$path]) && is_readable($this->xoopsPath[$path])) {
                $this->validPath[$path] = 1;
            }
            $ret *= $this->validPath[$path];
        }

        return $ret;
    }

    /**
     * @param $parent
     * @param $path
     * @param $error
     * @return null
     */
    public function setPermission($parent, $path, &$error)
    {
        if (is_array($path)) {
            foreach (array_keys($path) as $item) {
                if (is_string($item)) {
                    $error[$parent . '/' . $item] = $this->makeWritable($parent . '/' . $item);
                    if (empty($path[$item])) {
                        continue;
                    }
                    foreach ($path[$item] as $child) {
                        $this->setPermission($parent . '/' . $item, $child, $error);
                    }
                } else {
                    $error[$parent . '/' . $path[$item]] = $this->makeWritable($parent . '/' . $path[$item]);
                }
            }
        } else {
            $error[$parent . '/' . $path] = $this->makeWritable($parent . '/' . $path);
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function checkPermissions($path)
    {
        $paths  = array(
            'root' => array('mainfile.php', 'uploads', /*'templates_c', 'cache'*/),
            'data' => $this->dataPath);
        $errors = array(
            'root' => null,
            'data' => null);

        if (!isset($this->xoopsPath[$path])) {
            return false;
        }
        if (!isset($errors[$path])) {
            return true;
        }
        $this->setPermission($this->xoopsPath[$path], $paths[$path], $errors[$path]);
        if (in_array(false, $errors[$path])) {
            $this->permErrors[$path] = $errors[$path];
        }

        return true;
    }

    /**
     * Write-enable the specified folder
     *
     * @param string $path
     * @param bool   $create
     *
     * @internal param bool $recurse
     * @return false on failure, method (u-ser,g-roup,w-orld) on success
     */
    public function makeWritable($path, $create = true)
    {
        $mode = intval('0777', 8);
        if (!file_exists($path)) {
            if (!$create) {
                return false;
            } else {
                mkdir($path, $mode);
            }
        }
        if (!is_writable($path)) {
            chmod($path, $mode);
        }
        clearstatcache();
        if (is_writable($path)) {
            $info = stat($path);
            if ($info['mode'] & 0002) {
                return 'w';
            } elseif ($info['mode'] & 0020) {
                return 'g';
            }

            return 'u';
        }

        return false;
    }
}
