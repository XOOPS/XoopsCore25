<?php

/**
 * Class upgrade_2018
 */
class Upgrade_2018 extends XoopsUpgrade
{
    /**
     * @return bool
     */
    public function check_config_type()
    {
        $db     = $GLOBALS['xoopsDB'];
        $sql    = 'SHOW COLUMNS FROM ' . $db->prefix('config') . " LIKE 'conf_title'";
        $result = $db->queryF($sql);
        if (!$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
            );
        }
        while (false !== ($row = $db->fetchArray($result))) {
            if (strtolower(trim($row['Type'])) === 'varchar(255)') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $sql
     */
    protected function query($sql)
    {
        //echo $sql . "<br>";
        $db = $GLOBALS['xoopsDB'];
        if (!($ret = $db->queryF($sql))) {
            echo $db->error();
        }
    }

    /**
     * @return bool
     */
    public function apply_config_type()
    {
        $db           = $GLOBALS['xoopsDB'];
        $this->fields = array(
            'config' => array(
                'conf_title' => "varchar(255) NOT NULL default ''",
                'conf_desc' => "varchar(255) NOT NULL default ''"),
            'configcategory' => array('confcat_name' => "varchar(255) NOT NULL default ''"));

        foreach ($this->fields as $table => $data) {
            foreach ($data as $field => $property) {
                $sql = 'ALTER TABLE ' . $db->prefix($table) . " CHANGE `$field` `$field` $property";
                $this->query($sql);
            }
        }

        return true;
    }

    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('config_type');
    }
}

$upg = new Upgrade_2018();
return $upg;
