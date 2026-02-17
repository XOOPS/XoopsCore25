<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormButton;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormButton');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormButton.
 */
class XoopsFormButtonTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormButton('Submit', 'submit');

        $this->assertInstanceOf(XoopsFormButton::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormButton('Click Me', 'btn');

        $this->assertSame('Click Me', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormButton('Submit', 'submit_btn');

        $this->assertSame('submit_btn', $element->getName(false));
    }

    public function testConstructorDefaultValue(): void
    {
        $element = new XoopsFormButton('Submit', 'btn');

        $this->assertSame('', $element->getValue());
    }

    public function testConstructorWithValue(): void
    {
        $element = new XoopsFormButton('Submit', 'btn', 'Go');

        $this->assertSame('Go', $element->getValue());
    }

    public function testConstructorDefaultTypeButton(): void
    {
        $element = new XoopsFormButton('Submit', 'btn');

        $this->assertSame('button', $element->getType());
    }

    public function testConstructorWithType(): void
    {
        $element = new XoopsFormButton('Submit', 'btn', 'Go', 'submit');

        $this->assertSame('submit', $element->getType());
    }

    // =========================================================================
    // getType - valid types
    // =========================================================================

    public function testGetTypeButton(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'button');

        $this->assertSame('button', $element->getType());
    }

    public function testGetTypeSubmit(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'submit');

        $this->assertSame('submit', $element->getType());
    }

    public function testGetTypeReset(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'reset');

        $this->assertSame('reset', $element->getType());
    }

    // =========================================================================
    // getType - case insensitive validation
    // =========================================================================

    public function testGetTypeUppercaseSubmit(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'SUBMIT');

        // strtolower($this->_type) is used to check against the array,
        // but the original value is returned if valid
        $this->assertSame('SUBMIT', $element->getType());
    }

    public function testGetTypeMixedCaseReset(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'Reset');

        $this->assertSame('Reset', $element->getType());
    }

    // =========================================================================
    // getType - invalid types return 'button'
    // =========================================================================

    public function testGetTypeInvalidReturnsButton(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'invalid');

        $this->assertSame('button', $element->getType());
    }

    public function testGetTypeEmptyReturnsButton(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', '');

        $this->assertSame('button', $element->getType());
    }

    public function testGetTypeRandomStringReturnsButton(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'foobar');

        $this->assertSame('button', $element->getType());
    }

    public function testGetTypeImageReturnsButton(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', 'image');

        $this->assertSame('button', $element->getType());
    }

    public function testGetTypeXssReturnsButton(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', '<script>');

        $this->assertSame('button', $element->getType());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $element = new XoopsFormButton('Label', 'btn', 'Click Me');

        $this->assertSame('Click Me', $element->getValue());
        $this->assertSame('Click Me', $element->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $element = new XoopsFormButton('Label', 'btn', '<script>alert("xss")</script>');

        $result = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testGetValueEncodedQuotes(): void
    {
        $element = new XoopsFormButton('Label', 'btn', 'value"with\'quotes');

        $result = $element->getValue(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    public function testGetValueEncodedAmpersand(): void
    {
        $element = new XoopsFormButton('Label', 'btn', 'foo&bar');

        $this->assertSame('foo&amp;bar', $element->getValue(true));
    }

    public function testGetValueEmpty(): void
    {
        $element = new XoopsFormButton('Label', 'btn');

        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getValue(true));
    }

    // =========================================================================
    // setValue
    // =========================================================================

    public function testSetValue(): void
    {
        $element = new XoopsFormButton('Label', 'btn');
        $element->setValue('New Value');

        $this->assertSame('New Value', $element->getValue());
    }

    public function testSetValueOverwrites(): void
    {
        $element = new XoopsFormButton('Label', 'btn', 'First');
        $element->setValue('Second');

        $this->assertSame('Second', $element->getValue());
    }

    public function testSetValueWithSpecialChars(): void
    {
        $element = new XoopsFormButton('Label', 'btn');
        $element->setValue('Save & Continue');

        $this->assertSame('Save & Continue', $element->getValue(false));
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormButton('Submit', 'submit', 'Go', 'submit');

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderNotEmpty(): void
    {
        $element = new XoopsFormButton('Submit', 'submit', 'Go', 'submit');

        $result = $element->render();

        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testIsNotHiddenByDefault(): void
    {
        $element = new XoopsFormButton('Label', 'btn');

        $this->assertFalse($element->isHidden());
    }

    public function testIsNotRequiredByDefault(): void
    {
        $element = new XoopsFormButton('Label', 'btn');

        $this->assertFalse($element->isRequired());
    }

    public function testIsNotContainer(): void
    {
        $element = new XoopsFormButton('Label', 'btn');

        $this->assertFalse($element->isContainer());
    }

    /**
     * @param string $type
     * @param string $expected
     */
    #[DataProvider('validTypeDataProvider')]
    public function testValidTypeReturnsOriginal(string $type, string $expected): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', $type);

        $this->assertSame($expected, $element->getType());
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function validTypeDataProvider(): array
    {
        return [
            'button lowercase'   => ['button', 'button'],
            'submit lowercase'   => ['submit', 'submit'],
            'reset lowercase'    => ['reset', 'reset'],
            'button uppercase'   => ['BUTTON', 'BUTTON'],
            'submit uppercase'   => ['SUBMIT', 'SUBMIT'],
            'reset uppercase'    => ['RESET', 'RESET'],
            'submit mixed'       => ['Submit', 'Submit'],
            'reset mixed'        => ['Reset', 'Reset'],
            'button mixed'       => ['Button', 'Button'],
        ];
    }

    /**
     * @param string $type
     */
    #[DataProvider('invalidTypeDataProvider')]
    public function testInvalidTypeReturnsButton(string $type): void
    {
        $element = new XoopsFormButton('Label', 'btn', '', $type);

        $this->assertSame('button', $element->getType());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidTypeDataProvider(): array
    {
        return [
            'empty string'     => [''],
            'text'             => ['text'],
            'image'            => ['image'],
            'password'         => ['password'],
            'random'           => ['foobar'],
            'numeric'          => ['123'],
            'xss attempt'      => ['<script>'],
            'space'            => [' '],
            'submit with space'=> [' submit'],
        ];
    }

    /**
     * @param string $caption
     * @param string $name
     * @param string $value
     * @param string $type
     */
    #[DataProvider('buttonDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $name,
        string $value,
        string $type
    ): void {
        $element = new XoopsFormButton($caption, $name, $value, $type);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($name, $element->getName(false));
        $this->assertSame($value, $element->getValue());
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function buttonDataProvider(): array
    {
        return [
            'submit button'   => ['Submit', 'submit', 'Submit', 'submit'],
            'reset button'    => ['Reset', 'reset', 'Reset', 'reset'],
            'custom button'   => ['Go', 'go', 'Go!', 'button'],
            'empty value'     => ['Click', 'btn', '', 'button'],
        ];
    }
}
