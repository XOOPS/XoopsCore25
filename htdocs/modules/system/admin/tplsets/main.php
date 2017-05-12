<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

// Check users rights
if (!is_object($GLOBALS['xoopsUser']) || !is_object($GLOBALS['xoopsModule']) || !$GLOBALS['xoopsUser']->isAdmin($GLOBALS['xoopsModule']->mid())) {
    exit(_NOPERM);
}

// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'default', 'string');

// Define main template
$GLOBALS['xoopsOption']['template_main'] = 'system_templates.tpl';
// Call Header
xoops_cp_header();
// Define scripts
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
$xoTheme->addScript('modules/system/js/jquery.easing.js');
$xoTheme->addScript('modules/system/js/jqueryFileTree.js');
$xoTheme->addScript('modules/system/js/admin.js');
$xoTheme->addScript('modules/system/js/templates.js');
$xoTheme->addScript('modules/system/js/code_mirror/codemirror.js');
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/code_mirror/docs.css');
// Define Breadcrumb and tips
$xoBreadCrumb->addLink(_AM_SYSTEM_TEMPLATES_NAV_MAIN, system_adminVersion('tplsets', 'adminpath'));

switch ($op) {
    //index
    default:
        // Assign Breadcrumb menu
        $xoBreadCrumb->addHelp(system_adminVersion('tplsets', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_TEMPLATES_NAV_TIPS);
        $xoBreadCrumb->render();

        $GLOBALS['xoopsTpl']->assign('index', true);

        $form = new XoopsThemeForm(_AM_SYSTEM_TEMPLATES_GENERATE, 'form', 'admin.php?fct=tplsets', 'post', true);

        $ele            = new XoopsFormSelect(_AM_SYSTEM_TEMPLATES_SET, 'tplset', $GLOBALS['xoopsConfig']['template_set']);
        /* @var  $tplset_handler XoopsTplsetHandler */
        $tplset_handler = xoops_getHandler('tplset');
        $tplsetlist     = $tplset_handler->getList();
        asort($tplsetlist);
        foreach ($tplsetlist as $key => $name) {
            $ele->addOption($key, $name);
        }
        $form->addElement($ele);
        $form->addElement(new XoopsFormSelectTheme(_AM_SYSTEM_TEMPLATES_SELECT_THEME, 'select_theme', 1, 5), true);
        $form->addElement(new XoopsFormRadioYN(_AM_SYSTEM_TEMPLATES_FORCE_GENERATED, 'force_generated', 0, _YES, _NO), true);

        $modules        = new XoopsFormSelect(_AM_SYSTEM_TEMPLATES_SELECT_MODULES, 'select_modules');
        /* @var $module_handler XoopsModuleHandler */
        $module_handler = xoops_getHandler('module');
        $criteria       = new CriteriaCompo(new Criteria('isactive', 1));
        $moduleslist    = $module_handler->getList($criteria, true);
        $modules->addOption(0, _AM_SYSTEM_TEMPLATES_ALL_MODULES);
        $modules->addOptionArray($moduleslist);
        $form->addElement($modules, true);

        $form->addElement(new XoopsFormHidden('active_templates', '0'));
        $form->addElement(new XoopsFormHidden('active_modules', '0'));
        $form->addElement(new XoopsFormHidden('op', 'tpls_generate_surcharge'));
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $xoopsTpl->assign('form', $form->render());
        break;

    //generate surcharge
    case 'tpls_generate_surcharge':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=tplsets', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Assign Breadcrumb menu
        $xoBreadCrumb->addHelp(system_adminVersion('tplsets', 'help') . '#override');
        $xoBreadCrumb->addLink(_AM_SYSTEM_TEMPLATES_NAV_FILE_GENERATED);
        $xoBreadCrumb->render();

        if ($_REQUEST['select_modules'] == '0' || $_REQUEST['active_modules'] == '1') {
            //Generate modules
            if (isset($_REQUEST['select_theme']) && isset($_REQUEST['force_generated'])) {
                //on verifie si le dossier module existe
                $theme_surcharge = XOOPS_THEME_PATH . '/' . $_REQUEST['select_theme'] . '/modules';
                $indexFile       = XOOPS_ROOT_PATH . '/modules/system/include/index.html';
                $verif_write     = false;
                $text            = '';

                if (!is_dir($theme_surcharge)) {
                    //Creation du dossier modules

                    if (!is_dir($theme_surcharge)) {
                        mkdir($theme_surcharge, 0777);
                    }
                    chmod($theme_surcharge, 0777);
                    copy($indexFile, $theme_surcharge . '/index.html');
                }

                $tplset = system_CleanVars($POST, 'tplset', 'default', 'string');

                //on crée uniquement les templates qui n'existent pas
                /* @var $module_handler XoopsModuleHandler */
                $module_handler = xoops_getHandler('module');
                /* @var  $tplset_handler XoopsTplsetHandler */
                $tplset_handler = xoops_getHandler('tplset');
                /* @var  $tpltpl_handler XoopsTplfileHandler */
                $tpltpl_handler = xoops_getHandler('tplfile');

                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('tplset_name', $tplset));
                $tplsets_arr = $tplset_handler->getObjects();
                $tcount      = $tplset_handler->getCount();

                $tpltpl_handler = xoops_getHandler('tplfile');
                $installed_mods = $tpltpl_handler->getModuleTplCount($tplset);

                //all templates or only one template
                if ($_REQUEST['active_templates'] == 0) {
                    foreach (array_keys($tplsets_arr) as $i) {
                        $tplsetname = $tplsets_arr[$i]->getVar('tplset_name');
                        $tplstats   = $tpltpl_handler->getModuleTplCount($tplsetname);

                        if (count($tplstats) > 0) {
                            foreach ($tplstats as $moddir => $filecount) {
                                $module = $module_handler->getByDirname($moddir);
                                if (is_object($module)) {
                                    // create module folder
                                    if (!is_dir($theme_surcharge . '/' . $module->getVar('dirname'))) {
                                        mkdir($theme_surcharge . '/' . $module->getVar('dirname'), 0777);
                                        chmod($theme_surcharge . '/' . $module->getVar('dirname'), 0777);
                                        copy($indexFile, $theme_surcharge . '/' . $module->getVar('dirname') . '/index.html');
                                    }

                                    // create block folder
                                    if (!is_dir($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks')) {
                                        if (!is_dir($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks')) {
                                            mkdir($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks', 0777);
                                        }
                                        chmod($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks', 0777);
                                        copy($indexFile, $theme_surcharge . '/' . $module->getVar('dirname') . '/blocks' . '/index.html');
                                    }

                                    $class = 'odd';
                                    $text .= '<table cellspacing="1" class="outer"><tr><th colspan="3" align="center">' . _AM_SYSTEM_TEMPLATES_MODULES . ucfirst($module->getVar('dirname')) . '</th></tr><tr><th align="center">' . _AM_SYSTEM_TEMPLATES_TYPES . '</th><th  align="center">' . _AM_SYSTEM_TEMPLATES_FILES . '</th><th>' . _AM_SYSTEM_TEMPLATES_STATUS . '</th></tr>';

                                    // create template
                                    $templates      = $tpltpl_handler->find($tplsetname, 'module', null, $moddir);
                                    $templatesCount = count($templates);
                                    for ($j = 0; $j < $templatesCount; ++$j) {
                                        $filename = $templates[$j]->getVar('tpl_file');
                                        if ($tplsetname == $tplset) {
                                            $physical_file = XOOPS_THEME_PATH . '/' . $_REQUEST['select_theme'] . '/modules/' . $moddir . '/' . $filename;

                                            $tplfile = $tpltpl_handler->get($templates[$j]->getVar('tpl_id'), true);

                                            if (is_object($tplfile)) {
                                                if (!file_exists($physical_file) || $_REQUEST['force_generated'] == 1) {
                                                    $open = fopen('' . $physical_file . '', 'w+');
                                                    if (fwrite($open, '' . $tplfile->getVar('tpl_source', 'n'))) {
                                                        $text .= '<tr class="' . $class . '"><td align="center">' . _AM_SYSTEM_TEMPLATES_TEMPLATES . '</td><td>' . $physical_file . '</td><td align="center">';
                                                        if (file_exists($physical_file)) {
                                                            $text .= '<img width="16" src="' . system_AdminIcons('success.png') . '" /></td></tr>';
                                                        } else {
                                                            $text .= '<img width="16" src="' . system_AdminIcons('cancel.png') . '" /></td></tr>';
                                                        }
                                                        $verif_write = true;
                                                    }
                                                    fclose($open);
                                                    $class = ($class === 'even') ? 'odd' : 'even';
                                                }
                                            }
                                        }
                                    }

                                    // create block template
                                    $btemplates      = $tpltpl_handler->find($tplsetname, 'block', null, $moddir);
                                    $btemplatesCount = count($btemplates);
                                    for ($k = 0; $k < $btemplatesCount; ++$k) {
                                        $filename = $btemplates[$k]->getVar('tpl_file');
                                        if ($tplsetname == $tplset) {
                                            $physical_file = XOOPS_THEME_PATH . '/' . $_REQUEST['select_theme'] . '/modules/' . $moddir . '/blocks/' . $filename;
                                            $btplfile      = $tpltpl_handler->get($btemplates[$k]->getVar('tpl_id'), true);

                                            if (is_object($btplfile)) {
                                                if (!file_exists($physical_file) || $_REQUEST['force_generated'] == 1) {
                                                    $open = fopen($physical_file, 'w+');
                                                    if (fwrite($open, $btplfile->getVar('tpl_source', 'n'))) {
                                                        $text .= '<tr class="' . $class . '"><td align="center">' . _AM_SYSTEM_TEMPLATES_BLOCKS . '</td><td>' . $physical_file . '</td><td align="center">';
                                                        if (file_exists($physical_file)) {
                                                            $text .= '<img width="16" src="' . system_AdminIcons('success.png') . '" /></td></tr>';
                                                        } else {
                                                            $text .= '<img width="16" src="' . system_AdminIcons('cancel.png') . '" /></td></tr>';
                                                        }
                                                        $verif_write = true;
                                                    }
                                                    fclose($open);
                                                    $class = ($class === 'even') ? 'odd' : 'even';
                                                }
                                            }
                                        }
                                    }
                                    $text .= '</table>';
                                }
                            }
                            unset($module);
                        }
                    }
                } else {
                    foreach (array_keys($tplsets_arr) as $i) {
                        $tplsetname = $tplsets_arr[$i]->getVar('tplset_name');
                        $tplstats   = $tpltpl_handler->getModuleTplCount($tplsetname);

                        if (count($tplstats) > 0) {
                            $moddir = $_REQUEST['select_modules'];
                            $module = $module_handler->getByDirname($moddir);
                            if (is_object($module)) {
                                // create module folder
                                if (!is_dir($theme_surcharge . '/' . $module->getVar('dirname'))) {
                                    mkdir($theme_surcharge . '/' . $module->getVar('dirname'), 0777);
                                    chmod($theme_surcharge . '/' . $module->getVar('dirname'), 0777);
                                    copy($indexFile, $theme_surcharge . '/' . $module->getVar('dirname') . '/index.html');
                                }

                                // create block folder
                                if (!is_dir($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks')) {
                                    if (!is_dir($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks')) {
                                        mkdir($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks', 0777);
                                    }
                                    chmod($theme_surcharge . '/' . $module->getVar('dirname') . '/blocks', 0777);
                                    copy($indexFile, $theme_surcharge . '/' . $module->getVar('dirname') . '/blocks' . '/index.html');
                                }

                                $class = 'odd';
                                $text .= '<table cellspacing="1" class="outer"><tr><th colspan="3" align="center">' . _AM_SYSTEM_TEMPLATES_MODULES . ucfirst($module->getVar('dirname')) . '</th></tr><tr><th align="center">' . _AM_SYSTEM_TEMPLATES_TYPES . '</th><th  align="center">' . _AM_SYSTEM_TEMPLATES_FILES . '</th><th>' . _AM_SYSTEM_TEMPLATES_STATUS . '</th></tr>';
                                $select_templates_modules = $_REQUEST['select_templates_modules'];
                                $tempCount                = count($_REQUEST['select_templates_modules']);
                                for ($l = 0; $l < $tempCount; ++$l) {
                                    // create template
                                    $templates      = $tpltpl_handler->find($tplsetname, 'module', null, $moddir);
                                    $templatesCount = count($templates);
                                    for ($j = 0; $j < $templatesCount; ++$j) {
                                        $filename = $templates[$j]->getVar('tpl_file');
                                        if ($tplsetname == $tplset) {
                                            $physical_file = XOOPS_THEME_PATH . '/' . $_REQUEST['select_theme'] . '/modules/' . $moddir . '/' . $filename;

                                            $tplfile = $tpltpl_handler->get($templates[$j]->getVar('tpl_id'), true);

                                            if (is_object($tplfile)) {
                                                if (!file_exists($physical_file) || $_REQUEST['force_generated'] == 1) {
                                                    if ($select_templates_modules[$l] == $filename) {
                                                        $open = fopen('' . $physical_file . '', 'w+');
                                                        if (fwrite($open, '' . $tplfile->getVar('tpl_source', 'n'))) {
                                                            $text .= '<tr class="' . $class . '"><td align="center">' . _AM_SYSTEM_TEMPLATES_TEMPLATES . '</td><td>' . $physical_file . '</td><td align="center">';
                                                            if (file_exists($physical_file)) {
                                                                $text .= '<img width="16" src="' . system_AdminIcons('success.png') . '" /></td></tr>';
                                                            } else {
                                                                $text .= '<img width="16" src="' . system_AdminIcons('cancel.png') . '" /></td></tr>';
                                                            }
                                                            $verif_write = true;
                                                        }
                                                        fclose($open);
                                                    }
                                                    $class = ($class === 'even') ? 'odd' : 'even';
                                                }
                                            }
                                        }
                                    }

                                    // create block template
                                    $btemplates      = $tpltpl_handler->find($tplsetname, 'block', null, $moddir);
                                    $btemplatesCount = count($btemplates);
                                    for ($k = 0; $k < $btemplatesCount; ++$k) {
                                        $filename = $btemplates[$k]->getVar('tpl_file');
                                        if ($tplsetname == $tplset) {
                                            $physical_file = XOOPS_THEME_PATH . '/' . $_REQUEST['select_theme'] . '/modules/' . $moddir . '/blocks/' . $filename;
                                            $btplfile      = $tpltpl_handler->get($btemplates[$k]->getVar('tpl_id'), true);

                                            if (is_object($btplfile)) {
                                                if (!file_exists($physical_file) || $_REQUEST['force_generated'] == 1) {
                                                    if ($select_templates_modules[$l] == $filename) {
                                                        $open = fopen('' . $physical_file . '', 'w+');
                                                        if (fwrite($open, '' . $btplfile->getVar('tpl_source', 'n') . '')) {
                                                            $text .= '<tr class="' . $class . '"><td align="center">' . _AM_SYSTEM_TEMPLATES_BLOCKS . '</td><td>' . $physical_file . '</td><td align="center">';
                                                            if (file_exists($physical_file)) {
                                                                $text .= '<img width="16" src="' . system_AdminIcons('success.png') . '" /></td></tr>';
                                                            } else {
                                                                $text .= '<img width="16" src="' . system_AdminIcons('cancel.png') . '" /></td></tr>';
                                                            }
                                                            $verif_write = true;
                                                        }
                                                        fclose($open);
                                                    }
                                                    $class = ($class === 'even') ? 'odd' : 'even';
                                                }
                                            }
                                        }
                                    }
                                }
                                $text .= '</table>';
                            }
                            unset($module);
                        }
                    }
                }
                $xoopsTpl->assign('infos', $text);
                $xoopsTpl->assign('verif', $verif_write);
            } else {
                redirect_header('admin.php?fct=tplsets', 2, _AM_SYSTEM_TEMPLATES_SAVE);
            }
        } else {
            // Generate one module
            $GLOBALS['xoopsTpl']->assign('index', true);

            $tplset = system_CleanVars($POST, 'tplset', 'default', 'string');

            $form = new XoopsThemeForm(_AM_SYSTEM_TEMPLATES_SELECT_TEMPLATES, 'form', 'admin.php?fct=tplsets', 'post', true);

            $tpltpl_handler = xoops_getHandler('tplfile');
            $templates_arr  = $tpltpl_handler->find($tplset, '', null, $_REQUEST['select_modules']);

            $modules = new XoopsFormSelect(_AM_SYSTEM_TEMPLATES_SELECT_TEMPLATES, 'select_templates_modules', null, 10, true);
            foreach (array_keys($templates_arr) as $i) {
                $modules->addOption($templates_arr[$i]->getVar('tpl_file'));
            }
            $form->addElement($modules);

            $form->addElement(new XoopsFormHidden('active_templates', '1'));
            $form->addElement(new XoopsFormHidden('force_generated', $_REQUEST['force_generated']));
            $form->addElement(new XoopsFormHidden('select_modules', $_REQUEST['select_modules']));
            $form->addElement(new XoopsFormHidden('active_modules', '1'));
            $form->addElement(new XoopsFormHidden('select_theme', $_REQUEST['select_theme']));
            $form->addElement(new XoopsFormHidden('op', 'tpls_generate_surcharge'));
            $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
            $xoopsTpl->assign('form', $form->render());
        }
        break;

    // save
    case 'tpls_save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=tplsets', 2, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        XoopsLoad::load('XoopsRequest');
        $clean_path_file = XoopsRequest::getString('path_file', '');
        if (!empty($clean_path_file)) {
            $path_file = realpath(XOOPS_ROOT_PATH.'/themes'.trim($clean_path_file));
            $path_file = str_replace('\\','/',$path_file);
            $pathInfo = pathinfo($path_file);
            if (!in_array($pathInfo['extension'], array('css', 'html', 'tpl'))) {
                redirect_header('admin.php?fct=tplsets', 2, _AM_SYSTEM_TEMPLATES_ERROR);
                exit;
            }
            // copy file
            $copy_file = $path_file . '.back';
            copy($path_file, $copy_file);
            // Save modif
            if (isset($_REQUEST['templates'])) {
                $open = fopen('' . $path_file . '', 'w+');
                if (!fwrite($open, utf8_encode(stripslashes($_REQUEST['templates'])))) {
                    redirect_header('admin.php?fct=tplsets', 2, _AM_SYSTEM_TEMPLATES_ERROR);
                }
                fclose($open);
            }
        }
        redirect_header('admin.php?fct=tplsets', 2, _AM_SYSTEM_TEMPLATES_SAVE);
        break;
}
// Call Footer
xoops_cp_footer();
