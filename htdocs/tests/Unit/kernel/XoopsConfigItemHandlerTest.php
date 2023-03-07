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
require_once(XOOPS_TU_ROOT_PATH . '/kernel/configitem.php');

class XoopsConfigItemHandlerTest extends TestCase
{
    protected $myclass = 'XoopsConfigItemHandler';
    protected $conn    = null;

    protected function setUp(): void
    {
        $this->conn = XoopsDatabaseFactory::getDatabaseConnection();
    }

    public function test___construct()
    {
        $instance = new \XoopsConfigItemHandler($this->conn);
        $this->assertInstanceOf(\XoopsConfigItemHandler::class, $instance);
    }
}
