<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/include/functions.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formelement.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formhidden.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formhiddentoken.php');

class XoopsFormHiddenTokenTest extends TestCase
{
    protected $myClass = 'XoopsFormHiddenToken';

    public function test___construct()
    {
        $instance = new $this->myClass();
        $this->assertInstanceOf('XoopsFormHiddenToken', $instance);
    }
}
