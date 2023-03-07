<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/kernel/groupperm.php');

class XoopsGroupPermHandlerTest extends TestCase
{
    protected $conn = null;

    protected function setUp():void
    {
$this->conn = XoopsDatabaseFactory::getDatabaseConnection();
    }

    public function test___construct()
    {
        $instance = new \XoopsGroupPermHandler($this->conn);
        $this->assertInstanceOf(\XoopsGroupPermHandler::class, $instance);
    }
}
