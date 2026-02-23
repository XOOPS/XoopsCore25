<?php

declare(strict_types=1);

namespace kernel;

use Criteria;
use CriteriaCompo;
use CriteriaElement;
use XoopsConfigHandler;
use XoopsConfigItem;
use XoopsConfigItemHandler;
use XoopsConfigOption;
use XoopsConfigOptionHandler;

require_once XOOPS_ROOT_PATH . '/kernel/configoption.php';
require_once XOOPS_ROOT_PATH . '/kernel/configitem.php';

// Set up $GLOBALS['xoops'] before loading config.php, which calls $GLOBALS['xoops']->path()
if (!isset($GLOBALS['xoops'])) {
    $GLOBALS['xoops'] = new class {
        public function path(string $path): string
        {
            return XOOPS_ROOT_PATH . '/' . $path;
        }
    };
}

require_once XOOPS_ROOT_PATH . '/kernel/config.php';

/**
 * Comprehensive unit tests for XoopsConfigHandler.
 *
 * XoopsConfigHandler is a facade that delegates to two sub-handlers:
 *   - _cHandler (XoopsConfigItemHandler) for config items
 *   - _oHandler (XoopsConfigOptionHandler) for config options
 *
 * Tests verify correct delegation, caching behavior, option handling
 * during insert/delete, and edge cases.
 */
