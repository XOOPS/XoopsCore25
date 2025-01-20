<?php declare(strict_types=1);

namespace XoopsModules\Moduleinstaller\Common;

/*
 Utility Class Definition

 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @copyright    https://xoops.org 2000-2020 &copy; XOOPS Project
 * @author       ZySpec <zyspec@yahoo.com>
 * @author       Mamba <mambax7@gmail.com>
 */


use XoopsModules\Moduleinstaller\{
    Helper
};

/**
 * Class SysUtility
 */
class SysUtility
{
    use VersionChecks;    //checkVerXoops, checkVerPhp Traits
    use ServerStats;    // getServerStats Trait
    use FilesManagement;    // Files Management Trait

    /**
     * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
     * www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags
     * www.cakephp.org
     *
     * @TODO: Refactor to consider HTML5 & void (self-closing) elements
     * @TODO: Consider using https://github.com/jlgrall/truncateHTML/blob/master/truncateHTML.php
     *
     * @param string      $text         String to truncate.
     * @param int|null    $length       Length of returned string, including ellipsis.
     * @param string|null $ending       Ending to be appended to the trimmed string.
     * @param bool|null   $exact        If false, $text will not be cut mid-word
     * @param bool|null   $considerHtml If true, HTML tags would be handled correctly
     *
     * @return string Trimmed string.
     */
    public static function truncateHtml(
        string  $text,
        ?int    $length = null,
        ?string $ending = null,
        ?bool   $exact = null,
        ?bool   $considerHtml = null
    ): string {
        $length       ??= 100;
        $ending       ??= '...';
        $exact        ??= false;
        $considerHtml ??= true;
        $openTags     = [];
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (\mb_strlen(\preg_replace('/<.*?' . '>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            \preg_match_all('/(<.+?' . '>)?([^<>]*)/s', $text, $lines, \PREG_SET_ORDER);
            $totalLength = (int)\mb_strlen($ending);
            //$openTags    = [];
            $truncate = '';
            foreach ($lines as $lineMatchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($lineMatchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (\preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $lineMatchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } elseif (\preg_match('/^<\s*\/(\S+?)\s*>$/', $lineMatchings[1], $tagMatchings)) {
                        // delete tag from $openTags list
                        $pos = \array_search($tagMatchings[1], $openTags, true);
                        if (false !== $pos) {
                            unset($openTags[$pos]);
                        }
                        // if tag is an opening tag
                    } elseif (\preg_match('/^<\s*([^\s>!]+).*?' . '>$/s', $lineMatchings[1], $tagMatchings)) {
                        // add tag to the beginning of $openTags list
                        \array_unshift($openTags, \mb_strtolower($tagMatchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $lineMatchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $contentLength = (int)\mb_strlen(\preg_replace('/&[0-9a-z]{2,8};|&#\d{1,7};|[0-9a-f]{1,6};/i', ' ', $lineMatchings[2]));
                if (($totalLength + $contentLength) > $length) {
                    // the number of characters which are left
                    $left           = $length - $totalLength;
                    $entitiesLength = 0;
                    // search for html entities
                    if (\preg_match_all('/&[0-9a-z]{2,8};|&#\d{1,7};|[0-9a-f]{1,6};/i', $lineMatchings[2], $entities, \PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($left >= $entity[1] + 1 - $entitiesLength) {
                                $left--;
                                $entitiesLength += \mb_strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= \mb_substr($lineMatchings[2], 0, $left + $entitiesLength);
                    // maximum length is reached, so get off the loop
                    break;
                }
                $truncate    .= $lineMatchings[2];
                $totalLength += $contentLength;

                // if the maximum length is reached, get off the loop
                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (\mb_strlen($text) <= $length) {
                return $text;
            }
            $truncate = \mb_substr($text, 0, $length - \mb_strlen($ending));
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = \mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = \mb_substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($openTags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    /**
     * @param \XoopsModules\Moduleinstaller\Helper|null $helper
     * @param array|null                                $options
     * @return \XoopsFormDhtmlTextArea|\XoopsFormEditor
     */
    public static function getEditor(?Helper $helper = null, ?array $options = null)
    {
        /** @var Helper $helper */
        if (null === $options) {
            $options           = [];
            $options['name']   = 'Editor';
            $options['value']  = 'Editor';
            $options['rows']   = 10;
            $options['cols']   = '100%';
            $options['width']  = '100%';
            $options['height'] = '400px';
        }

        if (null === $helper) {
            $helper = Helper::getInstance();
        }

        $isAdmin = $helper->isUserAdmin();

        if (\class_exists('XoopsFormEditor')) {
            if ($isAdmin) {
                $descEditor = new \XoopsFormEditor(\ucfirst((string) $options['name']), $helper->getConfig('editorAdmin'), $options, $nohtml = false, $onfailure = 'textarea');
            } else {
                $descEditor = new \XoopsFormEditor(\ucfirst((string) $options['name']), $helper->getConfig('editorUser'), $options, $nohtml = false, $onfailure = 'textarea');
            }
        } else {
            $descEditor = new \XoopsFormDhtmlTextArea(\ucfirst((string) $options['name']), $options['name'], $options['value']);
        }

        //        $form->addElement($descEditor);

        return $descEditor;
    }

    /**
     * @param string $fieldname
     * @param string $table
     *
     * @return bool
     */
    public static function fieldExists(string $fieldname, string $table): bool
    {
        global $xoopsDB;
                $sql ="SHOW COLUMNS FROM   $table LIKE '$fieldname'";
        $result = self::queryFAndCheck($xoopsDB, $sql);

        return ($xoopsDB->getRowsNum($result) > 0);
    }

    /**
     * Clone a record in a dB
     *
     * @TODO need to exit more gracefully on error. Should throw/trigger error and then return false
     *
     * @param string $tableName name of dB table (without prefix)
     * @param string $idField   name of field (column) in dB table
     * @param int    $id        item id to clone
     * @return int|null
     */
    public static function cloneRecord(string $tableName, string $idField, int $id): ?int
    {
        $newId = null;
        $tempTable = [];
        $table  = $GLOBALS['xoopsDB']->prefix($tableName);
        // copy content of the record you wish to clone
        $sql       = "SELECT * FROM $table WHERE $idField='" . $id . "' ";
        $result = $GLOBALS['xoopsDB']->query($sql);
        if ($GLOBALS['xoopsDB']->isResultSet($result)) {
            $tempTable = $GLOBALS['xoopsDB']->fetchArray($result, \MYSQLI_ASSOC);
        }
        if (!$tempTable) {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error(), \E_USER_ERROR);
        }
        // set the auto-incremented id's value to blank.
        unset($tempTable[$idField]);
        // insert cloned copy of the original  record
        $sql    = "INSERT INTO $table (" . \implode(', ', \array_keys($tempTable)) . ") VALUES ('" . \implode("', '", $tempTable) . "')";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if ($result) {
            // Return the new id
            $newId = $GLOBALS['xoopsDB']->getInsertId();
        } else {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $GLOBALS['xoopsDB']->error(), \E_USER_ERROR);
        }
        return $newId;
    }

    /**
     * Check if dB table exists
     *
     * @param string $tablename dB tablename with prefix
     * @return bool true if table exists
     */
    public static function tableExists(string $tablename): bool
    {
        $ret    = false;
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $GLOBALS['xoopsLogger']->addDeprecated(
            \basename(\dirname(__DIR__, 2)) . ' Module: ' . __FUNCTION__ . ' function is deprecated, please use Xmf\Database\Tables method(s) instead.' . " Called from {$trace[0]['file']} line {$trace[0]['line']}"
        );
        $sql    = "SHOW TABLES LIKE '$tablename'";
        $result = self::queryFAndCheck($GLOBALS['xoopsDB'], $sql);

        return $ret;
    }

    /**
     * Query and check if the result is a valid result set
     *
     * @param \XoopsMySQLDatabase $xoopsDB XOOPS Database
     * @param string              $sql     a valid MySQL query
     * @param int                 $limit   number of records to return
     * @param int                 $start   offset of first record to return
     *
     * @return \mysqli_result query result
     */
    public static function queryAndCheck(\XoopsMySQLDatabase $xoopsDB, string $sql, $limit = 0, $start = 0): \mysqli_result
    {
        $result = $xoopsDB->query($sql, $limit, $start);

        if (!$xoopsDB->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(\_DB_QUERY_ERROR, $sql) . $xoopsDB->error(), \E_USER_ERROR);
        }

        return $result;
    }

    /**
     * QueryF and check if the result is a valid result set
     *
     * @param \XoopsMySQLDatabase $xoopsDB XOOPS Database
     * @param string              $sql     a valid MySQL query
     * @param int                 $limit   number of records to return
     * @param int                 $start   offset of first record to return
     *
     * @return \mysqli_result query result
     */
public static function queryFAndCheck(\XoopsMySQLDatabase $xoopsDB, string $sql, $limit = 0, $start = 0): \mysqli_result
    {
        $result = $xoopsDB->queryF($sql, $limit, $start);

        if (!$xoopsDB->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(\_DB_QUERY_ERROR, $sql) . $xoopsDB->error(), \E_USER_ERROR
            );
        }

        return $result;
    }

/**
     * Create a safe Criteria object that handles empty values, different data types,
     * and various operators.
     *
     * @param string         $column           Database field name
     * @param mixed          $values          Array or scalar value(s) to be used in the criteria
     * @param string         $operator        SQL operator (e.g., 'IN', 'LIKE', '=', '>')
     * @param \Criteria|null $noMatchCriteria Optional criteria to use when no valid values are provided
     * @return \Criteria
     */
    public static function createSafeCriteria(string $column, $values, string $operator = 'IN', ?\Criteria $noMatchCriteria = null): \Criteria
    {
        if (!empty($values)) {
            $values = (array)$values;

            // Filter out null, empty string, and false values
            $values = array_filter(
                $values, function ($value) {
                return $value !== null && $value !== '' && $value !== false;
            }
            );

            if (empty($values)) {
                // Return a criteria that always evaluates to false
                return $noMatchCriteria ?? new \Criteria($column, -1, '=');
            }

            switch ($operator) {
                case 'IN':
                    // Pass the array directly; Criteria handles it
                    return new \Criteria($column, $values, 'IN');
                case 'LIKE':
                    $criteria = new \CriteriaCompo();
                    foreach ($values as $value) {
                        $criteria->add(new \Criteria($column, '%' . $value . '%', 'LIKE'), 'OR');
                    }
                    return $criteria;
                default:
                    // For other operators, use the first value
                    return new \Criteria($column, reset($values), $operator);
            }
        }

        // If values are empty, return a criteria that matches no records
        return $noMatchCriteria ?? new \Criteria($column, -1, '=');
    }
}
