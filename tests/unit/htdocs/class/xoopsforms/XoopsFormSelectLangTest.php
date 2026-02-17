<?php
namespace xoopsforms;

use PHPUnit\Framework\TestCase;

/**
 * Tests for XoopsFormSelectLang.
 *
 * Source: class/xoopsform/formselectlang.php
 */
class XoopsFormSelectLangTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormElement');
        xoops_load('XoopsFormSelect');
        xoops_load('XoopsLists');
        xoops_load('XoopsFormSelectLang');
    }

    /**
     * Constructor must add language options.
     */
    public function testConstructorAddsLanguageOptions(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options, 'Language list should not be empty');
    }

    /**
     * The 'english' language directory exists, so it must be in the list.
     */
    public function testEnglishIsPresent(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        $this->assertArrayHasKey(
            'english',
            $options,
            'The "english" language must be present since the directory exists'
        );
    }

    /**
     * The 'english' key must map to the value 'english'.
     * (XoopsLists::getDirListAsArray returns key=value pairs for directories.)
     */
    public function testEnglishValueEqualsKey(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        $this->assertSame('english', $options['english']);
    }

    /**
     * Caption must be correctly set.
     */
    public function testCaptionIsSet(): void
    {
        $element = new \XoopsFormSelectLang('My Language', 'lang_field');

        $this->assertSame('My Language', $element->getCaption());
    }

    /**
     * Name must be correctly set.
     */
    public function testNameIsSet(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'my_lang');

        $this->assertSame('my_lang', $element->getName());
    }

    /**
     * Default size must be 1 (dropdown).
     */
    public function testDefaultSizeIsOne(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');

        $this->assertSame(1, $element->getSize());
    }

    /**
     * Custom size must be respected.
     */
    public function testCustomSize(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field', null, 5);

        $this->assertSame(5, $element->getSize());
    }

    /**
     * Pre-selected value must be respected.
     */
    public function testPreSelectedValue(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field', 'english');
        $value = $element->getValue();

        $this->assertContains('english', $value);
    }

    /**
     * Null value means no selection.
     */
    public function testNullValueMeansNoSelection(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field', null);
        $value = $element->getValue();

        $this->assertIsArray($value);
        $this->assertEmpty($value);
    }

    /**
     * The element must be an instance of XoopsFormSelect.
     */
    public function testInheritsXoopsFormSelect(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');

        $this->assertInstanceOf(\XoopsFormSelect::class, $element);
    }

    /**
     * The element must be an instance of XoopsFormElement.
     */
    public function testInheritsXoopsFormElement(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');

        $this->assertInstanceOf(\XoopsFormElement::class, $element);
    }

    /**
     * All language options must have string keys and string values.
     */
    public function testAllOptionsAreStrings(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        foreach ($options as $key => $value) {
            $this->assertIsString(
                $key,
                'Language option key must be a string'
            );
            $this->assertIsString(
                $value,
                sprintf('Language option value for key "%s" must be a string', $key)
            );
        }
    }

    /**
     * Language options should not contain dot-prefixed directories.
     */
    public function testNoDotPrefixedDirectories(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        foreach (array_keys($options) as $key) {
            $this->assertNotSame(
                '.',
                substr((string) $key, 0, 1),
                sprintf('Language option "%s" should not start with a dot', $key)
            );
        }
    }

    /**
     * Language options should not contain CVS or _darcs directories.
     */
    public function testNoIgnoredDirectories(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        $this->assertArrayNotHasKey('cvs', $options, 'cvs directory should be filtered out');
        $this->assertArrayNotHasKey('CVS', $options, 'CVS directory should be filtered out');
        $this->assertArrayNotHasKey('_darcs', $options, '_darcs directory should be filtered out');
    }

    /**
     * Render must return a non-empty string.
     */
    public function testRenderReturnsString(): void
    {
        xoops_load('XoopsFormRenderer');
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $rendered = $element->render();

        $this->assertIsString($rendered);
        $this->assertNotEmpty($rendered);
    }

    /**
     * Each language key should equal its value (getDirListAsArray returns $file => $file).
     */
    public function testKeysEqualValues(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        foreach ($options as $key => $value) {
            $this->assertSame(
                (string) $key,
                $value,
                sprintf('Language key "%s" must equal its value "%s"', $key, $value)
            );
        }
    }

    /**
     * At least one language option must exist.
     */
    public function testAtLeastOneLanguage(): void
    {
        $element = new \XoopsFormSelectLang('Language', 'lang_field');
        $options = $element->getOptions();

        $this->assertGreaterThanOrEqual(1, count($options));
    }
}
