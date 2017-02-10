<?php
/**
 * Smilies class manager
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
 * @author              Kazumi Ono (AKA onokazu)
 * @author              Gregory Mage (AKA Mage)
 * @package             system
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * System Smilies
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemSmilies extends XoopsObject
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('id', XOBJ_DTYPE_INT, null, false, 5);
        $this->initVar('code', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('smile_url', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('emotion', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('display', XOBJ_DTYPE_INT, null, false, 1);
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
            $blank_img = str_replace('smilies/', '', $this->getVar('smile_url', 'e'));
        }
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_SYSTEM_SMILIES_ADD) : sprintf(_AM_SYSTEM_SMILIES_EDIT);

        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement(new XoopsFormText(_AM_SYSTEM_SMILIES_CODE, 'code', 26, 25, $this->getVar('code')), true);
        $form->addElement(new XoopsFormText(_AM_SYSTEM_SMILIES_DESCRIPTION, 'emotion', 50, 50, $this->getVar('emotion')), true);

        $imgtray_img     = new XoopsFormElementTray(_AM_SYSTEM_SMILIES_FILE, '<br>');
        $imgpath_img     = sprintf(_AM_SYSTEM_SMILIES_IMAGE_PATH, XOOPS_UPLOAD_PATH . '/smilies/');
        $imageselect_img = new XoopsFormSelect($imgpath_img, 'smile_url', $blank_img);
        $image_array_img = XoopsLists::getImgListAsArray(XOOPS_UPLOAD_PATH . '/smilies');
        $imageselect_img->addOption("$blank_img", $blank_img);
        foreach ($image_array_img as $image_img) {
            $imageselect_img->addOption("$image_img", $image_img);
        }
        $imageselect_img->setExtra('onchange="showImgSelected(\'xo-smilies-img\', \'smile_url\', \'smilies\', \'\', \'' . XOOPS_UPLOAD_URL . '\' )"');
        $imgtray_img->addElement($imageselect_img, false);
        $imgtray_img->addElement(new XoopsFormLabel('', "<br><img src='" . XOOPS_UPLOAD_URL . '/smilies/' . $blank_img . "' name='image_img' id='xo-smilies-img' alt='' />"));

        $fileseltray_img = new XoopsFormElementTray('<br>', '<br><br>');
        $fileseltray_img->addElement(new XoopsFormFile(_AM_SYSTEM_SMILIES_UPLOADS, 'smile_url', 500000), false);
        $fileseltray_img->addElement(new XoopsFormLabel(''), false);
        $imgtray_img->addElement($fileseltray_img);
        $form->addElement($imgtray_img);

        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('smilies_id', $this->getVar('id')));
            $display = $this->getVar('display');
        } else {
            $display = 0;
        }

        $form->addElement(new XoopsFormRadioYN(_AM_SYSTEM_SMILIES_OFF, 'display', $display));

        $form->addElement(new XoopsFormHidden('op', 'save_smilie'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * System smilies handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 * @subpackage          avatar
 */
class SystemsmiliesHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'smiles', 'SystemSmilies', 'id', 'code');
    }
}
