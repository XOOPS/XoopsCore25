<?php
/**
 * XOOPS Kernel Class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      system
 * @subpackage   menus
 * @since        2.5.12
 * @author       XOOPS Development Team, Grégory Mage (AKA GregMage)
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

class XoopsMenusCategory extends XoopsObject
{
    /**
     * Tracks included language files to avoid duplicate includes
     * keyed by full path.
     *
     * @var array
     */
    private static $languageFilesIncluded = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_title', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('category_prefix', XOBJ_DTYPE_TXTAREA);
        $this->initVar('category_suffix', XOBJ_DTYPE_TXTAREA);
        $this->initVar('category_url', XOBJ_DTYPE_TXTBOX, null);
        $this->initVar('category_target', XOBJ_DTYPE_INT, 0);
        $this->initVar('category_position', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_protected', XOBJ_DTYPE_INT, 0);
        $this->initVar('category_active', XOBJ_DTYPE_INT, 1);

        // Load language file for menu items
        $language = $GLOBALS['xoopsConfig']['language'] ?? 'english';
        $fileinc = XOOPS_ROOT_PATH . "/modules/system/language/{$language}/menus/menus.php";
        if (!isset(self::$languageFilesIncluded[$fileinc])) {
            if (file_exists($fileinc)) {
                include_once $fileinc;
                self::$languageFilesIncluded[$fileinc] = true;
            } else {
                // Fallback to English if language file not found
                $fallback = XOOPS_ROOT_PATH . '/modules/system/language/english/menus/menus.php';
                if ($fileinc !== $fallback && !isset(self::$languageFilesIncluded[$fallback]) && file_exists($fallback)) {
                    include_once $fallback;
                    self::$languageFilesIncluded[$fallback] = true;
                }
            }
        }
        // Load language file for admin menus
        $fileinc_admin = XOOPS_ROOT_PATH . "/modules/system/language/{$language}/admin/menus.php";
        if (!isset(self::$languageFilesIncluded[$fileinc_admin])) {
            if (file_exists($fileinc_admin)) {
                include_once $fileinc_admin;
                self::$languageFilesIncluded[$fileinc_admin] = true;
            } else {
                $fallback_admin = XOOPS_ROOT_PATH . '/modules/system/language/english/admin/menus.php';
                if ($fileinc_admin !== $fallback_admin && !isset(self::$languageFilesIncluded[$fallback_admin]) && file_exists($fallback_admin)) {
                    include_once $fallback_admin;
                    self::$languageFilesIncluded[$fallback_admin] = true;
                }
            }
        }
    }

    /**
     * Retrieve the resolved title for display.
     *
     * If the stored title is a constant name, resolves and returns its value.
     * Otherwise returns the stored title as-is.
     *
     * @return string The resolved title value
     */
    public function getResolvedTitle()
    {
        $title = (string)$this->getVar('category_title');
        if (0 === strpos($title, 'MENUS_') && defined($title)) {
            return (string)constant($title);
        }
        return $title;
    }

    /**
     * Retrieve the title for administration interface with constant reference.
     *
     * @return string The resolved title with optional constant reference
     */
    public function getAdminTitle()
    {
        $title = (string)$this->getVar('category_title');
        if (0 === strpos($title, 'MENUS_') && defined($title)) {
            return constant($title) . ' (' . $title . ')';
        }
        return $title;
    }

    /**
     * @return mixed
     */
    public function get_new_enreg()
    {
        global $xoopsDB;
        $new_enreg = $xoopsDB->getInsertId();

        return $new_enreg;
    }

    public function getFormCat($action = false)
    {
        if ($action === false) {
            $action = \Xmf\Request::getString('REQUEST_URI', '', 'SERVER');
        }
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        //form title
        $title = $this->isNew() ? sprintf(_AM_SYSTEM_MENUS_ADDCAT) : sprintf(_AM_SYSTEM_MENUS_EDITCAT);

        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $isProtected = false;
        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('category_id', (string)$this->getVar('category_id')));
            $position = $this->getVar('category_position');
            $active = $this->getVar('category_active');
            $isProtected = (int)$this->getVar('category_protected') === 1;
        } else {
            $position = 0;
            $active = 1;
        }

        // title
        $title = new XoopsFormText(_AM_SYSTEM_MENUS_TITLECAT, 'category_title', 50, 255, (string)$this->getVar('category_title'));
        if ($isProtected) {
            $title->setExtra('readonly="readonly"');
        }
        $title->setDescription(_AM_SYSTEM_MENUS_TITLECAT_DESC);
        $form->addElement($title, true);
        // prefix
        $editor_configs = array(
            'name' => 'category_prefix',
            'value' => $this->getVar('category_prefix', 'n'),
            'rows' => 1,
            'cols' => 50,
            'width' => '100%',
            'height' => '200px',
            'editor' => 'Plain Text'
        );
        $prefix = new XoopsFormEditor(_AM_SYSTEM_MENUS_PREFIXCAT, 'category_prefix', $editor_configs, false, 'textarea');
        if ($isProtected) {
            $prefix->setExtra('readonly="readonly"');
            if (isset($prefix->editor) && is_object($prefix->editor)) {
                $prefix->editor->setExtra('readonly="readonly"');
            }
        }
        $prefix->setDescription(_AM_SYSTEM_MENUS_PREFIXCAT_DESC);
        $form->addElement($prefix, false);
        // suffix
        $editor_configs = array(
            'name' => 'category_suffix',
            'value' => $this->getVar('category_suffix', 'n'),
            'rows' => 1,
            'cols' => 50,
            'width' => '100%',
            'height' => '200px',
            'editor' => 'Plain Text'
        );
        $suffix = new XoopsFormEditor(_AM_SYSTEM_MENUS_SUFFIXCAT, 'category_suffix', $editor_configs, false, 'textarea');
        if ($isProtected) {
            $suffix->setExtra('readonly="readonly"');
            if (isset($suffix->editor) && is_object($suffix->editor)) {
                $suffix->editor->setExtra('readonly="readonly"');
            }
        }
        $suffix->setDescription(_AM_SYSTEM_MENUS_SUFFIXCAT_DESC);
        $form->addElement($suffix, false);
        // url
        $url = new XoopsFormText(_AM_SYSTEM_MENUS_URLCAT, 'category_url', 50, 255, (string)$this->getVar('category_url'));
        if ($isProtected) {
            $url->setExtra('readonly="readonly"');
        }
        $url->setDescription(_AM_SYSTEM_MENUS_URLCATDESC);
        $form->addElement($url, false);
        // target
        $radio = new XoopsFormRadio(_AM_SYSTEM_MENUS_TARGET, 'category_target', (string)$this->getVar('category_target'));
        $radio->addOption(0, _AM_SYSTEM_MENUS_TARGET_SELF);
        $radio->addOption(1, _AM_SYSTEM_MENUS_TARGET_BLANK);
        $form->addElement($radio, false);
        // position
        $form->addElement(new XoopsFormText(_AM_SYSTEM_MENUS_POSITIONCAT, 'category_position', 5, 5, $position));

        // active
        $radio = new XoopsFormRadio(_AM_SYSTEM_MENUS_ACTIVE, 'category_active', $active);
        $radio->addOption(1, _YES);
        $radio->addOption(0, _NO);
        $form->addElement($radio);

        // permission
        $permHelper = new \Xmf\Module\Helper\Permission();
        $perm = $permHelper->getGroupSelectFormForItem('menus_category_view', $this->getVar('category_id'), _AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY, 'menus_category_view_perms', true);
        $perm->setDescription(_AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY_DESC);
        $form->addElement($perm, false);

        $form->addElement(new XoopsFormHidden('op', 'savecat'));
        // submit
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

class XoopsMenusCategoryHandler extends XoopsPersistableObjectHandler
{
    /**
     * Constructor
     *
     * @param XoopsDatabase $db reference to a xoopsDB object
     */
    public function __construct($db)
    {
        parent::__construct($db, 'menuscategory', 'XoopsMenusCategory', 'category_id', 'category_title');
    }
}
