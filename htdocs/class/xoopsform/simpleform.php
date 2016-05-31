<?php
/**
 * XOOPS simple form
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
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * base class
 */
xoops_load('XoopsForm');

/**
 * Form that will output as a simple HTML form with minimum formatting
 */
class XoopsSimpleForm extends XoopsForm
{
    /**
     * create HTML to output the form with minimal formatting
     *
     * @return string
     */
    public function render()
    {
        $ret = $this->getTitle() . "\n<form name='" . $this->getName() . "' id='" . $this->getName() . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "'" . $this->getExtra() . ">\n";
        foreach ($this->getElements() as $ele) {
            if (!$ele->isHidden()) {
                $ret .= '<strong>' . $ele->getCaption() . '</strong><br>' . $ele->render() . "<br>\n";
            } else {
                $ret .= $ele->render() . "\n";
            }
        }
        $ret .= "</form>\n";

        return $ret;
    }
}
