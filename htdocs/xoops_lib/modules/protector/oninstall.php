<?php

use XoopsModules\Protector;
use XoopsModules\Protector\Registry;

require_once __DIR__ . '/preloads/autoloader.php';

// start hack by Trabis
if (!class_exists('XoopsModules\Protector\Registry')) {
    exit('Registry not found');
}

$registry  = Registry::getInstance();
$mydirname = $registry->getEntry('mydirname');
$mydirpath = $registry->getEntry('mydirpath');
$language  = $registry->getEntry('language');
// end hack by Trabis

eval(' function xoops_module_install_' . $mydirname . '( $module ) { return protector_oninstall_base( $module , "' . $mydirname . '" );} ');

if (!function_exists('protector_oninstall_base')) {

    /**
     * @param XoopsModule $module
     * @param string      $mydirname
     *
     * @return bool
     */
    function protector_oninstall_base(XoopsModule $module, $mydirname)
    {
        /** @var \XoopsModule $module */
        // translations on module install

        global $ret; // TODO :-D

        // for Cube 2.1
//        if (defined('XOOPS_CUBE_LEGACY')) {
//            $root =& XCube_Root::getSingleton();
//            $root->mDelegateManager->add('Legacy.Admin.Event.ModuleInstall.' . ucfirst($mydirname) . '.Success', 'protector_message_append_oninstall');
//            $ret = array();
//        } else {
            if (!is_array($ret)) {
                $ret = array();
            }
//        }

        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $mid = (int)$module->getVar('mid');

        // TABLES (loading mysql.sql)
        $sql_file_path = __DIR__ . '/sql/mysql.sql';
        $prefix_mod    = $db->prefix() . '_' . $mydirname;
        if (is_file($sql_file_path)) {
            $ret[] = 'SQL file found at <b>' . htmlspecialchars($sql_file_path, ENT_QUOTES | ENT_HTML5) . '</b>.<br> Creating tables...';

//            if (is_file(XOOPS_ROOT_PATH . '/class/database/oldsqlutility.php')) {
//                require_once XOOPS_ROOT_PATH . '/class/database/oldsqlutility.php';
//                $sqlutil = new OldSqlUtility(); //old code is -> $sqlutil = new OldSqlUtility ; //hack by Trabis
//            } else {
                require_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
                $sqlutil = new SqlUtility(); //old code is -> $sqlutil = new SqlUtility ; //hack by Trabis
//            }

            $sql_query = trim((string)file_get_contents($sql_file_path));
            $pieces    = array();
            $sqlutil::splitMySqlFile($pieces, $sql_query);
            $created_tables = array();
            foreach ($pieces as $piece) {
                $prefixed_query = $sqlutil::prefixQuery($piece, $prefix_mod);
                if (!$prefixed_query) {
                    $ret[] = 'Invalid SQL <b>' . htmlspecialchars($piece, ENT_QUOTES | ENT_HTML5) . '</b><br>';

                    return false;
                }
                if (!$db->query($prefixed_query[0])) {
                    $ret[] = '<b>' . htmlspecialchars($db->error(), ENT_QUOTES | ENT_HTML5) . '</b><br>';

                    //var_dump( $db->error() ) ;
                    return false;
                } else {
                    if (!in_array($prefixed_query[4], $created_tables)) {
                        $ret[]            = 'Table <b>' . htmlspecialchars($prefix_mod . '_' . $prefixed_query[4], ENT_QUOTES | ENT_HTML5) . '</b> created.<br>';
                        $created_tables[] = $prefixed_query[4];
                    } else {
                        $ret[] = 'Data inserted to table <b>' . htmlspecialchars($prefix_mod . '_' . $prefixed_query[4], ENT_QUOTES | ENT_HTML5) . '</b>.</br>';
                    }
                }
            }
        }

        // TEMPLATES
        /** @var \XoopsTplfileHandler $tplfileHandler */
        $tplfileHandler = xoops_getHandler('tplfile');
        $tpl_path        = __DIR__ . '/templates';
        if ($handler = @opendir($tpl_path . '/')) {
            while (false !== ($file = readdir($handler))) {
                if ('.' === substr($file, 0, 1)) {
                    continue;
                }
                $file_path = $tpl_path . '/' . $file;
                if (is_file($file_path)
                    && in_array(strrchr($file, '.'), array(
                        '.html',
                        '.css',
                        '.js',
                    ))) {
                    $mtime   = (int)(@filemtime($file_path));
                    $tplfile = $tplfileHandler->create();
                    $tplfile->setVar('tpl_source', file_get_contents($file_path), true);
                    $tplfile->setVar('tpl_refid', $mid);
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $mydirname . '_' . $file);
                    $tplfile->setVar('tpl_desc', '', true);
                    $tplfile->setVar('tpl_module', $mydirname);
                    $tplfile->setVar('tpl_lastmodified', $mtime);
                    $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_type', 'module');
                    if (!$tplfileHandler->insert($tplfile)) {
                        $ret[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b> to the database.</span><br>';
                    } else {
                        $tplid = $tplfile->getVar('tpl_id');
                        $ret[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)<br>';
                        // generate compiled file
                        require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
                        require_once XOOPS_ROOT_PATH . '/class/template.php';
                        if (!xoops_template_touch((int)$tplid)) {
                            $ret[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b>.</span><br>';
                        } else {
                            $ret[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b> compiled.</span><br>';
                        }
                    }
                }
            }
            closedir($handler);
        }
        require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        xoops_template_clear_module_cache($mid);

        return true;
    }

//    /**
//     * @param \XoopsModule $module_obj
//     * @param object $log
//     * @return void
//     */
//    function protector_message_append_oninstall(&$module_obj, &$log)
//    {
//        if (is_array(@$GLOBALS['ret'])) {
//            foreach ($GLOBALS['ret'] as $message) {
//                $log->add(strip_tags($message));
//            }
//        }
//
//        // use mLog->addWarning() or mLog->addError() if necessary
//    }
}
