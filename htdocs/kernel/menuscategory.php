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
     * Return the auto-generated primary key from the most recent insert.
     *
     * @return int Newly assigned category id
     */
    public function getNewEnreg(): int
    {
        return (int) \XoopsDatabaseFactory::getDatabaseConnection()->getInsertId();
    }

    /**
     * Load menu language files if not already loaded.
     *
     * Uses the standard xoops_loadLanguage() helper, which automatically
     * falls back to English when a translation is not available.
     */
    private static function ensureLanguageLoaded(): void
    {
        if (self::$langLoaded) {
            return;
        }
        xoops_loadLanguage('menus/menus', 'system');
        xoops_loadLanguage('admin/menus', 'system');
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
        $titleEl->setDescription(_AM_SYSTEM_MENUS_CATTITLE_DESC);
        if ($isProtected) {
            $titleEl->setExtra('readonly="readonly"');
        }
        $form->addElement($titleEl, true);

        $prefixEl = $this->buildAffixField('category_prefix', _AM_SYSTEM_MENUS_CATPREFIX, $isProtected);
        $prefixEl->setDescription(_AM_SYSTEM_MENUS_CATPREFIX_DESC);
        $form->addElement($prefixEl);

        $suffixEl = $this->buildAffixField('category_suffix', _AM_SYSTEM_MENUS_CATSUFFIX, $isProtected);
        $suffixEl->setDescription(_AM_SYSTEM_MENUS_CATSUFFIX_DESC);
        $form->addElement($suffixEl);

        $urlEl = new \XoopsFormText(
            _AM_SYSTEM_MENUS_CATURL,
            'category_url',
            60,
            255,
            $this->getVar('category_url', 'e')
        );
        $urlEl->setDescription(_AM_SYSTEM_MENUS_CATURL_DESC);
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
        $permEl->setDescription(_AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY_DESC);
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

    /**
     * Fetch active categories from a set of allowed IDs, sorted by position.
     *
     * Used by the theme loader after permission filtering to retrieve
     * only the categories visible to the current user.
     *
     * @param int[] $categoryIds Category IDs the current user may see
     *
     * @return array<int, XoopsMenusCategory>
     */
    public function getActiveCategoriesByIds(array $categoryIds): array
    {
        $categoryIds = array_values(array_filter(array_map('intval', $categoryIds)));
        if ([] === $categoryIds) {
            return [];
        }

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('category_active', '1'));
        $criteria->add(new \Criteria('category_id', $categoryIds, 'IN'));
        $criteria->setSort('category_position');
        $criteria->setOrder('ASC');

        return $this->getAll($criteria);
    }
}
