<?php

declare(strict_types=1);

namespace Xmf\Test;

use PHPUnit\Framework\TestCase;


use Xmf\Random;

class RandomTest extends TestCase
{
    /**
     * @var Random
     */
    protected $object;
    protected $myClass = Random::class;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        // $this->object = new Random();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testGenerateOneTimeToken()
    {
        try {
            $result = Random::generateOneTimeToken();
        } catch (\Exception $e) {
        }

        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{128}$/', $result);
    }

    public function testGenerateKey()
    {
        try {
            $result = Random::generateKey();
        } catch (\Exception $e) {
        }

        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{128}$/', $result);
    }
}
