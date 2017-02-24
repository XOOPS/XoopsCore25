<?php

/**
 * Class upgrade_2017
 */
class Upgrade_2017 extends XoopsUpgrade
{
    /**
     * @return bool
     */
    public function check_auth_db()
    {
        $db    = $GLOBALS['xoopsDB'];
        $value = $this->getDbValue($db, 'config', 'conf_id', "`conf_name` = 'ldap_use_TLS' AND `conf_catid` = " . XOOPS_CONF_AUTH);

        return (bool)$value;
    }

    /**
     * @param $sql
     */
    protected function query($sql)
    {
        $db = $GLOBALS['xoopsDB'];
        if (!($ret = $db->queryF($sql))) {
            echo $db->error();
        }
    }

    /**
     * @return bool
     */
    public function apply_auth_db()
    {
        $db = $GLOBALS['xoopsDB'];

        // Insert config values
        $table = $db->prefix('config');
        $data  = array(
            'ldap_use_TLS' => "'_MD_AM_LDAP_USETLS', '0', '_MD_AM_LDAP_USETLS_DESC', 'yesno', 'int', 21");
        foreach ($data as $name => $values) {
            if (!$this->getDbValue($db, 'config', 'conf_id', "`conf_modid`=0 AND `conf_catid`=7 AND `conf_name`='$name'")) {
                $this->query("INSERT INTO `$table` (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) " . "VALUES ( 0,7,'$name',$values)");
            }
        }

        return true;
    }

    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('auth_db');
    }
}

$upg = new Upgrade_2017();
return $upg;
