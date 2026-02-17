<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsPreload;
use XoopsPreloadItem;

/**
 * Test preload class used to verify event triggering.
 *
 * Defines static event methods that record whether they were called and
 * what arguments they received.
 */
class TestEventPreload extends \XoopsPreloadItem
{
    /** @var bool */
    public static $called = false;

    /** @var array */
    public static $args = [];

    /** @var int */
    public static $callCount = 0;

    public static function reset(): void
    {
        self::$called    = false;
        self::$args      = [];
        self::$callCount = 0;
    }

    /**
     * @param array $args
     */
    public static function eventTestaction($args): void
    {
        self::$called = true;
        self::$args   = $args;
        self::$callCount++;
    }
}

/**
 * Second test preload class to verify multiple handlers on the same event.
 */
class TestEventPreloadSecond extends \XoopsPreloadItem
{
    /** @var bool */
    public static $called = false;

    /** @var array */
    public static $args = [];

    public static function reset(): void
    {
        self::$called = false;
        self::$args   = [];
    }

    /**
     * @param array $args
     */
    public static function eventTestaction($args): void
    {
        self::$called = true;
        self::$args   = $args;
    }
}

/**
 * Comprehensive unit tests for XoopsPreload and XoopsPreloadItem.
 *
 * XoopsPreload is a singleton event dispatcher. During construction it scans
 * active modules for preload files, discovers event methods (those starting
 * with "event"), and stores them keyed by normalized event name. triggerEvent()
 * normalizes the event name (dots removed, lowercased) and calls all registered
 * handlers.
 *
 * Since the real constructor depends on the module filesystem and cache, most
 * event registration/triggering tests use reflection to inject handlers directly
 * into the _events array.
 *
 * Tested API:
 *   - getInstance()        Singleton accessor
 *   - triggerEvent()       Event dispatch with name normalization
 *   - _events injection    Via reflection, to test handler invocation
 *   - XoopsPreloadItem     Base class instantiation
 *
 */
#[CoversClass(XoopsPreload::class)]
#[CoversClass(XoopsPreloadItem::class)]
class XoopsPreloadTest extends TestCase
{
    /**
     * Save the original _events so we can restore them after each test.
     *
     * @var array
     */
    private $originalEvents;

    protected function setUp(): void
    {
        // Save original events
        $instance = XoopsPreload::getInstance();
        $ref  = new ReflectionClass($instance);
        $prop = $ref->getProperty('_events');
        $prop->setAccessible(true);
        $this->originalEvents = $prop->getValue($instance);

        // Reset test preload state
        TestEventPreload::reset();
        TestEventPreloadSecond::reset();
    }

    protected function tearDown(): void
    {
        // Restore original events to avoid polluting other tests
        $instance = XoopsPreload::getInstance();
        $ref  = new ReflectionClass($instance);
        $prop = $ref->getProperty('_events');
        $prop->setAccessible(true);
        $prop->setValue($instance, $this->originalEvents);
    }

    // ---------------------------------------------------------------
    //  getInstance() tests
    // ---------------------------------------------------------------

    /**
     * getInstance() must return an XoopsPreload instance.
     */
    public function testGetInstanceReturnsXoopsPreloadInstance(): void
    {
        $instance = XoopsPreload::getInstance();
        $this->assertInstanceOf(XoopsPreload::class, $instance);
    }

    /**
     * getInstance() must return the same instance on multiple calls (singleton).
     */
    public function testGetInstanceReturnsSameInstanceOnMultipleCalls(): void
    {
        $first  = XoopsPreload::getInstance();
        $second = XoopsPreload::getInstance();
        $third  = XoopsPreload::getInstance();

        $this->assertSame($first, $second, 'First and second should be the same instance');
        $this->assertSame($second, $third, 'Second and third should be the same instance');
    }

    /**
     * The singleton instance has _preloads and _events as public arrays.
     */
    public function testInstanceHasPublicPreloadsAndEventsArrays(): void
    {
        $instance = XoopsPreload::getInstance();
        $this->assertIsArray($instance->_preloads);
        $this->assertIsArray($instance->_events);
    }

    // ---------------------------------------------------------------
    //  triggerEvent() — name normalization tests
    // ---------------------------------------------------------------

    /**
     * triggerEvent() normalizes the event name by removing dots and lowercasing.
     * "test.dotted.event" becomes "testdottedevent".
     */
    public function testTriggerEventNormalizesDotsAndCase(): void
    {
        $instance = XoopsPreload::getInstance();

        // Inject a handler for the normalized name
        $this->injectEvent('testdottedevent', TestEventPreload::class, 'eventTestaction');

        // Trigger with dotted, mixed-case name
        $instance->triggerEvent('test.dotted.event');

        $this->assertTrue(TestEventPreload::$called, 'Handler should be called for normalized event name');
    }

