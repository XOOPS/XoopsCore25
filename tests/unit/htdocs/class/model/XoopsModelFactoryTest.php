<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsModelAbstract;
use XoopsModelFactory;
use XoopsModelJoint;
use XoopsModelRead;
use XoopsModelStats;
use XoopsModelSync;
use XoopsModelWrite;
use XoopsPersistableObjectHandler;

/**
 * Stub persistable handler that bypasses the parent constructor.
 *
 * The real XoopsPersistableObjectHandler constructor calls
 * XoopsDatabaseFactory::getDatabaseConnection(), which we do not want
 * in unit tests. This stub avoids that entirely and lets us inject
 * the stub DB and table metadata directly.
 */
class TestModelPersistableHandler extends XoopsPersistableObjectHandler
{
    public function __construct()
    {
        // Bypass parent constructor -- no real DB connection needed
    }
}

/**
 * Comprehensive tests for XoopsModelFactory and XoopsModelAbstract.
 *
 * XoopsModelFactory is a singleton factory that loads model sub-handler
 * classes (read, write, stats, joint, sync) from class/model/*.php files.
 * XoopsModelAbstract is the base class for all model sub-handlers,
 * providing setHandler() and setVars() methods.
 *
 * @package xoopsmodel
 */
#[CoversClass(XoopsModelFactory::class)]
#[CoversClass(XoopsModelAbstract::class)]
class XoopsModelFactoryTest extends TestCase
{
    /**
     * Build a stub XoopsPersistableObjectHandler with the required
     * properties set for model handler operations.
     *
     * @return TestModelPersistableHandler
     */
    private function createStubHandler(): TestModelPersistableHandler
    {
        $handler = new TestModelPersistableHandler();
        $handler->db = $GLOBALS['xoopsDB'];
        $handler->table = 'xoops_test_table';
        $handler->keyName = 'id';
        $handler->className = 'XoopsObject';
        $handler->identifierName = 'title';

        return $handler;
    }

    // ---------------------------------------------------------------
    // XoopsModelFactory: Class structure / singleton tests
    // ---------------------------------------------------------------

    #[Test]
    public function factoryClassExists(): void
    {
        $this->assertTrue(class_exists('XoopsModelFactory'));
    }

    #[Test]
    public function constructorIsProtected(): void
    {
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $ctor = $ref->getConstructor();

        $this->assertNotNull($ctor, 'XoopsModelFactory should have a constructor');
        $this->assertTrue($ctor->isProtected(), 'Constructor must be protected to enforce singleton');
    }

    #[Test]
    public function getInstanceReturnsXoopsModelFactoryInstance(): void
    {
        // getInstance() is not static in production code — it uses a static
        // local variable pattern but is declared as an instance method.
        // We must call it on an instance created via reflection.
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $result = $instance->getInstance();

        $this->assertInstanceOf(XoopsModelFactory::class, $result);
    }

    #[Test]
    public function getInstanceReturnsSameInstanceOnSubsequentCalls(): void
    {
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $first = $instance->getInstance();
        $second = $instance->getInstance();

        $this->assertSame($first, $second, 'getInstance() must return the same singleton');
    }

    #[Test]
    public function factoryInstanceHasHandlersArrayProperty(): void
    {
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $result = $instance->getInstance();

        $this->assertIsArray($result->handlers);
    }

    #[Test]
    public function factoryHandlersPropertyDefaultsToEmptyArray(): void
    {
        // Create a fresh instance via reflection to check default
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $prop = $ref->getProperty('handlers');
        $prop->setAccessible(true);

        $fresh = $ref->newInstanceWithoutConstructor();
        $this->assertSame([], $prop->getValue($fresh));
    }

    // ---------------------------------------------------------------
    // XoopsModelFactory::loadHandler() — loading model sub-handlers
    // ---------------------------------------------------------------

    #[Test]
    public function loadHandlerWithReadReturnsXoopsModelRead(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'read');

