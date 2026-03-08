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

// Dynamic function name via eval() — required for XOOPS module lifecycle callbacks.
// $mydirname comes from basename(__DIR__) via ProtectorRegistry, not user input.
if (!function_exists($mydirname . '_notify_iteminfo')) {
    eval('function ' . $mydirname . '_notify_iteminfo($category, $item_id) { return protector_notify_base(' . var_export($mydirname, true) . ', $category, $item_id); }');
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
