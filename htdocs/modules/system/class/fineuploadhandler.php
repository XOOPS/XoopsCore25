<?php

use Xmf\Request;

/**
 * Base SystemFineUploadHandler class to work with ajaxfineupload.php endpoint
 *
 * Upload files as specified
 *
 * Do not use or reference this directly from your client-side code.
 * Instead, this should be required via the endpoint.php or endpoint-cors.php
 * file(s).
 *
 * @license   MIT License (MIT)
 * @copyright Copyright (c) 2015-present, Widen Enterprises, Inc.
 * @link      https://github.com/FineUploader/php-traditional-server
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015-present, Widen Enterprises, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

abstract class SystemFineUploadHandler
{
    public $allowedExtensions = [];
    public $allowedMimeTypes = ['(none)']; // must specify!
    public $sizeLimit = null;
    public $inputName = 'qqfile';
    public $chunksFolder = 'chunks';

    public $chunksCleanupProbability = 0.001; // Once in 1000 requests on avg
    public $chunksExpireIn = 604800; // One week

    public $uploadName;
    public $claims;

    /**
     * XoopsFineUploadHandler constructor.
     * @param stdClass $claims claims passed in JWT header
     */
    public function __construct(\stdClass $claims)
    {
        $this->claims = $claims;
    }

    /**
     * Get the original filename
     */
    public function getName()
    {
        if (Request::hasVar('qqfilename', 'REQUEST')) {
            $qqfilename = Request::getString('qqfilename', '', 'REQUEST');
            return $qqfilename;
        }

        if (Request::hasVar($this->inputName, 'FILES')) {
            $file = Request::getArray($this->inputName, null, 'FILES');
            return $file ;
        }
    }

    /**
     * Get the name of the uploaded file
     * @return string
     */
    public function getUploadName()
    {
        return $this->uploadName;
    }

    /**
     * Combine chunks into a single file
     *
     * @param string      $uploadDirectory upload directory
     * @param string|null $name            name
     * @return array response to be json encoded and returned to client
     */
    public function combineChunks($uploadDirectory, $name = null)
    {
        $uuid = Request::getString('qquuid', '', 'POST');
        if ('' === $name) {
            $name = $this->getName();
        }
        $targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;
        $totalParts = Request::getInt('qqtotalparts', 1, 'REQUEST');

        $targetPath = implode(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid, $name]);
        $this->uploadName = $name;

        if (!file_exists($targetPath)) {
            if (!mkdir($concurrentDirectory = dirname($targetPath), 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        $target = fopen($targetPath, 'wb');

        for ($i = 0; $i < $totalParts; $i++) {
            $chunk = fopen($targetFolder . DIRECTORY_SEPARATOR . $i, 'rb');
            stream_copy_to_stream($chunk, $target);
            fclose($chunk);
        }

        // Success
        fclose($target);

        for ($i = 0; $i < $totalParts; $i++) {
            unlink($targetFolder . DIRECTORY_SEPARATOR . $i);
        }

        rmdir($targetFolder);

        if (null !== $this->sizeLimit && filesize($targetPath) > $this->sizeLimit) {
            unlink($targetPath);
            //http_response_code(413);
            header('HTTP/1.0 413 Request Entity Too Large');
            return ['success' => false, 'uuid' => $uuid, 'preventRetry' => true];
        }

        return ['success' => true, 'uuid' => $uuid];
    }

    /**
     * Process the upload.
     * @param string $uploadDirectory Target directory.
     * @param string $name Overwrites the name of the file.
     * @return array response to be json encoded and returned to client
     */
    public function handleUpload($uploadDirectory, $name = null)
    {
        if (is_writable($this->chunksFolder) &&
            1 == mt_rand(1, 1 / $this->chunksCleanupProbability)) {
            // Run garbage collection
            $this->cleanupChunks();
        }

        // Check that the max upload size specified in class configuration does not
        // exceed size allowed by server config
        if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit ||
            $this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit) {
            $neededRequestSize = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            return [
                'error' => 'Server error. Increase post_max_size and upload_max_filesize to ' . $neededRequestSize,
            ];
        }

        if ($this->isInaccessible($uploadDirectory)) {
            return ['error' => "Server error. Uploads directory isn't writable"];
        }

        $type = $_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'];

        if (!isset($type)) {
            return ['error' => "No files were uploaded."];
        }

        if (strpos(strtolower($type), 'multipart/') !== 0) {
            return [
                'error' => "Server error. Not a multipart request. Please set forceMultipart to default value (true).",
            ];
        }

        // Get size and name
        $file = Request::getArray($this->inputName, [], 'FILES');
        $size = $file['size'];
        if (Request::hasVar('qqtotalfilesize')) {
            $size = Request::getInt('qqtotalfilesize');
        }

        if (null === $name) {
            $name = $this->getName();
        }

        // check file error
        if ($file['error']) {
            return ['error' => 'Upload Error #' . $file['error']];
        }

        // Validate name
        if (null === $name || '' === $name) {
            return ['error' => 'File name empty.'];
        }

        // Validate file size
        if (0 == $size) {
            return ['error' => 'File is empty.'];
        }

        if (null !== $this->sizeLimit && $size > $this->sizeLimit) {
            return ['error' => 'File is too large.', 'preventRetry' => true];
        }

        // Validate file extension
        $pathinfo = pathinfo((string) $name);
        $ext = isset($pathinfo['extension']) ? strtolower($pathinfo['extension']) : '';

        if ($this->allowedExtensions
            && !in_array(strtolower($ext), array_map('strtolower', $this->allowedExtensions))) {
            $these = implode(', ', $this->allowedExtensions);
            return [
                'error' => 'File has an invalid extension, it should be one of ' . $these . '.',
                'preventRetry' => true,
            ];
        }

        $mimeType = '';
        if (!empty($this->allowedMimeTypes)) {

            $temp = $this->inputName;
            $mimeType = mime_content_type(Request::getArray($temp, [], 'FILES')['tmp_name']);
            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                return ['error' => 'File is of an invalid type.', 'preventRetry' => true];
            }
        }

        // Save a chunk
        $totalParts = 1;
        if (Request::hasVar('qqtotalparts')) {
            $totalParts = (int) Request::getString('qqtotalparts');
        }

        $uuid = Request::getString('qquuid');
        if ($totalParts > 1) {
            # chunked upload

            $chunksFolder = $this->chunksFolder;
            $partIndex = (int) Request::getString('qqpartindex');

            if (!is_writable($chunksFolder) && !is_executable($uploadDirectory)) {
                return ['error' => "Server error. Chunks directory isn't writable or executable."];
            }

            $targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;

            if (!file_exists($targetFolder)) {
                if (!mkdir($targetFolder, 0775, true) && !is_dir($targetFolder)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetFolder));
                }
            }

            $target = $targetFolder . '/' . $partIndex;

            $storeResult = $this->storeUploadedFile($target, $mimeType, $uuid);
            if (false !== $storeResult) {
                return $storeResult;
            }
        } else {
            # non-chunked upload

            $target = implode(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid, $name]);

            if ($target) {
                $this->uploadName = basename($target);

                $storeResult = $this->storeUploadedFile($target, $mimeType, $uuid);
                if (false !== $storeResult) {
                    return $storeResult;
                }
            }

            return ['error' => 'Could not save uploaded file.' .
                               'The upload was cancelled, or server error encountered',
            ];
        }
    }

    protected function storeUploadedFile($target, $mimeType, $uuid)
    {
        if (!is_dir(dirname((string) $target))) {
            if (!mkdir($concurrentDirectory = dirname((string) $target), 0775, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        $file = Request::getArray($this->inputName, null, 'FILES');
        if (null !== $file && move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => true, 'uuid' => $uuid];
        }
        return false;
    }

    /**
     * Process a delete.
     * @param string      $uploadDirectory Target directory.
     * @param string|null $name            Overwrites the name of the file.
     * @return array response to be json encoded and returned to client
     */
    public function handleDelete($uploadDirectory, $name = null)
    {
        if ($this->isInaccessible($uploadDirectory)) {
            return [
                'error' => "Server error. Uploads directory isn't writable"
                           . ((!$this->isWindows()) ? ' or executable.' : '.'),
            ];
        }

        $uuid = false;
        $method = Request::getString('REQUEST_METHOD', 'GET', 'SERVER');

        if ('DELETE' === $method) {
            $url = Request::getString('REQUEST_URI', '', 'SERVER');
            if ('' !== $url) {
                $url    = parse_url($url, PHP_URL_PATH);
                $tokens = explode('/', $url);
                $uuid = $tokens[count($tokens) - 1];
            }
        } elseif ('POST' === $method) {
            $uuid = Request::getString('qquuid', '', 'REQUEST');
        } else {
            return ['success' => false,
                'error'   => 'Invalid request method! ' . $method,
            ];
        }

        $target = implode(DIRECTORY_SEPARATOR, [$uploadDirectory, $uuid]);

        if (is_dir($target)) {
            $this->removeDir($target);
            return ['success' => true, 'uuid' => $uuid];
        } else {
            return [
                'success' => false,
                'error'   => 'File not found! Unable to delete.' . $url,
                'path'    => $uuid,
            ];
        }
    }

    /**
     * Returns a path to use with this upload. Check that the name does not exist,
     * and appends a suffix otherwise.
     * @param string $uploadDirectory Target directory
     * @param string $filename The name of the file to use.
     *
     * @return string|false path or false if path could not be determined
     */
    protected function getUniqueTargetPath($uploadDirectory, $filename)
    {
        // Allow only one process at the time to get a unique file name, otherwise
        // if multiple people would upload a file with the same name at the same time
        // only the latest would be saved.

        if (function_exists('sem_acquire')) {
            $lock = sem_get(ftok(__FILE__, 'u'));
            sem_acquire($lock);
        }

        $pathinfo = pathinfo($filename);
        $base = $pathinfo['filename'];
        $ext = $pathinfo['extension'] ?? '';
        $ext = '' == $ext ? $ext : '.' . $ext;

        $unique = $base;
        $suffix = 0;

        // Get unique file name for the file, by appending random suffix.

        while (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext)) {
            $suffix += random_int(1, 999);
            $unique = $base . '-' . $suffix;
        }

        $result =  $uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext;

        // Create an empty target file
        if (!touch($result)) {
            // Failed
            $result = false;
        }

        if (function_exists('sem_acquire')) {
            sem_release($lock);
        }

        return $result;
    }

    /**
     * Deletes all file parts in the chunks folder for files uploaded
     * more than chunksExpireIn seconds ago
     *
     * @return void
     */
    protected function cleanupChunks()
    {
        foreach (scandir($this->chunksFolder) as $item) {
            if ('.' == $item || '..' == $item) {
                continue;
            }

            $path = $this->chunksFolder . DIRECTORY_SEPARATOR . $item;

            if (!is_dir($path)) {
                continue;
            }

            if (time() - filemtime($path) > $this->chunksExpireIn) {
                $this->removeDir($path);
            }
        }
    }

    /**
     * Removes a directory and all files contained inside
     * @param string $dir
     * @return void
     */
    protected function removeDir($dir)
    {
        foreach (scandir($dir) as $item) {
            if ('.' == $item || '..' == $item) {
                continue;
            }

            if (is_dir($item)) {
                $this->removeDir($item);
            } else {
                unlink(implode(DIRECTORY_SEPARATOR, [$dir, $item]));
            }
        }
        rmdir($dir);
    }

    /**
     * Converts a given size with units to bytes.
     * @param string $str
     * @return int
     */
    protected function toBytes($str)
    {
        $str = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        if(is_numeric($last)) {
            $val = (int) $str;
        } else {
            $val = (int) substr($str, 0, -1);
        }
        switch ($last) {
            case 'g':
                $val *= 1024; // fall thru
                // no break
            case 'm':
                $val *= 1024; // fall thru
                // no break
            case 'k':
                $val *= 1024; // fall thru
        }
        return $val;
    }

    /**
     * Determines whether a directory can be accessed.
     *
     * is_executable() is not reliable on Windows prior PHP 5.0.0
     *  (https://www.php.net/manual/en/function.is-executable.php)
     * The following tests if the current OS is Windows and if so, merely
     * checks if the folder is writable;
     * otherwise, it checks additionally for executable status (like before).
     *
     * @param string $directory The target directory to test access
     * @return bool true if directory is NOT accessible
     */
    protected function isInaccessible($directory)
    {
        $isWin = $this->isWindows();
        $folderInaccessible =
            ($isWin) ? !is_writable($directory) : (!is_writable($directory) && !is_executable($directory));
        return $folderInaccessible;
    }

    /**
     * Determines is the OS is Windows or not
     *
     * @return bool
     */

    protected function isWindows()
    {
        $isWin = (stripos(PHP_OS, 'WIN') === 0);
        return $isWin;
    }
}
