<?php

/**
 * Class upgrade_2017
 */
class upgrade_2017 extends xoopsUpgrade
{

    /**
     * @return bool
     */
    function isApplied() {
        return ( /*$this->check_file_patch() &&*/ $this->check_auth_db() );
    }

    /**
     * @return bool
     */
    function apply() {
        return $this->apply_auth_db();
    }

    /**
     * @return bool
     */
    function check_file_patch() {
        /* $path = XOOPS_ROOT_PATH . '/class/auth';
        $lines = file( "$path/auth_provisionning.php");
        foreach ($lines as $line) {
            if ( strpos( $line, "ldap_provisionning_upd" ) !== false ) {
                // Patch found: do not apply again
                return true;
            }
        } */

        return true;
    }

    /**
     * @return bool
     */
    function check_auth_db() {
        $db = $GLOBALS['xoopsDB'];
        $value = getDbValue( $db, 'config', 'conf_id',
            "`conf_name` = 'ldap_use_TLS' AND `conf_catid` = " . XOOPS_CONF_AUTH
        );

        return (bool) ($value);
    }

    /**
     * @param $sql
     */
    function query( $sql ) {
        $db = $GLOBALS['xoopsDB'];
        if ( ! ( $ret = $db->queryF( $sql ) ) ) {
            echo $db->error();
        }
    }

    /**
     * @return bool
     */
    function apply_auth_db() {
        $db = $GLOBALS['xoopsDB'];

        // Insert config values
        $table = $db->prefix( 'config' );
        $data = array(
            'ldap_use_TLS'	=> "'_MD_AM_LDAP_USETLS', '0', '_MD_AM_LDAP_USETLS_DESC', 'yesno', 'int', 21",
        );
        foreach ($data as $name => $values) {
            if ( !getDbValue( $db, 'config', 'conf_id', "`conf_modid`=0 AND `conf_catid`=7 AND `conf_name`='$name'" ) ) {
                $this->query(
                    "INSERT INTO `$table` (conf_modid,conf_catid,conf_name,conf_title,conf_value,conf_desc,conf_formtype,conf_valuetype,conf_order) " .
                    "VALUES ( 0,7,'$name',$values)"
                );
            }
        }

        return true;
    }
}

$upg = new upgrade_2017();
return $upg;
