<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsFormCheckBox.
 */
#[CoversClass(\XoopsFormCheckBox::class)]
class XoopsFormCheckBoxTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormCheckBox');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $el = new \XoopsFormCheckBox('My Checkbox', 'mychk');
        $this->assertSame('My Checkbox', $el->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'field_chk');
        $this->assertSame('field_chk', $el->getName(false));
    }

    public function testConstructorNullValueLeavesEmpty(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', null);
        $this->assertSame([], $el->getValue());
    }

    public function testConstructorScalarValue(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', 'abc');
        $this->assertSame(['abc'], $el->getValue());
    }

    public function testConstructorArrayValue(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', ['a', 'b']);
        $this->assertSame(['a', 'b'], $el->getValue());
    }

    public function testConstructorDefaultDelimiter(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $this->assertSame('&nbsp;', $el->getDelimeter());
    }

    public function testConstructorCustomDelimiter(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', null, '<br>');
        $this->assertSame('<br>', $el->getDelimeter());
    }

    public function testConstructorSetsFormTypeToCheckbox(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $this->assertSame('checkbox', $el->getFormType());
    }

    // =========================================================================
    // setValue — REPLACES _value (unlike FormSelect which appends)
    // =========================================================================

    public function testSetValueScalar(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->setValue('one');
        $this->assertSame(['one'], $el->getValue());
    }

    public function testSetValueArray(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->setValue(['x', 'y']);
        $this->assertSame(['x', 'y'], $el->getValue());
    }

    public function testSetValueReplacesNotAppends(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', 'first');
        $el->setValue('second');
        // Unlike FormSelect, checkbox replaces the value
        $this->assertSame(['second'], $el->getValue());
        $this->assertNotContains('first', $el->getValue());
    }

    public function testSetValueReplacesWithArray(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', ['a', 'b']);
        $el->setValue(['c', 'd']);
        $this->assertSame(['c', 'd'], $el->getValue());
    }

    public function testSetValueMultipleCallsOnlyKeepsLast(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->setValue('a');
        $el->setValue('b');
        $el->setValue('c');
        $this->assertSame(['c'], $el->getValue());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', '<script>xss</script>');
        $values = $el->getValue(false);
        $this->assertSame(['<script>xss</script>'], $values);
    }

    public function testGetValueEncoded(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', '<b>bold</b>');
        $values = $el->getValue(true);
        $this->assertStringContainsString('&lt;b&gt;', $values[0]);
    }

    public function testGetValueEncodedPreservesFalsy(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', '');
        $values = $el->getValue(true);
        $this->assertSame([''], $values);
    }

    public function testGetValueReturnsArray(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', 'single');
        $this->assertIsArray($el->getValue());
    }

    public function testGetValueEmptyWhenNoValueSet(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $this->assertSame([], $el->getValue());
    }

    // =========================================================================
    // addOption / addOptionArray
    // =========================================================================

    public function testAddOptionWithName(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('val1', 'Label One');
        $opts = $el->getOptions();
        $this->assertArrayHasKey('val1', $opts);
        $this->assertSame('Label One', $opts['val1']);
    }

    public function testAddOptionWithoutNameUsesValue(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('val1');
        $opts = $el->getOptions();
        $this->assertSame('val1', $opts['val1']);
    }

    public function testAddOptionWithEmptyNameUsesValue(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('myval', '');
        $opts = $el->getOptions();
        $this->assertSame('myval', $opts['myval']);
    }

    public function testAddOptionArray(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOptionArray(['a' => 'Alpha', 'b' => 'Beta']);
        $opts = $el->getOptions();
        $this->assertCount(2, $opts);
        $this->assertSame('Alpha', $opts['a']);
        $this->assertSame('Beta', $opts['b']);
    }

    public function testAddOptionArrayIgnoresNonArray(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOptionArray('not_an_array');
        $this->assertSame([], $el->getOptions());
    }

    // =========================================================================
    // getOptions
    // =========================================================================

    public function testGetOptionsRaw(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(false);
        $this->assertSame('<i>name</i>', $opts['<b>key</b>']);
    }

    public function testGetOptionsEncode1EncodesKeysOnly(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(1);
        $keys = array_keys($opts);
        $this->assertStringContainsString('&lt;b&gt;', $keys[0]);
        $values = array_values($opts);
        $this->assertSame('<i>name</i>', $values[0]);
    }

    public function testGetOptionsEncode2EncodesBothKeysAndNames(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('<b>key</b>', '<i>name</i>');
        $opts = $el->getOptions(2);
        $keys = array_keys($opts);
        $values = array_values($opts);
        $this->assertStringContainsString('&lt;b&gt;', $keys[0]);
        $this->assertStringContainsString('&lt;i&gt;', $values[0]);
    }

    public function testGetOptionsEmptyReturnsEmptyArray(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $this->assertSame([], $el->getOptions());
    }

    // =========================================================================
    // getDelimeter
    // =========================================================================

    public function testGetDelimeterRaw(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', null, '&nbsp;');
        $this->assertSame('&nbsp;', $el->getDelimeter(false));
    }

    public function testGetDelimeterEncoded(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', null, '&nbsp;');
        $encoded = $el->getDelimeter(true);
        // &nbsp; is replaced with space, then htmlspecialchars applied
        $this->assertSame(' ', $encoded);
    }

    public function testGetDelimeterEncodedWithHtmlDelimiter(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', null, '<br>');
        $encoded = $el->getDelimeter(true);
        $this->assertStringContainsString('&lt;br&gt;', $encoded);
    }

    public function testGetDelimeterCustomNotEncoded(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', null, ' | ');
        $this->assertSame(' | ', $el->getDelimeter(false));
    }

    // =========================================================================
    // columns property
    // =========================================================================

    public function testColumnsPropertyDefaultNull(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $this->assertNull($el->columns);
    }

    public function testColumnsPropertyCanBeSet(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->columns = 3;
        $this->assertSame(3, $el->columns);
    }

    public function testColumnsPropertySetToOne(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->columns = 1;
        $this->assertSame(1, $el->columns);
    }

    // =========================================================================
    // formType
    // =========================================================================

    public function testFormTypeIsCheckbox(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $this->assertSame('checkbox', $el->getFormType());
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('val', 'Label');
        $result = $el->render();
        $this->assertIsString($result);
    }

    // =========================================================================
    // renderValidationJS
    // =========================================================================

    public function testRenderValidationJSWithCustomCode(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->customValidationCode = ['alert("custom");'];
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('alert("custom")', $js);
    }

    public function testRenderValidationJSWhenRequired(): void
    {
        $el = new \XoopsFormCheckBox('My Checkbox', 'chk');
        $el->_required = true;
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('hasChecked', $js);
        $this->assertStringContainsString('checkBox', $js);
        $this->assertStringContainsString('window.alert', $js);
    }

    public function testRenderValidationJSWhenRequiredContainsCaption(): void
    {
        $el = new \XoopsFormCheckBox('Accept Terms', 'terms');
        $el->_required = true;
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('Accept Terms', $js);
    }

    public function testRenderValidationJSWhenRequiredEmptyCaption(): void
    {
        $el = new \XoopsFormCheckBox('', 'myfield');
        $el->_required = true;
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('myfield', $js);
    }

    public function testRenderValidationJSNotRequired(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $js = $el->renderValidationJS();
        $this->assertSame('', $js);
    }

    public function testRenderValidationJSCustomCodeTakesPrecedence(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->_required = true;
        $el->customValidationCode = ['custom_check();'];
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('custom_check()', $js);
        $this->assertStringNotContainsString('hasChecked', $js);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testSpecialCharsInValues(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->setValue('<script>alert("xss")</script>');
        $encoded = $el->getValue(true);
        $this->assertStringNotContainsString('<script>', $encoded[0]);
    }

    public function testSpecialCharsInOptionNames(): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk');
        $el->addOption('k', '<img src=x onerror=alert(1)>');
        $opts = $el->getOptions(2);
        $names = array_values($opts);
        $this->assertStringNotContainsString('<img', $names[0]);
    }

    /**
     * Verify that setValue behavior is fundamentally different from FormSelect.
     * FormSelect appends, FormCheckBox replaces.
     */
    public function testSetValueReplaceBehaviorContrastWithSelect(): void
    {
        $chk = new \XoopsFormCheckBox('Cap', 'chk', 'initial');
        $chk->setValue('replaced');
        $this->assertSame(['replaced'], $chk->getValue());
        $this->assertNotContains('initial', $chk->getValue());
    }

    #[DataProvider('provideConstructorValues')]
    public function testConstructorWithVariousValues($input, array $expected): void
    {
        $el = new \XoopsFormCheckBox('Cap', 'chk', $input);
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
}