class XoopsConfigHandlerTest extends KernelTestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|\XoopsMySQLDatabase */
    private $db;

    /** @var XoopsConfigHandler */
    private $handler;

    /** @var XoopsConfigItemHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $mockCHandler;

    /** @var XoopsConfigOptionHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $mockOHandler;

    protected function setUp(): void
    {
        $this->db = $this->createMockDatabase();

        // Create the handler with the real constructor (which creates real sub-handlers)
        $this->handler = new XoopsConfigHandler($this->db);

        // Create mock sub-handlers to replace the real ones
        $this->mockCHandler = $this->createMock(XoopsConfigItemHandler::class);
        $this->mockOHandler = $this->createMock(XoopsConfigOptionHandler::class);

        // Inject mock sub-handlers via the public properties
        $this->setProtectedProperty($this->handler, '_cHandler', $this->mockCHandler);
        $this->setProtectedProperty($this->handler, '_oHandler', $this->mockOHandler);

        // Reset the cached configs
        $this->setProtectedProperty($this->handler, '_cachedConfigs', []);
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesBothSubHandlers(): void
    {
        $handler = new XoopsConfigHandler($this->db);

        $cHandler = $this->getProtectedProperty($handler, '_cHandler');
        $oHandler = $this->getProtectedProperty($handler, '_oHandler');

        self::assertInstanceOf(XoopsConfigItemHandler::class, $cHandler);
        self::assertInstanceOf(XoopsConfigOptionHandler::class, $oHandler);
    }

    public function testConstructorInitializesEmptyCache(): void
    {
        $handler = new XoopsConfigHandler($this->db);

        $cache = $this->getProtectedProperty($handler, '_cachedConfigs');

        self::assertIsArray($cache);
        self::assertEmpty($cache);
    }

    // =========================================================================
    // createConfig
    // =========================================================================

    public function testCreateConfigDelegatesToCHandler(): void
    {
        $config = new XoopsConfigItem();
        $this->mockCHandler->expects(self::once())
            ->method('create')
            ->willReturn($config);

        $result = $this->handler->createConfig();

        self::assertInstanceOf(XoopsConfigItem::class, $result);
        self::assertSame($config, $result);
    }

    public function testCreateConfigReturnsNewConfigItem(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();

        $this->mockCHandler->expects(self::once())
            ->method('create')
            ->willReturn($config);

        $result = $this->handler->createConfig();

        self::assertTrue($result->isNew());
    }

    // =========================================================================
    // getConfig - without options
    // =========================================================================

    public function testGetConfigWithoutOptionsReturnConfigItem(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);
        $config->assignVar('conf_name', 'sitename');

        $this->mockCHandler->expects(self::once())
            ->method('get')
            ->with(5)
            ->willReturn($config);

        $result = $this->handler->getConfig(5, false);

        self::assertInstanceOf(XoopsConfigItem::class, $result);
        self::assertEquals(5, $result->getVar('conf_id'));
        self::assertEquals('sitename', $result->getVar('conf_name'));
    }

    public function testGetConfigDefaultsWithoutOptions(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 3);

        $this->mockCHandler->expects(self::once())
            ->method('get')
            ->with(3)
            ->willReturn($config);

        // Second parameter defaults to false
        $result = $this->handler->getConfig(3);

        self::assertInstanceOf(XoopsConfigItem::class, $result);
        // Options should not have been loaded
        $options = &$result->getConfOptions();
        self::assertEmpty($options);
    }

    public function testGetConfigDoesNotCallOHandlerWhenWithoptionsFalse(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 1);

        $this->mockCHandler->method('get')->willReturn($config);

        // The option handler should never be called
        $this->mockOHandler->expects(self::never())
            ->method('getObjects');

        $this->handler->getConfig(1, false);
    }

    // =========================================================================
    // getConfig - with options
    // =========================================================================

    public function testGetConfigWithOptionsLoadsOptions(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);

        $opt1 = new XoopsConfigOption();
        $opt1->assignVars(['confop_id' => 1, 'confop_name' => 'Yes', 'confop_value' => '1', 'conf_id' => 5]);
        $opt2 = new XoopsConfigOption();
        $opt2->assignVars(['confop_id' => 2, 'confop_name' => 'No', 'confop_value' => '0', 'conf_id' => 5]);

        $this->mockCHandler->expects(self::once())
            ->method('get')
            ->with(5)
            ->willReturn($config);

        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$opt1, $opt2]);

        $result = $this->handler->getConfig(5, true);

        $options = &$result->getConfOptions();
        self::assertCount(2, $options);
    }

    public function testGetConfigWithOptionsPassesCriteriaWithConfId(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 7);

        $this->mockCHandler->method('get')->willReturn($config);

        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->with(self::callback(function ($criteria) {
                return $criteria instanceof Criteria;
            }))
            ->willReturn([]);

        $this->handler->getConfig(7, true);
    }

    public function testGetConfigWithOptionsAndEmptyOptionsList(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 10);

        $this->mockCHandler->method('get')->willReturn($config);
        $this->mockOHandler->method('getObjects')->willReturn([]);

        $result = $this->handler->getConfig(10, true);

        $options = &$result->getConfOptions();
        self::assertEmpty($options);
    }

    // =========================================================================
    // insertConfig - success cases
    // =========================================================================

    public function testInsertConfigSuccessWithNoOptions(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->setVar('conf_modid', 0);
        $config->setVar('conf_catid', 1);
        $config->setVar('conf_name', 'sitename');
        $config->assignVar('conf_id', 10);

        $this->mockCHandler->expects(self::once())
            ->method('insert')
            ->with($config)
            ->willReturn(true);

        $result = $this->handler->insertConfig($config);

        self::assertTrue($result);
    }

    public function testInsertConfigWithSingleOption(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 10);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 1);

        $opt = new XoopsConfigOption();
        $opt->setNew();
        $opt->setVar('confop_name', 'Yes');
        $opt->setVar('confop_value', '1');
        $config->setConfOptions($opt);

        $this->mockCHandler->expects(self::once())
            ->method('insert')
            ->with($config)
            ->willReturn(true);

        $this->mockOHandler->expects(self::once())
            ->method('insert')
            ->willReturn(1);

        $result = $this->handler->insertConfig($config);

        self::assertTrue($result);
    }

    public function testInsertConfigWithMultipleOptions(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 15);
        $config->assignVar('conf_modid', 1);
        $config->assignVar('conf_catid', 0);

        $opt1 = new XoopsConfigOption();
        $opt1->setNew();
        $opt1->setVar('confop_name', 'Option A');
        $opt1->setVar('confop_value', 'a');

        $opt2 = new XoopsConfigOption();
        $opt2->setNew();
        $opt2->setVar('confop_name', 'Option B');
        $opt2->setVar('confop_value', 'b');

        $opt3 = new XoopsConfigOption();
        $opt3->setNew();
        $opt3->setVar('confop_name', 'Option C');
        $opt3->setVar('confop_value', 'c');

        $config->setConfOptions([$opt1, $opt2, $opt3]);

        $this->mockCHandler->method('insert')->willReturn(true);

        $this->mockOHandler->expects(self::exactly(3))
            ->method('insert')
            ->willReturn(1);

        $result = $this->handler->insertConfig($config);

        self::assertTrue($result);
    }

    public function testInsertConfigSetsConfIdOnOptions(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 42);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 0);

        $opt = new XoopsConfigOption();
        $opt->setNew();
        $opt->setVar('confop_name', 'TestOpt');
        $opt->setVar('confop_value', 'val');
        $config->setConfOptions($opt);

        $this->mockCHandler->method('insert')->willReturn(true);

        $capturedOption = null;
        $this->mockOHandler->expects(self::once())
            ->method('insert')
            ->willReturnCallback(function ($option) use (&$capturedOption) {
                $capturedOption = $option;
                return 1;
            });

        $this->handler->insertConfig($config);

        // Verify conf_id was set on the option before inserting
        self::assertEquals(42, $capturedOption->getVar('conf_id'));
    }

    // =========================================================================
    // insertConfig - failure cases
    // =========================================================================

    public function testInsertConfigFailureWhenCHandlerFails(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();

        $this->mockCHandler->expects(self::once())
            ->method('insert')
            ->with($config)
            ->willReturn(false);

        // Option handler should never be called if config insert fails
        $this->mockOHandler->expects(self::never())
            ->method('insert');

        $result = $this->handler->insertConfig($config);

        self::assertFalse($result);
    }

    public function testInsertConfigStillReturnsTrueWhenOptionInsertFails(): void
    {
        // insertConfig returns true even if option insert fails, but logs errors
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 20);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 0);

        $opt = new XoopsConfigOption();
        $opt->setNew();
        $opt->setVar('confop_name', 'BadOpt');
        $opt->setVar('confop_value', 'bad');
        $config->setConfOptions($opt);

        $this->mockCHandler->method('insert')->willReturn(true);

        // Option insert fails (returns false/0)
        $this->mockOHandler->expects(self::once())
            ->method('insert')
            ->willReturn(false);

        $result = $this->handler->insertConfig($config);

        // The method still returns true - it logs option errors but doesn't fail overall
        self::assertTrue($result);
    }

    // =========================================================================
    // insertConfig - cache invalidation
    // =========================================================================

    public function testInsertConfigClearsCachedConfigsForMatchingModAndCat(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 10);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 1);

        // Pre-populate the cache with matching and non-matching entries
        $this->setProtectedProperty($this->handler, '_cachedConfigs', [
            0 => [
                1 => ['sitename' => 'XOOPS'],
                2 => ['slogan' => 'A CMS'],
            ],
            5 => [0 => ['theme' => 'default']],
        ]);

        $this->mockCHandler->method('insert')->willReturn(true);

        $this->handler->insertConfig($config);

        $cache = $this->getProtectedProperty($this->handler, '_cachedConfigs');
        // Only modid=0, catid=1 should be cleared
        self::assertArrayNotHasKey(1, $cache[0] ?? []);
        // Other cache entries should remain
        self::assertEquals('A CMS', $cache[0][2]['slogan']);
        self::assertEquals('default', $cache[5][0]['theme']);
    }

    public function testInsertConfigDoesNotClearCacheWhenNoMatchingEntry(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 10);
        $config->assignVar('conf_modid', 99);
        $config->assignVar('conf_catid', 88);

        // Cache has no entry for modid=99, catid=88
        $this->setProtectedProperty($this->handler, '_cachedConfigs', [
            0 => [1 => ['sitename' => 'XOOPS']],
        ]);

        $this->mockCHandler->method('insert')->willReturn(true);

        $this->handler->insertConfig($config);

        $cache = $this->getProtectedProperty($this->handler, '_cachedConfigs');
        // Existing cache should remain untouched
        self::assertEquals('XOOPS', $cache[0][1]['sitename']);
    }

    // =========================================================================
    // deleteConfig - success cases
    // =========================================================================

    public function testDeleteConfigSuccessNoPreloadedOptions(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 1);

        $this->mockCHandler->expects(self::once())
            ->method('delete')
            ->with($config)
            ->willReturn(true);

        // No options on the config item, so handler fetches them from DB
        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([]);

        $result = $this->handler->deleteConfig($config);

        self::assertTrue($result);
    }

    public function testDeleteConfigWithPreloadedOptionsDeletesThem(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 1);

        $opt1 = new XoopsConfigOption();
        $opt1->assignVar('confop_id', 10);
        $opt2 = new XoopsConfigOption();
        $opt2->assignVar('confop_id', 11);
        $config->setConfOptions([$opt1, $opt2]);

        $this->mockCHandler->expects(self::once())
            ->method('delete')
            ->willReturn(true);

        // Should delete each option individually
        $this->mockOHandler->expects(self::exactly(2))
            ->method('delete')
            ->willReturn(true);

        $result = $this->handler->deleteConfig($config);

        self::assertTrue($result);
    }

    public function testDeleteConfigFetchesOptionsFromDbWhenNonePreloaded(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 8);
        $config->assignVar('conf_modid', 2);
        $config->assignVar('conf_catid', 0);

        $opt = new XoopsConfigOption();
        $opt->assignVar('confop_id', 20);

        $this->mockCHandler->method('delete')->willReturn(true);

        // Since no options are preloaded, getConfigOptions is called which
        // delegates to _oHandler->getObjects
        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$opt]);

        $this->mockOHandler->expects(self::once())
            ->method('delete')
            ->with($opt)
            ->willReturn(true);

        $result = $this->handler->deleteConfig($config);

        self::assertTrue($result);
    }

    // =========================================================================
    // deleteConfig - failure cases
    // =========================================================================

    public function testDeleteConfigFailureWhenCHandlerFails(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);

        $this->mockCHandler->expects(self::once())
            ->method('delete')
            ->with($config)
            ->willReturn(false);

        // Options should not be touched if config delete fails
        $this->mockOHandler->expects(self::never())
            ->method('delete');
        $this->mockOHandler->expects(self::never())
            ->method('getObjects');

        $result = $this->handler->deleteConfig($config);

        self::assertFalse($result);
    }

    public function testDeleteConfigStillReturnsTrueEvenWhenOptionDeleteFails(): void
    {
        // deleteConfig does not check the return value of option deletes
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 0);

        $opt = new XoopsConfigOption();
        $opt->assignVar('confop_id', 1);
        $config->setConfOptions($opt);

        $this->mockCHandler->method('delete')->willReturn(true);

        // Option delete fails, but deleteConfig should still return true
        $this->mockOHandler->expects(self::once())
            ->method('delete')
            ->willReturn(false);

        $result = $this->handler->deleteConfig($config);

        self::assertTrue($result);
    }

    // =========================================================================
    // deleteConfig - cache invalidation
    // =========================================================================

    public function testDeleteConfigClearsCachedConfigsForMatchingModAndCat(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 1);

        // Pre-populate the cache
        $this->setProtectedProperty($this->handler, '_cachedConfigs', [
            0 => [1 => ['sitename' => 'XOOPS']],
        ]);

        $this->mockCHandler->method('delete')->willReturn(true);
        $this->mockOHandler->method('getObjects')->willReturn([]);

        $this->handler->deleteConfig($config);

        $cache = $this->getProtectedProperty($this->handler, '_cachedConfigs');
        self::assertArrayNotHasKey(1, $cache[0] ?? []);
    }

    public function testDeleteConfigPreservesCacheForOtherModCat(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);
        $config->assignVar('conf_modid', 1);
        $config->assignVar('conf_catid', 0);

        // Cache for a different module
        $this->setProtectedProperty($this->handler, '_cachedConfigs', [
            0 => [1 => ['sitename' => 'XOOPS']],
            1 => [0 => ['theme' => 'default']],
        ]);

        $this->mockCHandler->method('delete')->willReturn(true);
        $this->mockOHandler->method('getObjects')->willReturn([]);

        $this->handler->deleteConfig($config);

        $cache = $this->getProtectedProperty($this->handler, '_cachedConfigs');
        // modid=1, catid=0 should be cleared
        self::assertArrayNotHasKey(0, $cache[1] ?? []);
        // modid=0, catid=1 should remain
        self::assertEquals('XOOPS', $cache[0][1]['sitename']);
    }

    // =========================================================================
    // getConfigs
    // =========================================================================

    public function testGetConfigsDelegatesToCHandler(): void
    {
        $config1 = new XoopsConfigItem();
        $config1->assignVar('conf_id', 1);
        $config2 = new XoopsConfigItem();
        $config2->assignVar('conf_id', 2);

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$config1, $config2]);

        $result = $this->handler->getConfigs(null);

        self::assertCount(2, $result);
        self::assertInstanceOf(XoopsConfigItem::class, $result[0]);
        self::assertInstanceOf(XoopsConfigItem::class, $result[1]);
    }

    public function testGetConfigsWithCriteria(): void
    {
        $criteria = new CriteriaCompo(new Criteria('conf_modid', 0));

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->with($criteria, false)
            ->willReturn([]);

        $result = $this->handler->getConfigs($criteria);

        self::assertSame([], $result);
    }

    public function testGetConfigsWithIdAsKey(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->with(null, true)
            ->willReturn([5 => $config]);

        $result = $this->handler->getConfigs(null, true);

        self::assertArrayHasKey(5, $result);
    }

    public function testGetConfigsReturnsEmptyArrayWhenNone(): void
    {
        $this->mockCHandler->method('getObjects')->willReturn([]);

        $result = $this->handler->getConfigs(null);

        self::assertSame([], $result);
    }

    public function testGetConfigsPassesWithOptionsParameterButDoesNotUseIt(): void
    {
        // Note: getConfigs accepts $with_options but the current implementation
        // ignores it entirely (it is NOT passed to getObjects)
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->with(null, false)
            ->willReturn([]);

        $this->handler->getConfigs(null, false, true);
    }

    // =========================================================================
    // getConfigCount
    // =========================================================================

    public function testGetConfigCountDelegatesToCHandler(): void
    {
        $this->mockCHandler->expects(self::once())
            ->method('getCount')
            ->willReturn(7);

        $result = $this->handler->getConfigCount(null);

        self::assertSame(7, $result);
    }

    public function testGetConfigCountWithCriteria(): void
    {
        $criteria = new CriteriaCompo(new Criteria('conf_modid', 1));

        $this->mockCHandler->expects(self::once())
            ->method('getCount')
            ->with($criteria)
            ->willReturn(3);

        $result = $this->handler->getConfigCount($criteria);

        self::assertSame(3, $result);
    }

    public function testGetConfigCountReturnsZeroWhenEmpty(): void
    {
        $this->mockCHandler->method('getCount')->willReturn(0);

        $result = $this->handler->getConfigCount(null);

        self::assertSame(0, $result);
    }

    // =========================================================================
    // getConfigsByCat
    // =========================================================================

    public function testGetConfigsByCatReturnsNameValuePairs(): void
    {
        $config1 = new XoopsConfigItem();
        $config1->assignVar('conf_id', 1);
        $config1->assignVar('conf_name', 'sitename');
        $config1->assignVar('conf_value', 'XOOPS');
        $config1->assignVar('conf_valuetype', 'text');

        $config2 = new XoopsConfigItem();
        $config2->assignVar('conf_id', 2);
        $config2->assignVar('conf_name', 'slogan');
        $config2->assignVar('conf_value', 'Welcome');
        $config2->assignVar('conf_valuetype', 'text');

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$config1, $config2]);

        $result = &$this->handler->getConfigsByCat(1, 0);

        self::assertArrayHasKey('sitename', $result);
        self::assertEquals('XOOPS', $result['sitename']);
        self::assertArrayHasKey('slogan', $result);
        self::assertEquals('Welcome', $result['slogan']);
    }

    public function testGetConfigsByCatUsesStaticCache(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 1);
        $config->assignVar('conf_name', 'test');
        $config->assignVar('conf_value', 'value');
        $config->assignVar('conf_valuetype', 'text');

        // getObjects should only be called once; second call uses static cache
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$config]);

        // Use unique category+module to avoid cross-test static cache pollution
        $result1 = &$this->handler->getConfigsByCat(991, 991);
        $result2 = &$this->handler->getConfigsByCat(991, 991);

        self::assertSame($result1, $result2);
    }

    public function testGetConfigsByCatReturnsEmptyForNonExistentCategory(): void
    {
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([]);

        $result = &$this->handler->getConfigsByCat(888, 888);

        self::assertSame([], $result);
    }

    public function testGetConfigsByCatWithIntValueType(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 1);
        $config->assignVar('conf_name', 'per_page');
        $config->assignVar('conf_value', '20');
        $config->assignVar('conf_valuetype', 'int');

        $this->mockCHandler->method('getObjects')->willReturn([$config]);

        $result = &$this->handler->getConfigsByCat(777, 777);

        self::assertSame(20, $result['per_page']);
    }

    public function testGetConfigsByCatWithArrayValueType(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 1);
        $config->assignVar('conf_name', 'allowed_ext');
        $config->assignVar('conf_value', serialize(['jpg', 'png', 'gif']));
        $config->assignVar('conf_valuetype', 'array');

        $this->mockCHandler->method('getObjects')->willReturn([$config]);

        $result = &$this->handler->getConfigsByCat(666, 666);

        self::assertIsArray($result['allowed_ext']);
        self::assertSame(['jpg', 'png', 'gif'], $result['allowed_ext']);
    }

    public function testGetConfigsByCatWithFloatValueType(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 1);
        $config->assignVar('conf_name', 'tax_rate');
        $config->assignVar('conf_value', '19.5');
        $config->assignVar('conf_valuetype', 'float');

        $this->mockCHandler->method('getObjects')->willReturn([$config]);

        $result = &$this->handler->getConfigsByCat(555, 555);

        self::assertSame(19.5, $result['tax_rate']);
    }

    public function testGetConfigsByCatWithZeroCategoryGetsAllForModule(): void
    {
        // When category is 0 (empty), the criteria should not include conf_catid
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 1);
        $config->assignVar('conf_name', 'all_configs');
        $config->assignVar('conf_value', 'test');
        $config->assignVar('conf_valuetype', 'text');

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->with(
                self::callback(function ($criteria) {
                    // When category is empty (0), no category criteria is added
                    return $criteria instanceof CriteriaCompo;
                }),
                true
            )
            ->willReturn([$config]);

        $result = &$this->handler->getConfigsByCat(0, 444);

        self::assertArrayHasKey('all_configs', $result);
    }

    public function testGetConfigsByCatDefaultModuleIsZero(): void
    {
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([]);

        // Module defaults to 0 if not specified
        $result = &$this->handler->getConfigsByCat(333);

        self::assertSame([], $result);
    }

    // =========================================================================
    // createConfigOption
    // =========================================================================

    public function testCreateConfigOptionDelegatesToOHandler(): void
    {
        $option = new XoopsConfigOption();
        $this->mockOHandler->expects(self::once())
            ->method('create')
            ->willReturn($option);

        $result = $this->handler->createConfigOption();

        self::assertInstanceOf(XoopsConfigOption::class, $result);
        self::assertSame($option, $result);
    }

    // =========================================================================
    // getConfigOption
    // =========================================================================

    public function testGetConfigOptionDelegatesToOHandler(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVar('confop_id', 3);
        $option->assignVar('confop_name', 'Yes');
        $option->assignVar('confop_value', '1');

        $this->mockOHandler->expects(self::once())
            ->method('get')
            ->with(3)
            ->willReturn($option);

        $result = $this->handler->getConfigOption(3);

        self::assertInstanceOf(XoopsConfigOption::class, $result);
        self::assertEquals(3, $result->getVar('confop_id'));
        self::assertEquals('Yes', $result->getVar('confop_name'));
    }

    public function testGetConfigOptionReturnsFalseForInvalidId(): void
    {
        $this->mockOHandler->expects(self::once())
            ->method('get')
            ->with(9999)
            ->willReturn(false);

        $result = $this->handler->getConfigOption(9999);

        self::assertFalse($result);
    }

    // =========================================================================
    // getConfigOptions
    // =========================================================================

    public function testGetConfigOptionsDelegatesToOHandler(): void
    {
        $opt1 = new XoopsConfigOption();
        $opt1->assignVar('confop_id', 1);
        $opt2 = new XoopsConfigOption();
        $opt2->assignVar('confop_id', 2);

        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$opt1, $opt2]);

        $result = $this->handler->getConfigOptions(null);

        self::assertCount(2, $result);
    }

    public function testGetConfigOptionsWithCriteria(): void
    {
        $criteria = new CriteriaCompo(new Criteria('conf_id', 5));

        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->with($criteria, true)
            ->willReturn([]);

        $result = $this->handler->getConfigOptions($criteria, true);

        self::assertSame([], $result);
    }

    public function testGetConfigOptionsWithIdAsKey(): void
    {
        $opt = new XoopsConfigOption();
        $opt->assignVar('confop_id', 7);

        $this->mockOHandler->expects(self::once())
            ->method('getObjects')
            ->with(null, true)
            ->willReturn([7 => $opt]);

        $result = $this->handler->getConfigOptions(null, true);

        self::assertArrayHasKey(7, $result);
    }

    public function testGetConfigOptionsReturnsEmptyArrayWhenNone(): void
    {
        $this->mockOHandler->method('getObjects')->willReturn([]);

        $result = $this->handler->getConfigOptions(null);

        self::assertSame([], $result);
    }

    // =========================================================================
    // getConfigOptionsCount
    // =========================================================================

    public function testGetConfigOptionsCountDelegatesToOHandler(): void
    {
        $this->mockOHandler->expects(self::once())
            ->method('getCount')
            ->willReturn(12);

        $result = $this->handler->getConfigOptionsCount(null);

        self::assertSame(12, $result);
    }

    public function testGetConfigOptionsCountWithCriteria(): void
    {
        $criteria = new Criteria('conf_id', 5);

        $this->mockOHandler->expects(self::once())
            ->method('getCount')
            ->with($criteria)
            ->willReturn(4);

        $result = $this->handler->getConfigOptionsCount($criteria);

        self::assertSame(4, $result);
    }

    public function testGetConfigOptionsCountReturnsZero(): void
    {
        $this->mockOHandler->method('getCount')->willReturn(0);

        $result = $this->handler->getConfigOptionsCount(null);

        self::assertSame(0, $result);
    }

    // =========================================================================
    // getConfigList
    // =========================================================================

    public function testGetConfigListReturnsNameValuePairs(): void
    {
        $config1 = new XoopsConfigItem();
        $config1->assignVar('conf_name', 'theme');
        $config1->assignVar('conf_value', 'xbootstrap5');
        $config1->assignVar('conf_valuetype', 'text');

        $config2 = new XoopsConfigItem();
        $config2->assignVar('conf_name', 'per_page');
        $config2->assignVar('conf_value', '10');
        $config2->assignVar('conf_valuetype', 'int');

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$config1, $config2]);

        $result = $this->handler->getConfigList(1, 0);

        self::assertArrayHasKey('theme', $result);
        self::assertEquals('xbootstrap5', $result['theme']);
        self::assertArrayHasKey('per_page', $result);
        self::assertSame(10, $result['per_page']);
    }

    public function testGetConfigListUsesInstanceCache(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_name', 'cached');
        $config->assignVar('conf_value', 'yes');
        $config->assignVar('conf_valuetype', 'text');

        // getObjects should only be called once for the same modid/catid
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$config]);

        $result1 = $this->handler->getConfigList(55, 77);
        $result2 = $this->handler->getConfigList(55, 77);

        self::assertSame($result1, $result2);
        self::assertEquals('yes', $result1['cached']);
    }

    public function testGetConfigListDifferentModulesDontShareCache(): void
    {
        $config1 = new XoopsConfigItem();
        $config1->assignVar('conf_name', 'mod1config');
        $config1->assignVar('conf_value', 'value1');
        $config1->assignVar('conf_valuetype', 'text');

        $config2 = new XoopsConfigItem();
        $config2->assignVar('conf_name', 'mod2config');
        $config2->assignVar('conf_value', 'value2');
        $config2->assignVar('conf_valuetype', 'text');

        // Should be called twice - once for each module
        $this->mockCHandler->expects(self::exactly(2))
            ->method('getObjects')
            ->willReturnOnConsecutiveCalls([$config1], [$config2]);

        $result1 = $this->handler->getConfigList(100, 0);
        $result2 = $this->handler->getConfigList(200, 0);

        self::assertEquals('value1', $result1['mod1config']);
        self::assertEquals('value2', $result2['mod2config']);
    }

    public function testGetConfigListEmptyResult(): void
    {
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([]);

        $result = $this->handler->getConfigList(999, 0);

        self::assertSame([], $result);
    }

    public function testGetConfigListWithDifferentCategoriesDontShareCache(): void
    {
        $configA = new XoopsConfigItem();
        $configA->assignVar('conf_name', 'cat1cfg');
        $configA->assignVar('conf_value', 'a');
        $configA->assignVar('conf_valuetype', 'text');

        $configB = new XoopsConfigItem();
        $configB->assignVar('conf_name', 'cat2cfg');
        $configB->assignVar('conf_value', 'b');
        $configB->assignVar('conf_valuetype', 'text');

        $this->mockCHandler->expects(self::exactly(2))
            ->method('getObjects')
            ->willReturnOnConsecutiveCalls([$configA], [$configB]);

        $result1 = $this->handler->getConfigList(300, 1);
        $result2 = $this->handler->getConfigList(300, 2);

        self::assertEquals('a', $result1['cat1cfg']);
        self::assertEquals('b', $result2['cat2cfg']);
    }

    public function testGetConfigListHandlesArrayValueType(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_name', 'modules');
        $config->assignVar('conf_value', serialize(['system', 'publisher']));
        $config->assignVar('conf_valuetype', 'array');

        $this->mockCHandler->method('getObjects')->willReturn([$config]);

        $result = $this->handler->getConfigList(400, 0);

        self::assertIsArray($result['modules']);
        self::assertSame(['system', 'publisher'], $result['modules']);
    }

    public function testGetConfigListHandlesFloatValueType(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_name', 'ratio');
        $config->assignVar('conf_value', '3.14');
        $config->assignVar('conf_valuetype', 'float');

        $this->mockCHandler->method('getObjects')->willReturn([$config]);

        $result = $this->handler->getConfigList(500, 0);

        self::assertSame(3.14, $result['ratio']);
    }

    // =========================================================================
    // deleteConfigOption (deprecated)
    // =========================================================================

    public function testDeleteConfigOptionReturnsFalse(): void
    {
        // Set up a dummy logger to absorb the deprecated call
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = new class {
                /** @var string[] */
                public $messages = [];
                public function addDeprecated(string $msg): void
                {
                    $this->messages[] = $msg;
                }
            };
        }

        $criteria = new CriteriaCompo();
        $result = $this->handler->deleteConfigOption($criteria);

        self::assertFalse($result);
    }

    public function testDeleteConfigOptionLogsDeprecationMessage(): void
    {
        $logger = new class {
            /** @var string[] */
            public $messages = [];
            public function addDeprecated(string $msg): void
            {
                $this->messages[] = $msg;
            }
        };
        $GLOBALS['xoopsLogger'] = $logger;

        $criteria = new CriteriaCompo();
        $this->handler->deleteConfigOption($criteria);

        self::assertNotEmpty($logger->messages);
        self::assertStringContainsString('deleteConfigOption', $logger->messages[0]);
    }

    // =========================================================================
    // Integration-like tests: insert + cache invalidation together
    // =========================================================================

    public function testInsertConfigInvalidatesCacheAndSubsequentGetConfigListRefetches(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 1);
        $config->assignVar('conf_modid', 700);
        $config->assignVar('conf_catid', 0);
        $config->assignVar('conf_name', 'sitename');
        $config->assignVar('conf_value', 'NewSite');
        $config->assignVar('conf_valuetype', 'text');

        // Pre-populate cache
        $this->setProtectedProperty($this->handler, '_cachedConfigs', [
            700 => [0 => ['sitename' => 'OldSite']],
        ]);

        $this->mockCHandler->method('insert')->willReturn(true);

        // After insert, cache for 700/0 should be cleared
        $this->handler->insertConfig($config);

        $cache = $this->getProtectedProperty($this->handler, '_cachedConfigs');
        self::assertArrayNotHasKey(0, $cache[700] ?? []);

        // Now getConfigList should re-fetch from DB
        $updatedConfig = new XoopsConfigItem();
        $updatedConfig->assignVar('conf_name', 'sitename');
        $updatedConfig->assignVar('conf_value', 'NewSite');
        $updatedConfig->assignVar('conf_valuetype', 'text');

        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->willReturn([$updatedConfig]);

        $result = $this->handler->getConfigList(700, 0);

        self::assertEquals('NewSite', $result['sitename']);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testInsertConfigWithZeroConfId(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 0);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 0);

        $this->mockCHandler->method('insert')->willReturn(true);

        $result = $this->handler->insertConfig($config);

        self::assertTrue($result);
    }

    public function testMultipleConfigsInGetConfigListOverwriteDuplicateNames(): void
    {
        // If two configs have the same name, the last one wins
        $config1 = new XoopsConfigItem();
        $config1->assignVar('conf_name', 'duplicate');
        $config1->assignVar('conf_value', 'first');
        $config1->assignVar('conf_valuetype', 'text');

        $config2 = new XoopsConfigItem();
        $config2->assignVar('conf_name', 'duplicate');
        $config2->assignVar('conf_value', 'second');
        $config2->assignVar('conf_valuetype', 'text');

        $this->mockCHandler->method('getObjects')->willReturn([$config1, $config2]);

        $result = $this->handler->getConfigList(800, 0);

        self::assertEquals('second', $result['duplicate']);
    }

    public function testGetConfigListWithZeroCatIdAddsCategoryCriteria(): void
    {
        // When conf_catid is 0 (empty), the code adds a criteria for conf_catid = 0
        // This is per the source code: if (empty($conf_catid)) add Criteria('conf_catid', $conf_catid)
        $this->mockCHandler->expects(self::once())
            ->method('getObjects')
            ->with(self::callback(function ($criteria) {
                return $criteria instanceof CriteriaCompo;
            }))
            ->willReturn([]);

        $this->handler->getConfigList(900, 0);
    }

    public function testPublicPropertiesAreAccessible(): void
    {
        // _cHandler, _oHandler, and _cachedConfigs are declared public
        $handler = new XoopsConfigHandler($this->db);

        self::assertInstanceOf(XoopsConfigItemHandler::class, $handler->_cHandler);
        self::assertInstanceOf(XoopsConfigOptionHandler::class, $handler->_oHandler);
        self::assertIsArray($handler->_cachedConfigs);
    }

    public function testDeleteConfigWithMultipleOptionsDeletesAll(): void
    {
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 50);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 0);

        // Add 5 options
        for ($i = 1; $i <= 5; $i++) {
            $opt = new XoopsConfigOption();
            $opt->assignVar('confop_id', $i);
            $config->setConfOptions($opt);
        }

        $this->mockCHandler->method('delete')->willReturn(true);

        $this->mockOHandler->expects(self::exactly(5))
            ->method('delete')
            ->willReturn(true);

        $result = $this->handler->deleteConfig($config);

        self::assertTrue($result);
    }

    public function testInsertConfigWithMultipleOptionsWhereOneFailsStillReturnsTrue(): void
    {
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->assignVar('conf_id', 60);
        $config->assignVar('conf_modid', 0);
        $config->assignVar('conf_catid', 0);

        $opt1 = new XoopsConfigOption();
        $opt1->setNew();
        $opt1->setVar('confop_name', 'Good');
        $opt1->setVar('confop_value', '1');

        $opt2 = new XoopsConfigOption();
        $opt2->setNew();
        $opt2->setVar('confop_name', 'Bad');
        $opt2->setVar('confop_value', '0');

        $opt3 = new XoopsConfigOption();
        $opt3->setNew();
        $opt3->setVar('confop_name', 'AlsoGood');
        $opt3->setVar('confop_value', '2');

        $config->setConfOptions([$opt1, $opt2, $opt3]);

        $this->mockCHandler->method('insert')->willReturn(true);

        // Second option fails, others succeed
        $this->mockOHandler->expects(self::exactly(3))
            ->method('insert')
            ->willReturnOnConsecutiveCalls(1, false, 3);

        $result = $this->handler->insertConfig($config);

        // Should still return true even though one option failed
        self::assertTrue($result);
    }
}
