<?php
/*
 * Banners Class Manager
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author      Gregory Mage (AKA Mage)
 * @package     system
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * System Banner
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */

class SystemBanner extends XoopsObject
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('bid', XOBJ_DTYPE_INT, null, false, 5);
        $this->initVar('cid', XOBJ_DTYPE_INT, null, false, 3);
        $this->initVar('imptotal', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('impmade', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('clicks', XOBJ_DTYPE_INT, null, false, 8);
        $this->initVar('imageurl', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('clickurl', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('date', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('htmlbanner', XOBJ_DTYPE_INT, null, false, 1);
        $this->initVar('htmlcode', XOBJ_DTYPE_TXTBOX, null, false);
        // For allow HTML
        //$this->initVar( 'dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * @param bool $action
     *
     * @return XoopsThemeForm
     */
    public function getForm($action = false)
    {
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_SYSTEM_BANNERS_ADDNWBNR) : sprintf(_AM_SYSTEM_BANNERS_EDITBNR);

        xoops_load('XoopsFormLoader');

        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        /* @var  $banner_client_Handler SystemBannerclientHandler */
        $banner_client_Handler = xoops_getModuleHandler('bannerclient', 'system');
        $client_select         = new XoopsFormSelect(_AM_SYSTEM_BANNERS_CLINAMET, 'cid', $this->getVar('cid'));
        $client_select->addOptionArray($banner_client_Handler->getList());
        $form->addElement($client_select, true);

        $form->addElement(new XoopsFormText(_AM_SYSTEM_BANNERS_IMPPURCHT, 'imptotal', 20, 255, $this->getVar('imptotal')), true);
        $form->addElement(new XoopsFormText(_AM_SYSTEM_BANNERS_IMGURLT, 'imageurl', 80, 255, $this->getVar('imageurl')), false);
        $form->addElement(new XoopsFormText(_AM_SYSTEM_BANNERS_CLICKURLT, 'clickurl', 80, 255, $this->getVar('clickurl')), false);

        $htmlbanner = $this->isNew() ? 0 : $this->getVar('htmlbanner');
        $form->addElement(new XoopsFormRadioYN(_AM_SYSTEM_BANNERS_USEHTML, 'htmlbanner', $htmlbanner, _YES, _NO));

        $form->addElement(new xoopsFormTextArea(_AM_SYSTEM_BANNERS_CODEHTML, 'htmlcode', $this->getVar('htmlcode'), 5, 50), false);
        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('bid', $this->getVar('bid')));
        }
        $form->addElement(new XoopsFormHidden('op', 'banner_save'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        //$form->display();
        return $form;
    }
}

/**
 * System banner handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 * @subpackage          banner
 */
class SystemBannerHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|object $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'banner', 'SystemBanner', 'bid', 'imageurl');
    }
}
