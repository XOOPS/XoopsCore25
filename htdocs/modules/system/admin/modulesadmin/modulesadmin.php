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

/*
if ( !is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid()) ) {
    exit("Access Denied");
}
*/

/**
 * @param $dirname
 *
 * @return string
 */
function xoops_module_install($dirname)
{
    global $xoopsUser, $xoopsConfig;
    $dirname        = trim($dirname);
    $db             =& $GLOBALS['xoopsDB'];
    $reservedTables = array(
        'avatar',
        'avatar_users_link',
        'block_module_link',
        'xoopscomments',
        'config',
        'configcategory',
        'configoption',
        'image',
        'imagebody',
        'imagecategory',
        'imgset',
        'imgset_tplset_link',
        'imgsetimg',
        'groups',
        'groups_users_link',
        'group_permission',
        'online',
        'bannerclient',
        'banner',
        'bannerfinish',
        'priv_msgs',
        'ranks',
        'session',
        'smiles',
        'users',
        'newblocks',
        'modules',
        'tplfile',
        'tplset',
        'tplsource',
        'xoopsnotifications',
        'banner',
        'bannerclient',
        'bannerfinish');
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    if ($module_handler->getCount(new Criteria('dirname', $dirname)) == 0) {
        $module = $module_handler->create();
        $module->loadInfoAsVar($dirname);
        $module->setVar('weight', 1);
        $module->setVar('isactive', 1);
        $module->setVar('last_update', time());
        $error = false;
        $errs  = array();
        $msgs  = array();

        $msgs[] = '<div id="xo-module-log"><div class="header">';
        $msgs[] = $errs[] = '<h4>' . _AM_SYSTEM_MODULES_INSTALLING . $module->getInfo('name', 's') . '</h4>';
        if ($module->getInfo('image') !== false && trim($module->getInfo('image')) != '') {
            $msgs[] = '<a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/' . $module->getInfo('adminindex') . '"><img src="' . XOOPS_URL . '/modules/' . $dirname . '/' . trim($module->getInfo('image')) . '" alt="" /></a>';
        }
        $msgs[] = '<strong>' . _VERSION . ':</strong> ' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status');
        if ($module->getInfo('author') !== false && trim($module->getInfo('author')) != '') {
            $msgs[] = '<strong>' . _AUTHOR . ':</strong> ' . htmlspecialchars(trim($module->getInfo('author')));
        }
        $msgs[] = '</div><div class="logger">';
        // Load module specific install script if any
        $install_script = $module->getInfo('onInstall');
        if ($install_script && trim($install_script) != '') {
            include_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($install_script);
        }
        $func = "xoops_module_pre_install_{$dirname}";
        // If pre install function is defined, execute
        if (function_exists($func)) {
            $result = $func($module);
            if (!$result) {
                $error  = true;
                $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                $errs   = array_merge($errs, $module->getErrors());
            } else {
                $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>{$func}</strong>") . '</p>';
                $msgs += $module->getErrors();
            }
        }

        if ($error === false) {
            $sqlfile = $module->getInfo('sqlfile');
            if (is_array($sqlfile) && !empty($sqlfile[XOOPS_DB_TYPE])) {
                $sql_file_path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . $sqlfile[XOOPS_DB_TYPE];
                if (!file_exists($sql_file_path)) {
                    $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_SQL_NOT_FOUND, "<strong>{$sql_file_path}</strong>");
                    $error  = true;
                } else {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_SQL_FOUND, "<strong>{$sql_file_path}</strong>") . '<br  />' . _AM_SYSTEM_MODULES_CREATE_TABLES;
                    include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
                    $sql_query = fread(fopen($sql_file_path, 'r'), filesize($sql_file_path));
                    $sql_query = trim($sql_query);
                    SqlUtility::splitMySqlFile($pieces, $sql_query);
                    $created_tables = array();
                    foreach ($pieces as $piece) {
                        // [0] contains the prefixed query
                        // [4] contains unprefixed table name
                        $prefixed_query = SqlUtility::prefixQuery($piece, $db->prefix());
                        if (!$prefixed_query) {
                            $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_SQL_NOT_VALID, '<strong>' . $piece . '</strong>');
                            $error  = true;
                            break;
                        }
                        // check if the table name is reserved
                        if (!in_array($prefixed_query[4], $reservedTables)) {
                            // not reserved, so try to create one
                            if (!$db->query($prefixed_query[0])) {
                                $errs[] = $db->error();
                                $error  = true;
                                break;
                            } else {
                                if (!in_array($prefixed_query[4], $created_tables)) {
                                    $msgs[]           = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TABLE_CREATED, '<strong>' . $db->prefix($prefixed_query[4]) . '</strong>');
                                    $created_tables[] = $prefixed_query[4];
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_INSERT_DATA, '<strong>' . $db->prefix($prefixed_query[4]) . '</strong>');
                                }
                            }
                        } else {
                            // the table name is reserved, so halt the installation
                            $errs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TABLE_RESERVED, '<strong>' . $prefixed_query[4] . '</strong>');
                            $error  = true;
                            break;
                        }
                    }
                    // if there was an error, delete the tables created so far, so the next installation will not fail
                    if ($error === true) {
                        foreach ($created_tables as $ct) {
                            $db->query('DROP TABLE ' . $db->prefix($ct));
                        }
                    }
                }
            }
        }
        // if no error, save the module info and blocks info associated with it
        if ($error === false) {
            if (!$module_handler->insert($module)) {
                $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_INSERT_DATA_FAILD, '<strong>' . $module->getVar('name') . '</strong>');
                foreach ($created_tables as $ct) {
                    $db->query('DROP TABLE ' . $db->prefix($ct));
                }
                $ret = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILINS, '<strong>' . $module->name() . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>';
                foreach ($errs as $err) {
                    $ret .= ' - ' . $err . '<br>';
                }
                $ret .= '</p>';
                unset($module, $created_tables, $errs, $msgs);

                return $ret;
            } else {
                $newmid = $module->getVar('mid');
                unset($created_tables);
                $msgs[]          = '<p>' . _AM_SYSTEM_MODULES_INSERT_DATA_DONE . sprintf(_AM_SYSTEM_MODULES_MODULEID, '<strong>' . $newmid . '</strong>');
                $tplfile_handler = xoops_getHandler('tplfile');
                $templates       = $module->getInfo('templates');
                if ($templates !== false) {
                    $msgs[] = _AM_SYSTEM_MODULES_TEMPLATES_ADD;
                    foreach ($templates as $tpl) {
                        $tplfile = $tplfile_handler->create();
                        $type    = (isset($tpl['type']) ? $tpl['type'] : 'module');
                        $tpldata =&  xoops_module_gettemplate($dirname, $tpl['file'], $type);
                        $tplfile->setVar('tpl_source', $tpldata, true);
                        $tplfile->setVar('tpl_refid', $newmid);

                        $tplfile->setVar('tpl_tplset', 'default');
                        $tplfile->setVar('tpl_file', $tpl['file']);
                        $tplfile->setVar('tpl_desc', $tpl['description'], true);
                        $tplfile->setVar('tpl_module', $dirname);
                        $tplfile->setVar('tpl_lastmodified', time());
                        $tplfile->setVar('tpl_lastimported', time());
                        $tplfile->setVar('tpl_type', $type);
                        if (!$tplfile_handler->insert($tplfile)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                        } else {
                            $newtplid = $tplfile->getVar('tpl_id');
                            $msgs[]   = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_DATA, '<strong>' . $tpl['file'] . '</strong>') . '(ID: <strong>' . $newtplid . '</strong>)';
                            // generate compiled file
                            include_once XOOPS_ROOT_PATH . '/class/template.php';
                            if (!xoops_template_touch($newtplid)) {
                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED_FAILED, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                            } else {
                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED, '<strong>' . $tpl['file'] . '</strong>');
                            }
                        }
                        unset($tplfile, $tpldata);
                    }
                }
                include_once XOOPS_ROOT_PATH . '/class/template.php';
                xoops_template_clear_module_cache($newmid);
                $blocks = $module->getInfo('blocks');
                if ($blocks !== false) {
                    $msgs[] = _AM_SYSTEM_MODULES_BLOCKS_ADD;
                    foreach ($blocks as $blockkey => $block) {
                        // break the loop if missing block config
                        if (!isset($block['file']) || !isset($block['show_func'])) {
                            break;
                        }
                        $options = '';
                        if (!empty($block['options'])) {
                            $options = trim($block['options']);
                        }
                        $newbid    = $db->genId($db->prefix('newblocks') . '_bid_seq');
                        $edit_func = isset($block['edit_func']) ? trim($block['edit_func']) : '';
                        $template  = '';
                        if (isset($block['template']) && trim($block['template']) != '') {
                            $content =& xoops_module_gettemplate($dirname, $block['template'], 'blocks');
                        }
                        if (empty($content)) {
                            $content = '';
                        } else {
                            $template = trim($block['template']);
                        }
                        $block_name = addslashes(trim($block['name']));
                        $sql        = 'INSERT INTO ' . $db->prefix('newblocks') . " (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified) VALUES ($newbid, $newmid, " . (int)$blockkey . ", '$options', '" . $block_name . "','" . $block_name . "', '', 0, 0, 0, 'M', 'H', 1, '" . addslashes($dirname) . "', '" . addslashes(trim($block['file'])) . "', '" . addslashes(trim($block['show_func'])) . "', '" . addslashes($edit_func) . "', '" . $template . "', 0, " . time() . ')';
                        if (!$db->query($sql)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_BLOCK_ADD_ERROR, '<strong>' . $block['name'] . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ADD_ERROR_DATABASE, '<strong>' . $db->error() . '</strong>') . '</span>';
                        } else {
                            if (empty($newbid)) {
                                $newbid = $db->getInsertId();
                            }
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_ADD, '<strong>' . $block['name'] . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $newbid . '</strong>');
                            $sql    = 'INSERT INTO ' . $db->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newbid . ', -1)';
                            $db->query($sql);
                            if ($template != '') {
                                $tplfile = $tplfile_handler->create();
                                $tplfile->setVar('tpl_refid', $newbid);
                                $tplfile->setVar('tpl_source', $content, true);
                                $tplfile->setVar('tpl_tplset', 'default');
                                $tplfile->setVar('tpl_file', $block['template']);
                                $tplfile->setVar('tpl_module', $dirname);
                                $tplfile->setVar('tpl_type', 'block');
                                $tplfile->setVar('tpl_desc', $block['description'], true);
                                $tplfile->setVar('tpl_lastimported', 0);
                                $tplfile->setVar('tpl_lastmodified', time());
                                if (!$tplfile_handler->insert($tplfile)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                } else {
                                    $newtplid = $tplfile->getVar('tpl_id');
                                    $msgs[]   = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_DATA, '<strong>' . $block['template'] . '</strong>') . ' (ID: <strong>' . $newtplid . '</strong>)';
                                    // generate compiled file
                                    include_once XOOPS_ROOT_PATH . '/class/template.php';
                                    if (!xoops_template_touch($newtplid)) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED_FAILED, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                    } else {
                                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED, '<strong>' . $block['template'] . '</strong>');
                                    }
                                }
                                unset($tplfile);
                            }
                        }
                        unset($content);
                    }
                    unset($blocks);
                }
                $configs = $module->getInfo('config');
                if ($configs !== false) {
                    if ($module->getVar('hascomments') != 0) {
                        include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                        $configs[] = array(
                            'name'        => 'com_rule',
                            'title'       => '_CM_COMRULES',
                            'description' => '',
                            'formtype'    => 'select',
                            'valuetype'   => 'int',
                            'default'     => 1,
                            'options'     => array(
                                '_CM_COMNOCOM'        => XOOPS_COMMENT_APPROVENONE,
                                '_CM_COMAPPROVEALL'   => XOOPS_COMMENT_APPROVEALL,
                                '_CM_COMAPPROVEUSER'  => XOOPS_COMMENT_APPROVEUSER,
                                '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                        $configs[] = array(
                            'name'        => 'com_anonpost',
                            'title'       => '_CM_COMANONPOST',
                            'description' => '',
                            'formtype'    => 'yesno',
                            'valuetype'   => 'int',
                            'default'     => 0);
                    }
                } else {
                    if ($module->getVar('hascomments') != 0) {
                        $configs = array();
                        include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                        $configs[] = array(
                            'name'        => 'com_rule',
                            'title'       => '_CM_COMRULES',
                            'description' => '',
                            'formtype'    => 'select',
                            'valuetype'   => 'int',
                            'default'     => 1,
                            'options'     => array(
                                '_CM_COMNOCOM'        => XOOPS_COMMENT_APPROVENONE,
                                '_CM_COMAPPROVEALL'   => XOOPS_COMMENT_APPROVEALL,
                                '_CM_COMAPPROVEUSER'  => XOOPS_COMMENT_APPROVEUSER,
                                '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                        $configs[] = array(
                            'name'        => 'com_anonpost',
                            'title'       => '_CM_COMANONPOST',
                            'description' => '',
                            'formtype'    => 'yesno',
                            'valuetype'   => 'int',
                            'default'     => 0);
                    }
                }
                // RMV-NOTIFY
                if ($module->getVar('hasnotification') != 0) {
                    if (empty($configs)) {
                        $configs = array();
                    }
                    // Main notification options
                    include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                    include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
                    $options                             = array();
                    $options['_NOT_CONFIG_DISABLE']      = XOOPS_NOTIFICATION_DISABLE;
                    $options['_NOT_CONFIG_ENABLEBLOCK']  = XOOPS_NOTIFICATION_ENABLEBLOCK;
                    $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
                    $options['_NOT_CONFIG_ENABLEBOTH']   = XOOPS_NOTIFICATION_ENABLEBOTH;

                    $configs[] = array(
                        'name'        => 'notification_enabled',
                        'title'       => '_NOT_CONFIG_ENABLE',
                        'description' => '_NOT_CONFIG_ENABLEDSC',
                        'formtype'    => 'select',
                        'valuetype'   => 'int',
                        'default'     => XOOPS_NOTIFICATION_ENABLEBOTH,
                        'options'     => $options);
                    // Event-specific notification options
                    // FIXME: doesn't work when update module... can't read back the array of options properly...  " changing to &quot;
                    $options    = array();
                    $categories =& notificationCategoryInfo('', $module->getVar('mid'));
                    foreach ($categories as $category) {
                        $events =& notificationEvents($category['name'], false, $module->getVar('mid'));
                        foreach ($events as $event) {
                            if (!empty($event['invisible'])) {
                                continue;
                            }
                            $option_name           = $category['title'] . ' : ' . $event['title'];
                            $option_value          = $category['name'] . '-' . $event['name'];
                            $options[$option_name] = $option_value;
                        }
                        unset($events);
                    }
                    unset($categories);
                    $configs[] = array(
                        'name'        => 'notification_events',
                        'title'       => '_NOT_CONFIG_EVENTS',
                        'description' => '_NOT_CONFIG_EVENTSDSC',
                        'formtype'    => 'select_multi',
                        'valuetype'   => 'array',
                        'default'     => array_values($options),
                        'options'     => $options);
                }

                if ($configs !== false) {
                    $msgs[]         = _AM_SYSTEM_MODULES_MODULE_DATA_ADD;
                    /* @var $config_handler XoopsConfigHandler  */
                    $config_handler = xoops_getHandler('config');
                    $order          = 0;
                    foreach ($configs as $config) {
                        $confobj = $config_handler->createConfig();
                        $confobj->setVar('conf_modid', $newmid);
                        $confobj->setVar('conf_catid', 0);
                        $confobj->setVar('conf_name', $config['name']);
                        $confobj->setVar('conf_title', $config['title'], true);
                        $confobj->setVar('conf_desc', isset($config['description']) ? $config['description'] : '', true);
                        $confobj->setVar('conf_formtype', $config['formtype']);
                        $confobj->setVar('conf_valuetype', $config['valuetype']);
                        $confobj->setConfValueForInput($config['default'], true);
                        $confobj->setVar('conf_order', $order);
                        $confop_msgs = '';
                        if (isset($config['options']) && is_array($config['options'])) {
                            foreach ($config['options'] as $key => $value) {
                                $confop = $config_handler->createConfigOption();
                                $confop->setVar('confop_name', $key, true);
                                $confop->setVar('confop_value', $value, true);
                                $confobj->setConfOptions($confop);
                                $confop_msgs .= '<br>&nbsp;&nbsp;&nbsp;&nbsp; ' . _AM_SYSTEM_MODULES_CONFIG_ADD . _AM_SYSTEM_MODULES_NAME . ' <strong>' . (defined($key) ? constant($key) : $key) . '</strong> ' . _AM_SYSTEM_MODULES_VALUE . ' <strong>' . $value . '</strong> ';
                                unset($confop);
                            }
                        }
                        ++$order;
                        if ($config_handler->insertConfig($confobj) != false) {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD, '<strong>' . $config['name'] . '</strong>') . $confop_msgs;
                        } else {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD_ERROR, '<strong>' . $config['name'] . '</strong>') . '</span>';
                        }
                        unset($confobj);
                    }
                    unset($configs);
                }
            }
            $groups = array(XOOPS_GROUP_ADMIN);
            if ($module->getInfo('hasMain')) {
                $groups = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
            }
            // retrieve all block ids for this module
            $blocks        = XoopsBlock::getByModule($newmid, false);
            $msgs[]        = _AM_SYSTEM_MODULES_GROUP_SETTINGS_ADD;
            /* @var  $gperm_handler XoopsGroupPermHandler */
            $gperm_handler = xoops_getHandler('groupperm');
            foreach ($groups as $mygroup) {
                if ($gperm_handler->checkRight('module_admin', 0, $mygroup)) {
                    /* @var $mperm XoopsGroupPerm */
                    $mperm = $gperm_handler->create();
                    $mperm->setVar('gperm_groupid', $mygroup);
                    $mperm->setVar('gperm_itemid', $newmid);
                    $mperm->setVar('gperm_name', 'module_admin');
                    $mperm->setVar('gperm_modid', 1);
                    if (!$gperm_handler->insert($mperm)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_ACCESS_ADMIN_ADD_ERROR, '<strong>' . $mygroup . '</strong>') . '</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_ACCESS_ADMIN_ADD, '<strong>' . $mygroup . '</strong>');
                    }
                    unset($mperm);
                }
                $mperm = $gperm_handler->create();
                $mperm->setVar('gperm_groupid', $mygroup);
                $mperm->setVar('gperm_itemid', $newmid);
                $mperm->setVar('gperm_name', 'module_read');
                $mperm->setVar('gperm_modid', 1);
                if (!$gperm_handler->insert($mperm)) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_ACCESS_USER_ADD_ERROR, '<strong>' . $mygroup . '</strong>') . '</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_ACCESS_USER_ADD_ERROR, '<strong>' . $mygroup . '</strong>');
                }
                unset($mperm);
                foreach ($blocks as $blc) {
                    /* @var $bperm XoopsGroupPerm */
                    $bperm = $gperm_handler->create();
                    $bperm->setVar('gperm_groupid', $mygroup);
                    $bperm->setVar('gperm_itemid', $blc);
                    $bperm->setVar('gperm_name', 'block_read');
                    $bperm->setVar('gperm_modid', 1);
                    if (!$gperm_handler->insert($bperm)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_BLOCK_ACCESS_ERROR . ' Block ID: <strong>' . $blc . '</strong> Group ID: <strong>' . $mygroup . '</strong></span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_BLOCK_ACCESS . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $blc . '</strong>') . sprintf(_AM_SYSTEM_MODULES_GROUP_ID, '<strong>' . $mygroup . '</strong>');
                    }
                    unset($bperm);
                }
            }
            unset($blocks, $groups);

            // execute module specific install script if any
            $func = "xoops_module_install_{$dirname}";
            if (function_exists($func)) {
                if (!$lastmsg = $func($module)) {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                } else {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>{$func}</strong>") . '</p>';
                    if (is_string($lastmsg)) {
                        $msgs[] = $lastmsg;
                    }
                }
            }

            $msgs[] = sprintf(_AM_SYSTEM_MODULES_OKINS, '<strong>' . $module->getVar('name', 's') . '</strong>');
            $msgs[] = '</div></div>';

            $blocks = $module->getInfo('blocks');
            $redDevider = '<span class="red bold">  |  </span>';
            $msgs[] = '<div class="noininstall center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a> |
                        <a href="admin.php?fct=modulesadmin&op=installlist">' . _AM_SYSTEM_MODULES_TOINSTALL . '</a> | ';
            $msgs[] = '<br><span class="red bold">' . _AM_SYSTEM_MODULES_MODULE . ' ' . $module->getInfo('name') . ': </span></div>';
            if ($blocks !== false) {
                $msgs[] = '<div class="noininstall center"><a href="admin.php?fct=blocksadmin&op=list&filter=1&selgen=' . $newmid . '&selmod=-2&selgrp=-1&selvis=-1&filsave=1">' . _AM_SYSTEM_BLOCKS . '</a></div>';
            }
            $msgs[] = '<div class="noininstall center"><a href="admin.php?fct=preferences&op=showmod&mod=' . $newmid . '">' . _AM_SYSTEM_PREF . '</a>';
            $msgs[] = '<a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/' . $module->getInfo('adminindex') . '">' . _AM_SYSTEM_MODULES_ADMIN . '</a>';

            $testdataDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getInfo('dirname', 'e') . '/testdata';
            if (file_exists($testdataDirectory)) {
                $msgs[] = '<a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/testdata/index.php?op=load' . '">' . _AM_SYSTEM_MODULES_INSTALL_TESTDATA . '</a></div>';
            } else {
                $msgs[] = '</div>';
            }

            $ret = implode('<br>', $msgs);
            unset($blocks, $msgs, $errs, $module);

            return $ret;
        } else {
            $ret = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILINS, '<strong>' . $dirname . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>' . implode('<br>', $errs) . '</p>';
            unset($msgs, $errs);

            return $ret;
        }
    } else {
        return '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILINS, '<strong>' . $dirname . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_ALEXISTS, $dirname) . '</p>';
    }
}

