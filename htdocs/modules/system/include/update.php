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
    if (version_compare((string) $prev_version, '2.1.1', '<')) {
        $ret = update_system_v211($module);
    }
    // Clean up legacy .html template rows replaced by .tpl equivalents
    if (version_compare((string) $prev_version, '2.1.8', '<')) {
        update_system_remove_legacy_html_templates($module);
    }
    if (version_compare((string) $prev_version, '2.1.10', '<')) {
        if (!system_modules_add_menu_visibility($module)) {
            $ret = false;
        }
    }
    // Create/upgrade menu tables and seed defaults (added in 2.5.12)
    if (!system_menu_update($module)) {
        $ret = false;
    }

    if (!empty($module->getErrors())) {
        $ret = false;
    } elseif ($ret !== false) {
        $ret = true;
    }

    return $ret;
    // irmtfan bug fix: solve templates duplicate issue
}

/**
 * Add a dedicated menu visibility flag and migrate existing module ordering.
 *
 * Hidden modules historically used weight=0, which prevented them from being
 * reordered. This migration preserves the old admin list order, stores menu
 * visibility separately, and gives all non-system modules a real weight.
 *
 * @param XoopsModule $module
 *
 * @return bool
 */
function system_modules_add_menu_visibility(XoopsModule $module)
{
    global $xoopsDB;

    $tableName = $xoopsDB->prefix('modules');
    $sql       = "SHOW COLUMNS FROM {$tableName} LIKE 'show_in_menu'";
    $result    = $xoopsDB->query($sql);
    if (!$xoopsDB->isResultSet($result) || !($result instanceof \mysqli_result)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);
        $module->setErrors('Unable to inspect modules schema for show_in_menu.');

        return false;
    }

    $columnExists = false !== $xoopsDB->fetchArray($result);
    if (!$columnExists) {
        $sql = "ALTER TABLE {$tableName} ADD show_in_menu tinyint(1) unsigned NOT NULL default '1' AFTER weight";
        if (!$xoopsDB->exec($sql)) {
            xoops_error($xoopsDB->error() . '<br>' . $sql);
            $module->setErrors('Unable to add show_in_menu to the modules table.');

            return false;
        }
        // Reset cached column check so insert() picks up the new column
        XoopsModuleHandler::resetShowInMenuCache();
    }

    $sql = "UPDATE {$tableName} SET show_in_menu = CASE WHEN weight > 0 THEN 1 ELSE 0 END";
    if (!$xoopsDB->exec($sql)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);
        $module->setErrors('Unable to migrate module menu visibility.');

        return false;
    }

    $sql    = "SELECT mid FROM {$tableName} WHERE dirname <> 'system' ORDER BY weight ASC, mid ASC";
    $result = $xoopsDB->query($sql);
    if (!$xoopsDB->isResultSet($result) || !($result instanceof \mysqli_result)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);
        $module->setErrors('Unable to load modules for weight migration.');

        return false;
    }

    $weight = 1;
    while (false !== ($row = $xoopsDB->fetchArray($result))) {
        $sql = sprintf('UPDATE %s SET weight = %u WHERE mid = %u', $tableName, $weight, (int) $row['mid']);
        if (!$xoopsDB->exec($sql)) {
            xoops_error($xoopsDB->error() . '<br>' . $sql);
            $module->setErrors('Unable to reassign module weights during migration.');

            return false;
        }
        ++$weight;
    }

    return true;
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
 * Create or upgrade the menu management tables and seed protected defaults.
 *
 * Standardizes the root parent sentinel to 0 (matching XOOPS convention)
 * and enforces NOT NULL on affix columns.
 *
 * @param XoopsModule $module System module reference
 */
function system_menu_update(XoopsModule $module): bool
{
    $db = \XoopsDatabaseFactory::getDatabaseConnection();
    $mid = (int) $module->getVar('mid');

    try {
        system_menu_create_tables($db);
        system_menu_normalize_schema($db);
        system_menu_migrate_unsafe_urls($db);
        system_menu_seed_defaults($db, $mid);
    } catch (\Throwable $e) {
        $module->setErrors('Menu migration failed: ' . $e->getMessage());
        return false;
    }

    return true;
}

/**
 * Execute a DDL/DML statement and throw on failure.
 *
 * XOOPS DB layer returns false on error instead of throwing. This wrapper
 * converts false returns into exceptions so the caller's try/catch works.
 *
 * @param XoopsMySQLDatabase $db  Database connection
 * @param string             $sql SQL statement
 *
 * @throws \RuntimeException When the statement fails
 */
