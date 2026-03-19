<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * XOOPS Menu Category Kernel Object
 * @copyright    2000-2026 XOOPS Project https://xoops.org/
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since        2.5.12
 * @author       XOOPS Development Team
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

        $titleField = new \XoopsFormText(
            _AM_SYSTEM_MENUS_CATTITLE,
            'category_title',
            60,
            100,
            $this->getVar('category_title', 'e')
        );
        $titleField->setDescription(_AM_SYSTEM_MENUS_CATTITLE_DESC);
        if ($isProtected) {
            $titleField->setExtra('readonly="readonly"');
        }
        $form->addElement($titleField, true);

        $prefixField = $this->buildAffixField('category_prefix', _AM_SYSTEM_MENUS_CATPREFIX, $isProtected);
        $prefixField->setDescription(_AM_SYSTEM_MENUS_CATPREFIX_DESC);
        $form->addElement($prefixField);

        $suffixField = $this->buildAffixField('category_suffix', _AM_SYSTEM_MENUS_CATSUFFIX, $isProtected);
        $suffixField->setDescription(_AM_SYSTEM_MENUS_CATSUFFIX_DESC);
        $form->addElement($suffixField);

        $urlField = new \XoopsFormText(
            _AM_SYSTEM_MENUS_CATURL,
            'category_url',
            60,
            255,
            $this->getVar('category_url', 'e')
        );
        $urlField->setDescription(_AM_SYSTEM_MENUS_CATURL_DESC);
        if ($isProtected) {
            $urlField->setExtra('readonly="readonly"');
        }
        $form->addElement($urlField);

        $targetField = new \XoopsFormRadio(
            _AM_SYSTEM_MENUS_CATTARGET,
            'category_target',
            (string) $this->getVar('category_target')
        );
        $targetField->addOption('0', _AM_SYSTEM_MENUS_TARGET_SELF);
        $targetField->addOption('1', _AM_SYSTEM_MENUS_TARGET_BLANK);
        $form->addElement($targetField);

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

        $permField = new \XoopsGroupPermForm('', $GLOBALS['xoopsModule']->getVar('mid'), 'menus_category_view', '');
        $permField->addItem((int) $this->getVar('category_id'), _AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY);
        $permField->setDescription(_AM_SYSTEM_MENUS_PERMISSION_VIEW_CATEGORY_DESC);
        $form->addElement($permField);

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
        $field   = new \XoopsFormTextArea($label, $varName, $value, 3, 60);
        if ($isProtected) {
            $field->setExtra('readonly="readonly"');
        }
        return $field;
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
