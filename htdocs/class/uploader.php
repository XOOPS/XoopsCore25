<?php
/**
 * XOOPS file uploader
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

use Xmf\Request;

/**
 * Upload Media files
 *
 * Example of usage (single file):
 * <code>
 * include_once __DIR__ . '/uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $randomFilename = true;
 * $uploader = new XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight, $randomFilename);
 * if ($uploader->fetchMedia('single_file_name')) {
 *     if (!$uploader->upload()) {
 *         echo $uploader->getErrors();
 *     } else {
 *         echo '<h4>File uploaded successfully!</h4>'
 *         echo 'Saved as: ' . $uploader->getSavedFileName() . '<br>';
 *         echo 'Full path: ' . $uploader->getSavedDestination();
 *     }
 * } else {
 *        echo $uploader->getErrors();
 * }
 * </code>
 *
 * Example of usage (multiple file):
 * <code>
 * include_once __DIR__ . '/uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/webp');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $randomFilename = true;
 * $uploader = new XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight, $randomFilename);
 * for ($i = 0; $i < $uploader->countMedia('multiple_file_name'); $i++) {
 *     if ($uploader->fetchMedia('multiple_file_name')) {
 *        if (!$uploader->upload()) {
 *           echo $uploader->getErrors();
 *        } else {
 *           echo '<h4>File uploaded successfully!</h4>'
 *           echo 'Saved as: ' . $uploader->getSavedFileName() . '<br>';
 *           echo 'Full path: ' . $uploader->getSavedDestination();
 *        }
 *     } else {
 *        echo $uploader->getErrors();
 *     }
 * }
 * </code>
 *
 */
class XoopsMediaUploader
{
    /**
     * Flag indicating if unrecognized mimetypes should be allowed (use with precaution ! may lead to security issues )
     */

    public $allowUnknownTypes       = false;
    public $mediaName;
    public $mediaType;
    public $mediaSize;
    public $mediaTmpName;
    public $mediaError;
    public $mediaRealType           = '';
    public $uploadDir               = '';
    public $allowedMimeTypes        = [];
    public $deniedMimeTypes         = [
        'application/x-httpd-php',
    ];
    public $maxFileSize             = 0;
    public $maxWidth;
    public $maxHeight;
    public $targetFileName;
    public $prefix;
    public $errors                  = [];
    public $savedDestination;
    public $savedFileName;
    public $extensionToMime         = [];
    public $checkImageType          = true;
    public $extensionsToBeSanitized = [
        'php',
        'phtml',
        'phtm',
        'php3',
        'php4',
        'cgi',
        'pl',
        'asp',
        'php5',
        'php7',
    ];
    // extensions needed image check (anti-IE Content-Type XSS)
    public $imageExtensions = [
        1  => 'gif',
        2  => 'jpg',
        3  => 'png',
        4  => 'swf',
        5  => 'psd',
        6  => 'bmp',
        7  => 'tif',
        8  => 'tif',
        9  => 'jpc',
        10 => 'jp2',
        11 => 'jpx',
        12 => 'jb2',
        13 => 'swc',
        14 => 'iff',
        15 => 'wbmp',
        16 => 'xbm',
        17 => 'webp',
    ];
    public $randomFilename  = false;

    /**
     * Constructor
     *
     * @param string $uploadDir
     * @param array  $allowedMimeTypes
     * @param int    $maxFileSize
     * @param int    $maxWidth
     * @param int    $maxHeight
     * @param bool   $randomFilename
     */

    public function __construct($uploadDir, $allowedMimeTypes, $maxFileSize = 0, $maxWidth = null, $maxHeight = null, $randomFilename = false)
    {
        $this->extensionToMime = include $GLOBALS['xoops']->path('include/mimetypes.inc.php');
        if (!is_array($this->extensionToMime)) {
            $this->extensionToMime = [];

            return false;
        }
        if (is_array($allowedMimeTypes)) {
            $this->allowedMimeTypes = & $allowedMimeTypes;
        }
        $this->uploadDir = $uploadDir;

        $limits = [];
        $limits = $this->arrayPushIfPositive($limits, $maxFileSize);
        $limits = $this->arrayPushIfPositive($limits, $this->return_bytes(ini_get('upload_max_filesize')));
        $limits = $this->arrayPushIfPositive($limits, $this->return_bytes(ini_get('post_max_size')));
        $limits = $this->arrayPushIfPositive($limits, $this->return_bytes(ini_get('memory_limit')));
        $this->maxFileSize = min($limits);

        if (isset($maxWidth)) {
            $this->maxWidth = (int) $maxWidth;
        }
        if (isset($maxHeight)) {
            $this->maxHeight = (int) $maxHeight;
        }
        if (isset($randomFilename)) {
            $this->randomFilename = $randomFilename;
        }
        if (!include_once $GLOBALS['xoops']->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/uploader.php')) {
            include_once $GLOBALS['xoops']->path('language/english/uploader.php');
        }
    }

