<?php

declare(strict_types=1);

namespace Xmf\Test\Module\Helper;

use PHPUnit\Framework\TestCase;
use Xmf\Module\Helper\AbstractHelper;

require_once dirname(__DIR__, 5) . '/init_new.php';

class AbstractHelperTest extends TestCase
{
    /**
     * @var AbstractHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        //$this->object = new \Xmf\Module\Helper\AbstractHelper;
        $this->object = $this->getMockForAbstractClass(AbstractHelper::class);
        //$this->object->expects($this->any())
        //    ->method('getDefaultParams')
        //    ->will($this->returnValue(array()));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testSetDebug()
    {
        //TODO change $debug in AbstractHelper from protected to public, otherwise assertEquals() won't work

        $this->assertTrue(method_exists($this->object, 'setDebug'));
        $this->object->setDebug(true);
//        $this->assertAttributeEquals(true, 'debug', $this->object);
        $this->assertEquals(true, $this->object->debug);
        $this->object->setDebug(false);
//        $this->assertAttributeEquals(false, 'debug', $this->object);
        $this->assertEquals(false, $this->object->debug);
    }

    public function testAddLog()
    {
        $this->assertTrue(method_exists($this->object, 'addLog'));
        $this->object->addLog('message to send to bitbucket');
    }
}
