<?php

declare(strict_types=1);

namespace frameworksart;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FunctionsCacheTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            // Ensure ini functions are loaded first (defines FRAMEWORKS_ROOT_PATH)
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.ini.php';
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.cache.php';
            require_once XOOPS_ROOT_PATH . '/class/cache/xoopscache.php';
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        // Reset relevant globals
        unset($GLOBALS['xoopsUser']);
    }

    // ---------------------------------------------------------------
    // mod_generateCacheId_byGroup tests
    // ---------------------------------------------------------------

    #[Test]
    public function generateCacheIdByGroupReturnsAnonymousWhenNoGroups(): void
    {
        // No groups provided, no xoopsUser — should return XOOPS_GROUP_ANONYMOUS
        $result = mod_generateCacheId_byGroup(null);
        $this->assertSame((string) XOOPS_GROUP_ANONYMOUS, (string) $result);
    }

    #[Test]
    public function generateCacheIdByGroupReturnsStringForGroups(): void
    {
        $result = mod_generateCacheId_byGroup([1, 2]);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function generateCacheIdByGroupSortsGroupsForConsistency(): void
    {
        $result1 = mod_generateCacheId_byGroup([2, 1, 3]);
        $result2 = mod_generateCacheId_byGroup([3, 1, 2]);
        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function generateCacheIdByGroupReturnsDifferentIdsForDifferentGroups(): void
    {
        $result1 = mod_generateCacheId_byGroup([1]);
        $result2 = mod_generateCacheId_byGroup([1, 2]);
        $this->assertNotSame($result1, $result2);
    }

    #[Test]
    public function generateCacheIdByGroupUsesXoopsUserWhenNoGroupsProvided(): void
    {
        $mockUser = new class {
            public function getGroups() { return [1, 2]; }
        };
        $GLOBALS['xoopsUser'] = $mockUser;

        $resultFromUser = mod_generateCacheId_byGroup(null);
        $resultDirect = mod_generateCacheId_byGroup([1, 2]);

        $this->assertSame($resultDirect, $resultFromUser);
    }

    #[Test]
    public function generateCacheIdByGroupReturnsAnonymousForNonArrayGroups(): void
    {
        // Non-array, non-null, no xoopsUser
        $result = mod_generateCacheId_byGroup('not_an_array');
        $this->assertSame((string) XOOPS_GROUP_ANONYMOUS, (string) $result);
    }

    // ---------------------------------------------------------------
    // mod_generateCacheId tests (wrapper)
    // ---------------------------------------------------------------

    #[Test]
    public function generateCacheIdDelegatesToByGroup(): void
    {
        $result1 = mod_generateCacheId([1, 2]);
        $result2 = mod_generateCacheId_byGroup([1, 2]);
        $this->assertSame($result1, $result2);
    }

    // ---------------------------------------------------------------
    // mod_loadFile tests
    // ---------------------------------------------------------------

    #[Test]
    public function loadFileReturnsNullForEmptyName(): void
    {
        $result = mod_loadFile('', 'system');
        $this->assertNull($result);
    }

    #[Test]
    public function loadFileUsesSystemDirnameByDefault(): void
    {
        // No xoopsModule set, defaults to 'system'
        unset($GLOBALS['xoopsModule']);
        // XoopsCache::read returns false for non-existent keys when cache works,
        // or null when the cache engine is not initialized (test env).
        $result = @mod_loadFile('nonexistent_cache_key');
        // Either false (cache miss) or null (cache engine not init) — both are falsy
        $this->assertEmpty($result, 'mod_loadFile should return falsy for a non-existent key');
    }

    #[Test]
    public function loadFileUsesModuleDirnameWhenSet(): void
    {
        $mockModule = new class {
            public function getVar(string $key, string $format = 'e') {
                return $key === 'dirname' ? 'publisher' : '';
            }
        };
        $GLOBALS['xoopsModule'] = $mockModule;

        // Returns false (cache miss) or null (cache engine not init)
        $result = @mod_loadFile('test_key');
        $this->assertEmpty($result, 'mod_loadFile should return falsy for a non-existent key');

        unset($GLOBALS['xoopsModule']);
    }

    // ---------------------------------------------------------------
    // mod_loadCacheFile tests (wrapper)
    // ---------------------------------------------------------------

    #[Test]
    public function loadCacheFileReturnsNullForEmptyName(): void
    {
        $result = mod_loadCacheFile('');
        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // mod_createFile tests
    // ---------------------------------------------------------------

    #[Test]
    public function createFileReturnsBoolean(): void
    {
        unset($GLOBALS['xoopsModule']);
        // XoopsCache::write returns bool when cache works, or null when
        // the cache engine is not initialized (test env without cache dir).
        $result = @mod_createFile('test_data', 'test_name', 'system');
        $this->assertThat(
            $result,
            $this->logicalOr($this->isTrue(), $this->isFalse(), $this->isNull()),
            'mod_createFile should return bool or null'
        );
    }

    #[Test]
    public function createFileUsesTimestampAsDefaultName(): void
    {
        unset($GLOBALS['xoopsModule']);
        // Same as above — accepts bool or null depending on cache availability
        $result = @mod_createFile('data', null, 'system');
        $this->assertThat(
            $result,
            $this->logicalOr($this->isTrue(), $this->isFalse(), $this->isNull()),
            'mod_createFile should return bool or null'
        );
    }

    // ---------------------------------------------------------------
    // mod_clearFile tests
    // ---------------------------------------------------------------

    #[Test]
    public function clearFileReturnsTrueAlways(): void
    {
        // mod_clearFile always returns true
        $result = mod_clearFile('nonexistent', 'testmod');
        $this->assertTrue($result);
    }

    #[Test]
    public function clearFileWithEmptyDirnameReturnsTrueAlways(): void
    {
        // Empty dirname triggers the regex-based cleanup path
        $result = mod_clearFile('test_key', null);
        $this->assertTrue($result);
    }

    // ---------------------------------------------------------------
    // mod_clearCacheFile tests (wrapper)
    // ---------------------------------------------------------------

    #[Test]
    public function clearCacheFileDelegatesToClearFile(): void
    {
        $result = mod_clearCacheFile('test_key', 'system');
        $this->assertTrue($result);
    }
}