    /**
     * converts memory/file sizes as defined in php.ini to bytes
     *
     * @param $size_str
     *
     * @return int
     */
    public function return_bytes($size_str)
    {
        switch (substr($size_str, -1)) {
            case 'K':
            case 'k':
                return (int) $size_str * 1024;
            case 'M':
            case 'm':
                return (int) $size_str * 1048576;
            case 'G':
            case 'g':
                return (int) $size_str * 1073741824;
            default:
                return $size_str;
        }
    }

    /**
     * Count the uploaded files (in case of multiple upload)
     *
     * @param  string $media_name Name of the file field
     * @return int|false
     */
    public function countMedia($media_name)
    {
        if (!Request::hasVar($media_name, 'FILES')) {
            $this->setErrors(_ER_UP_FILENOTFOUND);
            return false;
        }
        $files = Request::getArray($media_name, [], 'FILES');
        return count($files['name']);
    }

    /**
     * Fetch the uploaded file
     *
     * @param  string $media_name Name of the file field
     * @param  int    $index      Index of the file (if more than one uploaded under that name)
     * @return bool
     */
    public function fetchMedia($media_name, $index = null)
    {
        if (empty($this->extensionToMime)) {
            $this->setErrors(_ER_UP_MIMETYPELOAD);

            return false;
        }

        if (!Request::hasVar($media_name, 'FILES')) {
            $this->setErrors(_ER_UP_FILENOTFOUND);
            return false;
        }

        $files = Request::getArray($media_name, [], 'FILES');

        if (is_array($files['name']) && isset($index)) {
            $index = (int) $index;
            $this->mediaName = $files['name'][$index];
            if ($this->randomFilename) {
                $unique = uniqid();
                $this->targetFileName = $unique . '--' . $this->mediaName;
            }
            $this->mediaType    = $files['type'][$index];
            $this->mediaSize    = $files['size'][$index];
            $this->mediaTmpName = $files['tmp_name'][$index];
            $this->mediaError   = !empty($files['error'][$index]) ? $files['error'][$index] : 0;
        } elseif (is_array($files['name']) && !isset($index)) {
            $this->setErrors(_ER_UP_INDEXNOTSET);
            return false;
        } else {
            $file = $files;
            $this->mediaName = $file['name'];
            if ($this->randomFilename) {
                $unique = uniqid();
                $this->targetFileName = $unique . '--' . $this->mediaName;
            }
            $this->mediaType    = $file['type'];
            $this->mediaSize    = $file['size'];
            $this->mediaTmpName = $file['tmp_name'];
            $this->mediaError   = !empty($file['error']) ? $file['error'] : 0;
        }

        if (($ext = strrpos($this->mediaName, '.')) !== false) {
            $ext = strtolower(substr($this->mediaName, $ext + 1));
            if (isset($this->extensionToMime[$ext])) {
                $this->mediaRealType = $this->extensionToMime[$ext];
            }
        }
        $this->errors = [];
        if ($this->mediaError > 0) {
            switch($this->mediaError) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->setErrors(_ER_UP_INISIZE);
                    return false;
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->setErrors(_ER_UP_FORMSIZE);
                    return false;
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->setErrors(_ER_UP_PARTIAL);
                    return false;
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->setErrors(_ER_UP_NOFILE);
                    return false;
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->setErrors(_ER_UP_NOTMPDIR);
                    return false;
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->setErrors(_ER_UP_CANTWRITE);
                    return false;
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->setErrors(_ER_UP_EXTENSION);
                    return false;
                    break;
                default:
                    $this->setErrors(_ER_UP_UNKNOWN);
                    return false;
                    break;
            }
        }

        if ((int) $this->mediaSize < 0) {
            $this->setErrors(_ER_UP_INVALIDFILESIZE);

            return false;
        }
        if ($this->mediaName == '') {
            $this->setErrors(_ER_UP_FILENAMEEMPTY);

            return false;
        }
        if ($this->mediaTmpName === 'none' || !is_uploaded_file($this->mediaTmpName)) {
            $this->setErrors(_ER_UP_NOFILEUPLOADED);

            return false;
        }

