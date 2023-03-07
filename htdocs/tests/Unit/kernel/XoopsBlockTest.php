<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');
require_once(XOOPS_TU_ROOT_PATH . '/kernel/block.php');

class XoopsBlockTest extends TestCase
{
    protected function setUp():void
    {
    }

    public function test___construct()
    {
        $instance = new \XoopsBlock();
        $this->assertInstanceOf(\XoopsBlock::class, $instance);
    }
}
