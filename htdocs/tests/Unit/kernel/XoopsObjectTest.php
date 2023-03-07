<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');

class XoopsObjectTestInstance extends XoopsObject
{

}

class XoopsObjectTest extends TestCase
{
    public $myClass = 'XoopsObjectTestInstance';

    public function test___construct()
    {
        $instance = new $this->myClass();
        $this->assertInstanceOf($this->myClass, $instance);
        $this->assertInstanceOf(\XoopsObject::class, $instance);
    }
}
