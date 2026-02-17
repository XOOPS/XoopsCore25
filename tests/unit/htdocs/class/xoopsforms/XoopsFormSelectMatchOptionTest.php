<?php
namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for XoopsFormSelectMatchOption.
 *
 * Source: class/xoopsform/formselectmatchoption.php
 */
class XoopsFormSelectMatchOptionTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormElement');
        xoops_load('XoopsFormSelect');
        xoops_load('XoopsFormSelectMatchOption');
    }

    /**
     * Constructor must add exactly 4 options.
     */
    public function testConstructorAddsFourOptions(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');
        $options = $element->getOptions();

        $this->assertCount(4, $options, 'There should be exactly 4 match options');
    }

    /**
     * Option keys must match the XOOPS_MATCH_* constants.
     */
    public function testOptionKeysMatchConstants(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');
        $options = $element->getOptions();

        $this->assertArrayHasKey(XOOPS_MATCH_START, $options);
        $this->assertArrayHasKey(XOOPS_MATCH_END, $options);
        $this->assertArrayHasKey(XOOPS_MATCH_EQUAL, $options);
        $this->assertArrayHasKey(XOOPS_MATCH_CONTAIN, $options);
    }

    /**
     * Option values must match the language string constants.
     */
    public function testOptionValuesMatchLanguageStrings(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');
        $options = $element->getOptions();

        $this->assertSame(_STARTSWITH, $options[XOOPS_MATCH_START]);
        $this->assertSame(_ENDSWITH, $options[XOOPS_MATCH_END]);
        $this->assertSame(_MATCHES, $options[XOOPS_MATCH_EQUAL]);
        $this->assertSame(_CONTAINS, $options[XOOPS_MATCH_CONTAIN]);
    }

    /**
     * The constant values must be 0, 1, 2, 3 respectively.
     */
    public function testConstantValuesAreExpected(): void
    {
        $this->assertSame(0, XOOPS_MATCH_START);
        $this->assertSame(1, XOOPS_MATCH_END);
        $this->assertSame(2, XOOPS_MATCH_EQUAL);
        $this->assertSame(3, XOOPS_MATCH_CONTAIN);
    }

    /**
     * The language string values must match expected text.
     */
    public function testLanguageStringValues(): void
    {
        $this->assertSame('Starts with', _STARTSWITH);
        $this->assertSame('Ends with', _ENDSWITH);
        $this->assertSame('Matches', _MATCHES);
        $this->assertSame('Contains', _CONTAINS);
    }

    /**
     * Caption must be correctly set.
     */
    public function testCaptionIsSet(): void
    {
        $element = new \XoopsFormSelectMatchOption('Test Caption', 'match_field');

        $this->assertSame('Test Caption', $element->getCaption());
    }

    /**
     * Name must be correctly set.
     */
    public function testNameIsSet(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'my_match');

        $this->assertSame('my_match', $element->getName());
    }

    /**
     * Default size must be 1 (dropdown).
     */
    public function testDefaultSizeIsOne(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');

        $this->assertSame(1, $element->getSize());
    }

    /**
     * Custom size must be respected.
     */
    public function testCustomSize(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field', null, 4);

        $this->assertSame(4, $element->getSize());
    }

    /**
     * Multiple selection must be disabled.
     */
    public function testMultipleIsFalse(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');

        $this->assertFalse($element->isMultiple());
    }

    /**
     * Pre-selected value must be respected.
     */
    public function testPreSelectedValue(): void
    {
        $element = new \XoopsFormSelectMatchOption(
            'Caption',
            'match_field',
            XOOPS_MATCH_CONTAIN
        );
        $value = $element->getValue();

        $this->assertContains(XOOPS_MATCH_CONTAIN, $value);
    }

    /**
     * No value should be pre-selected when null is passed.
     */
    public function testNullValueMeansNoSelection(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field', null);
        $value = $element->getValue();

        $this->assertIsArray($value);
        $this->assertEmpty($value);
    }

    /**
     * The element must be an instance of XoopsFormSelect.
     */
    public function testInheritsXoopsFormSelect(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');

        $this->assertInstanceOf(\XoopsFormSelect::class, $element);
    }

    /**
     * The element must be an instance of XoopsFormElement.
     */
    public function testInheritsXoopsFormElement(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');

        $this->assertInstanceOf(\XoopsFormElement::class, $element);
    }

    /**
     * Render must return a non-empty string.
     */
    public function testRenderReturnsString(): void
    {
        xoops_load('XoopsFormRenderer');
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');
        $rendered = $element->render();

        $this->assertIsString($rendered);
        $this->assertNotEmpty($rendered);
    }

    /**
     * Data provider: all match option constant/label pairs.
     *
     * @return array<string, array{int, string}>
     */
    public static function matchOptionProvider(): array
    {
        return [
            'XOOPS_MATCH_START'   => [XOOPS_MATCH_START, _STARTSWITH],
            'XOOPS_MATCH_END'     => [XOOPS_MATCH_END, _ENDSWITH],
            'XOOPS_MATCH_EQUAL'   => [XOOPS_MATCH_EQUAL, _MATCHES],
            'XOOPS_MATCH_CONTAIN' => [XOOPS_MATCH_CONTAIN, _CONTAINS],
        ];
    }

    /**
     * Each match option must be present with the correct label.
     */
    #[DataProvider('matchOptionProvider')]
    public function testEachOptionHasCorrectLabel(int $constantValue, string $expectedLabel): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');
        $options = $element->getOptions();

        $this->assertArrayHasKey($constantValue, $options);
        $this->assertSame($expectedLabel, $options[$constantValue]);
    }

    /**
     * Pre-selecting XOOPS_MATCH_START must work.
     */
    public function testPreSelectMatchStart(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field', XOOPS_MATCH_START);
        $value = $element->getValue();

        $this->assertContains(XOOPS_MATCH_START, $value);
    }

    /**
     * Options order must be: START, END, EQUAL, CONTAIN.
     */
    public function testOptionsOrder(): void
    {
        $element = new \XoopsFormSelectMatchOption('Caption', 'match_field');
        $options = $element->getOptions();
        $keys = array_keys($options);

        $expected = [
            XOOPS_MATCH_START,
            XOOPS_MATCH_END,
            XOOPS_MATCH_EQUAL,
            XOOPS_MATCH_CONTAIN,
        ];

        $this->assertSame($expected, $keys, 'Options must be in order: START, END, EQUAL, CONTAIN');
    }
}