/**
 * @param        $dirname
 * @param        $template
 * @param string $type
 *
 * @return string
 */
function &xoops_module_gettemplate($dirname, $template, $type = '')
{
    global $xoopsConfig;
    $ret = '';
    switch ($type) {
        case 'blocks':
        case 'admin':
            $path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/' . $type . '/' . $template;
            break;
        default:
            $path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/' . $template;
            break;
    }
    if (!file_exists($path)) {
        return $ret;
    } else {
        $lines = file($path);
    }
    if (!$lines) {
        return $ret;
    }
    $count = count($lines);
    for ($i = 0; $i < $count; ++$i) {
        $ret .= str_replace("\n", "\r\n", str_replace("\r\n", "\n", $lines[$i]));
    }

    return $ret;
}

/**
 * @param $dirname
 *
 * @return string
 */
function xoops_module_uninstall($dirname)
{
    global $xoopsConfig;
    $reservedTables = array(
        'avatar',
        'avatar_users_link',
        'block_module_link',
        'xoopscomments',
        'config',
        'configcategory',
        'configoption',
        'image',
        'imagebody',
        'imagecategory',
        'imgset',
        'imgset_tplset_link',
        'imgsetimg',
        'groups',
        'groups_users_link',
        'group_permission',
        'online',
        'bannerclient',
        'banner',
        'bannerfinish',
        'priv_msgs',
        'ranks',
        'session',
        'smiles',
        'users',
        'newblocks',
        'modules',
        'tplfile',
        'tplset',
        'tplsource',
        'xoopsnotifications',
        'banner',
        'bannerclient',
        'bannerfinish');
    $db             = XoopsDatabaseFactory::getDatabaseConnection();
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->getByDirname($dirname);
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    xoops_template_clear_module_cache($module->getVar('mid'));
    if ($module->getVar('dirname') === 'system') {
        return '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILUNINS, '<strong>' . $module->getVar('name') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br> - ' . _AM_SYSTEM_MODULES_SYSNO . '</p>';
    } elseif ($module->getVar('dirname') == $xoopsConfig['startpage']) {
        return '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILUNINS, '<strong>' . $module->getVar('name') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br> - ' . _AM_SYSTEM_MODULES_STRTNO . '</p>';
    } else {
        $msgs   = array();
        $msgs[] = '<div id="xo-module-log"><div class="header">';
        $msgs[] = $errs[] = '<h4>' . _AM_SYSTEM_MODULES_UNINSTALL . $module->getInfo('name', 's') . '</h4>';
        if ($module->getInfo('image') !== false && trim($module->getInfo('image')) != '') {
            $msgs[] = '<img src="' . XOOPS_URL . '/modules/' . $dirname . '/' . trim($module->getInfo('image')) . '" alt="" />';
        }
        $msgs[] = '<strong>' . _VERSION . ':</strong> ' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status');
        if ($module->getInfo('author') !== false && trim($module->getInfo('author')) != '') {
            $msgs[] = '<strong>' . _AUTHOR . ':</strong> ' . htmlspecialchars(trim($module->getInfo('author')));
        }
        $msgs[] = '</div><div class="logger">';
        // Load module specific install script if any
        $uninstall_script = $module->getInfo('onUninstall');
        if ($uninstall_script && trim($uninstall_script) != '') {
            include_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($uninstall_script);
        }
        $func = "xoops_module_pre_uninstall_{$dirname}";
        // If pre uninstall function is defined, execute
        if (function_exists($func)) {
            $result = $func($module);
            if (false === $result) {
                $errs   = $module->getErrors();
                $errs[] = sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func);

                return '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILUNINS, '<strong>' . $module->getVar('name') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>' . implode('<br>', $errs) . '</p>';
            } else {
                $prevErrs = $module->getErrors();
                array_unshift($prevErrs, '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>{$func}</strong>") . '</p>');
                $msgs = array_merge($msgs, $prevErrs);
            }
        }

        if (false === $module_handler->delete($module)) {
            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_DELETE_ERROR, $module->getVar('name')) . '</span>';
        } else {

            // delete template files
            $tplfile_handler = xoops_getHandler('tplfile');
            $templates       = $tplfile_handler->find(null, 'module', $module->getVar('mid'));
            $tcount          = count($templates);
            if ($tcount > 0) {
                $msgs[] = _AM_SYSTEM_MODULES_TEMPLATES_DELETE;
                for ($i = 0; $i < $tcount; ++$i) {
                    if (false === $tplfile_handler->delete($templates[$i])) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_DELETE_DATA_FAILD, $templates[$i]->getVar('tpl_file')) . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ID, '<strong>' . $templates[$i]->getVar('tpl_id') . '</strong>') . '</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_DELETE_DATA, '<strong>' . $templates[$i]->getVar('tpl_file') . '</strong>') . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ID, '<strong>' . $templates[$i]->getVar('tpl_id') . '</strong>');
                    }
                }
            }
            unset($templates);

            // delete blocks and block tempalte files
            $block_arr = XoopsBlock::getByModule($module->getVar('mid'));
            if (is_array($block_arr)) {
                $bcount = count($block_arr);
                $msgs[] = _AM_SYSTEM_MODULES_BLOCKS_DELETE;
                for ($i = 0; $i < $bcount; ++$i) {
                    if (false === $block_arr[$i]->delete()) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DELETE_ERROR, '<strong>' . $block_arr[$i]->getVar('name') . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $block_arr[$i]->getVar('bid') . '</strong>') . '</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DELETE, '<strong>' . $block_arr[$i]->getVar('name') . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $block_arr[$i]->getVar('bid') . '</strong>');
                    }
                    if ($block_arr[$i]->getVar('template') != '') {
                        $templates = $tplfile_handler->find(null, 'block', $block_arr[$i]->getVar('bid'));
                        $btcount   = count($templates);
                        if ($btcount > 0) {
                            for ($j = 0; $j < $btcount; ++$j) {
                                if (!$tplfile_handler->delete($templates[$j])) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DELETE_TEMPLATE_ERROR, $templates[$j]->getVar('tpl_file')) . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ID, '<strong>' . $templates[$j]->getVar('tpl_id') . '</strong>') . '</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DELETE_DATA, '<strong>' . $templates[$j]->getVar('tpl_file') . '</strong>') . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ID, '<strong>' . $templates[$j]->getVar('tpl_id') . '</strong>');
                                }
                            }
                        }
                        unset($templates);
                    }
                }
            }

            // delete tables used by this module
            $modtables = $module->getInfo('tables');
            if ($modtables !== false && is_array($modtables)) {
                $msgs[] = _AM_SYSTEM_MODULES_DELETE_MOD_TABLES;
                foreach ($modtables as $table) {
                    // prevent deletion of reserved core tables!
                    if (!in_array($table, $reservedTables)) {
                        $sql = 'DROP TABLE ' . $db->prefix($table);
                        if (!$db->query($sql)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TABLE_DROPPED_ERROR, '<strong>' . $db->prefix($table) . '</strong>') . '</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TABLE_DROPPED, '<strong>' . $db->prefix($table) . '</strong>');
                        }
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TABLE_DROPPED_FAILDED, '<strong>' . $db->prefix($table) . '</strong>') . '</span>';
                    }
                }
            }

            // delete comments if any
            if ($module->getVar('hascomments') != 0) {
                $msgs[]          = _AM_SYSTEM_MODULES_COMMENTS_DELETE;
                $comment_handler = xoops_getHandler('comment');
                if (false === $comment_handler->deleteByModule($module->getVar('mid'))) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_COMMENTS_DELETE_ERROR . '</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_COMMENTS_DELETED;
                }
            }

            // RMV-NOTIFY
            // delete notifications if any
            if ($module->getVar('hasnotification') != 0) {
                $msgs[] = _AM_SYSTEM_MODULES_NOTIFICATIONS_DELETE;
                if (false === xoops_notification_deletebymodule($module->getVar('mid'))) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_NOTIFICATIONS_DELETE_ERROR . '</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_NOTIFICATIONS_DELETED;
                }
            }

            // delete permissions if any
            $gperm_handler = xoops_getHandler('groupperm');
            if (false === $gperm_handler->deleteByModule($module->getVar('mid'))) {
                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_GROUP_PERMS_DELETE_ERROR . '</span>';
            } else {
                $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_GROUP_PERMS_DELETED;
            }

            // delete module config options if any
            if ($module->getVar('hasconfig') != 0 || $module->getVar('hascomments') != 0) {
                /* @var $config_handler XoopsConfigHandler  */
                $config_handler = xoops_getHandler('config');
                $configs        = $config_handler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
                $confcount      = count($configs);
                if ($confcount > 0) {
                    $msgs[] = _AM_SYSTEM_MODULES_MODULE_DATA_DELETE;
                    for ($i = 0; $i < $confcount; ++$i) {
                        if (false === $config_handler->deleteConfig($configs[$i])) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_CONFIG_DATA_DELETE_ERROR . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, '<strong>' . $configs[$i]->getvar('conf_id') . '</strong>') . '</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_GONFIG_DATA_DELETE . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, '<strong>' . $configs[$i]->getvar('conf_id') . '</strong>');
                        }
                    }
                }
            }

            // execute module specific install script if any
            $func = 'xoops_module_uninstall_' . $dirname;
            if (function_exists($func)) {
                if (!$func($module)) {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                } else {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>{$func}</strong>") . '</p>';
                }
            }
            $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_OKUNINS, '<strong>' . $module->getVar('name') . '</strong>') . '</p>';
        }
        $msgs[] = '</div></div>';
        $msgs[] = '<div class="center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a></div>';
        $ret    = implode('<br>', $msgs);

        return $ret;
    }
}

