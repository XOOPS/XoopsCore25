<?php

declare(strict_types=1);

namespace Xmf\Test\Key;

use PHPUnit\Framework\TestCase;

use Xmf\Key\StorageInterface;

class StorageInterfaceTest extends TestCase
{
    /**
     * @var StorageInterface
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = $this->createMock(StorageInterface::class);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testContracts()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->object);
        $this->assertTrue(method_exists($this->object, 'save'));
        $this->assertTrue(method_exists($this->object, 'fetch'));
        $this->assertTrue(method_exists($this->object, 'exists'));
        $this->assertTrue(method_exists($this->object, 'delete'));
    }
}
