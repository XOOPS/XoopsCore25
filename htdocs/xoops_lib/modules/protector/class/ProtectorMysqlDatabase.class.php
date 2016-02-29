<?php

if (file_exists(XOOPS_ROOT_PATH . '/class/database/drivers/' . XOOPS_DB_TYPE . '/database.php')) {
    require_once XOOPS_ROOT_PATH . '/class/database/drivers/' . XOOPS_DB_TYPE . '/database.php';
} else {
    require_once XOOPS_ROOT_PATH . '/class/database/' . XOOPS_DB_TYPE . 'database.php';
}

require_once XOOPS_ROOT_PATH . '/class/database/database.php';

/**
 * Class ProtectorMySQLDatabase
 */
class ProtectorMySQLDatabase extends XoopsMySQLDatabaseProxy
{
    public $doubtful_requests = array();
    public $doubtful_needles  = array(
        // 'order by' ,
        'concat',
        'information_schema',
        'select',
        'union',
        '/*', /**/
        '--',
        '#');

    /**
     * ProtectorMySQLDatabase constructor.
     */
    public function __construct()
    {
        $protector               = Protector::getInstance();
        $this->doubtful_requests = $protector->getDblayertrapDoubtfuls();
        $this->doubtful_needles  = array_merge($this->doubtful_needles, $this->doubtful_requests);
    }

    /**
     * @param $sql
     */
    public function injectionFound($sql)
    {
        $protector = Protector::getInstance();

        $protector->last_error_type = 'SQL Injection';
        $protector->message .= $sql;
        $protector->output_log($protector->last_error_type);
        die('SQL Injection found');
    }

    /**
     * @param $sql
     *
     * @return array
     */
    public function separateStringsInSQL($sql)
    {
        $sql            = trim($sql);
        $sql_len        = strlen($sql);
        $char           = '';
        $string_start   = '';
        $in_string      = false;
        $sql_wo_string  = '';
        $strings        = array();
        $current_string = '';

        for ($i = 0; $i < $sql_len; ++$i) {
            $char = $sql[$i];
            if ($in_string) {
                while (1) {
                    $new_i = strpos($sql, $string_start, $i);
                    $current_string .= substr($sql, $i, $new_i - $i + 1);
                    $i = $new_i;
                    if ($i === false) {
                        break 2;
                    } elseif (/* $string_start == '`' || */
                        $sql[$i - 1] !== '\\'
                    ) {
                        $string_start = '';
                        $in_string    = false;
                        $strings[]    = $current_string;
                        break;
                    } else {
                        $j                 = 2;
                        $escaped_backslash = false;
                        while ($i - $j > 0 && $sql[$i - $j] === '\\') {
                            $escaped_backslash = !$escaped_backslash;
                            ++$j;
                        }
                        if ($escaped_backslash) {
                            $string_start = '';
                            $in_string    = false;
                            $strings[]    = $current_string;
                            break;
                        } else {
                            ++$i;
                        }
                    }
                }
            } elseif ($char === '"' || $char === "'") { // dare to ignore ``
                $in_string      = true;
                $string_start   = $char;
                $current_string = $char;
            } else {
                $sql_wo_string .= $char;
            }
            // dare to ignore comment
            // because unescaped ' or " have been already checked in stage1
        }

        return array($sql_wo_string, $strings);
    }

    /**
     * @param $sql
     */
    public function checkSql($sql)
    {
        list($sql_wo_strings, $strings) = $this->separateStringsInSQL($sql);

        // stage1: addslashes() processed or not
        foreach ($this->doubtful_requests as $request) {
            if (addslashes($request) != $request) {
                if (false !== stripos($sql, trim($request))) {
                    // check the request stayed inside of strings as whole
                    $ok_flag = false;
                    foreach ($strings as $string) {
                        if (false !== strpos($string, $request)) {
                            $ok_flag = true;
                            break;
                        }
                    }
                    if (!$ok_flag) {
                        $this->injectionFound($sql);
                    }
                }
            }
        }

        // stage2: doubtful requests exists and outside of quotations ('or")
        // $_GET['d'] = '1 UNION SELECT ...'
        // NG: select a from b where c=$d
        // OK: select a from b where c='$d_escaped'
        // $_GET['d'] = '(select ... FROM)'
        // NG: select a from b where c=(select ... from)
        foreach ($this->doubtful_requests as $request) {
            if (false !== strpos($sql_wo_strings, trim($request))) {
                $this->injectionFound($sql);
            }
        }

        // stage3: comment exists or not without quoted strings (too sensitive?)
        if (preg_match('/(\/\*|\-\-|\#)/', $sql_wo_strings, $regs)) {
            foreach ($this->doubtful_requests as $request) {
                if (false !== strpos($request, $regs[1])) {
                    $this->injectionFound($sql);
                }
            }
        }
    }

    /**
     * @param string $sql
     * @param int    $limit
     * @param int    $start
     *
     * @return resource
     */
    public function &query($sql, $limit = 0, $start = 0)
    {
        $sql4check = substr($sql, 7);
        foreach ($this->doubtful_needles as $needle) {
            if (false !== stripos($sql4check, $needle)) {
                $this->checkSql($sql);
                break;
            }
        }

        if (!defined('XOOPS_DB_PROXY')) {
            $ret = parent::queryF($sql, $limit, $start);
        } else {
            $ret = parent::query($sql, $limit, $start);
        }

        return $ret;
    }
}
