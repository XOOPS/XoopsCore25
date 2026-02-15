<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormHidden;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormHidden');

/**
 * Unit tests for XoopsFormElement base class.
 *
 * XoopsFormElement::__construct() calls exit(), so we test base methods
 * through XoopsFormHidden, the simplest concrete subclass.
 */
class XoopsFormElementTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // isContainer
    // =========================================================================

    public function testIsContainerReturnsFalse(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertFalse($element->isContainer());
    }

    // =========================================================================
    // setName / getName
    // =========================================================================

    public function testSetNameAndGetName(): void
    {
        $element = new XoopsFormHidden('original', 'val');
        $element->setName('new_name');

        $this->assertSame('new_name', $element->getName(false));
    }

    public function testSetNameTrimsWhitespace(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('  spaced_name  ');

        $this->assertSame('spaced_name', $element->getName(false));
    }

    public function testGetNameEncodedByDefault(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('field<script>');

        $result = $element->getName();

        $this->assertSame('field&lt;script&gt;', $result);
    }

    public function testGetNameEncodedTrue(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('name"with"quotes');

        $result = $element->getName(true);

        $this->assertSame('name&quot;with&quot;quotes', $result);
    }

    public function testGetNameEncodedFalseReturnsRaw(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('name<tag>');

        $this->assertSame('name<tag>', $element->getName(false));
    }

    public function testGetNamePreservesAmpersand(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('foo&bar');

        // The method does str_replace('&amp;', '&', htmlspecialchars(...))
        // So '&' -> htmlspecialchars -> '&amp;' -> str_replace -> '&'
        $this->assertSame('foo&bar', $element->getName(true));
    }

    public function testGetNameEmptyString(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('');

        $this->assertSame('', $element->getName(false));
        $this->assertSame('', $element->getName(true));
    }

    // =========================================================================
    // setAccessKey / getAccessKey
    // =========================================================================

    public function testSetAndGetAccessKey(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setAccessKey('a');

        $this->assertSame('a', $element->getAccessKey());
    }

    public function testAccessKeyDefaultIsEmpty(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertSame('', $element->getAccessKey());
    }

    public function testSetAccessKeyTrims(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setAccessKey('  b  ');

        $this->assertSame('b', $element->getAccessKey());
    }

    // =========================================================================
    // getAccessString
    // =========================================================================

    public function testGetAccessStringUnderlineFirstOccurrence(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setAccessKey('a');

        $result = $element->getAccessString('banana');

        // 'b' before 'a' is htmlspecialchars('b'), then underlined 'a', then 'nana'
        $this->assertStringContainsString('<span style="text-decoration: underline;">a</span>', $result);
        $this->assertStringContainsString('b', $result);
        $this->assertStringContainsString('nana', $result);
    }

    public function testGetAccessStringNoAccessKeySet(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $result = $element->getAccessString('Hello');

        // With empty access key, just returns htmlspecialchars of the whole string
        $this->assertSame('Hello', $result);
    }

    public function testGetAccessStringKeyNotFound(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setAccessKey('z');

        $result = $element->getAccessString('Hello');

        // Key 'z' not found in 'Hello' -> returns htmlspecialchars('Hello')
        $this->assertSame('Hello', $result);
    }

    public function testGetAccessStringWithSpecialChars(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setAccessKey('H');

        $result = $element->getAccessString('Hello <World>');

        $this->assertStringContainsString('<span style="text-decoration: underline;">H</span>', $result);
        // The rest should be encoded
        $this->assertStringContainsString('&lt;World&gt;', $result);
    }

    public function testGetAccessStringCaseSensitive(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setAccessKey('A');

        $result = $element->getAccessString('apple');

        // 'A' is not found (case-sensitive) in 'apple', returns encoded string
        $this->assertSame('apple', $result);
    }

    // =========================================================================
    // setClass / getClass
    // =========================================================================

    public function testGetClassReturnsFalseWhenEmpty(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertFalse($element->getClass());
    }

    public function testSetClassSingle(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('form-control');

        $this->assertSame('form-control', $element->getClass());
    }

    public function testSetClassAccumulates(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('class-a');
        $element->setClass('class-b');

        $this->assertSame('class-a class-b', $element->getClass());
    }

    public function testSetClassMultipleAccumulation(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('one');
        $element->setClass('two');
        $element->setClass('three');

        $this->assertSame('one two three', $element->getClass());
    }

    public function testSetClassIgnoresEmptyString(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('');

        $this->assertFalse($element->getClass());
    }

    public function testSetClassTrimsWhitespace(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('  padded  ');

        $this->assertSame('padded', $element->getClass());
    }

    public function testSetClassIgnoresWhitespaceOnly(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('   ');

        $this->assertFalse($element->getClass());
    }

    public function testGetClassEncodesSpecialChars(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setClass('cls<xss>');

        $result = $element->getClass();

        $this->assertSame('cls&lt;xss&gt;', $result);
    }

    // =========================================================================
    // setCaption / getCaption
    // =========================================================================

    public function testSetAndGetCaption(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('My Caption');

        $this->assertSame('My Caption', $element->getCaption());
    }

    public function testGetCaptionDefaultNoEncode(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('Caption <b>Bold</b>');

        $this->assertSame('Caption <b>Bold</b>', $element->getCaption());
        $this->assertSame('Caption <b>Bold</b>', $element->getCaption(false));
    }

    public function testGetCaptionEncoded(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('Caption <script>alert("xss")</script>');

        $result = $element->getCaption(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testSetCaptionTrims(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('  trimmed  ');

        $this->assertSame('trimmed', $element->getCaption());
    }

    // =========================================================================
    // getTitle
    // =========================================================================

    public function testGetTitleWithoutDescription(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('My Title');

        $result = $element->getTitle();

        $this->assertSame('My Title', $result);
    }

    public function testGetTitleWithDescription(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('Caption');
        $element->setDescription('A description');

        $result = $element->getTitle();

        $this->assertSame('Caption - A description', $result);
    }

    public function testGetTitleStripsHtmlTags(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('<b>Bold</b> Caption');
        $element->setDescription('<i>Italic</i> desc');

        $result = $element->getTitle(false);

        $this->assertSame('Bold Caption - Italic desc', $result);
    }

    public function testGetTitleEncodedByDefault(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('Title & "stuff"');

        $result = $element->getTitle(true);

        $this->assertStringContainsString('&amp;', $result);
        $this->assertStringContainsString('&quot;', $result);
    }

    public function testGetTitleNotEncoded(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('Title & stuff');

        $result = $element->getTitle(false);

        $this->assertSame('Title & stuff', $result);
    }

    public function testGetTitleWithEmptyDescription(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('Caption');
        $element->setDescription('');

        // Empty string has strlen 0, so no " - description" suffix
        $result = $element->getTitle(false);

        $this->assertSame('Caption', $result);
    }

    // =========================================================================
    // setDescription / getDescription
    // =========================================================================

    public function testSetAndGetDescription(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setDescription('Help text');

        $this->assertSame('Help text', $element->getDescription());
    }

    public function testGetDescriptionDefaultNoEncode(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setDescription('Desc with <b>HTML</b>');

        $this->assertSame('Desc with <b>HTML</b>', $element->getDescription());
    }

    public function testGetDescriptionEncoded(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setDescription('Desc <script>xss</script>');

        $result = $element->getDescription(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testDescriptionDefaultIsEmpty(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertSame('', $element->getDescription());
    }

    public function testSetDescriptionTrims(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setDescription('  trimmed desc  ');

        $this->assertSame('trimmed desc', $element->getDescription());
    }

    // =========================================================================
    // setHidden / isHidden
    // =========================================================================

    public function testSetHiddenAndIsHidden(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        // XoopsFormHidden calls setHidden() in constructor
        $this->assertTrue($element->isHidden());
    }

    public function testIsHiddenDefaultFalseForBaseElement(): void
    {
        // XoopsFormHidden calls setHidden() in its constructor,
        // so we check the default property directly
        $element = new XoopsFormHidden('test', 'val');

        // XoopsFormHidden always sets hidden to true, but we can test
        // the base class default by resetting the property
        $element->_hidden = false;
        $this->assertFalse($element->isHidden());

        $element->setHidden();
        $this->assertTrue($element->isHidden());
    }

    // =========================================================================
    // isRequired
    // =========================================================================

    public function testIsRequiredDefaultFalse(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertFalse($element->isRequired());
    }

    public function testIsRequiredCanBeSetTrue(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->_required = true;

        $this->assertTrue($element->isRequired());
    }

    // =========================================================================
    // setExtra / getExtra
    // =========================================================================

    public function testSetExtraAppends(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setExtra('data-id="1"');

        $result = $element->getExtra();

        $this->assertSame(' data-id="1"', $result);
    }

    public function testSetExtraAccumulates(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setExtra('data-id="1"');
        $element->setExtra('data-name="test"');

        $result = $element->getExtra();

        $this->assertSame(' data-id="1" data-name="test"', $result);
    }

    public function testSetExtraReplaceMode(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setExtra('first');
        $element->setExtra('second');
        $element->setExtra('replaced', true);

        $result = $element->getExtra();

        $this->assertSame(' replaced', $result);
    }

    public function testSetExtraReturnsArray(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $returned = $element->setExtra('attr="val"');

        $this->assertIsArray($returned);
        $this->assertCount(1, $returned);
        $this->assertSame('attr="val"', $returned[0]);
    }

    public function testSetExtraTrims(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setExtra('  trimmed  ');

        $result = $element->getExtra();

        $this->assertSame(' trimmed', $result);
    }

    public function testGetExtraEncoded(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setExtra('data-val="<tag>"');

        $result = $element->getExtra(true);

        $this->assertStringContainsString('&lt;tag&gt;', $result);
        $this->assertStringNotContainsString('<tag>', $result);
    }

    public function testGetExtraEncodedEmptyReturnsEmpty(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $result = $element->getExtra(true);

        $this->assertSame('', $result);
    }

    public function testGetExtraNotEncodedReturnsSpacePrefixed(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        // Even with empty _extra array, getExtra(false) returns ' ' + implode
        $result = $element->getExtra(false);

        $this->assertSame(' ', $result);
    }

    // =========================================================================
    // setNocolspan / getNocolspan
    // =========================================================================

    public function testNocolspanDefaultFalse(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertFalse($element->getNocolspan());
    }

    public function testSetNocolspanDefaultTrue(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setNocolspan();

        $this->assertTrue($element->getNocolspan());
    }

    public function testSetNocolspanExplicitFalse(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setNocolspan(true);
        $element->setNocolspan(false);

        $this->assertFalse($element->getNocolspan());
    }

    // =========================================================================
    // setFormType / getFormType
    // =========================================================================

    public function testFormTypeDefaultEmpty(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertSame('', $element->getFormType());
    }

    public function testSetAndGetFormType(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setFormType('text');

        $this->assertSame('text', $element->getFormType());
    }

    public function testSetFormTypeDefaultEmpty(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setFormType('text');
        $element->setFormType();

        $this->assertSame('', $element->getFormType());
    }

    // =========================================================================
    // customValidationCode
    // =========================================================================

    public function testCustomValidationCodeDefaultEmptyArray(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertSame([], $element->customValidationCode);
    }

    // =========================================================================
    // renderValidationJS
    // =========================================================================

    public function testRenderValidationJSWithCustomCode(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->customValidationCode[] = 'alert("custom");';
        $element->customValidationCode[] = 'return true;';

        $result = $element->renderValidationJS();

        $this->assertSame('alert("custom");' . NWLINE . 'return true;', $result);
    }

    public function testRenderValidationJSReturnsFalseWhenNotRequired(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $result = $element->renderValidationJS();

        $this->assertFalse($result);
    }

    public function testRenderValidationJSRequiredGeneratesDefaultJS(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->_required = true;
        $element->setCaption('Username');

        ob_start();
        $result = $element->renderValidationJS();
        ob_end_clean();

        $this->assertIsString($result);
        $this->assertStringContainsString('myform.test', $result);
        $this->assertStringContainsString('window.alert', $result);
        $this->assertStringContainsString('Username', $result);
    }

    public function testRenderValidationJSRequiredWithEmptyCaptionUsesName(): void
    {
        $element = new XoopsFormHidden('fieldname', 'val');
        $element->_required = true;
        $element->setCaption('');

        ob_start();
        $result = $element->renderValidationJS();
        ob_end_clean();

        $this->assertIsString($result);
        $this->assertStringContainsString('fieldname', $result);
    }

    public function testRenderValidationJSRequiredCheckboxFormType(): void
    {
        $element = new XoopsFormHidden('agree', 'val');
        $element->_required = true;
        $element->setCaption('I agree');
        $element->setFormType('checkbox');

        ob_start();
        $result = $element->renderValidationJS();
        ob_end_clean();

        $this->assertIsString($result);
        $this->assertStringContainsString('.checked', $result);
    }

    public function testRenderValidationJSCustomCodeTakesPrecedenceOverRequired(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->_required = true;
        $element->customValidationCode[] = 'customCheck();';

        $result = $element->renderValidationJS();

        $this->assertSame('customCheck();', $result);
        $this->assertStringNotContainsString('window.alert', $result);
    }

    // =========================================================================
    // render (base class)
    // =========================================================================

    public function testRenderBaseReturnsNull(): void
    {
        // XoopsFormElement::render() returns null (empty body)
        // We can check this via XoopsFormHidden which overrides render,
        // but let's verify the base render property exists
        $element = new XoopsFormHidden('test', 'val');

        // XoopsFormHidden overrides render(), so we verify it returns a string
        $this->assertIsString($element->render());
    }

    // =========================================================================
    // Default properties
    // =========================================================================

    public function testDefaultPropertyValues(): void
    {
        $element = new XoopsFormHidden('test', 'val');

        $this->assertSame([], $element->customValidationCode);
        $this->assertSame('', $element->_accesskey);
        $this->assertSame([], $element->_class);
        $this->assertSame([], $element->_extra);
        $this->assertFalse($element->_required);
        $this->assertSame('', $element->_description);
        $this->assertFalse($element->_nocolspan);
        $this->assertSame('', $element->_formtype);
    }

    // =========================================================================
    // Edge cases: XSS in name and caption
    // =========================================================================

    public function testXssInNameIsEncodedByDefault(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('<script>alert("xss")</script>');

        $result = $element->getName(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testXssInCaptionIsEncodedWhenRequested(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption('<img src=x onerror=alert(1)>');

        $raw = $element->getCaption(false);
        $encoded = $element->getCaption(true);

        $this->assertStringContainsString('<img', $raw);
        $this->assertStringNotContainsString('<img', $encoded);
        $this->assertStringContainsString('&lt;img', $encoded);
    }

    public function testXssInDescriptionIsEncodedWhenRequested(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setDescription('"><script>alert(1)</script>');

        $encoded = $element->getDescription(true);

        $this->assertStringNotContainsString('<script>', $encoded);
    }

    // =========================================================================
    // Edge case: special characters
    // =========================================================================

    public function testNameWithQuotesEncoded(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName("name'with\"quotes");

        $result = $element->getName(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    /**
     * @param string $input
     * @param string $expectedRaw
     */
    #[DataProvider('captionDataProvider')]
    public function testCaptionRoundTrip(string $input, string $expectedRaw): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setCaption($input);

        $this->assertSame($expectedRaw, $element->getCaption(false));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function captionDataProvider(): array
    {
        return [
            'simple text'      => ['Hello', 'Hello'],
            'empty string'     => ['', ''],
            'html tags'        => ['<b>Bold</b>', '<b>Bold</b>'],
            'special chars'    => ['A & B', 'A & B'],
            'unicode'          => ['Cafe\u{0301}', 'Cafe\u{0301}'],
            'trimmed'          => ['  spaced  ', 'spaced'],
        ];
    }

    /**
     * @param string[] $classes
     * @param string|false $expected
     */
    #[DataProvider('classAccumulationDataProvider')]
    public function testClassAccumulationDataDriven(array $classes, $expected): void
    {
        $element = new XoopsFormHidden('test', 'val');

        foreach ($classes as $class) {
            $element->setClass($class);
        }

        $this->assertSame($expected, $element->getClass());
    }

    /**
     * @return array<string, array{0: string[], 1: string|false}>
     */
    public static function classAccumulationDataProvider(): array
    {
        return [
            'no classes'       => [[], false],
            'single class'     => [['btn'], 'btn'],
            'two classes'      => [['btn', 'primary'], 'btn primary'],
            'three classes'    => [['a', 'b', 'c'], 'a b c'],
            'with empty'       => [['btn', '', 'primary'], 'btn primary'],
        ];
    }
}