    /**
     * triggerEvent() with an uppercase event name is normalized correctly.
     */
    public function testTriggerEventNormalizesUpperCase(): void
    {
        $instance = XoopsPreload::getInstance();

        $this->injectEvent('testuppercase', TestEventPreload::class, 'eventTestaction');

        $instance->triggerEvent('TEST.UPPER.CASE');

        $this->assertTrue(TestEventPreload::$called, 'Uppercase event name should be normalized and matched');
    }

    /**
     * triggerEvent() for a non-existent event does not throw or error.
     */
    public function testTriggerEventForNonExistentEventDoesNotError(): void
    {
        $instance = XoopsPreload::getInstance();

        // This should simply do nothing, no exception
        $instance->triggerEvent('completely.nonexistent.event.xyz');

        // If we got here, no error was thrown
        $this->assertTrue(true, 'Triggering a non-existent event should be a no-op');
    }

    /**
     * triggerEvent() with empty string event name does not error.
     */
    public function testTriggerEventWithEmptyStringDoesNotError(): void
    {
        $instance = XoopsPreload::getInstance();

        $instance->triggerEvent('');

        $this->assertTrue(true, 'Empty event name should not cause errors');
    }

    // ---------------------------------------------------------------
    //  triggerEvent() — handler invocation tests
    // ---------------------------------------------------------------

    /**
     * triggerEvent() calls the registered handler.
     */
    public function testTriggerEventCallsRegisteredHandler(): void
    {
        $instance = XoopsPreload::getInstance();

        $this->injectEvent('testaction', TestEventPreload::class, 'eventTestaction');

        $this->assertFalse(TestEventPreload::$called, 'Precondition: handler not yet called');

        $instance->triggerEvent('testaction');

        $this->assertTrue(TestEventPreload::$called, 'Handler should be called after triggerEvent');
    }

    /**
     * triggerEvent() passes the args array to the handler.
     */
    public function testTriggerEventPassesArgsToHandler(): void
    {
        $instance = XoopsPreload::getInstance();

        $this->injectEvent('testaction', TestEventPreload::class, 'eventTestaction');

        $testArgs = ['key' => 'value', 'number' => 42];
        $instance->triggerEvent('testaction', $testArgs);

        $this->assertSame($testArgs, TestEventPreload::$args, 'Args should be passed through to handler');
    }

    /**
     * triggerEvent() passes empty args by default.
     */
    public function testTriggerEventDefaultArgsIsEmptyArray(): void
    {
        $instance = XoopsPreload::getInstance();

        $this->injectEvent('testaction', TestEventPreload::class, 'eventTestaction');

        $instance->triggerEvent('testaction');

        $this->assertSame([], TestEventPreload::$args, 'Default args should be empty array');
    }

    /**
     * Multiple handlers on the same event are all called.
     */
    public function testTriggerEventCallsMultipleHandlers(): void
    {
        $instance = XoopsPreload::getInstance();

        // Inject both handlers for the same event
        $ref  = new ReflectionClass($instance);
        $prop = $ref->getProperty('_events');
        $prop->setAccessible(true);
        $events = $prop->getValue($instance);

        $events['testaction'] = [
            ['class_name' => TestEventPreload::class, 'method' => 'eventTestaction'],
            ['class_name' => TestEventPreloadSecond::class, 'method' => 'eventTestaction'],
        ];
        $prop->setValue($instance, $events);

        $instance->triggerEvent('testaction', ['shared' => true]);

        $this->assertTrue(TestEventPreload::$called, 'First handler should be called');
        $this->assertTrue(TestEventPreloadSecond::$called, 'Second handler should be called');
        $this->assertSame(['shared' => true], TestEventPreload::$args);
        $this->assertSame(['shared' => true], TestEventPreloadSecond::$args);
    }

    /**
     * Triggering the same event multiple times calls the handler each time.
     */
    public function testTriggerEventCanBeCalledMultipleTimes(): void
    {
        $instance = XoopsPreload::getInstance();

        $this->injectEvent('testaction', TestEventPreload::class, 'eventTestaction');

        $instance->triggerEvent('testaction');
        $instance->triggerEvent('testaction');
        $instance->triggerEvent('testaction');

        $this->assertSame(3, TestEventPreload::$callCount, 'Handler should be called once per trigger');
    }

