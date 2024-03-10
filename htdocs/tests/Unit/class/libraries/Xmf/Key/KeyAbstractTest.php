<?php

declare(strict_types=1);

namespace Xmf\Test\Key;

use PHPUnit\Framework\TestCase;

use Xmf\Key\ArrayStorage;
use Xmf\Key\KeyAbstract;
use Xmf\Key\StorageInterface;

class KeyAbstractTest extends TestCase
{
    /**
     * @var StorageInterface
     */
    protected $storage;
    /**
     * @var KeyAbstract
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->storage = new ArrayStorage();
        $this->object  = $this->getMockForAbstractClass(KeyAbstract::class, [$this->storage, 'test']);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(KeyAbstract::class, $this->object);

        $class  = new \ReflectionClass(KeyAbstract::class);
        $method = $class->getMethod('__construct');
        $this->assertFalse($method->isAbstract());
    }

    public function testMethodsExist()
    {
        $this->assertTrue(method_exists($this->object, 'getSigning'));
        $this->assertTrue(method_exists($this->object, 'getVerifying'));
        $this->assertTrue(method_exists($this->object, 'create'));
        $this->assertTrue(method_exists($this->object, 'kill'));
    }
}
