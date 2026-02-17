<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsFormColorPicker.
 */
#[CoversClass(\XoopsFormColorPicker::class)]
class XoopsFormColorPickerTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormText');
        xoops_load('XoopsFormColorPicker');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $el = new \XoopsFormColorPicker('Pick a Color', 'color');
        $this->assertSame('Pick a Color', $el->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'my_color');
        $this->assertSame('my_color', $el->getName(false));
    }

    public function testConstructorDefaultValue(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $this->assertSame('#FFFFFF', $el->getValue());
    }

    public function testConstructorCustomValue(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#FF0000');
        $this->assertSame('#FF0000', $el->getValue());
    }

    public function testConstructorSetsSize9(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $this->assertSame(9, $el->getSize());
    }

    public function testConstructorSetsMaxlength7(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $this->assertSame(7, $el->getMaxlength());
    }

    // =========================================================================
    // Inherits XoopsFormText getters
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#00FF00');
        $this->assertSame('#00FF00', $el->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#00FF00');
        $encoded = $el->getValue(true);
        // A valid hex color should not change when encoded
        $this->assertSame('#00FF00', $encoded);
    }

    public function testGetValueEncodedWithSpecialChars(): void
    {
        // Edge case: someone passes a non-color value
        $el = new \XoopsFormColorPicker('Cap', 'color', '<script>');
        $encoded = $el->getValue(true);
        $this->assertStringContainsString('&lt;script&gt;', $encoded);
    }

    public function testSetValueChangesValue(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $el->setValue('#123456');
        $this->assertSame('#123456', $el->getValue());
    }

    public function testGetSizeAlwaysNine(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#000000');
        $this->assertSame(9, $el->getSize());
    }

    public function testGetMaxlengthAlwaysSeven(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#000000');
        $this->assertSame(7, $el->getMaxlength());
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $result = $el->render();
        $this->assertIsString($result);
    }

    // =========================================================================
    // renderValidationJS
    // =========================================================================

    public function testRenderValidationJSContainsHexRegex(): void
    {
        $el = new \XoopsFormColorPicker('Pick Color', 'color');
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('RegExp', $js);
        $this->assertStringContainsString('#[0-9a-fA-F]', $js);
        $this->assertStringContainsString('{6}', $js);
    }

    public function testRenderValidationJSContainsFieldName(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'my_color_field');
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('my_color_field', $js);
    }

    public function testRenderValidationJSContainsAlertAndFocus(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('window.alert', $js);
        $this->assertStringContainsString('.focus()', $js);
    }

    public function testRenderValidationJSContainsReturnFalse(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('return false', $js);
    }

    public function testRenderValidationJSUsesCaption(): void
    {
        $el = new \XoopsFormColorPicker('Background Color', 'bgcolor');
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('Background Color', $js);
    }

    public function testRenderValidationJSEmptyCaptionUsesName(): void
    {
        $el = new \XoopsFormColorPicker('', 'bgcolor');
        $js = $el->renderValidationJS();
        $this->assertStringContainsString('bgcolor', $js);
    }

    // =========================================================================
    // Instance type
    // =========================================================================

    public function testExtendsXoopsFormText(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $this->assertInstanceOf(\XoopsFormText::class, $el);
    }

    public function testExtendsXoopsFormElement(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color');
        $this->assertInstanceOf(\XoopsFormElement::class, $el);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testLowerCaseHexValue(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#abcdef');
        $this->assertSame('#abcdef', $el->getValue());
    }

    public function testMixedCaseHexValue(): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', '#AaBbCc');
        $this->assertSame('#AaBbCc', $el->getValue());
    }

    #[DataProvider('provideColorValues')]
    public function testConstructorWithVariousColors(string $value): void
    {
        $el = new \XoopsFormColorPicker('Cap', 'color', $value);
        $this->assertSame($value, $el->getValue());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideColorValues(): array
    {
        return [
            'white'   => ['#FFFFFF'],
            'black'   => ['#000000'],
            'red'     => ['#FF0000'],
            'green'   => ['#00FF00'],
            'blue'    => ['#0000FF'],
            'custom'  => ['#A1B2C3'],
            'lower'   => ['#abcdef'],
        ];
    }
}
