<?php
/**
 * Group class manager
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

include_once XOOPS_ROOT_PATH . '/kernel/group.php';

/**
 * System Group
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 */
class SystemGroup extends XoopsGroup
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
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

        if ($this->isNew()) {
            $s_cat_value   = '';
            $a_mod_value   = array();
            $r_mod_value   = array();
            $r_block_value = array();
        } else {
            /* @var XoopsGroupPermHandler $sysperm_handler */
            $sysperm_handler    = xoops_getHandler('groupperm');
            $s_cat_value        = $sysperm_handler->getItemIds('system_admin', $this->getVar('groupid'));
            /* @var XoopsMemberHandler $member_handler */
            $member_handler     = xoops_getHandler('member');
            $thisgroup          = $member_handler->getGroup($this->getVar('groupid'));
            /* @var XoopsGroupPermHandler $moduleperm_handler */
            $moduleperm_handler = xoops_getHandler('groupperm');
            $a_mod_value        = $moduleperm_handler->getItemIds('module_admin', $thisgroup->getVar('groupid'));
            $r_mod_value        = $moduleperm_handler->getItemIds('module_read', $thisgroup->getVar('groupid'));
            /* @var  XoopsGroupPermHandler $gperm_handler */
            $gperm_handler      = xoops_getHandler('groupperm');
            $r_block_value      = $gperm_handler->getItemIds('block_read', $this->getVar('groupid'));
        }
        xoops_load('XoopsFormLoader');
        xoops_load('XoopsLists');
        include_once XOOPS_ROOT_PATH . '/modules/system/constants.php';

        $title = $this->isNew() ? sprintf(_AM_SYSTEM_GROUPS_ADD) : sprintf(_AM_SYSTEM_GROUPS_EDIT);
        $form  = new XoopsThemeForm($title, 'groupform', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $name_text = new XoopsFormText(_AM_SYSTEM_GROUPS_NAME, 'name', 30, 50, $this->getVar('name'));
        $desc_text = new XoopsFormTextArea(_AM_SYSTEM_GROUPS_DESCRIPTION, 'desc', $this->getVar('description'));

        $system_catids = new XoopsFormElementTray(_AM_SYSTEM_GROUPS_SYSTEMRIGHTS, '');

        $s_cat_checkbox_all = new XoopsFormCheckBox('', 'catbox', 1);
        $s_cat_checkbox_all->addOption('allbox', _AM_SYSTEM_ALL);
        $s_cat_checkbox_all->setExtra(" onclick='xoopsCheckGroup(\"groupform\", \"catbox\" , \"system_catids[]\");' ");
        $s_cat_checkbox_all->setClass('xo-checkall');
        $system_catids->addElement($s_cat_checkbox_all);

        $s_cat_checkbox          = new XoopsFormCheckBox('', 'system_catids', $s_cat_value);
        $s_cat_checkbox->columns = 6;
        $admin_dir               = XOOPS_ROOT_PATH . '/modules/system/admin/';
        $dirlist                 = XoopsLists::getDirListAsArray($admin_dir);
        foreach ($dirlist as $file) {
            include XOOPS_ROOT_PATH . '/modules/system/admin/' . $file . '/xoops_version.php';
            if (!empty($modversion['category'])) {
                if (xoops_getModuleOption('active_' . $file, 'system') == 1) {
                    $s_cat_checkbox->addOption($modversion['category'], $modversion['name']);
                }
            }
            unset($modversion);
        }
        unset($dirlist);
        $system_catids->addElement($s_cat_checkbox);

        $admin_mids = new XoopsFormElementTray(_AM_SYSTEM_GROUPS_ACTIVERIGHTS, '');

        $s_admin_checkbox_all = new XoopsFormCheckBox('', 'adminbox', 1);
        $s_admin_checkbox_all->addOption('allbox', _AM_SYSTEM_ALL);
        $s_admin_checkbox_all->setExtra(" onclick='xoopsCheckGroup(\"groupform\", \"adminbox\" , \"admin_mids[]\");' ");
        $s_admin_checkbox_all->setClass('xo-checkall');
        $admin_mids->addElement($s_admin_checkbox_all);

        $a_mod_checkbox          = new XoopsFormCheckBox('', 'admin_mids[]', $a_mod_value);
        $a_mod_checkbox->columns = 5;
        /* @var XoopsModuleHandler $module_handler */
        $module_handler          = xoops_getHandler('module');
        $criteria                = new CriteriaCompo(new Criteria('hasadmin', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('dirname', 'system', '<>'));
        $a_mod_checkbox->addOptionArray($module_handler->getList($criteria));
        $admin_mids->addElement($a_mod_checkbox);

        $read_mids = new XoopsFormElementTray(_AM_SYSTEM_GROUPS_ACCESSRIGHTS, '');

        $s_mod_checkbox_all = new XoopsFormCheckBox('', 'readbox', 1);
        $s_mod_checkbox_all->addOption('allbox', _AM_SYSTEM_ALL);
        $s_mod_checkbox_all->setExtra(" onclick='xoopsCheckGroup(\"groupform\", \"readbox\" , \"read_mids[]\");' ");
        $s_mod_checkbox_all->setClass('xo-checkall');
        $read_mids->addElement($s_mod_checkbox_all);

        $r_mod_checkbox          = new XoopsFormCheckBox('', 'read_mids[]', $r_mod_value);
        $r_mod_checkbox->columns = 5;
        $criteria                = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        $r_mod_checkbox->addOptionArray($module_handler->getList($criteria));
        $read_mids->addElement($r_mod_checkbox);

        $criteria = new CriteriaCompo(new Criteria('isactive', 1));
        $criteria->setSort('mid');
        $criteria->setOrder('ASC');
        $module_list    = $module_handler->getList($criteria);
        $module_list[0] = _AM_SYSTEM_GROUPS_CUSTOMBLOCK;
        /* @var XoopsBlockHandler $block_handler */
        $block_handler = xoops_getHandler('block');
        $blocks_obj    = $block_handler->getObjects(new Criteria('mid', "('" . implode("', '", array_keys($module_list)) . "')", 'IN'), true);

        $blocks_module = array();
        foreach (array_keys($blocks_obj) as $bid) {
            $title                                                                             = $blocks_obj[$bid]->getVar('title');
            $blocks_module[$blocks_obj[$bid]->getVar('mid')][$blocks_obj[$bid]->getVar('bid')] = empty($title) ? $blocks_obj[$bid]->getVar('name') : $title;
        }
        ksort($blocks_module);

        $r_block_tray   = new XoopsFormElementTray(_AM_SYSTEM_GROUPS_BLOCKRIGHTS, '<br><br>');
        $s_checkbox_all = new XoopsFormCheckBox('', 'blocksbox', 1);
        $s_checkbox_all->addOption('allbox', _AM_SYSTEM_ALL);
        $s_checkbox_all->setExtra(" onclick='xoopsCheckGroup(\"groupform\", \"blocksbox\" , \"read_bids[]\");' ");
        $s_checkbox_all->setClass('xo-checkall');
        $r_block_tray->addElement($s_checkbox_all);
        foreach (array_keys($blocks_module) as $mid) {
            $new_blocks_array = array();
            foreach ($blocks_module[$mid] as $key => $value) {
                $new_blocks_array[$key] = "<a href='" . XOOPS_URL . "/modules/system/admin.php?fct=blocksadmin&amp;op=edit&amp;bid={$key}' title='ID: {$key}' rel='external'>{$value}</a>";
            }
            $r_block_checkbox          = new XoopsFormCheckBox('<strong>' . $module_list[$mid] . '</strong><br>', 'read_bids[]', $r_block_value);
            $r_block_checkbox->columns = 5;
            $r_block_checkbox->addOptionArray($new_blocks_array);
            $r_block_tray->addElement($r_block_checkbox);
            unset($r_block_checkbox);
        }
        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('g_id', $this->getVar('groupid')));
            $form->addElement(new XoopsFormHidden('op', 'groups_save_update'));
        } else {
            $form->addElement(new XoopsFormHidden('op', 'groups_save_add'));
        }
        $form->addElement($name_text, true);
        $form->addElement($desc_text);
        $form->addElement($system_catids);
        $form->addElement($admin_mids);
        $form->addElement($read_mids);
        $form->addElement($r_block_tray);
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }
}

/**
 * System group handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             system
 * @subpackage          avatar
 */
class SystemGroupHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'groups', 'SystemGroup', 'groupid', 'name');
    }
}
