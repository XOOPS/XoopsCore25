<?php
/**
 * Comprehensive test suite for XoopsThemeSetParser and its associated
 * ThemeSet*Handler tag handler classes.
 *
 * XoopsThemeSetParser extends SaxParser and adds domain-specific methods
 * for reading XOOPS theme set XML manifests. It registers 13 tag handlers
 * to extract theme metadata, template info, and image data.
 *
 * NOTE: ThemeSetAuthorHandler::handleEndElement() calls $parser->setCreditsData()
 * which does NOT exist on XoopsThemeSetParser. Tests avoid triggering that path.
 *
 * @see \XoopsThemeSetParser
 */

declare(strict_types=1);

/*
 * ThemeSetThemeNameHandler is referenced in the XoopsThemeSetParser constructor
 * but not defined in the source file (themesetparser.php). Define a stub here
 * in the global namespace BEFORE loading the source file, to prevent a fatal
 * error during instantiation.
 */
namespace {
    if (!class_exists('ThemeSetThemeNameHandler', false)) {
        class ThemeSetThemeNameHandler extends XmlTagHandler
        {
            public function __construct() {}

            /**
             * @return string
             */
            public function getName()
            {
                return 'themeset';
            }
        }
    }

    // Load the themesetparser source file which defines XoopsThemeSetParser
    // and all the ThemeSet*Handler classes (except ThemeSetThemeNameHandler).
    require_once XOOPS_ROOT_PATH . '/class/xml/themesetparser.php';
}

namespace xoopsxml {

    use PHPUnit\Framework\Attributes\CoversClass;
    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;
    use SaxParser;
    use XmlTagHandler;
    use XoopsThemeSetParser;
    use ThemeSetDateCreatedHandler;
    use ThemeSetAuthorHandler;
    use ThemeSetDescriptionHandler;
    use ThemeSetGeneratorHandler;
    use ThemeSetNameHandler;
    use ThemeSetEmailHandler;
    use ThemeSetLinkHandler;
    use ThemeSetTemplateHandler;
    use ThemeSetImageHandler;
    use ThemeSetModuleHandler;
    use ThemeSetFileTypeHandler;
    use ThemeSetTagHandler;
    use ThemeSetThemeNameHandler;

    #[CoversClass(XoopsThemeSetParser::class)]
    class XoopsThemeSetParserTest extends TestCase
    {
        /**
         * @var XoopsThemeSetParser
         */
        private XoopsThemeSetParser $parser;

        protected function setUp(): void
        {
            $this->parser = new XoopsThemeSetParser('<?xml version="1.0"?><themeset/>');
        }

        protected function tearDown(): void
        {
            @xml_parser_free($this->parser->parser);
        }

        // ---------------------------------------------------------------
        // Constructor tests
        // ---------------------------------------------------------------

        /**
         * Verify XoopsThemeSetParser can be instantiated.
         */
        #[Test]
        public function canBeInstantiated(): void
        {
            $parser = new XoopsThemeSetParser('<?xml version="1.0"?><themeset/>');
            $this->assertInstanceOf(XoopsThemeSetParser::class, $parser);
            @xml_parser_free($parser->parser);
        }

        /**
         * Verify XoopsThemeSetParser extends SaxParser.
         */
        #[Test]
        public function extendsSaxParser(): void
        {
            $this->assertInstanceOf(SaxParser::class, $this->parser);
        }

        /**
         * Verify the constructor registers tag handlers.
         */
        #[Test]
        public function constructorRegistersTagHandlers(): void
        {
            $this->assertNotEmpty($this->parser->tagHandlers);
        }

        /**
         * Verify the constructor initializes empty themeSetData.
         */
        #[Test]
        public function constructorInitializesEmptyThemeSetData(): void
        {
            $this->assertIsArray($this->parser->themeSetData);
            $this->assertEmpty($this->parser->themeSetData);
        }

        /**
         * Verify the constructor initializes empty imagesData.
         */
        #[Test]
        public function constructorInitializesEmptyImagesData(): void
        {
            $this->assertIsArray($this->parser->imagesData);
            $this->assertEmpty($this->parser->imagesData);
        }

        /**
         * Verify the constructor initializes empty templatesData.
         */
        #[Test]
        public function constructorInitializesEmptyTemplatesData(): void
        {
            $this->assertIsArray($this->parser->templatesData);
            $this->assertEmpty($this->parser->templatesData);
        }

