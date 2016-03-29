<?php
/**
 * File engine For XOOPS
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
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Convenience class for reading, writing and appending to files.
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
 * Convenience class for reading, writing and appending to files.
 *
 * @package    cake
 * @subpackage cake.cake.libs
 */
class XoopsFileHandler
{
    /**
     * folder object of the File
     *
     * @var object
     * @access public
     */
    public $folder;

    /**
     * Filename
     *
     * @var string
     * @access public
     */
    public $name;

    /**
     * file info
     *
     * @var string
     * @access public
     */
    public $info = array();

    /**
     * Holds the file handler resource if the file is opened
     *
     * @var resource
     * @access public
     */
    public $handle;

    /**
     * enable locking for file reading and writing
     *
     * @var boolean
     * @access public
     */
    public $lock;

    /**
     * Constructor
     *
     * @param string  $path   Path to file
     * @param boolean $create Create file if it does not exist (if true)
     * @param integer $mode   Mode to apply to the folder holding the file
     * @access private
     */
    public function __construct($path, $create = false, $mode = 0755)
    {
        XoopsLoad::load('XoopsFile');
        $this->folder = XoopsFile::getHandler('folder', dirname($path), $create, $mode);
        if (!is_dir($path)) {
            $this->name = basename($path);
        }
        if (!$this->exists()) {
            if ($create === true) {
                if ($this->safe($path) && $this->create() === false) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return null;
    }

    /**
     * Closes the current file if it is opened
     *
     * @access private
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Creates the File.
     *
     * @return boolean Success
     * @access public
     */
    public function create()
    {
        $dir = $this->folder->pwd();
        if (is_dir($dir) && is_writable($dir) && !$this->exists()) {
            if (touch($this->pwd())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Opens the current file with a given $mode
     *
     * @param  string  $mode  A valid 'fopen' mode string (r|w|a ...)
     * @param  boolean $force If true then the file will be re-opened even if its already opened, otherwise it won't
     * @return boolean True on success, false on failure
     * @access public
     */
    public function open($mode = 'r', $force = false)
    {
        if (!$force && is_resource($this->handle)) {
            return true;
        }
        if ($this->exists() === false) {
            if ($this->create() === false) {
                return false;
            }
        }
        $this->handle = fopen($this->pwd(), $mode);
        if (is_resource($this->handle)) {
            return true;
        }

        return false;
    }

    /**
     * Return the contents of this File as a string.
     *
     * @param bool|string $bytes where to start
     * @param string      $mode
     * @param boolean     $force If true then the file will be re-opened even if its already opened, otherwise it won't
     *
     * @return mixed string on success, false on failure
     * @access public
     */
    public function read($bytes = false, $mode = 'rb', $force = false)
    {
        $success = false;
        if ($this->lock !== null) {
            if (flock($this->handle, LOCK_SH) === false) {
                return false;
            }
        }
        if ($bytes === false) {
            $success = file_get_contents($this->pwd());
        } elseif ($this->open($mode, $force) === true) {
            if (is_int($bytes)) {
                $success = fread($this->handle, $bytes);
            } else {
                $data = '';
                while (!feof($this->handle)) {
                    $data .= fgets($this->handle, 4096);
                }
                $success = trim($data);
            }
        }
        if ($this->lock !== null) {
            flock($this->handle, LOCK_UN);
        }

        return $success;
    }

    /**
     * Sets or gets the offset for the currently opened file.
     *
     * @param  mixed   $offset The $offset in bytes to seek. If set to false then the current offset is returned.
     * @param  integer $seek   PHP Constant SEEK_SET | SEEK_CUR | SEEK_END determining what the $offset is relative to
     * @return mixed   True on success, false on failure (set mode), false on failure or integer offset on success (get mode)
     * @access public
     */
    public function offset($offset = false, $seek = SEEK_SET)
    {
        if ($offset === false) {
            if (is_resource($this->handle)) {
                return ftell($this->handle);
            }
        } elseif ($this->open() === true) {
            return fseek($this->handle, $offset, $seek) === 0;
        }

        return false;
    }

    /**
     * Prepares a ascii string for writing
     * fixes line endings
     *
     * @param  string $data Data to prepare for writing.
     * @return string
     * @access public
     */
    public function prepare($data)
    {
        $lineBreak = "\n";
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $lineBreak = "\r\n";
        }

        return strtr($data, array(
            "\r\n" => $lineBreak,
            "\n"   => $lineBreak,
            "\r"   => $lineBreak));
    }

    /**
     * Write given data to this File.
     *
     * @param  string      $data  Data to write to this File.
     * @param  string      $mode  Mode of writing. {@link http://php.net/fwrite See fwrite()}.
     * @param  bool|string $force force the file to open
     * @return boolean     Success
     * @access public
     */
    public function write($data, $mode = 'w', $force = false)
    {
        $success = false;
        if ($this->open($mode, $force) === true) {
            if ($this->lock !== null) {
                if (flock($this->handle, LOCK_EX) === false) {
                    return false;
                }
            }
            if (fwrite($this->handle, $data) !== false) {
                $success = true;
            }
            if ($this->lock !== null) {
                flock($this->handle, LOCK_UN);
            }
        }

        return $success;
    }

    /**
     * Append given data string to this File.
     *
     * @param  string      $data  Data to write
     * @param  bool|string $force force the file to open
     * @return boolean     Success
     * @access public
     */
    public function append($data, $force = false)
    {
        return $this->write($data, 'a', $force);
    }

    /**
     * Closes the current file if it is opened.
     *
     * @return boolean True if closing was successful or file was already closed, otherwise false
     * @access public
     */
    public function close()
    {
        if (!is_resource($this->handle)) {
            return true;
        }

        return fclose($this->handle);
    }

    /**
     * Deletes the File.
     *
     * @return boolean Success
     * @access public
     */
    public function delete()
    {
        if ($this->exists()) {
            return unlink($this->pwd());
        }

        return false;
    }

    /**
     * Returns the File extension.
     *
     * @return string The File extension
     * @access public
     */
    public function info()
    {
        if ($this->info == null) {
            $this->info = pathinfo($this->pwd());
        }
        if (!isset($this->info['filename'])) {
            $this->info['filename'] = $this->name();
        }

        return $this->info;
    }

    /**
     * Returns the File extension.
     *
     * @return string The File extension
     * @access public
     */
    public function ext()
    {
        if ($this->info == null) {
            $this->info();
        }
        if (isset($this->info['extension'])) {
            return $this->info['extension'];
        }

        return false;
    }

    /**
     * Returns the File name without extension.
     *
     * @return string The File name without extension.
     * @access public
     */
    public function name()
    {
        if ($this->info == null) {
            $this->info();
        }
        if (isset($this->info['extension'])) {
            return basename($this->name, '.' . $this->info['extension']);
        } elseif ($this->name) {
            return $this->name;
        }

        return false;
    }

    /**
     * makes filename safe for saving
     *
     * @param  string $name the name of the file to make safe if different from $this->name
     * @param  null   $ext
     * @return string $ext the extension of the file
     * @access public
     */
    public function safe($name = null, $ext = null)
    {
        if (!$name) {
            $name = $this->name;
        }
        if (!$ext) {
            $ext = $this->ext();
        }

        return preg_replace('/[^\w\.-]+/', '_', basename($name, $ext));
    }

    /**
     * Get md5 Checksum of file with previous check of Filesize
     *
     * @param  mixed $maxsize in MB or true to force
     * @return string md5 Checksum {@link http://php.net/md5_file See md5_file()}
     * @access public
     */
    public function md5($maxsize = 5)
    {
        if ($maxsize === true) {
            return md5_file($this->pwd());
        } else {
            $size = $this->size();
            if ($size && $size < ($maxsize * 1024) * 1024) {
                return md5_file($this->pwd());
            }
        }

        return false;
    }

    /**
     * Returns the full path of the File.
     *
     * @return string Full path to file
     * @access public
     */
    public function pwd()
    {
        return $this->folder->slashTerm($this->folder->pwd()) . $this->name;
    }

    /**
     * Returns true if the File exists.
     *
     * @return boolean true if it exists, false otherwise
     * @access public
     */
    public function exists()
    {
        $exists = (file_exists($this->pwd()) && is_file($this->pwd()));

        return $exists;
    }

    /**
     * Returns the "chmod" (permissions) of the File.
     *
     * @return string Permissions for the file
     * @access public
     */
    public function perms()
    {
        if ($this->exists()) {
            return substr(sprintf('%o', fileperms($this->pwd())), -4);
        }

        return false;
    }

    /**
     * Returns the Filesize, either in bytes or in human-readable format.
     *
     * @return string |int filesize as int or as a human-readable string
     * @access   public
     */
    public function size()
    {
        if ($this->exists()) {
            return filesize($this->pwd());
        }

        return false;
    }

    /**
     * Returns true if the File is writable.
     *
     * @return boolean true if its writable, false otherwise
     * @access public
     */
    public function writable()
    {
        return is_writable($this->pwd());
    }

    /**
     * Returns true if the File is executable.
     *
     * @return boolean true if its executable, false otherwise
     * @access public
     */
    public function executable()
    {
        return is_executable($this->pwd());
    }

    /**
     * Returns true if the File is readable.
     *
     * @return boolean true if file is readable, false otherwise
     * @access public
     */
    public function readable()
    {
        return is_readable($this->pwd());
    }

    /**
     * Returns the File's owner.
     *
     * @return integer the Fileowner
     */
    public function owner()
    {
        if ($this->exists()) {
            return fileowner($this->pwd());
        }

        return false;
    }

    /**
     * Returns the File group.
     *
     * @return integer the Filegroup
     * @access public
     */
    public function group()
    {
        if ($this->exists()) {
            return filegroup($this->pwd());
        }

        return false;
    }

    /**
     * Returns last access time.
     *
     * @return integer timestamp Timestamp of last access time
     * @access public
     */
    public function lastAccess()
    {
        if ($this->exists()) {
            return fileatime($this->pwd());
        }

        return false;
    }

    /**
     * Returns last modified time.
     *
     * @return integer timestamp Timestamp of last modification
     * @access public
     */
    public function lastChange()
    {
        if ($this->exists()) {
            return filemtime($this->pwd());
        }

        return false;
    }

    /**
     * Returns the current folder.
     *
     * @return Folder Current folder
     * @access public
     */
    public function &folder()
    {
        return $this->folder;
    }
}
