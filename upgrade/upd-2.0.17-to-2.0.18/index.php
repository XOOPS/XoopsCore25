<?php

/**
 * Class upgrade_2018
 */
class Upgrade_2018 extends XoopsUpgrade
{
    /**
     * @return bool
     */
    public function isApplied()
    {
        return $this->check_config_type();
    }

    /**
     * @return bool
     */
    public function apply()
    {
        return $this->apply_alter_tables();
    }

    /**
     * @return bool
     */
    public function check_config_type()
    {
        $db     = $GLOBALS['xoopsDB'];
        $sql    = 'SHOW COLUMNS FROM ' . $db->prefix('config') . " LIKE 'conf_title'";
        $result = $db->queryF($sql);
        while ($row = $db->fetchArray($result)) {
            if (strtolower(trim($row['Type'])) === 'varchar(255)') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $sql
     */
    public function query($sql)
    {
        //echo $sql . "<br />";
        $db = $GLOBALS['xoopsDB'];
        if (!($ret = $db->queryF($sql))) {
            echo $db->error();
        }
    }

    /**
     * @return bool
     */
    public function apply_alter_tables()
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
}

$upg = new Upgrade_2018();
return $upg;
