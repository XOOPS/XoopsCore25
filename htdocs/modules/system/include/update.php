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
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

/**
 * @param XoopsModule $module
 * @param string|null $prev_version
 *
 * @return bool|null
 */
function xoops_module_update_system(XoopsModule $module, $prev_version = null)
{
    // irmtfan bug fix: solve templates duplicate issue
    $ret = null;
    if ($prev_version < '2.1.1') {
        $ret = update_system_v211($module);
    }
    // Clean up legacy .html template rows replaced by .tpl equivalents
    if ($prev_version < '2.1.8') {
        update_system_remove_legacy_html_templates($module);
    }
    // Create menu management tables and seed default data
    if ($prev_version < '2.1.9') {
        update_system_v219_menus($module);
    }
    $errors = $module->getErrors();
    if (!empty($errors)) {
        print_r($errors);
    } else {
        $ret = true;
    }

    return $ret;
    // irmtfan bug fix: solve templates duplicate issue
}

// irmtfan bug fix: solve templates duplicate issue
/**
 * @param XoopsModule $module
 *
 * @return bool
 */
function update_system_v211($module)
{
    global $xoopsDB;
    $sql = 'SELECT t1.tpl_id FROM ' . $xoopsDB->prefix('tplfile') . ' t1, ' . $xoopsDB->prefix('tplfile') . ' t2 WHERE t1.tpl_refid = t2.tpl_refid AND t1.tpl_module = t2.tpl_module AND t1.tpl_tplset=t2.tpl_tplset AND t1.tpl_file = t2.tpl_file AND t1.tpl_type = t2.tpl_type AND t1.tpl_id > t2.tpl_id';
    $result = $xoopsDB->query($sql);
    if (!$xoopsDB->isResultSet($result) || !($result instanceof \mysqli_result)) {
        throw new \RuntimeException(
            \sprintf(_DB_QUERY_ERROR, $sql) . $xoopsDB->error(),
            E_USER_ERROR,
        );
    }
    $tplids = [];
    while (false !== ($row = $xoopsDB->fetchRow($result))) {
        [$tplid] = $row;
        $tplids[] = $tplid;
    }
    if (count($tplids) > 0) {
        $tplfile_handler = xoops_getHandler('tplfile');
        $duplicate_files = $tplfile_handler->getObjects(new Criteria('tpl_id', '(' . implode(',', $tplids) . ')', 'IN'));

        if (count($duplicate_files) > 0) {
            foreach (array_keys($duplicate_files) as $i) {
                $tplfile_handler->delete($duplicate_files[$i]);
            }
        }
    }
    $sql = 'SHOW INDEX FROM ' . $xoopsDB->prefix('tplfile') . " WHERE KEY_NAME = 'tpl_refid_module_set_file_type'";
    $result = $xoopsDB->query($sql);
    if (!$xoopsDB->isResultSet($result) || !($result instanceof \mysqli_result)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);

        return false;
    }
    $ret = [];
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $ret[] = $myrow;
    }
    if (!empty($ret)) {
        $module->setErrors("'tpl_refid_module_set_file_type' unique index is exist. Note: check 'tplfile' table to be sure this index is UNIQUE because XOOPS CORE need it.");

        return true;
    }
    $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tplfile') . ' ADD UNIQUE tpl_refid_module_set_file_type ( tpl_refid, tpl_module, tpl_tplset, tpl_file, tpl_type )';
    if (!$result = $xoopsDB->exec($sql)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);
        $module->setErrors("'tpl_refid_module_set_file_type' unique index is not added to 'tplfile' table. Warning: do not use XOOPS until you add this unique index.");

        return false;
    }

    return true;
}
// irmtfan bug fix: solve templates duplicate issue

/**
 * Remove legacy .html template DB rows that have been superseded by .tpl equivalents.
 *
 * Previous versions registered both .html and .tpl template files. The .html
 * registrations have been removed from xoops_version.php, but upgraded sites
 * may still have stale .html rows in the tplfile table. This cleans them up
 * only where a matching .tpl row already exists.
 *
 * @param XoopsModule $module
 */
function update_system_remove_legacy_html_templates(XoopsModule $module)
{
    global $xoopsDB;
    $dirname = $xoopsDB->escape($module->getVar('dirname', 'n'));

    // Only remove .html rows where a .tpl equivalent exists in the same tplset
    $sql = 'DELETE t1 FROM ' . $xoopsDB->prefix('tplfile') . ' t1'
         . ' INNER JOIN ' . $xoopsDB->prefix('tplfile') . ' t2'
         . ' ON t2.tpl_refid = t1.tpl_refid'
         . ' AND t2.tpl_module = t1.tpl_module'
         . ' AND t2.tpl_tplset = t1.tpl_tplset'
         . ' AND t2.tpl_type = t1.tpl_type'
         . " AND t2.tpl_file = REPLACE(t1.tpl_file, '.html', '.tpl')"
         . " WHERE t1.tpl_module = '" . $dirname . "'"
         . " AND t1.tpl_file LIKE '%.html'"
         . " AND t2.tpl_file LIKE '%.tpl'";

    $xoopsDB->exec($sql);
}

