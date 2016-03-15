<?php
// start hack by Trabis
if (!class_exists('ProtectorRegistry')) {
    exit('Registry not found');
}

$registry  = ProtectorRegistry::getInstance();
$mydirname = $registry->getEntry('mydirname');
$mydirpath = $registry->getEntry('mydirpath');
$language  = $registry->getEntry('language');
// end hack by Trabis

eval(' function xoops_module_update_' . $mydirname . '( $module ) { return protector_onupdate_base( $module , "' . $mydirname . '" ) ; } ');

if (!function_exists('protector_onupdate_base')) {

    /**
     * @param $module
     * @param $mydirname
     *
     * @return bool
     */
    function protector_onupdate_base($module, $mydirname)
    {
        // transations on module update

        global $msgs; // TODO :-D

        // for Cube 2.1
        if (defined('XOOPS_CUBE_LEGACY')) {
            $root =& XCube_Root::getSingleton();
            $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'protector_message_append_onupdate');
            $msgs = array();
        } else {
            if (!is_array($msgs)) {
                $msgs = array();
            }
        }

        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $mid = $module->getVar('mid');

        // TABLES (write here ALTER TABLE etc. if necessary)

        // configs (Though I know it is not a recommended way...)
        $check_sql = 'SHOW COLUMNS FROM ' . $db->prefix('config') . " LIKE 'conf_title'";
        if (($result = $db->query($check_sql)) && ($myrow = $db->fetchArray($result)) && @$myrow['Type'] === 'varchar(30)') {
            $db->queryF('ALTER TABLE ' . $db->prefix('config') . " MODIFY `conf_title` varchar(255) NOT NULL default '', MODIFY `conf_desc` varchar(255) NOT NULL default ''");
        }
        list(, $create_string) = $db->fetchRow($db->query('SHOW CREATE TABLE ' . $db->prefix('config')));
        foreach (explode('KEY', $create_string) as $line) {
            if (preg_match('/(\`conf\_title_\d+\`) \(\`conf\_title\`\)/', $line, $regs)) {
                $db->query('ALTER TABLE ' . $db->prefix('config') . ' DROP KEY ' . $regs[1]);
            }
        }
        $db->query('ALTER TABLE ' . $db->prefix('config') . ' ADD KEY `conf_title` (`conf_title`)');

        // 2.x -> 3.0
        list(, $create_string) = $db->fetchRow($db->query('SHOW CREATE TABLE ' . $db->prefix($mydirname . '_log')));
        if (preg_match('/timestamp\(/i', $create_string)) {
            $db->query('ALTER TABLE ' . $db->prefix($mydirname . '_log') . ' MODIFY `timestamp` DATETIME');
        }

        // TEMPLATES (all templates have been already removed by modulesadmin)
        $tplfile_handler = xoops_getHandler('tplfile');
        $tpl_path        = __DIR__ . '/templates';
        if ($handler = @opendir($tpl_path . '/')) {
            while (($file = readdir($handler)) !== false) {
                if (substr($file, 0, 1) === '.') {
                    continue;
                }
                $file_path = $tpl_path . '/' . $file;
                if (is_file($file_path) && in_array(strrchr($file, '.'), array('.html', '.css', '.js'))) {
                    $mtime   = (int)(@filemtime($file_path));
                    $tplfile = $tplfile_handler->create();
                    $tplfile->setVar('tpl_source', file_get_contents($file_path), true);
                    $tplfile->setVar('tpl_refid', $mid);
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $mydirname . '_' . $file);
                    $tplfile->setVar('tpl_desc', '', true);
                    $tplfile->setVar('tpl_module', $mydirname);
                    $tplfile->setVar('tpl_lastmodified', $mtime);
                    $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_type', 'module');
                    if (!$tplfile_handler->insert($tplfile)) {
                        $msgs[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b> to the database.</span>';
                    } else {
                        $tplid  = $tplfile->getVar('tpl_id');
                        $msgs[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)';
                        // generate compiled file
                        include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
                        include_once XOOPS_ROOT_PATH . '/class/template.php';
                        if (!xoops_template_touch($tplid)) {
                            $msgs[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b>.</span>';
                        } else {
                            $msgs[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b> compiled.</span>';
                        }
                    }
                }
            }
            closedir($handler);
        }
        include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
        include_once XOOPS_ROOT_PATH . '/class/template.php';
        xoops_template_clear_module_cache($mid);

        return true;
    }

    /**
     * @param $module_obj
     * @param $log
     */
    function protector_message_append_onupdate(&$module_obj, &$log)
    {
        if (is_array(@$GLOBALS['msgs'])) {
            foreach ($GLOBALS['msgs'] as $message) {
                $log->add(strip_tags($message));
            }
        }

        // use mLog->addWarning() or mLog->addError() if necessary
    }
}
