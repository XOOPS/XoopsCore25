<?php

declare(strict_types=1);

namespace xoopsxml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SaxParser;
use XmlTagHandler;

/**
 * Comprehensive test suite for the SaxParser class.
 *
 * SaxParser wraps PHP's xml_parser functions to provide an OOP interface
 * for SAX-style XML parsing with pluggable tag handlers.
 *
 * @see \SaxParser
 */
#[CoversClass(SaxParser::class)]
class SaxParserTest extends TestCase
{
    /**
     * @var SaxParser
     */
    private SaxParser $parser;

    protected function setUp(): void
    {
        $this->parser = new SaxParser('<?xml version="1.0"?><root/>');
    }

    protected function tearDown(): void
    {
        // Free parser resource to avoid leaks
        // Suppress errors in case the parser was already freed by a test
        @xml_parser_free($this->parser->parser);
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    /**
     * Verify that SaxParser can be constructed with a string input.
     */
    #[Test]
    public function constructorWithStringInput(): void
    {
        $parser = new SaxParser('<?xml version="1.0"?><root/>');
        $this->assertInstanceOf(SaxParser::class, $parser);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor initializes level to 0.
     */
    #[Test]
    public function constructorInitializesLevelToZero(): void
    {
        $parser = new SaxParser('<root/>');
        $this->assertSame(0, $parser->level);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor creates an XML parser resource/object.
     */
    #[Test]
    public function constructorCreatesXmlParser(): void
    {
        $parser = new SaxParser('<root/>');
        // In PHP 8.x, xml_parser_create returns an XMLParser object
        $this->assertInstanceOf(\XMLParser::class, $parser->parser);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor stores the input.
     */
    #[Test]
    public function constructorStoresInput(): void
    {
        $xml = '<?xml version="1.0"?><root/>';
        $parser = new SaxParser($xml);
        $this->assertSame($xml, $parser->input);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor sets case folding to false by default.
     */
    #[Test]
    public function constructorSetsCaseFoldingToFalse(): void
    {
        $parser = new SaxParser('<root/>');
        $this->assertFalse($parser->isCaseFolding);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor sets encoding to UTF-8 by default.
     */
    #[Test]
    public function constructorSetsUtfEncodingByDefault(): void
    {
        $parser = new SaxParser('<root/>');
        $this->assertSame('UTF-8', $parser->targetEncoding);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor initializes empty tagHandlers array.
     */
    #[Test]
    public function constructorInitializesEmptyTagHandlers(): void
    {
        $parser = new SaxParser('<root/>');
        $this->assertIsArray($parser->tagHandlers);
        $this->assertEmpty($parser->tagHandlers);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor initializes empty tags array.
     */
    #[Test]
    public function constructorInitializesEmptyTagsArray(): void
    {
        $parser = new SaxParser('<root/>');
        $this->assertIsArray($parser->tags);
        $this->assertEmpty($parser->tags);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the constructor initializes empty errors array.
     */
    #[Test]
    public function constructorInitializesEmptyErrorsArray(): void
    {
        $parser = new SaxParser('<root/>');
        $this->assertIsArray($parser->errors);
        $this->assertEmpty($parser->errors);
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // getCurrentLevel() tests
    // ---------------------------------------------------------------

    /**
     * Verify that getCurrentLevel() returns 0 initially.
     */
    #[Test]
    public function getCurrentLevelStartsAtZero(): void
    {
        $this->assertSame(0, $this->parser->getCurrentLevel());
    }

    /**
     * Verify that getCurrentLevel() returns the level property.
     */
    #[Test]
    public function getCurrentLevelReflectsProperty(): void
    {
        $this->parser->level = 5;
        $this->assertSame(5, $this->parser->getCurrentLevel());
    }

    // ---------------------------------------------------------------
    // setCaseFolding() tests
    // ---------------------------------------------------------------

    /**
     * Verify setCaseFolding(true) sets the isCaseFolding property to true.
     */
    #[Test]
    public function setCaseFoldingTrue(): void
    {
        $this->parser->setCaseFolding(true);
        $this->assertTrue($this->parser->isCaseFolding);
    }

    /**
     * Verify setCaseFolding(false) sets the isCaseFolding property to false.
     */
    #[Test]
    public function setCaseFoldingFalse(): void
    {
        $this->parser->setCaseFolding(true);
        $this->parser->setCaseFolding(false);
        $this->assertFalse($this->parser->isCaseFolding);
    }

    // ---------------------------------------------------------------
    // Encoding methods
    // ---------------------------------------------------------------

    /**
     * Verify that useIsoEncoding() sets targetEncoding to ISO-8859-1.
     */
    #[Test]
    public function useIsoEncodingSetsCorrectEncoding(): void
    {
        $this->parser->useIsoEncoding();
        $this->assertSame('ISO-8859-1', $this->parser->targetEncoding);
    }

    /**
     * Verify that useAsciiEncoding() sets targetEncoding to US-ASCII.
     */
    #[Test]
    public function useAsciiEncodingSetsCorrectEncoding(): void
    {
        $this->parser->useAsciiEncoding();
        $this->assertSame('US-ASCII', $this->parser->targetEncoding);
    }

    /**
     * Verify that useUtfEncoding() sets targetEncoding to UTF-8.
     */
    #[Test]
    public function useUtfEncodingSetsCorrectEncoding(): void
    {
        // Change first, then restore
        $this->parser->useIsoEncoding();
        $this->parser->useUtfEncoding();
        $this->assertSame('UTF-8', $this->parser->targetEncoding);
    }

    /**
     * Verify encoding methods can be called in sequence.
     */
    #[Test]
    public function encodingMethodsCanBeCalledInSequence(): void
    {
        $this->parser->useIsoEncoding();
        $this->assertSame('ISO-8859-1', $this->parser->targetEncoding);

        $this->parser->useAsciiEncoding();
        $this->assertSame('US-ASCII', $this->parser->targetEncoding);

        $this->parser->useUtfEncoding();
        $this->assertSame('UTF-8', $this->parser->targetEncoding);
    }

    // ---------------------------------------------------------------
    // getCurrentTag() tests
    // ---------------------------------------------------------------

    /**
     * Verify getCurrentTag() returns the last tag from the stack.
     */
    #[Test]
    public function getCurrentTagReturnsLastTag(): void
    {
        $this->parser->tags = ['root', 'child', 'grandchild'];
        $this->assertSame('grandchild', $this->parser->getCurrentTag());
    }

    /**
     * Verify getCurrentTag() with a single tag on the stack.
     */
    #[Test]
    public function getCurrentTagWithSingleTag(): void
    {
        $this->parser->tags = ['root'];
        $this->assertSame('root', $this->parser->getCurrentTag());
    }

    // ---------------------------------------------------------------
    // getParentTag() tests
    // ---------------------------------------------------------------

    /**
     * Verify getParentTag() returns the second-to-last tag.
     */
    #[Test]
    public function getParentTagReturnsSecondToLastTag(): void
    {
        $this->parser->tags = ['root', 'child', 'grandchild'];
        $this->assertSame('child', $this->parser->getParentTag());
    }

    /**
     * Verify getParentTag() returns false when only one tag is on the stack.
     */
    #[Test]
    public function getParentTagReturnsFalseWithOneTag(): void
    {
        $this->parser->tags = ['root'];
        $this->assertFalse($this->parser->getParentTag());
    }

    /**
     * Verify getParentTag() returns false with an empty tag stack.
     */
    #[Test]
    public function getParentTagReturnsFalseWithEmptyStack(): void
    {
        $this->parser->tags = [];
        $this->assertFalse($this->parser->getParentTag());
    }

    /**
     * Verify getParentTag() with exactly two tags on the stack.
     */
    #[Test]
    public function getParentTagWithTwoTags(): void
    {
        $this->parser->tags = ['root', 'child'];
        $this->assertSame('root', $this->parser->getParentTag());
    }

    // ---------------------------------------------------------------
    // parse() tests
    // ---------------------------------------------------------------

    /**
     * Verify parse() returns true for valid XML.
     */
    #[Test]
    public function parseReturnsTrueForValidXml(): void
    {
        $parser = new SaxParser('<?xml version="1.0"?><root><item id="1">Hello</item></root>');
        $result = $parser->parse();
        $this->assertTrue($result);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() returns false for invalid XML.
     */
    #[Test]
    public function parseReturnsFalseForInvalidXml(): void
    {
        $parser = new SaxParser('<root><unclosed>');
        $result = $parser->parse();
        $this->assertFalse($result);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() sets errors for invalid XML.
     */
    #[Test]
    public function parseSetsErrorsForInvalidXml(): void
    {
        $parser = new SaxParser('<root><unclosed>');
        $parser->parse();
        $this->assertNotEmpty($parser->errors);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() with simple well-formed XML processes without errors.
     */
    #[Test]
    public function parseWithSimpleWellFormedXml(): void
    {
        $xml = '<?xml version="1.0"?><root><child>text</child></root>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        $this->assertEmpty($parser->errors);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() with nested XML elements works correctly.
     */
    #[Test]
    public function parseWithNestedElements(): void
    {
        $xml = '<?xml version="1.0"?><root><level1><level2><level3>deep</level3></level2></level1></root>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() with attributes in elements.
     */
    #[Test]
    public function parseWithAttributes(): void
    {
        $xml = '<?xml version="1.0"?><root><item id="1" name="test">content</item></root>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() with empty root element.
     */
    #[Test]
    public function parseWithEmptyRootElement(): void
    {
        $xml = '<?xml version="1.0"?><root/>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that parse() updates the level during parsing.
     * After complete parsing, level should return to 0.
     */
    #[Test]
    public function parseLevelReturnsToZeroAfterParsing(): void
    {
        $xml = '<?xml version="1.0"?><root><child>text</child></root>';
        $parser = new SaxParser($xml);
        $parser->parse();
        $this->assertSame(0, $parser->getCurrentLevel());
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that tags stack is empty after complete parsing.
     */
    #[Test]
    public function parseTagsStackEmptyAfterComplete(): void
    {
        $xml = '<?xml version="1.0"?><root><child>text</child></root>';
        $parser = new SaxParser($xml);
        $parser->parse();
        $this->assertEmpty($parser->tags);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() with XML containing special characters.
     */
    #[Test]
    public function parseWithSpecialCharacters(): void
    {
        $xml = '<?xml version="1.0"?><root><item>Text with &amp; and &lt;brackets&gt;</item></root>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify parse() with XML containing multiple sibling elements.
     */
    #[Test]
    public function parseWithMultipleSiblings(): void
    {
        $xml = '<?xml version="1.0"?><root><a>1</a><b>2</b><c>3</c></root>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // free() tests
    // ---------------------------------------------------------------

    /**
     * Verify free() releases the parser resource.
     */
    #[Test]
    public function freeReleasesParser(): void
    {
        $parser = new SaxParser('<root/>');
        $parser->parse();
        // free() should not throw an exception
        $parser->free();
        $this->assertTrue(true, 'free() completed without error');
    }

    // ---------------------------------------------------------------
    // getXmlError() tests
    // ---------------------------------------------------------------

    /**
     * Verify getXmlError() returns a string with error information.
     */
    #[Test]
    public function getXmlErrorReturnsString(): void
    {
        $result = $this->parser->getXmlError();
        $this->assertIsString($result);
        $this->assertStringContainsString('XmlParse error:', $result);
    }

    /**
     * Verify getXmlError() includes line number information.
     */
    #[Test]
    public function getXmlErrorIncludesLineNumber(): void
    {
        $result = $this->parser->getXmlError();
        $this->assertStringContainsString('at line', $result);
    }

    // ---------------------------------------------------------------
    // addTagHandler() tests
    // ---------------------------------------------------------------

    /**
     * Verify addTagHandler() with handler that has a string name.
     */
    #[Test]
    public function addTagHandlerWithStringName(): void
    {
        $handler = new class extends XmlTagHandler {
            public function getName()
            {
                return 'testTag';
            }
        };

        $this->parser->addTagHandler($handler);
        $this->assertArrayHasKey('testTag', $this->parser->tagHandlers);
        $this->assertSame($handler, $this->parser->tagHandlers['testTag']);
    }

    /**
     * Verify addTagHandler() with handler that has an array name (multiple tags).
     */
    #[Test]
    public function addTagHandlerWithArrayName(): void
    {
        $handler = new class extends XmlTagHandler {
            public function getName()
            {
                return ['tag1', 'tag2', 'tag3'];
            }
        };

        $this->parser->addTagHandler($handler);
        $this->assertArrayHasKey('tag1', $this->parser->tagHandlers);
        $this->assertArrayHasKey('tag2', $this->parser->tagHandlers);
        $this->assertArrayHasKey('tag3', $this->parser->tagHandlers);
        $this->assertSame($handler, $this->parser->tagHandlers['tag1']);
        $this->assertSame($handler, $this->parser->tagHandlers['tag2']);
        $this->assertSame($handler, $this->parser->tagHandlers['tag3']);
    }

    /**
     * Verify addTagHandler() overwrites existing handler for same tag name.
     */
    #[Test]
    public function addTagHandlerOverwritesExistingHandler(): void
    {
        $handler1 = new class extends XmlTagHandler {
            public function getName()
            {
                return 'sameTag';
            }
        };
        $handler2 = new class extends XmlTagHandler {
            public function getName()
            {
                return 'sameTag';
            }
        };

        $this->parser->addTagHandler($handler1);
        $this->parser->addTagHandler($handler2);
        $this->assertSame($handler2, $this->parser->tagHandlers['sameTag']);
    }

    /**
     * Verify adding multiple different tag handlers.
     */
    #[Test]
    public function addMultipleDifferentHandlers(): void
    {
        $handler1 = new class extends XmlTagHandler {
            public function getName()
            {
                return 'alpha';
            }
        };
        $handler2 = new class extends XmlTagHandler {
            public function getName()
            {
                return 'beta';
            }
        };

        $this->parser->addTagHandler($handler1);
        $this->parser->addTagHandler($handler2);

        $this->assertCount(2, $this->parser->tagHandlers);
        $this->assertArrayHasKey('alpha', $this->parser->tagHandlers);
        $this->assertArrayHasKey('beta', $this->parser->tagHandlers);
    }

    // ---------------------------------------------------------------
    // setErrors() / getErrors() tests
    // ---------------------------------------------------------------

    /**
     * Verify setErrors() adds a single error message.
     */
    #[Test]
    public function setErrorsAddsSingleError(): void
    {
        $this->parser->setErrors('Test error');
        $this->assertCount(1, $this->parser->errors);
        $this->assertSame('Test error', $this->parser->errors[0]);
    }

    /**
     * Verify setErrors() accumulates multiple error messages.
     */
    #[Test]
    public function setErrorsAccumulatesMultipleErrors(): void
    {
        $this->parser->setErrors('Error 1');
        $this->parser->setErrors('Error 2');
        $this->parser->setErrors('Error 3');
        $this->assertCount(3, $this->parser->errors);
    }

    /**
     * Verify setErrors() trims whitespace from the error message.
     */
    #[Test]
    public function setErrorsTrimsWhitespace(): void
    {
        $this->parser->setErrors('  Error with spaces  ');
        $this->assertSame('Error with spaces', $this->parser->errors[0]);
    }

    /**
     * Verify getErrors(true) returns an HTML string with br tags.
     */
    #[Test]
    public function getErrorsAsHtmlReturnsBrSeparated(): void
    {
        $this->parser->setErrors('Error 1');
        $this->parser->setErrors('Error 2');
        $result = $this->parser->getErrors(true);
        $this->assertIsString($result);
        $this->assertStringContainsString('Error 1<br>', $result);
        $this->assertStringContainsString('Error 2<br>', $result);
    }

    /**
     * Verify getErrors(false) returns an array.
     */
    #[Test]
    public function getErrorsAsArrayReturnsArray(): void
    {
        $this->parser->setErrors('Error 1');
        $this->parser->setErrors('Error 2');
        $result = &$this->parser->getErrors(false);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('Error 1', $result[0]);
        $this->assertSame('Error 2', $result[1]);
    }

    /**
     * Verify getErrors() defaults to HTML mode.
     */
    #[Test]
    public function getErrorsDefaultsToHtml(): void
    {
        $this->parser->setErrors('Test error');
        $result = $this->parser->getErrors();
        $this->assertIsString($result);
        $this->assertStringContainsString('<br>', $result);
    }

    /**
     * Verify getErrors(true) with no errors returns an empty string.
     */
    #[Test]
    public function getErrorsHtmlWithNoErrorsReturnsEmptyString(): void
    {
        $result = $this->parser->getErrors(true);
        $this->assertSame('', $result);
    }

    /**
     * Verify getErrors(false) with no errors returns an empty array.
     */
    #[Test]
    public function getErrorsFalseWithNoErrorsReturnsEmptyArray(): void
    {
        $result = &$this->parser->getErrors(false);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // ---------------------------------------------------------------
    // handleBeginElement() / handleEndElement() dispatch tests
    // ---------------------------------------------------------------

    /**
     * Verify that parsing XML with a custom tag handler triggers
     * the handler's handleBeginElement method.
     */
    #[Test]
    public function customHandlerBeginElementIsCalled(): void
    {
        $beginCalled = false;
        $handler = new class($beginCalled) extends XmlTagHandler {
            private $beginCalled;
            public function __construct(&$beginCalled)
            {
                parent::__construct();
                $this->beginCalled = &$beginCalled;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->beginCalled = true;
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();
        $this->assertTrue($beginCalled, 'Tag handler handleBeginElement was called');
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that parsing XML with a custom tag handler triggers
     * the handler's handleEndElement method.
     */
    #[Test]
    public function customHandlerEndElementIsCalled(): void
    {
        $endCalled = false;
        $handler = new class($endCalled) extends XmlTagHandler {
            private $endCalled;
            public function __construct(&$endCalled)
            {
                parent::__construct();
                $this->endCalled = &$endCalled;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleEndElement($parser)
            {
                $this->endCalled = true;
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();
        $this->assertTrue($endCalled, 'Tag handler handleEndElement was called');
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that parsing XML with a custom tag handler triggers
     * the handler's handleCharacterData method.
     */
    #[Test]
    public function customHandlerCharacterDataIsCalled(): void
    {
        $capturedData = '';
        $handler = new class($capturedData) extends XmlTagHandler {
            private $capturedData;
            public function __construct(&$capturedData)
            {
                parent::__construct();
                $this->capturedData = &$capturedData;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleCharacterData($parser, &$data)
            {
                $this->capturedData .= $data;
            }
        };

        $xml = '<?xml version="1.0"?><root><item>Hello World</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();
        $this->assertSame('Hello World', $capturedData);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that parsing tracks levels correctly with begin/end elements.
     * Test by capturing level at begin and end of specific tags.
     */
    #[Test]
    public function parseLevelTrackedCorrectlyDuringParsing(): void
    {
        $levelsAtBegin = [];
        $handler = new class($levelsAtBegin) extends XmlTagHandler {
            private $levelsAtBegin;
            public function __construct(&$levelsAtBegin)
            {
                parent::__construct();
                $this->levelsAtBegin = &$levelsAtBegin;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->levelsAtBegin[] = $parser->getCurrentLevel();
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();
        // <root> is level 1, <item> is level 2
        $this->assertSame([2], $levelsAtBegin);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that handleBeginElement pushes tag onto the tags stack.
     */
    #[Test]
    public function handleBeginElementPushesTagOntoStack(): void
    {
        $capturedTags = [];
        $handler = new class($capturedTags) extends XmlTagHandler {
            private $capturedTags;
            public function __construct(&$capturedTags)
            {
                parent::__construct();
                $this->capturedTags = &$capturedTags;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->capturedTags = $parser->tags;
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();
        // At the time handleBeginElement fires for <item>, tags should be ['root', 'item']
        $this->assertSame(['root', 'item'], $capturedTags);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that handleEndElement pops tag from the tags stack.
     */
    #[Test]
    public function handleEndElementPopsTagFromStack(): void
    {
        $capturedTags = [];
        $handler = new class($capturedTags) extends XmlTagHandler {
            private $capturedTags;
            public function __construct(&$capturedTags)
            {
                parent::__construct();
                $this->capturedTags = &$capturedTags;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleEndElement($parser)
            {
                // At this point, 'item' has been popped from the stack
                $this->capturedTags = $parser->tags;
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();
        // After handleEndElement for <item>, only 'root' remains
        $this->assertSame(['root'], $capturedTags);
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // Default handler no-op tests
    // ---------------------------------------------------------------

    /**
     * Verify handleBeginElementDefault is a no-op.
     */
    #[Test]
    public function handleBeginElementDefaultIsNoOp(): void
    {
        $result = $this->parser->handleBeginElementDefault(null, 'tag', []);
        $this->assertNull($result);
    }

    /**
     * Verify handleEndElementDefault is a no-op.
     */
    #[Test]
    public function handleEndElementDefaultIsNoOp(): void
    {
        $result = $this->parser->handleEndElementDefault(null, 'tag');
        $this->assertNull($result);
    }

    /**
     * Verify handleCharacterDataDefault is a no-op.
     */
    #[Test]
    public function handleCharacterDataDefaultIsNoOp(): void
    {
        $result = $this->parser->handleCharacterDataDefault(null, 'data');
        $this->assertNull($result);
    }

    /**
     * Verify handleDefault is a no-op.
     */
    #[Test]
    public function handleDefaultIsNoOp(): void
    {
        $result = $this->parser->handleDefault(null, 'data');
        $this->assertNull($result);
    }

    /**
     * Verify handleUnparsedEntityDecl is a no-op.
     */
    #[Test]
    public function handleUnparsedEntityDeclIsNoOp(): void
    {
        $result = $this->parser->handleUnparsedEntityDecl(null, 'name', 'base', 'systemId', 'publicId', 'notation');
        $this->assertNull($result);
    }

    /**
     * Verify handleNotationDecl is a no-op.
     */
    #[Test]
    public function handleNotationDeclIsNoOp(): void
    {
        $result = $this->parser->handleNotationDecl(null, 'name', 'base', 'systemId', 'publicId');
        $this->assertNull($result);
    }

    /**
     * Verify handleExternalEntityRef is a no-op.
     */
    #[Test]
    public function handleExternalEntityRefIsNoOp(): void
    {
        $result = $this->parser->handleExternalEntityRef(null, 'entityNames', 'base', 'systemId', 'publicId');
        $this->assertNull($result);
    }

    /**
     * Verify handleProcessingInstruction is a no-op.
     */
    #[Test]
    public function handleProcessingInstructionIsNoOp(): void
    {
        $target = 'php';
        $data = 'echo 1;';
        $result = $this->parser->handleProcessingInstruction(null, $target, $data);
        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // Case folding integration test
    // ---------------------------------------------------------------

    /**
     * Verify that case folding disabled preserves lowercase tag names.
     */
    #[Test]
    public function caseFoldingDisabledPreservesCase(): void
    {
        $capturedTag = '';
        $handler = new class($capturedTag) extends XmlTagHandler {
            private $capturedTag;
            public function __construct(&$capturedTag)
            {
                parent::__construct();
                $this->capturedTag = &$capturedTag;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->capturedTag = $parser->getCurrentTag();
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->setCaseFolding(false);
        $parser->addTagHandler($handler);
        $parser->parse();
        $this->assertSame('item', $capturedTag);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that case folding enabled uppercases tag names.
     */
    #[Test]
    public function caseFoldingEnabledUppercasesTags(): void
    {
        $capturedTag = '';
        $handler = new class($capturedTag) extends XmlTagHandler {
            private $capturedTag;
            public function __construct(&$capturedTag)
            {
                parent::__construct();
                $this->capturedTag = &$capturedTag;
            }
            public function getName()
            {
                return 'ITEM';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->capturedTag = $parser->getCurrentTag();
            }
        };

        $xml = '<?xml version="1.0"?><root><item>text</item></root>';
        $parser = new SaxParser($xml);
        $parser->setCaseFolding(true);
        $parser->addTagHandler($handler);
        $parser->parse();
        $this->assertSame('ITEM', $capturedTag);
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // Parsing error detail tests
    // ---------------------------------------------------------------

    /**
     * Verify that the error message from invalid XML contains useful info.
     */
    #[Test]
    public function parseErrorMessageContainsXmlParseError(): void
    {
        $parser = new SaxParser('<root><unclosed>');
        $parser->parse();
        $this->assertNotEmpty($parser->errors);
        $this->assertStringContainsString('XmlParse error:', $parser->errors[0]);
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that the error message from invalid XML contains line number.
     */
    #[Test]
    public function parseErrorContainsLineNumber(): void
    {
        $parser = new SaxParser('<root><unclosed>');
        $parser->parse();
        $this->assertNotEmpty($parser->errors);
        $this->assertStringContainsString('at line', $parser->errors[0]);
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // Tags without registered handlers
    // ---------------------------------------------------------------

    /**
     * Verify parsing XML with tags that have no registered handler
     * falls through to default handlers without error.
     */
    #[Test]
    public function parseUnregisteredTagsUseDefaultHandlers(): void
    {
        $xml = '<?xml version="1.0"?><root><unknown>data</unknown></root>';
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    /**
     * Verify that multiple handlers for different tags all get called.
     */
    #[Test]
    public function multipleHandlersAllCalled(): void
    {
        $aCalled = false;
        $bCalled = false;
        $handlerA = new class($aCalled) extends XmlTagHandler {
            private $called;
            public function __construct(&$called)
            {
                parent::__construct();
                $this->called = &$called;
            }
            public function getName()
            {
                return 'alpha';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->called = true;
            }
        };

        $handlerB = new class($bCalled) extends XmlTagHandler {
            private $called;
            public function __construct(&$called)
            {
                parent::__construct();
                $this->called = &$called;
            }
            public function getName()
            {
                return 'beta';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->called = true;
            }
        };

        $xml = '<?xml version="1.0"?><root><alpha>a</alpha><beta>b</beta></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handlerA);
        $parser->addTagHandler($handlerB);
        $parser->parse();

        $this->assertTrue($aCalled, 'Handler A was called');
        $this->assertTrue($bCalled, 'Handler B was called');
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // Data provider tests
    // ---------------------------------------------------------------

    /**
     * Data provider for encoding method tests.
     *
     * @return array<string, array{string, string}>
     */
    public static function encodingProvider(): array
    {
        return [
            'ISO encoding'   => ['useIsoEncoding', 'ISO-8859-1'],
            'ASCII encoding' => ['useAsciiEncoding', 'US-ASCII'],
            'UTF-8 encoding' => ['useUtfEncoding', 'UTF-8'],
        ];
    }

    /**
     * Verify all encoding methods set the correct encoding via data provider.
     */
    #[Test]
    #[DataProvider('encodingProvider')]
    public function encodingMethodsSetsCorrectValue(string $method, string $expected): void
    {
        $this->parser->{$method}();
        $this->assertSame($expected, $this->parser->targetEncoding);
    }

    /**
     * Data provider for valid XML strings.
     *
     * @return array<string, array{string}>
     */
    public static function validXmlProvider(): array
    {
        return [
            'minimal root'         => ['<?xml version="1.0"?><root/>'],
            'root with text'       => ['<?xml version="1.0"?><root>text</root>'],
            'nested elements'      => ['<?xml version="1.0"?><root><child>text</child></root>'],
            'with attributes'      => ['<?xml version="1.0"?><root><item id="1">text</item></root>'],
            'multiple children'    => ['<?xml version="1.0"?><root><a/><b/><c/></root>'],
            'deeply nested'        => ['<?xml version="1.0"?><a><b><c><d>deep</d></c></b></a>'],
            'empty child elements' => ['<?xml version="1.0"?><root><empty/></root>'],
            'with CDATA'           => ['<?xml version="1.0"?><root><![CDATA[special <data>]]></root>'],
        ];
    }

    /**
     * Verify parse() succeeds for various valid XML inputs.
     */
    #[Test]
    #[DataProvider('validXmlProvider')]
    public function parseSucceedsForValidXml(string $xml): void
    {
        $parser = new SaxParser($xml);
        $this->assertTrue($parser->parse());
        @xml_parser_free($parser->parser);
    }

    /**
     * Data provider for invalid XML strings.
     *
     * @return array<string, array{string}>
     */
    public static function invalidXmlProvider(): array
    {
        return [
            'unclosed tag'             => ['<root><unclosed>'],
            'mismatched tags'          => ['<root><a></b></root>'],
            'invalid entity'           => ['<root>&badentity;</root>'],
        ];
    }

    /**
     * Verify parse() fails for various invalid XML inputs.
     */
    #[Test]
    #[DataProvider('invalidXmlProvider')]
    public function parseFailsForInvalidXml(string $xml): void
    {
        $parser = new SaxParser($xml);
        $this->assertFalse($parser->parse());
        $this->assertNotEmpty($parser->errors);
        @xml_parser_free($parser->parser);
    }

    // ---------------------------------------------------------------
    // Attribute capture test
    // ---------------------------------------------------------------

    /**
     * Verify that attributes are passed to the tag handler's handleBeginElement.
     */
    #[Test]
    public function attributesPassedToHandler(): void
    {
        $capturedAttrs = [];
        $handler = new class($capturedAttrs) extends XmlTagHandler {
            private $capturedAttrs;
            public function __construct(&$capturedAttrs)
            {
                parent::__construct();
                $this->capturedAttrs = &$capturedAttrs;
            }
            public function getName()
            {
                return 'item';
            }
            public function handleBeginElement($parser, &$attributes)
            {
                $this->capturedAttrs = $attributes;
            }
        };

        $xml = '<?xml version="1.0"?><root><item id="42" name="test">content</item></root>';
        $parser = new SaxParser($xml);
        $parser->addTagHandler($handler);
        $parser->parse();

        $this->assertArrayHasKey('id', $capturedAttrs);
        $this->assertSame('42', $capturedAttrs['id']);
        $this->assertArrayHasKey('name', $capturedAttrs);
        $this->assertSame('test', $capturedAttrs['name']);
        @xml_parser_free($parser->parser);
    }
}