        /**
         * Verify the constructor initializes empty tempArr.
         */
        #[Test]
        public function constructorInitializesEmptyTempArr(): void
        {
            $this->assertIsArray($this->parser->tempArr);
            $this->assertEmpty($this->parser->tempArr);
        }

        // ---------------------------------------------------------------
        // setThemeSetData() / getThemeSetData() tests
        // ---------------------------------------------------------------

        /**
         * Verify setThemeSetData() stores data by name.
         */
        #[Test]
        public function setThemeSetDataStoresByName(): void
        {
            $value = 'My Theme';
            $this->parser->setThemeSetData('name', $value);
            $this->assertSame('My Theme', $this->parser->getThemeSetData('name'));
        }

        /**
         * Verify getThemeSetData() with null returns entire array.
         */
        #[Test]
        public function getThemeSetDataWithNullReturnsAll(): void
        {
            $value1 = 'My Theme';
            $value2 = '2026-01-01';
            $this->parser->setThemeSetData('name', $value1);
            $this->parser->setThemeSetData('date', $value2);
            $result = $this->parser->getThemeSetData();
            $this->assertIsArray($result);
            $this->assertArrayHasKey('name', $result);
            $this->assertArrayHasKey('date', $result);
        }

        /**
         * Verify getThemeSetData() with no args returns entire array.
         */
        #[Test]
        public function getThemeSetDataWithNoArgsReturnsAll(): void
        {
            $result = $this->parser->getThemeSetData();
            $this->assertIsArray($result);
        }

        /**
         * Verify getThemeSetData() returns false for non-existent key.
         */
        #[Test]
        public function getThemeSetDataReturnsFalseForMissingName(): void
        {
            $this->assertFalse($this->parser->getThemeSetData('nonexistent'));
        }

        /**
         * Verify getThemeSetData() with empty array and specific name returns false.
         */
        #[Test]
        public function getThemeSetDataReturnsFalseWhenEmpty(): void
        {
            $this->assertFalse($this->parser->getThemeSetData('anything'));
        }

        /**
         * Verify setThemeSetData() can overwrite existing value.
         */
        #[Test]
        public function setThemeSetDataOverwritesExisting(): void
        {
            $value1 = 'First';
            $value2 = 'Second';
            $this->parser->setThemeSetData('name', $value1);
            $this->parser->setThemeSetData('name', $value2);
            $this->assertSame('Second', $this->parser->getThemeSetData('name'));
        }

        // ---------------------------------------------------------------
        // setImagesData() / getImagesData() tests
        // ---------------------------------------------------------------

        /**
         * Verify setImagesData() adds an image array.
         */
        #[Test]
        public function setImagesDataAddsImageArray(): void
        {
            $img = ['name' => 'logo.png', 'module' => 'system'];
            $this->parser->setImagesData($img);
            $images = &$this->parser->getImagesData();
            $this->assertCount(1, $images);
            $this->assertSame('logo.png', $images[0]['name']);
        }

        /**
         * Verify setImagesData() accumulates multiple entries.
         */
        #[Test]
        public function setImagesDataAccumulatesEntries(): void
        {
            $img1 = ['name' => 'logo.png'];
            $img2 = ['name' => 'banner.jpg'];
            $this->parser->setImagesData($img1);
            $this->parser->setImagesData($img2);
            $images = &$this->parser->getImagesData();
            $this->assertCount(2, $images);
        }

        /**
         * Verify getImagesData() returns empty array initially.
         */
        #[Test]
        public function getImagesDataReturnsEmptyArrayInitially(): void
        {
            $images = &$this->parser->getImagesData();
            $this->assertIsArray($images);
            $this->assertEmpty($images);
        }

        // ---------------------------------------------------------------
        // setTemplatesData() / getTemplatesData() tests
        // ---------------------------------------------------------------

        /**
         * Verify setTemplatesData() adds a template array.
         */
        #[Test]
        public function setTemplatesDataAddsTemplateArray(): void
        {
            $tpl = ['name' => 'index.tpl', 'module' => 'system'];
            $this->parser->setTemplatesData($tpl);
            $templates = &$this->parser->getTemplatesData();
            $this->assertCount(1, $templates);
            $this->assertSame('index.tpl', $templates[0]['name']);
        }

