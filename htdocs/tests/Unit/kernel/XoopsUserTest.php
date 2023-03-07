<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');
require_once(XOOPS_TU_ROOT_PATH . '/kernel/user.php');

class XoopsUserTest extends TestCase
{
    protected function setUp():void
    {
    }

    public function test___construct()
    {
        $instance = new \XoopsUser();
        $this->assertInstanceOf(\XoopsUser::class, $instance);
    }
}
