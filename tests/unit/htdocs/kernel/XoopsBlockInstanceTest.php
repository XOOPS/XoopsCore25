<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsBlockInstance;
use XoopsBlockInstanceHandler;

require_once XOOPS_ROOT_PATH . '/kernel/blockinstance.php';

/**
 * Unit tests for deprecated XoopsBlockInstance and XoopsBlockInstanceHandler classes.
 *
 * Both classes are deprecated stubs. Their constructors are empty, and all
 * magic methods (__call, __set, __get) log a deprecation message via
 * $GLOBALS['xoopsLogger']->addDeprecated() and return null.
 */
class XoopsBlockInstanceTest extends KernelTestCase
{
    /**
     * @var \XoopsLogger|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(\XoopsLogger::class);
        $GLOBALS['xoopsLogger'] = $this->logger;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['xoopsLogger']);
        parent::tearDown();
    }

    // =========================================================================
    // XoopsBlockInstance — instantiation
    // =========================================================================

    public function testBlockInstanceCanBeInstantiated(): void
    {
        $instance = new XoopsBlockInstance();

        $this->assertInstanceOf(XoopsBlockInstance::class, $instance);
    }

    // =========================================================================
    // XoopsBlockInstance — __call
    // =========================================================================

    public function testBlockInstanceCallReturnsNull(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated');

        $result = $instance->someMethod('arg1', 'arg2');

        $this->assertNull($result);
    }

    public function testBlockInstanceCallLogsDeprecationWithClassName(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstance'));

        $instance->someMethod();
    }

    public function testBlockInstanceCallLogsDeprecationWithMethodName(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains("'testMethod'"));

        $instance->testMethod();
    }

    public function testBlockInstanceCallMessageContainsNotExecuted(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('is not executed'));

        $instance->doSomething();
    }

    public function testBlockInstanceCallWithEmptyMethodName(): void
    {
        // PHP does not allow calling an empty method name via normal syntax,
        // so we use call_user_func to invoke __call directly.
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->logicalNot($this->stringContains('is not executed')));

        $result = $instance->__call('', []);

        $this->assertNull($result);
    }

    #[DataProvider('methodNameProvider')]
    public function testBlockInstanceCallWithVariousMethodNames(string $methodName): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstance'));

        $result = $instance->__call($methodName, []);

        $this->assertNull($result);
    }

    // =========================================================================
    // XoopsBlockInstance — __set
    // =========================================================================

    public function testBlockInstanceSetReturnsNull(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated');

        // __set returns null; the assignment expression yields the assigned value in PHP,
        // so we call __set directly to verify the return value.
        $result = $instance->__set('someProperty', 'value');

        $this->assertNull($result);
    }

    public function testBlockInstanceSetLogsDeprecationWithClassName(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstance'));

        $instance->myVar = 'test';
    }

    public function testBlockInstanceSetLogsDeprecationWithVariableName(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->stringContains("'title'"));

        $instance->title = 'Test Title';
    }

    public function testBlockInstanceSetMessageContainsNotSet(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->stringContains('is not set'));

        $instance->options = 'some|options';
    }

    // =========================================================================
    // XoopsBlockInstance — __get
    // =========================================================================

    public function testBlockInstanceGetReturnsNull(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated');

        $result = $instance->someProperty;

        $this->assertNull($result);
    }

    public function testBlockInstanceGetLogsDeprecationWithClassName(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstance'));

        $_ = $instance->myVar;
    }

    public function testBlockInstanceGetLogsDeprecationWithVariableName(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains("'bid'"));

        $_ = $instance->bid;
    }

    public function testBlockInstanceGetMessageContainsNotAvailable(): void
    {
        $instance = new XoopsBlockInstance();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('is not available'));

        $_ = $instance->content;
    }

    // =========================================================================
    // XoopsBlockInstanceHandler — instantiation
    // =========================================================================

    public function testBlockInstanceHandlerCanBeInstantiated(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->assertInstanceOf(XoopsBlockInstanceHandler::class, $handler);
    }

    // =========================================================================
    // XoopsBlockInstanceHandler — __call
    // =========================================================================

    public function testHandlerCallReturnsNull(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated');

        $result = $handler->getObjects();

        $this->assertNull($result);
    }

    public function testHandlerCallLogsDeprecationWithClassName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstanceHandler'));

        $handler->insert(null);
    }

    public function testHandlerCallLogsDeprecationWithMethodName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains("'delete'"));

        $handler->delete(42);
    }

    public function testHandlerCallMessageContainsNotExecuted(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('is not executed'));

        $handler->create();
    }

    public function testHandlerCallWithEmptyMethodName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->logicalNot($this->stringContains('is not executed')));

        $result = $handler->__call('', []);

        $this->assertNull($result);
    }

    #[DataProvider('methodNameProvider')]
    public function testHandlerCallWithVariousMethodNames(string $methodName): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstanceHandler'));

        $result = $handler->__call($methodName, []);

        $this->assertNull($result);
    }

    // =========================================================================
    // XoopsBlockInstanceHandler — __set
    // =========================================================================

    public function testHandlerSetReturnsNull(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated');

        $directResult = $handler->__set('db', 'value');

        $this->assertNull($directResult);
    }

    public function testHandlerSetLogsDeprecationWithClassName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstanceHandler'));

        $handler->table = 'newblocks';
    }

    public function testHandlerSetLogsDeprecationWithVariableName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->stringContains("'className'"));

        $handler->className = 'SomeClass';
    }

    public function testHandlerSetMessageContainsNotSet(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->stringContains('is not set'));

        $handler->keyName = 'bid';
    }

    // =========================================================================
    // XoopsBlockInstanceHandler — __get
    // =========================================================================

    public function testHandlerGetReturnsNull(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated');

        $result = $handler->db;

        $this->assertNull($result);
    }

    public function testHandlerGetLogsDeprecationWithClassName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('XoopsBlockInstanceHandler'));

        $_ = $handler->table;
    }

    public function testHandlerGetLogsDeprecationWithVariableName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains("'keyName'"));

        $_ = $handler->keyName;
    }

    public function testHandlerGetMessageContainsNotAvailable(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->stringContains('is not available'));

        $_ = $handler->className;
    }

    // =========================================================================
    // Exact deprecation message format verification
    // =========================================================================

    public function testBlockInstanceCallExactMessage(): void
    {
        $instance = new XoopsBlockInstance();

        $expectedMessage = "Class 'XoopsBlockInstance' is deprecated thus the method 'getData' is not executed!";

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $instance->getData();
    }

    public function testBlockInstanceCallExactMessageWithEmptyName(): void
    {
        $instance = new XoopsBlockInstance();

        $expectedMessage = "Class 'XoopsBlockInstance' is deprecated!";

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $instance->__call('', []);
    }

    public function testBlockInstanceSetExactMessage(): void
    {
        $instance = new XoopsBlockInstance();

        $expectedMessage = "Class 'XoopsBlockInstance' is deprecated thus the variable 'weight' is not set!";

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $instance->weight = 5;
    }

    public function testBlockInstanceGetExactMessage(): void
    {
        $instance = new XoopsBlockInstance();

        $expectedMessage = "Class 'XoopsBlockInstance' is deprecated thus the variable 'side' is not available!";

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $_ = $instance->side;
    }

    public function testHandlerCallExactMessage(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $expectedMessage = "Class 'XoopsBlockInstanceHandler' is deprecated thus the method 'get' is not executed!";

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $handler->get(1);
    }

    public function testHandlerCallExactMessageWithEmptyName(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $expectedMessage = "Class 'XoopsBlockInstanceHandler' is deprecated!";

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $handler->__call('', []);
    }

    public function testHandlerSetExactMessage(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $expectedMessage = "Class 'XoopsBlockInstanceHandler' is deprecated thus the variable 'table' is not set!";

        $this->logger->expects($this->atLeastOnce())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $handler->table = 'blocks';
    }

    public function testHandlerGetExactMessage(): void
    {
        $handler = new XoopsBlockInstanceHandler();

        $expectedMessage = "Class 'XoopsBlockInstanceHandler' is deprecated thus the variable 'db' is not available!";

        $this->logger->expects($this->once())
            ->method('addDeprecated')
            ->with($this->identicalTo($expectedMessage));

        $_ = $handler->db;
    }

    // =========================================================================
    // Data providers
    // =========================================================================

    /**
     * @return array<string, array{string}>
     */
    public static function methodNameProvider(): array
    {
        return [
            'simple method'       => ['get'],
            'camelCase method'    => ['getObjects'],
            'underscored method'  => ['get_list'],
            'numeric suffix'      => ['create2'],
            'single character'    => ['x'],
        ];
    }
}
