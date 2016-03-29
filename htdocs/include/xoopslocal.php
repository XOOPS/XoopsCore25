<?php
/**
 * Xoops Localization function
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
 * @package             core
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * XoopsLocalWrapper
 *
 */
class XoopsLocalWrapper
{
    /**
     * @param null $language
     *
     * @return bool
     */
    public static function load($language = null)
    {
        if (class_exists('Xoopslocal')) {
            return true;
        }
        require_once $GLOBALS['xoops']->path('class/xoopslocal.php');
        //XoopsLocal is inside language file, let us load it
        xoops_loadLanguage('locale');

        return true;
    }
}

/**
 * Enter description here...
 *
 * @return unknown
 */
function xoops_local()
{
    // get parameters
    $func_args = func_get_args();
    $func      = array_shift($func_args);

    // local method defined
    return call_user_func_array(array(
                                    'XoopsLocal',
                                    $func), $func_args);
}

XoopsLocalWrapper::load();
