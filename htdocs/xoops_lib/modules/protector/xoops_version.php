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

if (file_exists(__DIR__ . '/language/' . $language . '/modinfo.php')) {
    include __DIR__ . '/language/' . $language . '/modinfo.php';
} else {
    if (file_exists(__DIR__ . '/language/english/modinfo.php')) {
        include __DIR__ . '/language/english/modinfo.php';
    }
}
$constpref = '_MI_' . strtoupper($mydirname);
// end hack

$modversion['name']           = constant($constpref . '_NAME');
$modversion['description']    = constant($constpref . '_DESC');
$modversion['version']        = (string) file_get_contents(__DIR__ . '/include/version.txt');
$modversion['credits']        = 'PEAK Corp.';
$modversion['author']         = 'GIJ=CHECKMATE PEAK Corp.(http://www.peak.ne.jp/)';
$modversion['help']           = 'page=help';
$modversion['license']        = 'GNU GPL 2.0';
$modversion['license_url']    = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']       = 1;
$modversion['image']          = file_exists($mydirpath . '/module_icon.png') ? 'module_icon.png' : 'module_icon.php';
$modversion['iconbig']        = 'module_icon.php?file=iconbig';
$modversion['iconsmall']      = 'module_icon.php?file=iconsmall';
$modversion['dirname']        = $mydirname;
$modversion['dirmoduleadmin'] = 'Frameworks/moduleclasses';
$modversion['icons16']        = 'Frameworks/moduleclasses/icons/16';
$modversion['icons32']        = 'Frameworks/moduleclasses/icons/32';

//about
$modversion['release_date']        = '2019/02/18';
$modversion['module_website_url']  = 'https://xoops.org/';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.3.9';
$modversion['min_xoops']           = '2.5.10';

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false;
$modversion['tables']  = [];

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/admin_menu.php';

// Templates

// Blocks
$modversion['blocks'] = [];

// Menu
$modversion['hasMain'] = 0;

// Config
$modversion['config'][1] = [
    'name'        => 'global_disabled',
    'title'       => $constpref . '_GLOBAL_DISBL',
    'description' => $constpref . '_GLOBAL_DISBLDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => []];
$modversion['config'][]  = [
    'name'        => 'default_lang',
    'title'       => $constpref . '_DEFAULT_LANG',
    'description' => $constpref . '_DEFAULT_LANGDSC',
    'formtype'    => 'text',
    'valuetype'   => 'text',
    'default'     => isset($GLOBALS['xoopsConfig']['language']) ? $GLOBALS['xoopsConfig']['language'] : 'english',
    'options'     => []];
$modversion['config'][]  = [
    'name'        => 'log_level',
    'title'       => $constpref . '_LOG_LEVEL',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 255,
    'options'     => [
        $constpref . '_LOGLEVEL0'   => 0,
        $constpref . '_LOGLEVEL15'  => 15,
        $constpref . '_LOGLEVEL63'  => 63,
        $constpref . '_LOGLEVEL255' => 255]];
$modversion['config'][]  = [
    'name'        => 'banip_time0',
    'title'       => $constpref . '_BANIP_TIME0',
    'description' => $constpref . '_BANIP_TIME0DSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => 86400,
    'options'     => []];
$modversion['config'][]  = [
    'name'        => 'reliable_ips',
    'title'       => $constpref . '_RELIABLE_IPS',
    'description' => $constpref . '_RELIABLE_IPSDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'array',
    'default'     => '^192.168.|127.0.0.1',
    'options'     => []];
$modversion['config'][]  = [
    'name'        => 'session_fixed_topbit',
    'title'       => $constpref . '_HIJACK_TOPBIT',
    'description' => $constpref . '_HIJACK_TOPBITDSC',
    'formtype'    => 'text',
    'valuetype'   => 'text',
    'default'     => '24/56',
    'options'     => []];
$modversion['config'][]  = [
    'name'        => 'groups_denyipmove',
    'title'       => $constpref . '_HIJACK_DENYGP',
    'description' => $constpref . '_HIJACK_DENYGPDSC',
    'formtype'    => 'group_multi',
    'valuetype'   => 'array',
    'default'     => [1],
    'options'     => []];
$modversion['config'][]  = [
    'name'        => 'san_nullbyte',
    'title'       => $constpref . '_SAN_NULLBYTE',
    'description' => $constpref . '_SAN_NULLBYTEDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => []];
/* $modversion['config'][] = array(
    'name'            => 'die_nullbyte' ,
    'title'            => $constpref.'_DIE_NULLBYTE' ,
    'description'    => $constpref.'_DIE_NULLBYTEDSC' ,
    'formtype'        => 'yesno' ,
    'valuetype'        => 'int' ,
    'default'        => "1" ,
    'options'        => array()
) ; */
$modversion['config'][] = [
    'name'        => 'die_badext',
    'title'       => $constpref . '_DIE_BADEXT',
    'description' => $constpref . '_DIE_BADEXTDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'contami_action',
    'title'       => $constpref . '_CONTAMI_ACTION',
    'description' => $constpref . '_CONTAMI_ACTIONDS',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 3,
    'options'     => [
        $constpref . '_OPT_NONE'     => 0,
        $constpref . '_OPT_EXIT'     => 3,
        $constpref . '_OPT_BIPTIME0' => 7,
        $constpref . '_OPT_BIP'      => 15]];
$modversion['config'][] = [
    'name'        => 'isocom_action',
    'title'       => $constpref . '_ISOCOM_ACTION',
    'description' => $constpref . '_ISOCOM_ACTIONDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [
        $constpref . '_OPT_NONE'     => 0,
        $constpref . '_OPT_SAN'      => 1,
        $constpref . '_OPT_EXIT'     => 3,
        $constpref . '_OPT_BIPTIME0' => 7,
        $constpref . '_OPT_BIP'      => 15]];
$modversion['config'][] = [
    'name'        => 'union_action',
    'title'       => $constpref . '_UNION_ACTION',
    'description' => $constpref . '_UNION_ACTIONDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [
        $constpref . '_OPT_NONE'     => 0,
        $constpref . '_OPT_SAN'      => 1,
        $constpref . '_OPT_EXIT'     => 3,
        $constpref . '_OPT_BIPTIME0' => 7,
        $constpref . '_OPT_BIP'      => 15]];
