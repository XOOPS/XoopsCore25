<?php
/**
 * XOOPS table form
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
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsForm');

/**
 * Form that will output formatted as a HTML table
 *
 * No styles and no JavaScript to check for required fields.
 */
class XoopsTableForm extends XoopsForm
{
    /**
     * create HTML to output the form as a table
     *
     * YOU SHOULD AVOID TO USE THE FOLLOWING Nocolspan METHOD, IT WILL BE REMOVED
     *
     * To use the noColspan simply use the following example:
     *
     * $colspan = new XoopsFormDhtmlTextArea( '', 'key', $value, '100%', '100%' );
     * $colspan->setNocolspan();
     * $form->addElement( $colspan );
     *
     * @return string
     */
    public function render()
    {
        $ret    = $this->getTitle() . NWLINE . '<form name="' . $this->getName() . '" id="' . $this->getName() . '" action="' . $this->getAction() . '" method="' . $this->getMethod() . '"' . $this->getExtra() . '>' . NWLINE . '<table border="0" width="100%">' . NWLINE;
        $hidden = '';
        foreach ($this->getElements() as $ele) {
            if (!$ele->isHidden()) {
                if (!$ele->getNocolspan()) {
                    $ret .= '<tr valign="top" align="left"><td>' . $ele->getCaption();
                    if ($ele_desc = $ele->getDescription()) {
                        $ret .= '<br><br><span style="font-weight: normal;">' . $ele_desc . '</span>';
                    }
                    $ret .= '</td><td>' . $ele->render() . '</td></tr>';
                } else {
                    $ret .= '<tr valign="top" align="left"><td colspan="2">' . $ele->getCaption();
                    $ret .= '</td></tr><tr valign="top" align="left"><td>' . $ele->render() . '</td></tr>';
                }
            } else {
                $hidden .= $ele->render() . NWLINE;
            }
        }
        $ret .= '</table>' . NWLINE . ' ' . $hidden . '</form>' . NWLINE;

        return $ret;
    }
}
