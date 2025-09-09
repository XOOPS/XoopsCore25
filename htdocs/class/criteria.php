<?php
/**
 * XOOPS Criteria parser for database query
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          database
 * @since               2.0.0
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @author              Nathan Dial
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A criteria (grammar?) for a database query.
 *
 * Abstract base class should never be instantiated directly.
 *
 * @abstract
 */
class CriteriaElement
{
    /**
     * Sort order
     *
     * @var string
     */
    public $order = 'ASC';

    /**
     *
     * @var string
     */
    public $sort = '';

    /**
     * Number of records to retrieve
     *
     * @var int
     */
    public $limit = 0;

    /**
     * Offset of first record
     *
     * @var int
     */
    public $start = 0;

    /**
     *
     * @var string
     */
    public $groupby = '';

    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * Render the criteria element
     * @return string
     */
    public function render() {}

    /**
     *
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     *
     * @param string $order
     */
    public function setOrder($order)
    {
        if ('DESC' === strtoupper($order)) {
            $this->order = 'DESC';
        }
    }

    /**
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     *
     * @param int $limit
     */
    public function setLimit($limit = 0)
    {
        $this->limit = (int) $limit;
    }

    /**
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @param int $start
     */
    public function setStart($start = 0)
    {
        $this->start = (int) $start;
    }

    /**
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     *
     * @param string $group
     */
    public function setGroupBy($group)
    {
        $this->groupby = $group;
    }

    /**
     *
     * @return string
     */
    public function getGroupby()
    {
        return $this->groupby ? " GROUP BY {$this->groupby}" : '';
    }
    /**
     * *#@-
     */
}

/**
 * Collection of multiple {@link CriteriaElement}s
 *
 */
class CriteriaCompo extends CriteriaElement
{
    /**
     * The elements of the collection
     *
     * @var array Array of {@link CriteriaElement} objects
     */
    public $criteriaElements = [];

    /**
     * Conditions
     *
     * @var array
     */
    public $conditions = [];

    /**
     * Constructor
     *
     * @param CriteriaElement|null $ele
     * @param string $condition
     */
    public function __construct(?CriteriaElement $ele = null, $condition = 'AND')
    {
        if (isset($ele)) {
            $this->add($ele, $condition);
        }
    }

    /**
     * Add an element
     *
     * @param CriteriaElement|object $criteriaElement
     * @param string                 $condition
     * @return object reference to this collection
     */
    public function &add(CriteriaElement $criteriaElement, $condition = 'AND')
    {
        if (is_object($criteriaElement)) {
            $this->criteriaElements[] = & $criteriaElement;
            $this->conditions[]       = $condition;
        }

        return $this;
    }

    /**
     * Make the criteria into a query string
     *
     * @return string
     */
    public function render()
    {
        $ret   = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $render_string = $this->criteriaElements[0]->render();
            for ($i = 1; $i < $count; ++$i) {
                if (!$render = $this->criteriaElements[$i]->render()) {
                    continue;
                }
                $render_string .= (empty($render_string) ? '' : ' ' . $this->conditions[$i] . ' ') . $render;
            }
            $ret = empty($render_string) ? '' : "({$render_string})";
        }

        return $ret;
    }

    /**
     * Make the criteria into a SQL "WHERE" clause
     *
     * @return string
     */
    public function renderWhere()
    {
        $ret = $this->render();
        $ret = ($ret != '') ? 'WHERE ' . $ret : $ret;

        return $ret;
    }

    /**
     * Generate an LDAP filter from criteria
     *
     * @return string
     * @author Nathan Dial ndial@trillion21.com
     */
    public function renderLdap()
    {
        $retval = '';
        $count  = count($this->criteriaElements);
        if ($count > 0) {
            $retval = $this->criteriaElements[0]->renderLdap();
            for ($i = 1; $i < $count; ++$i) {
                $cond   = strtoupper($this->conditions[$i]);
                $op     = ($cond === 'OR') ? '|' : '&';
                $retval = "({$op}{$retval}" . $this->criteriaElements[$i]->renderLdap() . ')';
            }
        }

        return $retval;
    }
}

/**
 * A single criteria
 *
 */
class Criteria extends CriteriaElement
{
    /** @var string|null Optional table prefix (alias) like "u" for "u.`uname`" */
    public $prefix;

    /** @var string|null Optional column wrapper function with sprintf format, e.g. 'LOWER(%s)' */
    public $function;

    /** @var string Column name or expression (backticks handled for simple columns) */
    public $column;

    /** @var string SQL operator (=, <, >, LIKE, IN, IS NULL, etc.) */
    public $operator;

    /** @var mixed Value for the operator: scalar for most ops, array or "(a,b)" for IN/NOT IN */
    public $value;

    /** @var bool Allow empty string values to render (default false = skip empty) */
    protected $allowEmptyValue = false;

    /** @var bool Allow inner wildcards in LIKE (default false = escape inner % and _) */
    protected $allowInnerWildcards = false;

    /** @var bool Global default for allowing inner wildcards in LIKE across all instances */
    protected static $defaultAllowInnerWildcards = false;

    /**
     * Set the global default for allowing inner wildcards in LIKE patterns.
     * Useful during migrations of legacy modules that intentionally use inner wildcards.
     *
     * @param bool $on
     * @return void
     */
    public static function setDefaultAllowInnerWildcards($on = true)
    {
        self::$defaultAllowInnerWildcards = (bool)$on;
    }

    /**
     * Opt-in per instance for intentional inner wildcards in LIKE patterns.
     * Default remains secure (inner %/_ escaped).
     *
     * @param bool $on
     * @return $this
     */
    public function allowInnerWildcards($on = true)
    {
        $this->allowInnerWildcards = (bool)$on;
        return $this;
    }

