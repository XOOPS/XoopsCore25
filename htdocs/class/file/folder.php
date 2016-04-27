<?php
/**
 * Folder engine For XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2005-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          file
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

/**
 * Convenience class for handling directories.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                     1785 E. Sahara Avenue, Suite 490-204
 *                                     Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright  Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link       http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package    cake
 * @subpackage cake.cake.libs
 * @since      CakePHP(tm) v 0.2.9
 * @modifiedby $LastChangedBy: beckmi $
 * @lastmodified $Date: 2015-06-06 17:59:41 -0400 (Sat, 06 Jun 2015) $
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Folder structure browser, lists folders and files.
 *
 * Long description for class
 *
 * @package    cake
 * @subpackage cake.cake.libs
 */
class XoopsFolderHandler
{
    /**
     * Path to Folder.
     *
     * @var string
     * @access public
     */
    public $path;

    /**
     * Sortedness.
     *
     * @var boolean
     * @access public
     */
    public $sort = false;

    /**
     * mode to be used on create.
     *
     * @var boolean
     * @access public
     */
    public $mode = '0755';

    /**
     * holds messages from last method.
     *
     * @var array
     * @access private
     */
    public $messages = array();

    /**
     * holds errors from last method.
     *
     * @var array
     * @access private
     */
    public $errors = false;

    /**
     * holds array of complete directory paths.
     *
     * @var array
     * @access private
     */
    public $directories;

    /**
     * holds array of complete file paths.
     *
     * @var array
     * @access private
     */
    public $files;

    /**
     * Constructor.
     *
     * @param bool|string $path   Path to folder
     * @param boolean     $create Create folder if not found
     * @param mixed       $mode   Mode (CHMOD) to apply to created folder, false to ignore
     */
    public function __construct($path = false, $create = true, $mode = false)
    {
        if (empty($path)) {
            $path = XOOPS_VAR_PATH . '/caches/xoops_cache';
        }
        if ($mode) {
            $this->mode = intval($mode, 8);
        }
        if (!file_exists($path) && $create == true) {
            $this->create($path, $this->mode);
        }
        if (!$this->isAbsolute($path)) {
            $path = realpath($path);
        }
        $this->cd($path);
    }

    /**
     * Return current path.
     *
     * @return string Current path
     * @access public
     */
    public function pwd()
    {
        return $this->path;
    }

    /**
     * Change directory to $desired_path.
     *
     * @param string $path Path to the directory to change to
     *
     * @return string The new path. Returns false on failure
     * @access   public
     */
    public function cd($path)
    {
        $path = $this->realpath($path);
        if (is_dir($path) && file_exists($path)) {
            return $this->path = $path;
        }

        return false;
    }

    /**
     * Returns an array of the contents of the current directory, or false on failure.
     * The returned array holds two arrays: one of dirs and one of files.
     *
     * @param boolean $sort
     * @param mixed   $exceptions either an array or boolean true will no grab dot files
     *
     * @return mixed Contents of current directory as an array, false on failure
     * @access public
     */
    public function read($sort = true, $exceptions = false)
    {
        $dirs = $files = array();
        $dir  = opendir($this->path);
        if ($dir !== false) {
            while (false !== ($n = readdir($dir))) {
                $item = false;
                if (is_array($exceptions)) {
                    if (!in_array($n, $exceptions)) {
                        $item = $n;
                    }
                } else {
                    if ((!preg_match('/^\\.+$/', $n) && $exceptions == false) || ($exceptions == true && !preg_match('/^\\.(.*)$/', $n))) {
                        $item = $n;
                    }
                }
                if ($item !== false) {
                    if (is_dir($this->addPathElement($this->path, $item))) {
                        $dirs[] = $item;
                    } else {
                        $files[] = $item;
                    }
                }
            }
            if ($sort || $this->sort) {
                sort($dirs);
                sort($files);
            }
            closedir($dir);
        }

        return array(
            $dirs,
            $files);
    }

    /**
     * Returns an array of all matching files in current directory.
     *
     * @param string $regexp_pattern Preg_match pattern (Defaults to: .*)
     * @param bool   $sort
     *
     * @return array Files that match given pattern
     * @access   public
     */
    public function find($regexp_pattern = '.*', $sort = false)
    {
        $data = $this->read($sort);
        if (!is_array($data)) {
            return array();
        }
        list($dirs, $files) = $data;
        $found = array();
        foreach ($files as $file) {
            if (preg_match("/^{$regexp_pattern}$/i", $file)) {
                $found[] = $file;
            }
        }

        return $found;
    }

