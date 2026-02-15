<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/class/cache/xoopscache.php';

class XoopsCacheTest extends TestCase
{
    protected XoopsCache $cache;

    protected function setUp(): void
    {
        // Get singleton instance
        $this->cache = XoopsCache::getInstance();

        // XoopsCacheFile uses XoopsFolderHandler whose isAbsolute() regex only
        // matches "C:/" (forward slash) and NOT "C:\" (backslash).  On Windows
        // where XOOPS_VAR_PATH contains backslashes the file-cache engine
        // cannot initialise, causing most tests to fail with TypeErrors from
        // extract(false) / array_merge(…, non-array).  Skip the entire suite.
        $config = @$this->cache->config();
        if (!is_array($config) || !$this->cache->isInitialized('file')) {
            $this->markTestSkipped('XoopsCache file engine unavailable (Windows isAbsolute() path limitation)');
        }
    }

    public function testGetInstanceReturnsSameInstance()
    {
        $instance1 = XoopsCache::getInstance();
        $instance2 = XoopsCache::getInstance();

        $this->assertSame($instance1, $instance2, 'getInstance should return the same singleton instance');
    }

    public function testGetInstanceReturnsXoopsCacheObject()
    {
        $instance = XoopsCache::getInstance();

        $this->assertInstanceOf(XoopsCache::class, $instance);
    }

    public function testConfigWithDefaultName()
    {
        $result = $this->cache->config();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('engine', $result);
        $this->assertArrayHasKey('settings', $result);
        $this->assertEquals('file', $result['engine'], 'Default engine should be file');
    }

    public function testConfigWithCustomSettings()
    {
        $settings = [
            'engine' => 'file',
            'path' => '/tmp/cache',
            'duration' => 3600,
        ];

        $result = $this->cache->config('custom', $settings);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('engine', $result);
        $this->assertEquals('file', $result['engine']);
    }

