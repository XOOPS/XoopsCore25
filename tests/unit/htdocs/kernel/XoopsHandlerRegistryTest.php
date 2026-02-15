<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsHandlerRegistry;
use XoopsObjectHandler;
use XoopsMySQLDatabase;

require_once XOOPS_ROOT_PATH . '/kernel/handlerregistry.php';

/**
 * Comprehensive unit tests for XoopsHandlerRegistry.
 *
 * XoopsHandlerRegistry is a singleton registry for XoopsObjectHandler instances.
 * It stores kernel handlers and module-specific handlers in separate namespaces
 * within its internal array. No database dependency is required.
 *
 * Note: instance() is a non-static method that uses a static local variable
 * to implement the singleton pattern. It must be called on an object instance
 * (e.g. $reg->instance() or via (new XoopsHandlerRegistry())->instance()).
 *
 * Tested API:
 *   - instance()            Singleton accessor (non-static, static local variable)
 *   - setHandler()          Register a kernel handler
 *   - getHandler()          Retrieve a kernel handler
 *   - unsetHandler()        Remove a kernel handler
 *   - setModuleHandler()    Register a module handler
 *   - getModuleHandler()    Retrieve a module handler
 *   - unsetModuleHandler()  Remove a module handler
 */
final class XoopsHandlerRegistryTest extends KernelTestCase
{
    /**
     * @var XoopsHandlerRegistry
     */
    private $registry;

