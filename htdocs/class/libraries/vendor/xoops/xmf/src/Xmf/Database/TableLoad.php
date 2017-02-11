<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Database;

use Xmf\Yaml;

/**
 * Xmf\Database\TableLoad
 *
 * load a database table
 *
 * @category  Xmf\Database\TableLoad
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2016 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class TableLoad
{

    /**
     * loadTableFromArray
     *
     * @param string $table name of table to load without prefix
     * @param array  $data  array of rows to insert
     *                      Each element of the outer array represents a single table row.
     *                      Each row is an associative array in 'column' => 'value' format.
     *
     * @return int number of rows inserted
     */
    public static function loadTableFromArray($table, $data)
    {
        /** @var \XoopsDatabase */
        $db = \XoopsDatabaseFactory::getDatabaseConnection();

        $prefixedTable = $db->prefix($table);
        $count = 0;

        foreach ($data as $row) {
            $insertInto = 'INSERT INTO ' . $prefixedTable . ' (';
            $valueClause = ' VALUES (';
            $first = true;
            foreach ($row as $column => $value) {
                if ($first) {
                    $first = false;
                } else {
                    $insertInto .= ', ';
                    $valueClause .= ', ';
                }

                $insertInto .= $column;
                $valueClause .= $db->quote($value);
            }

            $sql = $insertInto . ') ' . $valueClause . ')';

            $result = $db->queryF($sql);
            if (false !== $result) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * loadTableFromYamlFile
     *
     * @param string $table    name of table to load without prefix
     * @param string $yamlFile name of file containing data dump in YAML format
     *
     * @return int number of rows inserted
     */
    public static function loadTableFromYamlFile($table, $yamlFile)
    {
        $count = 0;

        $data = Yaml::loadWrapped($yamlFile); // work with phpmyadmin YAML dumps
        if (false !== $data) {
            $count = static::loadTableFromArray($table, $data);
        }

        return $count;
    }

    /**
     * truncateTable - empty a database table
     *
     * @param string $table name of table to truncate
     *
     * @return int number of affected rows
     */
    public static function truncateTable($table)
    {
        /** @var \XoopsDatabase */
        $db = \XoopsDatabaseFactory::getDatabaseConnection();

        $prefixedTable = $db->prefix($table);
        $sql = 'TRUNCATE TABLE ' . $prefixedTable;
        $result = $db->queryF($sql);
        if (false !== $result) {
            $result = $db->getAffectedRows();
        }
        return $result;
    }

    /**
     * countRows - get count of rows in a table
     *
     * @param string           $table    name of table to count
     * @param \CriteriaElement $criteria optional criteria
     *
     * @return int number of rows
     */
    public static function countRows($table, $criteria = null)
    {
        /** @var \XoopsDatabase */
        $db = \XoopsDatabaseFactory::getDatabaseConnection();

        $prefixedTable = $db->prefix($table);
        $sql = 'SELECT COUNT(*) as count FROM ' . $prefixedTable . ' ';
        if (isset($criteria) && is_subclass_of($criteria, '\CriteriaElement')) {
            /* @var  $criteria \CriteriaCompo */
            $sql .= $criteria->renderWhere();
        }
        $result = $db->query($sql);
        $row = $db->fetchArray($result);
        $count = $row['count'];
        $db->freeRecordSet($result);
        return (int)$count;
    }

    /**
     * extractRows - get rows, all or a subset, from a table as an array
     *
     * @param string           $table       name of table to count
     * @param \CriteriaElement $criteria    optional criteria
     * @param string[]         $skipColumns do not include columns in this list
     *
     * @return array of table rows
     */
    public static function extractRows($table, $criteria = null, $skipColumns = array())
    {
        /** @var \XoopsDatabase */
        $db = \XoopsDatabaseFactory::getDatabaseConnection();

        $prefixedTable = $db->prefix($table);
        $sql = 'SELECT * FROM ' . $prefixedTable . ' ';
        if (isset($criteria) && is_subclass_of($criteria, '\CriteriaElement')) {
            /* @var  $criteria \CriteriaCompo */
            $sql .= $criteria->renderWhere();
        }
        $rows = array();
        $result = $db->query($sql);
        if ($result) {
            while ($row = $db->fetchArray($result)) {
                $rows[] = $row;
            }
        }

        $db->freeRecordSet($result);

        if (!empty($skipColumns)) {
            foreach ($rows as $index => $row) {
                foreach ($skipColumns as $column) {
                    unset($rows[$index][$column]);
                }
            }
        }

        return $rows;
    }

    /**
     * Save table data to a YAML file
     *
     * @param string           $table name of table to load without prefix
     * @param string           $yamlFile name of file containing data dump in YAML format
     * @param \CriteriaElement $criteria optional criteria
     * @param string[]         $skipColumns do not include columns in this list
     *
     * @return bool true on success, false on error
     */
    public static function saveTableToYamlFile($table, $yamlFile, $criteria = null, $skipColumns = array())
    {
        $rows = static::extractRows($table, $criteria, $skipColumns);

        $count = Yaml::save($rows, $yamlFile);

        return (false !== $count);
    }
}