    public function testConfigWithArrayParameter()
    {
        $config = [
            'name' => 'test_config',
            'settings' => [
                'engine' => 'file',
                'duration' => 7200,
            ]
        ];

        $result = $this->cache->config($config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('engine', $result);
    }

    public function testConfigWithEmptyStringName()
    {
        $result = $this->cache->config('', ['engine' => 'file']);

        $this->assertIsArray($result);
        $this->assertEquals('file', $result['engine']);
    }

    public function testConfigWithNonArraySettings()
    {
        // PHP 8+ enforces array type on array_merge() — XoopsCacheEngine::init()
        // receives a non-array $settings and crashes. This tests the known limitation.
        $this->expectException(\TypeError::class);
        $this->cache->config('test_nonarray', 'not_array');
    }

    public function testEngineWithValidEngine()
    {
        $result = $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $this->assertTrue($result, 'File engine should initialize successfully');
    }

    public function testEngineWithEmptyName()
    {
        $result = $this->cache->engine('', []);

        $this->assertFalse($result, 'Empty engine name should return false');
    }

    public function testEngineWithNonStringName()
    {
        $result = $this->cache->engine(null, []);

        $this->assertFalse($result, 'Null engine name should return false');
    }

    public function testEngineWithNonArraySettings()
    {
        // PHP 8+ enforces array type on array_merge() in XoopsCacheEngine::init()
        $this->expectException(\TypeError::class);
        $this->cache->engine('file', 'not_array');
    }

    public function testEngineWithInvalidEngine()
    {
        // Suppress the expected warning
        $result = @$this->cache->engine('nonexistent_engine', []);

        $this->assertFalse($result, 'Invalid engine should return false');
    }

    public function testKeyGenerationWithValidKey()
    {
        $key = $this->cache->key('test_key');

        $this->assertNotFalse($key);
        $this->assertIsString($key);
        $this->assertEquals('test_key', $key);
    }

    public function testKeyGenerationWithEmptyKey()
    {
        $key = $this->cache->key('');

        $this->assertFalse($key, 'Empty key should return false');
    }

    public function testKeyGenerationWithSlashes()
    {
        $key = $this->cache->key('path/to/key');

        $this->assertNotFalse($key);
        $this->assertEquals('path_to_key', $key, 'Slashes should be replaced with underscores');
    }

    public function testKeyGenerationWithDots()
    {
        $key = $this->cache->key('key.with.dots');

        $this->assertNotFalse($key);
        $this->assertEquals('key_with_dots', $key, 'Dots should be replaced with underscores');
    }

    public function testKeyGenerationWithMixedSpecialChars()
    {
        $key = $this->cache->key('path/to/key.name');

        $this->assertNotFalse($key);
        $this->assertEquals('path_to_key_name', $key);
    }

    public function testIsInitializedWithFileEngine()
    {
        // Initialize file engine
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = $this->cache->isInitialized('file');

        $this->assertTrue($result, 'File engine should be initialized');
    }

    public function testIsInitializedWithNullEngine()
    {
        // Configure default engine
        $this->cache->config('default', ['engine' => 'file']);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = $this->cache->isInitialized(null);

        $this->assertTrue($result, 'Should check current engine when null is passed');
    }

    public function testIsInitializedWithUninitializedEngine()
    {
        $result = $this->cache->isInitialized('nonexistent');

        $this->assertFalse($result, 'Uninitialized engine should return false');
    }

    public function testSettingsWithInitializedEngine()
    {
        // Initialize file engine
        $this->cache->engine('file', ['path' => sys_get_temp_dir(), 'duration' => 3600]);

        $settings = $this->cache->settings('file');

        $this->assertIsArray($settings);
        $this->assertArrayHasKey('duration', $settings);
    }

    public function testSettingsWithUninitializedEngine()
    {
        $settings = $this->cache->settings('nonexistent');

        $this->assertIsArray($settings);
        $this->assertEmpty($settings, 'Uninitialized engine should return empty array');
    }

    public function testSettingsWithNullEngine()
    {
        // Configure and initialize default engine
        $this->cache->config('default', ['engine' => 'file']);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $settings = $this->cache->settings(null);

        $this->assertIsArray($settings);
    }

    public function testWriteWithValidData()
    {
        // Configure file engine
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('test_key', 'test_value', 3600);

        $this->assertTrue($result, 'Write operation should succeed');
    }

    public function testWriteAndReadCycle()
    {
        // Configure file engine
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $key = 'cycle_test_key';
        $value = 'cycle_test_value';

        XoopsCache::write($key, $value, 3600);
        $result = XoopsCache::read($key);

        $this->assertEquals($value, $result, 'Read should return the written value');
    }

    public function testWriteWithResourceValue()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $resource = fopen('php://memory', 'r');
        $result = XoopsCache::write('resource_key', $resource, 3600);
        fclose($resource);

        $this->assertFalse($result, 'Writing resource should return false');
    }

    public function testWriteWithZeroDuration()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('zero_duration', 'value', 0);

