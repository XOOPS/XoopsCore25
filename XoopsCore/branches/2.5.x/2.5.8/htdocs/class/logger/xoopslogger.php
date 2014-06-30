<?php
/**
 * Xoops Logger handlers - component main class file
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      logger
 * @since           2.3.0
 * @author          Kazumi Ono  <onokazu@xoops.org>
 * @author          Skalpa Keo <skalpa@xoops.org>
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id$
 *
 * @todo            Not well written, just keep as it is. Refactored in 3.0
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Collects information for a page request
 *
 * Records information about database queries, blocks, and execution time
 * and can display it as HTML. It also catches php runtime errors.
 *
 * @package kernel
 */
class XoopsLogger
{
    /**
     * *#@+
     *
     * @var array
     */
    var $queries = array();
    var $blocks = array();
    var $extra = array();
    var $logstart = array();
    var $logend = array();
    var $errors = array();
    var $deprecated = array();
    /**
     * *#@-
     */

    var $usePopup = false;
    var $activated = true;

    /**
     * *@access protected
     */
    var $renderingEnabled = false;

    /**
     * XoopsLogger::__construct()
     */
    function __construct()
    {
    }

    /**
     * XoopsLogger::XoopsLogger()
     */
    function XoopsLogger()
    {
    }

    /**
     * Deprecated, use getInstance() instead
     */
    function &instance()
    {
        return XoopsLogger::getInstance();
    }

