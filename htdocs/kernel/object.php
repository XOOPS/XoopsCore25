<?php
/**
 * XOOPS Kernel Object
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
/**
 * YOU SHOULD NOT USE ANY OF THE UNICODE TYPES, THEY WILL BE REMOVED
 */

/**
 * *#@+
 * Xoops object datatype
 */
define('XOBJ_DTYPE_TXTBOX', 1);
define('XOBJ_DTYPE_TXTAREA', 2);
define('XOBJ_DTYPE_INT', 3);
define('XOBJ_DTYPE_URL', 4);
define('XOBJ_DTYPE_EMAIL', 5);
define('XOBJ_DTYPE_ARRAY', 6);
define('XOBJ_DTYPE_OTHER', 7);
define('XOBJ_DTYPE_SOURCE', 8);
define('XOBJ_DTYPE_STIME', 9);
define('XOBJ_DTYPE_MTIME', 10);
define('XOBJ_DTYPE_LTIME', 11);
define('XOBJ_DTYPE_FLOAT', 13);
define('XOBJ_DTYPE_DECIMAL', 14);
define('XOBJ_DTYPE_ENUM', 15);
// YOU SHOULD NEVER USE THE FOLLOWING TYPES, THEY WILL BE REMOVED
define('XOBJ_DTYPE_UNICODE_TXTBOX', 16);
define('XOBJ_DTYPE_UNICODE_TXTAREA', 17);
define('XOBJ_DTYPE_UNICODE_URL', 18);
define('XOBJ_DTYPE_UNICODE_EMAIL', 19);
define('XOBJ_DTYPE_UNICODE_ARRAY', 20);
define('XOBJ_DTYPE_UNICODE_OTHER', 21);
// Addition for 2.5.5
define('XOBJ_DTYPE_DATE', 22);
define('XOBJ_DTYPE_TIME', 23);
define('XOBJ_DTYPE_TIMESTAMP', 24);

/**
 * Base class for all objects in the Xoops kernel (and beyond)
 */
class XoopsObject
{
    /**
     * holds all variables(properties) of an object
     *
     * @var array
     * @access protected
     */
    public $vars = array();

    /**
     * variables cleaned for store in DB
     *
     * @var array
     * @access protected
     */
    public $cleanVars = array();

    /**
     * is it a newly created object?
     *
     * @var bool
     * @access private
     */
    public $_isNew = false;

    /**
     * has any of the values been modified?
     *
     * @var bool
     * @access private
     */
    public $_isDirty = false;

    /**
     * errors
     *
     * @var array
     * @access private
     */
    public $_errors = array();

    /**
     * additional filters registered dynamically by a child class object
     *
     * @access private
     */
    public $_filters = array();

    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
    public function __construct()
    {
    }

