<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/include/functions.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formelement.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formlabel.php');

class XoopsFormLabelTest extends TestCase
{
    protected $myClass = 'XoopsFormLabel';

    public function test___construct()
    {
        $instance = new $this->myClass();
        $this->assertInstanceOf('XoopsFormLabel', $instance);
    }
}
