<?php
/**
 * XOOPS Form Class Elements
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         kernel
 * @subpackage      form
 * @since           2.4.0
 * @author          John Neill <catzwolf@xoops.org>
 * @version         $Id$
 *
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * XoopsFormButtonTray
 *
 * @author 		John Neill <catzwolf@xoops.org>
 * @package 	kernel
 * @subpackage 	form
 * @access 		public
 */
class XoopsFormButtonTray extends XoopsFormElement
{
    /**
     * Value
     *
     * @var string
     * @access private
     */
    var $_value;

    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     *
     * @var string
     * @access private
     */
    var $_type;

    /**
     * XoopsFormButtonTray::XoopsFormButtonTray()
     *
     * @param mixed  $name
     * @param string $value
     * @param string $type
     * @param string $onclick
     * @param bool   $showDelete
     */
    function XoopsFormButtonTray( $name, $value = '', $type = '', $onclick = '', $showDelete = false )
    {
        $this->setName( $name );
        $this->setValue( $value );
        $this->_type = ( !empty( $type ) ) ? $type : 'submit';
        $this->_showDelete = $showDelete;
        if ($onclick) {
            $this->setExtra( $onclick );
        } else {
            $this->setExtra( '' );
        }
    }

    /**
     * XoopsFormButtonTray::getValue()
     *
     * @return string
     */
    function getValue()
    {
        return $this->_value;
    }

    /**
     * XoopsFormButtonTray::setValue()
     *
     * @param mixed $value
     *
     * @return void
     */
    function setValue( $value )
    {
        $this->_value = $value;
    }

    /**
     * XoopsFormButtonTray::getType()
     *
     * @return string
     */
    function getType()
    {
        return $this->_type;
    }

    /**
     * XoopsFormButtonTray::render()
     *
     * @return string|void
     */
    function render()
    {
        // onclick="this.form.elements.op.value=\'delfile\';
        $ret = '';
        if ($this->_showDelete) {
            $ret .= '<input type="submit" class="formbutton" name="delete" id="delete" value="' . _DELETE . '" onclick="this.form.elements.op.value=\'delete\'">&nbsp;';
        }
        $ret .= '<input type="button" value="' . _CANCEL . '" onClick="history.go(-1);return true;" />&nbsp;<input type="reset" class="formbutton"  name="reset"  id="reset" value="' . _RESET . '" />&nbsp;<input type="' . $this->getType() . '" class="formbutton"  name="' . $this->getName() . '"  id="' . $this->getName() . '" value="' . $this->getValue() . '"' . $this->getExtra() . '  />';

        return $ret;
    }
}
