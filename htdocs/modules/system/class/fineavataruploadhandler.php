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

class SystemFineAvatarUploadHandler extends SystemFineUploadHandler
{
    /**
     * XoopsFineAvatarUploadHandler constructor.
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
        $pathParts = pathinfo($this->getName());
        $avatarName = uniqid('savt') . '.' . strtolower($pathParts['extension']);
        $avatarNicename = str_replace(array('_','-'), ' ', $pathParts['filename']);
        $avatarPath = XOOPS_ROOT_PATH . '/uploads/avatars/' . $avatarName;

        if (false === move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $avatarPath)) {
            return false;
        }
        /* @var  $avt_handler XoopsAvatarHandler */
        $avt_handler = xoops_getHandler('avatar');
        $avatar = $avt_handler->create();
        
        $avatar->setVar('avatar_file', 'avatars/' . $avatarName);
        $avatar->setVar('avatar_name', $avatarNicename);
        $avatar->setVar('avatar_mimetype', $mimeType);
        $avatar->setVar('avatar_created', time());
        $avatar->setVar('avatar_display', 1);
        $avatar->setVar('avatar_weight', 0);
        $avatar->setVar('avatar_type', 's');
        if (!$avt_handler->insert($avatar)) {
            return array(
                'error' => sprintf(_FAILSAVEIMG, $avatar->getVar('avatar_name'))
            );
        }
        return array('success'=> true, "uuid" => $uuid);
    }
}
