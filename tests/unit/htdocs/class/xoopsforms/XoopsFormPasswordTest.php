<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormPassword;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormPassword');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormPassword.
 */
class XoopsFormPasswordTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertInstanceOf(XoopsFormPassword::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormPassword('Enter Password', 'pass', 20, 64);

        $this->assertSame('Enter Password', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormPassword('Password', 'password_field', 20, 64);

        $this->assertSame('password_field', $element->getName(false));
    }

    public function testConstructorSetsSize(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 30, 64);

        $this->assertSame(30, $element->getSize());
    }

    public function testConstructorSetsMaxlength(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 128);

        $this->assertSame(128, $element->getMaxlength());
    }

    public function testConstructorDefaultValue(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertSame('', $element->getValue());
    }

    public function testConstructorWithValue(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, 'secret123');

        $this->assertSame('secret123', $element->getValue());
    }

    // =========================================================================
    // autoComplete
    // =========================================================================

    public function testAutoCompleteDefaultFalse(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertFalse($element->autoComplete);
    }

    public function testAutoCompleteExplicitFalse(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, '', false);

        $this->assertFalse($element->autoComplete);
    }

    public function testAutoCompleteTrue(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, '', true);

        $this->assertTrue($element->autoComplete);
    }

    public function testAutoCompleteTruthyValue(): void
    {
        // !empty($autoComplete) is used, so any truthy value should work
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, '', 1);

        $this->assertTrue($element->autoComplete);
    }

    public function testAutoCompleteFalsyZero(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, '', 0);

        $this->assertFalse($element->autoComplete);
    }

    // =========================================================================
    // Size and maxlength
    // =========================================================================

    public function testSizeCastToInt(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', '25', 64);

        $this->assertSame(25, $element->getSize());
        $this->assertIsInt($element->getSize());
    }

    public function testMaxlengthCastToInt(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, '128');

        $this->assertSame(128, $element->getMaxlength());
        $this->assertIsInt($element->getMaxlength());
    }

    public function testGetSize(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 40, 64);

        $this->assertSame(40, $element->getSize());
    }

    public function testGetMaxlength(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 256);

        $this->assertSame(256, $element->getMaxlength());
    }

    public function testSizeZero(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 0, 64);

        $this->assertSame(0, $element->getSize());
    }

    public function testMaxlengthZero(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 0);

        $this->assertSame(0, $element->getMaxlength());
    }

    // =========================================================================
    // getValue
    // =========================================================================

    public function testGetValueRaw(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, 'mypass');

        $this->assertSame('mypass', $element->getValue());
        $this->assertSame('mypass', $element->getValue(false));
    }

    public function testGetValueEncoded(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, '<script>alert("xss")</script>');

        $result = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testGetValueEncodedQuotes(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, 'pass"with\'quotes');

        $result = $element->getValue(true);

        $this->assertStringContainsString('&quot;', $result);
        // ENT_HTML5 encodes single quotes as &apos; instead of &#039;
        $this->assertStringContainsString('&apos;', $result);
    }

    public function testGetValueEncodedAmpersand(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, 'foo&bar');

        $this->assertSame('foo&amp;bar', $element->getValue(true));
    }

    public function testGetValueEmpty(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertSame('', $element->getValue());
        $this->assertSame('', $element->getValue(true));
    }

    // =========================================================================
    // setValue
    // =========================================================================

    public function testSetValue(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);
        $element->setValue('newpassword');

        $this->assertSame('newpassword', $element->getValue());
    }

    public function testSetValueOverwrites(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64, 'original');
        $element->setValue('changed');

        $this->assertSame('changed', $element->getValue());
    }

    public function testSetValueSpecialChars(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);
        $element->setValue('p@$$w0rd!#%&*');

        $this->assertSame('p@$$w0rd!#%&*', $element->getValue(false));
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderNotEmpty(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $result = $element->render();

        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testIsNotHiddenByDefault(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertFalse($element->isHidden());
    }

    public function testIsNotRequiredByDefault(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertFalse($element->isRequired());
    }

    public function testIsNotContainer(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', 20, 64);

        $this->assertFalse($element->isContainer());
    }

    #[DataProvider('passwordFieldDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $name,
        int $size,
        int $maxlength,
        string $value,
        bool $autoComplete
    ): void {
        $element = new XoopsFormPassword($caption, $name, $size, $maxlength, $value, $autoComplete);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($name, $element->getName(false));
        $this->assertSame($size, $element->getSize());
        $this->assertSame($maxlength, $element->getMaxlength());
        $this->assertSame($value, $element->getValue());
        $this->assertSame($autoComplete, $element->autoComplete);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: int, 3: int, 4: string, 5: bool}>
     */
    public static function passwordFieldDataProvider(): array
    {
        return [
            'default'          => ['Password', 'pass', 20, 64, '', false],
            'with value'       => ['Password', 'pass', 20, 64, 'secret', false],
            'autocomplete on'  => ['Password', 'pass', 30, 128, '', true],
            'all custom'       => ['Confirm', 'pass2', 40, 256, 'secret', true],
            'no autocomplete'  => ['PIN', 'pin', 10, 6, '', false],
        ];
    }

    public function testNegativeSizeCastToInt(): void
    {
        $element = new XoopsFormPassword('Password', 'pass', -5, 64);

        $this->assertSame(-5, $element->getSize());
    }
}
