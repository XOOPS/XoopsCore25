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

// Note: D3-style cloned installs of Protector are no longer supported as of 2.5.12.
// The dynamic function naming via eval has been removed for security.
if (!function_exists('protector_notify_iteminfo')) {
    /**
     * XOOPS notification callback for protector module.
     *
     * @param string $category
     * @param int    $item_id
     *
     * @return array
     */
    function protector_notify_iteminfo($category, $item_id)
    {
        $registry  = ProtectorRegistry::getInstance();
        $mydirname = $registry->getEntry('mydirname');
        if (empty($mydirname)) {
            $mydirname = 'protector';
        }

        return protector_notify_base($mydirname, $category, $item_id);
    }
}

if (!function_exists('protector_notify_base')) {

    /**
     * @param $mydirname
     * @param $category
     * @param $item_id
     *
     * @return mixed
     */
    function protector_notify_base($mydirname, $category, $item_id)
    {
        include_once __DIR__ . '/include/common_functions.php';

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        /** @var XoopsModuleHandler $module_handler */
        $module_handler = xoops_getHandler('module');
        $module         = $module_handler->getByDirname($mydirname);

        if ($category === 'global') {
            $item['name'] = '';
            $item['url']  = '';

            return $item;
        }
        return [];
    }
}
