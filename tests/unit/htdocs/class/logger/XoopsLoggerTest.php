<?php

declare(strict_types=1);

namespace xoopslogger;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsLogger;

#[CoversClass(XoopsLogger::class)]
class XoopsLoggerTest extends TestCase
{
    private XoopsLogger $logger;

    public static function setUpBeforeClass(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
        // Call getInstance() once here so that its set_error_handler() and
        // set_exception_handler() calls happen before PHPUnit takes its
        // handler snapshot for individual tests. This prevents risky-test
        // warnings about modified handler stacks.
        XoopsLogger::getInstance();
    }

    protected function setUp(): void
    {
        // Use a fresh instance each time — avoid the singleton cache
        $this->logger = new XoopsLogger();
    }

    /**
     * Skip test if XoopsLogger lacks addLogger() (XOOPS 2.6 composite pattern).
     */
    private function requireAddLogger(): void
    {
        if (!method_exists($this->logger, 'addLogger')) {
            $this->markTestSkipped('XoopsLogger::addLogger() not available (XOOPS 2.6 only)');
        }
    }

    // ---------------------------------------------------------------
    // Constructor / singleton tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorInitializesEmptyArrays(): void
    {
        $logger = new XoopsLogger();
        $this->assertSame([], $logger->queries);
        $this->assertSame([], $logger->blocks);
        $this->assertSame([], $logger->extra);
        $this->assertSame([], $logger->errors);
        $this->assertSame([], $logger->deprecated);
        $this->assertSame([], $logger->logstart);
        $this->assertSame([], $logger->logend);
    }

    #[Test]
    public function constructorDefaultActivatedIsTrue(): void
    {
        $logger = new XoopsLogger();
        $this->assertTrue($logger->activated);
    }

    #[Test]
    public function constructorDefaultRenderingEnabledIsFalse(): void
    {
        $logger = new XoopsLogger();
        $this->assertFalse($logger->renderingEnabled);
    }

    #[Test]
    public function constructorDefaultUsePopupIsFalse(): void
    {
        $logger = new XoopsLogger();
        $this->assertFalse($logger->usePopup);
    }

    #[Test]
    public function getInstanceReturnsSameInstance(): void
    {
        // getInstance() was already called in setUpBeforeClass(), so the
        // static $instance is set and handlers are registered. Subsequent
        // calls just return the cached singleton without side effects.
        $a = XoopsLogger::getInstance();
        $b = XoopsLogger::getInstance();
        $this->assertSame($a, $b);
    }

    #[Test]
    public function getInstanceReturnsXoopsLoggerType(): void
    {
        $instance = XoopsLogger::getInstance();
        $this->assertInstanceOf(XoopsLogger::class, $instance);
    }

    #[Test]
    public function instanceMethodReturnsSameAsGetInstance(): void
    {
        $logger = new XoopsLogger();
        $result = $logger->instance();
        $this->assertSame(XoopsLogger::getInstance(), $result);
    }

    // ---------------------------------------------------------------
    // addLogger / getLoggers tests
    // ---------------------------------------------------------------

    #[Test]
    public function getLoggersInitiallyEmpty(): void
    {
        $this->requireAddLogger();
        $this->assertSame([], $this->logger->getLoggers());
    }

    #[Test]
    public function addLoggerAcceptsPsr3CompatibleObject(): void
    {
        $this->requireAddLogger();
        $mock = new class {
            public function log($level, $message, array $context = []) {}
        };
        $this->logger->addLogger($mock);
        $this->assertCount(1, $this->logger->getLoggers());
        $this->assertSame($mock, $this->logger->getLoggers()[0]);
    }

    #[Test]
    public function addLoggerRejectsNonObject(): void
    {
        $this->requireAddLogger();
        $this->logger->addLogger('not_an_object');
        $this->assertCount(0, $this->logger->getLoggers());
    }

    #[Test]
    public function addLoggerRejectsObjectWithoutLogMethod(): void
    {
        $this->requireAddLogger();
        $noLog = new class {
            public function write($msg) {}
        };
        $this->logger->addLogger($noLog);
        $this->assertCount(0, $this->logger->getLoggers());
    }

