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
    /** @var bool|null Cached legacy log flag */
    private static $legacyLogEnabled = null;

    /**
     * Initialize logging flag once
     */
    private static function isLegacyLogEnabled(): bool
    {
        if (self::$legacyLogEnabled === null) {
            self::$legacyLogEnabled = defined('XOOPS_DB_LEGACY_LOG') && XOOPS_DB_LEGACY_LOG;
        }
        return self::$legacyLogEnabled;
    }

    /**
     * Set the global default for allowing inner wildcards in LIKE patterns.
     * Useful during migrations of legacy modules that intentionally use inner wildcards.
     *
     * @param bool $on
     * @return void
     */
    public static function setDefaultAllowInnerWildcards(bool $on = true): void
    {
        self::$defaultAllowInnerWildcards = $on;
    }

    /**
     * Opt-in per instance for intentional inner wildcards in LIKE patterns.
     * Default remains secure (inner %/_ escaped).
     *
     * @param bool $on
     * @return $this
     */
    public function allowInnerWildcards(bool $on = true): self
    {
        $this->allowInnerWildcards = $on;
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
     * @param \XoopsDatabase|null $db Database connection
     * @return string SQL fragment
     * @throws RuntimeException if database connection is not available
     */
    public function render(?\XoopsDatabase $db = null): string
    {
        if ($db === null) {
            $db = \XoopsDatabaseFactory::getDatabaseConnection();
        }

        if (!$db) {
            throw new RuntimeException("Database connection required to render Criteria");
        }

        $col = (string)($this->column ?? '');

        if ($col === '') {
            return '';
        }

        $backtick = (strpos($col, '.') === false && strpos($col, '(') === false) ? '`' : '';
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

        $valStr = (string)$this->value;
        if (trim($valStr) === '' && !$this->allowEmptyValue) {
            return '';
        }

        // IN / NOT IN
        if ($op === 'IN' || $op === 'NOT IN') {
            if (is_array($this->value)) {
                $parts = [];
                foreach ($this->value as $v) {
                    if (is_int($v) || (is_string($v) && preg_match('/^-?\d+$/', $v))) {
                        $parts[] = (string)(int)$v;
                    } else {
                        $parts[] = $db->quote((string)$v);
                    }
                }
            return $clause . ' ' . $op . ' (' . implode(',', $parts) . ')';
        }

            // Legacy format: preformatted string "(...)"
            // Early return if logging disabled (performance optimization)
            if (!self::isLegacyLogEnabled()) {
            return $clause . ' ' . $op . ' ' . $this->value;
        }

            // Log for migration tracking (only when enabled)
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $caller = $bt[1] ?? []; // 0 = render(), 1 = immediate caller
            $file = $caller['file'] ?? 'unknown';
            $line = $caller['line'] ?? 0;
            $message = sprintf(
                'Legacy Criteria IN format used for column "%s" with value "%s" at %s:%d',
                $this->column,
                is_scalar($this->value) ? (string)$this->value : gettype($this->value),
                $file,
                $line
            );

            // Prefer XoopsLogger if available
            if (class_exists('XoopsLogger')) {
                \XoopsLogger::getInstance()->addExtra('CriteriaLegacyIN', $message);
        } else {
                error_log($message);
        }

            // Optional: deprecation notice in debug mode
            if (defined('XOOPS_DEBUG') && XOOPS_DEBUG) {
                trigger_error($message, E_USER_DEPRECATED);
            }

            return $clause . ' ' . $op . ' ' . $this->value;
        }

        // LIKE / NOT LIKE – BC: just quote the pattern
        if ($op === 'LIKE' || $op === 'NOT LIKE') {
            $pattern = (string)$this->value;
            $quoted  = $db->quote($pattern);
            return $clause . ' ' . $op . ' ' . $quoted;
        }

        // All other operators: =, <, >, <=, >=, !=, <>
        // Backtick bypass for column-to-column comparisons
        if (strlen($valStr) > 1 && $valStr[0] === '`' && $valStr[strlen($valStr)-1] === '`') {
            // Validate backticked value (security: only allow safe chars)
            if (preg_match('/^[a-zA-Z0-9_\.\-`]*$/', $valStr)) {
                $safeValue = $valStr;
        } else {
                // Invalid chars in backticked value → use empty backticks (old behavior)
                $safeValue = '``';
            }
        } else {
            // Regular value - keep integers unquoted, quote strings
            if (is_int($this->value) || (is_string($this->value) && preg_match('/^-?\d+$/', $this->value))) {
                $safeValue = (string)(int)$this->value;
            } else {
                $safeValue = $db->quote((string)$this->value);
            }
        }

        return $clause . ' ' . $op . ' ' . $safeValue;
    }

    /**
     * Render with leading WHERE clause
     *
     * @return string SQL WHERE clause or empty string
     */
    public function renderWhere(): string
    {
        $cond = $this->render();
        return empty($cond) ? '' : "WHERE {$cond}";
    }

    /**
     * Generate an LDAP filter from criteria
     *
     * @return string LDAP filter
     */
    public function renderLdap(): string
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
}
