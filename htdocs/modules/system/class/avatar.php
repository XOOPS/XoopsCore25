<?php
/**
 * Avatar Class Manager
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
 * @package             system
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

include_once $GLOBALS['xoops']->path('/kernel/avatar.php');

/**
 * System Avatar
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemAvatar extends XoopsAvatar
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
            $blank_img = str_replace('avatars/', '', $this->getVar('avatar_file', 'e'));
        }
        // Get User Config
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler  = xoops_getHandler('config');
        $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
        // New and edit form
        $form = new XoopsThemeForm(_AM_SYSTEM_AVATAR_ADD, 'avatar_form', 'admin.php', 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        // Name
        $form->addElement(new XoopsFormText(_IMAGENAME, 'avatar_name', 50, 255, $this->getVar('avatar_name', 'e')), true);
        // Name description
        $maxpixel = '<div>' . _US_MAXPIXEL . '&nbsp;:&nbsp;' . $xoopsConfigUser['avatar_width'] . ' x ' . $xoopsConfigUser['avatar_height'] . '</div>';
        $maxsize  = '<div>' . _US_MAXIMGSZ . '&nbsp;:&nbsp;' . $xoopsConfigUser['avatar_maxsize'] . '</div>';
        // Upload part
        $imgtray_img = new XoopsFormElementTray(_IMAGEFILE, '<br>');
        $imgtray_img->setDescription($maxpixel . $maxsize);
        $imageselect_img = new XoopsFormSelect(sprintf(_AM_SYSTEM_AVATAR_USE_FILE, XOOPS_UPLOAD_PATH . '/avatars/'), 'avatar_file', $blank_img);
        $image_array_img = XoopsLists::getImgListAsArray(XOOPS_UPLOAD_PATH . '/avatars');
        $imageselect_img->addOption("$blank_img", $blank_img);
        foreach ($image_array_img as $image_img) {
            $imageselect_img->addOption("$image_img", $image_img);
        }
        $imageselect_img->setExtra("onchange='showImgSelected(\"xo-avatar-img\", \"avatar_file\", \"avatars\", \"\", \"" . XOOPS_UPLOAD_URL . "\")'");
        $imgtray_img->addElement($imageselect_img, false);
        $imgtray_img->addElement(new XoopsFormLabel('', "<br><img src='" . XOOPS_UPLOAD_URL . '/avatars/' . $blank_img . "' name='image_img' id='xo-avatar-img' alt='' />"));
        $fileseltray_img = new XoopsFormElementTray('<br>', '<br><br>');
        $fileseltray_img->addElement(new XoopsFormFile(_AM_SYSTEM_AVATAR_UPLOAD, 'avatar_file', 500000), false);
        $imgtray_img->addElement($fileseltray_img);
        $form->addElement($imgtray_img);
        // Weight
        $form->addElement(new XoopsFormText(_IMGWEIGHT, 'avatar_weight', 3, 4, $this->getVar('avatar_weight', 'e')));
        // Display
        $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'avatar_display', $this->getVar('avatar_display', 'e'), _YES, _NO));
        // Hidden
        if ($this->isNew()) {
            $form->addElement(new XoopsFormHidden('avatar_type', 's'));
        }
        $form->addElement(new XoopsFormHidden('op', 'save'));
        $form->addElement(new XoopsFormHidden('fct', 'avatars'));
        $form->addElement(new XoopsFormHidden('avatar_id', $this->getVar('avatar_id', 'e')));
        // Button
        $form->addElement(new XoopsFormButton('', 'avt_button', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * System avatar handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 * @subpackage          avatar
 */
class SystemAvatarHandler extends XoopsAvatarHandler
{
    /**
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db);
        $this->className = 'SystemAvatar';
    }

    /**
     * Create new Object
     *
     * @param  bool $isNew
     * @return object
     */
    public function create($isNew = true)
    {
        $avatar = new SystemAvatar();
        if ($isNew) {
            $avatar->setNew();
        }

        return $avatar;
    }

    /**
     * Egt Object
     *
     * @param  int $id
     * @return object
     */
    public function get($id)
    {
        $avatar = false;
        $id     = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('avatar') . ' WHERE avatar_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $avatar = new SystemAvatar();
                $avatar->assignVars($this->db->fetchArray($result));

                return $avatar;
            }
        }

        return $avatar;
    }
}
