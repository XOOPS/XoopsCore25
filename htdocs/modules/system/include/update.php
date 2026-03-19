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
    // Create menu tables and seed defaults
    global $xoopsDB;
    system_menu_create_tables($xoopsDB);
    system_menu_migrate_unsafe_urls($xoopsDB);
    $systemModuleId = $module->getVar('mid');
    system_menu_seed_defaults($xoopsDB, (int) $systemModuleId);

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
 * Drop foreign key constraints referencing a given parent table.
 */
function system_menu_drop_parent_foreign_keys(XoopsMySQLDatabase $db, string $tableName): void
{
    $result = $db->query(
        "SELECT CONSTRAINT_NAME, TABLE_NAME
         FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
         WHERE REFERENCED_TABLE_NAME = " . $db->quote($db->prefix($tableName)) . "
           AND TABLE_SCHEMA = DATABASE()"
    );
    if (!($result instanceof \mysqli_result)) {
        return;
    }
    while ($row = $db->fetchArray($result)) {
        $db->query("ALTER TABLE `{$row['TABLE_NAME']}` DROP FOREIGN KEY `{$row['CONSTRAINT_NAME']}`");
    }
}

/**
 * Create the menuscategory and menusitems tables if they do not exist.
 */
function system_menu_create_tables(XoopsMySQLDatabase $db): void
{
    $prefix = $db->prefix('menuscategory');
    $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}` (
        `category_id`        INT          NOT NULL AUTO_INCREMENT,
        `category_title`     VARCHAR(100) NOT NULL DEFAULT '',
        `category_prefix`    TEXT,
        `category_suffix`    TEXT,
        `category_url`       VARCHAR(255) NOT NULL DEFAULT '',
        `category_target`    TINYINT(1)   NOT NULL DEFAULT 0,
        `category_position`  INT          NOT NULL DEFAULT 0,
        `category_protected` INT          NOT NULL DEFAULT 0,
        `category_active`    TINYINT(1)   NOT NULL DEFAULT 1,
        PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->query($sql);

    // Drop orphan FKs before (re-)creating the items table
    system_menu_drop_parent_foreign_keys($db, 'menuscategory');

    $prefix = $db->prefix('menusitems');
    $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}` (
        `items_id`        INT          NOT NULL AUTO_INCREMENT,
        `items_pid`       INT          NOT NULL DEFAULT 0,
        `items_cid`       INT          NOT NULL DEFAULT 0,
        `items_title`     VARCHAR(100) NOT NULL DEFAULT '',
        `items_prefix`    TEXT,
        `items_suffix`    TEXT,
        `items_url`       VARCHAR(255) NOT NULL DEFAULT '',
        `items_target`    TINYINT(1)   NOT NULL DEFAULT 0,
        `items_position`  INT          NOT NULL DEFAULT 0,
        `items_protected` INT          NOT NULL DEFAULT 0,
        `items_active`    TINYINT(1)   NOT NULL DEFAULT 1,
        PRIMARY KEY (`items_id`),
        KEY `idx_items_cid` (`items_cid`),
        KEY `idx_items_pid` (`items_pid`),
        CONSTRAINT `fk_items_category` FOREIGN KEY (`items_cid`)
            REFERENCES `{$db->prefix('menuscategory')}` (`category_id`)
            ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->query($sql);
}

/**
 * Insert a category row only if it does not already exist (by title).
 *
 * @return int The category_id of the existing or newly inserted row.
 */
function system_menu_ensure_category(
    XoopsMySQLDatabase $db,
    string $title,
    string $url,
    int $position,
    int $protected = 0,
    int $active = 1
): int {
    $table = $db->prefix('menuscategory');
    $result = $db->query(
        "SELECT `category_id` FROM `{$table}` WHERE `category_title` = " . $db->quote($title)
    );
    if ($db->isResultSet($result) && ($result instanceof \mysqli_result) && ($row = $db->fetchArray($result))) {
        return (int) $row['category_id'];
    }
    $db->query(sprintf(
        "INSERT INTO `%s` (`category_title`,`category_url`,`category_position`,`category_protected`,`category_active`)
         VALUES (%s, %s, %d, %d, %d)",
        $table,
        $db->quote($title),
        $db->quote($url),
        $position,
        $protected,
        $active
    ));
    return (int) $db->getInsertId();
}

/**
 * Insert an item row only if no item with the same title exists under the same category.
 */