$modversion['config'][] = [
    'name'        => 'id_forceintval',
    'title'       => $constpref . '_ID_INTVAL',
    'description' => $constpref . '_ID_INTVALDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'file_dotdot',
    'title'       => $constpref . '_FILE_DOTDOT',
    'description' => $constpref . '_FILE_DOTDOTDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'bf_count',
    'title'       => $constpref . '_BF_COUNT',
    'description' => $constpref . '_BF_COUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '10',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'bwlimit_count',
    'title'       => $constpref . '_BWLIMIT_COUNT',
    'description' => $constpref . '_BWLIMIT_COUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'dos_skipmodules',
    'title'       => $constpref . '_DOS_SKIPMODS',
    'description' => $constpref . '_DOS_SKIPMODSDSC',
    'formtype'    => 'text',
    'valuetype'   => 'text',
    'default'     => '',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'dos_expire',
    'title'       => $constpref . '_DOS_EXPIRE',
    'description' => $constpref . '_DOS_EXPIREDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '60',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'dos_f5count',
    'title'       => $constpref . '_DOS_F5COUNT',
    'description' => $constpref . '_DOS_F5COUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '20',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'dos_f5action',
    'title'       => $constpref . '_DOS_F5ACTION',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'exit',
    'options'     => [
        $constpref . '_DOSOPT_NONE'     => 'none',
        $constpref . '_DOSOPT_SLEEP'    => 'sleep',
        $constpref . '_DOSOPT_EXIT'     => 'exit',
        $constpref . '_DOSOPT_BIPTIME0' => 'biptime0',
        $constpref . '_DOSOPT_BIP'      => 'bip',
        $constpref . '_DOSOPT_HTA'      => 'hta']];
$modversion['config'][] = [
    'name'        => 'dos_crcount',
    'title'       => $constpref . '_DOS_CRCOUNT',
    'description' => $constpref . '_DOS_CRCOUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '40',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'dos_craction',
    'title'       => $constpref . '_DOS_CRACTION',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'exit',
    'options'     => [
        $constpref . '_DOSOPT_NONE'     => 'none',
        $constpref . '_DOSOPT_SLEEP'    => 'sleep',
        $constpref . '_DOSOPT_EXIT'     => 'exit',
        $constpref . '_DOSOPT_BIPTIME0' => 'biptime0',
        $constpref . '_DOSOPT_BIP'      => 'bip',
        $constpref . '_DOSOPT_HTA'      => 'hta']];
$modversion['config'][] = [
    'name'        => 'dos_crsafe',
    'title'       => $constpref . '_DOS_CRSAFE',
    'description' => $constpref . '_DOS_CRSAFEDSC',
    'formtype'    => 'text',
    'valuetype'   => 'text',
    'default'     => '/(bingbot|Googlebot|Yahoo! Slurp)/i',
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'bip_except',
    'title'       => $constpref . '_BIP_EXCEPT',
    'description' => $constpref . '_BIP_EXCEPTDSC',
    'formtype'    => 'group_multi',
    'valuetype'   => 'array',
    'default'     => [1],
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'disable_features',
    'title'       => $constpref . '_DISABLES',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => ['xmlrpc' => 1, 'xmlrpc + 2.0.9.2 bugs' => 1025, '_NONE' => 0]];
$modversion['config'][] = [
    'name'        => 'enable_dblayertrap',
    'title'       => $constpref . '_DBLAYERTRAP',
    'description' => $constpref . '_DBLAYERTRAPDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'dblayertrap_wo_server',
    'title'       => $constpref . '_DBTRAPWOSRV',
    'description' => $constpref . '_DBTRAPWOSRVDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'enable_bigumbrella',
    'title'       => $constpref . '_BIGUMBRELLA',
    'description' => $constpref . '_BIGUMBRELLADSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'spamcount_uri4user',
    'title'       => $constpref . '_SPAMURI4U',
    'description' => $constpref . '_SPAMURI4UDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => []];
$modversion['config'][] = [
    'name'        => 'spamcount_uri4guest',
    'title'       => $constpref . '_SPAMURI4G',
    'description' => $constpref . '_SPAMURI4GDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => []];

$modversion['config'][] = [
    'name'        => 'stopforumspam_action',
    'title'       => $constpref . '_STOPFORUMSPAM_ACTION',
    'description' => $constpref . '_STOPFORUMSPAM_ACTIONDSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'none',
    'options'     => [
        '_NONE'                      => 'none',
        $constpref . '_OPT_NONE'     => 'log',
        $constpref . '_OPT_SAN'      => 'san',
        $constpref . '_OPT_BIPTIME0' => 'biptime0',
        $constpref . '_OPT_BIP'      => 'bip']];

// Search
$modversion['hasSearch'] = 0;

// Comments
$modversion['hasComments'] = 0;

// Config Settings (only for modules that need config settings generated automatically)

// Notification

$modversion['hasNotification'] = 0;

// onInstall, onUpdate, onUninstall
$modversion['onInstall']   = 'oninstall.php';
$modversion['onUpdate']    = 'onupdate.php';
$modversion['onUninstall'] = 'onuninstall.php';
