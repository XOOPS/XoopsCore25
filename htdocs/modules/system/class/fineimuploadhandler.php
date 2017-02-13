<?php

/**
 * SystemFineImUploadHandler class to work with ajaxfineupload.php endpoint
 * to facilitate uploads for the system image manager
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

class SystemFineImUploadHandler extends SystemFineUploadHandler
{
    /**
     * XoopsFineImUploadHandler constructor.
     * @param stdClass $claims claims passed in JWT header
     */
    public function __construct(\stdClass $claims)
    {
        parent::__construct($claims);
        $this->allowedMimeTypes = array('image/gif', 'image/jpeg', 'image/png');
        $this->allowedExtensions = array('gif', 'jpeg', 'jpg', 'png');
    }

    protected function storeUploadedFile($target, $mimeType, $uuid)
    {
        /* @var XoopsImagecategoryHandler */
        $imgcatHandler = xoops_getHandler('imagecategory');
        $imgcat = $imgcatHandler->get($this->claims->cat);

        $pathParts = pathinfo($this->getName());

        $imageName = uniqid('img') . '.' . strtolower($pathParts['extension']);
        $imageNicename = str_replace(array('_','-'), ' ', $pathParts['filename']);
        $imagePath = XOOPS_ROOT_PATH . '/uploads/images/' . $imageName;

        $fbinary = null;
        if ($imgcat->getVar('imgcat_storetype') === 'db') {
            $fbinary = file_get_contents($_FILES[$this->inputName]['tmp_name']);
        } else {
            if (false === move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $imagePath)) {
                return false;
            }
        }

        /* @var $imageHandler XoopsImageHandler */
        $imageHandler = xoops_getHandler('image');
        $image = $imageHandler->create();

        $image->setVar('image_nicename', $imageNicename);
        $image->setVar('image_mimetype',  $mimeType);
        $image->setVar('image_created', time());
        $image->setVar('image_display', 1);
        $image->setVar('image_weight', 0);
        $image->setVar('imgcat_id', $this->claims->cat);
        if ($imgcat->getVar('imgcat_storetype') === 'db') {
            $image->setVar('image_body', $fbinary, true);
        } else {
            $image->setVar('image_name', 'images/' . $imageName);
        }
        if (!$imageHandler->insert($image)) {
            return array(
                'error' => sprintf(_FAILSAVEIMG, $image->getVar('image_nicename'))
            );
        }
        return array('success'=> true, "uuid" => $uuid);
    }
}
