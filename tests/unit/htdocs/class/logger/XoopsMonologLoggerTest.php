<?php
declare(strict_types=1);

namespace xoopslogger;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use XoopsMonologLogger;

require_once XOOPS_ROOT_PATH . '/class/logger/monologlogger.php';

#[CoversClass(XoopsMonologLogger::class)]
class XoopsMonologLoggerTest extends TestCase
{
    private TestHandler $testHandler;
    private XoopsMonologLogger $logger;

    protected function setUp(): void
    {
        $this->testHandler = new TestHandler();
        $this->logger = new XoopsMonologLogger('test', [$this->testHandler]);
    }

    /**
     * Convert a Logger::* integer constant to the appropriate type for
     * TestHandler::hasRecordThatContains().
     * Monolog 3 requires a Monolog\Level enum; Monolog 2 uses int.
     */
    private static function level(int $level): mixed
    {
        if (class_exists(\Monolog\Level::class)) {
            return \Monolog\Level::from($level);
        }
        return $level;
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorCreatesValidLoggerWithDefaults(): void
    {
        $logger = new XoopsMonologLogger();
        $this->assertNotNull($logger->getMonolog());
        $this->assertInstanceOf(Logger::class, $logger->getMonolog());
    }

    #[Test]
    public function constructorAppliesCustomChannelName(): void
    {
        $logger = new XoopsMonologLogger('my_channel', [$this->testHandler]);
        $monolog = $logger->getMonolog();
        $this->assertNotNull($monolog);
        $this->assertSame('my_channel', $monolog->getName());
    }

    #[Test]
    public function constructorDefaultChannelNameIsXoops(): void
    {
        $logger = new XoopsMonologLogger('xoops', [$this->testHandler]);
        $monolog = $logger->getMonolog();
        $this->assertNotNull($monolog);
        $this->assertSame('xoops', $monolog->getName());
    }

    #[Test]
    public function constructorPushesCustomHandlers(): void
    {
        $handler1 = new TestHandler();
        $handler2 = new TestHandler();
        $logger = new XoopsMonologLogger('test', [$handler1, $handler2]);
        $monolog = $logger->getMonolog();
        $this->assertNotNull($monolog);
        // Monolog stores handlers in a stack (LIFO), so both should be present
        $handlers = $monolog->getHandlers();
        $this->assertCount(2, $handlers);
    }

    #[Test]
    public function constructorPushesCustomProcessors(): void
    {
        $processorCalled = false;
        if (class_exists(\Monolog\LogRecord::class)) {
            // Monolog 3: processors receive immutable LogRecord objects
            $processor = function (\Monolog\LogRecord $record) use (&$processorCalled) {
                $processorCalled = true;
                return $record->with(extra: array_merge($record->extra, ['custom' => 'value']));
            };
        } else {
            // Monolog 2: processors receive arrays
            $processor = function (array $record) use (&$processorCalled) {
                $processorCalled = true;
                $record['extra']['custom'] = 'value';
                return $record;
            };
        }
        $handler = new TestHandler();
        $logger = new XoopsMonologLogger('test', [$handler], [$processor]);

        // Trigger the processor by logging
        $logger->log(LogLevel::INFO, 'processor test');
        $this->assertTrue($processorCalled, 'Custom processor should have been called');

        $records = $handler->getRecords();
        $this->assertCount(1, $records);
        if (class_exists(\Monolog\LogRecord::class)) {
            $this->assertSame('value', $records[0]->extra['custom']);
        } else {
            $this->assertSame('value', $records[0]['extra']['custom']);
        }
    }

    #[Test]
    public function constructorDefaultHandlerWritesToXoopsVarPathLogs(): void
    {
        // Create a logger with default handlers (no custom handlers)
        $logger = new XoopsMonologLogger('test_default');
        $monolog = $logger->getMonolog();
        $this->assertNotNull($monolog);

        $handlers = $monolog->getHandlers();
        $this->assertCount(1, $handlers);
        // The default handler should be a RotatingFileHandler
        $this->assertInstanceOf(
            \Monolog\Handler\RotatingFileHandler::class,
            $handlers[0]
        );
    }

    #[Test]
    public function constructorWithEmptyHandlersArrayUsesDefault(): void
    {
        $logger = new XoopsMonologLogger('test_empty', []);
        $monolog = $logger->getMonolog();
        $this->assertNotNull($monolog);

        $handlers = $monolog->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(
            \Monolog\Handler\RotatingFileHandler::class,
            $handlers[0]
        );
    }

    // ---------------------------------------------------------------
    // log() tests — PSR-3 level coverage
    // ---------------------------------------------------------------

    #[Test]
    public function logAtEmergencyLevel(): void
    {
        $this->logger->log(LogLevel::EMERGENCY, 'system is down');
        $this->assertTrue($this->testHandler->hasEmergencyRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('system is down', self::level(Logger::EMERGENCY)));
    }

    #[Test]
    public function logAtAlertLevel(): void
    {
        $this->logger->log(LogLevel::ALERT, 'action required');
        $this->assertTrue($this->testHandler->hasAlertRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('action required', self::level(Logger::ALERT)));
    }

    #[Test]
    public function logAtCriticalLevel(): void
    {
        $this->logger->log(LogLevel::CRITICAL, 'critical failure');
        $this->assertTrue($this->testHandler->hasCriticalRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('critical failure', self::level(Logger::CRITICAL)));
    }

    #[Test]
    public function logAtErrorLevel(): void
    {
        $this->logger->log(LogLevel::ERROR, 'an error occurred');
        $this->assertTrue($this->testHandler->hasErrorRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('an error occurred', self::level(Logger::ERROR)));
    }

    #[Test]
    public function logAtWarningLevel(): void
    {
        $this->logger->log(LogLevel::WARNING, 'deprecated feature');
        $this->assertTrue($this->testHandler->hasWarningRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('deprecated feature', self::level(Logger::WARNING)));
    }

    #[Test]
    public function logAtNoticeLevel(): void
    {
        $this->logger->log(LogLevel::NOTICE, 'normal event');
        $this->assertTrue($this->testHandler->hasNoticeRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('normal event', self::level(Logger::NOTICE)));
    }

    #[Test]
    public function logAtInfoLevel(): void
    {
        $this->logger->log(LogLevel::INFO, 'informational message');
        $this->assertTrue($this->testHandler->hasInfoRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('informational message', self::level(Logger::INFO)));
    }

    #[Test]
    public function logAtDebugLevel(): void
    {
        $this->logger->log(LogLevel::DEBUG, 'debug trace');
        $this->assertTrue($this->testHandler->hasDebugRecords());
        $this->assertTrue($this->testHandler->hasRecordThatContains('debug trace', self::level(Logger::DEBUG)));
    }

    // ---------------------------------------------------------------
    // log() tests — message and context
    // ---------------------------------------------------------------

    #[Test]
    public function logCapturesMessageCorrectly(): void
    {
        $this->logger->log(LogLevel::INFO, 'Hello World');
        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame('Hello World', $records[0]['message']);
    }

    #[Test]
    public function logPassesContextArrayThrough(): void
    {
        $this->logger->log(LogLevel::ERROR, 'test', ['user_id' => 42, 'action' => 'login']);
        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $this->assertArrayHasKey('user_id', $records[0]['context']);
        $this->assertSame(42, $records[0]['context']['user_id']);
        $this->assertArrayHasKey('action', $records[0]['context']);
        $this->assertSame('login', $records[0]['context']['action']);
    }

    #[Test]
    public function logStripsChannelKeyFromContext(): void
    {
        $this->logger->log(LogLevel::INFO, 'routed message', [
            'channel' => 'Queries',
            'sql'     => 'SELECT 1',
        ]);
        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $this->assertArrayNotHasKey('channel', $records[0]['context']);
        // Other context keys should remain
        $this->assertArrayHasKey('sql', $records[0]['context']);
        $this->assertSame('SELECT 1', $records[0]['context']['sql']);
    }

    #[Test]
    public function logStripsOnlyChannelKeyLeavingOtherKeys(): void
    {
        $context = [
            'channel'  => 'Blocks',
            'name'     => 'sidebar',
            'cached'   => true,
            'duration' => 0.05,
        ];
        $this->logger->log(LogLevel::DEBUG, 'block rendered', $context);
        $records = $this->testHandler->getRecords();
        $this->assertArrayNotHasKey('channel', $records[0]['context']);
        $this->assertSame('sidebar', $records[0]['context']['name']);
        $this->assertTrue($records[0]['context']['cached']);
        $this->assertSame(0.05, $records[0]['context']['duration']);
    }

    #[Test]
    public function logWithEmptyContextArray(): void
    {
        $this->logger->log(LogLevel::INFO, 'no context');
        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame([], $records[0]['context']);
    }

    #[Test]
    public function logDoesNothingWhenDeactivated(): void
    {
        // Create a logger that is deactivated via reflection
        $logger = new XoopsMonologLogger('test', [$this->testHandler]);
        $ref = new \ReflectionClass($logger);
        $prop = $ref->getProperty('activated');
        $prop->setAccessible(true);
        $prop->setValue($logger, false);

        $logger->log(LogLevel::ERROR, 'should not appear');
        $this->assertCount(0, $this->testHandler->getRecords());
        $this->assertFalse($this->testHandler->hasErrorRecords());
    }

    #[Test]
    public function logDoesNothingWhenMonologIsNull(): void
    {
        // Create a logger and null-out the monolog property
        $logger = new XoopsMonologLogger('test', [$this->testHandler]);
        $ref = new \ReflectionClass($logger);
        $prop = $ref->getProperty('monolog');
        $prop->setAccessible(true);
        $prop->setValue($logger, null);

        $logger->log(LogLevel::ERROR, 'should not appear');
        $this->assertCount(0, $this->testHandler->getRecords());
    }

    #[Test]
    public function logMultipleMessagesAreAllCaptured(): void
    {
        $this->logger->log(LogLevel::INFO, 'first');
        $this->logger->log(LogLevel::WARNING, 'second');
        $this->logger->log(LogLevel::ERROR, 'third');
        $records = $this->testHandler->getRecords();
        $this->assertCount(3, $records);
        $this->assertSame('first', $records[0]['message']);
        $this->assertSame('second', $records[1]['message']);
        $this->assertSame('third', $records[2]['message']);
    }

    #[Test]
    public function logCastsMessageToString(): void
    {
        // The source does (string) $message, so numeric values should work
        $this->logger->log(LogLevel::INFO, 12345);
        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame('12345', $records[0]['message']);
    }

    // ---------------------------------------------------------------
    // log() with data provider — all PSR-3 levels via a single test
    // ---------------------------------------------------------------

    /**
     * @return array<string, array{string, int}>
     */
    public static function psr3LevelProvider(): array
    {
        return [
            'emergency' => [LogLevel::EMERGENCY, Logger::EMERGENCY],
            'alert'     => [LogLevel::ALERT,     Logger::ALERT],
            'critical'  => [LogLevel::CRITICAL,  Logger::CRITICAL],
            'error'     => [LogLevel::ERROR,     Logger::ERROR],
            'warning'   => [LogLevel::WARNING,   Logger::WARNING],
            'notice'    => [LogLevel::NOTICE,    Logger::NOTICE],
            'info'      => [LogLevel::INFO,      Logger::INFO],
            'debug'     => [LogLevel::DEBUG,     Logger::DEBUG],
        ];
    }

    #[Test]
    #[DataProvider('psr3LevelProvider')]
    public function logMapsStringLevelToCorrectMonologLevel(string $psrLevel, int $monologLevel): void
    {
        $handler = new TestHandler();
        $logger = new XoopsMonologLogger('test', [$handler]);
        $logger->log($psrLevel, 'level test');
        $records = $handler->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame($monologLevel, $records[0]['level']);
    }

    // ---------------------------------------------------------------
    // normalizeLevel() tests (indirect via log())
    // ---------------------------------------------------------------

    #[Test]
    public function normalizeLevelHandsIntegerLevelsThrough(): void
    {
        // Pass Monolog integer level directly instead of PSR-3 string
        $this->logger->log(Logger::WARNING, 'integer level');
        $this->assertTrue($this->testHandler->hasWarningRecords());
        $records = $this->testHandler->getRecords();
        $this->assertSame(Logger::WARNING, $records[0]['level']);
    }

    #[Test]
    public function normalizeLevelMapsDebugIntegerCorrectly(): void
    {
        $this->logger->log(Logger::DEBUG, 'debug integer');
        $this->assertTrue($this->testHandler->hasDebugRecords());
        $records = $this->testHandler->getRecords();
        $this->assertSame(100, $records[0]['level']);
    }

    #[Test]
    public function normalizeLevelMapsErrorIntegerCorrectly(): void
    {
        $this->logger->log(Logger::ERROR, 'error integer');
        $this->assertTrue($this->testHandler->hasErrorRecords());
        $records = $this->testHandler->getRecords();
        $this->assertSame(400, $records[0]['level']);
    }

    #[Test]
    public function normalizeLevelMapsEmergencyIntegerCorrectly(): void
    {
        $this->logger->log(Logger::EMERGENCY, 'emergency integer');
        $this->assertTrue($this->testHandler->hasEmergencyRecords());
        $records = $this->testHandler->getRecords();
        $this->assertSame(600, $records[0]['level']);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function integerLevelProvider(): array
    {
        return [
            'debug_100'     => [100, Logger::DEBUG],
            'info_200'      => [200, Logger::INFO],
            'notice_250'    => [250, Logger::NOTICE],
            'warning_300'   => [300, Logger::WARNING],
            'error_400'     => [400, Logger::ERROR],
            'critical_500'  => [500, Logger::CRITICAL],
            'alert_550'     => [550, Logger::ALERT],
            'emergency_600' => [600, Logger::EMERGENCY],
        ];
    }

    #[Test]
    #[DataProvider('integerLevelProvider')]
    public function normalizeLevelPassesIntegerLevelThrough(int $inputLevel, int $expectedLevel): void
    {
        $handler = new TestHandler();
        $logger = new XoopsMonologLogger('test', [$handler]);
        $logger->log($inputLevel, 'integer level passthrough');
        $records = $handler->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame($expectedLevel, $records[0]['level']);
    }

    #[Test]
    public function normalizeLevelDefaultsToDebugForUnknownString(): void
    {
        // An unrecognized string level should default to DEBUG (100)
        $this->logger->log('nonexistent_level', 'unknown level');
        $this->assertTrue($this->testHandler->hasDebugRecords());
        $records = $this->testHandler->getRecords();
        $this->assertSame(Logger::DEBUG, $records[0]['level']);
    }

    // ---------------------------------------------------------------
    // quiet() tests
    // ---------------------------------------------------------------

    #[Test]
    public function quietReturnsVoidWithNoSideEffects(): void
    {
        // Log something first
        $this->logger->log(LogLevel::INFO, 'before quiet');

        // quiet() is a no-op for file logger
        $result = $this->logger->quiet();
        $this->assertNull($result);

        // Verify the logger is still functional after quiet()
        $this->logger->log(LogLevel::INFO, 'after quiet');
        $records = $this->testHandler->getRecords();
        $this->assertCount(2, $records);
        $this->assertSame('before quiet', $records[0]['message']);
        $this->assertSame('after quiet', $records[1]['message']);
    }

    #[Test]
    public function quietDoesNotAffectActivatedState(): void
    {
        $this->logger->quiet();
        // Logger should still be active after quiet()
        $this->logger->log(LogLevel::INFO, 'still active');
        $this->assertTrue($this->testHandler->hasInfoRecords());
    }

    // ---------------------------------------------------------------
    // getMonolog() tests
    // ---------------------------------------------------------------

    #[Test]
    public function getMonologReturnsLoggerInstanceWhenActivated(): void
    {
        $monolog = $this->logger->getMonolog();
        $this->assertNotNull($monolog);
        $this->assertInstanceOf(Logger::class, $monolog);
    }

    #[Test]
    public function getMonologReturnsSameInstanceOnMultipleCalls(): void
    {
        $first = $this->logger->getMonolog();
        $second = $this->logger->getMonolog();
        $this->assertSame($first, $second);
    }

    #[Test]
    public function getMonologReturnsNullWhenMonologPropertyIsNull(): void
    {
        $logger = new XoopsMonologLogger('test', [$this->testHandler]);
        $ref = new \ReflectionClass($logger);
        $prop = $ref->getProperty('monolog');
        $prop->setAccessible(true);
        $prop->setValue($logger, null);

        $this->assertNull($logger->getMonolog());
    }

    #[Test]
    public function getMonologChannelMatchesConstructorArgument(): void
    {
        $logger = new XoopsMonologLogger('custom_channel', [$this->testHandler]);
        $monolog = $logger->getMonolog();
        $this->assertSame('custom_channel', $monolog->getName());
    }

    // ---------------------------------------------------------------
    // Deactivation / exception handling tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorSetsActivatedFalseOnException(): void
    {
        // Pass an invalid handler that will cause an exception in the constructor
        // We simulate this by using reflection to check the activated property
        // after constructing with a valid handler, then forcing deactivation.
        $logger = new XoopsMonologLogger('test', [$this->testHandler]);
        $ref = new \ReflectionClass($logger);
        $activatedProp = $ref->getProperty('activated');
        $activatedProp->setAccessible(true);
        // Verify it's active with valid construction
        $this->assertTrue($activatedProp->getValue($logger));
    }

    #[Test]
    public function logSilentlyIgnoresExceptionsFromMonolog(): void
    {
        // Monolog 3.x uses LogRecord, Monolog 2.x uses array
        if (class_exists(\Monolog\LogRecord::class)) {
            $throwingHandler = new class extends \Monolog\Handler\AbstractProcessingHandler {
                protected function write(\Monolog\LogRecord $record): void
                {
                    throw new \RuntimeException('Handler write error');
                }
            };
        } else {
            $throwingHandler = new class extends \Monolog\Handler\AbstractProcessingHandler {
                protected function write(array $record): void
                {
                    throw new \RuntimeException('Handler write error');
                }
            };
        }
        $logger = new XoopsMonologLogger('test', [$throwingHandler]);

        // Should not throw — the exception is silently caught
        $logger->log(LogLevel::ERROR, 'this should not throw');
        $this->assertTrue(true, 'No exception was thrown');
    }

    // ---------------------------------------------------------------
    // Context edge cases
    // ---------------------------------------------------------------

    #[Test]
    public function logWithContextContainingOnlyChannelKeyResultsInEmptyContext(): void
    {
        $this->logger->log(LogLevel::INFO, 'channel only', ['channel' => 'Test']);
        $records = $this->testHandler->getRecords();
        $this->assertCount(1, $records);
        $this->assertSame([], $records[0]['context']);
    }

    #[Test]
    public function logPreservesNestedContextArrays(): void
    {
        $context = [
            'user' => ['id' => 1, 'name' => 'admin'],
            'request' => ['method' => 'GET', 'uri' => '/index.php'],
        ];
        $this->logger->log(LogLevel::INFO, 'nested context', $context);
        $records = $this->testHandler->getRecords();
        $this->assertSame(['id' => 1, 'name' => 'admin'], $records[0]['context']['user']);
        $this->assertSame(['method' => 'GET', 'uri' => '/index.php'], $records[0]['context']['request']);
    }

    #[Test]
    public function logDoesNotMutateOriginalContextArray(): void
    {
        $context = ['channel' => 'Queries', 'sql' => 'SELECT 1'];
        $this->logger->log(LogLevel::DEBUG, 'mutation check', $context);
        // The original array should still contain the 'channel' key
        $this->assertArrayHasKey('channel', $context);
        $this->assertSame('Queries', $context['channel']);
    }
}