    /**
     * triggerEvent() with a dotted name triggers the handler registered under
     * the normalized (dotless) key.
     */
    public function testTriggerEventWithDotsMatchesNormalizedKey(): void
    {
        $instance = XoopsPreload::getInstance();

        // Register under the normalized key (use a unique test event name to
        // avoid triggering real module preload handlers)
        $this->injectEvent('testincludefakestart', TestEventPreload::class, 'eventTestaction');

        // Trigger with the dotted version
        $instance->triggerEvent('test.include.fake.start');

        $this->assertTrue(TestEventPreload::$called, 'Dotted event name should match the normalized key');
    }

    // ---------------------------------------------------------------
    //  _events and _preloads structure tests
    // ---------------------------------------------------------------

    /**
     * The _events array uses normalized event names as keys.
     */
    public function testEventsArrayKeysAreNormalized(): void
    {
        $instance = XoopsPreload::getInstance();

        foreach (array_keys($instance->_events) as $key) {
            $this->assertSame(
                strtolower($key),
                $key,
                "Event key '{$key}' should be lowercase"
            );
            $this->assertFalse(
                strpos($key, '.') !== false,
                "Event key '{$key}' should not contain dots"
            );
        }
    }

    /**
     * Each entry in _events is an array of handler definitions.
     */
    public function testEventsArrayValuesAreArraysOfHandlers(): void
    {
        $instance = XoopsPreload::getInstance();

        foreach ($instance->_events as $eventName => $handlers) {
            $this->assertIsArray($handlers, "Handlers for '{$eventName}' should be an array");
            foreach ($handlers as $i => $handler) {
                $this->assertArrayHasKey(
                    'class_name',
                    $handler,
                    "Handler [{$i}] for '{$eventName}' must have 'class_name'"
                );
                $this->assertArrayHasKey(
                    'method',
                    $handler,
                    "Handler [{$i}] for '{$eventName}' must have 'method'"
                );
            }
        }
    }

    /**
     * The _preloads array entries have 'module' and 'file' keys.
     */
    public function testPreloadsArrayEntriesHaveModuleAndFileKeys(): void
    {
        $instance = XoopsPreload::getInstance();

        foreach ($instance->_preloads as $i => $preload) {
            $this->assertArrayHasKey('module', $preload, "Preload [{$i}] must have 'module' key");
            $this->assertArrayHasKey('file', $preload, "Preload [{$i}] must have 'file' key");
        }
    }

    // ---------------------------------------------------------------
    //  XoopsPreloadItem tests
    // ---------------------------------------------------------------

    /**
     * XoopsPreloadItem can be instantiated.
     */
    public function testXoopsPreloadItemCanBeInstantiated(): void
    {
        $item = new XoopsPreloadItem();
        $this->assertInstanceOf(XoopsPreloadItem::class, $item);
    }

    /**
     * XoopsPreloadItem constructor exists and is public.
     */
    public function testXoopsPreloadItemConstructorIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsPreloadItem::class);
        $constructor = $ref->getConstructor();

        $this->assertNotNull($constructor, 'XoopsPreloadItem should have a constructor');
        $this->assertTrue($constructor->isPublic(), 'Constructor should be public');
    }

    /**
     * TestEventPreload extends XoopsPreloadItem.
     */
    public function testTestPreloadExtendsXoopsPreloadItem(): void
    {
        $preload = new TestEventPreload();
        $this->assertInstanceOf(XoopsPreloadItem::class, $preload);
    }

    // ---------------------------------------------------------------
    //  XoopsPreload constructor is protected (singleton enforcement)
    // ---------------------------------------------------------------

    /**
     * XoopsPreload constructor is protected (cannot be called directly).
     */
    public function testXoopsPreloadConstructorIsProtected(): void
    {
        $ref = new ReflectionClass(XoopsPreload::class);
        $constructor = $ref->getConstructor();

        $this->assertNotNull($constructor, 'XoopsPreload should have a constructor');
        $this->assertTrue($constructor->isProtected(), 'Constructor should be protected');
    }

    // ---------------------------------------------------------------
    //  Helpers
    // ---------------------------------------------------------------

    /**
     * Inject a single event handler into the XoopsPreload _events array.
     *
     * @param string $normalizedEventName Event key (already normalized: lowercase, no dots)
     * @param string $className           Fully-qualified class name
     * @param string $methodName          Static method name
     */
    private function injectEvent(string $normalizedEventName, string $className, string $methodName): void
    {
        $instance = XoopsPreload::getInstance();
        $ref  = new ReflectionClass($instance);
        $prop = $ref->getProperty('_events');
        $prop->setAccessible(true);
        $events = $prop->getValue($instance);

        $events[$normalizedEventName][] = [
            'class_name' => $className,
            'method'     => $methodName,
        ];

        $prop->setValue($instance, $events);
    }
}
