<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');
require_once(XOOPS_TU_ROOT_PATH . '/kernel/tplfile.php');

class XoopsTplFileTest extends TestCase
{
    protected function setUp():void
    {
    }

    public function test___construct()
    {
        $instance = new \XoopsTplfile();
        $this->assertInstanceOf('\XoopsTplFile', $instance);
    }
}
