<?php
/**
 * User ranks class manager
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
 * @author              Gregory Mage (AKA Mage)
 * @package             system
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * System User ranks
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemUserrank extends XoopsObject
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('rank_id', XOBJ_DTYPE_INT, null, false, 5);
        $this->initVar('rank_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('rank_min', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('rank_max', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('rank_special', XOBJ_DTYPE_INT, null, false, 1);
        $this->initVar('rank_image', XOBJ_DTYPE_TXTBOX, null, false);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false)
    {
        if ($this->isNew()) {
            $blank_img = 'blank.gif';
        } else {
            $blank_img = str_replace('ranks/', '', $this->getVar('rank_image', 'e'));
        }
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_SYSTEM_USERRANK_ADD) : sprintf(_AM_SYSTEM_USERRANK_EDIT);

        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new XoopsFormText(_AM_SYSTEM_USERRANK_TITLE, 'rank_title', 50, 50, $this->getVar('rank_title'), true));
        $form->addElement(new XoopsFormText(_AM_SYSTEM_USERRANK_MINPOST, 'rank_min', 10, 10, $this->getVar('rank_min')));
        $form->addElement(new XoopsFormText(_AM_SYSTEM_USERRANK_MAXPOST, 'rank_max', 10, 10, $this->getVar('rank_max')));

        $imgtray_img     = new XoopsFormElementTray(_AM_SYSTEM_USERRANK_IMAGE, '<br>');
        $imgpath_img     = sprintf(_AM_SYSTEM_USERRANK_IMAGE_PATH, XOOPS_UPLOAD_PATH . '/ranks/');
        $imageselect_img = new XoopsFormSelect($imgpath_img, 'rank_image', $blank_img);
        $image_array_img = XoopsLists::getImgListAsArray(XOOPS_UPLOAD_PATH . '/ranks');
        $imageselect_img->addOption("$blank_img", $blank_img);
        foreach ($image_array_img as $image_img) {
            $imageselect_img->addOption("$image_img", $image_img);
        }
        $imageselect_img->setExtra("onchange='showImgSelected(\"xo-ranks-img\", \"rank_image\", \"ranks\", \"\", \"" . XOOPS_UPLOAD_URL . "\")'");
        $imgtray_img->addElement($imageselect_img, false);
        $imgtray_img->addElement(new XoopsFormLabel('', "<br><img src='" . XOOPS_UPLOAD_URL . '/ranks/' . $blank_img . "' name='image_img' id='xo-ranks-img' alt='' />"));

        $fileseltray_img = new XoopsFormElementTray('<br>', '<br><br>');
        $fileseltray_img->addElement(new XoopsFormFile(_AM_SYSTEM_USERRANK_UPLOAD, 'rank_image', 500000), false);
        $fileseltray_img->addElement(new XoopsFormLabel(''), false);
        $imgtray_img->addElement($fileseltray_img);
        $form->addElement($imgtray_img);

        $rank_special = 0;
        if (!$this->isNew()) {
            $rank_special = $this->getVar('rank_special');
        }

        $special_tray = new XoopsFormElementTray(_AM_SYSTEM_USERRANK_SPECIAL, '<br>');
        $special_tray->setDescription(_AM_SYSTEM_USERRANK_SPECIAL_CAN);
        $special_tray->addElement(new XoopsFormRadioYN('', 'rank_special', $rank_special));
        $form->addElement($special_tray);
        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('rank_id', $this->getVar('rank_id')));
        }
        $form->addElement(new XoopsFormHidden('op', 'userrank_save'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * System user ranks handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 * @subpackage          avatar
 */
class SystemuserrankHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'ranks', 'SystemUserrank', 'rank_id', 'rank_title');
    }
}