    /**
     * Returns an array of all matching files in and below current directory.
     *
     * @param string $pattern Preg_match pattern (Defaults to: .*)
     * @param bool   $sort
     *
     * @return array Files matching $pattern
     * @access public
     */
    public function findRecursive($pattern = '.*', $sort = false)
    {
        $startsOn = $this->path;
        $out      = $this->_findRecursive($pattern, $sort);
        $this->cd($startsOn);

        return $out;
    }

    /**
     * Private helper function for findRecursive.
     *
     * @param string $pattern Pattern to match against
     * @param bool   $sort
     *
     * @return array Files matching pattern
     * @access private
     */
    public function _findRecursive($pattern, $sort = false)
    {
        list($dirs, $files) = $this->read($sort);
        $found = array();
        foreach ($files as $file) {
            if (preg_match("/^{$pattern}$/i", $file)) {
                $found[] = $this->addPathElement($this->path, $file);
            }
        }
        $start = $this->path;
        foreach ($dirs as $dir) {
            $this->cd($this->addPathElement($start, $dir));
            $found = array_merge($found, $this->findRecursive($pattern));
        }

        return $found;
    }

    /**
     * Returns true if given $path is a Windows path.
     *
     * @param string $path Path to check
     *
     * @return boolean true if windows path, false otherwise
     * @access public
     * @static
     */
    public function isWindowsPath($path)
    {
        if (preg_match('/^[A-Z]:\\\\/i', $path)) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if given $path is an absolute path.
     *
     * @param string $path Path to check
     *
     * @return bool
     * @access public
     * @static
     */
    public function isAbsolute($path)
    {
        $match = preg_match('/^\\//', $path) || preg_match('/^[A-Z]:\\//i', $path);

        return $match;
    }

    /**
     * Returns a correct set of slashes for given $path. (\\ for Windows paths and / for other paths.)
     *
     * @param string $path Path to check
     *
     * @return string Set of slashes ("\\" or "/")
     * @access public
     * @static
     */
    public function normalizePath($path)
    {
        if (XoopsFolderHandler::isWindowsPath($path)) {
            return '\\';
        }

        return '/';
    }

    /**
     * Returns a correct set of slashes for given $path. (\\ for Windows paths and / for other paths.)
     *
     * @param string $path Path to check
     *
     * @return string Set of slashes ("\\" or "/")
     * @access public
     * @static
     */
    public function correctSlashFor($path)
    {
        if (XoopsFolderHandler::isWindowsPath($path)) {
            return '\\';
        }

        return '/';
    }

    /**
     * Returns $path with added terminating slash (corrected for Windows or other OS).
     *
     * @param string $path Path to check
     *
     * @return string Path with ending slash
     * @access public
     * @static
     */
    public function slashTerm($path)
    {
        if (XoopsFolderHandler::isSlashTerm($path)) {
            return $path;
        }

        return $path . XoopsFolderHandler::correctSlashFor($path);
    }

    /**
     * Returns $path with $element added, with correct slash in-between.
     *
     * @param string $path    Path
     * @param string $element Element to and at end of path
     *
     * @return string Combined path
     * @access public
     * @static
     */
    public function addPathElement($path, $element)
    {
        return $this->slashTerm($path) . $element;
    }

    /**
     * Returns true if the File is in a given XoopsPath.
     *
     * @param string $path
     *
     * @return bool
     * @access public
     */
    public function inXoopsPath($path = '')
    {
        $dir    = substr($this->slashTerm(XOOPS_ROOT_PATH), 0, -1);
        $newdir = $dir . $path;

        return $this->inPath($newdir);
    }

    /**
     * Returns true if the File is in given path.
     *
     * @param string $path
     * @param bool   $reverse
     *
     * @return bool
     * @access public
     */
    public function inPath($path = '', $reverse = false)
    {
        $dir     = $this->slashTerm($path);
        $current = $this->slashTerm($this->pwd());
        if (!$reverse) {
            $return = preg_match('/^(.*)' . preg_quote($dir, '/') . '(.*)/', $current);
        } else {
            $return = preg_match('/^(.*)' . preg_quote($current, '/') . '(.*)/', $dir);
        }
        if ($return == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Change the mode on a directory structure recursively.
     *
     * @param string   $path       The path to chmod
     * @param bool|int $mode       octal value 0755
     * @param boolean  $recursive  chmod recursively
     * @param array    $exceptions array of files, directories to skip
     *
     * @return boolean Returns TRUE on success, FALSE on failure
     * @access public
     */
    public function chmod($path, $mode = false, $recursive = true, $exceptions = array())
    {
        if (!$mode) {
            $mode = $this->mode;
        }
        if ($recursive === false && is_dir($path)) {
            if (chmod($path, intval($mode, 8))) {
                $this->messages[] = sprintf('%s changed to %s', $path, $mode);

                return true;
            } else {
                $this->errors[] = sprintf('%s NOT changed to %s', $path, $mode);

                return false;
            }
        }
        if (is_dir($path)) {
            list($paths) = $this->tree($path);
            foreach ($paths as $key => $fullpath) {
                $check = explode('/', $fullpath);
                $count = count($check);

                if (in_array($check[$count - 1], $exceptions)) {
                    continue;
                }

                if (chmod($fullpath, intval($mode, 8))) {
                    $this->messages[] = sprintf('%s changed to %s', $fullpath, $mode);
                } else {
                    $this->errors[] = sprintf('%s NOT changed to %s', $fullpath, $mode);
                }
            }
            if (empty($this->errors)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of nested directories and files in each directory
     *
     * @param string  $path   the directory path to build the tree from
     * @param boolean $hidden return hidden files and directories
     * @param string  $type   either file or dir. null returns both files and directories
     *
     * @return mixed array of nested directories and files in each directory
     * @access public
     */
    public function tree($path, $hidden = true, $type = null)
    {
        $path              = rtrim($path, '/');
        $this->files       = array();
        $this->directories = array(
            $path);
        $directories       = array();
        while (count($this->directories)) {
            $dir = array_pop($this->directories);
            $this->_tree($dir, $hidden);
            $directories[] =  $dir;
        }
        if ($type === null) {
            return array(
                $directories,
                $this->files);
        }
        if ($type === 'dir') {
            return $directories;
        }

        return $this->files;
    }

    /**
     * Private method to list directories and files in each directory
     *
     * @param string $path
     * @param        $hidden
     *
     * @internal param $ $ = boolean $hidden
     * @access   private
     */
    public function _tree($path, $hidden)
    {
        if (is_dir($path)) {
            $dirHandle = opendir($path);
            while (false !== ($item = readdir($dirHandle))) {
                $found = false;
                if (($hidden === true && $item !== '.' && $item !== '..') || ($hidden === false && !preg_match('/^\\.(.*)$/', $item))) {
                    $found = $path . '/' . $item;
                }
                if ($found !== false) {
                    if (is_dir($found)) {
                        $this->directories[] =  $found;
                    } else {
                        $this->files[] =  $found;
                    }
                }
            }
            closedir($dirHandle);
        }
    }

    /**
     * Create a directory structure recursively.
     *
     * @param string   $pathname The directory structure to create
     * @param bool|int $mode     octal value 0755
     *
     * @return boolean Returns TRUE on success, FALSE on failure
     * @access public
     */
    public function create($pathname, $mode = false)
    {
        if (is_dir($pathname) || empty($pathname)) {
            return true;
        }
        if (!$mode) {
            $mode = $this->mode;
        }
        if (is_file($pathname)) {
            $this->errors[] = sprintf('%s is a file', $pathname);

            return true;
        }
        $nextPathname = substr($pathname, 0, strrpos($pathname, '/'));
        if ($this->create($nextPathname, $mode)) {
            if (!file_exists($pathname)) {
                if (mkdir($pathname, intval($mode, 8))) {
                    $this->messages[] = sprintf('%s created', $pathname);

                    return true;
                } else {
                    $this->errors[] = sprintf('%s NOT created', $pathname);

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns the size in bytes of this Folder.
     *
     * @return int $size
     * @access   public
     */
    public function dirsize()
    {
        $size      = 0;
        $directory = $this->slashTerm($this->path);
        $stack     = array($directory);
        $count     = count($stack);
        for ($i = 0, $j = $count; $i < $j; ++$i) {
            if (is_file($stack[$i])) {
                $size += filesize($stack[$i]);
            } else {
                if (is_dir($stack[$i])) {
                    $dir = dir($stack[$i]);
                    if ($dir) {
                        while (false !== ($entry = $dir->read())) {
                            if ($entry === '.' || $entry === '..') {
                                continue;
                            }
                            $add = $stack[$i] . $entry;
                            if (is_dir($stack[$i] . $entry)) {
                                $add = $this->slashTerm($add);
                            }
                            $stack[] = $add;
                        }
                        $dir->close();
                    }
                }
            }
            $j = count($stack);
        }

        return $size;
    }

    /**
     * Recursively Remove directories if system allow.
     *
     * @param string $path Path of directory to delete
     *
     * @return boolean Success
     * @access public
     */
    public function delete($path)
    {
        $path = $this->slashTerm($path);
        if (is_dir($path) === true) {
            $files        = glob($path . '*', GLOB_NOSORT);
            $normal_files = glob($path . '*');
            $hidden_files = glob($path . '\.?*');
            $files        = array_merge($normal_files, $hidden_files);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (preg_match("/(\.|\.\.)$/", $file)) {
                        continue;
                    }
                    if (is_file($file) === true) {
                        if (unlink($file)) {
                            $this->messages[] = sprintf('%s removed', $path);
                        } else {
                            $this->errors[] = sprintf('%s NOT removed', $path);
                        }
                    } else {
                        if (is_dir($file) === true) {
                            if ($this->delete($file) === false) {
                                return false;
                            }
                        }
                    }
                }
            }
            $path = substr($path, 0, strlen($path) - 1);
            if (rmdir($path) === false) {
                $this->errors[] = sprintf('%s NOT removed', $path);

                return false;
            } else {
                $this->messages[] = sprintf('%s removed', $path);
            }
        }

        return true;
    }

    /**
     * Recursive directory copy.
     *
     * @param array $options (to, from, chmod, skip)
     *
     * @return bool
     * @access public
     */
    public function copy($options = array())
    {
        $to = null;
        if (is_string($options)) {
            $to      = $options;
            $options = array();
        }
        $options = array_merge(array(
                                   'to'   => $to,
                                   'from' => $this->path,
                                   'mode' => $this->mode,
                                   'skip' => array()), $options);

        $fromDir = $options['from'];
        $toDir   = $options['to'];
        $mode    = $options['mode'];
        if (!$this->cd($fromDir)) {
            $this->errors[] = sprintf('%s not found', $fromDir);

            return false;
        }
        if (!is_dir($toDir)) {
            mkdir($toDir, $mode);
        }
        if (!is_writable($toDir)) {
            $this->errors[] = sprintf('%s not writable', $toDir);

            return false;
        }
        $exceptions = array_merge(array(
                                      '.',
                                      '..',
                                      '.svn'), $options['skip']);
        $handle     = opendir($fromDir);
        if ($handle) {
            while (false !== ($item = readdir($handle))) {
                if (!in_array($item, $exceptions)) {
                    $from = $this->addPathElement($fromDir, $item);
                    $to   = $this->addPathElement($toDir, $item);
                    if (is_file($from)) {
                        if (copy($from, $to)) {
                            chmod($to, intval($mode, 8));
                            touch($to, filemtime($from));
                            $this->messages[] = sprintf('%s copied to %s', $from, $to);
                        } else {
                            $this->errors[] = sprintf('%s NOT copied to %s', $from, $to);
                        }
                    }
                    if (is_dir($from) && !file_exists($to)) {
                        if (mkdir($to, intval($mode, 8))) {
                            chmod($to, intval($mode, 8));
                            $this->messages[] = sprintf('%s created', $to);
                            $options          = array_merge($options, array(
                                                                        'to'   => $to,
                                                                        'from' => $from));
                            $this->copy($options);
                        } else {
                            $this->errors[] = sprintf('%s not created', $to);
                        }
                    }
                }
            }
            closedir($handle);
        } else {
            return false;
        }
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * Recursive directory move.
     *
     * @param array $options (to, from, chmod, skip)
     *
     * @return boolean Success
     * @access public
     */
    public function move($options)
    {
        $to = null;
        if (is_string($options)) {
            $to      = $options;
            $options = (array)$options;
        }
        $options = array_merge(array(
                                   'to'   => $to,
                                   'from' => $this->path,
                                   'mode' => $this->mode,
                                   'skip' => array()), $options);
        if ($this->copy($options)) {
            if ($this->delete($options['from'])) {
                return $this->cd($options['to']);
            }
        }

        return false;
    }

    /**
     * get messages from latest method
     *
     * @return array
     * @access public
     */
    public function messages()
    {
        return $this->messages;
    }

    /**
     * get error from latest method
     *
     * @return array
     * @access public
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Get the real path (taking ".." and such into account)
     *
     * @param string $path Path to resolve
     *
     * @return string The resolved path
     */
    public function realpath($path)
    {
        $path = trim($path);
        if (strpos($path, '..') === false) {
            if (!$this->isAbsolute($path)) {
                $path = $this->addPathElement($this->path, $path);
            }

            return $path;
        }
        $parts    = explode('/', $path);
        $newparts = array();
        $newpath  = $path{0} === '/' ? '/' : '';
        while (($part = array_shift($parts)) !== null) {
            if ($part === '.' || $part == '') {
                continue;
            }
            if ($part === '..') {
                if (count($newparts) > 0) {
                    array_pop($newparts);
                    continue;
                } else {
                    return false;
                }
            }
            $newparts[] = $part;
        }
        $newpath .= implode('/', $newparts);
        if (strlen($path > 1) && $path{strlen($path) - 1} === '/') {
            $newpath .= '/';
        }

        return $newpath;
    }

    /**
     * Returns true if given $path ends in a slash (i.e. is slash-terminated).
     *
     * @param string $path Path to check
     *
     * @return boolean true if path ends with slash, false otherwise
     * @access public
     * @static
     */
    public function isSlashTerm($path)
    {
        if (preg_match('/[\/\\\]$/', $path)) {
            return true;
        }

        return false;
    }
}
