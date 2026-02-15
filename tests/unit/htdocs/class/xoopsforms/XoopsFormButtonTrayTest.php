<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsFormButtonTray.
 */
#[CoversClass(\XoopsFormButtonTray::class)]
class XoopsFormButtonTrayTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormButtonTray');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsName(): void
    {
        $el = new \XoopsFormButtonTray('submit_btn');
        $this->assertSame('submit_btn', $el->getName(false));
    }

    public function testConstructorDefaultValueEmpty(): void
    {
        $el = new \XoopsFormButtonTray('btn');
        $this->assertSame('', $el->getValue());
    }

    public function testConstructorCustomValue(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Save');
        $this->assertSame('Save', $el->getValue());
    }

    public function testConstructorDefaultTypeSubmit(): void
    {
        $el = new \XoopsFormButtonTray('btn');
        $this->assertSame('submit', $el->getType());
    }

    public function testConstructorEmptyTypeDefaultsToSubmit(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', '');
        $this->assertSame('submit', $el->getType());
    }

    public function testConstructorCustomType(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Reset', 'reset');
        $this->assertSame('reset', $el->getType());
    }

    public function testConstructorButtonType(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Click', 'button');
        $this->assertSame('button', $el->getType());
    }

    public function testConstructorOnclickSetsExtra(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', 'submit', 'onclick="doSomething()"');
        $extra = $el->getExtra();
        $this->assertStringContainsString('onclick="doSomething()"', $extra);
    }

    public function testConstructorNoOnclickSetsEmptyExtra(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', 'submit', '');
        $extra = $el->getExtra();
        // getExtra() always prepends a space; empty extra should be just space
        $this->assertSame(' ', $extra);
    }

    public function testConstructorShowDeleteFalseByDefault(): void
    {
        $el = new \XoopsFormButtonTray('btn');
        $this->assertFalse($el->_showDelete);
    }

    public function testConstructorShowDeleteTrue(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', 'submit', '', true);
        $this->assertTrue($el->_showDelete);
    }

    // =========================================================================
    // getValue / setValue
    // =========================================================================

    public function testGetValueReturnsString(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Submit Form');
        $this->assertSame('Submit Form', $el->getValue());
    }

    public function testSetValueChangesValue(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Old');
        $el->setValue('New');
        $this->assertSame('New', $el->getValue());
    }

    public function testSetValueToEmpty(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Something');
        $el->setValue('');
        $this->assertSame('', $el->getValue());
    }

    public function testGetValueNoEncodeParam(): void
    {
        // Unlike FormSelect/FormCheckBox, ButtonTray's getValue() has no $encode param
        $el = new \XoopsFormButtonTray('btn', '<b>Bold</b>');
        $this->assertSame('<b>Bold</b>', $el->getValue());
    }

    // =========================================================================
    // getType
    // =========================================================================

    public function testGetTypeSubmit(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', 'submit');
        $this->assertSame('submit', $el->getType());
    }

    public function testGetTypeReset(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Reset', 'reset');
        $this->assertSame('reset', $el->getType());
    }

    public function testGetTypeButton(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Click', 'button');
        $this->assertSame('button', $el->getType());
    }

    // =========================================================================
    // _showDelete
    // =========================================================================

    public function testShowDeleteFlag(): void
    {
        $el = new \XoopsFormButtonTray('btn', '', '', '', true);
        $this->assertTrue($el->_showDelete);
    }

    public function testShowDeleteFlagFalse(): void
    {
        $el = new \XoopsFormButtonTray('btn', '', '', '', false);
        $this->assertFalse($el->_showDelete);
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Submit');
        $result = $el->render();
        $this->assertIsString($result);
    }

    // =========================================================================
    // Extra attribute from onclick
    // =========================================================================

    public function testOnclickStoredInExtra(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', 'submit', 'onclick="return confirm(\'Sure?\')"');
        $extra = $el->getExtra();
        $this->assertStringContainsString('onclick=', $extra);
    }

    public function testNoOnclickExtraIsEmpty(): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Go', 'submit');
        $extra = $el->getExtra();
        // Empty string onclick in constructor means setExtra('') was called
        $this->assertSame(' ', $extra);
    }

    // =========================================================================
    // Instance type
    // =========================================================================

    public function testExtendsXoopsFormElement(): void
    {
        $el = new \XoopsFormButtonTray('btn');
        $this->assertInstanceOf(\XoopsFormElement::class, $el);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testConstructorWithAllParams(): void
    {
        $el = new \XoopsFormButtonTray(
            'my_button',
            'Save Changes',
            'submit',
            'onclick="validate()"',
            true
        );
        $this->assertSame('my_button', $el->getName(false));
        $this->assertSame('Save Changes', $el->getValue());
        $this->assertSame('submit', $el->getType());
        $this->assertTrue($el->_showDelete);
        $this->assertStringContainsString('onclick="validate()"', $el->getExtra());
    }

    public function testSetValueMultipleTimes(): void
    {
        $el = new \XoopsFormButtonTray('btn');
        $el->setValue('First');
        $el->setValue('Second');
        $el->setValue('Third');
        $this->assertSame('Third', $el->getValue());
    }

    public function testNameWithSpecialChars(): void
    {
        $el = new \XoopsFormButtonTray('btn[0]');
        $this->assertSame('btn[0]', $el->getName(false));
    }

    #[DataProvider('provideTypeDefaults')]
    public function testTypeDefaults(string $input, string $expected): void
    {
        $el = new \XoopsFormButtonTray('btn', 'Val', $input);
        $this->assertSame($expected, $el->getType());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideTypeDefaults(): array
    {
        return [
            'empty defaults to submit' => ['', 'submit'],
            'submit stays submit'      => ['submit', 'submit'],
            'reset stays reset'        => ['reset', 'reset'],
            'button stays button'      => ['button', 'button'],
        ];
    }
}
