<?php

/**
 * @return bool
 */
function protector_postcommon()
{
    global $xoopsUser , $xoopsModule ;

    // patch for 2.2.x from xoops.org (I know this is not so beautiful...)
    if ( substr( @XOOPS_VERSION , 6 , 3 ) > 2.0 && stristr( @$_SERVER['REQUEST_URI'] , 'modules/system/admin.php?fct=preferences' ) ) {
        $module_handler =& xoops_gethandler( 'module' ) ;
        $module =& $module_handler->get( intval( @$_GET['mod'] ) ) ;
        if ( is_object( $module ) ) {
            $module->getInfo() ;
        }
    }

    // configs writable check
    if ( @$_SERVER['REQUEST_URI'] == '/admin.php' && ! is_writable( dirname(dirname(__FILE__)).'/configs' ) ) {
        trigger_error( 'You should turn the directory ' . dirname(dirname(__FILE__)).'/configs writable' , E_USER_WARNING ) ;
    }

    // Protector object
    require_once dirname(dirname(__FILE__)).'/class/protector.php' ;
    $db =& XoopsDatabaseFactory::getDatabaseConnection();
    $protector =& Protector::getInstance() ;
    $protector->setConn( $db->conn ) ;
    $protector->updateConfFromDb() ;
    $conf = $protector->getConf() ;
    if( empty( $conf ) ) return true ; // not installed yet

    // phpmailer vulnerability
    // http://larholm.com/2007/06/11/phpmailer-0day-remote-execution/
    if ( in_array( substr( XOOPS_VERSION , 0 , 12 ) , array( 'XOOPS 2.0.16' , 'XOOPS 2.0.13' , 'XOOPS 2.2.4' ) ) ) {
        $config_handler =& xoops_gethandler('config');
        $xoopsMailerConfig =& $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
        if ( $xoopsMailerConfig['mailmethod'] == 'sendmail' && md5_file( XOOPS_ROOT_PATH.'/class/mail/phpmailer/class.phpmailer.php' ) == 'ee1c09a8e579631f0511972f929fe36a' ) {
            echo '<strong>phpmailer security hole! Change the preferences of mail from "sendmail" to another, or upgrade the core right now! (message by protector)</strong>' ;
        }
    }

    // global enabled or disabled
    if( ! empty( $conf['global_disabled'] ) ) return true ;

    // group1_ips (groupid=1)
    if ( is_object( $xoopsUser ) && in_array( 1 , $xoopsUser->getGroups() ) ) {
        $group1_ips = $protector->get_group1_ips( true ) ;
        if ( implode( '' , array_keys( $group1_ips ) ) ) {
            $group1_allow = $protector->ip_match( $group1_ips ) ;
            if( empty( $group1_allow ) ) die( 'This account is disabled for your IP by Protector.<br />Clear cookie if you want to access this site as a guest.' ) ;
        }
    }

    // reliable ips
    $reliable_ips = @unserialize( @$conf['reliable_ips'] ) ;
    if ( is_array( $reliable_ips ) ) foreach ($reliable_ips as $reliable_ip) {
        if ( ! empty( $reliable_ip ) && preg_match( '/'.$reliable_ip.'/' , $_SERVER['REMOTE_ADDR'] ) ) {
            return true ;
        }
    }

    // user information (uid and can be banned)
    if ( is_object( @$xoopsUser ) ) {
        $uid = $xoopsUser->getVar('uid') ;
        $can_ban = count( @array_intersect( $xoopsUser->getGroups() , @unserialize( @$conf['bip_except'] ) ) ) ? false : true ;
    } else {
        // login failed check
        if ( ( ! empty( $_POST['uname'] ) && ! empty( $_POST['pass'] ) ) || ( ! empty( $_COOKIE['autologin_uname'] ) && ! empty( $_COOKIE['autologin_pass'] ) ) ) {
            $protector->check_brute_force() ;
        }
        $uid = 0 ;
        $can_ban = true ;
    }
    // CHECK for spammers IPS/EMAILS during POST Actions
    if (@$conf['stopforumspam_action'] != 'none') {
        $protector->stopforumspam($uid);
    }

    // If precheck has already judged that he should be banned
    if ($can_ban && $protector->_should_be_banned) {
        $protector->register_bad_ips() ;
    } else if ($can_ban && $protector->_should_be_banned_time0) {
        $protector->register_bad_ips( time() + $protector->_conf['banip_time0'] ) ;
    }

    // DOS/CRAWLER skipping based on 'dirname' or getcwd()
    $dos_skipping = false ;
    $skip_dirnames = explode( '|' , @$conf['dos_skipmodules'] ) ;
    if( ! is_array( $skip_dirnames ) ) $skip_dirnames = array() ;
    if ( is_object( @$xoopsModule ) ) {
        if ( in_array( $xoopsModule->getVar('dirname') , $skip_dirnames ) ) {
            $dos_skipping = true ;
        }
    } else {
        foreach ($skip_dirnames as $skip_dirname) {
            if ( $skip_dirname && strstr( getcwd() , $skip_dirname ) ) {
                $dos_skipping = true ;
                break ;
            }
        }
    }

    // module can controll DoS skipping
    if( defined( 'PROTECTOR_SKIP_DOS_CHECK' ) ) $dos_skipping = true ;

    // DoS Attack
    if ( empty( $dos_skipping ) && ! $protector->check_dos_attack( $uid , $can_ban ) ) {
        $protector->output_log( $protector->last_error_type , $uid , true , 16 ) ;
    }

    // check session hi-jacking
    $ips = explode( '.' ,  @$_SESSION['protector_last_ip'] ) ;
    $protector_last_numip = @$ips[0] * 0x1000000 + @$ips[1] * 0x10000 + @$ips[2] * 0x100 + @$ips[3] ;
    $ips = explode( '.' ,  $_SERVER['REMOTE_ADDR'] ) ;
    $remote_numip = @$ips[0] * 0x1000000 + @$ips[1] * 0x10000 + @$ips[2] * 0x100 + @$ips[3] ;
    $shift = 32 - @$conf['session_fixed_topbit'] ;
    if ( $shift < 32 && $shift >= 0 && ! empty( $_SESSION['protector_last_ip'] ) && $protector_last_numip >> $shift != $remote_numip >> $shift ) {
        if ( is_object( $xoopsUser ) && count( array_intersect( $xoopsUser->getGroups() , unserialize( $conf['groups_denyipmove'] ) ) ) ) {
            $protector->purge( true ) ;
        }
    }
    $_SESSION['protector_last_ip'] = $_SERVER['REMOTE_ADDR'] ;

    // SQL Injection "Isolated /*"
    if ( ! $protector->check_sql_isolatedcommentin( @$conf['isocom_action'] & 1 ) ) {
        if( ( $conf['isocom_action'] & 8 ) && $can_ban ) $protector->register_bad_ips() ;
        else if( ( $conf['isocom_action'] & 4 ) && $can_ban ) $protector->register_bad_ips( time() + $protector->_conf['banip_time0'] ) ;
        $protector->output_log( 'ISOCOM' , $uid , true , 32 ) ;
        if( $conf['isocom_action'] & 2 ) $protector->purge() ;
    }

    // SQL Injection "UNION"
    if ( ! $protector->check_sql_union( @$conf['union_action'] & 1 ) ) {
        if( ( $conf['union_action'] & 8 ) && $can_ban ) $protector->register_bad_ips() ;
        else if( ( $conf['union_action'] & 4 ) && $can_ban ) $protector->register_bad_ips( time() + $protector->_conf['banip_time0'] ) ;
        $protector->output_log( 'UNION' , $uid , true , 32 ) ;
        if( $conf['union_action'] & 2 ) $protector->purge() ;
    }

    if ( ! empty( $_POST ) ) {
        // SPAM Check
        if ( is_object( $xoopsUser ) ) {
            if ( ! $xoopsUser->isAdmin() && $conf['spamcount_uri4user'] ) {
                $protector->spam_check( intval( $conf['spamcount_uri4user'] ) , $xoopsUser->getVar('uid') ) ;
            }
        } else if ($conf['spamcount_uri4guest']) {

            $protector->spam_check( intval( $conf['spamcount_uri4guest'] ) , 0 ) ;
        }

        // filter plugins for POST on postcommon stage
        $protector->call_filter( 'postcommon_post' ) ;
    }

    // register.php Protection
    if ($_SERVER['SCRIPT_FILENAME'] == XOOPS_ROOT_PATH.'/register.php') {
        $protector->call_filter( 'postcommon_register' ) ;
    }

}
