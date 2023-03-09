<?php

declare(strict_types=1);

namespace Xmf\Test\Jwt;

use PHPUnit\Framework\TestCase;


use Xmf\Jwt\KeyFactory;
use Xmf\Key\ArrayStorage;
use Xmf\Key\Basic;

class KeyFactoryTest extends TestCase
{
    /**
     * @var KeyFactory
     */
    protected $object;
    /**
     * @var ArrayStorage
     */
    protected $storage;
    /**
     * @var string
     */
    protected $testKey = 'x-unit-test-key';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        //$this->object = new KeyFactory;
        $this->storage = new ArrayStorage();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->storage->delete($this->testKey);
    }

    public function testBuild()
    {
        $instance = KeyFactory::build($this->testKey, $this->storage);
        $this->assertInstanceOf(Basic::class, $instance);
        $this->assertTrue($this->storage->exists($this->testKey));

        $actual = KeyFactory::build($this->testKey, $this->storage);
        $this->assertNotSame($instance, $actual);

        $this->assertEquals($instance->getSigning(), $actual->getSigning());
    }

    public function testBuildException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $instance = KeyFactory::build(['muck'], $this->storage);
    }
}
