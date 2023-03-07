<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/class/logger/xoopslogger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/preload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/database/databasefactory.php');

require_once(XOOPS_TU_ROOT_PATH . '/kernel/online.php');

class XoopsOnlineHandlerTest extends TestCase
{
    protected $conn = null;

    protected function setUp():void
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $onlineHandler = new \XoopsOnlineHandler($db);
//        $this->conn = $onlineHandler->db();
        $this->conn = $db;
    }

    public function test___construct()
    {
        $instance = new \XoopsOnlineHandler($this->conn);
        $this->assertInstanceOf(\XoopsOnlineHandler::class, $instance);
    }
}
