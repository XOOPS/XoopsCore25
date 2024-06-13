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
        /** @var XoopsModule $module */
        // translations on module update

        global $msgs; // TODO :-D

        if (!is_array($msgs)) {
            $msgs = [];
        }

        /** @var XoopsMySQLDatabase $db */
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $mid = $module->getVar('mid');

        // TABLES (write here ALTER TABLE etc. if necessary)

        // configs (Though I know it is not a recommended way...)
        $sql = 'SHOW COLUMNS FROM ' . $db->prefix('config') . " LIKE 'conf_title'";
        $result = $db->query($sql);
        if ($result !== false && $db->isResultSet($result)) {
            if ($result instanceof mysqli_result && ($myrow = $db->fetchArray($result)) && isset($myrow['Type']) && $myrow['Type'] === 'varchar(30)') {
                $db->queryF('ALTER TABLE ' . $db->prefix('config') . " MODIFY `conf_title` varchar(255) NOT NULL default '', MODIFY `conf_desc` varchar(255) NOT NULL default ''");
            }
        }

        $sql = 'SHOW CREATE TABLE ' . $db->prefix('config');
        $result = $db->query($sql);
        if (false === $result || !($result instanceof mysqli_result) || !$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
                E_USER_ERROR,
            );
        } else {
            [, $create_string] = $db->fetchRow($result);
        }


        foreach (explode('KEY', $create_string) as $line) {
            if (preg_match('/(\`conf\_title_\d+\`) \(\`conf\_title\`\)/', $line, $regs)) {
                $db->query('ALTER TABLE ' . $db->prefix('config') . ' DROP KEY ' . $regs[1]);
            }
        }
        $db->query('ALTER TABLE ' . $db->prefix('config') . ' ADD KEY `conf_title` (`conf_title`)');

        // 2.x -> 3.0
        $sql = 'SHOW CREATE TABLE ' . $db->prefix($mydirname . '_log');
        $result = $db->query($sql);

        if (false === $result || !($result instanceof mysqli_result) || !$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
                E_USER_ERROR,
            );
        } else {
            [, $create_string] = $db->fetchRow($result);
        }


        if (preg_match('/timestamp\(/i', $create_string)) {
            $db->query('ALTER TABLE ' . $db->prefix($mydirname . '_log') . ' MODIFY `timestamp` DATETIME');
        }

        // TEMPLATES (all templates have been already removed by modulesadmin)
        /** @var XoopsTplfileHandler $tplfile_handler */
        $tplfile_handler = xoops_getHandler('tplfile');
        $tpl_path = __DIR__ . '/templates';
        // Check if the directory exists
        if (is_dir($tpl_path) && is_readable($tpl_path)) {
            // Try to open the directory
            if ($handler = opendir($tpl_path . '/')) {
                while (($file = readdir($handler)) !== false) {
                    if (substr($file, 0, 1) === '.') {
                        continue;
                    }
                    $file_path = $tpl_path . '/' . $file;
                    if (is_file($file_path) && in_array(strrchr($file, '.'), ['.html', '.css', '.js'])) {
                        $mtime   = (int) (@filemtime($file_path));
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
                            $ret[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b> to the database.</span><br>';
                        } else {
                            $tplid  = $tplfile->getVar('tpl_id');
                            $msgs[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)<br>';
                            // generate compiled file
                            include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
                            include_once XOOPS_ROOT_PATH . '/class/template.php';
                            if (!xoops_template_touch((string) $tplid)) {
                                $msgs[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b>.</span><br>';
                            } else {
                                $msgs[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file, ENT_QUOTES | ENT_HTML5) . '</b> compiled.</span><br>';
                            }
                        }
                    }
                }
                closedir($handler);
            } else {
                // Handle the error condition when opendir fails
                $msgs[] = '<span style="color:#ff0000;">ERROR: Could not open the template directory:  <b>' . htmlspecialchars($tpl_path) . '</b>.</span>';
            }
        } else {
            // Directory does not exist; handle this condition
            $msgs[] = '<span style="color:#ff0000;">ERROR: The template directory does not exist or is not readable: <b>' . htmlspecialchars($tpl_path) . '</b>.</span><br>';
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
    function protector_message_append_onupdate(&$module_obj, $log)
    {
        if (isset($GLOBALS['msgs']) && is_array($GLOBALS['msgs'])) {
            foreach ($GLOBALS['msgs'] as $message) {
                $log->add(strip_tags($message));
            }
        }

        // use mLog->addWarning() or mLog->addError() if necessary
    }
}