function system_menu_exec_or_throw(XoopsMySQLDatabase $db, string $sql): void
{
    $result = $db->exec($sql);
    if ($result === false) {
        throw new \RuntimeException('SQL failed: ' . mb_substr($sql, 0, 200));
    }
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
    if (!$db->isResultSet($result) || !($result instanceof \mysqli_result)) {
        return;
    }
    while ($row = $db->fetchArray($result)) {
        $db->exec("ALTER TABLE `{$row['TABLE_NAME']}` DROP FOREIGN KEY `{$row['CONSTRAINT_NAME']}`");
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
        `category_prefix`    TEXT         NOT NULL,
        `category_suffix`    TEXT         NOT NULL,
        `category_url`       VARCHAR(255) NOT NULL DEFAULT '',
        `category_target`    TINYINT(1)   NOT NULL DEFAULT 0,
        `category_position`  INT          NOT NULL DEFAULT 0,
        `category_protected` INT          NOT NULL DEFAULT 0,
        `category_active`    TINYINT(1)   NOT NULL DEFAULT 1,
        PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    system_menu_exec_or_throw($db, $sql);

    // Drop orphan FKs before (re-)creating the items table
    system_menu_drop_parent_foreign_keys($db, 'menuscategory');

    // FK constraint name must include the table prefix to be unique per-database,
    // since multiple XOOPS installs can share the same MySQL database.
    $prefix = $db->prefix('menusitems');
    $fkName = $db->prefix('fk_items_category');
    $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}` (
        `items_id`        INT          NOT NULL AUTO_INCREMENT,
        `items_pid`       INT          NOT NULL DEFAULT 0,
        `items_cid`       INT          NOT NULL DEFAULT 0,
        `items_title`     VARCHAR(100) NOT NULL DEFAULT '',
        `items_prefix`    TEXT         NOT NULL,
        `items_suffix`    TEXT         NOT NULL,
        `items_url`       VARCHAR(255) NOT NULL DEFAULT '',
        `items_target`    TINYINT(1)   NOT NULL DEFAULT 0,
        `items_position`  INT          NOT NULL DEFAULT 0,
        `items_protected` INT          NOT NULL DEFAULT 0,
        `items_active`    TINYINT(1)   NOT NULL DEFAULT 1,
        PRIMARY KEY (`items_id`),
        KEY `idx_items_cid` (`items_cid`),
        KEY `idx_items_pid` (`items_pid`),
        CONSTRAINT `{$fkName}` FOREIGN KEY (`items_cid`)
            REFERENCES `{$db->prefix('menuscategory')}` (`category_id`)
            ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    system_menu_exec_or_throw($db, $sql);

    // Re-add FK if it was dropped on an existing table (CREATE TABLE IF NOT EXISTS is a no-op)
    $fkCheck = $db->query(
        "SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS"
        . " WHERE CONSTRAINT_NAME = " . $db->quote($fkName)
        . " AND TABLE_NAME = " . $db->quote($prefix)
        . " AND TABLE_SCHEMA = DATABASE()"
    );
    if ($db->isResultSet($fkCheck) && ($fkCheck instanceof \mysqli_result) && 0 === $db->getRowsNum($fkCheck)) {
        system_menu_exec_or_throw(
            $db,
            "ALTER TABLE `{$prefix}` ADD CONSTRAINT `{$fkName}`"
            . " FOREIGN KEY (`items_cid`) REFERENCES `{$db->prefix('menuscategory')}` (`category_id`)"
            . " ON DELETE CASCADE"
        );
    }
}

/**
 * Normalize the menu schema for XOOPS conventions.
 *
 * - Converts any NULL items_pid values to 0 (root sentinel)
 * - Drops legacy self-referencing FK on items_pid
 * - Enforces NOT NULL on items_pid
 * - Enforces NOT NULL on all prefix/suffix TEXT columns
 * - Migrates root-relative '/' category URLs to 'index.php' for subdirectory safety
 */
function system_menu_normalize_schema(XoopsMySQLDatabase $db): void
{
    $catTable = $db->prefix('menuscategory');
    $itemTable = $db->prefix('menusitems');

    // Drop self-referencing FK on items_pid (incompatible with 0-as-root convention)
    $result = $db->query(
        "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE"
        . " WHERE TABLE_SCHEMA = DATABASE()"
        . " AND TABLE_NAME = " . $db->quote($itemTable)
        . " AND COLUMN_NAME = 'items_pid'"
        . " AND REFERENCED_TABLE_NAME IS NOT NULL"
    );
    if ($db->isResultSet($result) && ($result instanceof \mysqli_result)) {
        while ($row = $db->fetchArray($result)) {
            $db->exec("ALTER TABLE `{$itemTable}` DROP FOREIGN KEY `{$row['CONSTRAINT_NAME']}`");
        }
    }

    // Normalize NULL parent IDs to 0
    system_menu_exec_or_throw($db, "UPDATE `{$itemTable}` SET `items_pid` = 0 WHERE `items_pid` IS NULL");
    system_menu_exec_or_throw($db, "ALTER TABLE `{$itemTable}` MODIFY `items_pid` INT NOT NULL DEFAULT 0");

    // Enforce NOT NULL on affix columns
    system_menu_exec_or_throw($db, "ALTER TABLE `{$catTable}` MODIFY `category_prefix` TEXT NOT NULL");
    system_menu_exec_or_throw($db, "ALTER TABLE `{$catTable}` MODIFY `category_suffix` TEXT NOT NULL");
    system_menu_exec_or_throw($db, "ALTER TABLE `{$itemTable}` MODIFY `items_prefix` TEXT NOT NULL");
    system_menu_exec_or_throw($db, "ALTER TABLE `{$itemTable}` MODIFY `items_suffix` TEXT NOT NULL");

    // Migrate root-relative '/' to 'index.php' (safe for subdirectory installs)
    system_menu_exec_or_throw(
        $db,
        "UPDATE `{$catTable}` SET `category_url` = " . $db->quote('index.php')
        . " WHERE `category_protected` = 1 AND `category_url` = " . $db->quote('/')
    );
}

/**
 * Ensure a protected category exists and matches the current seed definition.
 *
 * On upgrade, existing categories keep their active state so administrators
 * do not lose local enable/disable decisions.
 *
 * @param XoopsMySQLDatabase    $db         Database connection
 * @param array<string, mixed>  $definition Category seed definition
 *
 * @return int Persisted category id
 */
function system_menu_ensure_category(XoopsMySQLDatabase $db, array $definition): int
{
    $table = $db->prefix('menuscategory');
    $result = $db->query(
        "SELECT `category_id`, `category_active` FROM `{$table}`"
        . " WHERE `category_title` = " . $db->quote($definition['title'])
        . " AND `category_protected` = " . (int) $definition['protected']
        . " ORDER BY `category_id` ASC"
    );
    if ($db->isResultSet($result) && ($result instanceof \mysqli_result) && ($row = $db->fetchArray($result))) {
        // Existing row: only sync seed-owned content fields (prefix, suffix, url).
        // Preserve admin-maintained layout fields (position, target, active).
        system_menu_exec_or_throw($db, sprintf(
            "UPDATE `%s` SET `category_prefix` = %s, `category_suffix` = %s, `category_url` = %s"
            . " WHERE `category_id` = %d",
            $table,
            $db->quote($definition['prefix']),
            $db->quote($definition['suffix']),
            $db->quote($definition['url']),
            (int) $row['category_id']
        ));
        return (int) $row['category_id'];
    }

    system_menu_exec_or_throw($db, sprintf(
        "INSERT INTO `%s` (`category_title`,`category_prefix`,`category_suffix`,`category_url`,"
        . "`category_target`,`category_position`,`category_protected`,`category_active`)"
        . " VALUES (%s, %s, %s, %s, %d, %d, %d, %d)",
        $table,
        $db->quote($definition['title']),
        $db->quote($definition['prefix']),
        $db->quote($definition['suffix']),
        $db->quote($definition['url']),
        (int) $definition['target'],
        (int) $definition['position'],
        (int) $definition['protected'],
        (int) $definition['active']
    ));
    return (int) $db->getInsertId();
}

/**
 * Ensure an item exists under its category and matches the current seed definition.
 *
 * On upgrade, existing items keep their active state so administrators
 * do not lose local enable/disable decisions.
 *
 * @param XoopsMySQLDatabase    $db         Database connection
 * @param int                   $categoryId Parent category id
 * @param array<string, mixed>  $definition Item seed definition
 *
 * @return int Persisted item id
 */
function system_menu_ensure_item(XoopsMySQLDatabase $db, int $categoryId, array $definition): int
{
    $table = $db->prefix('menusitems');
    $result = $db->query(sprintf(
        "SELECT `items_id`, `items_active` FROM `%s`"
        . " WHERE `items_cid` = %d AND `items_title` = %s AND `items_protected` = %d"
        . " ORDER BY `items_id` ASC",
        $table,
        $categoryId,
        $db->quote($definition['title']),
        (int) $definition['protected']
    ));
    if ($db->isResultSet($result) && ($result instanceof \mysqli_result) && ($row = $db->fetchArray($result))) {
        // Existing row: only sync seed-owned content fields (prefix, suffix, url).
        // Preserve admin-maintained layout fields (pid, position, target, active).
        system_menu_exec_or_throw($db, sprintf(
            "UPDATE `%s` SET `items_prefix` = %s, `items_suffix` = %s, `items_url` = %s"
            . " WHERE `items_id` = %d",
            $table,
            $db->quote($definition['prefix']),
            $db->quote($definition['suffix']),
            $db->quote($definition['url']),
            (int) $row['items_id']
        ));
        return (int) $row['items_id'];
    }

    system_menu_exec_or_throw($db, sprintf(
        "INSERT INTO `%s` (`items_cid`,`items_pid`,`items_title`,`items_prefix`,`items_suffix`,"
        . "`items_url`,`items_target`,`items_position`,`items_protected`,`items_active`)"
        . " VALUES (%d, %d, %s, %s, %s, %s, %d, %d, %d, %d)",
        $table,
        $categoryId,
        (int) $definition['pid'],
        $db->quote($definition['title']),
        $db->quote($definition['prefix']),
        $db->quote($definition['suffix']),
        $db->quote($definition['url']),
        (int) $definition['target'],
        (int) $definition['position'],
        (int) $definition['protected'],
        (int) $definition['active']
    ));
    return (int) $db->getInsertId();
}

/**
 * Seed menu permissions for a set of groups.
 *
 * @param int                $moduleId  System module id
 * @param string             $permName  Permission name
 * @param int                $itemId    Item or category id
 * @param int[]              $groupIds  Group ids to grant
 */
function system_menu_seed_permissions(
    int $moduleId,
    string $permName,
    int $itemId,
    array $groupIds
): void {
    $handler = xoops_getHandler('groupperm');
    foreach ($groupIds as $gid) {
        // Idempotent: skip if this exact permission already exists
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('gperm_groupid', (int) $gid));
        $criteria->add(new \Criteria('gperm_itemid', $itemId));
        $criteria->add(new \Criteria('gperm_name', $permName));
        $criteria->add(new \Criteria('gperm_modid', $moduleId));
        if ($handler->getCount($criteria) > 0) {
            continue;
        }
        $perm = $handler->create();
        $perm->setVar('gperm_groupid', (int) $gid);
        $perm->setVar('gperm_itemid', $itemId);
        $perm->setVar('gperm_name', $permName);
        $perm->setVar('gperm_modid', $moduleId);
        $handler->insert($perm);
    }
}

/**
 * Seed default menu categories, items, and permissions.
 */
function system_menu_seed_defaults(XoopsMySQLDatabase $db, int $moduleId): void
{
    $adminGroup = defined('XOOPS_GROUP_ADMIN') ? (int) XOOPS_GROUP_ADMIN : 1;
    $usersGroup = defined('XOOPS_GROUP_USERS') ? (int) XOOPS_GROUP_USERS : 2;
    $anonGroup  = defined('XOOPS_GROUP_ANONYMOUS') ? (int) XOOPS_GROUP_ANONYMOUS : 3;

    $allGroups   = [$adminGroup, $usersGroup, $anonGroup];
    $authGroups  = [$adminGroup, $usersGroup];
    $adminGroups = [$adminGroup];

    // --- Category definitions ---
    $categories = [
        'home' => [
            'title'     => 'MENUS_HOME',
            'prefix'    => '<span class="fa fa-home"></span>',
            'suffix'    => '',
            'url'       => 'index.php',
            'target'    => 0,
            'position'  => 1,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $allGroups,
        ],
        'account' => [
            'title'     => 'MENUS_ACCOUNT',
            'prefix'    => '<span class="fa fa-user fa-fw"></span>',
            'suffix'    => '',
            'url'       => '',
            'target'    => 0,
            'position'  => 2,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $allGroups,
        ],
        'admin' => [
            'title'     => 'MENUS_ADMIN',
            'prefix'    => '<span class="fa fa-wrench fa-fw"></span>',
            'suffix'    => '',
            'url'       => 'admin.php',
            'target'    => 0,
            'position'  => 3,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $adminGroups,
        ],
    ];

    // --- Item definitions (under Account) ---
    $items = [
        [
            'title'     => 'MENUS_ACCOUNT_EDIT',
            'prefix'    => '<span class="fa fa-edit fa-fw"></span>',
            'suffix'    => '',
            'url'       => 'user.php',
            'target'    => 0,
            'position'  => 1,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $authGroups,
        ],
        [
            'title'     => 'MENUS_ACCOUNT_LOGIN',
            'prefix'    => '<span class="fa fa-sign-in fa-fw"></span>',
            'suffix'    => '',
            'url'       => 'user.php',
            'target'    => 0,
            'position'  => 2,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => [$anonGroup],
        ],
        [
            'title'     => 'MENUS_ACCOUNT_REGISTER',
            'prefix'    => '<span class="fa fa-sign-in fa-fw"></span>',
            'suffix'    => '',
            'url'       => 'register.php',
            'target'    => 0,
            'position'  => 3,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => [$anonGroup],
        ],
        [
            'title'     => 'MENUS_ACCOUNT_MESSAGES',
            'prefix'    => '<span class="fa fa-envelope fa-fw"></span>',
            'suffix'    => '<span class="badge bg-primary rounded-pill"><{xoInboxCount}></span>',
            'url'       => 'viewpmsg.php',
            'target'    => 0,
            'position'  => 4,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $authGroups,
        ],
        [
            'title'     => 'MENUS_ACCOUNT_NOTIFICATIONS',
            'prefix'    => '<span class="fa fa-info-circle fa-fw"></span>',
            'suffix'    => '',
            'url'       => 'notifications.php',
            'target'    => 0,
            'position'  => 5,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $authGroups,
        ],
        [
            'title'     => 'MENUS_ACCOUNT_TOOLBAR',
            'prefix'    => '<span class="fa fa-wrench fa-fw"></span>',
            'suffix'    => '<span id="xswatch-toolbar-ind"></span>',
            'url'       => '#xswatch-toolbar-toggle',
            'target'    => 0,
            'position'  => 6,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $authGroups,
        ],
        [
            'title'     => 'MENUS_ACCOUNT_LOGOUT',
            'prefix'    => '<span class="fa fa-sign-out fa-fw"></span>',
            'suffix'    => '',
            'url'       => 'user.php?op=logout',
            'target'    => 0,
            'position'  => 7,
            'pid'       => 0,
            'protected' => 1,
            'active'    => 1,
            'groups'    => $authGroups,
        ],
    ];

    // --- Persist categories ---
    $categoryIds = [];
    foreach ($categories as $key => $catDef) {
        $categoryIds[$key] = system_menu_ensure_category($db, $catDef);
    }

    // --- Persist items (all under Account) ---
    $itemIds = [];
    foreach ($items as $itemDef) {
        $itemIds[] = system_menu_ensure_item($db, $categoryIds['account'], $itemDef);
    }

    // --- Seed category permissions (idempotent — skips existing) ---
    foreach ($categories as $key => $catDef) {
        system_menu_seed_permissions($moduleId, 'menus_category_view', $categoryIds[$key], $catDef['groups']);
    }

    // --- Seed item permissions ---
    foreach ($items as $idx => $itemDef) {
        system_menu_seed_permissions($moduleId, 'menus_items_view', $itemIds[$idx], $itemDef['groups']);
    }
}

/**
 * Migrate any existing unsafe URLs (javascript:) to safe placeholders.
 */
function system_menu_migrate_unsafe_urls(XoopsMySQLDatabase $db): void
{
    $catTable = $db->prefix('menuscategory');
    $itemTable = $db->prefix('menusitems');

    system_menu_exec_or_throw($db, "UPDATE `{$catTable}` SET `category_url` = '#' WHERE TRIM(`category_url`) LIKE 'javascript:%'");
    system_menu_exec_or_throw($db, "UPDATE `{$itemTable}` SET `items_url` = '#' WHERE TRIM(`items_url`) LIKE 'javascript:%'");
}