    /**
     * Get a reference to the only instance of this class
     *
     * @return object XoopsLogger  reference to the only instance
     */
    static function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new XoopsLogger();
            // Always catch errors, for security reasons
            set_error_handler('XoopsErrorHandler_HandleError');
        }

        return $instance;
    }

    /**
     * Enable logger output rendering
     * When output rendering is enabled, the logger will insert its output within the page content.
     * If the string <!--{xo-logger-output}--> is found in the page content, the logger output will
     * replace it, otherwise it will be inserted after all the page output.
     */
    function enableRendering()
    {
        if (!$this->renderingEnabled) {
            ob_start(array(&$this , 'render'));
            $this->renderingEnabled = true;
        }
    }

    /**
     * Returns the current microtime in seconds.
     *
     * @return float
     */
    function microtime()
    {
        $now = explode(' ', microtime());

        return (float) $now[0] + (float) $now[1];
    }

    /**
     * Start a timer
     *
     * @param string $name name of the timer
     */
    function startTime($name = 'XOOPS')
    {
        if ($this->activated)
            $this->logstart[$name] = $this->microtime();
    }

    /**
     * Stop a timer
     *
     * @param string $name name of the timer
     */
    function stopTime($name = 'XOOPS')
    {
        if ($this->activated)
            $this->logend[$name] = $this->microtime();
    }

    /**
     * Log a database query
     *
     * @param string $sql        SQL string
     * @param string $error      error message (if any)
     * @param int    $errno      error number (if any)
     * @param null   $query_time
     */
    function addQuery($sql, $error = null, $errno = null, $query_time = null)
    {
        if ($this->activated)
            $this->queries[] = array('sql' => $sql , 'error' => $error , 'errno' => $errno, 'query_time' => $query_time);
    }

    /**
     * Log display of a block
     *
     * @param string $name      name of the block
     * @param bool   $cached    was the block cached?
     * @param int    $cachetime cachetime of the block
     */
    function addBlock($name, $cached = false, $cachetime = 0)
    {
        if ($this->activated)
            $this->blocks[] = array('name' => $name , 'cached' => $cached , 'cachetime' => $cachetime);
    }

    /**
     * Log extra information
     *
     * @param string $name name for the entry
     * @param int    $msg  text message for the entry
     */
    function addExtra($name, $msg)
    {
        if ($this->activated)
            $this->extra[] = array('name' => $name , 'msg' => $msg);
    }

    /**
     * Log messages for deprecated functions
     *
     * @deprecated
     *
     * @param int $msg text message for the entry
     *
     */
    function addDeprecated($msg)
    {
        if ($this->activated)
            $this->deprecated[] = $msg;
    }

    /**
     * Error handling callback (called by the zend engine)
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  string  $errline
     */
    function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($this->activated && ($errno & error_reporting())) {
            // NOTE: we only store relative pathnames
            $this->errors[] = compact('errno', 'errstr', 'errfile', 'errline');
        }
        if ($errno == E_USER_ERROR) {
            $trace = true;
            if (substr($errstr, 0, '8') == 'notrace:') {
                $trace = false;
                $errstr = substr($errstr, 8);
            }
            echo sprintf(_XOOPS_FATAL_MESSAGE, $errstr);
            if ($trace && function_exists('debug_backtrace')) {
                echo "<div style='color:#f0f0f0;background-color:#f0f0f0'>" . _XOOPS_FATAL_BACKTRACE . ":<br />";
                $trace = debug_backtrace();
                array_shift($trace);
                foreach ($trace as $step) {
                    if (isset($step['file'])) {
                        echo $this->sanitizePath($step['file']);
                        echo ' (' . $step['line'] . ")\n<br />";
                    }
                }
                echo '</div>';
            }
            exit();
        }
    }

    /**
     *
     * @access protected
     *
     * @param  string $path
     *
     * @return mixed|string
     */
    function sanitizePath($path)
    {
        $path = str_replace(array('\\' , XOOPS_ROOT_PATH , str_replace('\\', '/', realpath(XOOPS_ROOT_PATH))), array('/' , '' , ''), $path);

        return $path;
    }

    /**
     * Output buffering callback inserting logger dump in page output
     */
    function render($output)
    {
        global $xoopsUser;
        if (!$this->activated) {
            return $output;
        }

        $log = $this->dump($this->usePopup ? 'popup' : '');
        $this->renderingEnabled = $this->activated = false;
        $pattern = '<!--{xo-logger-output}-->';
        $pos = strpos($output, $pattern);
        if ($pos !== false) {
            return substr($output, 0, $pos) . $log . substr($output, $pos + strlen($pattern));
        } else {
            return $output . $log;
        }
    }

    /**
     * *#@+
     *
     * @protected
     */
    function dump($mode = '')
    {
        include XOOPS_ROOT_PATH . '/class/logger/render.php';

        return $ret;
    }

    /**
     * get the current execution time of a timer
     *
     * @param  string $name  name of the counter
     * @param  bool   $unset removes counter from global log
     * @return float  current execution time of the counter
     */
    function dumpTime($name = 'XOOPS', $unset = false)
    {
        if (!$this->activated)
            return null;

        if (!isset($this->logstart[$name])) {
            return 0;
        }
        $stop = isset($this->logend[$name]) ? $this->logend[$name] : $this->microtime();
        $start = $this->logstart[$name];

        if ($unset) {
            unset($this->logstart[$name]);
        }

        return $stop - $start;
    }

    /**
     * XoopsLogger::triggerError()
     *
     * @deprecated
     * @param  int     $errkey
     * @param  string  $errStr
     * @param  string  $errFile
     * @param  string  $errLine
     * @param  integer $errNo
     * @return void
*/
    function triggerError($errkey = 0, $errStr = '', $errFile = '', $errLine = '', $errNo = 0)
    {
        $GLOBALS['xoopsLogger']->addDeprecated('\'$xoopsLogger->triggerError();\' is deprecated since XOOPS 2.5.4');

        if (!empty($errStr)) {
            $errStr = sprintf($errStr,$errkey );
           }
        $errFile = $this->sanitizePath($errFile);
        $this->handleError($errNo, $errStr, $errFile, $errLine);
    }

    /**
     * *#@+
     *
     * @deprecated
     */
    function dumpAll()
    {
        $GLOBALS['xoopsLogger']->addDeprecated('\'$xoopsLogger->dumpAll();\' is deprecated since XOOPS 2.5.4, please use \'$xoopsLogger->dump(\'\');\' instead.');

        return $this->dump('');
    }

    /**
     * dnmp Blocks @deprecated
     *
     * @return dump
     */
    function dumpBlocks()
    {
        $GLOBALS['xoopsLogger']->addDeprecated('\'$xoopsLogger->dumpBlocks();\' is deprecated since XOOPS 2.5.4, please use \'$xoopsLogger->dump(\'blocks\');\' instead.');

        return $this->dump('blocks');
    }

    /**
     * dumpExtra @deprecated
     *
     * @return dimp
     */
    function dumpExtra()
    {
        $GLOBALS['xoopsLogger']->addDeprecated('\'$xoopsLogger->dumpExtra();\' is deprecated since XOOPS 2.5.4, please use \'$xoopsLogger->dump(\'extra\');\' instead.');

        return $this->dump('extra');
    }

    /**
     * dump Queries @deprecated
     *
     * @return unknown
     */
    function dumpQueries()
    {
        $GLOBALS['xoopsLogger']->addDeprecated('\'$xoopsLogger->dumpQueries();\' is deprecated since XOOPS 2.5.4, please use \'$xoopsLogger->dump(\'queries\');\' instead.');

        return $this->dump('queries');
    }
/**
 * *#@-
 */
}

/**
 * PHP Error handler
 *
 * NB: You're not supposed to call this function directly, if you don't understand why, then
 * you'd better spend some time reading your PHP manual before you hurt somebody
 *
 * @internal : Using a function and not calling the handler method directly because of old PHP versions
 * set_error_handler() have problems with the array( obj,methodname ) syntax
 */
function XoopsErrorHandler_HandleError($errNo, $errStr, $errFile, $errLine, $errContext = null)
{
    // We don't want every error to come through this will help speed things up'
    if ($errNo == '2048') {
        return true;
    }
    // XOOPS should always be STRICT compliant thus the above lines makes no sense and will be removed! -- Added by Taiwen Jiang
    $logger =& XoopsLogger::getInstance();
    $logger->handleError($errNo, $errStr, $errFile, $errLine, $errContext);
}
