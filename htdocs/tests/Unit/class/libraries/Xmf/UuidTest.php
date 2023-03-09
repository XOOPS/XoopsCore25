<?php

declare(strict_types=1);

namespace Xmf\Test;

use PHPUnit\Framework\TestCase;

use Xmf\Uuid;

class UuidTest extends TestCase
{
    /**
     * @var Random
     */
    protected $object;
    protected $myClass = Uuid::class;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testGenerate()
    {
        // match spec for version 4 UUID as per rfc4122
        $uuidMatch = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

        try {
            $result = Uuid::generate();
        } catch (\Exception $e) {
        }
        $this->assertMatchesRegularExpression($uuidMatch, $result);

        try {
            $anotherResult = Uuid::generate();
        } catch (\Exception $e) {
        }
        $this->assertMatchesRegularExpression($uuidMatch, $anotherResult);

        $this->assertNotEquals($result, $anotherResult);
    }

    public function testPackUnpack()
    {
        try {
            $uuid = Uuid::generate();
        } catch (\Exception $e) {
        }
        $binUuid = Uuid::packAsBinary($uuid);
        $strUuid = Uuid::unpackBinary($binUuid);
        $this->assertEquals($uuid, $strUuid);
    }

    public function testInvalidPack()
    {
        $this->expectException(\InvalidArgumentException::class);
        $binUuid = Uuid::packAsBinary('garbage-data');
    }

    public function testInvalidUnpack()
    {
        $this->expectException(\InvalidArgumentException::class);
        $binUuid = Uuid::unpackBinary('123456789012345');
    }

    public function testInvalidUnpack2()
    {
        $this->expectException(\UnexpectedValueException::class);
        $binUuid = Uuid::unpackBinary('0000000000000000');
    }

    /* verify natural sort order is the same for readable and binary formats */
    public function testSortOrder()
    {
        $auuid = [];
        $buuid = [];
        for ($i = 1; $i < 10; ++$i) {
            try {
                $uuid = Uuid::generate();
            } catch (\Exception $e) {
            }
            $auuid[] = $uuid;
            $buuid[] = Uuid::packAsBinary($uuid);
        }
        sort($auuid);
        sort($buuid);
        foreach ($auuid as $key => $uuid) {
            $this->assertEquals($uuid, Uuid::unpackBinary($buuid[$key]));
        }
    }
}

