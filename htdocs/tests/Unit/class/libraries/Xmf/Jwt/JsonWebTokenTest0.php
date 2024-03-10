<?php

declare(strict_types=1);

namespace Xmf\Test\Jwt;

use PHPUnit\Framework\TestCase;

use Xmf\Jwt\JsonWebToken;
use Xmf\Key\ArrayStorage;
use Xmf\Key\Basic;
use Xmf\Key\KeyAbstract;
use Xmf\Key\StorageInterface;

class JsonWebTokenTest extends TestCase
{
    /** @var StorageInterface */
    protected $storage;
    /** @var KeyAbstract */
    protected $key;
    /**
     * @var JsonWebToken
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->storage = new ArrayStorage();
        $this->key     = new Basic($this->storage, 'testkey');
        $this->key->create();
        $this->object = new JsonWebToken($this->key);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function test__construct()
    {
        $this->assertInstanceOf(JsonWebToken::class, $this->object);

        $this->expectException(\DomainException::class);
        $actual = new JsonWebToken($this->key, 'badalgo');
    }

    public function testSetAlgorithm()
    {
        $actual = $this->object->setAlgorithm('HS512');
        $this->assertSame($this->object, $actual);

        $this->expectException(\DomainException::class);
        $actual = $this->object->setAlgorithm('xxxxx');
    }

    public function testCreateDecode()
    {
        $token = $this->object->create(['test' => 'create'], 6);
        $this->assertIsString($token);

        $this->assertFalse($this->object->decode($token, ['not-that-test' => 'create']));
        $this->assertFalse($this->object->decode($token, ['test' => 'notcreate']));

        $decoder = new JsonWebToken($this->key);
        $this->assertNotSame($this->object, $decoder);

        $actual = $decoder->decode($token, ['test' => 'create']);
        $this->assertObjectHasAttribute('exp', $actual);
        $this->assertObjectHasAttribute('test', $actual);

        // create expired token
        $token = $this->object->create(['test' => 'create', 'exp' => (time() - 30)]);
        $this->assertIsString($token);

        $actual = @$decoder->decode($token);
        $this->assertFalse($actual);
    }
}
