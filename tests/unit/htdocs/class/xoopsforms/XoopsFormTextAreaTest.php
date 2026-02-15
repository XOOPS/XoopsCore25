<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormTextArea;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormTextArea');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormTextArea.
 */
class XoopsFormTextAreaTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertInstanceOf(XoopsFormTextArea::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormTextArea('My Bio', 'bio');

        $this->assertSame('My Bio', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormTextArea('Bio', 'biography');

        $this->assertSame('biography', $element->getName(false));
    }

    public function testConstructorDefaultValue(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertSame('', $element->getValue());
    }

    public function testConstructorWithValue(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'Some text here');

        $this->assertSame('Some text here', $element->getValue());
    }

    // =========================================================================
    // Default rows and cols
    // =========================================================================

    public function testDefaultRows(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertSame(5, $element->getRows());
    }

    public function testDefaultCols(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertSame(50, $element->getCols());
    }

    // =========================================================================
    // Custom rows and cols
    // =========================================================================

    public function testCustomRows(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 10);

        $this->assertSame(10, $element->getRows());
    }

    public function testCustomCols(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 5, 80);

        $this->assertSame(80, $element->getCols());
    }

    public function testRowsCastToInt(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', '8', 50);

        $this->assertSame(8, $element->getRows());
        $this->assertIsInt($element->getRows());
    }

    public function testColsCastToInt(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 5, '60');

        $this->assertSame(60, $element->getCols());
        $this->assertIsInt($element->getCols());
    }

    // =========================================================================
    // getRows / getCols
    // =========================================================================

    public function testGetRows(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 15, 70);

        $this->assertSame(15, $element->getRows());
    }

    public function testGetCols(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 15, 70);

        $this->assertSame(70, $element->getCols());
    }

    public function testRowsZero(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 0);

        $this->assertSame(0, $element->getRows());
    }

    public function testColsZero(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 5, 0);

        $this->assertSame(0, $element->getCols());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'Hello World');

        $this->assertSame('Hello World', $element->getValue());
        $this->assertSame('Hello World', $element->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '<script>alert("xss")</script>');

        $result = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testGetValueEncodedQuotes(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'text"with\'quotes');

        $result = $element->getValue(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    public function testGetValueEncodedAmpersand(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'foo&bar');

        $this->assertSame('foo&amp;bar', $element->getValue(true));
    }

    public function testGetValueEmpty(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getValue(true));
    }

    public function testGetValueMultiline(): void
    {
        $text = "Line 1\nLine 2\nLine 3";
        $element = new XoopsFormTextArea('Bio', 'bio', $text);

        $this->assertSame($text, $element->getValue());
    }

    // =========================================================================
    // setValue
    // =========================================================================

    public function testSetValue(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');
        $element->setValue('new content');

        $this->assertSame('new content', $element->getValue());
    }

    public function testSetValueOverwrites(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'original');
        $element->setValue('updated');

        $this->assertSame('updated', $element->getValue());
    }

    public function testSetValueWithSpecialChars(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');
        $element->setValue('<b>"bold"</b> & stuff');

        $this->assertSame('<b>"bold"</b> & stuff', $element->getValue(false));
    }

    public function testSetValueWithNewlines(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');
        $element->setValue("line1\r\nline2\nline3");

        $this->assertSame("line1\r\nline2\nline3", $element->getValue());
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'content');

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderNotEmpty(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', 'content');

        $result = $element->render();

        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testIsNotHiddenByDefault(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertFalse($element->isHidden());
    }

    public function testIsNotRequiredByDefault(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertFalse($element->isRequired());
    }

    public function testIsNotContainer(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio');

        $this->assertFalse($element->isContainer());
    }

    #[DataProvider('textAreaDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $name,
        string $value,
        int $rows,
        int $cols
    ): void {
        $element = new XoopsFormTextArea($caption, $name, $value, $rows, $cols);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($name, $element->getName(false));
        $this->assertSame($value, $element->getValue());
        $this->assertSame($rows, $element->getRows());
        $this->assertSame($cols, $element->getCols());
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: int, 4: int}>
     */
    public static function textAreaDataProvider(): array
    {
        return [
            'default size'     => ['Bio', 'bio', '', 5, 50],
            'large textarea'   => ['Content', 'content', 'text', 20, 80],
            'small textarea'   => ['Note', 'note', 'short', 2, 30],
            'with value'       => ['Desc', 'desc', 'Description here', 10, 60],
            'minimal'          => ['', 'x', '', 1, 1],
        ];
    }

    public function testNegativeRowsCastToInt(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', -5, 50);

        $this->assertSame(-5, $element->getRows());
    }

    public function testNegativeColsCastToInt(): void
    {
        $element = new XoopsFormTextArea('Bio', 'bio', '', 5, -50);

        $this->assertSame(-50, $element->getCols());
    }
}
