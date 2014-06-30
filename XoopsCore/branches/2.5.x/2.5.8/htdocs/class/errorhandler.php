<?php
/**
 * XOOPS legacy error handler
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
 * @since           2.0.0
 * @version         $Id$
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_loadLanguage('errors');

XoopsLoad::load('xoopslogger');

$GLOBALS['xoopsLogger']->addDeprecated("'/class/errorhandler.php' is deprecated since XOOPS 2.5.4, please use 'class/logger/xoopslogger.php' instead.");


/**
 * Xoops ErrorHandler
 *
 * Backward compatibility code, do not use this class directly
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono <onokazu@xoops.org>
 * @author John Neill <catzwolf@xoops.org>
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 */
class XoopsErrorHandler extends XoopsLogger
{
    /**
     * Activate the error handler
     */
    function activate($showErrors = false)
    {
        $this->activated = $showErrors;
    }

    /**
     * Render the list of errors
     */
    function renderErrors()
    {
        return $this->dump('errors');
    }
}
