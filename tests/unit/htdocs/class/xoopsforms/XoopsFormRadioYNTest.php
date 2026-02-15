<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsFormRadioYN.
 */
#[CoversClass(\XoopsFormRadioYN::class)]
class XoopsFormRadioYNTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormRadioYN');
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $el = new \XoopsFormRadioYN('Enable Feature', 'enable');
        $this->assertSame('Enable Feature', $el->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'my_yn');
        $this->assertSame('my_yn', $el->getName(false));
    }

    // =========================================================================
    // Default Yes/No options
    // =========================================================================

    public function testConstructorAddsYesNoOptions(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $opts = $el->getOptions();
        $this->assertCount(2, $opts);
        $this->assertArrayHasKey(1, $opts);
        $this->assertArrayHasKey(0, $opts);
    }

    public function testDefaultLabelsAreYesAndNo(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $opts = $el->getOptions();
        $this->assertSame(_YES, $opts[1]);
        $this->assertSame(_NO, $opts[0]);
    }

    public function testDefaultLabelsAreConstantValues(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $opts = $el->getOptions();
        $this->assertSame('Yes', $opts[1]);
        $this->assertSame('No', $opts[0]);
    }

    // =========================================================================
    // Custom labels
    // =========================================================================

    public function testCustomYesLabel(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', null, 'Oui');
        $opts = $el->getOptions();
        $this->assertSame('Oui', $opts[1]);
        $this->assertSame(_NO, $opts[0]);
    }

    public function testCustomNoLabel(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', null, _YES, 'Non');
        $opts = $el->getOptions();
        $this->assertSame(_YES, $opts[1]);
        $this->assertSame('Non', $opts[0]);
    }

    public function testCustomBothLabels(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', null, 'Enabled', 'Disabled');
        $opts = $el->getOptions();
        $this->assertSame('Enabled', $opts[1]);
        $this->assertSame('Disabled', $opts[0]);
    }

    // =========================================================================
    // Value setting
    // =========================================================================

    public function testConstructorNullValueLeavesNull(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $this->assertNull($el->getValue());
    }

    public function testConstructorValueOne(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', 1);
        $this->assertSame(1, $el->getValue());
    }

    public function testConstructorValueZero(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', 0);
        $this->assertSame(0, $el->getValue());
    }

    public function testConstructorStringValue(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', '1');
        $this->assertSame('1', $el->getValue());
    }

    public function testSetValueOverrides(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', 1);
        $el->setValue(0);
        $this->assertSame(0, $el->getValue());
    }

    // =========================================================================
    // Inherits Radio behavior
    // =========================================================================

    public function testGetValueReturnsScalar(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', 1);
        $value = $el->getValue();
        $this->assertIsNotArray($value);
    }

    public function testGetValueEncodedWithXss(): void
    {
        // While unusual, test the encoding path with a non-typical value
        $el = new \XoopsFormRadioYN('Cap', 'yn', '<script>');
        $encoded = $el->getValue(true);
        $this->assertStringNotContainsString('<script>', $encoded);
    }

    public function testInheritsAddOption(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $el->addOption(2, 'Maybe');
        $opts = $el->getOptions();
        $this->assertCount(3, $opts);
        $this->assertSame('Maybe', $opts[2]);
    }

    public function testInheritsAddOptionArray(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $el->addOptionArray([2 => 'Maybe', 3 => 'Unknown']);
        $opts = $el->getOptions();
        $this->assertCount(4, $opts);
    }

    public function testInheritsGetDelimeter(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $this->assertSame('&nbsp;', $el->getDelimeter());
    }

    public function testInheritsColumnsProperty(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $this->assertNull($el->columns);
        $el->columns = 2;
        $this->assertSame(2, $el->columns);
    }

    public function testInheritsGetOptionsEncode(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', null, '<b>Yes</b>', '<i>No</i>');
        $opts = $el->getOptions(2);
        $values = array_values($opts);
        $this->assertStringContainsString('&lt;b&gt;', $values[0]);
        $this->assertStringContainsString('&lt;i&gt;', $values[1]);
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn', 1);
        $result = $el->render();
        $this->assertIsString($result);
    }

    // =========================================================================
    // Option order
    // =========================================================================

    public function testYesOptionIsFirstNoOptionIsSecond(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $opts = $el->getOptions();
        $keys = array_keys($opts);
        $this->assertSame(1, $keys[0]);
        $this->assertSame(0, $keys[1]);
    }

    // =========================================================================
    // Instance type
    // =========================================================================

    public function testExtendsXoopsFormRadio(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $this->assertInstanceOf(\XoopsFormRadio::class, $el);
    }

    public function testExtendsXoopsFormElement(): void
    {
        $el = new \XoopsFormRadioYN('Cap', 'yn');
        $this->assertInstanceOf(\XoopsFormElement::class, $el);
    }
}