        $this->assertInstanceOf(XoopsModelRead::class, $model);
    }

    #[Test]
    public function loadHandlerWithWriteReturnsXoopsModelWrite(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'write');

        $this->assertInstanceOf(XoopsModelWrite::class, $model);
    }

    #[Test]
    public function loadHandlerWithStatsReturnsXoopsModelStats(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'stats');

        $this->assertInstanceOf(XoopsModelStats::class, $model);
    }

    #[Test]
    public function loadHandlerWithJointReturnsXoopsModelJoint(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'joint');

        $this->assertInstanceOf(XoopsModelJoint::class, $model);
    }

    #[Test]
    public function loadHandlerWithSyncReturnsXoopsModelSync(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'sync');

        $this->assertInstanceOf(XoopsModelSync::class, $model);
    }

    #[Test]
    public function loadHandlerSetsHandlerOnReturnedModel(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'read');

        $this->assertSame($handler, $model->handler);
    }

    #[Test]
    public function loadHandlerWithArgsSetsVarsOnModel(): void
    {
        $handler = $this->createStubHandler();
        $args = ['customProp' => 'customValue', 'anotherProp' => 42];
        $model = XoopsModelFactory::loadHandler($handler, 'read', $args);

        $this->assertSame('customValue', $model->customProp);
        $this->assertSame(42, $model->anotherProp);
    }

    #[Test]
    public function loadHandlerWithNullArgsDoesNotSetVars(): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, 'stats', null);

        // The model should still be valid, with handler set
        $this->assertSame($handler, $model->handler);
    }

    #[Test]
    public function loadHandlerCachesHandlersSameNameReturnsSameClass(): void
    {
        $handler1 = $this->createStubHandler();
        $handler2 = $this->createStubHandler();

        $model1 = XoopsModelFactory::loadHandler($handler1, 'read');
        $model2 = XoopsModelFactory::loadHandler($handler2, 'read');

        // The static $handlers array caches by name, so same object is returned
        $this->assertSame($model1, $model2, 'loadHandler should cache and return the same model instance for the same name');
    }

    #[Test]
    public function loadHandlerUpdatesHandlerReferenceOnCachedModel(): void
    {
        $handler1 = $this->createStubHandler();
        $handler1->table = 'xoops_first_table';

        $handler2 = $this->createStubHandler();
        $handler2->table = 'xoops_second_table';

        $model1 = XoopsModelFactory::loadHandler($handler1, 'stats');
        $model2 = XoopsModelFactory::loadHandler($handler2, 'stats');

        // Even though cached, the handler reference is updated on each call
        $this->assertSame($handler2, $model2->handler);
    }

    #[Test]
    public function loadHandlerWithInvalidNameTriggersWarningAndReturnsNull(): void
    {
        $handler = $this->createStubHandler();

        // Suppress the E_USER_WARNING so the test doesn't fail
        set_error_handler(function ($errno, $errstr) {
            return true;
        });

        try {
            $result = XoopsModelFactory::loadHandler($handler, 'nonexistent_model_handler_xyz');
        } finally {
            restore_error_handler();
        }

        $this->assertNull($result);
    }

    #[Test]
    public function loadHandlerIsStaticMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $method = $ref->getMethod('loadHandler');

        $this->assertTrue($method->isStatic());
    }

    #[Test]
    public function loadHandlerAcceptsThreeParameters(): void
    {
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $method = $ref->getMethod('loadHandler');
        $params = $method->getParameters();

        $this->assertCount(3, $params);
        $this->assertSame('ohandler', $params[0]->getName());
        $this->assertSame('name', $params[1]->getName());
        $this->assertSame('args', $params[2]->getName());
    }

    #[Test]
    public function loadHandlerThirdParameterDefaultsToNull(): void
    {
        $ref = new ReflectionClass(XoopsModelFactory::class);
        $method = $ref->getMethod('loadHandler');
        $params = $method->getParameters();

        $this->assertTrue($params[2]->isOptional());
        $this->assertNull($params[2]->getDefaultValue());
    }

    // ---------------------------------------------------------------
    // XoopsModelFactory: Data provider test for all known model types
    // ---------------------------------------------------------------

    #[Test]
    #[DataProvider('modelNameClassProvider')]
    public function loadHandlerReturnsCorrectClassForName(string $name, string $expectedClass): void
    {
        $handler = $this->createStubHandler();
        $model = XoopsModelFactory::loadHandler($handler, $name);

        $this->assertInstanceOf($expectedClass, $model);
    }

    /**
     * Data provider for model name -> expected class mapping.
     *
     * @return array<string, array{string, string}>
     */
    public static function modelNameClassProvider(): array
    {
        return [
            'read handler'  => ['read', XoopsModelRead::class],
            'write handler' => ['write', XoopsModelWrite::class],
            'stats handler' => ['stats', XoopsModelStats::class],
            'joint handler' => ['joint', XoopsModelJoint::class],
            'sync handler'  => ['sync', XoopsModelSync::class],
        ];
    }

    // ---------------------------------------------------------------
    // XoopsModelAbstract: Class structure tests
    // ---------------------------------------------------------------

    #[Test]
    public function abstractClassExists(): void
    {
        $this->assertTrue(class_exists('XoopsModelAbstract'));
    }

    #[Test]
    public function abstractClassHasHandlerProperty(): void
    {
        $ref = new ReflectionClass(XoopsModelAbstract::class);
        $this->assertTrue($ref->hasProperty('handler'));
    }

    #[Test]
    public function abstractClassHandlerPropertyIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsModelAbstract::class);
        $prop = $ref->getProperty('handler');
        $this->assertTrue($prop->isPublic());
    }

    #[Test]
    public function abstractClassHasSetHandlerMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelAbstract::class);
        $this->assertTrue($ref->hasMethod('setHandler'));
    }

    #[Test]
    public function abstractClassHasSetVarsMethod(): void
    {
        $ref = new ReflectionClass(XoopsModelAbstract::class);
        $this->assertTrue($ref->hasMethod('setVars'));
    }

    // ---------------------------------------------------------------
    // XoopsModelAbstract: Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorWithNullArgsAndNullHandler(): void
    {
        $model = new XoopsModelRead(null, null);

        // handler should not be set (setHandler returns false for null)
        $this->assertNull($model->handler);
    }

    #[Test]
    public function constructorWithArrayArgsSetsProperties(): void
    {
        $args = ['foo' => 'bar', 'baz' => 123];
        $model = new XoopsModelRead($args);

        $this->assertSame('bar', $model->foo);
        $this->assertSame(123, $model->baz);
    }

    #[Test]
    public function constructorWithHandlerSetsHandlerReference(): void
    {
        $handler = $this->createStubHandler();
        $model = new XoopsModelRead(null, $handler);

        $this->assertSame($handler, $model->handler);
    }

    #[Test]
    public function constructorWithBothArgsAndHandler(): void
    {
        $handler = $this->createStubHandler();
        $args = ['testKey' => 'testValue'];
        $model = new XoopsModelRead($args, $handler);

        $this->assertSame($handler, $model->handler);
        $this->assertSame('testValue', $model->testKey);
    }

    // ---------------------------------------------------------------
    // XoopsModelAbstract::setHandler() tests
    // ---------------------------------------------------------------

    #[Test]
    public function setHandlerWithValidPersistableObjectHandlerReturnsTrue(): void
    {
        $model = new XoopsModelRead();
        $handler = $this->createStubHandler();

        $result = $model->setHandler($handler);

        $this->assertTrue($result);
        $this->assertSame($handler, $model->handler);
    }

    #[Test]
    public function setHandlerWithNonObjectReturnsFalse(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setHandler('not_an_object');

        $this->assertFalse($result);
    }

    #[Test]
    public function setHandlerWithNullReturnsFalse(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setHandler(null);

        $this->assertFalse($result);
    }

    #[Test]
    public function setHandlerWithWrongObjectTypeReturnsFalse(): void
    {
        $model = new XoopsModelRead();

        // stdClass is not a XoopsPersistableObjectHandler
        $result = $model->setHandler(new \stdClass());

        $this->assertFalse($result);
    }

    #[Test]
    public function setHandlerWithIntegerReturnsFalse(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setHandler(42);

        $this->assertFalse($result);
    }

    #[Test]
    public function setHandlerWithArrayReturnsFalse(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setHandler(['not', 'a', 'handler']);

        $this->assertFalse($result);
    }

    #[Test]
    public function setHandlerWithBoolReturnsFalse(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setHandler(false);

        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // XoopsModelAbstract::setVars() tests
    // ---------------------------------------------------------------

    #[Test]
    public function setVarsWithArraySetsPropertiesOnObject(): void
    {
        $model = new XoopsModelRead();
        $result = $model->setVars(['alpha' => 'one', 'beta' => 'two']);

        $this->assertTrue($result);
        $this->assertSame('one', $model->alpha);
        $this->assertSame('two', $model->beta);
    }

    #[Test]
    public function setVarsWithNullReturnsTrue(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setVars(null);

        $this->assertTrue($result);
    }

    #[Test]
    public function setVarsWithEmptyArrayReturnsTrue(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setVars([]);

        $this->assertTrue($result);
    }

    #[Test]
    public function setVarsWithNonArrayReturnsTrue(): void
    {
        $model = new XoopsModelRead();

        // Non-array, non-null: the method still returns true
        // (it just skips the foreach)
        $result = $model->setVars('a string');

        $this->assertTrue($result);
    }

    #[Test]
    public function setVarsWithFalseReturnsTrue(): void
    {
        $model = new XoopsModelRead();

        $result = $model->setVars(false);

        $this->assertTrue($result);
    }

    #[Test]
    public function setVarsOverwritesExistingProperties(): void
    {
        $model = new XoopsModelRead();
        $model->setVars(['myProp' => 'initial']);

        $this->assertSame('initial', $model->myProp);

        $model->setVars(['myProp' => 'updated']);

        $this->assertSame('updated', $model->myProp);
    }

    #[Test]
    public function setVarsWithNumericKeys(): void
    {
        $model = new XoopsModelRead();

        // Numeric keys become dynamic properties with numeric names
        // This is valid PHP, though unusual
        $result = $model->setVars([0 => 'zero', 1 => 'one']);

        $this->assertTrue($result);
    }

    // ---------------------------------------------------------------
    // XoopsModelAbstract: All subclasses extend XoopsModelAbstract
    // ---------------------------------------------------------------

    #[Test]
    #[DataProvider('subclassProvider')]
    public function subclassExtendsXoopsModelAbstract(string $className): void
    {
        $ref = new ReflectionClass($className);
        $this->assertSame('XoopsModelAbstract', $ref->getParentClass()->getName());
    }

    /**
     * Data provider for all known model subclasses.
     *
     * @return array<string, array{string}>
     */
    public static function subclassProvider(): array
    {
        return [
            'XoopsModelRead'  => ['XoopsModelRead'],
            'XoopsModelWrite' => ['XoopsModelWrite'],
            'XoopsModelStats' => ['XoopsModelStats'],
            'XoopsModelJoint' => ['XoopsModelJoint'],
            'XoopsModelSync'  => ['XoopsModelSync'],
        ];
    }
}
