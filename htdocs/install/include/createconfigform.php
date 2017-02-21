<?php
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

if (!defined('XOOPS_INSTALL')) {
    die('XOOPS Custom Installation die');
}

include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

define('PREF_1', _MD_AM_GENERAL);
define('PREF_2', _MD_AM_USERSETTINGS);
define('PREF_3', _MD_AM_METAFOOTER);
define('PREF_4', _MD_AM_CENSOR);
define('PREF_5', _MD_AM_SEARCH);
define('PREF_6', _MD_AM_MAILER);
if (defined('_MD_AM_AUTHENTICATION')) {
    define('PREF_7', _MD_AM_AUTHENTICATION);
}

/**
 * @param $config
 *
 * @return array
 */
function createConfigform($config)
{
    xoops_load('XoopsFormRendererBootstrap3');
    XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap3());

    /* @var $config_handler XoopsConfigHandler  */
    $config_handler         = xoops_getHandler('config');
    $GLOBALS['xoopsConfig'] = $xoopsConfig = $config_handler->getConfigsByCat(XOOPS_CONF);

    $ret       = array();
    $confcount = count($config);

    for ($i = 0; $i < $confcount; ++$i) {
        $conf_catid = $config[$i]->getVar('conf_catid');
        if (!isset($ret[$conf_catid])) {
            $form_title       = constant('PREF_' . $conf_catid);
            $ret[$conf_catid] = new XoopsThemeForm($form_title, 'configs', 'index.php', 'post');
        }

        $title = constant($config[$i]->getVar('conf_title'));

        switch ($config[$i]->getVar('conf_formtype')) {
            case 'textarea':
                $myts = MyTextSanitizer::getInstance();
                if ($config[$i]->getVar('conf_valuetype') === 'array') {
                    // this is exceptional.. only when value type is arrayneed a smarter way for this
                    $ele = ($config[$i]->getVar('conf_value') != '') ? new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars(implode('|', $config[$i]->getConfValueForOutput())), 5, 50) : new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), '', 5, 50);
                } else {
                    $ele = new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()), 5, 100);
                }
                break;

            case 'select':
                $ele     = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                $opcount = count($options);
                for ($j = 0; $j < $opcount; ++$j) {
                    $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                    $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                    $ele->addOption($optval, $optkey);
                }
                break;

            case 'select_multi':
                $ele     = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                $opcount = count($options);
                for ($j = 0; $j < $opcount; ++$j) {
                    $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                    $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                    $ele->addOption($optval, $optkey);
                }
                break;

            case 'yesno':
                $ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
                break;

            case 'theme':
            case 'theme_multi':
                $ele = ($config[$i]->getVar('conf_formtype') !== 'theme_multi') ? new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput()) : new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                $dirlist = XoopsLists::getThemesList();
                if (!empty($dirlist)) {
                    asort($dirlist);
                    $ele->addOptionArray($dirlist);
                }
                // old theme value is used to determine whether to update cache or not. kind of dirty way
                $form->addElement(new XoopsFormHidden('_old_theme', $config[$i]->getConfValueForOutput()));
                break;

            case 'tplset':
                $ele            = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $tplset_handler = xoops_getHandler('tplset');
                $tplsetlist     = $tplset_handler->getList();
                asort($tplsetlist);
                foreach ($tplsetlist as $key => $name) {
                    $ele->addOption($key, $name);
                }
                // old theme value is used to determine whether to update cache or not. kind of dirty way
                $form->addElement(new XoopsFormHidden('_old_theme', $config[$i]->getConfValueForOutput()));
                break;

            case 'timezone':
                $ele = new XoopsFormSelectTimezone($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                break;

            case 'language':
                $ele = new XoopsFormSelectLang($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                break;

            case 'startpage':
                $ele            = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                /* @var $module_handler XoopsModuleHandler */
                $module_handler = xoops_getHandler('module');
                $criteria       = new CriteriaCompo(new Criteria('hasmain', 1));
                $criteria->add(new Criteria('isactive', 1));
                $moduleslist       = $module_handler->getList($criteria, true);
                $moduleslist['--'] = _MD_AM_NONE;
                $ele->addOptionArray($moduleslist);
                break;

            case 'group':
                $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                break;

            case 'group_multi':
                $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                break;

            // RMV-NOTIFY - added 'user' and 'user_multi'
            case 'user':
                $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                break;

            case 'user_multi':
                $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                break;

            case 'module_cache':
                /* @var $module_handler XoopsModuleHandler */
                $module_handler = xoops_getHandler('module');
                $modules        = $module_handler->getObjects(new Criteria('hasmain', 1), true);
                $currrent_val   = $config[$i]->getConfValueForOutput();
                $cache_options  = array(
                    '0'      => _NOCACHE,
                    '30'     => sprintf(_SECONDS, 30),
                    '60'     => _MINUTE,
                    '300'    => sprintf(_MINUTES, 5),
                    '1800'   => sprintf(_MINUTES, 30),
                    '3600'   => _HOUR,
                    '18000'  => sprintf(_HOURS, 5),
                    '86400'  => _DAY,
                    '259200' => sprintf(_DAYS, 3),
                    '604800' => _WEEK);
                if (count($modules) > 0) {
                    $ele = new XoopsFormElementTray($title, '<br>');
                    foreach (array_keys($modules) as $mid) {
                        $c_val   = isset($currrent_val[$mid]) ? (int)$currrent_val[$mid] : null;
                        $selform = new XoopsFormSelect($modules[$mid]->getVar('name'), $config[$i]->getVar('conf_name') . "[$mid]", $c_val);
                        $selform->addOptionArray($cache_options);
                        $ele->addElement($selform);
                        unset($selform);
                    }
                } else {
                    $ele = new XoopsFormLabel($title, _MD_AM_NOMODULE);
                }
                break;

            case 'site_cache':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $ele->addOptionArray(array(
                                         '0'      => _NOCACHE,
                                         '30'     => sprintf(_SECONDS, 30),
                                         '60'     => _MINUTE,
                                         '300'    => sprintf(_MINUTES, 5),
                                         '1800'   => sprintf(_MINUTES, 30),
                                         '3600'   => _HOUR,
                                         '18000'  => sprintf(_HOURS, 5),
                                         '86400'  => _DAY,
                                         '259200' => sprintf(_DAYS, 3),
                                         '604800' => _WEEK));
                break;

            case 'password':
                $myts = MyTextSanitizer::getInstance();
                $ele  = new XoopsFormPassword($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;

            case 'color':
                $myts = MyTextSanitizer::getInstance();
                $ele  = new XoopsFormColorPicker($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;

            case 'hidden':
                $myts = MyTextSanitizer::getInstance();
                $ele  = new XoopsFormHidden($config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;

            case 'textbox':
            default:
                $myts = MyTextSanitizer::getInstance();
                $ele  = new XoopsFormText($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;
        }

        if (defined($config[$i]->getVar('conf_desc')) && constant($config[$i]->getVar('conf_desc')) != '') {
            $ele->setDescription(constant($config[$i]->getVar('conf_desc')));
        }
        $ret[$conf_catid]->addElement($ele);

        $hidden = new XoopsFormHidden('conf_ids[]', $config[$i]->getVar('conf_id'));
        $ret[$conf_catid]->addElement($hidden);

        unset($ele, $hidden);
    }

    return $ret;
}

/**
 * @param $config
 *
 * @return array
 */
function createThemeform($config)
{
    xoops_load('XoopsFormRendererBootstrap3');
    XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap3());

    $title          = (!defined($config->getVar('conf_desc')) || constant($config->getVar('conf_desc')) === '') ? constant($config->getVar('conf_title')) : constant($config->getVar('conf_title')) . '<br><br><span>' . constant($config->getVar('conf_desc')) . '</span>';
    $form_theme_set = new XoopsFormSelect('', $config->getVar('conf_name'), $config->getConfValueForOutput(), 1, false);
    $dirlist        = XoopsLists::getThemesList();
    if (!empty($dirlist)) {
        asort($dirlist);
        $form_theme_set->addOptionArray($dirlist);
    }

    $label_content = '';

    // read ini file for each theme
    foreach ($dirlist as $theme) {
        // set default value
        $theme_ini = array(
            'Name'        => $theme,
            'Description' => '',
            'Version'     => '',
            'Format'      => '',
            'Author'      => '',
            'Demo'        => '',
            'Url'         => '',
            'Download'    => '',
            'W3C'         => '',
            'Licence'     => '',
            'thumbnail'   => 'screenshot.gif',
            'screenshot'  => 'screenshot.png');

        if ($theme == $config->getConfValueForOutput()) {
            $label_content .= '<div class="theme_preview" id="'.$theme.'" style="display:block;">';
        } else {
            $label_content .= '<div class="theme_preview" id="'.$theme.'" style="display:none;">';
        }
        if (file_exists(XOOPS_ROOT_PATH . "/themes/$theme/theme.ini")) {
            $theme_ini = parse_ini_file(XOOPS_ROOT_PATH . "/themes/$theme/theme.ini");
            if ($theme_ini['screenshot'] == '') {
                $theme_ini['screenshot'] = 'screenshot.png';
                $theme_ini['thumbnail']  = 'thumbnail.png';
            }
        }

        if ($theme_ini['screenshot'] !== '' && file_exists(XOOPS_ROOT_PATH . '/themes/' . $theme . '/' . $theme_ini['screenshot'])) {
            $label_content .= '<img class="img-responsive" src="' . XOOPS_URL . '/themes/' . $theme . '/' . $theme_ini['screenshot'] . '" alt="Screenshot" />';
        } elseif ($theme_ini['thumbnail'] !== '' && file_exists(XOOPS_ROOT_PATH . '/themes/' . $theme .'/' . $theme_ini['thumbnail'])) {
            $label_content .= '<img class="img-responsive" src="' . XOOPS_URL . '/themes/' . $theme . '/' . $theme_ini['thumbnail'] . '" alt="$theme" />';
        } else {
            $label_content .= THEME_NO_SCREENSHOT;
        }
        $label_content .= '</div>';
    }
    // read ini file for each theme

    $form_theme_set->setExtra("onchange='showThemeSelected(this)'");

    $form = new XoopsThemeForm($title, 'themes', 'index.php', 'post');
    $form->addElement($form_theme_set);
    $form->addElement(new XoopsFormLabel('', "<div id='screenshot'>" . $label_content . '</div>'));

    $form->addElement(new XoopsFormHidden('conf_ids[]', $config->getVar('conf_id')));

    return $ret = array($form);
}
