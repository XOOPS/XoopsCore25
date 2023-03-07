<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/class/logger/xoopslogger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/preload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/database/databasefactory.php');

require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');

class XoopsObjecthandlerTestInstance extends \XoopsObjectHandler
{
}

class XoopsObjectHandlerTest extends TestCase
{
    public $myClass = 'XoopsObjecthandlerTestInstance';

    public function test___publicProperties()
    {
        $items = ['db'];
        foreach ($items as $item) {
            try {
                $prop = new ReflectionProperty($this->myClass, $item);
            } catch (ReflectionException $e) {
            }
            $this->assertTrue($prop->isPublic());
        }
    }

    public function test___construct()
    {
        $conn     = \XoopsDatabaseFactory::getDatabaseConnection();
        $instance = new $this->myClass($conn);
        $this->assertInstanceOf($this->myClass, $instance);
        $this->assertInstanceOf(\XoopsObjectHandler::class, $instance);
    }
}
