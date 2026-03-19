<?php
/**
 * XOOPS Menu Category Kernel Object
 *
 * @category  Kernel
 * @author    XOOPS Core Team
 * @copyright 2001-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2+ (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A single menu category (top-level navigation group).
 */
class XoopsMenusCategory extends XoopsObject
{
    /** @var bool Whether the language file has been loaded */
    private static bool $langLoaded = false;

    public function __construct()
    {
        parent::__construct();
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_title', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('category_prefix', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('category_suffix', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('category_url', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('category_target', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('category_position', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('category_protected', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('category_active', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * Load the menu language file if not already loaded.
     *
     * @return void
     */
    private static function ensureLanguageLoaded(): void
    {
        if (self::$langLoaded) {
            return;
        }
        $lang = $GLOBALS['xoopsConfig']['language'] ?? 'english';
        $path = XOOPS_ROOT_PATH . '/modules/system/language/' . $lang . '/admin/menus.php';
        if (is_readable($path)) {
            include_once $path;
        }
        self::$langLoaded = true;
    }

    /**
     * Resolve the title: if it matches a defined constant, return the constant's value.
     *
     * @return string The resolved title
     */
    public function getResolvedTitle(): string
    {
        self::ensureLanguageLoaded();
        $raw = $this->getVar('category_title', 'n');
        if ($raw !== '' && defined($raw)) {
            return constant($raw);
        }
        return (string) $raw;
    }

    /**
     * Return a display title for admin views showing both resolved value and constant name.
     *
     * @return string The admin display title
     */
    public function getAdminTitle(): string
    {
        $raw      = (string) $this->getVar('category_title', 'n');
        $resolved = $this->getResolvedTitle();
        if ($resolved !== $raw) {
            return $resolved . ' (' . $raw . ')';
        }
        return $resolved;
    }

    /**
     * Build the admin edit form for this category.
     *
     * @param string $action Form action URL
     *
     * @return \XoopsThemeForm
     */
    public function getFormCat(string $action): \XoopsThemeForm
    {
        $isEdit      = (bool) $this->getVar('category_id');
        $isProtected = (bool) $this->getVar('category_protected');
        $title       = $isEdit ? _AM_SYSTEM_MENUS_EDITCAT : _AM_SYSTEM_MENUS_ADDCAT;

        $form = new \XoopsThemeForm($title, 'categoryform', $action, 'post', true);

        if ($isEdit) {
            $form->addElement(new \XoopsFormHidden('category_id', (string) $this->getVar('category_id')));
        }

        $titleEl = new \XoopsFormText(
            _AM_SYSTEM_MENUS_CATTITLE,
            'category_title',
            60,
            100,
            $this->getVar('category_title', 'e')
        );
        if ($isProtected) {
            $titleEl->setExtra('readonly="readonly"');
        }
        $form->addElement($titleEl, true);

        $form->addElement($this->buildAffixField('category_prefix', _AM_SYSTEM_MENUS_CATPREFIX, $isProtected));
        $form->addElement($this->buildAffixField('category_suffix', _AM_SYSTEM_MENUS_CATSUFFIX, $isProtected));

        $urlEl = new \XoopsFormText(
            _AM_SYSTEM_MENUS_CATURL,
            'category_url',
            60,
            255,
            $this->getVar('category_url', 'e')
        );
        if ($isProtected) {
            $urlEl->setExtra('readonly="readonly"');
        }
        $form->addElement($urlEl);

        $targetEl = new \XoopsFormRadio(
            _AM_SYSTEM_MENUS_CATTARGET,
            'category_target',
            (string) $this->getVar('category_target')
        );
        $targetEl->addOption('0', _AM_SYSTEM_MENUS_TARGET_SELF);
        $targetEl->addOption('1', _AM_SYSTEM_MENUS_TARGET_BLANK);
        $form->addElement($targetEl);

        $form->addElement(new \XoopsFormText(
            _AM_SYSTEM_MENUS_CATPOSITION,
            'category_position',
            5,
            10,
            (string) $this->getVar('category_position')
        ));
        $form->addElement(new \XoopsFormRadioYN(
            _AM_SYSTEM_MENUS_ACTIVE,
            'category_active',
            (int) $this->getVar('category_active')
        ));

        $permEl = new \XoopsGroupPermForm('', $GLOBALS['xoopsModule']->getVar('mid'), 'menus_category_view', '');
        $permEl->addItem((int) $this->getVar('category_id'), _AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY);
        $form->addElement($permEl);

        $form->addElement(new \XoopsFormHidden('op', 'savecat'));
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

    /**
     * Build a textarea element for a prefix/suffix field.
     *
     * @param string $varName     The variable name
     * @param string $label       The form label
     * @param bool   $isProtected Whether the field should be readonly
     *
     * @return \XoopsFormTextArea
     */
    private function buildAffixField(string $varName, string $label, bool $isProtected): \XoopsFormTextArea
    {
        $value = (string) $this->getVar($varName, 'n');
        $el    = new \XoopsFormTextArea($label, $varName, $value, 3, 60);
        if ($isProtected) {
            $el->setExtra('readonly="readonly"');
        }
        return $el;
    }
}

/**
 * Handler for XoopsMenusCategory objects.
 *
 * Note: Constructor accepts \XoopsDatabase to match parent XoopsPersistableObjectHandler
 * signature. At runtime, the concrete XoopsMySQLDatabase is always passed.
 */
class XoopsMenusCategoryHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @param \XoopsDatabase $db Database connection
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'menuscategory', 'XoopsMenusCategory', 'category_id', 'category_title');
    }
}