        /**
         * Verify setTemplatesData() accumulates multiple entries.
         */
        #[Test]
        public function setTemplatesDataAccumulatesEntries(): void
        {
            $tpl1 = ['name' => 'index.tpl'];
            $tpl2 = ['name' => 'header.tpl'];
            $tpl3 = ['name' => 'footer.tpl'];
            $this->parser->setTemplatesData($tpl1);
            $this->parser->setTemplatesData($tpl2);
            $this->parser->setTemplatesData($tpl3);
            $templates = &$this->parser->getTemplatesData();
            $this->assertCount(3, $templates);
        }

        /**
         * Verify getTemplatesData() returns empty array initially.
         */
        #[Test]
        public function getTemplatesDataReturnsEmptyArrayInitially(): void
        {
            $templates = &$this->parser->getTemplatesData();
            $this->assertIsArray($templates);
            $this->assertEmpty($templates);
        }

        // ---------------------------------------------------------------
        // setTempArr() / getTempArr() / resetTempArr() tests
        // ---------------------------------------------------------------

        /**
         * Verify setTempArr() sets an initial value.
         */
        #[Test]
        public function setTempArrSetsInitialValue(): void
        {
            $value = 'test_value';
            $this->parser->setTempArr('key', $value);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('test_value', $tempArr['key']);
        }

        /**
         * Verify setTempArr() appends with delimiter when key exists.
         */
        #[Test]
        public function setTempArrAppendsWithDelimiter(): void
        {
            $value1 = 'first';
            $value2 = 'second';
            $this->parser->setTempArr('key', $value1);
            $this->parser->setTempArr('key', $value2, ', ');
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('first, second', $tempArr['key']);
        }

        /**
         * Verify setTempArr() appends without delimiter (empty string).
         */
        #[Test]
        public function setTempArrAppendsWithoutDelimiter(): void
        {
            $value1 = 'Hello';
            $value2 = 'World';
            $this->parser->setTempArr('key', $value1);
            $this->parser->setTempArr('key', $value2);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('HelloWorld', $tempArr['key']);
        }

        /**
         * Verify setTempArr() can store multiple different keys.
         */
        #[Test]
        public function setTempArrStoresMultipleKeys(): void
        {
            $val1 = 'alpha';
            $val2 = 'beta';
            $this->parser->setTempArr('a', $val1);
            $this->parser->setTempArr('b', $val2);
            $tempArr = $this->parser->getTempArr();
            $this->assertCount(2, $tempArr);
            $this->assertSame('alpha', $tempArr['a']);
            $this->assertSame('beta', $tempArr['b']);
        }

        /**
         * Verify getTempArr() returns empty array initially.
         */
        #[Test]
        public function getTempArrReturnsEmptyArrayInitially(): void
        {
            $this->assertIsArray($this->parser->getTempArr());
            $this->assertEmpty($this->parser->getTempArr());
        }

        /**
         * Verify resetTempArr() clears all temp data.
         */
        #[Test]
        public function resetTempArrClearsArray(): void
        {
            $value = 'data';
            $this->parser->setTempArr('key', $value);
            $this->assertNotEmpty($this->parser->getTempArr());

            $this->parser->resetTempArr();
            $this->assertEmpty($this->parser->getTempArr());
        }

        /**
         * Verify resetTempArr() allows re-population after clearing.
         */
        #[Test]
        public function resetTempArrAllowsRePopulation(): void
        {
            $value1 = 'old';
            $this->parser->setTempArr('key', $value1);
            $this->parser->resetTempArr();

            $value2 = 'new';
            $this->parser->setTempArr('key', $value2);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('new', $tempArr['key']);
        }

        // ---------------------------------------------------------------
        // Tag handler getName() verification tests
        // ---------------------------------------------------------------