    #[Test]
    public function addLoggerAcceptsMultipleLoggers(): void
    {
        $this->requireAddLogger();
        $mock1 = new class { public function log($level, $message, array $context = []) {} };
        $mock2 = new class { public function log($level, $message, array $context = []) {} };
        $this->logger->addLogger($mock1);
        $this->logger->addLogger($mock2);
        $this->assertCount(2, $this->logger->getLoggers());
    }

    // ---------------------------------------------------------------
    // log() dispatch tests
    // ---------------------------------------------------------------

    #[Test]
    public function logDispatchesToRegisteredLoggers(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->received = [];
        $mock = new LogCollectorMock($collector);
        $this->logger->addLogger($mock);
        $this->logger->log('info', 'Test message', ['key' => 'val']);
        $this->assertCount(1, $collector->received);
        $this->assertSame('info', $collector->received[0]['level']);
        $this->assertSame('Test message', $collector->received[0]['message']);
        $this->assertSame(['key' => 'val'], $collector->received[0]['context']);
    }

    #[Test]
    public function logSwallowsExceptionsFromLoggers(): void
    {
        $this->requireAddLogger();
        $mock = new class {
            public function log($level, $message, array $context = [])
            {
                throw new \RuntimeException('Logger error');
            }
        };
        $this->logger->addLogger($mock);
        // Should not throw
        $this->logger->log('error', 'This should not throw');
        $this->assertTrue(true);
    }

    #[Test]
    public function logDispatchesToAllLoggersEvenIfOneThrows(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->called = false;
        $throwing = new class {
            public function log($level, $message, array $context = [])
            {
                throw new \RuntimeException('fail');
            }
        };
        $working = new LogFlagMock($collector);
        $this->logger->addLogger($throwing);
        $this->logger->addLogger($working);
        $this->logger->log('info', 'test');
        $this->assertTrue($collector->called);
    }

    #[Test]
    public function logWithNoLoggersDoesNothing(): void
    {
        $this->requireAddLogger();
        // Should not throw or error
        $this->logger->log('debug', 'no loggers');
        $this->assertTrue(true);
    }

    // ---------------------------------------------------------------
    // quiet() tests
    // ---------------------------------------------------------------

    #[Test]
    public function quietCallsQuietOnLoggersWithThatMethod(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->quietCalled = false;
        $mock = new LogQuietMock($collector);
        $this->logger->addLogger($mock);
        $this->logger->quiet();
        $this->assertTrue($collector->quietCalled);
    }

    #[Test]
    public function quietSkipsLoggersWithoutQuietMethod(): void
    {
        $this->requireAddLogger();
        $mock = new class {
            public function log($level, $message, array $context = []) {}
        };
        $this->logger->addLogger($mock);
        // Should not throw
        $this->logger->quiet();
        $this->assertTrue(true);
    }

    // ---------------------------------------------------------------
    // Timer tests (startTime / stopTime / dumpTime / microtime)
    // ---------------------------------------------------------------

    #[Test]
    public function microtimeReturnsFloat(): void
    {
        $result = $this->logger->microtime();
        $this->assertIsFloat($result);
    }

    #[Test]
    public function microtimeReturnsReasonableTimestamp(): void
    {
        $result = $this->logger->microtime();
        $this->assertGreaterThan(1000000000.0, $result);
    }

    #[Test]
    public function startTimeRecordsTimestamp(): void
    {
        $this->logger->startTime('test_timer');
        $this->assertArrayHasKey('test_timer', $this->logger->logstart);
        $this->assertIsFloat($this->logger->logstart['test_timer']);
    }

    #[Test]
    public function stopTimeRecordsTimestamp(): void
    {
        $this->logger->startTime('test_timer');
        $this->logger->stopTime('test_timer');
        $this->assertArrayHasKey('test_timer', $this->logger->logend);
        $this->assertIsFloat($this->logger->logend['test_timer']);
    }

    #[Test]
    public function dumpTimeReturnsElapsedTime(): void
    {
        $this->logger->startTime('test_timer');
        usleep(10000); // 10ms
        $this->logger->stopTime('test_timer');
        $elapsed = $this->logger->dumpTime('test_timer');
        $this->assertIsFloat($elapsed);
        $this->assertGreaterThan(0.0, $elapsed);
    }