    /**
     * PHP 4 style constructor compatibility shim
     * @deprecated all callers should be using parent::__construct()
     */
    public function XoopsObject()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("Should call parent::__construct in {$trace[0]['file']} line {$trace[0]['line']},");
        self::__construct();
    }

    /**
     * *#@+
     * used for new/clone objects
     *
     * @access public
     */
    public function setNew()
    {
        $this->_isNew = true;
    }

    public function unsetNew()
    {
        $this->_isNew = false;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->_isNew;
    }

    /**
     * *#@+
     * mark modified objects as dirty
     *
     * used for modified objects only
     *
     * @access public
     */
    public function setDirty()
    {
        $this->_isDirty = true;
    }

    public function unsetDirty()
    {
        $this->_isDirty = false;
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->_isDirty;
    }

    /**
     * initialize variables for the object
     *
     * YOU SHOULD NOT USE THE $enumeration PARAMETER
     *
     * @access   public
     *
     * @param string $key
     * @param int    $data_type set to one of XOBJ_DTYPE_XXX constants (set to XOBJ_DTYPE_OTHER if no data type checking nor text sanitizing is required)
     * @param null   $value
     * @param bool   $required  require html form input?
     * @param int    $maxlength for XOBJ_DTYPE_TXTBOX type only
     * @param string $options
     * @param string $enumerations
     *
     * @return void
     */
    public function initVar($key, $data_type, $value = null, $required = false, $maxlength = null, $options = '', $enumerations = '')
    {
        $this->vars[$key] = array(
            'value'       => $value,
            'required'    => $required,
            'data_type'   => $data_type,
            'maxlength'   => $maxlength,
            'changed'     => false,
            'options'     => $options,
            'enumeration' => $enumerations);
    }

    /**
     * assign a value to a variable
     *
     * @access public
     * @param string $key   name of the variable to assign
     * @param mixed  $value value to assign
     */
    public function assignVar($key, $value)
    {
        if (isset($key) && isset($this->vars[$key])) {
            switch ($this->vars[$key]['data_type']) {
                case XOBJ_DTYPE_UNICODE_ARRAY:
                    if (is_array($value)) {
                        $this->vars[$key]['value'] =& array_walk($value, 'xoops_aw_decode');
                    } else {
                        $this->vars[$key]['value'] =& xoops_convert_decode($value);
                    }
                    break;
                case XOBJ_DTYPE_UNICODE_URL:
                case XOBJ_DTYPE_UNICODE_EMAIL:
                case XOBJ_DTYPE_UNICODE_OTHER:
                case XOBJ_DTYPE_UNICODE_TXTBOX:
                case XOBJ_DTYPE_UNICODE_TXTAREA:
                    $this->vars[$key]['value'] = xoops_convert_decode($value);
                    break;
                case XOBJ_DTYPE_DATE:
                    if (!is_string($value) && is_numeric($value)) {
                        $this->vars[$key]['value'] = date(_DBDATESTRING, $value);
                    } else {
                        $this->vars[$key]['value'] = date(_DBDATESTRING, strtotime($value));
                    }
                    break;
                case XOBJ_DTYPE_TIME:
                    if (!is_string($value) && is_numeric($value)) {
                        $this->vars[$key]['value'] = date(_DBTIMESTRING, $value);
                    } else {
                        $this->vars[$key]['value'] = date(_DBTIMESTRING, strtotime($value));
                    }
                    break;
                case XOBJ_DTYPE_TIMESTAMP:
                    if (!is_string($value) && is_numeric($value)) {
                        $this->vars[$key]['value'] = date(_DBTIMESTAMPSTRING, $value);
                    } else {
                        $this->vars[$key]['value'] = date(_DBTIMESTAMPSTRING, strtotime($value));
                    }
                    break;
                // YOU SHOULD NOT USE THE ABOVE TYPES, THEY WILL BE REMOVED
                default:
                    $this->vars[$key]['value'] =& $value;
            }
        }
    }

    /**
     * assign values to multiple variables in a batch
     *
     * @access   private
     * @param $var_arr
     * @internal param array $var_array associative array of values to assign
     */
    public function assignVars($var_arr)
    {
        foreach ($var_arr as $key => $value) {
            $this->assignVar($key, $value);
        }
    }

    /**
     * assign a value to a variable
     *
     * @access public
     * @param string $key   name of the variable to assign
     * @param mixed  $value value to assign
     * @param bool   $not_gpc
     */
    public function setVar($key, $value, $not_gpc = false)
    {
        if (!empty($key) && isset($value) && isset($this->vars[$key])) {
            $this->vars[$key]['value']   =& $value;
            $this->vars[$key]['not_gpc'] = $not_gpc;
            $this->vars[$key]['changed'] = true;
            $this->setDirty();
        }
    }

    /**
     * assign values to multiple variables in a batch
     *
     * @access private
     * @param array $var_arr associative array of values to assign
     * @param bool  $not_gpc
     */
    public function setVars($var_arr, $not_gpc = false)
    {
        foreach ($var_arr as $key => $value) {
            $this->setVar($key, $value, $not_gpc);
        }
    }

    /**
     * unset variable(s) for the object
     *
     * @access public
     *
     * @param mixed $var
     *
     * @return bool
     */
    public function destroyVars($var)
    {
        if (empty($var)) {
            return true;
        }
        $var = !is_array($var) ? array($var) : $var;
        foreach ($var as $key) {
            if (!isset($this->vars[$key])) {
                continue;
            }
            $this->vars[$key]['changed'] = null;
        }

        return true;
    }

    /**
     * @param $var
     * @return bool
     * @deprecated use destroyVars() instead,  destoryVars() will be removed in the next major release
     */
    public function destoryVars($var)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("XoopsObject::destoryVars() is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
        return $this->destroyVars($var);
    }

    /**
     * Assign values to multiple variables in a batch
     *
     * Meant for a CGI context:
     * - prefixed CGI args are considered save
     * - avoids polluting of namespace with CGI args
     *
     * @access private
     * @param array  $var_arr associative array of values to assign
     * @param string $pref    prefix (only keys starting with the prefix will be set)
     * @param bool   $not_gpc
     */
    public function setFormVars($var_arr = null, $pref = 'xo_', $not_gpc = false)
    {
        $len = strlen($pref);
        foreach ($var_arr as $key => $value) {
            if ($pref == substr($key, 0, $len)) {
                $this->setVar(substr($key, $len), $value, $not_gpc);
            }
        }
    }

    /**
     * returns all variables for the object
     *
     * @access public
     * @return array associative array of key->value pairs
     */
    public function &getVars()
    {
        return $this->vars;
    }

    /**
     * Returns the values of the specified variables
     *
     * @param  mixed  $keys     An array containing the names of the keys to retrieve, or null to get all of them
     * @param  string $format   Format to use (see getVar)
     * @param  int    $maxDepth Maximum level of recursion to use if some vars are objects themselves
     * @return array  associative array of key->value pairs
     */
    public function getValues($keys = null, $format = 's', $maxDepth = 1)
    {
        if (!isset($keys)) {
            $keys = array_keys($this->vars);
        }
        $vars = array();
        foreach ($keys as $key) {
            if (isset($this->vars[$key])) {
                if (is_object($this->vars[$key]) && is_a($this->vars[$key], 'XoopsObject')) {
                    if ($maxDepth) {
                        $vars[$key] = $this->vars[$key]->getValues(null, $format, $maxDepth - 1);
                    }
                } else {
                    $vars[$key] = $this->getVar($key, $format);
                }
            }
        }

        return $vars;
    }

    /**
     * returns a specific variable for the object in a proper format
     *
     * YOU SHOULD NOT USE ANY OF THE UNICODE TYPES, THEY WILL BE REMOVED
     *
     * @access public
     * @param  string $key    key of the object's variable to be returned
     * @param  string $format format to use for the output
     * @return mixed  formatted value of the variable
     */
    public function getVar($key, $format = 's')
    {
        $ret = null;
        if (!isset($this->vars[$key])) {
            return $ret;
        }
        $ret = $this->vars[$key]['value'];
        $ts  = MyTextSanitizer::getInstance();
        switch ($this->vars[$key]['data_type']) {
            case XOBJ_DTYPE_INT:
                $ret = (int) $ret;
                break;
            case XOBJ_DTYPE_UNICODE_TXTBOX:
            case XOBJ_DTYPE_TXTBOX:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                    case 'e':
                    case 'edit':
                        return $ts->htmlSpecialChars($ret);
                        break 1;
                    case 'p':
                    case 'preview':
                    case 'f':
                    case 'formpreview':
                        return $ts->htmlSpecialChars($ts->stripSlashesGPC($ret));
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_UNICODE_TXTAREA:
            case XOBJ_DTYPE_TXTAREA:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        $html   = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['doxcode']['value']) || $this->vars['doxcode']['value'] == 1) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value']) || $this->vars['dosmiley']['value'] == 1) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                        $br     = (!isset($this->vars['dobr']['value']) || $this->vars['dobr']['value'] == 1) ? 1 : 0;

                        return $ts->displayTarea($ret, $html, $smiley, $xcode, $image, $br);
                        break 1;
                    case 'e':
                    case 'edit':
                        return htmlspecialchars($ret, ENT_QUOTES);
                        break 1;
                    case 'p':
                    case 'preview':
                        $html   = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['doxcode']['value']) || $this->vars['doxcode']['value'] == 1) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value']) || $this->vars['dosmiley']['value'] == 1) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                        $br     = (!isset($this->vars['dobr']['value']) || $this->vars['dobr']['value'] == 1) ? 1 : 0;

                        return $ts->previewTarea($ret, $html, $smiley, $xcode, $image, $br);
                        break 1;
                    case 'f':
                    case 'formpreview':
                        return htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_UNICODE_ARRAY:
                switch (strtolower($format)) {
                    case 'n':
                    case 'none':
                        break 1;
                    default:
                        if (!is_array($ret)) {
                            if ($ret != '') {
                                $ret = unserialize($ret);
                            }
                            $ret = is_array($ret) ? $ret : array();
                            if (is_array($ret)) {
                                $ret = array_walk($ret, 'xoops_aw_decode');
                            }
                        }

                        return $ret;
                        break 1;
                }
                break;
            case XOBJ_DTYPE_ARRAY:
                switch (strtolower($format)) {
                    case 'n':
                    case 'none':
                        break 1;
                    default:
                        if (!is_array($ret)) {
                            if ($ret != '') {
                                $ret = unserialize($ret);
                            }
                            $ret = is_array($ret) ? $ret : array();
                        }

                        return $ret;
                        break 1;
                }
                break;
            case XOBJ_DTYPE_SOURCE:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        break 1;
                    case 'e':
                    case 'edit':
                        return htmlspecialchars($ret, ENT_QUOTES);
                        break 1;
                    case 'p':
                    case 'preview':
                        return $ts->stripSlashesGPC($ret);
                        break 1;
                    case 'f':
                    case 'formpreview':
                        return htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_DATE:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return date(_DBDATESTRING, strtotime($ret));
                        } else {
                            return date(_DBDATESTRING, $ret);
                        }
                        break 1;
                    case 'e':
                    case 'edit':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return htmlspecialchars(date(_DBDATESTRING, strtotime($ret)), ENT_QUOTES);
                        } else {
                            return htmlspecialchars(date(_DBDATESTRING, $ret), ENT_QUOTES);
                        }
                        break 1;
                    case 'p':
                    case 'preview':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return $ts->stripSlashesGPC(date(_DBDATESTRING, strtotime($ret)));
                        } else {
                            return $ts->stripSlashesGPC(date(_DBDATESTRING, $ret));
                        }
                        break 1;
                    case 'f':
                    case 'formpreview':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return htmlspecialchars($ts->stripSlashesGPC(date(_DBDATESTRING, strtotime($ret))), ENT_QUOTES);
                        } else {
                            return htmlspecialchars($ts->stripSlashesGPC(date(_DBDATESTRING, $ret)), ENT_QUOTES);
                        }
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_TIME:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return date(_DBTIMESTRING, strtotime($ret));
                        } else {
                            return date(_DBTIMESTRING, $ret);
                        }
                        break 1;
                    case 'e':
                    case 'edit':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return htmlspecialchars(date(_DBTIMESTRING, strtotime($ret)), ENT_QUOTES);
                        } else {
                            return htmlspecialchars(date(_DBTIMESTRING, $ret), ENT_QUOTES);
                        }
                        break 1;
                    case 'p':
                    case 'preview':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return $ts->stripSlashesGPC(date(_DBTIMESTRING, strtotime($ret)));
                        } else {
                            return $ts->stripSlashesGPC(date(_DBTIMESTRING, $ret));
                        }
                        break 1;
                    case 'f':
                    case 'formpreview':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return htmlspecialchars($ts->stripSlashesGPC(date(_DBTIMESTRING, strtotime($ret))), ENT_QUOTES);
                        } else {
                            return htmlspecialchars($ts->stripSlashesGPC(date(_DBTIMESTRING, $ret)), ENT_QUOTES);
                        }
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_TIMESTAMP:
                switch (strtolower($format)) {
                    case 's':
                    case 'show':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return date(_DBTIMESTAMPSTRING, strtotime($ret));
                        } else {
                            return date(_DBTIMESTAMPSTRING, $ret);
                        }
                        break 1;
                    case 'e':
                    case 'edit':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return htmlspecialchars(date(_DBTIMESTAMPSTRING, strtotime($ret)), ENT_QUOTES);
                        } else {
                            return htmlspecialchars(date(_DBTIMESTAMPSTRING, $ret), ENT_QUOTES);
                        }
                        break 1;
                    case 'p':
                    case 'preview':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return $ts->stripSlashesGPC(date(_DBTIMESTAMPSTRING, strtotime($ret)));
                        } else {
                            return $ts->stripSlashesGPC(date(_DBTIMESTAMPSTRING, $ret));
                        }
                        break 1;
                    case 'f':
                    case 'formpreview':
                        if (is_string($ret) && !is_numeric($ret)) {
                            return htmlspecialchars($ts->stripSlashesGPC(date(_DBTIMESTAMPSTRING, strtotime($ret))), ENT_QUOTES);
                        } else {
                            return htmlspecialchars($ts->stripSlashesGPC(date(_DBTIMESTAMPSTRING, $ret)), ENT_QUOTES);
                        }
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            default:
                if ($this->vars[$key]['options'] != '' && $ret != '') {
                    switch (strtolower($format)) {
                        case 's':
                        case 'show':
                            $selected = explode('|', $ret);
                            $options  = explode('|', $this->vars[$key]['options']);
                            $i        = 1;
                            $ret      = array();
                            foreach ($options as $op) {
                                if (in_array($i, $selected)) {
                                    $ret[] = $op;
                                }
                                ++$i;
                            }

                            return implode(', ', $ret);
                        case 'e':
                        case 'edit':
                            $ret = explode('|', $ret);
                            break 1;
                        default:
                            break 1;
                    }
                }
                break;
        }

        return $ret;
    }

    /**
     * clean values of all variables of the object for storage.
     * also add slashes wherever needed
     *
     * YOU SHOULD NOT USE ANY OF THE UNICODE TYPES, THEY WILL BE REMOVED
     *
     * @return bool true if successful
     * @access public
     */
    public function cleanVars()
    {
        $ts              = MyTextSanitizer::getInstance();
        $existing_errors = $this->getErrors();
        $this->_errors   = array();
        foreach ($this->vars as $k => $v) {
            $cleanv = $v['value'];
            if (!$v['changed']) {
            } else {
                $cleanv = is_string($cleanv) ? trim($cleanv) : $cleanv;
                switch ($v['data_type']) {
                    case XOBJ_DTYPE_TIMESTAMP:
                        $cleanv = !is_string($cleanv) && is_numeric($cleanv) ? date(_DBTIMESTAMPSTRING, $cleanv) : date(_DBTIMESTAMPSTRING, strtotime($cleanv));
                        break;
                    case XOBJ_DTYPE_TIME:
                        $cleanv = !is_string($cleanv) && is_numeric($cleanv) ? date(_DBTIMESTRING, $cleanv) : date(_DBTIMESTRING, strtotime($cleanv));
                        break;
                    case XOBJ_DTYPE_DATE:
                        $cleanv = !is_string($cleanv) && is_numeric($cleanv) ? date(_DBDATESTRING, $cleanv) : date(_DBDATESTRING, strtotime($cleanv));
                        break;
                    case XOBJ_DTYPE_TXTBOX:
                        if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        if (isset($v['maxlength']) && strlen($cleanv) > (int)$v['maxlength']) {
                            $this->setErrors(sprintf(_XOBJ_ERR_SHORTERTHAN, $k, (int)$v['maxlength']));
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                        } else {
                            $cleanv = $ts->censorString($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_TXTAREA:
                        if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                        } else {
                            $cleanv = $ts->censorString($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_SOURCE:
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_INT:
                        $cleanv = (int)$cleanv;
                        break;

                    case XOBJ_DTYPE_EMAIL:
                        if ($v['required'] && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $cleanv)) {
                            $this->setErrors('Invalid Email'); //_XOBJ_ERR_INVALID_EMAIL
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_URL:
                        if ($v['required'] && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                            $cleanv = XOOPS_PROT . $cleanv;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv =& $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_ARRAY:
                        $cleanv = (array)$cleanv;
                        $cleanv = serialize($cleanv);
                        break;
                    case XOBJ_DTYPE_STIME:
                    case XOBJ_DTYPE_MTIME:
                    case XOBJ_DTYPE_LTIME:
                        $cleanv = !is_string($cleanv) ? (int)$cleanv : strtotime($cleanv);
                        break;
                    case XOBJ_DTYPE_FLOAT:
                        $cleanv = (float)$cleanv;
                        break;
                    case XOBJ_DTYPE_DECIMAL:
                        $cleanv = (float)$cleanv;
                        break;
                    case XOBJ_DTYPE_ENUM:
                        if (!in_array($cleanv, $v['enumeration'])) {
                            $this->setErrors('Invalid Enumeration');//_XOBJ_ERR_INVALID_ENUMERATION
                            continue 2;
                        }
                        break;
                    case XOBJ_DTYPE_UNICODE_TXTBOX:
                        if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        $cleanv = xoops_convert_encode($cleanv);
                        if (isset($v['maxlength']) && strlen($cleanv) > (int)$v['maxlength']) {
                            $this->setErrors(sprintf(_XOBJ_ERR_SHORTERTHAN, $k, (int)$v['maxlength']));
                            continue 2;
                        }
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                        } else {
                            $cleanv = $ts->censorString($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_UNICODE_TXTAREA:
                        if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        $cleanv = xoops_convert_encode($cleanv);
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                        } else {
                            $cleanv = $ts->censorString($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_UNICODE_EMAIL:
                        if ($v['required'] && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $cleanv)) {
                            $this->setErrors('Invalid Email');
                            continue 2;
                        }
                        $cleanv = xoops_convert_encode($cleanv);
                        if (!$v['not_gpc']) {
                            $cleanv = $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_UNICODE_URL:
                        if ($v['required'] && $cleanv == '') {
                            $this->setErrors(sprintf(_XOBJ_ERR_REQUIRED, $k));
                            continue 2;
                        }
                        if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                            $cleanv = XOOPS_PROT . $cleanv;
                        }
                        $cleanv = xoops_convert_encode($cleanv);
                        if (!$v['not_gpc']) {
                            $cleanv =& $ts->stripSlashesGPC($cleanv);
                        }
                        break;
                    case XOBJ_DTYPE_UNICODE_ARRAY:
                        $cleanv = serialize(array_walk($cleanv, 'xoops_aw_encode'));
                        break;
                    default:
                        break;

                }
            }
            $this->cleanVars[$k] = str_replace('\\"', '"', $cleanv);
            unset($cleanv);
        }
        if (count($this->_errors) > 0) {
            $this->_errors = array_merge($existing_errors, $this->_errors);

            return false;
        }
        $this->_errors = array_merge($existing_errors, $this->_errors);
        $this->unsetDirty();

        return true;
    }

    /**
     * dynamically register additional filter for the object
     *
     * @param string $filtername name of the filter
     *
     * @deprecated \XoopsObject::registerFilter is deprecated since XOOPS 2.5.8 and will be removed in the next major release
     */
    public function registerFilter($filtername)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("XoopsObject::registerFilter() is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
        $this->_filters[] = $filtername;
    }

    /**
     * load all additional filters that have been registered to the object
     *
     * @access private
     */
    public function _loadFilters()
    {
        static $loaded;
        if (isset($loaded)) {
            return null;
        }
        $loaded = 1;

        $path = empty($this->plugin_path) ? __DIR__ . '/filters' : $this->plugin_path;
        if (file_exists($file = $path . '/filter.php')) {
            include_once $file;
            foreach ($this->_filters as $f) {
                if (file_exists($file = $path . '/' . strtolower($f) . 'php')) {
                    include_once $file;
                }
            }
        }
    }

    /**
     * load all local filters for the object
     *
     * Filter distribution:
     * In each module folder there is a folder "filter" containing filter files with,
     * filename: [name_of_target_class][.][function/action_name][.php];
     * function name: [dirname][_][name_of_target_class][_][function/action_name];
     * parameter: the target object
     *
     * @param string $method function or action name
     *
     * @deprecated \XoopsObject::loadFilters is deprecated since XOOPS 2.5.8 and will be removed in the next major release
     */
    public function loadFilters($method)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("XoopsObject::loadFilters() is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");

        $this->_loadFilters();

        xoops_load('XoopsCache');
        $class = get_class($this);
        if (!$modules_active = XoopsCache::read('system_modules_active')) {
            /* @var $module_handler XoopsModuleHandler */
            $module_handler = xoops_getHandler('module');
            $modules_obj    = $module_handler->getObjects(new Criteria('isactive', 1));
            $modules_active = array();
            foreach (array_keys($modules_obj) as $key) {
                $modules_active[] = $modules_obj[$key]->getVar('dirname');
            }
            unset($modules_obj);
            XoopsCache::write('system_modules_active', $modules_active);
        }
        foreach ($modules_active as $dirname) {
            if (file_exists($file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/filter/' . $class . '.' . $method . '.php')) {
                include_once $file;
                if (function_exists($class . '_' . $method)) {
                    call_user_func_array($dirname . '_' . $class . '_' . $method, array(&$this));
                }
            }
        }
    }

    /**
     * create a clone(copy) of the current object
     *
     * @access public
     * @return object clone
     */
    public function xoopsClone()
    {
        $class = get_class($this);
        $clone = null;
        $clone = new $class();
        foreach ($this->vars as $k => $v) {
            $clone->assignVar($k, $v['value']);
        }
        // need this to notify the handler class that this is a newly created object
        $clone->setNew();

        return $clone;
    }

    /**
     * Adjust a newly cloned object
     */
    public function __clone()
    {
        // need this to notify the handler class that this is a newly created object
        $this->setNew();
    }

    /**
     * add an error
     *
     * @param $err_str
     * @internal param string $value error to add
     * @access   public
     */
    public function setErrors($err_str)
    {
        if (is_array($err_str)) {
            $this->_errors = array_merge($this->_errors, $err_str);
        } else {
            $this->_errors[] = trim($err_str);
        }
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * return the errors for this object as html
     *
     * @return string html listing the errors
     * @access public
     */
    public function getHtmlErrors()
    {
        $ret = '<h4>Errors</h4>';
        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $ret .= $error . '<br>';
            }
        } else {
            $ret .= 'None<br>';
        }

        return $ret;
    }

    /**
     * Returns an array representation of the object
     *
     * Deprecated, use getValues() directly
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getValues();
    }
}

/**
 * XOOPS object handler class.
 * This class is an abstract class of handler classes that are responsible for providing
 * data access mechanisms to the data source of its corresponding data objects
 *
 * @package             kernel
 * @abstract
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsObjectHandler
{
    /**
     * XoopsDatabase holds referenced to {@link XoopsDatabase} class object
     *
     * @var XoopsDatabase
     */
    public $db;

    /**
     * called from child classes only
     *
     * @param XoopsDatabase $db reference to the {@link XoopsDatabase} object
     * @access protected
     */
    public function __construct(XoopsDatabase $db)
    {
        /* @var $db XoopsMySQLDatabase  */
        $this->db = $db;
    }

    /**
     * PHP 4 style constructor compatibility shim
     *
     * @param XoopsDatabase $db database object
     * @deprecated all callers should be using parent::__construct()
     */
    public function XoopsObjectHandler($db)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("Should call parent::__construct in {$trace[0]['file']} line {$trace[0]['line']},");
        self::__construct($db);
    }

    /**
     * creates a new object
     *
     * @abstract
     * @return XoopsObject
     */
    public function create()
    {
    }

    /**
     * gets a value object
     *
     * @param int $int_id
     * @abstract
     * @return XoopsObject
     */
    public function get($int_id)
    {
    }

    /**
     * insert/update object
     *
     * @param XoopsObject $object
     * @abstract
     */
    public function insert(XoopsObject $object)
    {
    }

    /**
     * delete object from database
     *
     * @param XoopsObject $object
     * @abstract
     */
    public function delete(XoopsObject $object)
    {
    }
}

/**
 * Persistable Object Handler class.
 *
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              Jan Keller Pedersen <mithrandir@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             Kernel
 */
class XoopsPersistableObjectHandler extends XoopsObjectHandler
{
    /**
     * holds reference to custom extended object handler
     *
     * var object
     *
     * @access private
     */
    /**
     * static protected
     */
    public $handler;

    /**
     * holds reference to predefined extended object handlers: read, stats, joint, write, sync
     *
     * The handlers hold methods for different purposes, which could be all put together inside of current class.
     * However, load codes only if they are necessary, thus they are now split out.
     *
     * var array of objects
     *
     * @access private
     */
    /**
     * static protected
     */
    public $handlers = array('read' => null, 'stats' => null, 'joint' => null, 'write' => null, 'sync' => null);

    /**
     * Information about the class, the handler is managing
     *
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $keyName;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $identifierName;

    /**
     * @var string
     */
    public $field_link;

    /**
     * @var string
     */
    public $field_object;

    /**
     * Constructor
     *
     * @param null|XoopsDatabase $db             database connection
     * @param string             $table          Name of database table
     * @param string             $className      Name of the XoopsObject class this handler manages
     * @param string             $keyName        Name of the property holding the key
     * @param string             $identifierName Name of the property holding an identifier
     *                                            name (title, name ...), used on getList()
     */
    public function __construct(XoopsDatabase $db = null, $table = '', $className = '', $keyName = '', $identifierName = '')
    {
        $db    = XoopsDatabaseFactory::getDatabaseConnection();
        $table = $db->prefix($table);
        parent::__construct($db);
        $this->table     = $table;
        $this->keyName   = $keyName;
        $this->className = $className;
        if ($identifierName) {
            $this->identifierName = $identifierName;
        }
    }

    /**
     * PHP 4 style constructor compatibility shim
     *
     * @param null|XoopsDatabase $db             database connection
     * @param string             $table          Name of database table
     * @param string             $className      Name of the XoopsObject class this handler manages
     * @param string             $keyName        Name of the property holding the key
     * @param string             $identifierName Name of the property holding an identifier
     *                                            name (title, name ...), used on getList()
     *
     * @deprecated all callers should be using parent::__construct()
     */
    public function XoopsPersistableObjectHandler(XoopsDatabase $db = null, $table = '', $className = '', $keyName = '', $identifierName = '')
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error("Should call parent::__construct in {$trace[0]['file']} line {$trace[0]['line']},");
        self::__construct($db, $table, $className, $keyName, $identifierName);
    }

    /**
     * Set custom handler
     *
     * @access   protected
     * @param null|string   $handler
     * @param null   $args
     * @param string $path path to class
     * @internal param object $handler
     * @internal param mixed  $args
     * @return object of handler
     */
    public function setHandler($handler = null, $args = null, $path = null)
    {
        $this->handler = null;
        if (is_object($handler)) {
            $this->handler = $handler;
        } elseif (is_string($handler)) {
            xoops_load('XoopsModelFactory');
            $this->handler = XoopsModelFactory::loadHandler($this, $handler, $args);
        }

        return $this->handler;
    }

    /**
     * Load predefined handler
     *
     * @access protected
     * @param  string $name handler name
     * @param  mixed  $args args
     * @return XoopsModelAbstract of handler {@link XoopsModelAbstract}
     */
    public function loadHandler($name, $args = null)
    {
        static $handlers;
        if (!isset($handlers[$name])) {
            xoops_load('XoopsModelFactory');
            $handlers[$name] = XoopsModelFactory::loadHandler($this, $name, $args);
        } else {
            $handlers[$name]->setHandler($this);
            $handlers[$name]->setVars($args);
        }

        return $handlers[$name];

        /**
         * // Following code just kept as placeholder for PHP5
         * if (!isset(self::$handlers[$name])) {
         * self::$handlers[$name] = XoopsModelFactory::loadHandler($this, $name, $args);
         * } else {
         * self::$handlers[$name]->setHandler($this);
         * self::$handlers[$name]->setVars($args);
         * }
         *
         * return self::$handlers[$name];
         */
    }

    /**
     * Magic method for overloading of delegation
     *
     * To be enabled in XOOPS 3.0 with PHP 5
     *
     * @access protected
     * @param  string $name method name
     * @param  array  $args arguments
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (is_object($this->handler) && is_callable(array($this->handler, $name))) {
            return call_user_func_array(array($this->handler, $name), $args);
        }
        foreach (array_keys($this->handlers) as $_handler) {
            $handler = $this->loadHandler($_handler);
            if (is_callable(array($handler, $name))) {
                return call_user_func_array(array($handler, $name), $args);
            }
        }

        return null;
    }

    /**
     * *#@+
     * Methods of native handler {@link XoopsPersistableObjectHandler}
     */
    /**
     * create a new object
     *
     * @param  bool $isNew Flag the new objects as new
     * @return XoopsObject {@link XoopsObject}
     */
    public function create($isNew = true)
    {
        $obj = new $this->className();
        if ($isNew === true) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * Load a {@link XoopsObject} object from the database
     *
     * @access protected
     * @param  mixed $id     ID
     * @param  array $fields fields to fetch
     * @return XoopsObject {@link XoopsObject}
     */
    public function get($id = null, $fields = null)
    {
        $object = null;
        if (empty($id)) {
            $object = $this->create();

            return $object;
        }
        if (is_array($fields) && count($fields) > 0) {
            $select = implode(',', $fields);
            if (!in_array($this->keyName, $fields)) {
                $select .= ', ' . $this->keyName;
            }
        } else {
            $select = '*';
        }
        $sql = sprintf('SELECT %s FROM %s WHERE %s = %s', $select, $this->table, $this->keyName, $this->db->quote($id));
        //$sql = "SELECT {$select} FROM {$this->table} WHERE {$this->keyName} = " . $this->db->quote($id);
        if (!$result = $this->db->query($sql)) {
            return $object;
        }
        if (!$this->db->getRowsNum($result)) {
            return $object;
        }
        $object = $this->create(false);
        $object->assignVars($this->db->fetchArray($result));

        return $object;
    }
    /**
     * *#@-
     */

    /**
     * *#@+
     * Methods of write handler {@link XoopsObjectWrite}
     */
    /**
     * insert an object into the database
     *
     * @param  XoopsObject $object {@link XoopsObject} reference to object
     * @param  bool        $force  flag to force the query execution despite security settings
     * @return mixed       object ID
     */
    public function insert(XoopsObject $object, $force = true)
    {
        $handler = $this->loadHandler('write');

        return $handler->insert($object, $force);
    }

    /**
     * delete an object from the database
     *
     * @param  XoopsObject $object {@link XoopsObject} reference to the object to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(XoopsObject $object, $force = false)
    {
        $handler = $this->loadHandler('write');

        return $handler->delete($object, $force);
    }

    /**
     * delete all objects matching the conditions
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} with conditions to meet
     * @param  bool            $force    force to delete
     * @param  bool            $asObject delete in object way: instantiate all objects and delete one by one
     * @return bool
     */
    public function deleteAll(CriteriaElement $criteria = null, $force = true, $asObject = false)
    {
        $handler = $this->loadHandler('write');

        return $handler->deleteAll($criteria, $force, $asObject);
    }

    /**
     * Change a field for objects with a certain criteria
     *
     * @param  string          $fieldname  Name of the field
     * @param  mixed           $fieldvalue Value to write
     * @param  CriteriaElement $criteria   {@link CriteriaElement}
     * @param  bool            $force      force to query
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue, CriteriaElement $criteria = null, $force = false)
    {
        $handler = $this->loadHandler('write');

        return $handler->updateAll($fieldname, $fieldvalue, $criteria, $force);
    }
    /**
     * *#@-
     */

    /**
     * *#@+
     * Methods of read handler {@link XoopsObjectRead}
     */
    /**
     * Retrieve objects from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool            $id_as_key use the ID as key for the array
     * @param  bool            $as_object return an array of objects
     * @return array
     */
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $handler = $this->loadHandler('read');
        $ret     = $handler->getObjects($criteria, $id_as_key, $as_object);

        return $ret;
    }

    /**
     * get all objects matching a condition
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement} to match
     * @param  array           $fields    variables to fetch
     * @param  bool            $asObject  flag indicating as object, otherwise as array
     * @param  bool            $id_as_key use the ID as key for the array
     * @return array           of objects/array {@link XoopsObject}
     */
    public function &getAll(CriteriaElement $criteria = null, $fields = null, $asObject = true, $id_as_key = true)
    {
        $handler = $this->loadHandler('read');
        $ret     = $handler->getAll($criteria, $fields, $asObject, $id_as_key);

        return $ret;
    }

    /**
     * Retrieve a list of objects data
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} conditions to be met
     * @param  int             $limit    Max number of objects to fetch
     * @param  int             $start    Which record to start at
     * @return array
     */
    public function getList(CriteriaElement $criteria = null, $limit = 0, $start = 0)
    {
        $handler = $this->loadHandler('read');
        $ret     = $handler->getList($criteria, $limit, $start);

        return $ret;
    }

    /**
     * get IDs of objects matching a condition
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return array           of object IDs
     */
    public function &getIds(CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('read');
        $ret     = $handler->getIds($criteria);

        return $ret;
    }

    /**
     * get a limited list of objects matching a condition
     *
     * {@link CriteriaCompo}
     *
     * @param  int             $limit    Max number of objects to fetch
     * @param  int             $start    Which record to start at
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @param  array           $fields   variables to fetch
     * @param  bool            $asObject flag indicating as object, otherwise as array
     * @return array           of objects     {@link XoopsObject}
     */
    public function &getByLimit($limit = 0, $start = 0, CriteriaElement $criteria = null, $fields = null, $asObject = true)
    {
        $handler = $this->loadHandler('read');
        $ret     = $handler->getByLimit($limit, $start, $criteria, $fields, $asObject);

        return $ret;
    }
    /**
     * *#@-
     */

    /**
     * *#@+
     * Methods of stats handler {@link XoopsObjectStats}
     */
    /**
     * count objects matching a condition
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of objects
     */
    public function getCount(CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('stats');

        return $handler->getCount($criteria);
    }

    /**
     * Get counts of objects matching a condition
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return array           of counts
     */
    public function getCounts(CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('stats');

        return $handler->getCounts($criteria);
    }
    /**
     * *#@-
     */

    /**
     * *#@+
     * Methods of joint handler {@link XoopsObjectJoint}
     */
    /**
     * get a list of objects matching a condition joint with another related object
     *
     * @param  CriteriaElement $criteria     {@link CriteriaElement} to match
     * @param  array           $fields       variables to fetch
     * @param  bool            $asObject     flag indicating as object, otherwise as array
     * @param  string          $field_link   field of linked object for JOIN
     * @param  string          $field_object field of current object for JOIN
     * @return array           of objects {@link XoopsObject}
     */
    public function &getByLink(CriteriaElement $criteria = null, $fields = null, $asObject = true, $field_link = null, $field_object = null)
    {
        $handler = $this->loadHandler('joint');
        $ret     = $handler->getByLink($criteria, $fields, $asObject, $field_link, $field_object);

        return $ret;
    }

    /**
     * Count of objects matching a condition
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of objects
     */
    public function getCountByLink(CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('joint');
        $ret     = $handler->getCountByLink($criteria);

        return $ret;
    }

    /**
     * array of count of objects matching a condition of, groupby linked object keyname
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of objects
     */
    public function getCountsByLink(CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('joint');
        $ret     = $handler->getCountsByLink($criteria);

        return $ret;
    }

    /**
     * update objects matching a condition against linked objects
     *
     * @param  array           $data     array of key => value
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of objects
     */
    public function updateByLink($data, CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('joint');
        $ret     = $handler->updateByLink($data, $criteria);

        return $ret;
    }

    /**
     * Delete objects matching a condition against linked objects
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of objects
     */
    public function deleteByLink(CriteriaElement $criteria = null)
    {
        $handler = $this->loadHandler('joint');
        $ret     = $handler->deleteByLink($criteria);

        return $ret;
    }
    /**
     * *#@-
     */

    /**
     * *#@+
     * Methods of sync handler {@link XoopsObjectSync}
     */
    /**
     * Clean orphan objects against linked objects
     *
     * @param  string $table_link   table of linked object for JOIN
     * @param  string $field_link   field of linked object for JOIN
     * @param  string $field_object field of current object for JOIN
     * @return bool   true on success
     */
    public function cleanOrphan($table_link = '', $field_link = '', $field_object = '')
    {
        $handler = $this->loadHandler('sync');
        $ret     = $handler->cleanOrphan($table_link, $field_link, $field_object);

        return $ret;
    }

    /**
     * Synchronizing objects
     *
     * @return bool true on success
     */
    public function synchronization()
    {
        $retval = $this->cleanOrphan();

        return $retval;
    }
    /**
     * *#@-
     */

    /**#@+
     * @deprecated
     * @param      $result
     * @param bool $id_as_key
     * @param bool $as_object
     * @return bool
     */
    public function convertResultSet($result, $id_as_key = false, $as_object = true)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}