        /**
         * Data provider for tag handler class names and their expected
         * getName() return values.
         *
         * @return array<string, array{string, string}>
         */
        public static function tagHandlerNamesProvider(): array
        {
            return [
                'ThemeSetDateCreatedHandler'  => ['ThemeSetDateCreatedHandler', 'dateCreated'],
                'ThemeSetAuthorHandler'       => ['ThemeSetAuthorHandler', 'author'],
                'ThemeSetDescriptionHandler'  => ['ThemeSetDescriptionHandler', 'description'],
                'ThemeSetGeneratorHandler'    => ['ThemeSetGeneratorHandler', 'generator'],
                'ThemeSetNameHandler'         => ['ThemeSetNameHandler', 'name'],
                'ThemeSetEmailHandler'        => ['ThemeSetEmailHandler', 'email'],
                'ThemeSetLinkHandler'         => ['ThemeSetLinkHandler', 'link'],
                'ThemeSetTemplateHandler'     => ['ThemeSetTemplateHandler', 'template'],
                'ThemeSetImageHandler'        => ['ThemeSetImageHandler', 'image'],
                'ThemeSetModuleHandler'       => ['ThemeSetModuleHandler', 'module'],
                'ThemeSetFileTypeHandler'     => ['ThemeSetFileTypeHandler', 'fileType'],
                'ThemeSetTagHandler'          => ['ThemeSetTagHandler', 'tag'],
            ];
        }

        /**
         * Verify each tag handler class has the correct getName() return value.
         */
        #[Test]
        #[DataProvider('tagHandlerNamesProvider')]
        public function tagHandlerGetNameReturnsExpected(string $className, string $expectedName): void
        {
            $fqcn = '\\' . $className;
            $handler = new $fqcn();
            $this->assertSame($expectedName, $handler->getName());
        }

        // ---------------------------------------------------------------
        // Tag handler class hierarchy tests
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetDateCreatedHandler extends XmlTagHandler.
         */
        #[Test]
        public function dateCreatedHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetDateCreatedHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetAuthorHandler extends XmlTagHandler.
         */
        #[Test]
        public function authorHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetAuthorHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetDescriptionHandler extends XmlTagHandler.
         */
        #[Test]
        public function descriptionHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetDescriptionHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetGeneratorHandler extends XmlTagHandler.
         */
        #[Test]
        public function generatorHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetGeneratorHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetNameHandler extends XmlTagHandler.
         */
        #[Test]
        public function nameHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetNameHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetEmailHandler extends XmlTagHandler.
         */
        #[Test]
        public function emailHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetEmailHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetLinkHandler extends XmlTagHandler.
         */
        #[Test]
        public function linkHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetLinkHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetTemplateHandler extends XmlTagHandler.
         */
        #[Test]
        public function templateHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetTemplateHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetImageHandler extends XmlTagHandler.
         */
        #[Test]
        public function imageHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetImageHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetModuleHandler extends XmlTagHandler.
         */
        #[Test]
        public function moduleHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetModuleHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetFileTypeHandler extends XmlTagHandler.
         */
        #[Test]
        public function fileTypeHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetFileTypeHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetTagHandler extends XmlTagHandler.
         */
        #[Test]
        public function tagHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetTagHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        /**
         * Verify ThemeSetThemeNameHandler extends XmlTagHandler.
         */
        #[Test]
        public function themeNameHandlerExtendsXmlTagHandler(): void
        {
            $handler = new ThemeSetThemeNameHandler();
            $this->assertInstanceOf(XmlTagHandler::class, $handler);
        }

        // ---------------------------------------------------------------
        // Registered tag handler verification
        // ---------------------------------------------------------------

        /**
         * Verify that the parser has handlers registered for all expected
         * tag names.
         */
        #[Test]
        public function parserHasAllExpectedHandlers(): void
        {
            $expectedTags = [
                'themeset', 'dateCreated', 'author', 'description',
                'generator', 'name', 'email', 'link',
                'template', 'image', 'module', 'fileType', 'tag',
            ];
            foreach ($expectedTags as $tag) {
                $this->assertArrayHasKey(
                    $tag,
                    $this->parser->tagHandlers,
                    "Expected tag handler for '{$tag}' to be registered"
                );
            }
        }

        /**
         * Verify the constructor registers exactly 13 tag handler entries.
         */
        #[Test]
        public function constructorRegistersThirteenHandlers(): void
        {
            $this->assertCount(13, $this->parser->tagHandlers);
        }

        // ---------------------------------------------------------------
        // ThemeSetDateCreatedHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetDateCreatedHandler sets 'date' in themeSetData
         * when parent tag is 'themeset'.
         */
        #[Test]
        public function dateCreatedHandlerSetsDateWhenParentIsThemeset(): void
        {
            $handler = new ThemeSetDateCreatedHandler();
            // Simulate parser state: parent tag is 'themeset'
            $this->parser->tags = ['themeset', 'dateCreated'];
            $data = '2026-01-15';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertSame('2026-01-15', $this->parser->getThemeSetData('date'));
        }