    /**
     * Each test gets a registry with a clean _handlers array.
     *
     * Because instance() uses a PHP static local variable which cannot
     * be reset via reflection, the singleton persists across tests.
     * We reset its _handlers property (which is public) to ensure isolation.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Obtain the singleton via a temporary instance
        $this->registry = (new XoopsHandlerRegistry())->instance();
        // Clear all registered handlers for test isolation
        $this->registry->_handlers = [];
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        $this->registry->_handlers = [];
        parent::tearDown();
    }

    // =========================================================================
    // Singleton pattern tests
    // =========================================================================

    public function testInstanceReturnsSameObject(): void
    {
        $temp = new XoopsHandlerRegistry();
        $instance1 = $temp->instance();
        $instance2 = $temp->instance();

        $this->assertSame($instance1, $instance2, 'instance() must return the same object on repeated calls');
    }

    public function testInstanceReturnsXoopsHandlerRegistry(): void
    {
        $instance = (new XoopsHandlerRegistry())->instance();

        $this->assertInstanceOf(
            XoopsHandlerRegistry::class,
            $instance,
            'instance() must return an XoopsHandlerRegistry object'
        );
    }

    public function testInstanceReturnsSameObjectFromDifferentTemporaryInstances(): void
    {
        $a = (new XoopsHandlerRegistry())->instance();
        $b = (new XoopsHandlerRegistry())->instance();

        $this->assertSame($a, $b, 'instance() from different temporary objects must return the same singleton');
    }

    public function testInstanceIsDifferentFromDirectConstruction(): void
    {
        $singleton = (new XoopsHandlerRegistry())->instance();
        $direct = new XoopsHandlerRegistry();

        // A directly constructed object is NOT the singleton
        $this->assertNotSame($singleton, $direct);
    }

    public function testSingletonStateIsPersisted(): void
    {
        $handler = $this->createMockHandler();
        $singleton = (new XoopsHandlerRegistry())->instance();
        $singleton->setHandler('persist_test', $handler);

        // Retrieving singleton again should see the same state
        $again = (new XoopsHandlerRegistry())->instance();
        $this->assertSame($handler, $again->getHandler('persist_test'));
    }

    // =========================================================================
    // Kernel handler: setHandler / getHandler
    // =========================================================================

    public function testSetAndGetHandler(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('member', $handler);

        $result = $this->registry->getHandler('member');
        $this->assertSame($handler, $result, 'getHandler must return the same handler that was set');
    }

    public function testGetHandlerReturnsFalseForUnregisteredName(): void
    {
        $result = $this->registry->getHandler('nonexistent');

        $this->assertFalse($result, 'getHandler must return false for names that have not been registered');
    }

    public function testGetHandlerReturnsFalseWhenRegistryIsEmpty(): void
    {
        $result = $this->registry->getHandler('anything');

        $this->assertFalse($result);
    }

    public function testSetHandlerOverwritesExistingEntry(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();

        $this->registry->setHandler('config', $handler1);
        $this->registry->setHandler('config', $handler2);

        $result = $this->registry->getHandler('config');
        $this->assertSame($handler2, $result, 'Setting a handler with an existing name must overwrite the previous one');
    }

    public function testSetMultipleHandlers(): void
    {
        $member = $this->createMockHandler();
        $config = $this->createMockHandler();
        $module = $this->createMockHandler();

        $this->registry->setHandler('member', $member);
        $this->registry->setHandler('config', $config);
        $this->registry->setHandler('module', $module);

        $this->assertSame($member, $this->registry->getHandler('member'));
        $this->assertSame($config, $this->registry->getHandler('config'));
        $this->assertSame($module, $this->registry->getHandler('module'));
    }

    #[DataProvider('kernelHandlerNameProvider')]
    public function testSetAndGetWithVariousNames(string $name): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler($name, $handler);

        $this->assertSame($handler, $this->registry->getHandler($name));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function kernelHandlerNameProvider(): array
    {
        return [
            'simple name'        => ['member'],
            'single character'   => ['x'],
            'numeric string'     => ['123'],
            'underscore prefix'  => ['_test'],
            'mixed case'         => ['MixedCase'],
            'with dots'          => ['foo.bar'],
            'long name'          => [str_repeat('a', 200)],
            'empty string'       => [''],
        ];
    }

    // =========================================================================
    // Kernel handler: unsetHandler
    // =========================================================================

    public function testUnsetHandlerRemovesRegisteredHandler(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('block', $handler);

        $this->registry->unsetHandler('block');

        $this->assertFalse(
            $this->registry->getHandler('block'),
            'getHandler must return false after unsetHandler'
        );
    }

    public function testUnsetHandlerForNonExistentNameDoesNotError(): void
    {
        // Should not throw or emit any errors
        $this->registry->unsetHandler('doesnotexist');

        $this->assertFalse($this->registry->getHandler('doesnotexist'));
    }

    public function testUnsetHandlerDoesNotAffectOtherHandlers(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();

        $this->registry->setHandler('first', $handler1);
        $this->registry->setHandler('second', $handler2);
        $this->registry->unsetHandler('first');

        $this->assertFalse($this->registry->getHandler('first'));
        $this->assertSame($handler2, $this->registry->getHandler('second'));
    }

    public function testSetHandlerAfterUnset(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();

        $this->registry->setHandler('tpl', $handler1);
        $this->registry->unsetHandler('tpl');
        $this->registry->setHandler('tpl', $handler2);

        $this->assertSame($handler2, $this->registry->getHandler('tpl'));
    }

    // =========================================================================
    // Module handler: setModuleHandler / getModuleHandler
    // =========================================================================

    public function testSetAndGetModuleHandler(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('publisher', 'article', $handler);

        $result = $this->registry->getModuleHandler('publisher', 'article');
        $this->assertSame($handler, $result);
    }

    public function testGetModuleHandlerReturnsFalseForUnregisteredModule(): void
    {
        $result = $this->registry->getModuleHandler('nonexistent', 'handler');

        $this->assertFalse($result);
    }

    public function testGetModuleHandlerReturnsFalseForUnregisteredName(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('publisher', 'article', $handler);

        $result = $this->registry->getModuleHandler('publisher', 'category');
        $this->assertFalse($result);
    }

    public function testGetModuleHandlerReturnsFalseWhenRegistryIsEmpty(): void
    {
        $result = $this->registry->getModuleHandler('any', 'thing');

        $this->assertFalse($result);
    }

    public function testSetModuleHandlerOverwritesExistingEntry(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'item', $handler1);
        $this->registry->setModuleHandler('publisher', 'item', $handler2);

        $result = $this->registry->getModuleHandler('publisher', 'item');
        $this->assertSame($handler2, $result);
    }

    public function testMultipleModulesWithSameHandlerName(): void
    {
        $pubHandler = $this->createMockHandler();
        $newsHandler = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'article', $pubHandler);
        $this->registry->setModuleHandler('news', 'article', $newsHandler);

        $this->assertSame($pubHandler, $this->registry->getModuleHandler('publisher', 'article'));
        $this->assertSame($newsHandler, $this->registry->getModuleHandler('news', 'article'));
    }

    public function testMultipleHandlersWithinSameModule(): void
    {
        $articleHandler = $this->createMockHandler();
        $categoryHandler = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'article', $articleHandler);
        $this->registry->setModuleHandler('publisher', 'category', $categoryHandler);

        $this->assertSame($articleHandler, $this->registry->getModuleHandler('publisher', 'article'));
        $this->assertSame($categoryHandler, $this->registry->getModuleHandler('publisher', 'category'));
    }

    #[DataProvider('moduleHandlerNameProvider')]
    public function testSetAndGetModuleWithVariousNames(string $module, string $name): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler($module, $name, $handler);

        $this->assertSame($handler, $this->registry->getModuleHandler($module, $name));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function moduleHandlerNameProvider(): array
    {
        return [
            'typical module and handler' => ['publisher', 'article'],
            'single char module'         => ['x', 'handler'],
            'single char handler'        => ['mod', 'h'],
            'numeric strings'            => ['123', '456'],
            'underscore names'           => ['_my_mod', '_my_handler'],
            'mixed case'                 => ['MyModule', 'MyHandler'],
            'empty module name'          => ['', 'handler'],
            'empty handler name'         => ['module', ''],
            'both empty'                 => ['', ''],
        ];
    }

    // =========================================================================
    // Module handler: unsetModuleHandler
    // =========================================================================

    public function testUnsetModuleHandlerRemovesRegisteredHandler(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('publisher', 'article', $handler);

        $this->registry->unsetModuleHandler('publisher', 'article');

        $this->assertFalse($this->registry->getModuleHandler('publisher', 'article'));
    }

    public function testUnsetModuleHandlerForNonExistentModuleDoesNotError(): void
    {
        $this->registry->unsetModuleHandler('nonexistent', 'handler');

        $this->assertFalse($this->registry->getModuleHandler('nonexistent', 'handler'));
    }

    public function testUnsetModuleHandlerDoesNotAffectOtherModules(): void
    {
        $pubHandler = $this->createMockHandler();
        $newsHandler = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'article', $pubHandler);
        $this->registry->setModuleHandler('news', 'article', $newsHandler);

        $this->registry->unsetModuleHandler('publisher', 'article');

        $this->assertFalse($this->registry->getModuleHandler('publisher', 'article'));
        $this->assertSame($newsHandler, $this->registry->getModuleHandler('news', 'article'));
    }

    public function testUnsetModuleHandlerDoesNotAffectOtherHandlersInSameModule(): void
    {
        $articleHandler = $this->createMockHandler();
        $categoryHandler = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'article', $articleHandler);
        $this->registry->setModuleHandler('publisher', 'category', $categoryHandler);

        $this->registry->unsetModuleHandler('publisher', 'article');

        $this->assertFalse($this->registry->getModuleHandler('publisher', 'article'));
        $this->assertSame($categoryHandler, $this->registry->getModuleHandler('publisher', 'category'));
    }

    public function testSetModuleHandlerAfterUnset(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();

        $this->registry->setModuleHandler('mymod', 'item', $handler1);
        $this->registry->unsetModuleHandler('mymod', 'item');
        $this->registry->setModuleHandler('mymod', 'item', $handler2);

        $this->assertSame($handler2, $this->registry->getModuleHandler('mymod', 'item'));
    }

    // =========================================================================
    // Kernel and module handler isolation
    // =========================================================================

    public function testKernelAndModuleHandlersAreIsolated(): void
    {
        $kernelHandler = $this->createMockHandler();
        $moduleHandler = $this->createMockHandler();

        $this->registry->setHandler('article', $kernelHandler);
        $this->registry->setModuleHandler('publisher', 'article', $moduleHandler);

        $this->assertSame($kernelHandler, $this->registry->getHandler('article'));
        $this->assertSame($moduleHandler, $this->registry->getModuleHandler('publisher', 'article'));
        $this->assertNotSame($kernelHandler, $moduleHandler);
    }

    public function testUnsetKernelHandlerDoesNotAffectModuleHandler(): void
    {
        $kernelHandler = $this->createMockHandler();
        $moduleHandler = $this->createMockHandler();

        $this->registry->setHandler('block', $kernelHandler);
        $this->registry->setModuleHandler('system', 'block', $moduleHandler);

        $this->registry->unsetHandler('block');

        $this->assertFalse($this->registry->getHandler('block'));
        $this->assertSame($moduleHandler, $this->registry->getModuleHandler('system', 'block'));
    }

    public function testUnsetModuleHandlerDoesNotAffectKernelHandler(): void
    {
        $kernelHandler = $this->createMockHandler();
        $moduleHandler = $this->createMockHandler();

        $this->registry->setHandler('config', $kernelHandler);
        $this->registry->setModuleHandler('system', 'config', $moduleHandler);

        $this->registry->unsetModuleHandler('system', 'config');

        $this->assertSame($kernelHandler, $this->registry->getHandler('config'));
        $this->assertFalse($this->registry->getModuleHandler('system', 'config'));
    }

    // =========================================================================
    // Internal state: _handlers property
    // =========================================================================

    public function testHandlersPropertyIsEmptyArrayByDefault(): void
    {
        $freshRegistry = new XoopsHandlerRegistry();

        $this->assertSame([], $freshRegistry->_handlers);
    }

    public function testSetHandlerPopulatesKernelNamespace(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('member', $handler);

        $this->assertArrayHasKey('kernel', $this->registry->_handlers);
        $this->assertArrayHasKey('member', $this->registry->_handlers['kernel']);
        $this->assertSame($handler, $this->registry->_handlers['kernel']['member']);
    }

    public function testSetModuleHandlerPopulatesModuleNamespace(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('publisher', 'article', $handler);

        $this->assertArrayHasKey('module', $this->registry->_handlers);
        $this->assertArrayHasKey('publisher', $this->registry->_handlers['module']);
        $this->assertArrayHasKey('article', $this->registry->_handlers['module']['publisher']);
        $this->assertSame($handler, $this->registry->_handlers['module']['publisher']['article']);
    }

    public function testClearingHandlersArrayResetsAll(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('member', $handler);
        $this->registry->setModuleHandler('publisher', 'article', $handler);

        // Manually clear the public property
        $this->registry->_handlers = [];

        $this->assertFalse($this->registry->getHandler('member'));
        $this->assertFalse($this->registry->getModuleHandler('publisher', 'article'));
    }

    public function testHandlerCountInKernelNamespace(): void
    {
        $this->registry->setHandler('member', $this->createMockHandler());
        $this->registry->setHandler('config', $this->createMockHandler());
        $this->registry->setHandler('module', $this->createMockHandler());

        $this->assertCount(3, $this->registry->_handlers['kernel']);
    }

    public function testHandlerCountInModuleNamespace(): void
    {
        $this->registry->setModuleHandler('publisher', 'article', $this->createMockHandler());
        $this->registry->setModuleHandler('publisher', 'category', $this->createMockHandler());
        $this->registry->setModuleHandler('news', 'story', $this->createMockHandler());

        $this->assertCount(2, $this->registry->_handlers['module']['publisher']);
        $this->assertCount(1, $this->registry->_handlers['module']['news']);
    }

    public function testIteratingKernelHandlers(): void
    {
        $member = $this->createMockHandler();
        $config = $this->createMockHandler();
        $block  = $this->createMockHandler();

        $this->registry->setHandler('member', $member);
        $this->registry->setHandler('config', $config);
        $this->registry->setHandler('block', $block);

        $expected = [
            'member' => $member,
            'config' => $config,
            'block'  => $block,
        ];

        $iterated = [];
        foreach ($this->registry->_handlers['kernel'] as $name => $handler) {
            $iterated[$name] = $handler;
        }

        $this->assertSame($expected, $iterated);
    }

    public function testIteratingModuleHandlers(): void
    {
        $pubArticle = $this->createMockHandler();
        $pubCategory = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'article', $pubArticle);
        $this->registry->setModuleHandler('publisher', 'category', $pubCategory);

        $expected = [
            'article'  => $pubArticle,
            'category' => $pubCategory,
        ];

        $iterated = [];
        foreach ($this->registry->_handlers['module']['publisher'] as $name => $handler) {
            $iterated[$name] = $handler;
        }

        $this->assertSame($expected, $iterated);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testSetSameHandlerForMultipleNames(): void
    {
        $handler = $this->createMockHandler();

        $this->registry->setHandler('alias1', $handler);
        $this->registry->setHandler('alias2', $handler);

        $this->assertSame(
            $this->registry->getHandler('alias1'),
            $this->registry->getHandler('alias2')
        );
    }

    public function testSetSameHandlerAcrossKernelAndModule(): void
    {
        $handler = $this->createMockHandler();

        $this->registry->setHandler('shared', $handler);
        $this->registry->setModuleHandler('mymod', 'shared', $handler);

        $this->assertSame($handler, $this->registry->getHandler('shared'));
        $this->assertSame($handler, $this->registry->getModuleHandler('mymod', 'shared'));
    }

    public function testUnsetThenGetReturnsFalse(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('temp', $handler);

        // Confirm it was set
        $this->assertSame($handler, $this->registry->getHandler('temp'));

        // Unset and confirm
        $this->registry->unsetHandler('temp');
        $this->assertFalse($this->registry->getHandler('temp'));
    }

    public function testUnsetModuleThenGetReturnsFalse(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('mod', 'handler', $handler);

        // Confirm it was set
        $this->assertSame($handler, $this->registry->getModuleHandler('mod', 'handler'));

        // Unset and confirm
        $this->registry->unsetModuleHandler('mod', 'handler');
        $this->assertFalse($this->registry->getModuleHandler('mod', 'handler'));
    }

    public function testDoubleUnsetDoesNotError(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('once', $handler);

        $this->registry->unsetHandler('once');
        $this->registry->unsetHandler('once'); // second call — should not throw

        $this->assertFalse($this->registry->getHandler('once'));
    }

    public function testDoubleModuleUnsetDoesNotError(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('m', 'h', $handler);

        $this->registry->unsetModuleHandler('m', 'h');
        $this->registry->unsetModuleHandler('m', 'h'); // second call

        $this->assertFalse($this->registry->getModuleHandler('m', 'h'));
    }

    public function testGetHandlerWithEmptyStringKey(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('', $handler);

        $this->assertSame($handler, $this->registry->getHandler(''));
    }

    public function testGetModuleHandlerWithEmptyStringKeys(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('', '', $handler);

        $this->assertSame($handler, $this->registry->getModuleHandler('', ''));
    }

    public function testLargeNumberOfKernelHandlers(): void
    {
        $handlers = [];
        for ($i = 0; $i < 50; $i++) {
            $key = 'handler_' . $i;
            $handlers[$key] = $this->createMockHandler();
            $this->registry->setHandler($key, $handlers[$key]);
        }

        // Verify all can be retrieved
        for ($i = 0; $i < 50; $i++) {
            $key = 'handler_' . $i;
            $this->assertSame($handlers[$key], $this->registry->getHandler($key));
        }

        $this->assertCount(50, $this->registry->_handlers['kernel']);
    }

    public function testLargeNumberOfModuleHandlers(): void
    {
        $handlers = [];
        for ($i = 0; $i < 30; $i++) {
            $mod = 'mod_' . $i;
            $name = 'handler_' . $i;
            $handlers[$mod][$name] = $this->createMockHandler();
            $this->registry->setModuleHandler($mod, $name, $handlers[$mod][$name]);
        }

        // Verify all can be retrieved
        for ($i = 0; $i < 30; $i++) {
            $mod = 'mod_' . $i;
            $name = 'handler_' . $i;
            $this->assertSame(
                $handlers[$mod][$name],
                $this->registry->getModuleHandler($mod, $name)
            );
        }
    }

    public function testRegistryHandlersByReference(): void
    {
        // XoopsHandlerRegistry stores handlers by reference (=&).
        // Verify the stored reference points to the same object.
        $handler = $this->createMockHandler();
        $this->registry->setHandler('ref_test', $handler);

        $retrieved = $this->registry->getHandler('ref_test');
        $this->assertSame($handler, $retrieved, 'Handler stored by reference must be the same object');
    }

    public function testModuleRegistryHandlersByReference(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('mymod', 'ref_test', $handler);

        $retrieved = $this->registry->getModuleHandler('mymod', 'ref_test');
        $this->assertSame($handler, $retrieved, 'Module handler stored by reference must be the same object');
    }

    public function testNewInstanceHasEmptyHandlersArray(): void
    {
        // Direct instantiation (not via singleton) should also start empty
        $reg = new XoopsHandlerRegistry();
        $this->assertIsArray($reg->_handlers);
        $this->assertEmpty($reg->_handlers);
    }

    public function testSetHandlerDoesNotPolluteModuleNamespace(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setHandler('article', $handler);

        $this->assertArrayNotHasKey('module', $this->registry->_handlers);
    }

    public function testSetModuleHandlerDoesNotPolluteKernelNamespace(): void
    {
        $handler = $this->createMockHandler();
        $this->registry->setModuleHandler('publisher', 'article', $handler);

        $this->assertArrayNotHasKey('kernel', $this->registry->_handlers);
    }

    public function testDirectInstanceOperationsDoNotAffectSingleton(): void
    {
        // A directly constructed instance is separate from the singleton
        $direct = new XoopsHandlerRegistry();
        $handler = $this->createMockHandler();
        $direct->setHandler('direct_only', $handler);

        // The singleton should NOT have this handler
        $singleton = (new XoopsHandlerRegistry())->instance();
        // The singleton is our $this->registry which was cleared in setUp
        // So it should only have what we added to it, not what we added to $direct
        $this->assertFalse($this->registry->getHandler('direct_only'));
    }

    public function testOverwritePreservesOtherKernelEntries(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();
        $handler3 = $this->createMockHandler();

        $this->registry->setHandler('keep1', $handler1);
        $this->registry->setHandler('overwrite', $handler2);
        $this->registry->setHandler('keep2', $handler3);

        // Overwrite the middle one
        $replacement = $this->createMockHandler();
        $this->registry->setHandler('overwrite', $replacement);

        $this->assertSame($handler1, $this->registry->getHandler('keep1'));
        $this->assertSame($replacement, $this->registry->getHandler('overwrite'));
        $this->assertSame($handler3, $this->registry->getHandler('keep2'));
    }

    public function testOverwritePreservesOtherModuleEntries(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();
        $handler3 = $this->createMockHandler();

        $this->registry->setModuleHandler('pub', 'keep1', $handler1);
        $this->registry->setModuleHandler('pub', 'overwrite', $handler2);
        $this->registry->setModuleHandler('pub', 'keep2', $handler3);

        // Overwrite the middle one
        $replacement = $this->createMockHandler();
        $this->registry->setModuleHandler('pub', 'overwrite', $replacement);

        $this->assertSame($handler1, $this->registry->getModuleHandler('pub', 'keep1'));
        $this->assertSame($replacement, $this->registry->getModuleHandler('pub', 'overwrite'));
        $this->assertSame($handler3, $this->registry->getModuleHandler('pub', 'keep2'));
    }

    public function testGetHandlerAfterClearReturnsNewHandler(): void
    {
        $handler1 = $this->createMockHandler();
        $handler2 = $this->createMockHandler();

        $this->registry->setHandler('test', $handler1);
        $this->registry->_handlers = [];
        $this->registry->setHandler('test', $handler2);

        $this->assertSame($handler2, $this->registry->getHandler('test'));
        $this->assertNotSame($handler1, $this->registry->getHandler('test'));
    }

    public function testModuleNamespaceStructureWithMultipleModules(): void
    {
        $this->registry->setModuleHandler('publisher', 'article', $this->createMockHandler());
        $this->registry->setModuleHandler('publisher', 'category', $this->createMockHandler());
        $this->registry->setModuleHandler('news', 'story', $this->createMockHandler());
        $this->registry->setModuleHandler('news', 'topic', $this->createMockHandler());
        $this->registry->setModuleHandler('system', 'block', $this->createMockHandler());

        // Verify the structure
        $this->assertCount(3, $this->registry->_handlers['module']);
        $this->assertArrayHasKey('publisher', $this->registry->_handlers['module']);
        $this->assertArrayHasKey('news', $this->registry->_handlers['module']);
        $this->assertArrayHasKey('system', $this->registry->_handlers['module']);
        $this->assertCount(2, $this->registry->_handlers['module']['publisher']);
        $this->assertCount(2, $this->registry->_handlers['module']['news']);
        $this->assertCount(1, $this->registry->_handlers['module']['system']);
    }

    public function testCaseSensitiveKernelHandlerNames(): void
    {
        $lower = $this->createMockHandler();
        $upper = $this->createMockHandler();

        $this->registry->setHandler('member', $lower);
        $this->registry->setHandler('Member', $upper);

        // These should be distinct entries (PHP array keys are case-sensitive)
        $this->assertSame($lower, $this->registry->getHandler('member'));
        $this->assertSame($upper, $this->registry->getHandler('Member'));
        $this->assertNotSame($lower, $upper);
        $this->assertCount(2, $this->registry->_handlers['kernel']);
    }

    public function testCaseSensitiveModuleNames(): void
    {
        $lower = $this->createMockHandler();
        $upper = $this->createMockHandler();

        $this->registry->setModuleHandler('publisher', 'article', $lower);
        $this->registry->setModuleHandler('Publisher', 'article', $upper);

        $this->assertSame($lower, $this->registry->getModuleHandler('publisher', 'article'));
        $this->assertSame($upper, $this->registry->getModuleHandler('Publisher', 'article'));
        $this->assertCount(2, $this->registry->_handlers['module']);
    }

    // =========================================================================
    // Helper methods
    // =========================================================================

    /**
     * Create a mock XoopsObjectHandler for registry tests.
     *
     * @return XoopsObjectHandler|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createMockHandler()
    {
        return $this->getMockBuilder(XoopsObjectHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
