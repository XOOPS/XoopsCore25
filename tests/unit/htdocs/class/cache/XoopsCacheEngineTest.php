<?php

declare(strict_types=1);

namespace xoopscache;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsCacheEngine;
use XoopsCacheEngineInterface;

#[CoversClass(XoopsCacheEngine::class)]
class XoopsCacheEngineTest extends TestCase
{
    private XoopsCacheEngine $engine;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/cache/xoopscache.php';

        // Create a concrete subclass for testing the abstract class
        $this->engine = new class extends XoopsCacheEngine {
            public function write($key, $value, $duration = null) { return true; }
            public function read($key) { return false; }
        };
    }

    // ---------------------------------------------------------------
    // Interface implementation test
    // ---------------------------------------------------------------

    #[Test]
    public function implementsCacheEngineInterface(): void
    {
        if (!interface_exists('XoopsCacheEngineInterface', false)) {
            $this->markTestSkipped('XoopsCacheEngineInterface not available (XOOPS 2.6 only)');
        }
        $this->assertInstanceOf(XoopsCacheEngineInterface::class, $this->engine);
    }

    // ---------------------------------------------------------------
    // init() tests
    // ---------------------------------------------------------------

    #[Test]
    public function initReturnsTrue(): void
    {
        $this->assertTrue($this->engine->init());
    }

    #[Test]
    public function initSetsDefaultSettings(): void
    {
        $this->engine->init();
        $settings = $this->engine->settings();
        $this->assertArrayHasKey('duration', $settings);
        $this->assertArrayHasKey('probability', $settings);
        $this->assertSame(31556926, $settings['duration']);
        $this->assertSame(100, $settings['probability']);
    }

    #[Test]
    public function initMergesCustomSettings(): void
    {
        $this->engine->init(['duration' => 3600, 'custom' => 'value']);
        $settings = $this->engine->settings();
        $this->assertSame(3600, $settings['duration']);
        $this->assertSame('value', $settings['custom']);
        $this->assertSame(100, $settings['probability']); // default preserved
    }

    #[Test]
    public function initCanOverrideProbability(): void
    {
        $this->engine->init(['probability' => 50]);
        $settings = $this->engine->settings();
        $this->assertSame(50, $settings['probability']);
    }

    #[Test]
    public function initCanBeCalledMultipleTimes(): void
    {
        $this->engine->init(['duration' => 100]);
        $this->engine->init(['duration' => 200]);
        $this->assertSame(200, $this->engine->settings()['duration']);
    }

    // ---------------------------------------------------------------
    // settings() tests
    // ---------------------------------------------------------------

    #[Test]
    public function settingsReturnsArray(): void
    {
        $this->engine->init();
        $this->assertIsArray($this->engine->settings());
    }

    #[Test]
    public function settingsReturnsCurrentSettings(): void
    {
        $this->engine->init(['test_key' => 'test_value']);
        $settings = $this->engine->settings();
        $this->assertSame('test_value', $settings['test_key']);
    }

    // ---------------------------------------------------------------
    // gc() tests
    // ---------------------------------------------------------------

    #[Test]
    public function gcDoesNotThrow(): void
    {
        $this->engine->gc();
        $this->assertTrue(true);
    }

    // ---------------------------------------------------------------
    // delete() tests
    // ---------------------------------------------------------------

    #[Test]
    public function deleteReturnsNull(): void
    {
        $result = $this->engine->delete('key');
        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // clear() tests
    // ---------------------------------------------------------------

    #[Test]
    public function clearReturnsNull(): void
    {
        $result = $this->engine->clear(true);
        $this->assertNull($result);
    }
}
