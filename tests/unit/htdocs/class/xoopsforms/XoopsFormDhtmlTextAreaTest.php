<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormDhtmlTextArea;

xoops_load('XoopsFormDhtmlTextArea');
xoops_load('XoopsFormTextArea');
xoops_load('XoopsFormElement');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormDhtmlTextArea.
 *
 * XoopsFormDhtmlTextArea extends XoopsFormTextArea and adds DHTML/editor support.
 * These tests verify property initialization, inherited textarea methods,
 * and rendering behaviour when no HTML editor is configured.
 */
class XoopsFormDhtmlTextAreaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Reset state between tests.
     *
     * The constructor uses a static $inLoop counter to prevent recursion.
     * We need to ensure it starts at 0 for each test.
     * Also set a mock renderer to avoid deep textsanitizer plugin dependencies.
     */
    protected function setUp(): void
    {
        // Clear any global xoopsConfig editor setting that might interfere
        unset($GLOBALS['xoopsConfig']['editor']);

        // Set a mock renderer to avoid textsanitizer plugin chain in render tests
        $mockRenderer = new class implements \XoopsFormRendererInterface {
            public function renderFormDhtmlTextArea(\XoopsFormDhtmlTextArea $element)
            {
                return '<textarea name="' . $element->getName() . '">' . $element->getValue(true) . '</textarea>';
            }
            public function __call($name, $args) { return ''; }
            public function renderFormButton(\XoopsFormButton $element) { return ''; }
            public function renderFormButtonTray(\XoopsFormButtonTray $element) { return ''; }
            public function renderFormCheckBox(\XoopsFormCheckBox $element) { return ''; }
            public function renderFormColorPicker(\XoopsFormColorPicker $element) { return ''; }
            public function renderFormElementTray(\XoopsFormElementTray $element) { return ''; }
            public function renderFormFile(\XoopsFormFile $element) { return ''; }
            public function renderFormHidden(\XoopsFormHidden $element) { return ''; }
            public function renderFormHiddenToken(\XoopsFormHiddenToken $element) { return ''; }
            public function renderFormLabel(\XoopsFormLabel $element) { return ''; }
            public function renderFormPassword(\XoopsFormPassword $element) { return ''; }
            public function renderFormRadio(\XoopsFormRadio $element) { return ''; }
            public function renderFormSelect(\XoopsFormSelect $element) { return ''; }
            public function renderFormText(\XoopsFormText $element) { return ''; }
            public function renderFormTextArea(\XoopsFormTextArea $element) { return ''; }
            public function renderFormTextDateSelect(\XoopsFormTextDateSelect $element) { return ''; }
            public function addThemeFormBreak(\XoopsThemeForm $form, $extra, $class) {}
            public function renderThemeForm(\XoopsThemeForm $form) { return ''; }
            public function renderSimpleForm(\XoopsSimpleForm $form) { return ''; }
        };
        \XoopsFormRenderer::getInstance()->set($mockRenderer);
    }

    /**
     * Restore the default renderer after each test.
     */
    protected function tearDown(): void
    {
        // Reset the singleton's renderer to null via reflection so it reverts to default
        $instance = \XoopsFormRenderer::getInstance();
        $ref = new \ReflectionClass($instance);
        $prop = $ref->getProperty('renderer');
        $prop->setAccessible(true);
        $prop->setValue($instance, null);
    }

    // =========================================================================
    // Constructor — property initialization
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormDhtmlTextArea('My Caption', 'field_name', 'initial value');

        $this->assertSame('My Caption', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'my_field');

        $this->assertSame('my_field', $element->getName(false));
    }

    public function testConstructorSetsValue(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'some text');

        $this->assertSame('some text', $element->getValue());
    }

    public function testConstructorSetsDefaultValueToEmptyString(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertSame('', $element->getValue());
    }

    public function testConstructorSetsRows(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 10);

        $this->assertSame(10, $element->getRows());
    }

    public function testConstructorSetsDefaultRows(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertSame(5, $element->getRows());
    }

    public function testConstructorSetsCols(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 5, 80);

        $this->assertSame(80, $element->getCols());
    }

    public function testConstructorSetsDefaultCols(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertSame(50, $element->getCols());
    }

    public function testConstructorSetsHiddenText(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 5, 50, 'myHiddenText');

        $this->assertSame('myHiddenText', $element->_hiddenText);
    }

    public function testConstructorSetsDefaultHiddenText(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertSame('xoopsHiddenText', $element->_hiddenText);
    }

    public function testConstructorWithAllParameters(): void
    {
        $element = new XoopsFormDhtmlTextArea(
            'Full Caption',
            'full_name',
            'Full Value',
            8,
            60,
            'fullHiddenText',
            []
        );

        $this->assertSame('Full Caption', $element->getCaption());
        $this->assertSame('full_name', $element->getName(false));
        $this->assertSame('Full Value', $element->getValue());
        $this->assertSame(8, $element->getRows());
        $this->assertSame(60, $element->getCols());
        $this->assertSame('fullHiddenText', $element->_hiddenText);
    }

    // =========================================================================
    // Default property values
    // =========================================================================

    public function testSkipPreviewDefaultFalse(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertFalse($element->skipPreview);
    }

    public function testDoHtmlDefaultFalse(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertFalse($element->doHtml);
    }

    public function testJsDefaultEmptyString(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertSame('', $element->js);
    }

    public function testHtmlEditorDefaultEmpty(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        // When no editor is configured, htmlEditor stays as empty array or is not an object
        $this->assertTrue(
            empty($element->htmlEditor) || !is_object($element->htmlEditor),
            'htmlEditor should be empty or not an object when no editor is configured'
        );
    }

    // =========================================================================
    // Inherited textarea getters — getRows, getCols
    // =========================================================================

    public function testGetRowsReturnsInteger(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 7);

        $this->assertIsInt($element->getRows());
        $this->assertSame(7, $element->getRows());
    }

    public function testGetColsReturnsInteger(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 5, 75);

        $this->assertIsInt($element->getCols());
        $this->assertSame(75, $element->getCols());
    }

    // =========================================================================
    // Inherited textarea getValue / setValue
    // =========================================================================

    public function testGetValueReturnsRawByDefault(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '<b>bold</b>');

        $this->assertSame('<b>bold</b>', $element->getValue());
    }

    public function testGetValueEncodedReturnsHtmlSpecialChars(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '<script>alert("xss")</script>');

        $encoded = $element->getValue(true);

        $this->assertStringNotContainsString('<script>', $encoded);
        $this->assertStringContainsString('&lt;script&gt;', $encoded);
    }

    public function testSetValueChangesValue(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'original');
        $element->setValue('updated');

        $this->assertSame('updated', $element->getValue());
    }

    public function testSetValueToEmptyString(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'something');
        $element->setValue('');

        $this->assertSame('', $element->getValue());
    }

    // =========================================================================
    // render — no HTML editor configured
    // =========================================================================

    public function testRenderReturnsStringWhenNoEditor(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderContainsElementNameWhenNoEditor(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'testfield', 'value');

        $result = $element->render();

        $this->assertIsString($result);
        // The rendered output should reference the element name
        $this->assertStringContainsString('testfield', $result);
    }

    // =========================================================================
    // render — with mock HTML editor (object with isEnabled)
    // =========================================================================

    public function testRenderDelegatesToEditorWhenEditorIsEnabledObject(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        $mockEditor = new class {
            public $isEnabled = true;
            public function render(): string
            {
                return '<div class="mock-editor">Editor Output</div>';
            }
        };
        $element->htmlEditor = $mockEditor;

        $result = $element->render();

        $this->assertSame('<div class="mock-editor">Editor Output</div>', $result);
    }

    public function testRenderFallsBackWhenEditorIsDisabled(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        $mockEditor = new class {
            public $isEnabled = false;
            public function render(): string
            {
                return 'should not see this';
            }
        };
        $element->htmlEditor = $mockEditor;

        $result = $element->render();

        // Should NOT use the editor's render when isEnabled is false
        $this->assertNotSame('should not see this', $result);
        $this->assertIsString($result);
    }

    public function testRenderDelegatesToEditorWhenIsEnabledNotSet(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        // Editor object without isEnabled property — should still render via editor
        $mockEditor = new class {
            public function render(): string
            {
                return '<div>editor-without-isEnabled</div>';
            }
        };
        $element->htmlEditor = $mockEditor;

        $result = $element->render();

        $this->assertSame('<div>editor-without-isEnabled</div>', $result);
    }

    // =========================================================================
    // renderValidationJS — no HTML editor
    // =========================================================================

    public function testRenderValidationJSCallsParentWhenNoEditor(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');
        $element->htmlEditor = [];

        // Parent renderValidationJS returns false when not required and no custom code
        $result = $element->renderValidationJS();

        $this->assertFalse($result);
    }

    public function testRenderValidationJSRequiredReturnsJSWhenNoEditor(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'myfield', 'value');
        $element->htmlEditor = [];
        $element->_required = true;
        $element->setCaption('My Field');

        ob_start();
        $result = $element->renderValidationJS();
        ob_end_clean();

        $this->assertIsString($result);
        $this->assertStringContainsString('myfield', $result);
        $this->assertStringContainsString('window.alert', $result);
    }

    // =========================================================================
    // renderValidationJS — with mock HTML editor
    // =========================================================================

    public function testRenderValidationJSDelegatesToEditorWhenEnabled(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        $mockEditor = new class {
            public $isEnabled = true;
            public function renderValidationJS(): string
            {
                return 'editorValidation();';
            }
        };
        $element->htmlEditor = $mockEditor;

        $result = $element->renderValidationJS();

        $this->assertSame('editorValidation();', $result);
    }

    public function testRenderValidationJSFallsBackWhenEditorDisabled(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        $mockEditor = new class {
            public $isEnabled = false;
            public function renderValidationJS(): string
            {
                return 'should not see this';
            }
        };
        $element->htmlEditor = $mockEditor;

        // Falls back to parent because isEnabled is false
        $result = $element->renderValidationJS();

        $this->assertNotSame('should not see this', $result);
    }

    public function testRenderValidationJSFallsBackWhenEditorHasNoMethod(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', 'value');

        // Editor without renderValidationJS method
        $mockEditor = new class {
            public $isEnabled = true;
        };
        $element->htmlEditor = $mockEditor;

        // Falls back to parent because method_exists check fails
        $result = $element->renderValidationJS();

        $this->assertFalse($result);
    }

    // =========================================================================
    // skipPreview / doHtml — modifiable properties
    // =========================================================================

    public function testSkipPreviewCanBeSetToTrue(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');
        $element->skipPreview = true;

        $this->assertTrue($element->skipPreview);
    }

    public function testDoHtmlCanBeSetToTrue(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');
        $element->doHtml = true;

        $this->assertTrue($element->doHtml);
    }

    public function testJsCanBeSet(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');
        $element->js = 'console.log("test");';

        $this->assertSame('console.log("test");', $element->js);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testConstructorWithEmptyCaption(): void
    {
        $element = new XoopsFormDhtmlTextArea('', 'name');

        $this->assertSame('', $element->getCaption());
    }

    public function testConstructorWithEmptyName(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', '');

        $this->assertSame('', $element->getName(false));
    }

    public function testConstructorWithHtmlInValue(): void
    {
        $html = '<p>Hello <strong>World</strong></p>';
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', $html);

        $this->assertSame($html, $element->getValue());
    }

    public function testConstructorWithLargeRowsAndCols(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 100, 200);

        $this->assertSame(100, $element->getRows());
        $this->assertSame(200, $element->getCols());
    }

    public function testConstructorWithZeroRowsAndCols(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 0, 0);

        $this->assertSame(0, $element->getRows());
        $this->assertSame(0, $element->getCols());
    }

    public function testIsNotAContainer(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertFalse($element->isContainer());
    }

    public function testValueWithSpecialCharacters(): void
    {
        $value = "Line1\nLine2\tTabbed & <special> \"quotes\"";
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', $value);

        $this->assertSame($value, $element->getValue());
    }

    public function testValueWithUnicodeCharacters(): void
    {
        $value = "\xc3\xa9\xc3\xa0\xc3\xbc \xe4\xb8\xad\xe6\x96\x87 \xf0\x9f\x98\x80";
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', $value);

        $this->assertSame($value, $element->getValue());
    }

    /**
     * @param string $caption
     * @param string $name
     * @param string $value
     * @param int    $rows
     * @param int    $cols
     * @param string $hiddentext
     */
    #[DataProvider('constructorDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $name,
        string $value,
        int $rows,
        int $cols,
        string $hiddentext
    ): void {
        $element = new XoopsFormDhtmlTextArea($caption, $name, $value, $rows, $cols, $hiddentext);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($name, $element->getName(false));
        $this->assertSame($value, $element->getValue());
        $this->assertSame($rows, $element->getRows());
        $this->assertSame($cols, $element->getCols());
        $this->assertSame($hiddentext, $element->_hiddenText);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: int, 4: int, 5: string}>
     */
    public static function constructorDataProvider(): array
    {
        return [
            'defaults'           => ['Cap', 'name', '', 5, 50, 'xoopsHiddenText'],
            'custom rows cols'   => ['Caption', 'field', 'val', 10, 80, 'hidden1'],
            'minimal'            => ['', '', '', 1, 1, ''],
            'large values'       => ['A very long caption text', 'a_long_field_name', 'A very long value text', 99, 999, 'longHidden'],
            'special chars'      => ['Cap & <test>', 'field_name', '<b>value</b>', 5, 50, 'hid"den'],
        ];
    }

    /**
     * @param string $hiddentext
     * @param string $expected
     */
    #[DataProvider('hiddenTextProvider')]
    public function testHiddenTextValues(string $hiddentext, string $expected): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name', '', 5, 50, $hiddentext);

        $this->assertSame($expected, $element->_hiddenText);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function hiddenTextProvider(): array
    {
        return [
            'default text'     => ['xoopsHiddenText', 'xoopsHiddenText'],
            'custom text'      => ['customHidden', 'customHidden'],
            'empty string'     => ['', ''],
            'special chars'    => ['hidden<tag>', 'hidden<tag>'],
            'numeric string'   => ['12345', '12345'],
        ];
    }

    // =========================================================================
    // Inheritance verification
    // =========================================================================

    public function testExtendsXoopsFormTextArea(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertInstanceOf(\XoopsFormTextArea::class, $element);
    }

    public function testExtendsXoopsFormElement(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'name');

        $this->assertInstanceOf(\XoopsFormElement::class, $element);
    }

    public function testSetCaptionInherited(): void
    {
        $element = new XoopsFormDhtmlTextArea('Original', 'name');
        $element->setCaption('Updated');

        $this->assertSame('Updated', $element->getCaption());
    }

    public function testSetNameInherited(): void
    {
        $element = new XoopsFormDhtmlTextArea('Caption', 'original');
        $element->setName('updated');

        $this->assertSame('updated', $element->getName(false));
    }
}
