<?php

declare(strict_types=1);

namespace xoopsxml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XmlTagHandler;

/**
 * Comprehensive test suite for the XmlTagHandler class.
 *
 * XmlTagHandler is a base class used by SaxParser to dispatch XML parsing
 * events to specific tag handlers. It provides stub methods that subclasses
 * override to handle begin/end elements and character data.
 *
 * @see \XmlTagHandler
 */
#[CoversClass(XmlTagHandler::class)]
class XmlTagHandlerTest extends TestCase
{
    /**
     * @var XmlTagHandler
     */
    private XmlTagHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new XmlTagHandler();
    }

    // ---------------------------------------------------------------
    // Construction tests
    // ---------------------------------------------------------------

    /**
     * Verify that XmlTagHandler can be instantiated without errors.
     */
    #[Test]
    public function canBeInstantiated(): void
    {
        $handler = new XmlTagHandler();
        $this->assertInstanceOf(XmlTagHandler::class, $handler);
    }

    /**
     * Verify that multiple instances are independent objects.
     */
    #[Test]
    public function multipleInstancesAreIndependent(): void
    {
        $handler1 = new XmlTagHandler();
        $handler2 = new XmlTagHandler();
        $this->assertNotSame($handler1, $handler2);
    }

    // ---------------------------------------------------------------
    // getName() tests
    // ---------------------------------------------------------------

    /**
     * Verify that getName() returns an empty string for the base class.
     */
    #[Test]
    public function getNameReturnsEmptyString(): void
    {
        $this->assertSame('', $this->handler->getName());
    }

    /**
     * Verify that getName() return type is a string.
     */
    #[Test]
    public function getNameReturnTypeIsString(): void
    {
        $result = $this->handler->getName();
        $this->assertIsString($result);
    }

    /**
     * Verify that getName() consistently returns the same value.
     */
    #[Test]
    public function getNameIsConsistent(): void
    {
        $this->assertSame($this->handler->getName(), $this->handler->getName());
    }

    // ---------------------------------------------------------------
    // handleBeginElement() tests
    // ---------------------------------------------------------------

    /**
     * Verify that handleBeginElement() accepts arguments without error.
     */
    #[Test]
    public function handleBeginElementAcceptsArgsWithoutError(): void
    {
        $parser = null;
        $attributes = [];
        // Should execute without throwing any exception
        $this->handler->handleBeginElement($parser, $attributes);
        $this->assertTrue(true, 'handleBeginElement completed without error');
    }

    /**
     * Verify that handleBeginElement() accepts populated attributes array.
     */
    #[Test]
    public function handleBeginElementAcceptsPopulatedAttributes(): void
    {
        $parser = null;
        $attributes = ['id' => '1', 'name' => 'test'];
        $this->handler->handleBeginElement($parser, $attributes);
        $this->assertTrue(true, 'handleBeginElement completed with populated attributes');
    }

    /**
     * Verify that handleBeginElement() returns null (void method).
     */
    #[Test]
    public function handleBeginElementReturnsNull(): void
    {
        $parser = null;
        $attributes = [];
        $result = $this->handler->handleBeginElement($parser, $attributes);
        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // handleEndElement() tests
    // ---------------------------------------------------------------

    /**
     * Verify that handleEndElement() accepts arguments without error.
     */
    #[Test]
    public function handleEndElementAcceptsArgsWithoutError(): void
    {
        $parser = null;
        $this->handler->handleEndElement($parser);
        $this->assertTrue(true, 'handleEndElement completed without error');
    }

    /**
     * Verify that handleEndElement() accepts a mock parser object.
     */
    #[Test]
    public function handleEndElementAcceptsMockParser(): void
    {
        $parser = new \stdClass();
        $this->handler->handleEndElement($parser);
        $this->assertTrue(true, 'handleEndElement completed with stdClass parser');
    }

    /**
     * Verify that handleEndElement() returns null (void method).
     */
    #[Test]
    public function handleEndElementReturnsNull(): void
    {
        $parser = null;
        $result = $this->handler->handleEndElement($parser);
        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // handleCharacterData() tests
    // ---------------------------------------------------------------

    /**
     * Verify that handleCharacterData() accepts arguments without error.
     */
    #[Test]
    public function handleCharacterDataAcceptsArgsWithoutError(): void
    {
        $parser = null;
        $data = 'some character data';
        $this->handler->handleCharacterData($parser, $data);
        $this->assertTrue(true, 'handleCharacterData completed without error');
    }

    /**
     * Verify that handleCharacterData() accepts empty string data.
     */
    #[Test]
    public function handleCharacterDataAcceptsEmptyString(): void
    {
        $parser = null;
        $data = '';
        $this->handler->handleCharacterData($parser, $data);
        $this->assertTrue(true, 'handleCharacterData completed with empty data');
    }

    /**
     * Verify that handleCharacterData() returns null (void method).
     */
    #[Test]
    public function handleCharacterDataReturnsNull(): void
    {
        $parser = null;
        $data = 'test data';
        $result = $this->handler->handleCharacterData($parser, $data);
        $this->assertNull($result);
    }

    /**
     * Verify that handleCharacterData() does not modify the data reference.
     */
    #[Test]
    public function handleCharacterDataDoesNotModifyData(): void
    {
        $parser = null;
        $data = 'original data';
        $this->handler->handleCharacterData($parser, $data);
        $this->assertSame('original data', $data);
    }

    // ---------------------------------------------------------------
    // Subclass override tests
    // ---------------------------------------------------------------

    /**
     * Verify that a subclass can override getName() to return a custom string.
     */
    #[Test]
    public function subclassCanOverrideGetName(): void
    {
        $subclass = new class extends XmlTagHandler {
            public function getName()
            {
                return 'customTag';
            }
        };

        $this->assertSame('customTag', $subclass->getName());
    }

    /**
     * Verify that a subclass can override getName() to return an array.
     * SaxParser::addTagHandler() supports array names for multiple tags.
     */
    #[Test]
    public function subclassCanOverrideGetNameWithArray(): void
    {
        $subclass = new class extends XmlTagHandler {
            public function getName()
            {
                return ['tag1', 'tag2', 'tag3'];
            }
        };

        $result = $subclass->getName();
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertSame(['tag1', 'tag2', 'tag3'], $result);
    }

    /**
     * Verify that a subclass can override handleBeginElement().
     */
    #[Test]
    public function subclassCanOverrideHandleBeginElement(): void
    {
        $called = false;
        $subclass = new class($called) extends XmlTagHandler {
            private $called;
            public function __construct(&$called)
            {
                parent::__construct();
                $this->called = &$called;
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->called = true;
            }
        };

        $attrs = ['id' => '1'];
        $subclass->handleBeginElement(null, $attrs);
        $this->assertTrue($called, 'Subclass handleBeginElement was called');
    }

    /**
     * Verify that a subclass can override handleEndElement().
     */
    #[Test]
    public function subclassCanOverrideHandleEndElement(): void
    {
        $called = false;
        $subclass = new class($called) extends XmlTagHandler {
            private $called;
            public function __construct(&$called)
            {
                parent::__construct();
                $this->called = &$called;
            }
            public function handleEndElement($parser)
            {
                $this->called = true;
            }
        };

        $subclass->handleEndElement(null);
        $this->assertTrue($called, 'Subclass handleEndElement was called');
    }

    /**
     * Verify that a subclass can override handleCharacterData().
     */
    #[Test]
    public function subclassCanOverrideHandleCharacterData(): void
    {
        $captured = '';
        $subclass = new class($captured) extends XmlTagHandler {
            private $captured;
            public function __construct(&$captured)
            {
                parent::__construct();
                $this->captured = &$captured;
            }
            public function handleCharacterData($parser, &$data)
            {
                $this->captured = $data;
            }
        };

        $data = 'Hello World';
        $subclass->handleCharacterData(null, $data);
        $this->assertSame('Hello World', $captured);
    }

    /**
     * Verify that XmlTagHandler is not abstract and can be used directly.
     */
    #[Test]
    public function classIsNotAbstract(): void
    {
        $ref = new \ReflectionClass(XmlTagHandler::class);
        $this->assertFalse($ref->isAbstract());
    }

    /**
     * Verify that the base class has exactly the expected public methods.
     */
    #[Test]
    public function classHasExpectedPublicMethods(): void
    {
        $ref = new \ReflectionClass(XmlTagHandler::class);
        $methods = array_map(
            function (\ReflectionMethod $m) {
                return $m->getName();
            },
            $ref->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        $this->assertContains('__construct', $methods);
        $this->assertContains('getName', $methods);
        $this->assertContains('handleBeginElement', $methods);
        $this->assertContains('handleEndElement', $methods);
        $this->assertContains('handleCharacterData', $methods);
    }
}