/**
 * @param $dirname
 * @return string
 */
function xoops_module_update($dirname)
{
    global $xoopsUser, $xoopsConfig, $xoopsTpl;
    $dirname = trim($dirname);
    $xoopsDB =& $GLOBALS['xoopsDB'];

    $myts = MyTextSanitizer::getInstance();

    $dirname        = $myts->htmlspecialchars(trim($dirname));
    /* @var $module_handler XoopsModuleHandler */
    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->getByDirname($dirname);
    // Save current version for use in the update function
    $prev_version = $module->getVar('version');
    $clearTpl     = new XoopsTpl();
    $clearTpl->clearCache($dirname);

    // we don't want to change the module name set by admin
    $temp_name = $module->getVar('name');
    $module->loadInfoAsVar($dirname);
    $module->setVar('name', $temp_name);
    $module->setVar('last_update', time());
    /*
        // Call Header
        // Define main template
        $GLOBALS['xoopsOption']['template_main'] = 'system_header.html';
        // Call Header
        xoops_cp_header();
        // Define Stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
        // Define Breadcrumb and tips
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_ADMIN, system_adminVersion('modulesadmin', 'adminpath'));
        $xoBreadCrumb->addLink(_AM_SYSTEM_MODULES_UPDATE);
        $xoBreadCrumb->addHelp(system_adminVersion('modulesadmin', 'help') . '#update');https://www.facebook.com/photo.php?v=10154358806675333
        $xoBreadCrumb->render();

        */
    if (!$module_handler->insert($module)) {
        echo '<p>Could not update ' . $module->getVar('name') . '</p>';
        echo "<br><div class='center'><a href='admin.php?fct=modulesadmin'>" . _AM_SYSTEM_MODULES_BTOMADMIN . '</a></div>';
    } else {
        $newmid = $module->getVar('mid');
        $msgs   = array();
        $msgs[] = '<div id="xo-module-log"><div class="header">';
        $msgs[] = $errs[] = '<h4>' . _AM_SYSTEM_MODULES_UPDATING . $module->getInfo('name', 's') . '</h4>';
        if ($module->getInfo('image') !== false && trim($module->getInfo('image')) != '') {
            $msgs[] = '<img src="' . XOOPS_URL . '/modules/' . $dirname . '/' . trim($module->getInfo('image')) . '" alt="" />';
        }
        $msgs[] = '<strong>' . _VERSION . ':</strong> ' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status');
        if ($module->getInfo('author') !== false && trim($module->getInfo('author')) != '') {
            $msgs[] = '<strong>' . _AUTHOR . ':</strong> ' . $myts->htmlspecialchars(trim($module->getInfo('author')));
        }
        $msgs[]          = '</div><div class="logger">';

        $update_script = $module->getInfo('onUpdate');
        if (!empty($update_script) && trim($update_script) != '') {
            include_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($update_script);
        }
        // execute module specific update script if any
        if (function_exists('xoops_module_pre_update_' . $dirname)) {
            $func = 'xoops_module_pre_update_' . $dirname;
            if (!$func($module, $prev_version)) {
                $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                $msgs = array_merge($msgs, $module->getErrors());
            } else {
                $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, '<strong>' . $func . '</strong>') . '</p>';
                $msgs += $module->getErrors();
            }
        }
        $msgs[]          = _AM_SYSTEM_MODULES_MODULE_DATA_UPDATE;
        /* @var $tplfile_handler XoopsTplfileHandler */
        $tplfile_handler = xoops_getHandler('tplfile');
        // irmtfan bug fix: remove codes for delete templates
        /*
        $deltpl          = $tplfile_handler->find('default', 'module', $module->getVar('mid'));
        $delng           = array();
        if (is_array($deltpl)) {
            // delete template file entry in db
            $dcount = count($deltpl);
            for ($i = 0; $i < $dcount; $i++) {
                if (!$tplfile_handler->delete($deltpl[$i])) {
                    $delng[] = $deltpl[$i]->getVar('tpl_file');
                }
            }
        }
        */
        // irmtfan bug fix: remove codes for delete templates
        $templates = $module->getInfo('templates');
        if ($templates !== false) {
            $msgs[] = _AM_SYSTEM_MODULES_TEMPLATES_UPDATE;
            foreach ($templates as $tpl) {
                $tpl['file'] = trim($tpl['file']);
                // START irmtfan solve templates duplicate issue
                // if (!in_array($tpl['file'], $delng)) { // irmtfan bug fix: remove codes for delete templates
                $type = (isset($tpl['type']) ? $tpl['type'] : 'module');
                if (preg_match("/\.css$/i", $tpl['file'])) {
                    $type = 'css';
                }
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('tpl_refid', $newmid), 'AND');
                $criteria->add(new Criteria('tpl_module', $dirname), 'AND');
                $criteria->add(new Criteria('tpl_tplset', 'default'), 'AND');
                $criteria->add(new Criteria('tpl_file', $tpl['file']), 'AND');
                $criteria->add(new Criteria('tpl_type', $type), 'AND');
                $tplfiles = $tplfile_handler->getObjects($criteria);

                $tpldata =& xoops_module_gettemplate($dirname, $tpl['file'], $type);
                $tplfile = empty($tplfiles) ? $tplfile_handler->create() : $tplfiles[0];
                // END irmtfan solve templates duplicate issue
                $tplfile->setVar('tpl_refid', $newmid);
                $tplfile->setVar('tpl_lastimported', 0);
                $tplfile->setVar('tpl_lastmodified', time());
                $tplfile->setVar('tpl_type', $type);
                $tplfile->setVar('tpl_source', $tpldata, true);
                $tplfile->setVar('tpl_module', $dirname);
                $tplfile->setVar('tpl_tplset', 'default');
                $tplfile->setVar('tpl_file', $tpl['file'], true);
                $tplfile->setVar('tpl_desc', $tpl['description'], true);
                if (!$tplfile_handler->insert($tplfile)) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                } else {
                    $newid  = $tplfile->getVar('tpl_id');
                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_INSERT_DATA, '<strong>' . $tpl['file'] . '</strong>');
                    if ($xoopsConfig['template_set'] === 'default') {
                        if (!xoops_template_touch($newid)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE_ERROR, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;<span>' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                        }
                    }
                }
                unset($tpldata);
                // irmtfan bug fix: remove codes for delete templates
                /*
                } else {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_AM_SYSTEM_MODULES_TEMPLATE_DELETE_OLD_ERROR, "<strong>".$tpl['file']."</strong>").'</span>';
                }
                */
                // irmtfan bug fix: remove codes for delete templates
            }
        }
        $blocks = $module->getInfo('blocks');
        $msgs[] = _AM_SYSTEM_MODULES_BLOCKS_REBUILD;
        if ($blocks !== false) {
            $showfuncs = array();
            $funcfiles = array();
            foreach ($blocks as $i => $block) {
                if (isset($block['show_func']) && $block['show_func'] != '' && isset($block['file']) && $block['file'] != '') {
                    $editfunc    = isset($block['edit_func']) ? $block['edit_func'] : '';
                    $showfuncs[] = $block['show_func'];
                    $funcfiles[] = $block['file'];
                    $content = '';
                    $template = '';
                    if (isset($block['template']) && trim($block['template']) != '') {
                        $content =& xoops_module_gettemplate($dirname, $block['template'], 'blocks');
                    }
                    if (!$content) {
                        $content = '';
                    } else {
                        $template = $block['template'];
                    }
                    $options = '';
                    if (!empty($block['options'])) {
                        $options = $block['options'];
                    }
                    $sql     = 'SELECT bid, name FROM ' . $xoopsDB->prefix('newblocks') . ' WHERE mid=' . $module->getVar('mid') . ' AND func_num=' . $i . " AND show_func='" . addslashes($block['show_func']) . "' AND func_file='" . addslashes($block['file']) . "'";
                    $fresult = $xoopsDB->query($sql);
                    $fcount  = 0;
                    while (false !== ($fblock = $xoopsDB->fetchArray($fresult))) {
                        ++$fcount;
                        $sql    = 'UPDATE ' . $xoopsDB->prefix('newblocks') . " SET name='" . addslashes($block['name']) . "', edit_func='" . addslashes($editfunc) . "', content='', template='" . $template . "', last_modified=" . time() . ' WHERE bid=' . $fblock['bid'];
                        $result = $xoopsDB->query($sql);
                        if (!$result) {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_UPDATE_ERROR, $fblock['name']);
                        } else {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_UPDATE, $fblock['name']) . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $fblock['bid'] . '</strong>');
                            if ($template != '') {
                                $tplfile = $tplfile_handler->find('default', 'block', $fblock['bid']);
                                if (count($tplfile) == 0) {
                                    $tplfile_new = $tplfile_handler->create();
                                    $tplfile_new->setVar('tpl_module', $dirname);
                                    $tplfile_new->setVar('tpl_refid', $fblock['bid']);
                                    $tplfile_new->setVar('tpl_tplset', 'default');
                                    $tplfile_new->setVar('tpl_file', $block['template'], true);
                                    $tplfile_new->setVar('tpl_type', 'block');
                                } else {
                                    $tplfile_new = $tplfile[0];
                                }
                                $tplfile_new->setVar('tpl_source', $content, true);
                                $tplfile_new->setVar('tpl_desc', $block['description'], true);
                                $tplfile_new->setVar('tpl_lastmodified', time());
                                $tplfile_new->setVar('tpl_lastimported', 0);
                                $tplfile_new->setVar('tpl_file', $block['template'], true); // irmtfan bug fix:  block template file will not updated after update the module
                                if (!$tplfile_handler->insert($tplfile_new)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_UPDATE_ERROR, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_UPDATE, '<strong>' . $block['template'] . '</strong>');
                                    if ($xoopsConfig['template_set'] === 'default') {
                                        if (!xoops_template_touch($tplfile_new->getVar('tpl_id'))) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE_ERROR, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE, '<strong>' . $block['template'] . '</strong>');
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($fcount == 0) {
                        $newbid     = $xoopsDB->genId($xoopsDB->prefix('newblocks') . '_bid_seq');
                        $block_name = addslashes($block['name']);
                        $block_type = ($module->getVar('dirname') === 'system') ? 'S' : 'M';
                        $sql        = 'INSERT INTO ' . $xoopsDB->prefix('newblocks') . ' (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, isactive, dirname, func_file, show_func, edit_func, template, last_modified) VALUES (' . $newbid . ', ' . $module->getVar('mid') . ', ' . $i . ",'" . addslashes($options) . "','" . $block_name . "', '" . $block_name . "', '', 0, 0, 0, '{$block_type}', 1, '" . addslashes($dirname) . "', '" . addslashes($block['file']) . "', '" . addslashes($block['show_func']) . "', '" . addslashes($editfunc) . "', '" . $template . "', " . time() . ')';
                        $result     = $xoopsDB->query($sql);
                        if (!$result) {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_SQL_NOT_CREATE, $block['name']);
                            echo $sql;
                        } else {
                            if (empty($newbid)) {
                                $newbid = $xoopsDB->getInsertId();
                            }
                            if ($module->getInfo('hasMain')) {
                                $groups = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
                            } else {
                                $groups = array(XOOPS_GROUP_ADMIN);
                            }
                            $gperm_handler = xoops_getHandler('groupperm');
                            foreach ($groups as $mygroup) {
                                $bperm = $gperm_handler->create();
                                $bperm->setVar('gperm_groupid', $mygroup);
                                $bperm->setVar('gperm_itemid', $newbid);
                                $bperm->setVar('gperm_name', 'block_read');
                                $bperm->setVar('gperm_modid', 1);
                                if (!$gperm_handler->insert($bperm)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_BLOCK_ACCESS_ERROR . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $newbid . '</strong>') . sprintf(_AM_SYSTEM_MODULES_GROUP_ID, '<strong>' . $mygroup . '</strong>') . '</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_BLOCK_ACCESS . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $newbid . '</strong>') . sprintf(_AM_SYSTEM_MODULES_GROUP_ID, '<strong>' . $mygroup . '</strong>');
                                }
                            }

                            if ($template != '') {
                                $tplfile = $tplfile_handler->create();
                                $tplfile->setVar('tpl_module', $dirname);
                                $tplfile->setVar('tpl_refid', $newbid);
                                $tplfile->setVar('tpl_source', $content, true);
                                $tplfile->setVar('tpl_tplset', 'default');
                                $tplfile->setVar('tpl_file', $block['template'], true);
                                $tplfile->setVar('tpl_type', 'block');
                                $tplfile->setVar('tpl_lastimported', time());
                                $tplfile->setVar('tpl_lastmodified', time());
                                $tplfile->setVar('tpl_desc', $block['description'], true);
                                if (!$tplfile_handler->insert($tplfile)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                } else {
                                    $newid  = $tplfile->getVar('tpl_id');
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_DATA, '<strong>' . $block['template'] . '</strong>');
                                    if ($xoopsConfig['template_set'] === 'default') {
                                        if (!xoops_template_touch($newid)) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE_FAILD, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_RECOMPILE, '<strong>' . $block['template'] . '</strong>');
                                        }
                                    }
                                }
                            }
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_CREATED, '<strong>' . $block['name'] . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $newbid . '</strong>');
                            $sql    = 'INSERT INTO ' . $xoopsDB->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newbid . ', -1)';
                            $xoopsDB->query($sql);
                        }
                    }
                }
            }
            $block_arr = XoopsBlock::getByModule($module->getVar('mid'));
            /* @var $block XoopsBlock */
            foreach ($block_arr as $block) {
                if (!in_array($block->getVar('show_func'), $showfuncs) || !in_array($block->getVar('func_file'), $funcfiles)) {
                    $sql = sprintf('DELETE FROM %s WHERE bid = %u', $xoopsDB->prefix('newblocks'), $block->getVar('bid'));
                    if (!$xoopsDB->query($sql)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DELETE_ERROR, '<strong>' . $block->getVar('name') . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $block->getVar('bid') . '</strong>') . '</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;Block <strong>' . $block->getVar('name') . '</strong> deleted. Block ID: <strong>' . $block->getVar('bid') . '</strong>';
                        if ($block->getVar('template') != '') {
                            $tplfiles = $tplfile_handler->find(null, 'block', $block->getVar('bid'));
                            if (is_array($tplfiles)) {
                                $btcount = count($tplfiles);
                                for ($k = 0; $k < $btcount; $k++) {
                                    if (!$tplfile_handler->delete($tplfiles[$k])) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_BLOCK_DEPRECATED_ERROR . '(ID: <strong>' . $tplfiles[$k]->getVar('tpl_id') . '</strong>)</span>';
                                    } else {
                                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_DEPRECATED, '<strong>' . $tplfiles[$k]->getVar('tpl_file') . '</strong>');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // reset compile_id

        //        $xoTheme  =& $xoopsThemeFactory->createInstance(array('contentTemplate' => @$GLOBALS['xoopsOption']['template_main']));
        //        $xoopsTpl =& $xoTheme->template;
        //        $xoopsTpl->setCompileId();

        $template = $clearTpl;
        $template->setCompileId();
        //        $GLOBALS['xoopsTpl']->setCompileId();
        //        $xoopsTpl->setCompileId();

        // first delete all config entries
        /* @var $config_handler XoopsConfigHandler  */
        $config_handler = xoops_getHandler('config');
        $configs        = $config_handler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
        $confcount      = count($configs);
        $config_delng   = array();
        if ($confcount > 0) {
            $msgs[] = _AM_SYSTEM_MODULES_MODULE_DATA_DELETE;
            for ($i = 0; $i < $confcount; $i++) {
                if (!$config_handler->deleteConfig($configs[$i])) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_CONFIG_DATA_DELETE_ERROR . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, '<strong>' . $configs[$i]->getvar('conf_id') . '</strong>') . '</span>';
                    // save the name of config failed to delete for later use
                    $config_delng[] = $configs[$i]->getvar('conf_name');
                } else {
                    $config_old[$configs[$i]->getvar('conf_name')]['value']     = $configs[$i]->getvar('conf_value', 'N');
                    $config_old[$configs[$i]->getvar('conf_name')]['formtype']  = $configs[$i]->getvar('conf_formtype');
                    $config_old[$configs[$i]->getvar('conf_name')]['valuetype'] = $configs[$i]->getvar('conf_valuetype');
                    $msgs[]                                                     = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_GONFIG_DATA_DELETE . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, '<strong>' . $configs[$i]->getVar('conf_id') . '</strong>');
                }
            }
        }

        // now reinsert them with the new settings
        $configs = $module->getInfo('config');
        if ($configs !== false) {
            if ($module->getVar('hascomments') != 0) {
                include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                array_push($configs, array(
                    'name'        => 'com_rule',
                    'title'       => '_CM_COMRULES',
                    'description' => '',
                    'formtype'    => 'select',
                    'valuetype'   => 'int',
                    'default'     => 1,
                    'options'     => array(
                        '_CM_COMNOCOM'        => XOOPS_COMMENT_APPROVENONE,
                        '_CM_COMAPPROVEALL'   => XOOPS_COMMENT_APPROVEALL,
                        '_CM_COMAPPROVEUSER'  => XOOPS_COMMENT_APPROVEUSER,
                        '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)));
                array_push($configs, array(
                    'name'        => 'com_anonpost',
                    'title'       => '_CM_COMANONPOST',
                    'description' => '',
                    'formtype'    => 'yesno',
                    'valuetype'   => 'int',
                    'default'     => 0));
            }
        } else {
            if ($module->getVar('hascomments') != 0) {
                $configs = array();
                include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                $configs[] = array(
                    'name'        => 'com_rule',
                    'title'       => '_CM_COMRULES',
                    'description' => '',
                    'formtype'    => 'select',
                    'valuetype'   => 'int',
                    'default'     => 1,
                    'options'     => array(
                        '_CM_COMNOCOM'        => XOOPS_COMMENT_APPROVENONE,
                        '_CM_COMAPPROVEALL'   => XOOPS_COMMENT_APPROVEALL,
                        '_CM_COMAPPROVEUSER'  => XOOPS_COMMENT_APPROVEUSER,
                        '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                $configs[] = array(
                    'name'        => 'com_anonpost',
                    'title'       => '_CM_COMANONPOST',
                    'description' => '',
                    'formtype'    => 'yesno',
                    'valuetype'   => 'int',
                    'default'     => 0);
            }
        }
        // RMV-NOTIFY
        if ($module->getVar('hasnotification') != 0) {
            if (empty($configs)) {
                $configs = array();
            }
            // Main notification options
            include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
            include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
            $options                             = array();
            $options['_NOT_CONFIG_DISABLE']      = XOOPS_NOTIFICATION_DISABLE;
            $options['_NOT_CONFIG_ENABLEBLOCK']  = XOOPS_NOTIFICATION_ENABLEBLOCK;
            $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
            $options['_NOT_CONFIG_ENABLEBOTH']   = XOOPS_NOTIFICATION_ENABLEBOTH;

            //$configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLED', 'description' => '_NOT_CONFIG_ENABLEDDSC', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
            $configs[] = array(
                'name'        => 'notification_enabled',
                'title'       => '_NOT_CONFIG_ENABLE',
                'description' => '_NOT_CONFIG_ENABLEDSC',
                'formtype'    => 'select',
                'valuetype'   => 'int',
                'default'     => XOOPS_NOTIFICATION_ENABLEBOTH,
                'options'     => $options);
            // Event specific notification options
            // FIXME: for some reason the default doesn't come up properly
            //  initially is ok, but not when 'update' module..
            $options    = array();
            $categories =& notificationCategoryInfo('', $module->getVar('mid'));
            foreach ($categories as $category) {
                $events =& notificationEvents($category['name'], false, $module->getVar('mid'));
                foreach ($events as $event) {
                    if (!empty($event['invisible'])) {
                        continue;
                    }
                    $option_name           = $category['title'] . ' : ' . $event['title'];
                    $option_value          = $category['name'] . '-' . $event['name'];
                    $options[$option_name] = $option_value;
                    //$configs[] = array ('name' => notificationGenerateConfig($category,$event,'name'), 'title' => notificationGenerateConfig($category,$event,'title_constant'), 'description' => notificationGenerateConfig($category,$event,'description_constant'), 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
                }
            }
            $configs[] = array(
                'name'        => 'notification_events',
                'title'       => '_NOT_CONFIG_EVENTS',
                'description' => '_NOT_CONFIG_EVENTSDSC',
                'formtype'    => 'select_multi',
                'valuetype'   => 'array',
                'default'     => array_values($options),
                'options'     => $options);
        }

        if ($configs !== false) {
            $msgs[]         = 'Adding module config data...';
            /* @var $config_handler XoopsConfigHandler  */
            $config_handler = xoops_getHandler('config');
            $order          = 0;
            foreach ($configs as $config) {
                // only insert ones that have been deleted previously with success
                if (!in_array($config['name'], $config_delng)) {
                    $confobj = $config_handler->createConfig();
                    $confobj->setVar('conf_modid', $newmid);
                    $confobj->setVar('conf_catid', 0);
                    $confobj->setVar('conf_name', $config['name']);
                    $confobj->setVar('conf_title', $config['title'], true);
                    $confobj->setVar('conf_desc', $config['description'], true);
                    $confobj->setVar('conf_formtype', $config['formtype']);
                    if (isset($config['valuetype'])) {
                        $confobj->setVar('conf_valuetype', $config['valuetype']);
                    }
                    if (isset($config_old[$config['name']]['value']) && $config_old[$config['name']]['formtype'] == $config['formtype'] && $config_old[$config['name']]['valuetype'] == $config['valuetype']) {
                        // preserver the old value if any
                        // form type and value type must be the same
                        $confobj->setVar('conf_value', $config_old[$config['name']]['value'], true);
                    } else {
                        $confobj->setConfValueForInput($config['default'], true);

                        //$confobj->setVar('conf_value', $config['default'], true);
                    }
                    $confobj->setVar('conf_order', $order);
                    $confop_msgs = '';
                    if (isset($config['options']) && is_array($config['options'])) {
                        foreach ($config['options'] as $key => $value) {
                            $confop = $config_handler->createConfigOption();
                            $confop->setVar('confop_name', $key, true);
                            $confop->setVar('confop_value', $value, true);
                            $confobj->setConfOptions($confop);
                            $confop_msgs .= '<br>&nbsp;&nbsp;&nbsp;&nbsp; ' . _AM_SYSTEM_MODULES_CONFIG_ADD . _AM_SYSTEM_MODULES_NAME . ' <strong>' . (defined($key) ? constant($key) : $key) . '</strong> ' . _AM_SYSTEM_MODULES_VALUE . ' <strong>' . $value . '</strong> ';
                            unset($confop);
                        }
                    }
                    $order++;
                    if (false !== $config_handler->insertConfig($confobj)) {
                        //$msgs[] = '&nbsp;&nbsp;Config <strong>'.$config['name'].'</strong> added to the database.'.$confop_msgs;
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD, '<strong>' . $config['name'] . '</strong>') . $confop_msgs;
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD_ERROR, '<strong>' . $config['name'] . '</strong>') . '</span>';
                    }
                    unset($confobj);
                }
            }
            unset($configs);
        }

        // execute module specific update script if any
        if (function_exists('xoops_module_update_' . $dirname)) {
            $func = 'xoops_module_update_' . $dirname;
            if (!$func($module, $prev_version)) {
                $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                $msgs = array_merge($msgs, $module->getErrors());
            } else {
                $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, '<strong>' . $func . '</strong>') . '</p>';
                $msgs += $module->getErrors();
            }
        }
        $msgs[] = sprintf(_AM_SYSTEM_MODULES_OKUPD, '<strong>' . $module->getVar('name', 's') . '</strong>');
        $msgs[] = '</div></div>';
        $msgs[] = '<div class="center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a>  | <a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/' . $module->getInfo('adminindex') . '">' . _AM_SYSTEM_MODULES_ADMIN . '</a></div>';
        //        foreach ($msgs as $msg) {
        //            echo $msg . '<br>';
        //        }
    }
    // Call Footer
    //    xoops_cp_footer();
    // Flush cache files for cpanel GUIs
    //    xoops_load("cpanel", "system");
    //    XoopsSystemCpanel::flush();
    //
    //    require_once XOOPS_ROOT_PATH . '/modules/system/class/maintenance.php';
    //    $maintenance = new SystemMaintenance();
    //    $folder      = array(1, 3);
    //    $maintenance->CleanCache($folder);
    //Set active modules in cache folder
    //    xoops_setActiveModules();
    //    break;
    //-----------------------------------------------

    $ret = implode('<br>', $msgs);

    return $ret;
}