/**
 * Create menu management tables and seed default data.
 *
 * @param XoopsModule $module
 */
function update_system_v219_menus(XoopsModule $module)
{
    global $xoopsDB;

    $mid = $module->getVar('mid');

    // Create menuscategory table
    $sql = "CREATE TABLE IF NOT EXISTS " . $xoopsDB->prefix('menuscategory') . " (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        category_title VARCHAR(100) NOT NULL,
        category_prefix TEXT NOT NULL,
        category_suffix TEXT NOT NULL,
        category_url VARCHAR(255) NULL,
        category_target TINYINT(1) DEFAULT 0,
        category_position INT DEFAULT 0,
        category_protected INT DEFAULT 0,
        category_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB";
    $xoopsDB->exec($sql);

    // Create menusitems table
    $sql = "CREATE TABLE IF NOT EXISTS " . $xoopsDB->prefix('menusitems') . " (
        items_id INT AUTO_INCREMENT PRIMARY KEY,
        items_pid INT NULL,
        items_cid INT NULL,
        items_title VARCHAR(100) NOT NULL,
        items_prefix TEXT NOT NULL,
        items_suffix TEXT NOT NULL,
        items_url VARCHAR(255) NULL,
        items_target TINYINT(1) DEFAULT 0,
        items_position INT DEFAULT 0,
        items_protected INT DEFAULT 0,
        items_active TINYINT(1) DEFAULT 1,
        FOREIGN KEY (items_cid) REFERENCES " . $xoopsDB->prefix('menuscategory') . "(category_id) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    $xoopsDB->exec($sql);

    // Drop self-referencing FK on items_pid if it exists (XOOPS uses 0 for "no parent")
    $table = $xoopsDB->prefix('menusitems');
    $result = $xoopsDB->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE"
        . " WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}'"
        . " AND COLUMN_NAME = 'items_pid' AND REFERENCED_TABLE_NAME IS NOT NULL");
    if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $xoopsDB->exec("ALTER TABLE {$table} DROP FOREIGN KEY `{$row['CONSTRAINT_NAME']}`");
        }
    }

    // Widen affix columns from VARCHAR(100) to TEXT for existing installs
    $catTable = $xoopsDB->prefix('menuscategory');
    $xoopsDB->exec("ALTER TABLE {$catTable} MODIFY category_prefix TEXT NOT NULL");
    $xoopsDB->exec("ALTER TABLE {$catTable} MODIFY category_suffix TEXT NOT NULL");
    $xoopsDB->exec("ALTER TABLE {$table} MODIFY items_prefix TEXT NOT NULL");
    $xoopsDB->exec("ALTER TABLE {$table} MODIFY items_suffix TEXT NOT NULL");

    // Only seed data if all three targets have expected rows
    $catCount = $itemCount = $permCount = 0;
    $result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('menuscategory'));
    if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
        [$catCount] = $xoopsDB->fetchRow($result);
    }
    $result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('menusitems'));
    if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
        [$itemCount] = $xoopsDB->fetchRow($result);
    }
    $permTable = $xoopsDB->prefix('group_permission');
    $result = $xoopsDB->query("SELECT COUNT(*) FROM {$permTable}"
        . " WHERE gperm_name IN ('menus_category_view','menus_items_view')");
    if ($xoopsDB->isResultSet($result) && $result instanceof \mysqli_result) {
        [$permCount] = $xoopsDB->fetchRow($result);
    }

    if ((int)$catCount > 0 && (int)$itemCount > 0 && (int)$permCount > 0) {
        // Fully seeded — only run migrations
        $xoopsDB->exec("UPDATE " . $xoopsDB->prefix('menuscategory')
            . " SET category_url = 'index.php'"
            . " WHERE category_url = '/' AND category_protected = 1");
        return;
    }

    // Partial or empty state — clean up before re-seeding
    if ((int)$catCount > 0 || (int)$itemCount > 0) {
        $xoopsDB->exec("DELETE FROM " . $xoopsDB->prefix('menusitems'));
        $xoopsDB->exec("DELETE FROM " . $xoopsDB->prefix('menuscategory'));
    }
    if ((int)$permCount > 0) {
        $xoopsDB->exec("DELETE FROM {$permTable}"
            . " WHERE gperm_name IN ('menus_category_view','menus_items_view')");
    }

    // Seed default categories
    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menuscategory')
        . " (category_title, category_prefix, category_suffix, category_url, category_target, category_position, category_protected, category_active)"
        . " VALUES ('MENUS_HOME', '<span class=\"fa fa-home\"></span>', '', 'index.php', 0, 0, 1, 1)");
    $catHomeId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menuscategory')
        . " (category_title, category_prefix, category_suffix, category_url, category_target, category_position, category_protected, category_active)"
        . " VALUES ('MENUS_ADMIN', '<span class=\"fa fa-wrench fa-fw\"></span>', '', 'admin.php', 0, 10, 1, 1)");
    $catAdminId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menuscategory')
        . " (category_title, category_prefix, category_suffix, category_url, category_target, category_position, category_protected, category_active)"
        . " VALUES ('MENUS_ACCOUNT', '<span class=\"fa fa-user fa-fw\"></span>', '', '', 0, 20, 1, 1)");
    $catAccountId = $xoopsDB->getInsertId();

    // Seed default items under Account category
    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_EDIT', '<span class=\"fa fa-edit fa-fw\"></span>', '', 'user.php', 0, 1, 1, 1)");
    $itemEditId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_LOGIN', '<span class=\"fa fa-sign-in fa-fw\"></span>', '', 'user.php', 0, 2, 1, 1)");
    $itemLoginId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_REGISTER', '<span class=\"fa fa-sign-in fa-fw\"></span>', '', 'register.php', 0, 3, 1, 1)");
    $itemRegisterId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_MESSAGES', '<span class=\"fa fa-envelope fa-fw\"></span>', '<span class=\"badge bg-primary rounded-pill\"><{xoInboxCount}></span>', 'viewpmsg.php', 0, 4, 1, 1)");
    $itemMessagesId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_NOTIFICATIONS', '<span class=\"fa fa-info-circle fa-fw\"></span>', '', 'notifications.php', 0, 5, 1, 1)");
    $itemNotifId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_TOOLBAR', '<span class=\"fa fa-wrench fa-fw\"></span>', '<span id=\"xswatch-toolbar-ind\"></span>', 'javascript:xswatchToolbarToggle();', 0, 6, 1, 1)");
    $itemToolbarId = $xoopsDB->getInsertId();

    $xoopsDB->exec("INSERT INTO " . $xoopsDB->prefix('menusitems')
        . " (items_pid, items_cid, items_title, items_prefix, items_suffix, items_url, items_target, items_position, items_protected, items_active)"
        . " VALUES (0, {$catAccountId}, 'MENUS_ACCOUNT_LOGOUT', '<span class=\"fa fa-sign-out fa-fw\"></span>', '', 'user.php?op=logout', 0, 7, 1, 1)");
    $itemLogoutId = $xoopsDB->getInsertId();

    // Seed permissions using the actual module ID
    // Category permissions: Home visible to all groups (1=admin, 2=registered, 3=anonymous)
    foreach ([1, 2, 3] as $gid) {
        $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ({$gid}, {$catHomeId}, {$mid}, 'menus_category_view')");
    }
    // Admin category visible to admin only
    $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (1, {$catAdminId}, {$mid}, 'menus_category_view')");
    // Account category visible to all groups
    foreach ([1, 2, 3] as $gid) {
        $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ({$gid}, {$catAccountId}, {$mid}, 'menus_category_view')");
    }

    // Item permissions
    // Edit Account: admin + registered
    foreach ([1, 2] as $gid) {
        $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ({$gid}, {$itemEditId}, {$mid}, 'menus_items_view')");
    }
    // Login: anonymous only
    $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (3, {$itemLoginId}, {$mid}, 'menus_items_view')");
    // Register: anonymous only
    $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (3, {$itemRegisterId}, {$mid}, 'menus_items_view')");
    // Messages: admin + registered
    foreach ([1, 2] as $gid) {
        $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ({$gid}, {$itemMessagesId}, {$mid}, 'menus_items_view')");
    }
    // Notifications: admin + registered
    foreach ([1, 2] as $gid) {
        $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ({$gid}, {$itemNotifId}, {$mid}, 'menus_items_view')");
    }
    // Toolbar: admin only
    $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (1, {$itemToolbarId}, {$mid}, 'menus_items_view')");
    // Logout: admin + registered
    foreach ([1, 2] as $gid) {
        $xoopsDB->exec("INSERT INTO {$permTable} (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ({$gid}, {$itemLogoutId}, {$mid}, 'menus_items_view')");
    }
}
