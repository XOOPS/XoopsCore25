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

class XoopsPersistableObjectHandlerTestInstance extends \XoopsPersistableObjectHandler
{
    // allow access to the protected function in abstract class
    public function __construct(
        \XoopsDatabase $db,
                                        $table = '',
                                        $className = '',
                                        $keyName = '',
                                        $identifierName = ''
    ) {
        parent::__construct($db, $table, $className, $keyName, $identifierName);
    }
}

class XoopsPersistableObjectHandlerTest extends TestCase
{
    protected $myclass = 'XoopsPersistableObjectHandlerTestInstance';

    public function test___publicProperties()
    {
        $items = ['db'];
        foreach ($items as $item) {
            try {
                $prop = new ReflectionProperty($this->myclass, $item);
            } catch (ReflectionException $e) {
            }
            $this->assertTrue($prop->isPublic());
        }
    }

    public function test___construct()
    {
        $conn           = \XoopsDatabaseFactory::getDatabaseConnection();
        $table          = 'table';
        $className      = 'className';
        $keyName        = 'keyName';
        $identifierName = 'identifierName';
        $instance       = new $this->myclass($conn, $table, $className, $keyName, $identifierName);
        $this->assertInstanceOf($this->myclass, $instance);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $instance);
    }
}