        /**
         * Verify ThemeSetDateCreatedHandler does nothing when parent
         * is not 'themeset'.
         */
        #[Test]
        public function dateCreatedHandlerDoesNothingForUnknownParent(): void
        {
            $handler = new ThemeSetDateCreatedHandler();
            $this->parser->tags = ['other', 'dateCreated'];
            $data = '2026-01-15';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertFalse($this->parser->getThemeSetData('date'));
        }

        // ---------------------------------------------------------------
        // ThemeSetGeneratorHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetGeneratorHandler sets 'generator' in themeSetData
         * when parent tag is 'themeset'.
         */
        #[Test]
        public function generatorHandlerSetsGeneratorWhenParentIsThemeset(): void
        {
            $handler = new ThemeSetGeneratorHandler();
            $this->parser->tags = ['themeset', 'generator'];
            $data = 'XOOPS Theme Generator';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertSame(
                'XOOPS Theme Generator',
                $this->parser->getThemeSetData('generator')
            );
        }

        // ---------------------------------------------------------------
        // ThemeSetNameHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetNameHandler sets 'name' in themeSetData
         * when parent tag is 'themeset'.
         */
        #[Test]
        public function nameHandlerSetsNameWhenParentIsThemeset(): void
        {
            $handler = new ThemeSetNameHandler();
            $this->parser->tags = ['themeset', 'name'];
            $data = 'My Custom Theme';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertSame(
                'My Custom Theme',
                $this->parser->getThemeSetData('name')
            );
        }

        /**
         * Verify ThemeSetNameHandler sets temp 'name' when parent is 'author'.
         */
        #[Test]
        public function nameHandlerSetsTempNameWhenParentIsAuthor(): void
        {
            $handler = new ThemeSetNameHandler();
            $this->parser->tags = ['themeset', 'author', 'name'];
            $data = 'John Doe';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('John Doe', $tempArr['name']);
        }

        // ---------------------------------------------------------------
        // ThemeSetEmailHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetEmailHandler sets temp 'email' when parent
         * is 'author'.
         */
        #[Test]
        public function emailHandlerSetsTempEmailWhenParentIsAuthor(): void
        {
            $handler = new ThemeSetEmailHandler();
            $this->parser->tags = ['themeset', 'author', 'email'];
            $data = 'test@example.com';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('test@example.com', $tempArr['email']);
        }

        /**
         * Verify ThemeSetEmailHandler does nothing for unknown parent.
         */
        #[Test]
        public function emailHandlerDoesNothingForUnknownParent(): void
        {
            $handler = new ThemeSetEmailHandler();
            $this->parser->tags = ['themeset', 'email'];
            $data = 'test@example.com';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertEmpty($this->parser->getTempArr());
        }

        // ---------------------------------------------------------------
        // ThemeSetLinkHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetLinkHandler sets temp 'link' when parent
         * is 'author'.
         */
        #[Test]
        public function linkHandlerSetsTempLinkWhenParentIsAuthor(): void
        {
            $handler = new ThemeSetLinkHandler();
            $this->parser->tags = ['themeset', 'author', 'link'];
            $data = 'https://example.com';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('https://example.com', $tempArr['link']);
        }

        // ---------------------------------------------------------------
        // ThemeSetDescriptionHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetDescriptionHandler sets temp 'description'
         * when parent is 'template'.
         */
        #[Test]
        public function descriptionHandlerSetsTempDescWhenParentIsTemplate(): void
        {
            $handler = new ThemeSetDescriptionHandler();
            $this->parser->tags = ['themeset', 'template', 'description'];
            $data = 'Template description text';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('Template description text', $tempArr['description']);
        }

        /**
         * Verify ThemeSetDescriptionHandler sets temp 'description'
         * when parent is 'image'.
         */
        #[Test]
        public function descriptionHandlerSetsTempDescWhenParentIsImage(): void
        {
            $handler = new ThemeSetDescriptionHandler();
            $this->parser->tags = ['themeset', 'image', 'description'];
            $data = 'Image description';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('Image description', $tempArr['description']);
        }

