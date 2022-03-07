<?php

use XoopsModules\Protector;
use XoopsModules\Protector\Registry;

require_once __DIR__ . '/preloads/autoloader.php';

// start hack by Trabis
if (!class_exists('XoopsModules\Protector\Registry')) {
    exit('Registry not found');
}

$registry  = Registry::getInstance();
$mydirname = $registry->getEntry('mydirname');
$mydirpath = $registry->getEntry('mydirpath');
$language  = $registry->getEntry('language');
// end hack by Trabis

eval('function ' . $mydirname . '_notify_iteminfo( $category, $item_id ){    return protector_notify_base( "' . $mydirname . '" , $category , $item_id ) ;}');

if (!function_exists('protector_notify_base')) {

    /**
     * @param string $mydirname
     * @param string $category
     * @param $item_id
     *
     * @return array|null
     */
    function protector_notify_base($mydirname, $category, $item_id)
    {
        require_once __DIR__ . '/include/common_functions.php';

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module         = $moduleHandler->getByDirname($mydirname);

        if ($category === 'global') {
            $item['name'] = '';
            $item['url']  = '';

            return $item;
        }
        return null;
    }
}
