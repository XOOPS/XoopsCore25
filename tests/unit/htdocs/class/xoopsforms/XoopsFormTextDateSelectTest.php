<?php
namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for XoopsFormTextDateSelect.
 *
 * Source: class/xoopsform/formtextdateselect.php
 */
class XoopsFormTextDateSelectTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormElement');
        xoops_load('XoopsFormText');
        xoops_load('XoopsFormRenderer');
        xoops_load('XoopsFormTextDateSelect');
    }

    /**
     * Constructor with value=0 should set value to current timestamp.
     */
    public function testZeroValueUsesCurrentTime(): void
    {
        $before = time();
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, 0);
        $after = time();

        $value = (int) $element->getValue();

        $this->assertGreaterThanOrEqual($before, $value);
        $this->assertLessThanOrEqual($after, $value);
    }

    /**
     * Constructor with default value (omitted) should use current timestamp.
     */
    public function testDefaultValueUsesCurrentTime(): void
    {
        $before = time();
        $element = new \XoopsFormTextDateSelect('Date', 'date_field');
        $after = time();

        $value = (int) $element->getValue();

        $this->assertGreaterThanOrEqual($before, $value);
        $this->assertLessThanOrEqual($after, $value);
    }

    /**
     * Constructor with a valid timestamp keeps that timestamp.
     */
    public function testValidTimestampIsPreserved(): void
    {
        $timestamp = 1609459200; // 2021-01-01 00:00:00 UTC
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, $timestamp);

        $this->assertEquals($timestamp, $element->getValue());
    }

    /**
     * Constructor with a non-numeric string should use current time.
     */
    public function testNonNumericValueUsesCurrentTime(): void
    {
        $before = time();
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, 'not-a-number');
        $after = time();

        $value = (int) $element->getValue();

        $this->assertGreaterThanOrEqual($before, $value);
        $this->assertLessThanOrEqual($after, $value);
    }

    /**
     * Default size should be 15.
     */
    public function testDefaultSizeIsFifteen(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field');

        $this->assertSame(15, $element->getSize());
    }

    /**
     * Custom size should be respected.
     */
    public function testCustomSize(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 20);

        $this->assertSame(20, $element->getSize());
    }

    /**
     * Maxlength must always be 25.
     */
    public function testMaxlengthIs25(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field');

        $this->assertSame(25, $element->getMaxlength());
    }

    /**
     * Caption must be correctly set.
     */
    public function testCaptionIsSet(): void
    {
        $element = new \XoopsFormTextDateSelect('My Date', 'date_field');

        $this->assertSame('My Date', $element->getCaption());
    }

    /**
     * Name must be correctly set.
     */
    public function testNameIsSet(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'my_date');

        $this->assertSame('my_date', $element->getName());
    }

    /**
     * The element must be an instance of XoopsFormText.
     */
    public function testInheritsXoopsFormText(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field');

        $this->assertInstanceOf(\XoopsFormText::class, $element);
    }

    /**
     * The element must be an instance of XoopsFormElement.
     */
    public function testInheritsXoopsFormElement(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field');

        $this->assertInstanceOf(\XoopsFormElement::class, $element);
    }

    /**
     * render() must return a string.
     */
    public function testRenderReturnsString(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, time());
        $result = $element->render();

        $this->assertIsString($result);
    }

    /**
     * A large valid timestamp should be preserved.
     */
    public function testLargeTimestampIsPreserved(): void
    {
        $timestamp = 2147483647; // Max 32-bit signed integer
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, $timestamp);

        $this->assertEquals($timestamp, $element->getValue());
    }

    /**
     * A small positive timestamp should be preserved.
     */
    public function testSmallPositiveTimestamp(): void
    {
        $timestamp = 1;
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, $timestamp);

        $this->assertEquals($timestamp, $element->getValue());
    }

    /**
     * A negative timestamp should be preserved (dates before epoch).
     */
    public function testNegativeTimestampIsPreserved(): void
    {
        $timestamp = -86400; // One day before Unix epoch
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, $timestamp);

        $this->assertEquals($timestamp, $element->getValue());
    }

    /**
     * An empty string value should use current time (non-numeric).
     */
    public function testEmptyStringUsesCurrentTime(): void
    {
        $before = time();
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, '');
        $after = time();

        $value = (int) $element->getValue();

        // Empty string is non-numeric, so time() is used
        $this->assertGreaterThanOrEqual($before, $value);
        $this->assertLessThanOrEqual($after, $value);
    }

    /**
     * A numeric string timestamp should be treated as numeric and preserved.
     */
    public function testNumericStringIsPreserved(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, '1609459200');

        $this->assertEquals(1609459200, $element->getValue());
    }

    /**
     * Data provider: various constructor inputs and expected behavior.
     *
     * @return array<string, array{int|string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            'zero uses current time'     => [0, true],
            'positive timestamp kept'    => [1609459200, false],
            'negative timestamp kept'    => [-86400, false],
            'string non-numeric uses time' => ['abc', true],
        ];
    }

    /**
     * Various value inputs must be handled correctly.
     */
    #[DataProvider('valueProvider')]
    public function testValueHandling($inputValue, bool $shouldBeCurrentTime): void
    {
        $before = time();
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, $inputValue);
        $after = time();

        $value = (int) $element->getValue();

        if ($shouldBeCurrentTime) {
            $this->assertGreaterThanOrEqual($before, $value);
            $this->assertLessThanOrEqual($after, $value);
        } else {
            $this->assertEquals((int) $inputValue, $value);
        }
    }

    /**
     * setValue should override the initial value.
     */
    public function testSetValueOverridesInitial(): void
    {
        $element = new \XoopsFormTextDateSelect('Date', 'date_field', 15, 1609459200);
        $element->setValue(1234567890);

        $this->assertEquals(1234567890, $element->getValue());
    }
}
