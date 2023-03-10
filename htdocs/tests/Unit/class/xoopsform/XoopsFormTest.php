<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/form.php');

class XoopsFormInstance extends XoopsForm
{
    public function render()
    {
    }
}

class XoopsFormTest extends TestCase
{
    protected $myClass = 'XoopsFormInstance';

    public function test___construct()
    {
        $instance = new $this->myClass('', '', '');
        $this->assertInstanceOf('XoopsForm', $instance);
    }
}
