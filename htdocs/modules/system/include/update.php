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