/**
 * @param $mid
 *
 * @return string
 */
function xoops_module_activate($mid)
{
    // Get module handler

    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->get($mid);
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    xoops_template_clear_module_cache($module->getVar('mid'));
    // Display header
    $msgs[] = '<div id="xo-module-log">';
    $msgs .= xoops_module_log_header($module, _AM_SYSTEM_MODULES_ACTIVATE);
    // Change value
    $module->setVar('isactive', 1);
    if (!$module_handler->insert($module)) {
        $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILACT, '<strong>' . $module->getVar('name', 's') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>' . $module->getHtmlErrors() . '</p>';
    } else {
        $blocks = XoopsBlock::getByModule($module->getVar('mid'));
        $bcount = count($blocks);
        for ($i = 0; $i < $bcount; ++$i) {
            $blocks[$i]->setVar('isactive', 1);
            $blocks[$i]->store();
        }
        $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_OKACT, '<strong>' . $module->getVar('name', 's') . '</strong>') . '</p></div>';
    }
    //$msgs[] = '</div>';
    $msgs[] = '<div class="center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a></div>';
    $ret    = implode('<br>', $msgs);

    return $ret;
}

/**
 * @param $mid
 *
 * @return string
 */
function xoops_module_deactivate($mid)
{
    global $xoopsConfig;
    // Get module handler

    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->get($mid);
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    xoops_template_clear_module_cache($mid);
    // Display header
    $msgs[] = '<div id="xo-module-log">';
    $msgs .= xoops_module_log_header($module, _AM_SYSTEM_MODULES_DEACTIVATE);
    // Change value
    $module->setVar('isactive', 0);
    if ($module->getVar('dirname') === 'system') {
        $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILDEACT, '<strong>' . $module->getVar('name') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br> - ' . _AM_SYSTEM_MODULES_SYSNO . '</p>';
    } elseif ($module->getVar('dirname') == $xoopsConfig['startpage']) {
        $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILDEACT, '<strong>' . $module->getVar('name') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br> - ' . _AM_SYSTEM_MODULES_STRTNO . '</p>';
    } else {
        if (!$module_handler->insert($module)) {
            $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILDEACT, '<strong>' . $module->getVar('name') . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>' . $module->getHtmlErrors() . '</p>';
        } else {
            $blocks = XoopsBlock::getByModule($module->getVar('mid'));
            $bcount = count($blocks);
            for ($i = 0; $i < $bcount; ++$i) {
                $blocks[$i]->setVar('isactive', 0);
                $blocks[$i]->store();
            }
            $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_OKDEACT, '<strong>' . $module->getVar('name') . '</strong>') . '</p>';
        }
    }
    $msgs[] = '<div class="center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a></div>';
    $ret    = implode('<br>', $msgs);

    return $ret;
}

