<?php

/**
 * @return bool
 */
function protector_prepare()
{
    // check the access is from install/index.php
    if (defined('_INSTALL_CHARSET') && !is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
        die('To use installer, remove protector\'s lines from mainfile.php first.');
    }

    // Protector class
    require_once dirname(__DIR__) . '/class/protector.php';

    // Protector object
    $protector = Protector::getInstance();
    $conf      = $protector->getConf();

    // phar wrapper deserialization
    array_walk_recursive($_GET, 'protector_phar_check');
    array_walk_recursive($_POST, 'protector_phar_check');

    // bandwidth limitation
    if (isset($conf['bwlimit_count']) && $conf['bwlimit_count'] >= 10) {
        $bwexpire = $protector->get_bwlimit();
        if ($bwexpire > time()) {
            header('HTTP/1.0 503 Service unavailable');
            $protector->call_filter('precommon_bwlimit', 'This website is very busy now. Please try later.');
        }
    }

    // bad_ips
    $bad_ips      = $protector->get_bad_ips(true);
    $bad_ip_match = $protector->ip_match($bad_ips);
    if ($bad_ip_match) {
        $protector->call_filter('precommon_badip', 'You are registered as BAD_IP by Protector.');
    }

    // global enabled or disabled
    if (!empty($conf['global_disabled'])) {
        return true;
    }

    // reliable ips
    if (isset($conf['reliable_ips'])) {
        $reliable_ips = unserialize($conf['reliable_ips'], array('allowed_classes' => false));
    } else {
        $reliable_ips = array();
    }

        // for the environment of (buggy core version && magic_quotes_gpc)
    if (!is_array($reliable_ips) && isset($conf['reliable_ips'])) {
        $reliable_ips = unserialize(stripslashes($conf['reliable_ips']), array('allowed_classes' => false));
        if (!is_array($reliable_ips)) {
            $reliable_ips = array();
        }
    }
    $is_reliable = false;
    foreach ($reliable_ips as $reliable_ip) {
        if (!empty($reliable_ip) && preg_match('/' . $reliable_ip . '/', $_SERVER['REMOTE_ADDR'])) {
            $is_reliable = true;
        }
    }

    // "DB Layer Trapper"
    $force_override = (strstr($_SERVER['REQUEST_URI'], 'protector/admin/index.php?page=advisory') !== false) ? true : false;

    // $force_override = true ;
    if ($force_override || !empty($conf['enable_dblayertrap'])) {
        @define('PROTECTOR_ENABLED_ANTI_SQL_INJECTION', 1);
        $protector->dblayertrap_init($force_override);
    }

    // "Big Umbrella" subset version
    if (!empty($conf['enable_bigumbrella'])) {
        @define('PROTECTOR_ENABLED_ANTI_XSS', 1);
        $protector->bigumbrella_init();
    }

    // force intval variables whose name is *id
    if (!empty($conf['id_forceintval'])) {
        $protector->intval_allrequestsendid();
    }

    // eliminate '..' from requests looks like file specifications
    if (!$is_reliable && !empty($conf['file_dotdot'])) {
        $protector->eliminate_dotdot();
    }

    // Check uploaded files
    if (!$is_reliable && !empty($_FILES) && !empty($conf['die_badext']) && !defined('PROTECTOR_SKIP_FILESCHECKER') && !$protector->check_uploaded_files()) {
        $protector->output_log($protector->last_error_type);
        $protector->purge();
    }

    // Variables contamination
    if (!$protector->check_contami_systemglobals()) {
        if (isset($conf['contami_action']) && ($conf['contami_action'] & 4)) {
            if ($conf['contami_action'] & 8) {
                $protector->_should_be_banned = true;
            } else {
                $protector->_should_be_banned_time0 = true;
            }
            $_GET = $_POST = array();
        }

        $protector->output_log($protector->last_error_type);
        if (isset($conf['contami_action']) && ($conf['contami_action'] & 2)) {
            $protector->purge();
        }
    }

    // prepare for DoS
    //if ( ! $protector->check_dos_attack_prepare() ) {
    //    $protector->output_log( $protector->last_error_type , 0 , true ) ;
    //}

    if (!empty($conf['disable_features'])) {
        $protector->disable_features();
    }
    return null;
}

/**
 * Callback for array_walk_recursive to check for phar wrapper
 *
 * @param mixed $item
 * @param mixed $key
 *
 * @return void
 */
function protector_phar_check($item, $key)
{
    $check = preg_match('#^\s*phar://#', $item);
    if(1===$check) {
        $protector = Protector::getInstance();
        $protector->message = 'Protector detects attacking actions';
        $protector->output_log('PHAR');
        $protector->purge(false);
    }
}
