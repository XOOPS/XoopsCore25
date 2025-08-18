<?php
/**
 * XOOPS form element
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
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('Restricted access');
}

/**
 * A hidden token field
 */
class XoopsFormHiddenToken extends XoopsFormHidden
{
    /**
     * Constructor
     *
     * @param string $name "name" attribute
     * @param int    $timeout
     */
    public function __construct($name = 'XOOPS_TOKEN', $timeout = 0)
    {
        parent::__construct($name . '_REQUEST', $GLOBALS['xoopsSecurity']->createToken($timeout, $name));
    }
}
