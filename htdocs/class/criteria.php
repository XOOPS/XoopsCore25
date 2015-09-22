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
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      database
 * @since           2.0.0
 * @author          Kazumi Ono <onokazu@xoops.org>
 * @author          Nathan Dial
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

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
    var $order = 'ASC';

    /**
     *
     * @var string
     */
    var $sort = '';

    /**
     * Number of records to retrieve
     *
     * @var int
     */
    var $limit = 0;

    /**
     * Offset of first record
     *
     * @var int
     */
    var $start = 0;

    /**
     *
     * @var string
     */
    var $groupby = '';

    /**
     * Constructor
     */
    function CriteriaElement()
    {
    }

    /**
     * Render the criteria element
     */
    function render()
    {
    }

    /**
     *
     * @param string $sort
     */
    function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     *
     * @return string
     */
    function getSort()
    {
        return $this->sort;
    }

    /**
     *
     * @param string $order
     */
    function setOrder($order)
    {
        if ('DESC' == strtoupper($order)) {
            $this->order = 'DESC';
        }
    }

    /**
     *
     * @return string
     */
    function getOrder()
    {
        return $this->order;
    }

    /**
     *
     * @param int $limit
     */
    function setLimit($limit = 0)
    {
        $this->limit = (int)($limit);
    }

    /**
     *
     * @return int
     */
    function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @param int $start
     */
    function setStart($start = 0)
    {
        $this->start = (int)($start);
    }

    /**
     *
     * @return int
     */
    function getStart()
    {
        return $this->start;
    }

    /**
     *
     * @param string $group
     */
    function setGroupby($group)
    {
        $this->groupby = $group;
    }

    /**
     *
     * @return string
     */
    function getGroupby()
    {
        return $this->groupby ? " GROUP BY {$this->groupby}" : "";
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
    var $criteriaElements = array();

    /**
     * Conditions
     *
     * @var array
     */
    var $conditions = array();

    /**
     * Constructor
     *
     * @param object $ele
     * @param string $condition
     */
    function CriteriaCompo($ele = null, $condition = 'AND')
    {
        if (isset($ele)) {
            $this->add($ele, $condition);
        }
    }

    /**
     * Add an element
     *
     * @param object $criteriaElement
     * @param string $condition
     *
     * @return object reference to this collection
     */
    function &add(&$criteriaElement, $condition = 'AND')
    {
        if (is_object($criteriaElement)) {
            $this->criteriaElements[] =& $criteriaElement;
            $this->conditions[] = $condition;
        }

        return $this;
    }

    /**
     * Make the criteria into a query string
     *
     * @return string
     */
    function render()
    {
        $ret = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $render_string = $this->criteriaElements[0]->render();
            for ($i = 1; $i < $count; ++$i) {
                if (!$render = $this->criteriaElements[$i]->render())
                    continue;
                $render_string .= (empty($render_string) ? "" : ' ' . $this->conditions[$i] . ' ') . $render;
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
    function renderWhere()
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
    function renderLdap()
    {
        $retval = '';
        $count = count($this->criteriaElements);
        if ($count > 0) {
            $retval = $this->criteriaElements[0]->renderLdap();
            for ($i = 1; $i < $count; ++$i) {
                $cond = strtoupper($this->conditions[$i]);
                $op = ($cond == "OR") ? "|" : "&";
                $retval = "({$op}{$retval}" . $this->criteriaElements[$i]->renderLdap() . ")";
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
    /**
     *
     * @var string
     */
    var $prefix;
    var $function;
    var $column;
    var $operator;
    var $value;

    /**
     * Constructor
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     * @param string $prefix
     * @param string $function
     */
    function Criteria($column, $value = '', $operator = '=', $prefix = '', $function = '')
    {
        $this->prefix = $prefix;
        $this->function = $function;
        $this->column = $column;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * Make a sql condition string
     *
     * @return string
     */
    function render()
    {
        $clause = (!empty($this->prefix) ? "{$this->prefix}." : "") . $this->column;
        if (!empty($this->function)) {
            $clause = sprintf($this->function, $clause);
        }
        if (in_array(strtoupper($this->operator), array('IS NULL' , 'IS NOT NULL'))) {
            $clause .= ' ' . $this->operator;
        } else {
            if ('' === ($value = trim($this->value))) {
                return '';
            }
            if (!in_array(strtoupper($this->operator), array('IN' , 'NOT IN'))) {
                if ((substr($value, 0, 1) != '`') && (substr($value, - 1) != '`')) {
                    $value = "'{$value}'";
                } else if (!preg_match('/^[a-zA-Z0-9_\.\-`]*$/', $value)) {
                    $value = '``';
                }
            }
            $clause .= " {$this->operator} {$value}";
        }

        return $clause;
    }

    /**
     * Generate an LDAP filter from criteria
     *
     * @return string
     * @author Nathan Dial ndial@trillion21.com, improved by Pierre-Eric MENUET pemen@sourceforge.net
     */
    function renderLdap()
    {
        if ($this->operator == '>') {
            $this->operator = '>=';
        }
        if ($this->operator == '<') {
            $this->operator = '<=';
        }

        if ($this->operator == '!=' || $this->operator == '<>') {
            $operator = '=';
            $clause = "(!(" . $this->column . $operator . $this->value . "))";
        } else {
            if ($this->operator == 'IN') {
                $newvalue = str_replace(array('(' , ')'), '', $this->value);
                $tab = explode(',', $newvalue);
                foreach ($tab as $uid) {
                    $clause .= "({$this->column}={$uid})";
                }
                $clause = '(|' . $clause . ')';
            } else {
                $clause = "(" . $this->column . $this->operator . $this->value . ")";
            }
        }

        return $clause;
    }

    /**
     * Make a SQL "WHERE" clause
     *
     * @return string
     */
    function renderWhere()
    {
        $cond = $this->render();

        return empty($cond) ? '' : "WHERE {$cond}";
    }
}
