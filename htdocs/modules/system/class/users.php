<?php
/**
 * Users Class Manager
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
 * @package             system
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

include_once XOOPS_ROOT_PATH . '/kernel/user.php';

/**
 * System Users
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @package             system
 */
class SystemUsers extends XoopsUser
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return XoopsThemeForm
     */
    public function getForm()
    {
        if ($this->isNew()) {
            $blank_img = 'blank.gif';
        } else {
            $blank_img = $this->getVar('avatar_file', 'e');
        }
        // Get User Config
        /** @var XoopsConfigHandler $config_handler */
        $config_handler  = xoops_getHandler('config');
        $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

        $form = new XoopsThemeForm(_AM_SYSTEM_AVATAR_ADD, 'avatar_form', 'admin.php', 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement(new XoopsFormText(_IMAGENAME, 'avatar_name', 50, 255, $this->getVar('avatar_name', 'e')), true);

        $maxpixel = '<div class="small basic italic">' . _US_MAXPIXEL . '&nbsp;:&nbsp;' . $xoopsConfigUser['avatar_width'] . ' x ' . $xoopsConfigUser['avatar_height'] . '</div>';
        $maxsize  = '<div class="small basic italic">' . _US_MAXIMGSZ . '&nbsp;:&nbsp;' . $xoopsConfigUser['avatar_maxsize'] . '</div>';

        $uploadirectory_img = '';
        $imgtray_img        = new XoopsFormElementTray(_IMAGEFILE . '<br><br>' . $maxpixel . $maxsize, '<br>');
        $imageselect_img    = new XoopsFormSelect(sprintf(_AM_SYSTEM_AVATAR_USE_FILE, XOOPS_UPLOAD_PATH), 'avatar_file', $blank_img);
        $image_array_img    = XoopsLists::getImgListAsArray(XOOPS_UPLOAD_PATH);
        $imageselect_img->addOption((string)$blank_img, $blank_img);
        foreach ($image_array_img as $image_img) {
            //            if (preg_match('#avt#', $image_img)) {
            if (false !== strpos($image_img, 'avt')) {
                $imageselect_img->addOption((string)$image_img, $image_img);
            }
        }
        $imageselect_img->setExtra("onchange='showImgSelected(\"image_img\", \"avatar_file\", \"" . $uploadirectory_img . "\", \"\", \"" . XOOPS_UPLOAD_URL . "\")'");
        $imgtray_img->addElement($imageselect_img, false);
        $imgtray_img->addElement(new XoopsFormLabel('', "<br><img src='" . XOOPS_UPLOAD_URL . '/' . $blank_img . "' name='image_img' id='image_img' alt='' />"));
        $fileseltray_img = new XoopsFormElementTray('<br>', '<br><br>');
        $fileseltray_img->addElement(new XoopsFormFile(_AM_SYSTEM_AVATAR_UPLOAD, 'avatar_file', 500000), false);
        $imgtray_img->addElement($fileseltray_img);
        $form->addElement($imgtray_img);

        $form->addElement(new XoopsFormText(_IMGWEIGHT, 'avatar_weight', 3, 4, $this->getVar('avatar_weight', 'e')));
        $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'avatar_display', $this->getVar('avatar_display', 'e'), _YES, _NO));
        $form->addElement(new XoopsFormHidden('op', 'save'));
        $form->addElement(new XoopsFormHidden('fct', 'avatars'));
        $form->addElement(new XoopsFormHidden('avatar_id', $this->getVar('avatar_id', 'e')));
        $form->addElement(new XoopsFormButton('', 'avt_button', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * System users handler class. (Singleton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @package             system
 * @subpackage          avatar
 */
class SystemUsersHandler extends XoopsUserHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db);
        $this->className = 'SystemUsers';
    }
}
