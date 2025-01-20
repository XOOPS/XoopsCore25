<?php declare(strict_types=1);

/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @since           2.5.9
 * @author          Michael Beck (aka Mamba): https://github.com/mambax7
 */

use Xmf\Database\TableLoad;
use Xmf\Request;
use Xmf\Yaml;
use XoopsModules\Moduleinstaller\{
    Common\Configurator,
    Helper,
    Utility
};
/** @var Helper $helper */
/** @var Utility $utility */
/** @var Configurator $configurator */
require \dirname(__DIR__, 3) . '/include/cp_header.php';
require \dirname(__DIR__) . '/preloads/autoloader.php';

$op = Request::getCmd('op', '');

$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

$helper = Helper::getInstance();
// Load language files
$helper->loadLanguage('common');

switch ($op) {
    case 'load':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($helper->url('admin/index.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            loadSampleData();
        } else {
            xoops_cp_header();
            xoops_confirm(['ok' => 1, 'op' => 'load'], 'index.php', constant('CO_' . $moduleDirNameUpper . '_' . 'LOAD_SAMPLEDATA_CONFIRM'), constant('CO_' . $moduleDirNameUpper . '_' . 'CONFIRM'), true);
            xoops_cp_footer();
        }
        break;
    case 'save':
        saveSampleData();
        break;
    case 'clear':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($helper->url('admin/index.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            clearSampleData();
        } else {
            xoops_cp_header();
            xoops_confirm(['ok' => 1, 'op' => 'clear'], 'index.php', sprintf(constant('CO_' . $moduleDirNameUpper . '_' . 'CLEAR_SAMPLEDATA')), constant('CO_' . $moduleDirNameUpper . '_' . 'CONFIRM'), true);
            xoops_cp_footer();
        }
        break;
}

// XMF TableLoad for SAMPLE data

function loadSampleData(): void
{
    global $xoopsConfig;
    $moduleDirName      = \basename(\dirname(__DIR__));
    $moduleDirNameUpper = \mb_strtoupper($moduleDirName);

    $utility      = new Utility();
    $configurator = new Configurator();

    $tables = \Xmf\Module\Helper::getHelper($moduleDirName)->getModule()->getInfo('tables');

    $language = 'english/';
    if (is_dir(__DIR__ . '/' . $xoopsConfig['language'])) {
        $language = $xoopsConfig['language'] . '/';
    }

    // load module tables
    foreach ($tables as $table) {
        $tabledata = Yaml::readWrapped($language . $table . '.yml');
        TableLoad::truncateTable($table);
        TableLoad::loadTableFromArray($table, $tabledata);
    }

    // load permissions
    $table     = 'group_permission';
    $tabledata = Yaml::readWrapped($language . $table . '.yml');
    $mid       = \Xmf\Module\Helper::getHelper($moduleDirName)->getModule()->getVar('mid');
    loadTableFromArrayWithReplace($table, $tabledata, 'gperm_modid', $mid);

    //  ---  COPY test folder files ---------------
    if ($configurator->copyTestFolders && \is_array($configurator->copyTestFolders)) {
        //        $file =  \dirname(__DIR__) . '/testdata/images/';
        foreach (array_keys($configurator->copyTestFolders) as $i) {
            $src  = $configurator->copyTestFolders[$i][0];
            $dest = $configurator->copyTestFolders[$i][1];
            $utility::rcopy($src, $dest);
        }
    }
    \redirect_header('../admin/index.php', 1, \constant('CO_' . $moduleDirNameUpper . '_' . 'LOAD_SAMPLEDATA_SUCCESS'));
}

function saveSampleData(): void
{
    $skipColumns = [];
    global $xoopsConfig;
    $moduleDirName      = \basename(\dirname(__DIR__));
    $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
    $helper             = Helper::getInstance();
    $tables             = $helper->getModule()->getInfo('tables');

    $languageFolder = __DIR__ . '/' . $xoopsConfig['language'];
    if (!file_exists($languageFolder . '/')) {
        Utility::createFolder($languageFolder . '/');
    }
    $exportFolder = $languageFolder . '/Exports-' . date('Y-m-d-H-i-s') . '/';
    Utility::createFolder($exportFolder);

    // save module tables
    foreach ($tables as $table) {
        TableLoad::saveTableToYamlFile($table, $exportFolder . $table . '.yml');
    }

    // save permissions
    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('gperm_modid', (string)$helper->getModule()->getVar('mid')));
    $skipColumns[] = 'gperm_id';
    TableLoad::saveTableToYamlFile('group_permission', $exportFolder . 'group_permission.yml', $criteria, $skipColumns);
    unset($criteria);

    \redirect_header('../admin/index.php', 1, \constant('CO_' . $moduleDirNameUpper . '_' . 'SAVE_SAMPLEDATA_SUCCESS'));
}

function exportSchema(): void
{
    $moduleDirName      = \basename(\dirname(__DIR__));
    $moduleDirNameUpper = \mb_strtoupper($moduleDirName);

    try {
        // TODO set exportSchema
        //        $migrate = new Migrate($moduleDirName);
        //        $migrate->saveCurrentSchema();
        //
        //        redirect_header('../admin/index.php', 1, constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA_SUCCESS'));
    } catch (\Throwable $exception) {
        exit(constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA_ERROR'));
    }
}

/**
 * loadTableFromArrayWithReplace
 *
 * @param string $table  value which should be used instead of original value of $search
 *
 * @param array  $data   array of rows to insert
 *                       Each element of the outer array represents a single table row.
 *                       Each row is an associative array in 'column' => 'value' format.
 * @param string $search name of column for which the value should be replaced
 * @param        $replace
 * @return int number of rows inserted
 */
function loadTableFromArrayWithReplace($table, $data, $search, $replace)
{
    /** @var \XoopsMySQLDatabase $db */
    $db = \XoopsDatabaseFactory::getDatabaseConnection();

    $prefixedTable = $db->prefix($table);
    $count         = 0;

    $sql = 'DELETE FROM ' . $prefixedTable . ' WHERE `' . $search . '`=' . $db->quote($replace);

    $result = $db->queryF($sql);

    foreach ($data as $row) {
        $insertInto  = 'INSERT INTO ' . $prefixedTable . ' (';
        $valueClause = ' VALUES (';
        $first       = true;
        foreach ($row as $column => $value) {
            if ($first) {
                $first = false;
            } else {
                $insertInto  .= ', ';
                $valueClause .= ', ';
            }

            $insertInto .= $column;
            if ($search === $column) {
                $valueClause .= $db->quote($replace);
            } else {
                $valueClause .= $db->quote($value);
            }
        }

        $sql = $insertInto . ') ' . $valueClause . ')';

        $result = $db->queryF($sql);
        if (false !== $result) {
            ++$count;
        }
    }

    return $count;
}

function clearSampleData(): void
{
    $moduleDirName      = \basename(\dirname(__DIR__));
    $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
    $helper             = Helper::getInstance();
    // Load language files
    $helper->loadLanguage('common');
    $tables = $helper->getModule()->getInfo('tables');
    // truncate module tables
    foreach ($tables as $table) {
        \Xmf\Database\TableLoad::truncateTable($table);
    }
    redirect_header($helper->url('admin/index.php'), 1, constant('CO_' . $moduleDirNameUpper . '_' . 'CLEAR_SAMPLEDATA_OK'));
}