/**
 * @param $mid
 * @param $name
 *
 * @return string
 */
function xoops_module_change($mid, $name)
{

    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->get($mid);
    $module->setVar('name', $name);
    $myts = MyTextSanitizer::getInstance();
    if (!$module_handler->insert($module)) {
        $ret = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILORDER, '<strong>' . $myts->stripSlashesGPC($name) . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>';
        $ret .= $module->getHtmlErrors() . '</p>';

        return $ret;
    }

    return '<p>' . sprintf(_AM_SYSTEM_MODULES_OKORDER, '<strong>' . $myts->stripSlashesGPC($name) . '</strong>') . '</p>';
}

/**
 * @param $module
 * @param $title
 *
 * @return array
 */
function xoops_module_log_header($module, $title)
{
    $msgs[] = '<div class="header">';
    $msgs[] = $errs[] = '<h4>' . $title . $module->getInfo('name', 's') . '</h4>';
    if ($module->getInfo('image') !== false && trim($module->getInfo('image')) != '') {
        $msgs[] = '<img src="' . XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . trim($module->getInfo('image')) . '" alt="" />';
    }
    $msgs[] = '<strong>' . _VERSION . ':</strong> ' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status');
    if ($module->getInfo('author') !== false && trim($module->getInfo('author')) != '') {
        $msgs[] = '<strong>' . _AUTHOR . ':</strong> ' . htmlspecialchars(trim($module->getInfo('author')));
    }
    $msgs[] = '</div>';

    return $msgs;
}

/**
 * Clean cache 'xoops_data/caches/smarty_cache'
 *
 * @param array|null $cacheList int[] of cache "ids"
 *                              1 = smarty cache
 *                              2 = smarty compile
 *                              3 = xoops cache
 *                              or null to clear all
 * @return bool
 */
function xoops_module_delayed_clean_cache($cacheList = null)
{
    if (empty($cacheList)) {
        $cacheList = array (1,2,3);
    }
    require_once XOOPS_ROOT_PATH . '/modules/system/class/maintenance.php';
    $maintenance = new SystemMaintenance();
    register_shutdown_function(array($maintenance, 'CleanCache'), $cacheList);
}
