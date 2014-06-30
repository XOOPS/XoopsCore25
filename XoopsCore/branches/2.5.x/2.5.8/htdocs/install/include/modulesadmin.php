<?php
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright    (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package     installer
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 * @version     $Id$
 */

defined('XOOPS_INSTALL') or die('XOOPS Installation wizard die');
require_once XOOPS_ROOT_PATH . "/modules/system/admin/modulesadmin/modulesadmin.php";

/*
function xoops_module_install($dirname)
{
    global $xoopsUser, $xoopsConfig;
    $dirname = trim($dirname);
    $db =& $GLOBALS["xoopsDB"];
    $reservedTables = array('avatar', 'avatar_users_link', 'block_module_link', 'xoopscomments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups','groups_users_link','group_permission', 'online', 'bannerclient', 'banner', 'bannerfinish', 'priv_msgs', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'modules', 'tplfile', 'tplset', 'tplsource', 'xoopsnotifications', 'banner', 'bannerclient', 'bannerfinish');
    $module_handler =& xoops_gethandler('module');
    if ($module_handler->getCount(new Criteria('dirname', $dirname)) == 0) {
        $module =& $module_handler->create();
        $module->loadInfoAsVar($dirname);
        $module->setVar('weight', 1);

        $error = false;
        $errs = array();
        $msgs = array();
        $msgs[] = '<h4 style="text-align:left;margin-bottom: 0px;border-bottom: dashed 1px #000000;">'._MD_AM_INSTALLING .$module->getInfo('name').'</h4>';
        if ($module->getInfo('image') != false && trim($module->getInfo('image')) != '') {
            $msgs[] ='<img src="'.XOOPS_URL.'/modules/'.$dirname.'/'.trim($module->getInfo('image')).'" alt="" />';
        }
        $msgs[] ='<strong>'._VERSION.':</strong> '.$module->getInfo('version');
        if ($module->getInfo('author') != false && trim($module->getInfo('author')) != '') {
            $msgs[] ='<strong>'._AUTHOR.':</strong> '.trim($module->getInfo('author'));
        }
        $msgs[] = '';
        $errs[] = '<h4 style="text-align:left;margin-bottom: 0px;border-bottom: dashed 1px #000000;">'._MD_AM_INSTALLING .$module->getInfo('name').'</h4>';

        // Load module specific install script if any
        $install_script = $module->getInfo('onInstall');
        if ($install_script && trim($install_script) != '') {
            include_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($install_script);
        }
        $func = "xoops_module_pre_install_{$dirname}";
        // If pre install function is defined, execute
        if (function_exists($func)) {
            $result = $func($module);
            if ( !$result ) {
                $error = true;
                $errs[] = "<p>" . sprintf(_MD_AM_FAILED_EXECUTE, $func) . "</p>";
                $errs = array_merge($errs, $module->getErrors());
            } else {
                $msgs[] = "<p>" . sprintf(_MD_AM_FAILED_SUCESS, "<strong>{$func}</strong>") . "</p>";
                $msgs += $module->getErrors();
            }
        }

        if ($error == false) {
            $sqlfile = $module->getInfo('sqlfile');
            if (is_array($sqlfile) && !empty($sqlfile[XOOPS_DB_TYPE])) {

                $sql_file_path = XOOPS_ROOT_PATH."/modules/".$dirname."/".$sqlfile[XOOPS_DB_TYPE];
                if (!file_exists($sql_file_path)) {
                    $errs[] = "<p>".sprintf(_MD_AM_SQL_NOT_FOUND, "<strong>{$sql_file_path}</strong>");
                    $error = true;
                } else {
                    $msgs[] = "<p>".sprintf(_MD_AM_SQL_FOUND, "<strong>{$sql_file_path}</strong>")."<br  />" ._MD_AM_CREATE_TABLES;
                    include_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';
                    $sql_query = fread(fopen($sql_file_path, 'r'), filesize($sql_file_path));
                    $sql_query = trim($sql_query);
                    SqlUtility::splitMySqlFile($pieces, $sql_query);
                    $created_tables = array();
                    foreach ($pieces as $piece) {
                        // [0] contains the prefixed query
                        // [4] contains unprefixed table name
                        $prefixed_query = SqlUtility::prefixQuery($piece, $db->prefix());
                        if (!$prefixed_query) {
                            $errs[] ="<p>".sprintf(_MD_AM_SQL_NOT_VALID, "<strong>".$piece."</strong>");
                            $error = true;
                            break;
                        }
                        // check if the table name is reserved
                        if (!in_array($prefixed_query[4], $reservedTables)) {
                            // not reserved, so try to create one
                            if (!$db->query($prefixed_query[0])) {
                                $errs[] = $db->error();
                                $error = true;
                                break;
                            } else {

                                if (!in_array($prefixed_query[4], $created_tables)) {
                                    $msgs[] = "&nbsp;&nbsp;" . sprintf(_MD_AM_TABLE_CREATED, "<strong>" . $db->prefix($prefixed_query[4]) . "</strong>");
                                    $created_tables[] = $prefixed_query[4];
                                } else {
                                    $msgs[] = "&nbsp;&nbsp;" . sprintf(_MD_AM_INSERT_DATA, "<strong>" . $db->prefix($prefixed_query[4]) . "</strong>");
                                }
                            }
                        } else {
                            // the table name is reserved, so halt the installation
                            $errs[] = "&nbsp;&nbsp;" . sprintf(_MD_AM_TABLE_RESERVED, "<strong>" . $prefixed_query[4] . "</strong>");
                            $error = true;
                            break;
                        }
                    }
                    // if there was an error, delete the tables created so far, so the next installation will not fail
                    if ($error == true) {
                        foreach ($created_tables as $ct) {
                            $db->query("DROP TABLE " . $db->prefix($ct));
                        }
                    }
                }
            }
        }
        // if no error, save the module info and blocks info associated with it
        if ($error == false) {
            if (!$module_handler->insert($module)) {
                $errs[] = "<p>".sprintf(_MD_AM_INSERT_DATA_FAILD, "<strong>".$module->getVar('name')."</strong>");
                foreach ($created_tables as $ct) {
                    $db->query("DROP TABLE ".$db->prefix($ct));
                }
                $ret = "<p>".sprintf(_MD_AM_FAILINS, "<strong>".$module->name()."</strong>")."&nbsp;"._MD_AM_ERRORSC."<br />";
                foreach ( $errs as $err ) {
                    $ret .= " - ".$err."<br />";
                }
                $ret .= "</p>";
                unset($module);
                unset($created_tables);
                unset($errs);
                unset($msgs);
                return $ret;
            } else {
                $newmid = $module->getVar('mid');
                unset($created_tables);
                $msgs[] = "<p>"._MD_AM_INSERT_DATA_DONE. sprintf(_MD_AM_MODULEID, "<strong>".$newmid."</strong>");
                $tplfile_handler =& xoops_gethandler('tplfile');
                $templates = $module->getInfo('templates');
                if ($templates != false) {
                    $msgs[] = _MD_AM_TEMPLATES_ADD;
                    foreach ($templates as $tpl) {
                        $tplfile =& $tplfile_handler->create();
                        $tpldata =& xoops_module_gettemplate($dirname, $tpl['file']);
                        $tplfile->setVar('tpl_source', $tpldata, true);
                        $tplfile->setVar('tpl_refid', $newmid);

                        $tplfile->setVar('tpl_tplset', 'default');
                        $tplfile->setVar('tpl_file', $tpl['file']);
                        $tplfile->setVar('tpl_desc', $tpl['description'], true);
                        $tplfile->setVar('tpl_module', $dirname);
                        $tplfile->setVar('tpl_lastmodified', time());
                        $tplfile->setVar('tpl_lastimported', 0);
                        $tplfile->setVar('tpl_type', 'module');
                        if (!$tplfile_handler->insert($tplfile)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_TEMPLATE_ADD_ERROR, "<strong>".$tpl['file']."</strong>")."</span>";
                        } else {
                            $newtplid = $tplfile->getVar('tpl_id');
                            $msgs[] = "&nbsp;&nbsp;".sprintf(_MD_AM_TEMPLATE_ADD_DATA, "<strong>".$tpl['file']."</strong>")."(ID: <strong>".$newtplid."</strong>)";
                            // generate compiled file
                            include_once XOOPS_ROOT_PATH.'/class/template.php';
                            if (!xoops_template_touch($newtplid)) {
                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_TEMPLATE_COMPILED_FAILED, "<strong>".$tpl['file']."</strong>")."</span>";
                            } else {
                                $msgs[] = "&nbsp;&nbsp;".sprintf(_MD_AM_TEMPLATE_COMPILED, "<strong>".$tpl['file']."</strong>");
                            }
                        }
                        unset($tpldata);
                    }
                }
                include_once XOOPS_ROOT_PATH.'/class/template.php';
                xoops_template_clear_module_cache($newmid);
                $blocks = $module->getInfo('blocks');
                if ($blocks != false) {
                    $msgs[] = _MD_AM_BLOCKS_ADD;
                    foreach ($blocks as $blockkey => $block) {
                        // break the loop if missing block config
                        if (!isset($block['file']) || !isset($block['show_func'])) {
                            break;
                        }
                        $options = '';
                        if (!empty($block['options'])) {
                            $options = trim($block['options']);
                        }
                        $newbid = $db->genId($db->prefix('newblocks').'_bid_seq');
                        $edit_func = isset($block['edit_func']) ? trim($block['edit_func']) : '';
                        $template = '';
                        if ((isset($block['template']) && trim($block['template']) != '')) {
                            $content =& xoops_module_gettemplate($dirname, $block['template'], true);
                        }
                        if (empty($content)) {
                            $content = '';
                        } else {
                            $template = trim($block['template']);
                        }
                        $block_name = addslashes(trim($block['name']));
                        $sql = "INSERT INTO ".$db->prefix("newblocks")." (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified) VALUES ($newbid, $newmid, ".intval($blockkey).", '$options', '".$block_name."','".$block_name."', '', 0, 0, 0, 'M', 'H', 1, '".addslashes($dirname)."', '".addslashes(trim($block['file']))."', '".addslashes(trim($block['show_func']))."', '".addslashes($edit_func)."', '".$template."', 0, ".time().")";
                        if (!$db->query($sql)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_BLOCK_ADD_ERROR, "<strong>".$block['name']."</strong>").sprintf(_MD_AM_BLOCK_ADD_ERROR_DATABASE, "<strong>".$db->error()."</strong>")."</span>";
                        } else {
                            if (empty($newbid)) {
                                $newbid = $db->getInsertId();
                            }
                            $msgs[] = "&nbsp;&nbsp;".sprintf(_MD_AM_BLOCK_ADD, "<strong>".$block['name']."</strong>").sprintf(_MD_AM_BLOCK_ID, "<strong>".$newbid."</strong>");
                            $sql = 'INSERT INTO '.$db->prefix('block_module_link').' (block_id, module_id) VALUES ('.$newbid.', -1)';
                            $db->query($sql);
                            if ($template != '') {
                                $tplfile =& $tplfile_handler->create();
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
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_TEMPLATE_ADD_ERROR, "<strong>".$block['template']."</strong>")."</span>";
                                } else {
                                    $newtplid = $tplfile->getVar('tpl_id');
                                    $msgs[] = "&nbsp;&nbsp;".sprintf(_MD_AM_TEMPLATE_ADD_DATA, "<strong>".$block['template']."</strong>")." (ID: <strong>".$newtplid."</strong>)";
                                    // generate compiled file
                                    include_once XOOPS_ROOT_PATH.'/class/template.php';
                                    if (!xoops_template_touch($newtplid)) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_TEMPLATE_COMPILED_FAILED, "<strong>".$block['template']."</strong>")."</span>";

                                    } else {
                                        $msgs[] = "&nbsp;&nbsp;".sprintf(_MD_AM_TEMPLATE_COMPILED, "<strong>".$block['template']."</strong>");
                                    }
                                }
                            }
                        }
                        unset($content);
                    }
                    unset($blocks);
                }
                $configs = $module->getInfo('config');
                if ($configs != false) {
                    if ($module->getVar('hascomments') != 0) {
                        include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
                        array_push($configs, array('name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)));
                        array_push($configs, array('name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0));
                    }
                } else {
                    if ($module->getVar('hascomments') != 0) {
                        $configs = array();
                        include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
                        $configs[] = array('name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                        $configs[] = array('name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0);
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
                    $options = array();
                    $options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
                    $options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
                    $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
                    $options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;

                    $configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLE', 'description' => '_NOT_CONFIG_ENABLEDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => XOOPS_NOTIFICATION_ENABLEBOTH, 'options' => $options);
                    // Event-specific notification options
                    // FIXME: doesn't work when update module... can't read back the array of options properly...  " changing to &quot;
                    $options = array();
                    $categories =& notificationCategoryInfo('',$module->getVar('mid'));
                    foreach ($categories as $category) {
                        $events =& notificationEvents ($category['name'], false, $module->getVar('mid'));
                        foreach ($events as $event) {
                            if (!empty($event['invisible'])) {
                                continue;
                            }
                            $option_name = $category['title'] . ' : ' . $event['title'];
                            $option_value = $category['name'] . '-' . $event['name'];
                            $options[$option_name] = $option_value;
                        }
                    }
                    $configs[] = array ('name' => 'notification_events', 'title' => '_NOT_CONFIG_EVENTS', 'description' => '_NOT_CONFIG_EVENTSDSC', 'formtype' => 'select_multi', 'valuetype' => 'array', 'default' => array_values($options), 'options' => $options);
                }

                if ($configs != false) {
                    $msgs[] = _MD_AM_MODULE_DATA_ADD;
                    $config_handler =& xoops_gethandler('config');
                    $order = 0;
                    foreach ($configs as $config) {
                        $confobj =& $config_handler->createConfig();
                        $confobj->setVar('conf_modid', $newmid);
                        $confobj->setVar('conf_catid', 0);
                        $confobj->setVar('conf_name', $config['name']);
                        $confobj->setVar('conf_title', $config['title'], true);
                        $confobj->setVar('conf_desc', $config['description'], true);
                        $confobj->setVar('conf_formtype', $config['formtype']);
                        $confobj->setVar('conf_valuetype', $config['valuetype']);
                        $confobj->setConfValueForInput($config['default'], true);
                        $confobj->setVar('conf_order', $order);
                        $confop_msgs = '';
                        if (isset($config['options']) && is_array($config['options'])) {
                            foreach ($config['options'] as $key => $value) {
                                $confop =& $config_handler->createConfigOption();
                                $confop->setVar('confop_name', $key, true);
                                $confop->setVar('confop_value', $value, true);
                                $confobj->setConfOptions($confop);
                                $confop_msgs .= '<br />&nbsp;&nbsp;&nbsp;&nbsp; '._MD_AM_CONFIG_ADD. _MD_AM_NAME .' <strong>'.$key.'</strong> '. _MD_AM_VALUE.' <strong>'.$value.'</strong> ';
                                unset($confop);
                            }
                        }
                        $order++;
                        if ($config_handler->insertConfig($confobj) != false) {
                            $msgs[] = '&nbsp;&nbsp;'.sprintf(_MD_AM_CONFIG_DATA_ADD, "<strong>".$config['name']."</strong>").$confop_msgs;

                        } else {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_CONFIG_DATA_ADD_ERROR, "<strong>".$config['name']."</strong>")."</span>";
                        }
                        unset($confobj);
                    }
                    unset($configs);
                }
            }
            if ($module->getInfo('hasMain')) {
                $groups = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
            } else {
                $groups = array(XOOPS_GROUP_ADMIN);
            }
            // retrieve all block ids for this module
            $blocks = XoopsBlock::getByModule($newmid, false);
            $msgs[] = _MD_AM_GROUP_SETTINGS_ADD;
            $gperm_handler =& xoops_gethandler('groupperm');
            foreach ($groups as $mygroup) {
                if ($gperm_handler->checkRight('module_admin', 0, $mygroup)) {
                    $mperm =& $gperm_handler->create();
                    $mperm->setVar('gperm_groupid', $mygroup);
                    $mperm->setVar('gperm_itemid', $newmid);
                    $mperm->setVar('gperm_name', 'module_admin');
                    $mperm->setVar('gperm_modid', 1);
                    if (!$gperm_handler->insert($mperm)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_ACCESS_ADMIN_ADD_ERROR, "<strong>".$mygroup."</strong>")."</span>";
                    } else {
                        $msgs[] = "&nbsp;&nbsp;".sprintf(_MD_AM_ACCESS_ADMIN_ADD, "<strong>".$mygroup."</strong>");
                    }
                    unset($mperm);
                }
                $mperm =& $gperm_handler->create();
                $mperm->setVar('gperm_groupid', $mygroup);
                $mperm->setVar('gperm_itemid', $newmid);
                $mperm->setVar('gperm_name', 'module_read');
                $mperm->setVar('gperm_modid', 1);
                if (!$gperm_handler->insert($mperm)) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(_MD_AM_ACCESS_USER_ADD_ERROR, "<strong>".$mygroup."</strong>")."</span>";
                } else {
                    $msgs[] = '&nbsp;&nbsp;'.sprintf(_MD_AM_ACCESS_USER_ADD_ERROR, "<strong>".$mygroup."</strong>");
                }
                unset($mperm);
                foreach ($blocks as $blc) {
                    $bperm =& $gperm_handler->create();
                    $bperm->setVar('gperm_groupid', $mygroup);
                    $bperm->setVar('gperm_itemid', $blc);
                    $bperm->setVar('gperm_name', 'block_read');
                    $bperm->setVar('gperm_modid', 1);
                    if (!$gperm_handler->insert($bperm)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'._MD_AM_BLOCK_ACCESS_ERROR.' Block ID: <strong>'.$blc.'</strong> Group ID: <strong>'.$mygroup.'</strong></span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;'._MD_AM_BLOCK_ACCESS. sprintf(_MD_AM_BLOCK_ID, "<strong>".$blc."</strong>") .sprintf(_MD_AM_GROUP_ID, "<strong>".$mygroup."</strong>");
                    }
                    unset($bperm);
                }
            }
            unset($blocks);
            unset($groups);

            // execute module specific install script if any
            $func = "xoops_module_install_{$dirname}";
            if (function_exists($func)) {
                if ( !$lastmsg = $func($module) ) {
                    $msgs[] = "<p>" . sprintf(_MD_AM_FAILED_EXECUTE, $func) . "</p>";
                } else {
                    $msgs[] = "<p>" . sprintf(_MD_AM_FAILED_SUCESS, "<strong>{$func}</strong>") . "</p>";
                    if ( is_string( $lastmsg ) ) {
                        $msgs[] = $lastmsg;
                    }
                }
            }

            $ret = '<div>' . implode("<br />", $msgs) . '</div><br />' . sprintf(_MD_AM_OKINS, "<strong>" . $module->getVar('name') . "</strong>");
            unset($msgs);
            unset($errs);
            unset($module);
            return $ret;
        } else {
            $ret = '<p>' . sprintf(_MD_AM_FAILINS, '<strong>' . $dirname . '</strong>') . '&nbsp;' . _MD_AM_ERRORSC . '<br />' . implode("<br />", $errs) . '</p>';
            unset($msgs);
            unset($errs);
            return $ret;
        }
    } else {
        return "<p>".sprintf(_MD_AM_FAILINS, "<strong>".$dirname."</strong>")."&nbsp;"._MD_AM_ERRORSC."<br />&nbsp;&nbsp;".sprintf(_MD_AM_ALEXISTS, $dirname)."</p>";
    }
}



function &xoops_module_gettemplate($dirname, $template, $block = false)
{
    global $xoopsConfig;
    $ret = '';
    if ($block) {
        $path = XOOPS_ROOT_PATH.'/modules/'.$dirname.'/templates/blocks/'.$template;
    } else {
        $path = XOOPS_ROOT_PATH.'/modules/'.$dirname.'/templates/'.$template;
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
    for ($i = 0; $i < $count; $i++) {
        $ret .= str_replace("\n", "\r\n", str_replace("\r\n", "\n", $lines[$i]));
    }
    return $ret;
}
*/
