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

/* @var $module_handler XoopsModuleHandler */
$module_handler = xoops_getHandler('module');
$xoopsModule    = XoopsModule::getByDirname($mydirname);
$moduleInfo     = $module_handler->get($xoopsModule->getVar('mid'));
$pathIcon32     = $moduleInfo->getInfo('icons32');

$constpref = '_MI_' . strtoupper($mydirname);

$adminmenu = array(
    array(
        'title' => constant($constpref . '_ADMINHOME'),
        'link'  => 'admin/index.php',
        'icon'  => '../../' . $pathIcon32 . '/home.png'),
    array(
        'title' => constant($constpref . '_ADMININDEX'),
        'link'  => 'admin/center.php?page=center',
        //'link' => 'admin/center.php' ,
        'icon'  => '../../' . $pathIcon32 . '/firewall.png'),
    array(
        'title' => constant($constpref . '_ADMINSTATS'),
        'link'  => 'admin/stats.php',
        'icon'  => '../../' . $pathIcon32 . '/stats.png'),
    array(
        'title' => constant($constpref . '_ADVISORY'),
        //'link' => 'admin/center.php?page=advisory' ,
        'link'  => 'admin/advisory.php',
        'icon'  => '../../' . $pathIcon32 . '/security.png'),
    array(
        'title' => constant($constpref . '_PREFIXMANAGER'),
        //'link' => 'admin/center.php?page=prefix_manager' ,
        'link'  => 'admin/prefix_manager.php',
        'icon'  => '../../' . $pathIcon32 . '/manage.png'),
    array(
        'title' => constant($constpref . '_ADMINABOUT'),
        'link'  => 'admin/about.php',
        'icon'  => '../../' . $pathIcon32 . '/about.png'));

$adminmenu4altsys = array(
    array(
        'title' => constant($constpref . '_ADMENU_MYBLOCKSADMIN'),
        'link'  => 'admin/main.php?mode=admin&lib=altsys&page=myblocksadmin'),
    array(
        'title' => _PREFERENCES,
        'link'  => 'admin/main.php?mode=admin&lib=altsys&page=mypreferences'));
