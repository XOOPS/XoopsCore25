<?php

declare(strict_types=1);

namespace xoopscache;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsCache;

#[CoversClass(XoopsCache::class)]
class XoopsCacheKeyTest extends TestCase
{
    private XoopsCache $cache;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/cache/xoopscache.php';
        $this->cache = new XoopsCache();
    }

    // ---------------------------------------------------------------
    // key() tests
    // ---------------------------------------------------------------

    #[Test]
    public function keyReturnsFalseForEmptyString(): void
    {
        $this->assertFalse($this->cache->key(''));
    }

    #[Test]
    public function keyReturnsFalseForNull(): void
    {
        $this->assertFalse($this->cache->key(null));
    }

    #[Test]
    public function keyReturnsFalseForZero(): void
    {
        // empty(0) is true in PHP
        $this->assertFalse($this->cache->key(0));
    }

    #[Test]
    public function keyReturnsStringForValidInput(): void
    {
        $result = $this->cache->key('test_key');
        $this->assertIsString($result);
        $this->assertSame('test_key', $result);
    }

    #[Test]
    public function keyReplacesSlashWithUnderscore(): void
    {
        $result = $this->cache->key('path/to/key');
        $this->assertSame('path_to_key', $result);
    }

    #[Test]
    public function keyReplacesDotWithUnderscore(): void
    {
        $result = $this->cache->key('file.cache');
        $this->assertSame('file_cache', $result);
    }

    #[Test]
    public function keyReplacesMultipleSpecialChars(): void
    {
        $result = $this->cache->key('a/b.c/d.e');
        $this->assertSame('a_b_c_d_e', $result);
    }

    #[Test]
    public function keyPreservesUnderscores(): void
    {
        $result = $this->cache->key('already_safe_key');
        $this->assertSame('already_safe_key', $result);
    }

    #[Test]
    public function keyPreservesDashes(): void
    {
        $result = $this->cache->key('key-with-dashes');
        $this->assertSame('key-with-dashes', $result);
    }

    // ---------------------------------------------------------------
    // isInitialized() tests
    // ---------------------------------------------------------------

    #[Test]
    public function isInitializedReturnsFalseWhenNoEngineSet(): void
    {
        $cache = new XoopsCache();
        $this->assertFalse($cache->isInitialized('nonexistent'));
    }

    #[Test]
    public function isInitializedReturnsBoolForNullEngine(): void
    {
        $cache = new XoopsCache();
        $result = $cache->isInitialized(null);
        $this->assertIsBool($result);
    }

    // ---------------------------------------------------------------
    // settings() tests
    // ---------------------------------------------------------------

    #[Test]
    public function settingsReturnsArrayWhenNoEngine(): void
    {
        $cache = new XoopsCache();
        $result = $cache->settings('nonexistent');
        $this->assertIsArray($result);
    }

    #[Test]
    public function settingsReturnsArrayForNullEngine(): void
    {
        $cache = new XoopsCache();
        $result = $cache->settings(null);
        $this->assertIsArray($result);
    }

    // ---------------------------------------------------------------
    // getInstance tests
    // ---------------------------------------------------------------

    #[Test]
    public function getInstanceReturnsSingleton(): void
    {
        $a = XoopsCache::getInstance();
        $b = XoopsCache::getInstance();
        $this->assertSame($a, $b);
    }

    #[Test]
    public function getInstanceReturnsXoopsCacheType(): void
    {
        $this->assertInstanceOf(XoopsCache::class, XoopsCache::getInstance());
    }
}
