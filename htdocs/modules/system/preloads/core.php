<?php
/**
 * System Preloads
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
 * @author              Cointin Maxime (AKA Kraven30)
 * @author              Andricq Nicolas (AKA MusS)
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class SystemCorePreload
 */
class SystemCorePreload extends XoopsPreloadItem
{
    /**
     * @param $args
     */
    public static function eventCoreIncludeFunctionsRedirectheader($args)
    {
        global $xoopsConfig;
        $url = $args[0];
        if (preg_match("/[\\0-\\31]|about:|script:/i", (string) $url)) {
            if (!preg_match('/^\b(java)?script:([\s]*)history\.go\(-\d*\)([\s]*[;]*[\s]*)$/si', (string) $url)) {
                $url = XOOPS_URL;
            }
        }
        if (!headers_sent() && isset($xoopsConfig['redirect_message_ajax']) && $xoopsConfig['redirect_message_ajax']) {
            $_SESSION['redirect_message'] = $args[2];
            header('Location: ' . preg_replace('/[&]amp;/i', '&', (string) $url));
            exit();
        }
    }

    /**
     * @param $args
     */
    public static function eventCoreHeaderCheckcache($args)
    {
        if (!empty($_SESSION['redirect_message'])) {
            $GLOBALS['xoTheme']->contentCacheLifetime = 0;
            unset($_SESSION['redirect_message']);
        }
    }

    /**
     * @param $args
     */
    public static function eventCoreHeaderAddmeta($args)
    {
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/fontawesome.min.css');
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/solid.min.css');
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/brands.min.css');
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/v4-shims.min.css');
        if (defined('XOOPS_STARTPAGE_REDIRECTED') || (isset($GLOBALS['xoopsOption']['template_main']) && $GLOBALS['xoopsOption']['template_main'] === 'db:system_homepage.tpl')) {
            if (is_object($GLOBALS['xoopsTpl'])) {
                $GLOBALS['xoopsTpl']->assign('homepage', true);
            }
        }

        if (!empty($_SESSION['redirect_message'])) {
            /**
             * Don't load jquery if already done by the theme
             */
            $GLOBALS['xoTheme']->addScript(
                '',
                ['type' => 'text/javascript'],
                "
                if (typeof jQuery == 'undefined') {
                    var tag = '<scr' + 'ipt type=\'text/javascript\' src=\'" . XOOPS_URL . "/browse.php?Frameworks/jquery/jquery.js\'></scr' + 'ipt>';            	    
                    document.write(tag);            	    
	            };",
            );
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.jgrowl.js');
            $GLOBALS['xoTheme']->addScript('', ['type' => 'text/javascript'], '
            (function($){
                $(document).ready(function(){
                $.jGrowl("' . $_SESSION['redirect_message'] . '", {  life:3000 , position: "center", speed: "slow" });
            });
            })(jQuery);
            ');
        }
    }

    /**
     * @param $args
     */
    public static function eventSystemClassGuiHeader($args)
    {
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/fontawesome.min.css');
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/solid.min.css');
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/brands.min.css');
        $GLOBALS['xoTheme']->addStylesheet('media/font-awesome6/css/v4-shims.min.css');
        if (!empty($_SESSION['redirect_message'])) {
            //$GLOBALS['xoTheme']->addStylesheet('xoops.css');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.jgrowl.js');
            $GLOBALS['xoTheme']->addScript('', ['type' => 'text/javascript'], '
            (function($){
            $(document).ready(function(){
                $.jGrowl("' . $_SESSION['redirect_message'] . '", {  life:3000 , position: "center", speed: "slow" });
            });
            })(jQuery);
            ');
            unset($_SESSION['redirect_message']);
        }
    }
}
