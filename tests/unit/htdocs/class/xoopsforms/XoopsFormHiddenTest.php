<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormHidden;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormHidden');

/**
 * Unit tests for XoopsFormHidden.
 */
class XoopsFormHiddenTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $this->assertInstanceOf(XoopsFormHidden::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $this->assertSame('myfield', $element->getName(false));
    }

    public function testConstructorSetsValue(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $this->assertSame('myvalue', $element->getValue());
    }

    public function testConstructorSetsHidden(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $this->assertTrue($element->isHidden());
    }

    public function testConstructorSetsCaptionEmpty(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $this->assertSame('', $element->getCaption());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $element = new XoopsFormHidden('test', 'Hello World');

        $this->assertSame('Hello World', $element->getValue());
        $this->assertSame('Hello World', $element->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $element = new XoopsFormHidden('test', '<script>alert("xss")</script>');

        $result = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testGetValueEncodedQuotes(): void
    {
        $element = new XoopsFormHidden('test', 'value"with\'quotes');

        $result = $element->getValue(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    public function testGetValueEncodedAmpersand(): void
    {
        $element = new XoopsFormHidden('test', 'foo&bar');

        $result = $element->getValue(true);

        $this->assertSame('foo&amp;bar', $result);
    }

    public function testGetValueEmptyString(): void
    {
        $element = new XoopsFormHidden('test', '');

        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getValue(true));
    }

    // =========================================================================
    // setValue
    // =========================================================================

    public function testSetValue(): void
    {
        $element = new XoopsFormHidden('test', 'initial');
        $element->setValue('updated');

        $this->assertSame('updated', $element->getValue());
    }

    public function testSetValueOverwrites(): void
    {
        $element = new XoopsFormHidden('test', 'first');
        $element->setValue('second');
        $element->setValue('third');

        $this->assertSame('third', $element->getValue());
    }

    public function testSetValueWithSpecialChars(): void
    {
        $element = new XoopsFormHidden('test', '');
        $element->setValue('<b>"value"</b>');

        $this->assertSame('<b>"value"</b>', $element->getValue(false));
    }

    public function testSetValueNumeric(): void
    {
        $element = new XoopsFormHidden('test', '');
        $element->setValue('42');

        $this->assertSame('42', $element->getValue());
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderContainsInputTag(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $this->assertStringContainsString('<input', $result);
        $this->assertStringContainsString('type="hidden"', $result);
    }

    public function testRenderContainsNameAttribute(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $this->assertStringContainsString('name="myfield"', $result);
    }

    public function testRenderContainsIdAttribute(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $this->assertStringContainsString('id="myfield"', $result);
    }

    public function testRenderContainsValueAttribute(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $this->assertStringContainsString('value="myvalue"', $result);
    }

    public function testRenderSelfClosingTag(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $this->assertStringContainsString('/>', $result);
    }

    public function testRenderFullOutput(): void
    {
        $element = new XoopsFormHidden('myfield', 'myvalue');

        $result = $element->render();

        $expected = '<input type="hidden" name="myfield" id="myfield" value="myvalue" />';
        $this->assertSame($expected, $result);
    }

    /**
     * render() uses getValue() without encoding (not getValue(true)),
     * and getName() with default encoding.
     * This means XSS in value is NOT escaped in render output.
     */
    public function testRenderDoesNotEncodeValue(): void
    {
        $element = new XoopsFormHidden('test', '<script>alert(1)</script>');

        $result = $element->render();

        // The value is inserted raw via getValue() (no encode)
        $this->assertStringContainsString('value="<script>alert(1)</script>"', $result);
    }

    public function testRenderEncodesName(): void
    {
        $element = new XoopsFormHidden('test', 'val');
        $element->setName('field<name>');

        $result = $element->render();

        // getName() defaults to encode=true
        $this->assertStringContainsString('name="field&lt;name&gt;"', $result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testEmptyNameAndValue(): void
    {
        $element = new XoopsFormHidden('', '');

        $this->assertSame('', $element->getName(false));
        $this->assertSame('', $element->getValue(false));
    }

    public function testNumericValue(): void
    {
        $element = new XoopsFormHidden('id', '123');

        $result = $element->render();

        $this->assertStringContainsString('value="123"', $result);
    }

    public function testValueWithHtmlEntities(): void
    {
        $element = new XoopsFormHidden('test', '&amp;already&amp;encoded');

        $raw = $element->getValue(false);
        $encoded = $element->getValue(true);

        $this->assertSame('&amp;already&amp;encoded', $raw);
        $this->assertSame('&amp;amp;already&amp;amp;encoded', $encoded);
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $expectedName
     * @param string $expectedValue
     */
    #[DataProvider('hiddenFieldDataProvider')]
    public function testRenderVariousInputs(
        string $name,
        string $value,
        string $expectedName,
        string $expectedValue
    ): void {
        $element = new XoopsFormHidden($name, $value);
        $result = $element->render();

        $this->assertStringContainsString('name="' . $expectedName . '"', $result);
        $this->assertStringContainsString('value="' . $expectedValue . '"', $result);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function hiddenFieldDataProvider(): array
    {
        return [
            'simple'           => ['field', 'value', 'field', 'value'],
            'numeric name'     => ['field1', '100', 'field1', '100'],
            'underscore name'  => ['my_field', 'data', 'my_field', 'data'],
            'empty value'      => ['field', '', 'field', ''],
        ];
    }
}
