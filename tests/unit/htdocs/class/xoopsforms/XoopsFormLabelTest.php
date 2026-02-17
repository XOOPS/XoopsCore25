<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormLabel;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormLabel');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormLabel.
 */
class XoopsFormLabelTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormLabel();

        $this->assertInstanceOf(XoopsFormLabel::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorAllDefaults(): void
    {
        $element = new XoopsFormLabel();

        $this->assertSame('', $element->getCaption());
        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getName(false));
    }

    public function testConstructorWithCaption(): void
    {
        $element = new XoopsFormLabel('My Label');

        $this->assertSame('My Label', $element->getCaption());
        $this->assertSame('', $element->getValue());
    }

    public function testConstructorWithCaptionAndValue(): void
    {
        $element = new XoopsFormLabel('Label', 'Content here');

        $this->assertSame('Label', $element->getCaption());
        $this->assertSame('Content here', $element->getValue());
    }

    public function testConstructorWithAllParams(): void
    {
        $element = new XoopsFormLabel('Caption', 'Value', 'myname');

        $this->assertSame('Caption', $element->getCaption());
        $this->assertSame('Value', $element->getValue());
        $this->assertSame('myname', $element->getName(false));
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $element = new XoopsFormLabel('Label', 'Hello World');

        $this->assertSame('Hello World', $element->getValue());
        $this->assertSame('Hello World', $element->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $element = new XoopsFormLabel('Label', '<script>alert("xss")</script>');

        $result = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testGetValueEncodedQuotes(): void
    {
        $element = new XoopsFormLabel('Label', 'text"with\'quotes');

        $result = $element->getValue(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    public function testGetValueEncodedAmpersand(): void
    {
        $element = new XoopsFormLabel('Label', 'foo&bar');

        $this->assertSame('foo&amp;bar', $element->getValue(true));
    }

    public function testGetValueEmpty(): void
    {
        $element = new XoopsFormLabel();

        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getValue(true));
    }

    public function testGetValueWithHtml(): void
    {
        $html = '<p>Some <strong>formatted</strong> text</p>';
        $element = new XoopsFormLabel('Label', $html);

        // Raw should contain HTML
        $this->assertSame($html, $element->getValue(false));

        // Encoded should escape HTML
        $encoded = $element->getValue(true);
        $this->assertStringNotContainsString('<p>', $encoded);
        $this->assertStringContainsString('&lt;p&gt;', $encoded);
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormLabel('Label', 'Content');

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderNotEmpty(): void
    {
        $element = new XoopsFormLabel('Label', 'Content');

        $result = $element->render();

        $this->assertNotEmpty($result);
    }

    public function testRenderEmptyLabel(): void
    {
        $element = new XoopsFormLabel();

        $result = $element->render();

        $this->assertIsString($result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testIsNotHiddenByDefault(): void
    {
        $element = new XoopsFormLabel('Label', 'Value');

        $this->assertFalse($element->isHidden());
    }

    public function testIsNotRequiredByDefault(): void
    {
        $element = new XoopsFormLabel('Label', 'Value');

        $this->assertFalse($element->isRequired());
    }

    public function testIsNotContainer(): void
    {
        $element = new XoopsFormLabel();

        $this->assertFalse($element->isContainer());
    }

    #[DataProvider('labelDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $value,
        string $name
    ): void {
        $element = new XoopsFormLabel($caption, $value, $name);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($value, $element->getValue());
        $this->assertSame($name, $element->getName(false));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function labelDataProvider(): array
    {
        return [
            'all empty'       => ['', '', ''],
            'caption only'    => ['Caption', '', ''],
            'value only'      => ['', 'Some value', ''],
            'name only'       => ['', '', 'myname'],
            'all set'         => ['Label', 'Content', 'field'],
            'html value'      => ['Info', '<em>Important</em>', 'info'],
        ];
    }

    public function testValueWithMultipleLines(): void
    {
        $multiline = "Line 1\nLine 2\nLine 3";
        $element = new XoopsFormLabel('Label', $multiline);

        $this->assertSame($multiline, $element->getValue());
    }

    public function testValueWithSpecialChars(): void
    {
        $element = new XoopsFormLabel('Label', 'Price: $100 (50% off)');

        $this->assertSame('Price: $100 (50% off)', $element->getValue());
    }

    public function testCaptionTrimmed(): void
    {
        $element = new XoopsFormLabel('  Trimmed  ', 'val');

        $this->assertSame('Trimmed', $element->getCaption());
    }

    public function testNameTrimmed(): void
    {
        // setName() trims the name
        $element = new XoopsFormLabel('Cap', 'Val', '  trimmed_name  ');

        $this->assertSame('trimmed_name', $element->getName(false));
    }
}
