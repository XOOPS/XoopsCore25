<?php
/**
 * Send tar files through a http socket
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * base class
 */
include_once XOOPS_ROOT_PATH . '/class/downloader.php';
/**
 * Class to handle tar files
 */
include_once XOOPS_ROOT_PATH . '/class/class.tar.php';

/**
 * Class XoopsTarDownloader
 */
class XoopsTarDownloader extends XoopsDownloader
{
    /**
     * Constructor
     *
     * @param string $ext      file extension
     * @param string $mimyType Mimetype
     */
    public function __construct($ext = '.tar.gz', $mimyType = 'application/x-gzip')
    {
        $this->archiver = new tar();
        $this->ext      = trim($ext);
        $this->mimeType = trim($mimyType);
    }

    /**
     * Add a file to the archive
     *
     * @param string $filepath    Full path to the file
     * @param string $newfilename Filename (if you don't want to use the original)
     */
    public function addFile($filepath, $newfilename = null)
    {
        $this->archiver->addFile($filepath);
        if (isset($newfilename)) {
            // dirty, but no other way
            for ($i = 0; $i < $this->archiver->numFiles; ++$i) {
                if ($this->archiver->files[$i]['name'] == $filepath) {
                    $this->archiver->files[$i]['name'] = trim($newfilename);
                    break;
                }
            }
        }
    }

    /**
     * Add a binary file to the archive
     *
     * @param string $filepath    Full path to the file
     * @param string $newfilename Filename (if you don't want to use the original)
     */
    public function addBinaryFile($filepath, $newfilename = null)
    {
        $this->archiver->addFile($filepath, true);
        if (isset($newfilename)) {
            // dirty, but no other way
            for ($i = 0; $i < $this->archiver->numFiles; ++$i) {
                if ($this->archiver->files[$i]['name'] == $filepath) {
                    $this->archiver->files[$i]['name'] = trim($newfilename);
                    break;
                }
            }
        }
    }

    /**
     * Add a dummy file to the archive
     *
     * @param string  $data     Data to write
     * @param string  $filename Name for the file in the archive
     * @param integer $time
     */
    public function addFileData(&$data, $filename, $time = 0)
    {
        $dummyfile = XOOPS_CACHE_PATH . '/dummy_' . time() . '.tpl';
        $fp        = fopen($dummyfile, 'w');
        fwrite($fp, $data);
        fclose($fp);
        $this->archiver->addFile($dummyfile);
        unlink($dummyfile);
        // dirty, but no other way
        for ($i = 0; $i < $this->archiver->numFiles; ++$i) {
            if ($this->archiver->files[$i]['name'] == $dummyfile) {
                $this->archiver->files[$i]['name'] = $filename;
                if ($time != 0) {
                    $this->archiver->files[$i]['time'] = $time;
                }
                break;
            }
        }
    }

    /**
     * Add a binary dummy file to the archive
     *
     * @param string  $data     Data to write
     * @param string  $filename Name for the file in the archive
     * @param integer $time
     */
    public function addBinaryFileData(&$data, $filename, $time = 0)
    {
        $dummyfile = XOOPS_CACHE_PATH . '/dummy_' . time() . '.tpl';
        $fp        = fopen($dummyfile, 'wb');
        fwrite($fp, $data);
        fclose($fp);
        $this->archiver->addFile($dummyfile, true);
        unlink($dummyfile);
        // dirty, but no other way
        for ($i = 0; $i < $this->archiver->numFiles; ++$i) {
            if ($this->archiver->files[$i]['name'] == $dummyfile) {
                $this->archiver->files[$i]['name'] = $filename;
                if ($time != 0) {
                    $this->archiver->files[$i]['time'] = $time;
                }
                break;
            }
        }
    }

    /**
     * Send the file to the client
     *
     * @param string  $name Filename
     * @param boolean $gzip Use GZ compression
     */
    public function download($name, $gzip = true)
    {
        $this->_header($name . $this->ext);
        echo $this->archiver->toTarOutput($name . $this->ext, $gzip);
    }
}