        // ---------------------------------------------------------------
        // ThemeSetModuleHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetModuleHandler sets temp 'module' when parent
         * is 'template'.
         */
        #[Test]
        public function moduleHandlerSetsTempModuleWhenParentIsTemplate(): void
        {
            $handler = new ThemeSetModuleHandler();
            $this->parser->tags = ['themeset', 'template', 'module'];
            $data = 'system';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('system', $tempArr['module']);
        }

        /**
         * Verify ThemeSetModuleHandler sets temp 'module' when parent
         * is 'image'.
         */
        #[Test]
        public function moduleHandlerSetsTempModuleWhenParentIsImage(): void
        {
            $handler = new ThemeSetModuleHandler();
            $this->parser->tags = ['themeset', 'image', 'module'];
            $data = 'publisher';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('publisher', $tempArr['module']);
        }

        // ---------------------------------------------------------------
        // ThemeSetFileTypeHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetFileTypeHandler sets temp 'type' when parent
         * is 'template'.
         */
        #[Test]
        public function fileTypeHandlerSetsTempTypeWhenParentIsTemplate(): void
        {
            $handler = new ThemeSetFileTypeHandler();
            $this->parser->tags = ['themeset', 'template', 'fileType'];
            $data = 'module';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('module', $tempArr['type']);
        }

        /**
         * Verify ThemeSetFileTypeHandler does nothing when parent is
         * not 'template'.
         */
        #[Test]
        public function fileTypeHandlerDoesNothingForUnknownParent(): void
        {
            $handler = new ThemeSetFileTypeHandler();
            $this->parser->tags = ['themeset', 'image', 'fileType'];
            $data = 'module';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertEmpty($this->parser->getTempArr());
        }

        // ---------------------------------------------------------------
        // ThemeSetTagHandler character data test
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetTagHandler sets temp 'tag' when parent is 'image'.
         */
        #[Test]
        public function tagHandlerSetsTempTagWhenParentIsImage(): void
        {
            $handler = new ThemeSetTagHandler();
            $this->parser->tags = ['themeset', 'image', 'tag'];
            $data = 'screenshot';
            $handler->handleCharacterData($this->parser, $data);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('screenshot', $tempArr['tag']);
        }

        /**
         * Verify ThemeSetTagHandler does nothing when parent is not 'image'.
         */
        #[Test]
        public function tagHandlerDoesNothingForUnknownParent(): void
        {
            $handler = new ThemeSetTagHandler();
            $this->parser->tags = ['themeset', 'template', 'tag'];
            $data = 'screenshot';
            $handler->handleCharacterData($this->parser, $data);
            $this->assertEmpty($this->parser->getTempArr());
        }

        // ---------------------------------------------------------------
        // ThemeSetAuthorHandler tests
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetAuthorHandler::handleBeginElement resets tempArr.
         */
        #[Test]
        public function authorHandlerBeginElementResetsTempArr(): void
        {
            $value = 'old data';
            $this->parser->setTempArr('key', $value);
            $this->assertNotEmpty($this->parser->getTempArr());

            $handler = new ThemeSetAuthorHandler();
            $attrs = [];
            $handler->handleBeginElement($this->parser, $attrs);
            $this->assertEmpty($this->parser->getTempArr());
        }

        // ---------------------------------------------------------------
        // ThemeSetTemplateHandler tests
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetTemplateHandler::handleBeginElement resets tempArr
         * and sets the template name from attributes.
         */
        #[Test]
        public function templateHandlerBeginElementResetsAndSetsName(): void
        {
            $handler = new ThemeSetTemplateHandler();
            $attrs = ['name' => 'system_header.tpl'];
            $handler->handleBeginElement($this->parser, $attrs);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('system_header.tpl', $tempArr['name']);
        }

        /**
         * Verify ThemeSetTemplateHandler::handleEndElement pushes tempArr
         * into templatesData.
         */
        #[Test]
        public function templateHandlerEndElementPushesToTemplatesData(): void
        {
            // Simulate a template begin and character data
            $handler = new ThemeSetTemplateHandler();
            $attrs = ['name' => 'index.tpl'];
            $handler->handleBeginElement($this->parser, $attrs);

            // Simulate adding description
            $desc = 'Main template';
            $this->parser->setTempArr('description', $desc);

            $handler->handleEndElement($this->parser);
            $templates = &$this->parser->getTemplatesData();
            $this->assertCount(1, $templates);
            $this->assertSame('index.tpl', $templates[0]['name']);
            $this->assertSame('Main template', $templates[0]['description']);
        }

