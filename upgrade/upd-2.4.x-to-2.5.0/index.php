<?php
/**
 * Upgrader from to 2.4.x to 2.5.0
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             upgrader
 * @since               2.5.0
 * @author              Andricq Nicolas (AKA MusS)
 */

require_once __DIR__ . '/dbmanager.php';

/**
 * Class upgrade_250
 */
class Upgrade_250 extends XoopsUpgrade
{
    /**
     * Check if cpanel config already exists
     *
     */
    public function check_config()
    {
        $sql = 'SELECT COUNT(*) FROM `' . $GLOBALS['xoopsDB']->prefix('config') . "` WHERE `conf_name` IN ('break1', 'usetips')";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
            return false;
        }
        list($count) = $GLOBALS['xoopsDB']->fetchRow($result);

        return ($count != 0);
    }

    /**
     * @return bool
     */
    public function check_templates()
    {
        $sql = 'SELECT COUNT(*) FROM `' . $GLOBALS['xoopsDB']->prefix('tplfile') . "` WHERE `tpl_file` IN ('system_header.html', 'system_header.tpl') AND `tpl_type` = 'admin'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
            return false;
        }
        list($count) = $GLOBALS['xoopsDB']->fetchRow($result);

        return ($count != 0);
    }

    /**
     * @return bool
     */
    public function apply_config()
    {
        $dbm = new Db_manager();

        $sql = 'SELECT conf_id FROM `' . $GLOBALS['xoopsDB']->prefix('config') . "` WHERE `conf_name` IN ('cpanel')";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
            return false;
        }
        $count = $GLOBALS['xoopsDB']->fetchRow($result);

        $sql = 'UPDATE `' . $GLOBALS['xoopsDB']->prefix('config') . "` SET `conf_value` = 'default' WHERE `conf_id` = " . $count[0];
        if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
            return false;
        }

        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'break1', '_MI_SYSTEM_PREFERENCE_BREAK_GENERAL', 'head', '', 'line_break', 'textbox', 0)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'usetips', '_MI_SYSTEM_PREFERENCE_TIPS', '1', '_MI_SYSTEM_PREFERENCE_TIPS_DSC', 'yesno', 'int', 10)");
        $icon_id = $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'typeicons', '_MI_SYSTEM_PREFERENCE_ICONS', 'default', '', 'select', 'text', 20)");
        $bc_id   = $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'typebreadcrumb', '_MI_SYSTEM_PREFERENCE_BREADCRUMB', 'default', '', 'select', 'text', 30)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'break2', '_MI_SYSTEM_PREFERENCE_BREAK_ACTIVE', 'head', '', 'line_break', 'textbox', 40)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_avatars', '_MI_SYSTEM_PREFERENCE_ACTIVE_AVATARS', '1', '', 'yesno', 'int', 50)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_banners', '_MI_SYSTEM_PREFERENCE_ACTIVE_BANNERS', '1', '', 'yesno', 'int', 60)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_blocksadmin', '_MI_SYSTEM_PREFERENCE_ACTIVE_BLOCKSADMIN', '1', '', 'hidden', 'int', 70)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_comments', '_MI_SYSTEM_PREFERENCE_ACTIVE_COMMENTS', '1', '', 'yesno', 'int', 80)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_filemanager', '_MI_SYSTEM_PREFERENCE_ACTIVE_FILEMANAGER', '1', '', 'yesno', 'int', 90)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_groups', '_MI_SYSTEM_PREFERENCE_ACTIVE_GROUPS', '1', '', 'hidden', 'int', 100)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_images', '_MI_SYSTEM_PREFERENCE_ACTIVE_IMAGES', '1', '', 'yesno', 'int', 110)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_mailusers', '_MI_SYSTEM_PREFERENCE_ACTIVE_MAILUSERS', '1', '', 'yesno', 'int', 120)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_modulesadmin', '_MI_SYSTEM_PREFERENCE_ACTIVE_MODULESADMIN', '1', '', 'hidden', 'int', 130)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_maintenance', '_MI_SYSTEM_PREFERENCE_ACTIVE_MAINTENANCE', '1', '', 'yesno', 'int', 140)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_preferences', '_MI_SYSTEM_PREFERENCE_ACTIVE_PREFERENCES', '1', '', 'hidden', 'int', 150)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_smilies', '_MI_SYSTEM_PREFERENCE_ACTIVE_SMILIES', '1', '', 'yesno', 'int', 160)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_tplsets', '_MI_SYSTEM_PREFERENCE_ACTIVE_TPLSETS', '1', '', 'hidden', 'int', 170)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_userrank', '_MI_SYSTEM_PREFERENCE_ACTIVE_USERRANK', '1', '', 'yesno', 'int', 180)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'active_users', '_MI_SYSTEM_PREFERENCE_ACTIVE_USERS', '1', '', 'yesno', 'int', 190)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'break3', '_MI_SYSTEM_PREFERENCE_BREAK_PAGER', 'head', '', 'line_break', 'textbox', 200)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'avatars_pager', '_MI_SYSTEM_PREFERENCE_AVATARS_PAGER', '10', '', 'textbox', 'int', 210)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'banners_pager', '_MI_SYSTEM_PREFERENCE_BANNERS_PAGER', '10', '', 'textbox', 'int', 220)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'comments_pager', '_MI_SYSTEM_PREFERENCE_COMMENTS_PAGER', '20', '', 'textbox', 'int', 230)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'groups_pager', '_MI_SYSTEM_PREFERENCE_GROUPS_PAGER', '15', '', 'textbox', 'int', 240)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'images_pager', '_MI_SYSTEM_PREFERENCE_IMAGES_PAGER', '15', '', 'textbox', 'int', 250)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'smilies_pager', '_MI_SYSTEM_PREFERENCE_SMILIES_PAGER', '20', '', 'textbox', 'int', 260)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'userranks_pager', '_MI_SYSTEM_PREFERENCE_USERRANKS_PAGER', '20', '', 'textbox', 'int', 270)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'users_pager', '_MI_SYSTEM_PREFERENCE_USERS_PAGER', '20', '', 'textbox', 'int', 280)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'break4', '_MI_SYSTEM_PREFERENCE_BREAK_EDITOR', 'head', '', 'line_break', 'textbox', 290)");
        $block_id = $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'blocks_editor', '_MI_SYSTEM_PREFERENCE_BLOCKS_EDITOR', 'dhtmltextarea', '_MI_SYSTEM_PREFERENCE_BLOCKS_EDITOR_DSC', 'select', 'text', 300)");
        $com_id   = $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'comments_editor', '_MI_SYSTEM_PREFERENCE_COMMENTS_EDITOR', 'dhtmltextarea', '_MI_SYSTEM_PREFERENCE_COMMENTS_EDITOR_DSC', 'select', 'text', 310)");
        $main_id  = $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'general_editor', '_MI_SYSTEM_PREFERENCE_GENERAL_EDITOR', 'dhtmltextarea', '_MI_SYSTEM_PREFERENCE_GENERAL_EDITOR_DSC', 'select', 'text', 320)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'redirect', '_MI_SYSTEM_PREFERENCE_REDIRECT', 'admin.php?fct=preferences', '', 'hidden', 'text', 330)");
        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'com_anonpost', '_MI_SYSTEM_PREFERENCE_ANONPOST', '', '', 'hidden', 'text', 340)");
        $jquery_id = $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (1, 0, 'jquery_theme', '_MI_SYSTEM_PREFERENCE_JQUERY_THEME', 'base', '', 'select', 'text', 35)");

        $dbm->insert('config', " (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) VALUES (0, 1, 'redirect_message_ajax', '_MD_AM_CUSTOM_REDIRECT', '1', '_MD_AM_CUSTOM_REDIRECT_DESC', 'yesno', 'int', 12)");

        require_once __DIR__ . '/../class/xoopslists.php';
        $editors = XoopsLists::getDirListAsArray('../class/xoopseditor');
        foreach ($editors as $dir) {
            $dbm->insert('configoption', " (confop_name,confop_value,conf_id) VALUES ('" . $dir . "', '" . $dir . "', $block_id)");
        }
        foreach ($editors as $dir) {
            $dbm->insert('configoption', " (confop_name,confop_value,conf_id) VALUES ('" . $dir . "', '" . $dir . "', $com_id)");
        }
        foreach ($editors as $dir) {
            $dbm->insert('configoption', " (confop_name,confop_value,conf_id) VALUES ('" . $dir . "', '" . $dir . "', $main_id)");
        }
        $icons = XoopsLists::getDirListAsArray('../modules/system/images/icons');
        foreach ($icons as $dir) {
            $dbm->insert('configoption', " (confop_name,confop_value,conf_id) VALUES ('" . $dir . "', '" . $dir . "', $icon_id)");
        }
        $breadcrumb = XoopsLists::getDirListAsArray('../modules/system/images/breadcrumb');
        foreach ($breadcrumb as $dir) {
            $dbm->insert('configoption', " (confop_name,confop_value,conf_id) VALUES ('" . $dir . "', '" . $dir . "', $bc_id)");
        }
        $jqueryui = XoopsLists::getDirListAsArray('../modules/system/css/ui');
        foreach ($jqueryui as $dir) {
            $dbm->insert('configoption', " (confop_name,confop_value,conf_id) VALUES ('" . $dir . "', '" . $dir . "', $jquery_id)");
        }

        return true;
    }

    /**
     * @return bool
     */
    public function apply_templates()
    {
        include_once __DIR__ . '/../modules/system/xoops_version.php';

        $dbm  = new Db_manager();
        $time = time();
        foreach ($modversion['templates'] as $tplfile) {
            // Admin templates
            if (isset($tplfile['type']) && $tplfile['type'] === 'admin' && $fp = fopen('../modules/system/templates/admin/' . $tplfile['file'], 'r')) {
                $newtplid  = $dbm->insert('tplfile', " VALUES (0, 1, 'system', 'default', '" . addslashes($tplfile['file']) . "', '" . addslashes($tplfile['description']) . "', " . $time . ', ' . $time . ", 'admin')");
                $tplsource = fread($fp, filesize('../modules/system/templates/admin/' . $tplfile['file']));
                fclose($fp);
                $dbm->insert('tplsource', ' (tpl_id, tpl_source) VALUES (' . $newtplid . ", '" . addslashes($tplsource) . "')");
            }
        }

        return true;
    }

    /**
     * Identify a block mangled in the change from XOOPS 2.4.x to 2.5.0
     *
     * The user menu block was element 1 in the old $modversion['blocks'] array, but
     * became element 0 in 2.5.0. This results in the old block not being updated with
     * new settings. We see it in 2.5.8+ where the theme overrides fail because of the
     * extension change from .html to .tpl.
     *
     * Installs are not a problem, just upgrades. This is the only block affected.
     *
     * @return CriteriaElement
     */
    private function strayblockCriteria()
    {
        $criteria = new CriteriaCompo(new Criteria('mid','1', '='));
        $criteria->add(new Criteria('block_type','S', '='));
        $criteria->add(new Criteria('func_num','1', '='));
        $criteria->add(new Criteria('template','system_block_user.html', '='));
        return $criteria;
    }

    /**
     * @return bool
     */
    public function check_strayblock()
    {
        $criteria = $this->strayblockCriteria();
        $count = Xmf\Database\TableLoad::countRows('newblocks', $criteria);

        return ($count === 0);
    }

    /**
     * @return bool
     */
    public function apply_strayblock()
    {
        $criteria = $this->strayblockCriteria();
        $tables = new Xmf\Database\Tables();
        $tables->useTable('newblocks');
        $tables->update('newblocks', array('func_num' => '0'), $criteria);

        return $tables->executeQueue(true);
    }

    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('config', 'templates', 'strayblock');
    }
}

$upg = new Upgrade_250();
return $upg;
