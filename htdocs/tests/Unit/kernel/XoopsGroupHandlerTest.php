<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/kernel/group.php');

class XoopsGroupHandlerTest extends TestCase
{
    protected $conn = null;

    protected function setUp():void
    {
$this->conn = XoopsDatabaseFactory::getDatabaseConnection();
    }

    public function test___construct()
    {
        $instance = new \XoopsGroupHandler($this->conn);
        $this->assertInstanceOf(\XoopsGroupHandler::class, $instance);
    }
}