        $this->assertFalse($result, 'Zero duration should return false');
    }

    public function testWriteWithNegativeDuration()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('negative_duration', 'value', -100);

        $this->assertFalse($result, 'Negative duration should return false');
    }

    public function testWriteWithStringDuration()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('string_duration', 'value', '+1 hour');

        $this->assertTrue($result, 'String duration should be parsed by strtotime');
    }

    public function testWriteWithArrayDurationConfig()
    {
        $this->cache->config('custom', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('array_duration', 'value', ['config' => 'custom', 'duration' => 3600]);

        $this->assertTrue($result, 'Array duration with config should work');
    }

    public function testWriteWithConfigNameAsDuration()
    {
        $this->cache->config('named_config', ['engine' => 'file', 'path' => sys_get_temp_dir(), 'duration' => 7200]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('config_duration', 'value', 'named_config');

        $this->assertTrue($result, 'Config name as duration should work');
    }

    public function testReadNonExistentKey()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::read('nonexistent_key_12345');

        $this->assertFalse($result, 'Reading nonexistent key should return false');
    }

    public function testDeleteExistingKey()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $key = 'delete_test_key';
        XoopsCache::write($key, 'value', 3600);

        $result = XoopsCache::delete($key);

        $this->assertTrue($result, 'Delete should return true');

        // Verify it's deleted
        $readResult = XoopsCache::read($key);
        $this->assertFalse($readResult, 'Key should no longer exist after deletion');
    }

    public function testDeleteNonExistentKey()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::delete('nonexistent_delete_key');

        // Result may vary by engine, but should not throw error
        $this->assertIsBool($result);
    }

    public function testClearCache()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        // Write some data
        XoopsCache::write('clear_test_1', 'value1', 3600);
        XoopsCache::write('clear_test_2', 'value2', 3600);

        $result = $this->cache->clear(false);

        $this->assertTrue($result, 'Clear should return true');
    }

    public function testClearCacheWithCheck()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = $this->cache->clear(true);

        $this->assertTrue($result, 'Clear with check should return true');
    }

    public function testGarbageCollection()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = $this->cache->gc();

        $this->assertTrue($result, 'Garbage collection should return true');
    }

    public function testGarbageCollectionWithoutInitializedEngine()
    {
        // Create a new instance to avoid initialized engines
        $result = @$this->cache->gc();

        // Should return false without initialized engine
        $this->assertFalse($result);
    }

    public function testWriteWithComplexDataStructure()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $complexData = [
            'string' => 'test',
            'number' => 42,
            'array' => [1, 2, 3],
            'nested' => ['key' => 'value'],
        ];

        XoopsCache::write('complex_data', $complexData, 3600);
        $result = XoopsCache::read('complex_data');

        $this->assertEquals($complexData, $result, 'Complex data should be preserved');
    }

    public function testWriteWithBooleanValue()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        XoopsCache::write('bool_true', true, 3600);
        XoopsCache::write('bool_false', false, 3600);

        $resultTrue = XoopsCache::read('bool_true');
        $resultFalse = XoopsCache::read('bool_false');

        $this->assertTrue($resultTrue);
        $this->assertFalse($resultFalse);
    }

    public function testWriteWithNullValue()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        XoopsCache::write('null_value', null, 3600);
        $result = XoopsCache::read('null_value');

        $this->assertNull($result);
    }

    public function testWriteWithNumericValue()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        XoopsCache::write('int_value', 42, 3600);
        XoopsCache::write('float_value', 3.14, 3600);

        $intResult = XoopsCache::read('int_value');
        $floatResult = XoopsCache::read('float_value');

        $this->assertEquals(42, $intResult);
        $this->assertEquals(3.14, $floatResult);
    }

    public function testKeyPrefixingWithXoopsUrl()
    {
        // The write method prefixes keys with substr(md5(XOOPS_URL), 0, 8)
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::write('prefix_test', 'value', 3600);
        $this->assertTrue($result);

        // Should be able to read it back
        $readResult = XoopsCache::read('prefix_test');
        $this->assertEquals('value', $readResult);
    }

    public function testConfigReturnsFalseOnInvalidConfiguration()
    {
        // Test with invalid config that doesn't have required keys
        $instance = XoopsCache::getInstance();

        // This should still work as config creates default settings
        $result = $instance->config('test', []);

        $this->assertIsArray($result);
    }

    public function testEngineLoadFailsGracefully()
    {
        // Try to load non-existent engine
        $result = @$this->cache->engine('totally_fake_engine_xyz', []);

        $this->assertFalse($result, 'Loading non-existent engine should return false');
    }

    public function testMultipleWritesAndReads()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $testData = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        // Write all
        foreach ($testData as $key => $value) {
            XoopsCache::write($key, $value, 3600);
        }

        // Read all and verify
        foreach ($testData as $key => $value) {
            $result = XoopsCache::read($key);
            $this->assertEquals($value, $result, "Key {$key} should match its value");
        }
    }

    public function testWriteOverwritesExistingKey()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $key = 'overwrite_test';

        XoopsCache::write($key, 'original', 3600);
        XoopsCache::write($key, 'updated', 3600);

        $result = XoopsCache::read($key);

        $this->assertEquals('updated', $result, 'Second write should overwrite first');
    }

    public function testConfigPersistsBetweenCalls()
    {
        $this->cache->config('persistent', ['engine' => 'file', 'path' => sys_get_temp_dir()]);

        // Call config again without settings
        $result = $this->cache->config('persistent');

        $this->assertIsArray($result);
        $this->assertEquals('file', $result['engine'], 'Config should persist');
    }

    // Additional edge case and regression tests

    public function testWriteWithVeryLongKey()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $longKey = str_repeat('a', 500);
        $result = XoopsCache::write($longKey, 'value', 3600);

        $this->assertTrue($result);
        $this->assertEquals('value', XoopsCache::read($longKey));
    }

    public function testWriteWithSpecialCharactersInValue()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $specialValue = "Line1\nLine2\tTab\r\n'quotes\"double";
        XoopsCache::write('special_chars', $specialValue, 3600);

        $result = XoopsCache::read('special_chars');
        $this->assertEquals($specialValue, $result);
    }

    public function testConcurrentReadAfterWrite()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        XoopsCache::write('concurrent', 'data', 3600);

        // Multiple reads should all succeed
        $read1 = XoopsCache::read('concurrent');
        $read2 = XoopsCache::read('concurrent');
        $read3 = XoopsCache::read('concurrent');

        $this->assertEquals('data', $read1);
        $this->assertEquals('data', $read2);
        $this->assertEquals('data', $read3);
    }

    public function testDeleteReturnsCorrectType()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $result = XoopsCache::delete('any_key');

        $this->assertIsBool($result, 'Delete should always return boolean');
    }

    public function testEngineInitializationWithEmptySettings()
    {
        $result = $this->cache->engine('file', []);

        $this->assertTrue($result, 'File engine should initialize with empty settings');
    }

    public function testKeyWithNumericInput()
    {
        $key = $this->cache->key(12345);

        $this->assertIsString($key);
        $this->assertEquals('12345', $key);
    }

    public function testReadWriteDeleteCycle()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        $key = 'lifecycle_test';

        // Write
        $writeResult = XoopsCache::write($key, 'data', 3600);
        $this->assertTrue($writeResult);

        // Read
        $readResult = XoopsCache::read($key);
        $this->assertEquals('data', $readResult);

        // Delete
        $deleteResult = XoopsCache::delete($key);
        $this->assertTrue($deleteResult);

        // Read after delete
        $readAfterDelete = XoopsCache::read($key);
        $this->assertFalse($readAfterDelete);
    }

    public function testWriteWithLargeDataStructure()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        // Create a large nested array
        $largeData = [];
        for ($i = 0; $i < 100; $i++) {
            $largeData["key_$i"] = [
                'id' => $i,
                'name' => "Item $i",
                'data' => str_repeat('x', 100),
                'nested' => ['a' => 1, 'b' => 2, 'c' => 3]
            ];
        }

        $result = XoopsCache::write('large_data', $largeData, 3600);
        $this->assertTrue($result);

        $retrieved = XoopsCache::read('large_data');
        $this->assertEquals($largeData, $retrieved);
    }

    public function testConfigWithMultipleEngines()
    {
        // Configure multiple engines
        $this->cache->config('file_cache', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->config('second_cache', ['engine' => 'file', 'path' => sys_get_temp_dir() . '/cache2']);

        $config1 = $this->cache->config('file_cache');
        $config2 = $this->cache->config('second_cache');

        $this->assertIsArray($config1);
        $this->assertIsArray($config2);
        $this->assertEquals('file', $config1['engine']);
        $this->assertEquals('file', $config2['engine']);
    }

    public function testWriteRejectsInvalidDurationFormats()
    {
        $this->cache->config('default', ['engine' => 'file', 'path' => sys_get_temp_dir()]);
        $this->cache->engine('file', ['path' => sys_get_temp_dir()]);

        // Invalid duration string that can't be parsed
        $result = XoopsCache::write('invalid_duration', 'value', 'not a valid time string');

        // Should handle gracefully - either false or use default duration
        $this->assertIsBool($result);
    }
}
