<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsFormRadio.
 */
#[CoversClass(\XoopsFormRadio::class)]
class XoopsFormRadioTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormRadio');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $el = new \XoopsFormRadio('My Radio', 'myradio');
        $this->assertSame('My Radio', $el->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $el = new \XoopsFormRadio('Cap', 'field_radio');
        $this->assertSame('field_radio', $el->getName(false));
    }

    public function testConstructorNullValueLeavesNull(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', null);
        $this->assertNull($el->getValue());
    }

    public function testConstructorScalarValue(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', 'abc');
        $this->assertSame('abc', $el->getValue());
    }

    public function testConstructorIntegerValue(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', 1);
        $this->assertSame(1, $el->getValue());
    }

    public function testConstructorDefaultDelimiter(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $this->assertSame('&nbsp;', $el->getDelimeter());
    }

    public function testConstructorCustomDelimiter(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', null, '<br>');
        $this->assertSame('<br>', $el->getDelimeter());
    }

    // =========================================================================
    // getValue — returns SCALAR (not array)
    // =========================================================================

    public function testGetValueReturnsScalar(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', 'selected');
        $value = $el->getValue();
        $this->assertSame('selected', $value);
        $this->assertIsNotArray($value);
    }

    public function testGetValueReturnsNullWhenNotSet(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $this->assertNull($el->getValue());
    }

    public function testGetValueRawNoEncoding(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', '<script>xss</script>');
        $this->assertSame('<script>xss</script>', $el->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', '<b>bold</b>');
        $encoded = $el->getValue(true);
        $this->assertStringContainsString('&lt;b&gt;', $encoded);
        $this->assertStringNotContainsString('<b>', $encoded);
    }

    public function testGetValueEncodedNullReturnsNull(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $this->assertNull($el->getValue(true));
    }

    public function testGetValueEncodedSpecialChars(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', '"quotes" & <angles>');
        $encoded = $el->getValue(true);
        $this->assertStringContainsString('&amp;', $encoded);
        $this->assertStringContainsString('&quot;', $encoded);
        $this->assertStringContainsString('&lt;', $encoded);
    }

    // =========================================================================
    // setValue — sets scalar value
    // =========================================================================

    public function testSetValueScalar(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->setValue('option1');
        $this->assertSame('option1', $el->getValue());
    }

    public function testSetValueReplaces(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', 'old');
        $el->setValue('new');
        $this->assertSame('new', $el->getValue());
    }

    public function testSetValueInteger(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->setValue(42);
        $this->assertSame(42, $el->getValue());
    }

    public function testSetValueZero(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->setValue(0);
        $this->assertSame(0, $el->getValue());
    }

    public function testSetValueEmptyString(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->setValue('');
        $this->assertSame('', $el->getValue());
    }

    // =========================================================================
    // addOption / addOptionArray
    // =========================================================================

    public function testAddOptionWithName(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('val1', 'Label One');
        $opts = $el->getOptions();
        $this->assertArrayHasKey('val1', $opts);
        $this->assertSame('Label One', $opts['val1']);
    }

    public function testAddOptionWithoutNameUsesValue(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('val1');
        $opts = $el->getOptions();
        $this->assertSame('val1', $opts['val1']);
    }

    public function testAddOptionWithEmptyNameUsesValue(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('myval', '');
        $opts = $el->getOptions();
        $this->assertSame('myval', $opts['myval']);
    }

    public function testAddOptionIntegerKey(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption(1, 'One');
        $el->addOption(2, 'Two');
        $opts = $el->getOptions();
        $this->assertSame('One', $opts[1]);
        $this->assertSame('Two', $opts[2]);
    }

    public function testAddOptionOverwritesSameKey(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('key', 'First');
        $el->addOption('key', 'Second');
        $opts = $el->getOptions();
        $this->assertSame('Second', $opts['key']);
    }

    public function testAddOptionArray(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOptionArray(['a' => 'Alpha', 'b' => 'Beta']);
        $opts = $el->getOptions();
        $this->assertCount(2, $opts);
        $this->assertSame('Alpha', $opts['a']);
        $this->assertSame('Beta', $opts['b']);
    }

    public function testAddOptionArrayMergesWithExisting(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('x', 'X');
        $el->addOptionArray(['y' => 'Y', 'z' => 'Z']);
        $opts = $el->getOptions();
        $this->assertCount(3, $opts);
    }

    public function testAddOptionArrayIgnoresNonArray(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOptionArray('not_an_array');
        $this->assertSame([], $el->getOptions());
    }

    // =========================================================================
    // getOptions
    // =========================================================================

    public function testGetOptionsRaw(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(false);
        $this->assertSame('<i>name</i>', $opts['<b>key</b>']);
    }

    public function testGetOptionsEncode1EncodesKeysOnly(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(1);
        $keys = array_keys($opts);
        $this->assertStringContainsString('&lt;b&gt;', $keys[0]);
        $values = array_values($opts);
        $this->assertSame('<i>name</i>', $values[0]);
    }

    public function testGetOptionsEncode2EncodesBothKeysAndNames(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(2);
        $keys = array_keys($opts);
        $values = array_values($opts);
        $this->assertStringContainsString('&lt;b&gt;', $keys[0]);
        $this->assertStringContainsString('&lt;i&gt;', $values[0]);
    }

    public function testGetOptionsEmptyReturnsEmptyArray(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $this->assertSame([], $el->getOptions());
    }

    // =========================================================================
    // getDelimeter
    // =========================================================================

    public function testGetDelimeterRaw(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', null, '&nbsp;');
        $this->assertSame('&nbsp;', $el->getDelimeter(false));
    }

    public function testGetDelimeterEncoded(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', null, '&nbsp;');
        $encoded = $el->getDelimeter(true);
        // &nbsp; is replaced with space, then htmlspecialchars applied
        $this->assertSame(' ', $encoded);
    }

    public function testGetDelimeterEncodedWithHtmlDelimiter(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', null, '<br>');
        $encoded = $el->getDelimeter(true);
        $this->assertStringContainsString('&lt;br&gt;', $encoded);
    }

    public function testGetDelimeterCustomNotEncoded(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', null, ' | ');
        $this->assertSame(' | ', $el->getDelimeter(false));
    }

    // =========================================================================
    // columns property
    // =========================================================================

    public function testColumnsPropertyDefaultNull(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $this->assertNull($el->columns);
    }

    public function testColumnsPropertyCanBeSet(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->columns = 3;
        $this->assertSame(3, $el->columns);
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('val', 'Label');
        $result = $el->render();
        $this->assertIsString($result);
    }

    // =========================================================================
    // Contrast with FormSelect and FormCheckBox
    // =========================================================================

    /**
     * Verify getValue returns scalar, not array (key difference from Select/CheckBox).
     */
    public function testGetValueIsScalarNotArray(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', 'val');
        $value = $el->getValue();
        $this->assertIsNotArray($value);
        $this->assertSame('val', $value);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testSpecialCharsInValues(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->setValue('<script>alert("xss")</script>');
        $encoded = $el->getValue(true);
        $this->assertStringNotContainsString('<script>', $encoded);
    }

    public function testSpecialCharsInOptionNames(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('k', '<img src=x onerror=alert(1)>');
        $opts = $el->getOptions(2);
        $names = array_values($opts);
        $this->assertStringNotContainsString('<img', $names[0]);
    }

    public function testOptionsPreserveInsertionOrder(): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad');
        $el->addOption('c', 'Charlie');
        $el->addOption('a', 'Alpha');
        $el->addOption('b', 'Bravo');
        $keys = array_keys($el->getOptions());
        $this->assertSame(['c', 'a', 'b'], $keys);
    }

    #[DataProvider('provideConstructorValues')]
    public function testConstructorWithVariousValues($input, $expected): void
    {
        $el = new \XoopsFormRadio('Cap', 'rad', $input);
        $this->assertSame($expected, $el->getValue());
    }

    /**
     * @return array<string, array{mixed, mixed}>
     */
    public static function provideConstructorValues(): array
    {
        return [
            'null'         => [null, null],
            'empty string' => ['', ''],
            'zero'         => [0, 0],
            'string'       => ['hello', 'hello'],
            'integer'      => [42, 42],
        ];
    }
}