function system_menu_ensure_item(
    XoopsMySQLDatabase $db,
    int $categoryId,
    string $title,
    string $url,
    int $position,
    int $parentId = 0,
    int $protected = 0,
    int $active = 1,
    string $prefix = '',
    string $suffix = ''
): void {
    $table = $db->prefix('menusitems');
    $result = $db->query(sprintf(
        "SELECT `items_id` FROM `%s` WHERE `items_cid` = %d AND `items_title` = %s",
        $table,
        $categoryId,
        $db->quote($title)
    ));
    if ($db->isResultSet($result) && ($result instanceof \mysqli_result) && $db->fetchArray($result)) {
        return;
    }
    $db->query(sprintf(
        "INSERT INTO `%s` (`items_cid`,`items_pid`,`items_title`,`items_prefix`,`items_suffix`,`items_url`,`items_position`,`items_protected`,`items_active`)
         VALUES (%d, %d, %s, %s, %s, %s, %d, %d, %d)",
        $table,
        $categoryId,
        $parentId,
        $db->quote($title),
        $db->quote($prefix),
        $db->quote($suffix),
        $db->quote($url),
        $position,
        $protected,
        $active
    ));
}

/**
 * Grant a permission if it does not already exist.
 */
function system_menu_ensure_permission(
    XoopsMySQLDatabase $db,
    string $permName,
    int $itemId,
    int $groupId,
    int $moduleId
): void {
    $handler = xoops_getHandler('groupperm');
    if (!$handler->checkRight($permName, $itemId, $groupId, $moduleId)) {
        $perm = $handler->create();
        $perm->setVar('gperm_groupid', $groupId);
        $perm->setVar('gperm_itemid', $itemId);
        $perm->setVar('gperm_name', $permName);
        $perm->setVar('gperm_modid', $moduleId);
        $handler->insert($perm);
    }
}

/**
 * Seed default menu categories and items.
 */
function system_menu_seed_defaults(XoopsMySQLDatabase $db, int $moduleId): void
{
    $allGroups   = [1, 2, 3];
    $authGroups  = [1, 2];
    $adminGroups = [1];

    $homeId    = system_menu_ensure_category($db, 'MENUS_HOME', 'index.php', 1, 1, 1);
    $adminId   = system_menu_ensure_category($db, 'MENUS_ADMIN', 'admin.php', 2, 1, 1);
    $accountId = system_menu_ensure_category($db, 'MENUS_ACCOUNT', 'user.php', 3, 1, 1);

    foreach ($allGroups as $gid) {
        system_menu_ensure_permission($db, 'menus_category_view', $homeId, $gid, $moduleId);
    }
    foreach ($adminGroups as $gid) {
        system_menu_ensure_permission($db, 'menus_category_view', $adminId, $gid, $moduleId);
    }
    foreach ($allGroups as $gid) {
        system_menu_ensure_permission($db, 'menus_category_view', $accountId, $gid, $moduleId);
    }

    $items = [
        ['MENUS_ACCOUNT_EDIT',          'edituser.php',            1, 0, 1, 1],
        ['MENUS_ACCOUNT_LOGIN',         'user.php',                2, 0, 1, 1],
        ['MENUS_ACCOUNT_REGISTER',      'register.php',            3, 0, 1, 1],
        ['MENUS_ACCOUNT_MESSAGES',      'modules/pm/index.php',    4, 0, 1, 1, '', '<{xoInboxCount}>'],
        ['MENUS_ACCOUNT_NOTIFICATIONS', 'notifications.php',       5, 0, 1, 1],
        ['MENUS_ACCOUNT_TOOLBAR',       '#toolbar',                6, 0, 1, 1],
        ['MENUS_ACCOUNT_LOGOUT',        'user.php?op=logout',      7, 0, 1, 1],
    ];
    foreach ($items as $item) {
        $pfx = $item[6] ?? '';
        $sfx = $item[7] ?? '';
        system_menu_ensure_item($db, $accountId, $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $pfx, $sfx);
    }

    $itemTable = $db->prefix('menusitems');
    $result = $db->query(
        "SELECT `items_id`, `items_title` FROM `{$itemTable}` WHERE `items_cid` = " . $db->quote((string) $accountId)
    );
    if ($db->isResultSet($result) && ($result instanceof \mysqli_result)) {
        while ($row = $db->fetchArray($result)) {
            $groups = match ($row['items_title']) {
                'MENUS_ACCOUNT_LOGIN', 'MENUS_ACCOUNT_REGISTER' => [3],
                default => $authGroups,
            };
            foreach ($groups as $gid) {
                system_menu_ensure_permission($db, 'menus_items_view', (int)$row['items_id'], $gid, $moduleId);
            }
        }
    }
}

/**
 * Migrate any existing unsafe URLs (javascript:) to safe placeholders.
 */
function system_menu_migrate_unsafe_urls(XoopsMySQLDatabase $db): void
{
    $catTable = $db->prefix('menuscategory');
    $itemTable = $db->prefix('menusitems');

    $db->query("UPDATE `{$catTable}` SET `category_url` = '#' WHERE `category_url` LIKE 'javascript:%'");
    $db->query("UPDATE `{$itemTable}` SET `items_url` = '#' WHERE `items_url` LIKE 'javascript:%'");
}
