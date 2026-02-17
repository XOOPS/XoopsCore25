<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormText;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormText');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormText.
 */
class XoopsFormTextTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormText('Label', 'username', 30, 255);

        $this->assertInstanceOf(XoopsFormText::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormText('My Caption', 'field', 20, 100);

        $this->assertSame('My Caption', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormText('Label', 'myfield', 20, 100);

        $this->assertSame('myfield', $element->getName(false));
    }

    public function testConstructorSetsSize(): void
    {
        $element = new XoopsFormText('Label', 'field', 30, 100);

        $this->assertSame(30, $element->getSize());
    }

    public function testConstructorSetsMaxlength(): void
    {
        $element = new XoopsFormText('Label', 'field', 30, 255);

        $this->assertSame(255, $element->getMaxlength());
    }

    public function testConstructorSetsDefaultValue(): void
    {
        $element = new XoopsFormText('Label', 'field', 30, 255);

        $this->assertSame('', $element->getValue());
    }

    public function testConstructorSetsValueParam(): void
    {
        $element = new XoopsFormText('Label', 'field', 30, 255, 'initial value');

        $this->assertSame('initial value', $element->getValue());
    }

    // =========================================================================
    // Size and maxlength cast to int
    // =========================================================================

    public function testSizeCastToInt(): void
    {
        $element = new XoopsFormText('Label', 'field', '25', 100);

        $this->assertSame(25, $element->getSize());
        $this->assertIsInt($element->getSize());
    }

    public function testMaxlengthCastToInt(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, '200');

        $this->assertSame(200, $element->getMaxlength());
        $this->assertIsInt($element->getMaxlength());
    }

    public function testSizeZero(): void
    {
        $element = new XoopsFormText('Label', 'field', 0, 100);

        $this->assertSame(0, $element->getSize());
    }

    public function testMaxlengthZero(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 0);

        $this->assertSame(0, $element->getMaxlength());
    }

    // =========================================================================
    // getSize / getMaxlength
    // =========================================================================

    public function testGetSize(): void
    {
        $element = new XoopsFormText('Label', 'field', 50, 255);

        $this->assertSame(50, $element->getSize());
    }

    public function testGetMaxlength(): void
    {
        $element = new XoopsFormText('Label', 'field', 50, 128);

        $this->assertSame(128, $element->getMaxlength());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, 'Hello World');

        $this->assertSame('Hello World', $element->getValue());
        $this->assertSame('Hello World', $element->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, '<script>alert("xss")</script>');

        $result = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testGetValueEncodedQuotes(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, 'value"with\'quotes');

        $result = $element->getValue(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    public function testGetValueEncodedAmpersand(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, 'foo&bar');

        $this->assertSame('foo&amp;bar', $element->getValue(true));
    }

    public function testGetValueEmpty(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100);

        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getValue(true));
    }

    // =========================================================================
    // setValue
    // =========================================================================

    public function testSetValue(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100);
        $element->setValue('new value');

        $this->assertSame('new value', $element->getValue());
    }

    public function testSetValueOverwrites(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, 'original');
        $element->setValue('updated');

        $this->assertSame('updated', $element->getValue());
    }

    public function testSetValueWithSpecialChars(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100);
        $element->setValue('<b>"bold"</b>');

        $this->assertSame('<b>"bold"</b>', $element->getValue(false));
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, 'value');

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderNotEmpty(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100, 'value');

        $result = $element->render();

        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testLargeSize(): void
    {
        $element = new XoopsFormText('Label', 'field', 999, 9999);

        $this->assertSame(999, $element->getSize());
        $this->assertSame(9999, $element->getMaxlength());
    }

    public function testNegativeSizeCastsToInt(): void
    {
        $element = new XoopsFormText('Label', 'field', -5, -10);

        $this->assertSame(-5, $element->getSize());
        $this->assertSame(-10, $element->getMaxlength());
    }

    #[DataProvider('textFieldDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $name,
        int $size,
        int $maxlength,
        string $value
    ): void {
        $element = new XoopsFormText($caption, $name, $size, $maxlength, $value);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($name, $element->getName(false));
        $this->assertSame($size, $element->getSize());
        $this->assertSame($maxlength, $element->getMaxlength());
        $this->assertSame($value, $element->getValue());
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: int, 3: int, 4: string}>
     */
    public static function textFieldDataProvider(): array
    {
        return [
            'username field'   => ['Username', 'username', 30, 255, ''],
            'email field'      => ['Email', 'email', 40, 255, 'user@example.com'],
            'search field'     => ['Search', 'q', 50, 100, 'query'],
            'small field'      => ['Code', 'code', 5, 10, 'ABC'],
            'with value'       => ['Name', 'fullname', 60, 200, 'John Doe'],
        ];
    }

    public function testIsNotHiddenByDefault(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100);

        $this->assertFalse($element->isHidden());
    }

    public function testIsNotRequiredByDefault(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100);

        $this->assertFalse($element->isRequired());
    }

    public function testIsNotContainer(): void
    {
        $element = new XoopsFormText('Label', 'field', 20, 100);

        $this->assertFalse($element->isContainer());
    }
}
