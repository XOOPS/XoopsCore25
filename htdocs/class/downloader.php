<?php
/**
 * XOOPS downloader
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
 * @author              Kazumi Ono <onokazu@xoops.org>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Sends non HTML files through a http socket
 *
 */
class XoopsDownloader
{
    /**
     * *#@+
     * file information
     */
    public $mimetype;
    public $ext;
    public $archiver;
    /**
     * *#@-
     */

    /**
     * Constructor
     */

    public function __construct()
    {
        // EMPTY
    }

    /**
     * Send the HTTP header
     *
     * @param string $filename
     * @access private
     */
    public function _header($filename)
    {
        if (function_exists('mb_http_output')) {
            mb_http_output('pass');
        }
        header('Content-Type: ' . $this->mimetype);
        if (preg_match("/MSIE (\d\.\d{1,2})/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Pragma: no-cache');
        }
    }

    /**
     * XoopsDownloader::addFile()
     *
     * @param string $filepath
     * @param string $newfilename
     */
    public function addFile($filepath, $newfilename = null)
    {
        // EMPTY
    }

    /**
     * XoopsDownloader::addBinaryFile()
     *
     * @param string $filepath
     * @param string $newfilename
     */
    public function addBinaryFile($filepath, $newfilename = null)
    {
        // EMPTY
    }

    /**
     * XoopsDownloader::addFileData()
     *
     * @param mixed   $data
     * @param string  $filename
     * @param integer $time
     */
    public function addFileData(&$data, $filename, $time = 0)
    {
        // EMPTY
    }

    /**
     * XoopsDownloader::addBinaryFileData()
     *
     * @param mixed   $data
     * @param string  $filename
     * @param integer $time
     */
    public function addBinaryFileData(&$data, $filename, $time = 0)
    {
        // EMPTY
    }

    /**
     * XoopsDownloader::download()
     *
     * @param string  $name
     * @param boolean $gzip
     */
    public function download($name, $gzip = true)
    {
        // EMPTY
    }
}