    #[Test]
    public function dumpTimeReturnsZeroForUnstartedTimer(): void
    {
        $result = $this->logger->dumpTime('nonexistent');
        $this->assertSame(0, $result);
    }

    #[Test]
    public function dumpTimeWithUnsetRemovesTimer(): void
    {
        $this->logger->startTime('removable');
        $this->logger->stopTime('removable');
        $this->logger->dumpTime('removable', true);
        $this->assertArrayNotHasKey('removable', $this->logger->logstart);
    }

    #[Test]
    public function dumpTimeWithoutUnsetKeepsTimer(): void
    {
        $this->logger->startTime('keepable');
        $this->logger->stopTime('keepable');
        $this->logger->dumpTime('keepable', false);
        $this->assertArrayHasKey('keepable', $this->logger->logstart);
    }

    #[Test]
    public function dumpTimeReturnsNullWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $result = $this->logger->dumpTime('XOOPS');
        $this->assertNull($result);
    }

    #[Test]
    public function startTimeDefaultNameIsXOOPS(): void
    {
        $this->logger->startTime();
        $this->assertArrayHasKey('XOOPS', $this->logger->logstart);
    }

    #[Test]
    public function startTimeDoesNothingWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $this->logger->startTime('inactive');
        $this->assertArrayNotHasKey('inactive', $this->logger->logstart);
    }

    #[Test]
    public function stopTimeDoesNothingWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $this->logger->stopTime('inactive');
        $this->assertArrayNotHasKey('inactive', $this->logger->logend);
    }

    // ---------------------------------------------------------------
    // addQuery tests
    // ---------------------------------------------------------------

    #[Test]
    public function addQueryStoresQueryWhenActivated(): void
    {
        $this->logger->addQuery('SELECT 1');
        $this->assertCount(1, $this->logger->queries);
        $this->assertSame('SELECT 1', $this->logger->queries[0]['sql']);
        $this->assertNull($this->logger->queries[0]['error']);
        $this->assertNull($this->logger->queries[0]['errno']);
    }

    #[Test]
    public function addQueryStoresErrorInfo(): void
    {
        $this->logger->addQuery('BAD SQL', 'Syntax error', 1064, 0.001);
        $this->assertSame('Syntax error', $this->logger->queries[0]['error']);
        $this->assertSame(1064, $this->logger->queries[0]['errno']);
    }

    #[Test]
    public function addQueryStoresQueryTime(): void
    {
        $this->logger->addQuery('SELECT 1', null, null, 0.0023);
        $this->assertSame(0.0023, $this->logger->queries[0]['query_time']);
    }

    #[Test]
    public function addQueryDoesNotStoreWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $this->logger->addQuery('SELECT 1');
        $this->assertCount(0, $this->logger->queries);
    }

    #[Test]
    public function addQueryDispatchesToLoggers(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->received = [];
        $mock = new LogCollectorMock($collector);
        $this->logger->addLogger($mock);
        $this->logger->addQuery('SELECT 1', null, null, 0.001);
        $this->assertCount(1, $collector->received);
        $this->assertSame('debug', $collector->received[0]['level']);
        $this->assertSame('Queries', $collector->received[0]['context']['channel']);
    }

    #[Test]
    public function addQueryWithErrorDispatchesAsError(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->received = [];
        $mock = new LogCollectorMock($collector);
        $this->logger->addLogger($mock);
        $this->logger->addQuery('BAD SQL', 'Error msg', 1064);
        $this->assertSame('error', $collector->received[0]['level']);
        $this->assertStringContainsString('Error msg', $collector->received[0]['message']);
    }

    #[Test]
    public function addQueryMultipleQueries(): void
    {
        $this->logger->addQuery('SELECT 1');
        $this->logger->addQuery('SELECT 2');
        $this->logger->addQuery('SELECT 3');
        $this->assertCount(3, $this->logger->queries);
    }

    // ---------------------------------------------------------------
    // addBlock tests
    // ---------------------------------------------------------------

    #[Test]
    public function addBlockStoresBlockWhenActivated(): void
    {
        $this->logger->addBlock('sidebar', true, 3600);
        $this->assertCount(1, $this->logger->blocks);
        $this->assertSame('sidebar', $this->logger->blocks[0]['name']);
        $this->assertTrue($this->logger->blocks[0]['cached']);
        $this->assertSame(3600, $this->logger->blocks[0]['cachetime']);
    }

    #[Test]
    public function addBlockDoesNotStoreWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $this->logger->addBlock('sidebar', true, 3600);
        $this->assertCount(0, $this->logger->blocks);
    }

    #[Test]
    public function addBlockDispatchesToLoggers(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->received = [];
        $mock = new LogCollectorMock($collector);
        $this->logger->addLogger($mock);
        $this->logger->addBlock('header', false, 0);
        $this->assertSame('Blocks', $collector->received[0]['context']['channel']);
    }

    // ---------------------------------------------------------------
    // addExtra tests
    // ---------------------------------------------------------------

    #[Test]
    public function addExtraStoresEntry(): void
    {
        $this->logger->addExtra('Module', 'system loaded');
        $this->assertCount(1, $this->logger->extra);
        $this->assertSame('Module', $this->logger->extra[0]['name']);
        $this->assertSame('system loaded', $this->logger->extra[0]['msg']);
    }

    #[Test]
    public function addExtraDoesNotStoreWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $this->logger->addExtra('Module', 'test');
        $this->assertCount(0, $this->logger->extra);
    }

    #[Test]
    public function addExtraDispatchesToLoggers(): void
    {
        $this->requireAddLogger();
        $collector = new \stdClass();
        $collector->received = [];
        $mock = new LogCollectorMock($collector);
        $this->logger->addLogger($mock);
        $this->logger->addExtra('Test', 'Value');
        $this->assertSame('Extra', $collector->received[0]['context']['channel']);
    }

    // ---------------------------------------------------------------
    // addDeprecated tests
    // ---------------------------------------------------------------

    #[Test]
    public function addDeprecatedStoresMessageWhenActivated(): void
    {
        $this->logger->addDeprecated('Old method used');
        $this->assertCount(1, $this->logger->deprecated);
        $this->assertStringContainsString('Old method used', $this->logger->deprecated[0]);
    }

    #[Test]
    public function addDeprecatedDoesNotStoreWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $this->logger->addDeprecated('Old method used');
        $this->assertCount(0, $this->logger->deprecated);
    }

    #[Test]
    public function addDeprecatedIncludesBacktrace(): void
    {
        $this->logger->addDeprecated('test');
        $this->assertStringContainsString('trace:', $this->logger->deprecated[0]);
    }

    // ---------------------------------------------------------------
    // sanitizePath tests
    // ---------------------------------------------------------------

    #[Test]
    public function sanitizePathRemovesRootPath(): void
    {
        $path = XOOPS_ROOT_PATH . '/class/test.php';
        $result = $this->logger->sanitizePath($path);
        $this->assertStringNotContainsString(XOOPS_ROOT_PATH, $result);
    }

    #[Test]
    public function sanitizePathConvertsBackslashesToForwardSlashes(): void
    {
        $path = 'C:\\some\\path\\file.php';
        $result = $this->logger->sanitizePath($path);
        $this->assertStringNotContainsString('\\', $result);
    }

    #[Test]
    public function sanitizePathWithEmptyString(): void
    {
        $result = $this->logger->sanitizePath('');
        $this->assertSame('', $result);
    }

    // ---------------------------------------------------------------
    // isThrowable tests
    // ---------------------------------------------------------------

    #[Test]
    public function isThrowableReturnsTrueForException(): void
    {
        $ref = new \ReflectionMethod($this->logger, 'isThrowable');
        $ref->setAccessible(true);
        $this->assertTrue($ref->invoke($this->logger, new \RuntimeException('test')));
    }

    #[Test]
    public function isThrowableReturnsTrueForError(): void
    {
        $ref = new \ReflectionMethod($this->logger, 'isThrowable');
        $ref->setAccessible(true);
        $this->assertTrue($ref->invoke($this->logger, new \Error('test')));
    }

    #[Test]
    public function isThrowableReturnsFalseForNonThrowable(): void
    {
        $ref = new \ReflectionMethod($this->logger, 'isThrowable');
        $ref->setAccessible(true);
        $this->assertFalse($ref->invoke($this->logger, 'not throwable'));
    }

    #[Test]
    public function isThrowableReturnsFalseForNull(): void
    {
        $ref = new \ReflectionMethod($this->logger, 'isThrowable');
        $ref->setAccessible(true);
        $this->assertFalse($ref->invoke($this->logger, null));
    }

    #[Test]
    public function isThrowableReturnsFalseForInteger(): void
    {
        $ref = new \ReflectionMethod($this->logger, 'isThrowable');
        $ref->setAccessible(true);
        $this->assertFalse($ref->invoke($this->logger, 42));
    }

    // ---------------------------------------------------------------
    // render tests
    // ---------------------------------------------------------------

    #[Test]
    public function renderReturnsOutputUnchangedWhenDeactivated(): void
    {
        $this->logger->activated = false;
        $output = '<html>content</html>';
        $result = $this->logger->render($output);
        $this->assertSame($output, $result);
    }

    // ---------------------------------------------------------------
    // enableRendering tests
    // ---------------------------------------------------------------

    #[Test]
    public function renderingEnabledCanBeSetToTrue(): void
    {
        // enableRendering() registers an OB callback that includes render.php
        // which requires many _LOGGER_* language constants. Instead, test the
        // property directly to verify the flag mechanism works.
        $this->assertFalse($this->logger->renderingEnabled);
        $this->logger->renderingEnabled = true;
        $this->assertTrue($this->logger->renderingEnabled);
    }

    // ---------------------------------------------------------------
    // Property type safety tests
    // ---------------------------------------------------------------

    #[Test]
    public function queriesPropertyIsAlwaysArray(): void
    {
        $this->assertIsArray($this->logger->queries);
    }

    #[Test]
    public function blocksPropertyIsAlwaysArray(): void
    {
        $this->assertIsArray($this->logger->blocks);
    }

    #[Test]
    public function extraPropertyIsAlwaysArray(): void
    {
        $this->assertIsArray($this->logger->extra);
    }

    #[Test]
    public function errorsPropertyIsAlwaysArray(): void
    {
        $this->assertIsArray($this->logger->errors);
    }

    #[Test]
    public function deprecatedPropertyIsAlwaysArray(): void
    {
        $this->assertIsArray($this->logger->deprecated);
    }

    #[Test]
    public function activatedPropertyIsBool(): void
    {
        $this->assertIsBool($this->logger->activated);
    }

    #[Test]
    public function renderingEnabledPropertyIsBool(): void
    {
        $this->assertIsBool($this->logger->renderingEnabled);
    }

    #[Test]
    public function usePopupPropertyIsBool(): void
    {
        $this->assertIsBool($this->logger->usePopup);
    }
}

/**
 * Helper mock that collects log calls via a shared stdClass.
 */
class LogCollectorMock
{
    private \stdClass $collector;

    public function __construct(\stdClass $collector)
    {
        $this->collector = $collector;
    }

    public function log($level, $message, array $context = []): void
    {
        $this->collector->received[] = [
            'level'   => $level,
            'message' => $message,
            'context' => $context,
        ];
    }
}

/**
 * Helper mock that sets a flag on a shared stdClass when log() is called.
 */
class LogFlagMock
{
    private \stdClass $collector;

    public function __construct(\stdClass $collector)
    {
        $this->collector = $collector;
    }

    public function log($level, $message, array $context = []): void
    {
        $this->collector->called = true;
    }
}

/**
 * Helper mock that tracks quiet() calls via a shared stdClass.
 */
class LogQuietMock
{
    private \stdClass $collector;

    public function __construct(\stdClass $collector)
    {
        $this->collector = $collector;
    }

    public function log($level, $message, array $context = []): void
    {
    }

    public function quiet(): void
    {
        $this->collector->quietCalled = true;
    }
}