        // ---------------------------------------------------------------
        // ThemeSetImageHandler tests
        // ---------------------------------------------------------------

        /**
         * Verify ThemeSetImageHandler::handleBeginElement resets tempArr
         * and sets the image name from first attribute.
         */
        #[Test]
        public function imageHandlerBeginElementResetsAndSetsName(): void
        {
            $handler = new ThemeSetImageHandler();
            $attrs = ['logo.png'];
            $handler->handleBeginElement($this->parser, $attrs);
            $tempArr = $this->parser->getTempArr();
            $this->assertSame('logo.png', $tempArr['name']);
        }

        /**
         * Verify ThemeSetImageHandler::handleEndElement pushes tempArr
         * into imagesData.
         */
        #[Test]
        public function imageHandlerEndElementPushesToImagesData(): void
        {
            $handler = new ThemeSetImageHandler();
            $attrs = ['banner.jpg'];
            $handler->handleBeginElement($this->parser, $attrs);

            $tag = 'header';
            $this->parser->setTempArr('tag', $tag);

            $handler->handleEndElement($this->parser);
            $images = &$this->parser->getImagesData();
            $this->assertCount(1, $images);
            $this->assertSame('banner.jpg', $images[0]['name']);
            $this->assertSame('header', $images[0]['tag']);
        }

        // ---------------------------------------------------------------
        // Full XML parsing integration test
        // ---------------------------------------------------------------

        /**
         * Verify parsing a simple themeset XML populates themeSetData
         * correctly. This test avoids the <author> end element to
         * sidestep the missing setCreditsData() method.
         */
        #[Test]
        public function parseSimpleThemesetXmlPopulatesData(): void
        {
            $xml = '<?xml version="1.0"?>'
                 . '<themeset>'
                 . '<name>Test Theme</name>'
                 . '<dateCreated>2026-02-12</dateCreated>'
                 . '<generator>XOOPS Test</generator>'
                 . '</themeset>';

            $parser = new XoopsThemeSetParser($xml);
            $result = $parser->parse();
            $this->assertTrue($result, 'XML parsed successfully');

            $this->assertSame('Test Theme', $parser->getThemeSetData('name'));
            $this->assertSame('2026-02-12', $parser->getThemeSetData('date'));
            $this->assertSame('XOOPS Test', $parser->getThemeSetData('generator'));

            @xml_parser_free($parser->parser);
        }

        /**
         * Verify parsing XML with template element populates templatesData.
         */
        #[Test]
        public function parseXmlWithTemplatePopulatesTemplatesData(): void
        {
            $xml = '<?xml version="1.0"?>'
                 . '<themeset>'
                 . '<template name="system_header.tpl">'
                 . '<module>system</module>'
                 . '<fileType>module</fileType>'
                 . '<description>Header template</description>'
                 . '</template>'
                 . '</themeset>';

            $parser = new XoopsThemeSetParser($xml);
            $result = $parser->parse();
            $this->assertTrue($result, 'XML parsed successfully');

            $templates = &$parser->getTemplatesData();
            $this->assertCount(1, $templates);
            $this->assertSame('system_header.tpl', $templates[0]['name']);
            $this->assertSame('system', $templates[0]['module']);
            $this->assertSame('module', $templates[0]['type']);
            $this->assertSame('Header template', $templates[0]['description']);

            @xml_parser_free($parser->parser);
        }

        /**
         * Verify parsing XML with multiple templates accumulates all
         * template data.
         */
        #[Test]
        public function parseXmlWithMultipleTemplates(): void
        {
            $xml = '<?xml version="1.0"?>'
                 . '<themeset>'
                 . '<template name="header.tpl">'
                 . '<module>system</module>'
                 . '</template>'
                 . '<template name="footer.tpl">'
                 . '<module>system</module>'
                 . '</template>'
                 . '</themeset>';

            $parser = new XoopsThemeSetParser($xml);
            $parser->parse();

            $templates = &$parser->getTemplatesData();
            $this->assertCount(2, $templates);
            $this->assertSame('header.tpl', $templates[0]['name']);
            $this->assertSame('footer.tpl', $templates[1]['name']);

            @xml_parser_free($parser->parser);
        }
    }
}
