<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/include/functions.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formselect.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formelement.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formtext.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formtextdateselect.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formelementtray.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formdatetime.php');

class XoopsFormDateTimeTest extends TestCase
{
    protected $myClass = 'XoopsFormDateTime';

    public function test___construct()
    {
        $instance = new $this->myClass('', '');
        $this->assertInstanceOf('XoopsFormDateTime', $instance);
    }

    public function test_const()
    {
        $this->assertNotNull(\XoopsFormDateTime::SHOW_BOTH);
        $this->assertNotNull(\XoopsFormDateTime::SHOW_DATE);
        $this->assertNotNull(\XoopsFormDateTime::SHOW_TIME);
    }
}
