<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsFormSelect.
 */
#[CoversClass(\XoopsFormSelect::class)]
class XoopsFormSelectTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormSelect');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $el = new \XoopsFormSelect('My Caption', 'myselect');
        $this->assertSame('My Caption', $el->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $el = new \XoopsFormSelect('Cap', 'field_name');
        $this->assertSame('field_name', $el->getName(false));
    }

    public function testConstructorDefaultSize(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $this->assertSame(1, $el->getSize());
    }

    public function testConstructorCustomSize(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null, 5);
        $this->assertSame(5, $el->getSize());
    }

    public function testConstructorSizeCastToInt(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null, '7');
        $this->assertSame(7, $el->getSize());
    }

    public function testConstructorDefaultNotMultiple(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $this->assertFalse($el->isMultiple());
    }

    public function testConstructorMultipleTrue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null, 1, true);
        $this->assertTrue($el->isMultiple());
    }

    public function testConstructorNullValueLeavesEmpty(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null);
        $this->assertSame([], $el->getValue());
    }

    public function testConstructorScalarValue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', 'abc');
        $this->assertSame(['abc'], $el->getValue());
    }

    public function testConstructorArrayValue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', ['a', 'b']);
        $this->assertSame(['a', 'b'], $el->getValue());
    }

    // =========================================================================
    // isMultiple
    // =========================================================================

    public function testIsMultipleFalse(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null, 1, false);
        $this->assertFalse($el->isMultiple());
    }

    public function testIsMultipleTrue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null, 3, true);
        $this->assertTrue($el->isMultiple());
    }

    // =========================================================================
    // getSize
    // =========================================================================

    public function testGetSizeDefault(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $this->assertSame(1, $el->getSize());
    }

    public function testGetSizeCustom(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', null, 10);
        $this->assertSame(10, $el->getSize());
    }

    // =========================================================================
    // setValue — APPENDS to _value
    // =========================================================================

    public function testSetValueScalar(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->setValue('one');
        $this->assertSame(['one'], $el->getValue());
    }

    public function testSetValueArray(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->setValue(['x', 'y']);
        $this->assertSame(['x', 'y'], $el->getValue());
    }

    public function testSetValueAppendsDoesNotReplace(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', 'first');
        $el->setValue('second');
        $this->assertSame(['first', 'second'], $el->getValue());
    }

    public function testSetValueAppendsArrayToExisting(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', 'a');
        $el->setValue(['b', 'c']);
        $this->assertSame(['a', 'b', 'c'], $el->getValue());
    }

    public function testSetValueNullDoesNotAppend(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', 'keep');
        $el->setValue(null);
        $this->assertSame(['keep'], $el->getValue());
    }

    public function testSetValueIntegerScalar(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->setValue(42);
        $this->assertSame([42], $el->getValue());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', '<script>xss</script>');
        $values = $el->getValue(false);
        $this->assertSame(['<script>xss</script>'], $values);
    }

    public function testGetValueEncoded(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', '<b>bold</b>');
        $values = $el->getValue(true);
        $this->assertNotSame('<b>bold</b>', $values[0]);
        $this->assertStringContainsString('&lt;b&gt;', $values[0]);
    }

    public function testGetValueEncodedPreservesFalsy(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', '');
        $values = $el->getValue(true);
        // Empty string is falsy, so getValue returns it unencoded
        $this->assertSame([''], $values);
    }

    public function testGetValueEncodedZeroValue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', 0);
        $values = $el->getValue(true);
        // 0 is falsy, so getValue returns it unencoded
        $this->assertSame([0], $values);
    }

    public function testGetValueReturnsArray(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', 'single');
        $this->assertIsArray($el->getValue());
    }

    public function testGetValueEmptyWhenNoValueSet(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $this->assertSame([], $el->getValue());
    }

    // =========================================================================
    // addOption
    // =========================================================================

    public function testAddOptionWithName(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('val1', 'Label One');
        $opts = $el->getOptions();
        $this->assertArrayHasKey('val1', $opts);
        $this->assertSame('Label One', $opts['val1']);
    }

    public function testAddOptionWithoutNameUsesValue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('val1');
        $opts = $el->getOptions();
        $this->assertSame('val1', $opts['val1']);
    }

    public function testAddOptionWithEmptyNameUsesValue(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('myval', '');
        $opts = $el->getOptions();
        $this->assertSame('myval', $opts['myval']);
    }

    public function testAddOptionIntegerKey(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption(1, 'One');
        $el->addOption(2, 'Two');
        $opts = $el->getOptions();
        $this->assertSame('One', $opts[1]);
        $this->assertSame('Two', $opts[2]);
    }

    public function testAddOptionOverwritesSameKey(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('key', 'First');
        $el->addOption('key', 'Second');
        $opts = $el->getOptions();
        $this->assertSame('Second', $opts['key']);
    }

    // =========================================================================
    // addOptionArray
    // =========================================================================

    public function testAddOptionArray(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOptionArray(['a' => 'Alpha', 'b' => 'Beta']);
        $opts = $el->getOptions();
        $this->assertCount(2, $opts);
        $this->assertSame('Alpha', $opts['a']);
        $this->assertSame('Beta', $opts['b']);
    }

    public function testAddOptionArrayMergesWithExisting(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('x', 'X');
        $el->addOptionArray(['y' => 'Y', 'z' => 'Z']);
        $opts = $el->getOptions();
        $this->assertCount(3, $opts);
    }

    public function testAddOptionArrayIgnoresNonArray(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOptionArray('not_an_array');
        $this->assertSame([], $el->getOptions());
    }

    // =========================================================================
    // getOptions
    // =========================================================================

    public function testGetOptionsRaw(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(false);
        $this->assertSame('<i>name</i>', $opts['<b>key</b>']);
    }

    public function testGetOptionsEncode1EncodesKeysOnly(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(1);
        // Key should be encoded
        $keys = array_keys($opts);
        $this->assertStringContainsString('&lt;b&gt;', $keys[0]);
        // Name should NOT be encoded at level 1
        $values = array_values($opts);
        $this->assertSame('<i>name</i>', $values[0]);
    }

    public function testGetOptionsEncode2EncodesBothKeysAndNames(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(2);
        $keys = array_keys($opts);
        $values = array_values($opts);
        $this->assertStringContainsString('&lt;b&gt;', $keys[0]);
        $this->assertStringContainsString('&lt;i&gt;', $values[0]);
    }

    public function testGetOptionsEmptyReturnsEmptyArray(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $this->assertSame([], $el->getOptions());
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('val', 'Label');
        $result = $el->render();
        $this->assertIsString($result);
    }

    // =========================================================================
    // renderValidationJS
    // =========================================================================

    public function testRenderValidationJSWithCustomCode(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->customValidationCode = ['alert("custom");'];
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('alert("custom")', $js);
    }

    public function testRenderValidationJSWhenRequired(): void
    {
        $el = new \XoopsFormSelect('My Select', 'sel');
        $el->_required = true;
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('hasSelected', $js);
        $this->assertStringContainsString('selectBox', $js);
        $this->assertStringContainsString('window.alert', $js);
    }

    public function testRenderValidationJSWhenRequiredContainsCaption(): void
    {
        $el = new \XoopsFormSelect('Choose Color', 'color');
        $el->_required = true;
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('Choose Color', $js);
    }

    public function testRenderValidationJSWhenRequiredEmptyCaption(): void
    {
        $el = new \XoopsFormSelect('', 'myfield');
        $el->_required = true;
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('myfield', $js);
    }

    public function testRenderValidationJSNotRequired(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $js = $el->renderValidationJS();
        $this->assertSame('', $js);
    }

    public function testRenderValidationJSCustomCodeTakesPrecedence(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->_required = true;
        $el->customValidationCode = ['custom_check();'];
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('custom_check()', $js);
        $this->assertStringNotContainsString('hasSelected', $js);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testSpecialCharsInValues(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->setValue('<script>alert("xss")</script>');
        $encoded = $el->getValue(true);
        $this->assertStringNotContainsString('<script>', $encoded[0]);
    }

    public function testSpecialCharsInOptionNames(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('k', '<img src=x onerror=alert(1)>');
        $opts = $el->getOptions(2);
        $names = array_values($opts);
        $this->assertStringNotContainsString('<img', $names[0]);
    }

    public function testMultipleSetValueCallsAccumulate(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->setValue('a');
        $el->setValue('b');
        $el->setValue('c');
        $this->assertSame(['a', 'b', 'c'], $el->getValue());
    }

    #[DataProvider('provideConstructorValues')]
    public function testConstructorWithVariousValues($input, array $expected): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel', $input);
        $this->assertSame($expected, $el->getValue());
    }

    /**
     * @return array<string, array{mixed, array}>
     */
    public static function provideConstructorValues(): array
    {
        return [
            'null'         => [null, []],
            'empty string' => ['', ['']],
            'zero'         => [0, [0]],
            'string'       => ['hello', ['hello']],
            'array'        => [['a', 'b'], ['a', 'b']],
            'empty array'  => [[], []],
        ];
    }

    public function testOptionsPreserveInsertionOrder(): void
    {
        $el = new \XoopsFormSelect('Cap', 'sel');
        $el->addOption('c', 'Charlie');
        $el->addOption('a', 'Alpha');
        $el->addOption('b', 'Bravo');
        $keys = array_keys($el->getOptions());
        $this->assertSame(['c', 'a', 'b'], $keys);
    }
}
