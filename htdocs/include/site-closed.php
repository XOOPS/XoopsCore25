<?php
/**
 * XOOPS Closed Site
 *
 * Temporary solution for "site closed" status
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
 * @since               2.0.17
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

global $xoopsConfig, $xoopsUser;

$allowed = false;
if (is_object($xoopsUser)) {
    foreach ($xoopsUser->getGroups() as $group) {
        if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
            $allowed = true;
            break;
        }
    }
} elseif (!empty($_POST['xoops_login'])) {
    include_once $GLOBALS['xoops']->path('include/checklogin.php');
    exit();
}

if (!$allowed) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    require_once $GLOBALS['xoops']->path('class/theme.php');
    $xoopsThemeFactory                = null;
    $xoopsThemeFactory                = new xos_opal_ThemeFactory();
    $xoopsThemeFactory->allowedThemes = $xoopsConfig['theme_set_allowed'];
    $xoopsThemeFactory->defaultTheme  = $xoopsConfig['theme_set'];
    $xoTheme                          = $xoopsThemeFactory->createInstance(
        [
            'plugins' => [],
        ],
    );
    $xoTheme->addScript(
        '/include/xoops.js',
        [
            'type' => 'text/javascript',
        ],
    );
    $xoopsTpl = $xoTheme->template;
    $xoopsTpl->assign(
        [
            'xoops_theme'       => $xoopsConfig['theme_set'],
            'xoops_imageurl'    => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
            'xoops_themecss'    => xoops_getcss($xoopsConfig['theme_set']),
            'xoops_requesturi'  => htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES | ENT_HTML5),
            'xoops_sitename'    => htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES | ENT_HTML5),
            'xoops_slogan'      => htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES | ENT_HTML5),
            'xoops_dirname'     => !empty($xoopsModule) ? $xoopsModule->getVar('dirname') : 'system',
            'xoops_banner'      => $xoopsConfig['banners'] ? xoops_getbanner() : '&nbsp;',
            'xoops_pagetitle'   => isset($xoopsModule) && is_object($xoopsModule) ? $xoopsModule->getVar('name') : htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES | ENT_HTML5),
            'lang_login'        => _LOGIN,
            'lang_username'     => _USERNAME,
            'lang_password'     => _PASSWORD,
            'lang_siteclosemsg' => $xoopsConfig['closesite_text'],
        ],
    );
    if (isset($_SESSION['redirect_message'])) {
        $xoopsTpl->assign('redirect_message', $_SESSION['redirect_message']);
        unset($_SESSION['redirect_message']);
    }

    /** @var XoopsConfigHandler $config_handler */
    $config_handler = xoops_getHandler('config');
    $criteria       = new CriteriaCompo(new Criteria('conf_modid', 0));
    $criteria->add(new Criteria('conf_catid', XOOPS_CONF_METAFOOTER));
    $config = $config_handler->getConfigs($criteria, true);
    foreach (array_keys($config) as $i) {
        $name  = $config[$i]->getVar('conf_name', 'n');
        $value = $config[$i]->getVar('conf_value', 'n');
        // limited substitutions for {X_SITEURL} and {X_YEAR}
        if ($name === 'footer' || $name === 'meta_copyright') {
            $value = str_replace('{X_SITEURL}', XOOPS_URL . '/', $value);
            $value = str_replace('{X_YEAR}', date('Y', time()), $value);
        }
        if (substr($name, 0, 5) === 'meta_') {
            $xoopsTpl->assign("xoops_$name", htmlspecialchars($value, ENT_QUOTES | ENT_HTML5));
        } else {
            // prefix each tag with 'xoops_'
            $xoopsTpl->assign("xoops_$name", $value);
        }
    }
    $xoopsTpl->debugging      = false;
    $xoopsTpl->debugging_ctrl = 'none';
    $xoopsTpl->caching        = 0;
    // handle error and transition to tpl naming convention
    if ($xoopsTpl->templateExists('db:system_siteclosed.tpl')) {
        $xoopsTpl->display('db:system_siteclosed.tpl');
    } elseif ($xoopsTpl->templateExists('db:system_siteclosed.html')) {
        $xoopsTpl->display('db:system_siteclosed.html');
    } else {
        echo $xoopsConfig['closesite_text'];
    }
    exit();
}
unset($allowed, $group);

return true;
