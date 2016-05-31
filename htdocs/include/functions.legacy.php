<?php
/**
 *  XOOPS legacy functions
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
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Deprecated functions
 * @param         $name
 * @param  string $module
 * @param  string $default
 * @return bool
 */

// Backward compatibility for 2.2*
function xoops_load_lang_file($name, $module = '', $default = 'english')
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated, use xoops_loadLanguage() instead');

    return xoops_loadLanguage($name, $module);
}

/**
 * @param int $docheck
 *
 * @return mixed
 */
function xoops_refcheck($docheck = 1)
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated, use xoopsSecurity::checkReferer instead');

    return $GLOBALS['xoopsSecurity']->checkReferer($docheck);
}

/**
 * @param $userid
 *
 * @return string
 */
function xoops_getLinkedUnameFromId($userid)
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated, use XoopsUserUtility::getUnameFromId() instead');
    xoops_load('XoopsUserUtility');

    return XoopsUserUtility::getUnameFromId($userid, false, true);
}

/*
 * Function to display banners in all pages
 */
function showbanner()
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated, use xoops_getbanner instead');
    echo xoops_getbanner();
}

/*
 * This function is deprecated. Do not use!
 */
function getTheme()
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . "() is deprecated, use \$xoopsConfig['theme_set'] directly");

    return $GLOBALS['xoopsConfig']['theme_set'];
}

/*
 * Function to get css file for a certain theme
 * This function will be deprecated.
 */
/**
 * @param string $theme
 *
 * @return string
 */
function getcss($theme = '')
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated, use xoops_getcss instead');

    return xoops_getcss($theme);
}

/**
 * @return XoopsMailer|XoopsMailerLocal
 */
function &getMailer()
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated, use xoops_getMailer instead');
    $mailer =& xoops_getMailer();

    return $mailer;
}

/*
 * Functions to display dhtml loading image box
 */
function OpenWaitBox()
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated');
    echo "<div id='waitDiv' style='position:absolute;left:40%;top:50%;visibility:hidden;text-align: center;'>
    <table cellpadding='6' border='2' class='bg2'>
      <tr>
        <td align='center'><strong><big>" . _FETCHING . "</big></strong><br><img src='" . XOOPS_URL . "/images/await.gif' alt='' /><br>" . _PLEASEWAIT . "</td>
      </tr>
    </table>
    </div>
    <script type='text/javascript'>
    <!--//
    var DHTML = (document.getElementById || document.all || document.layers);
    function ap_getObj(name)
    {
        if (document.getElementById){
            return document.getElementById(name).style;
        } elseif (document.all)
        {
            return document.all[name].style;
        } elseif (document.layers)
        {
            return document.layers[name];
        }
    }
    function ap_showWaitMessage(div,flag)
    {
        if (!DHTML) {
            return null;
        }
        var x = ap_getObj(div);
        x.visibility = (flag) ? 'visible' : 'hidden';
        if (!document.getElementById) {
            if (document.layers) {
                x.left=280/2;
            }
        }

        return true;
    }
    ap_showWaitMessage('waitDiv', 1);
    //-->
    </script>";
}

function CloseWaitBox()
{
    $GLOBALS['xoopsLogger']->addDeprecated('Function ' . __FUNCTION__ . '() is deprecated');
    echo "<script type='text/javascript'>
    <!--//
    ap_showWaitMessage('waitDiv', 0);
    //-->
    </script>
    ";
}