    /**
     * Constructor
     *
     * @param string      $column
     * @param mixed       $value
     * @param string      $operator
     * @param string|null $prefix
     * @param string|null $function  sprintf format string, e.g. 'LOWER(%s)'
     * @param bool        $allowEmptyValue
     */
    public function __construct($column, $value = '', $operator = '=', $prefix = '', $function = '', $allowEmptyValue = false)
    {
        $this->prefix           = $prefix;
        $this->function         = $function;
        $this->column           = $column;
        $this->value            = $value;
        $this->operator         = $operator;
        $this->allowEmptyValue  = $allowEmptyValue;
        $this->allowInnerWildcards = self::$defaultAllowInnerWildcards;

        // Legacy always-true workaround: new Criteria(1, '1', '=') → no WHERE
        if ((int)$column === 1 && (int)$value === 1 && $operator === '=') {
            $this->column = '';
            $this->value  = '';
        }
    }

    /**
     * Render the SQL fragment (no leading WHERE)
     *
     * @return string
     */
    public function render()
    {
        /** @var \XoopsDatabase|null $xoopsDB */
        $xoopsDB = isset($GLOBALS['xoopsDB']) ? $GLOBALS['xoopsDB'] : null;
        if (!$xoopsDB) {
            return '';
        }

        $col = (string)($this->column ?? '');
        $backtick = (strpos($col, '.') === false) ? '`' : '';
        if (strpos($col, '(') !== false) { // function/expression like COUNT(col)
            $backtick = '';
        }

        $clause = (empty($this->prefix) ? '' : "{$this->prefix}.") . $backtick . $col . $backtick;

        if (!empty($this->function)) {
            // function should be a trusted sprintf pattern, e.g. 'LOWER(%s)'
            $clause = sprintf($this->function, $clause);
        }

        $op = strtoupper((string)$this->operator);

        // Null checks require no value
        if ($op === 'IS NULL' || $op === 'IS NOT NULL') {
            return $clause . ' ' . $op;
        }

        // Skip empty values unless explicitly allowed
        $rawValue = (string)$this->value;
        if (trim($rawValue) === '' && !$this->allowEmptyValue) {
            return '';
        }

        // IN / NOT IN: accept arrays or "(a,b)" string
        if ($op === 'IN' || $op === 'NOT IN') {
            $vals = is_array($this->value)
                ? $this->value
                : array_map('trim', explode(',', trim((string)$this->value, " ()")));

            $parts = [];
            foreach ($vals as $v) {
                if (is_int($v) || (is_string($v) && preg_match('/^-?\d+$/', $v))) {
                    $parts[] = (string)(int)$v;
                } else {
                    $parts[] = $xoopsDB->quoteString((string)$v);
                }
            }
            return $clause . ' ' . $op . ' (' . implode(',', $parts) . ')';
        }

        // LIKE / NOT LIKE: preserve leading/trailing % runs; escape inner unless opted-in
        if ($op === 'LIKE' || $op === 'NOT LIKE') {
            $pattern = (string)$this->value;

            // NEW: if pattern is only % signs, it's effectively a no-op for LIKE;
            // don't emit a predicate so we don't exclude NULL rows.
            if ($op === 'LIKE' && $pattern !== '' && strspn($pattern, '%') === strlen($pattern)) {
                return '';
            }

            $len     = strlen($pattern);
            $lead    = strspn($pattern, '%');
            $trail   = strspn(strrev($pattern), '%');
            $coreLen = $len - $lead - $trail;

            if ($coreLen <= 0) {
                // Pattern is entirely %'s (NOT LIKE case falls through above, only applies to LIKE)
                $final = $pattern; // unreachable for LIKE due to early return
            } else {
                $left  = substr($pattern, 0, $lead);
                $core  = substr($pattern, $lead, $coreLen);
                $right = substr($pattern, $len - $trail);

                $core = str_replace('\\', '\\\\', $core);
                if (!$this->allowInnerWildcards) {
                    $core = str_replace(['%', '_'], ['\\%', '\\_'], $core);
                }
                $final = $left . $core . $right;
            }

            $quoted = $xoopsDB->quoteString($final);
            // IMPORTANT: no ESCAPE clause for MySQL/MariaDB
            return $clause . ' ' . $op . ' ' . $quoted;
        }

        // Equality/comparisons: keep integers numeric; quote strings via DB layer
        $v = $this->value;
        if (is_int($v) || (is_string($v) && preg_match('/^-?\d+$/', $v))) {
            $safe = (string)(int)$v;
        } else {
            $safe = $xoopsDB->quoteString((string)$v);
        }

        return $clause . ' ' . $op . ' ' . $safe;
    }

    /**
     * Generate an LDAP filter from criteria (unchanged semantics)
     *
     * @return string
     */
    public function renderLdap()
    {
        if ($this->operator === '>') {
            $this->operator = '>=';
        }
        if ($this->operator === '<') {
            $this->operator = '<=';
        }

        if ($this->operator === '!=' || $this->operator === '<>') {
            $operator = '=';
            $clause   = '(!(' . $this->column . $operator . $this->value . '))';
        } else {
            if ($this->operator === 'IN') {
                $newvalue = str_replace(['(', ')'], '', $this->value);
                $tab      = explode(',', $newvalue);
                $clause = '';
                foreach ($tab as $uid) {
                    $clause .= "({$this->column}={$uid})";
                }
                $clause = '(|' . $clause . ')';
            } else {
                $clause = '(' . $this->column . $this->operator . $this->value . ')';
            }
        }

        return $clause;
    }

    /**
     * Convenience: render with leading WHERE (or empty if no condition)
     *
     * @return string
     */
    public function renderWhere()
    {
        $cond = $this->render();
        return empty($cond) ? '' : "WHERE {$cond}";
    }
}
