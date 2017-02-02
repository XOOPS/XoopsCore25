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
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

/**
 * Class XoopsXmlRpcDocument
 */
class XoopsXmlRpcDocument
{
    public $_tags = array();

    /**
     * XoopsXmlRpcDocument constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $tagobj
     */
    public function add(&$tagobj)
    {
        $this->_tags[] =& $tagobj;
    }

    public function render()
    {
    }
}

/**
 * Class XoopsXmlRpcResponse
 */
class XoopsXmlRpcResponse extends XoopsXmlRpcDocument
{
    /**
     * @return string
     */
    public function render()
    {
        $count   = count($this->_tags);
        $payload = '';
        for ($i = 0; $i < $count; ++$i) {
            if (!$this->_tags[$i]->isFault()) {
                $payload .= $this->_tags[$i]->render();
            } else {
                return '<?xml version="1.0"?><methodResponse>' . $this->_tags[$i]->render() . '</methodResponse>';
            }
        }

        return '<?xml version="1.0"?><methodResponse><params><param>' . $payload . '</param></params></methodResponse>';
    }
}

/**
 * Class XoopsXmlRpcRequest
 */
class XoopsXmlRpcRequest extends XoopsXmlRpcDocument
{
    public $methodName;

    /**
     * @param $methodName
     */
    public function __construct($methodName)
    {
        $this->methodName = trim($methodName);
    }

    /**
     * @return string
     */
    public function render()
    {
        $count   = count($this->_tags);
        $payload = '';
        for ($i = 0; $i < $count; ++$i) {
            $payload .= '<param>' . $this->_tags[$i]->render() . '</param>';
        }

        return '<?xml version="1.0"?><methodCall><methodName>' . $this->methodName . '</methodName><params>' . $payload . '</params></methodCall>';
    }
}

/**
 * Class XoopsXmlRpcTag
 */
class XoopsXmlRpcTag
{
    public $_fault = false;

    /**
     * XoopsXmlRpcTag constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $text
     *
     * @return mixed
     */
    public function &encode(&$text)
    {
        $text = preg_replace(array("/\&([a-z\d\#]+)\;/i", "/\&/", "/\#\|\|([a-z\d\#]+)\|\|\#/i"), array(
            "#||\\1||#",
            '&amp;',
            "&\\1;"), str_replace(array(
                                      '<',
                                      '>'), array(
                                      '&lt;',
                                      '&gt;'), $text));

        return $text;
    }

    /**
     * @param bool $fault
     */
    public function setFault($fault = true)
    {
        $this->_fault = ((int)$fault > 0);// ? true : false;
    }

    /**
     * @return bool
     */
    public function isFault()
    {
        return $this->_fault;
    }

    public function render()
    {
    }
}

/**
 * Class XoopsXmlRpcFault
 */
class XoopsXmlRpcFault extends XoopsXmlRpcTag
{
    public $_code;
    public $_extra;

    /**
     * @param      $code
     * @param null $extra
     */
    public function __construct($code, $extra = null)
    {
        $this->setFault(true);
        $this->_code  = (int)$code;
        $this->_extra = isset($extra) ? trim($extra) : '';
    }

    /**
     * @return string
     */
    public function render()
    {
        switch ($this->_code) {
            case 101:
                $string = 'Invalid server URI';
                break;
            case 102:
                $string = 'Parser parse error';
                break;
            case 103:
                $string = 'Module not found';
                break;
            case 104:
                $string = 'User authentication failed';
                break;
            case 105:
                $string = 'Module API not found';
                break;
            case 106:
                $string = 'Method response error';
                break;
            case 107:
                $string = 'Method not supported';
                break;
            case 108:
                $string = 'Invalid parameter';
                break;
            case 109:
                $string = 'Missing parameters';
                break;
            case 110:
                $string = 'Selected blog application does not exist';
                break;
            case 111:
                $string = 'Method permission denied';
                break;
            default:
                $string = 'Method response error';
                break;
        }
        $string .= "\n" . $this->_extra;

        return '<fault><value><struct><member><name>faultCode</name><value>' . $this->_code . '</value></member><member><name>faultString</name><value>' . $this->encode($string) . '</value></member></struct></value></fault>';
    }
}

/**
 * Class XoopsXmlRpcInt
 */
class XoopsXmlRpcInt extends XoopsXmlRpcTag
{
    public $_value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->_value = (int)$value;
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<value><int>' . $this->_value . '</int></value>';
    }
}

/**
 * Class XoopsXmlRpcDouble
 */
class XoopsXmlRpcDouble extends XoopsXmlRpcTag
{
    public $_value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->_value = (float)$value;
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<value><double>' . $this->_value . '</double></value>';
    }
}

/**
 * Class XoopsXmlRpcBoolean
 */
class XoopsXmlRpcBoolean extends XoopsXmlRpcTag
{
    public $_value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->_value = (!empty($value) && $value != false) ? 1 : 0;
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<value><boolean>' . $this->_value . '</boolean></value>';
    }
}

/**
 * Class XoopsXmlRpcString
 */
class XoopsXmlRpcString extends XoopsXmlRpcTag
{
    public $_value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->_value = (string)$value;
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<value><string>' . $this->encode($this->_value) . '</string></value>';
    }
}

/**
 * Class XoopsXmlRpcDatetime
 */
class XoopsXmlRpcDatetime extends XoopsXmlRpcTag
{
    public $_value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        if (!is_numeric($value)) {
            $this->_value = strtotime($value);
        } else {
            $this->_value = (int)$value;
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<value><dateTime.iso8601>' . gmstrftime('%Y%m%dT%H:%M:%S', $this->_value) . '</dateTime.iso8601></value>';
    }
}

/**
 * Class XoopsXmlRpcBase64
 */
class XoopsXmlRpcBase64 extends XoopsXmlRpcTag
{
    public $_value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->_value = base64_encode($value);
    }

    /**
     * @return string
     */
    public function render()
    {
        return '<value><base64>' . $this->_value . '</base64></value>';
    }
}

/**
 * Class XoopsXmlRpcArray
 */
class XoopsXmlRpcArray extends XoopsXmlRpcTag
{
    public $_tags = array();

    /**
     * XoopsXmlRpcArray constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $tagobj
     */
    public function add(&$tagobj)
    {
        $this->_tags[] =& $tagobj;
    }

    /**
     * @return string
     */
    public function render()
    {
        $count = count($this->_tags);
        $ret   = '<value><array><data>';
        for ($i = 0; $i < $count; ++$i) {
            $ret .= $this->_tags[$i]->render();
        }
        $ret .= '</data></array></value>';

        return $ret;
    }
}

/**
 * Class XoopsXmlRpcStruct
 */
class XoopsXmlRpcStruct extends XoopsXmlRpcTag
{
    public $_tags = array();

    /**
     * XoopsXmlRpcStruct constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $name
     * @param $tagobj
     */
    public function add($name, &$tagobj)
    {
        $this->_tags[] = array('name' => $name, 'value' => $tagobj);
    }

    /**
     * @return string
     */
    public function render()
    {
        $count = count($this->_tags);
        $ret   = '<value><struct>';
        for ($i = 0; $i < $count; ++$i) {
            $ret .= '<member><name>' . $this->encode($this->_tags[$i]['name']) . '</name>' . $this->_tags[$i]['value']->render() . '</member>';
        }
        $ret .= '</struct></value>';

        return $ret;
    }
}