        return true;
    }

    /**
     * Set the target filename
     *
     * @param string $value
     */
    public function setTargetFileName($value)
    {
        $this->targetFileName = (string) trim($value);
    }

    /**
     * Set the prefix
     *
     * @param string $value
     */
    public function setPrefix($value)
    {
        $this->prefix = (string) trim($value);
    }

    /**
     * Get the uploaded filename
     *
     * @return string
     */
    public function getMediaName()
    {
        return $this->mediaName;
    }

    /**
     * Get the type of the uploaded file
     *
     * @return string
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return int
     */
    public function getMediaSize()
    {
        return $this->mediaSize;
    }

    /**
     * Get the temporary name that the uploaded file was stored under
     *
     * @return string
     */
    public function getMediaTmpName()
    {
        return $this->mediaTmpName;
    }

    /**
     * Get the saved filename
     *
     * @return string
     */
    public function getSavedFileName()
    {
        return $this->savedFileName;
    }

    /**
     * Get the destination the file is saved to
     *
     * @return string
     */
    public function getSavedDestination()
    {
        return $this->savedDestination;
    }

    /**
     * Check the file and copy it to the destination
     *
     * @param  int $chmod
     * @return bool
     */
    public function upload($chmod = 0644)
    {
        if ($this->uploadDir == '') {
            $this->setErrors(_ER_UP_UPLOADDIRNOTSET);

            return false;
        }
        if (!is_dir($this->uploadDir)) {
            $this->setErrors(sprintf(_ER_UP_FAILEDOPENDIR, $this->uploadDir));

            return false;
        }
        if (!is_writable($this->uploadDir)) {
            $this->setErrors(sprintf(_ER_UP_FAILEDOPENDIRWRITE, $this->uploadDir));

            return false;
        }
        $this->sanitizeMultipleExtensions();

        if (!$this->checkMaxFileSize()) {
            return false;
        }
        if (!$this->checkMaxWidth()) {
            return false;
        }
        if (!$this->checkMaxHeight()) {
            return false;
        }
        if (!$this->checkMimeType()) {
            return false;
        }
        if (!$this->checkImageType()) {
            return false;
        }
        if (count($this->errors) > 0) {
            return false;
        }

        return $this->_copyFile($chmod);
    }

    /**
     * Copy the file to its destination
     *
     * @param int $chmod
     * @return bool
     */
    public function _copyFile($chmod)
    {
        $matched = [];
        if (!preg_match("/\.([a-zA-Z0-9]+)$/", $this->mediaName, $matched)) {
            $this->setErrors(_ER_UP_INVALIDFILENAME);
            return false;
        }

        if (isset($this->targetFileName)) {
            $this->savedFileName = $this->targetFileName;
        } elseif (isset($this->prefix)) {
            $this->savedFileName = uniqid($this->prefix, false) . '.' . strtolower($matched[1]); //TODO: for true, need to increase size of image_name field in image table
        } else {
            $this->savedFileName = strtolower($this->mediaName);
        }

        $this->savedFileName = iconv('UTF-8', 'ASCII//TRANSLIT', $this->savedFileName);
        $this->savedFileName = preg_replace('!\s+!', '_', $this->savedFileName);
        $this->savedFileName = preg_replace("/[^a-zA-Z0-9\._-]/", '', $this->savedFileName);

        $this->savedDestination = $this->uploadDir . '/' . $this->savedFileName;

        if (!move_uploaded_file($this->mediaTmpName, $this->savedDestination)) {
            $this->setErrors(sprintf(_ER_UP_FAILEDSAVEFILE, $this->savedDestination));
            return false;
        }

        // Check for IE XSS vulnerability for image files
        $ext = strtolower(substr(strrchr($this->savedDestination, '.'), 1));
        if (in_array($ext, $this->imageExtensions)) {
            $info = getimagesize($this->savedDestination);
            if ($info === false || $this->imageExtensions[(int) $info[2]] != $ext) {
                $this->setErrors(_ER_UP_SUSPICIOUSREFUSED);
                unlink($this->savedDestination);
                return false;
            }
        }

        if (false === chmod($this->savedDestination, $chmod)) {
            $this->setErrors(_ER_UP_MODE_NOT_CHANGED);
        }

        return true;
    }

    /**
     * Is the file the right size?
     *
     * @return bool
     */
    public function checkMaxFileSize()
    {
        if (!isset($this->maxFileSize)) {
            return true;
        }
        if ($this->mediaSize > $this->maxFileSize) {
            $this->setErrors(sprintf(_ER_UP_FILESIZETOOLARGE, $this->maxFileSize, $this->mediaSize));

            return false;
        }

        return true;
    }

    /**
     * Is the picture the right width?
     *
     * @return bool
     */
    public function checkMaxWidth()
    {
        if (!isset($this->maxWidth)) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[0] > $this->maxWidth) {
                $this->setErrors(sprintf(_ER_UP_FILEWIDTHTOOLARGE, $this->maxWidth, $dimension[0]));

                return false;
            }
        } else {
            trigger_error(sprintf(_ER_UP_FAILEDFETCHIMAGESIZE, $this->mediaTmpName), E_USER_WARNING);
        }

        return true;
    }

    /**
     * Is the picture the right height?
     *
     * @return bool
     */
    public function checkMaxHeight()
    {
        if (!isset($this->maxHeight)) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[1] > $this->maxHeight) {
                $this->setErrors(sprintf(_ER_UP_FILEHEIGHTTOOLARGE, $this->maxHeight, $dimension[1]));

                return false;
            }
        } else {
            trigger_error(sprintf(_ER_UP_FAILEDFETCHIMAGESIZE, $this->mediaTmpName), E_USER_WARNING);
        }

        return true;
    }

    /**
     * Check whether or not the uploaded file type is allowed
     *
     * @return bool
     */
    public function checkMimeType()
    {
        // if the browser supplied mime type looks suspicious, refuse it
        $structureCheck = (bool) preg_match('/^\w+\/[-+.\w]+$/', $this->mediaType);
        if (false === $structureCheck) {
            $this->mediaType = 'invalid';
            $this->setErrors(_ER_UP_UNKNOWNFILETYPEREJECTED);
            return false;
        }

        if (empty($this->mediaRealType) && empty($this->allowUnknownTypes)) {
            $this->setErrors(_ER_UP_UNKNOWNFILETYPEREJECTED);

            return false;
        }

        if ((!empty($this->allowedMimeTypes) && !in_array($this->mediaRealType, $this->allowedMimeTypes)) || (!empty($this->deniedMimeTypes) && in_array($this->mediaRealType, $this->deniedMimeTypes))) {
            $this->setErrors(sprintf(_ER_UP_MIMETYPENOTALLOWED, htmlspecialchars($this->mediaRealType, ENT_QUOTES | ENT_HTML5)));

            return false;
        }

        return true;
    }

    /**
     * Check whether or not the uploaded image type is valid
     *
     * @return bool
     */
    public function checkImageType()
    {
        if (empty($this->checkImageType)) {
            return true;
        }

        if (('image' === substr($this->mediaType, 0, strpos($this->mediaType, '/'))) || (!empty($this->mediaRealType) && 'image' === substr($this->mediaRealType, 0, strpos($this->mediaRealType, '/')))) {
            if (!($info = @getimagesize($this->mediaTmpName))) {
                $this->setErrors(_ER_UP_INVALIDIMAGEFILE);

                return false;
            }
        }

        return true;
    }

    /**
     * Sanitize executable filename with multiple extensions
     */
    public function sanitizeMultipleExtensions()
    {
        if (empty($this->extensionsToBeSanitized)) {
            return null;
        }

        $patterns = [];
        $replaces = [];
        foreach ($this->extensionsToBeSanitized as $ext) {
            $patterns[] = "/\." . preg_quote($ext, '/') . "\./i";
            $replaces[] = '_' . $ext . '.';
        }
        $this->mediaName = preg_replace($patterns, $replaces, $this->mediaName);
    }

    /**
     * Add an error
     *
     * @param string $error
     */
    public function setErrors($error)
    {
        $this->errors[] = trim($error);
    }

    /**
     * Get generated errors
     *
     * @param  bool $ashtml Format using HTML?
     * @return array |string    Array of array messages OR HTML string
     */
    public function &getErrors($ashtml = true)
    {
        if (!$ashtml) {
            return $this->errors;
        } else {
            $ret = '';
            if (count($this->errors) > 0) {
                $ret = '<h4>' . sprintf(_ER_UP_ERRORSRETURNED, htmlspecialchars($this->mediaName, ENT_QUOTES | ENT_HTML5)) . '</h4>';
                foreach ($this->errors as $error) {
                    $ret .= $error . '<br>';
                }
            }

            return $ret;
        }
    }

    /**
     * Push value onto set.
     * Used in max file size calculation to eliminate -1 (unlimited) ini values
     *
     * @param array $set   array of values
     * @param int   $value value to push
     *
     * @return mixed
     */
    protected function arrayPushIfPositive($set, $value)
    {
        if ($value > 0) {
            array_push($set, $value);
        }
        return $set;
    }
}
