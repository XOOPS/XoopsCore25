<?php
/**
 * XOOPS Menu Items Kernel Object
 *
 * @category  Kernel
 * @author    XOOPS Core Team
 * @copyright 2001-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2+ (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A single menu item within a category.
 */
class XoopsMenusItems extends XoopsObject
{
    /** @var bool Whether the language file has been loaded */
    private static bool $langLoaded = false;

    public function __construct()
    {
        parent::__construct();
        $this->initVar('items_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('items_pid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('items_cid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('items_title', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('items_prefix', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('items_suffix', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('items_url', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('items_target', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('items_position', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('items_protected', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('items_active', XOBJ_DTYPE_INT, 1, false);
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
        $raw = $this->getVar('items_title', 'n');
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
        $raw      = (string) $this->getVar('items_title', 'n');
        $resolved = $this->getResolvedTitle();
        if ($resolved !== $raw) {
            return $resolved . ' (' . $raw . ')';
        }
        return $resolved;
    }

    /**
     * Build the admin edit form for this item.
     *
     * @param int    $categoryId The category this item belongs to
     * @param string $action     Form action URL
     *
     * @return \XoopsThemeForm
     */
    public function getFormItems(int $categoryId, string $action): \XoopsThemeForm
    {
        $isEdit      = (bool) $this->getVar('items_id');
        $isProtected = (bool) $this->getVar('items_protected');
        $title       = $isEdit ? _AM_SYSTEM_MENUS_EDITITEM : _AM_SYSTEM_MENUS_ADDITEM;

        $form = new \XoopsThemeForm($title, 'itemform', $action, 'post', true);

        if ($isEdit) {
            $form->addElement(new \XoopsFormHidden('items_id', (string) $this->getVar('items_id')));
        }
        $form->addElement(new \XoopsFormHidden('items_cid', (string) $categoryId));

        $catHandler = xoops_getHandler('menuscategory');
        $catObj     = $catHandler->get($categoryId);
        if ($catObj) {
            $form->addElement(new \XoopsFormLabel(
                _AM_SYSTEM_MENUS_ITEMCATEGORY,
                htmlspecialchars($catObj->getAdminTitle(), ENT_QUOTES, 'UTF-8')
            ));
        }

        $form->addElement($this->buildParentSelector($categoryId, $isProtected));

        $titleEl = new \XoopsFormText(
            _AM_SYSTEM_MENUS_ITEMTITLE,
            'items_title',
            60,
            100,
            $this->getVar('items_title', 'e')
        );
        if ($isProtected) {
            $titleEl->setExtra('readonly="readonly"');
        }
        $form->addElement($titleEl, true);

        $form->addElement($this->buildAffixField('items_prefix', _AM_SYSTEM_MENUS_ITEMPREFIX, $isProtected));
        $form->addElement($this->buildAffixField('items_suffix', _AM_SYSTEM_MENUS_ITEMSUFFIX, $isProtected));

        $urlEl = new \XoopsFormText(
            _AM_SYSTEM_MENUS_ITEMURL,
            'items_url',
            60,
            255,
            $this->getVar('items_url', 'e')
        );
        if ($isProtected) {
            $urlEl->setExtra('readonly="readonly"');
        }
        $form->addElement($urlEl);

        $targetEl = new \XoopsFormRadio(
            _AM_SYSTEM_MENUS_ITEMTARGET,
            'items_target',
            (string) $this->getVar('items_target')
        );
        $targetEl->addOption('0', _AM_SYSTEM_MENUS_TARGET_SELF);
        $targetEl->addOption('1', _AM_SYSTEM_MENUS_TARGET_BLANK);
        $form->addElement($targetEl);

        $form->addElement(new \XoopsFormText(
            _AM_SYSTEM_MENUS_ITEMPOSITION,
            'items_position',
            5,
            10,
            (string) $this->getVar('items_position')
        ));
        $form->addElement(new \XoopsFormRadioYN(
            _AM_SYSTEM_MENUS_ACTIVE,
            'items_active',
            (int) $this->getVar('items_active')
        ));

        $permEl = new \XoopsGroupPermForm('', $GLOBALS['xoopsModule']->getVar('mid'), 'menus_items_view', '');
        $permEl->addItem((int) $this->getVar('items_id'), _AM_SYSTEM_MENUS_PERMISSION_VIEW_ITEM);
        $form->addElement($permEl);

        $form->addElement(new \XoopsFormHidden('op', 'saveitem'));
        $form->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

    /**
     * Build a parent-item dropdown for the given category.
     *
     * @param int  $categoryId  The category to list items from
     * @param bool $isProtected Whether the selector should be disabled
     *
     * @return \XoopsFormSelect
     */
    private function buildParentSelector(int $categoryId, bool $isProtected): \XoopsFormSelect
    {
        $currentId = (int) $this->getVar('items_id');
        $handler   = xoops_getHandler('menusitems');
        $criteria  = new \CriteriaCompo(new \Criteria('items_cid', (string) $categoryId));
        $criteria->setSort('items_position');
        $criteria->setOrder('ASC');
        $allItems = $handler->getObjects($criteria);

        $options = [0 => '---'];
        foreach ($allItems as $sibling) {
            $sid = (int) $sibling->getVar('items_id');
            if ($sid !== $currentId) {
                $options[$sid] = $sibling->getAdminTitle();
            }
        }

        $el = new \XoopsFormSelect(
            _AM_SYSTEM_MENUS_ITEMPARENT,
            'items_pid',
            (string) $this->getVar('items_pid'),
            1,
            false
        );
        $el->addOptionArray($options);
        if ($isProtected) {
            $el->setExtra('disabled="disabled"');
        }
        return $el;
    }

    /**
     * Build a textarea element for prefix/suffix.
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
 * Handler for XoopsMenusItems objects.
 *
 * Note: Constructor accepts \XoopsDatabase to match parent XoopsPersistableObjectHandler
 * signature. At runtime, the concrete XoopsMySQLDatabase is always passed.
 */
class XoopsMenusItemsHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @param \XoopsDatabase $db Database connection
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'menusitems', 'XoopsMenusItems', 'items_id', 'items_title');
    }
}
