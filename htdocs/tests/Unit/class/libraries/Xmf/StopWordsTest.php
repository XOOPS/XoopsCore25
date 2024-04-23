<?php

declare(strict_types=1);

namespace Xmf\Test;

use PHPUnit\Framework\TestCase;


use Xmf\StopWords;

/**
 * Mock StopWords
 */
class MockStopWords extends \Xmf\StopWords
{
    public function __construct()
    {
        $this->stopwordList = array_fill_keys(['it', 'is', 'our', 'mock'], true);
    }
}

class StopWordsTest extends TestCase
{
    /**
     * @var StopWords
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new MockStopWords();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testCheck()
    {
        $this->assertTrue($this->object->check('XOOPS'));
        $this->assertFalse($this->object->check('is'));
        $this->assertFalse($this->object->check('IS'));
    }
}
