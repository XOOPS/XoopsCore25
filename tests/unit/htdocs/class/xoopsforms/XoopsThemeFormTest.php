<?php

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/bootstrap.php';

// Define _REQUIRED if not already defined (needed by legacy renderer)
if (!defined('_REQUIRED')) {
    define('_REQUIRED', 'Required');
}

xoops_load('XoopsFormElement');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsForm');
xoops_load('XoopsThemeForm');
xoops_load('XoopsFormText');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormHiddenToken');
xoops_load('XoopsFormRenderer');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');

/**
 * Tests for XoopsThemeForm
 */
#[CoversClass(\XoopsThemeForm::class)]
class XoopsThemeFormTest extends TestCase
{
    /**
     * @var \XoopsThemeForm
     */
    protected $form;

    protected function setUp(): void
    {
        $this->form = new \XoopsThemeForm(
            'Theme Form Title',
            'themeform',
            'process.php',
            'post',
            false
        );
    }

    // ------------------------------------------------------------------
    //  Inheritance
    // ------------------------------------------------------------------

    public function testExtendsXoopsForm(): void
    {
        $this->assertInstanceOf(\XoopsForm::class, $this->form);
    }

    // ------------------------------------------------------------------
    //  Constructor
    // ------------------------------------------------------------------

    public function testConstructorSetsProperties(): void
    {
        $this->assertSame('Theme Form Title', $this->form->getTitle());
        $this->assertSame('themeform', $this->form->getName(false));
        $this->assertSame('process.php', $this->form->getAction(false));
        $this->assertSame('post', $this->form->getMethod());
    }

    public function testConstructorWithAllParams(): void
    {
        $form = new \XoopsThemeForm('Title', 'myform', 'action.php', 'get', false, 'Form summary');
        $this->assertSame('Title', $form->getTitle());
        $this->assertSame('get', $form->getMethod());
        $this->assertSame('Form summary', $form->getSummary());
    }

    // ------------------------------------------------------------------
    //  render()
    // ------------------------------------------------------------------

    public function testRenderReturnsString(): void
    {
        $result = $this->form->render();
        $this->assertIsString($result);
    }

    public function testRenderContainsFormTag(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('<form', $result);
        $this->assertStringContainsString('</form>', $result);
    }

    public function testRenderContainsFormAttributes(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('name="themeform"', $result);
        $this->assertStringContainsString('id="themeform"', $result);
        $this->assertStringContainsString('action="process.php"', $result);
        $this->assertStringContainsString('method="post"', $result);
    }

    public function testRenderContainsTableStructure(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('<table', $result);
        $this->assertStringContainsString('</table>', $result);
    }

    public function testRenderContainsTitle(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('Theme Form Title', $result);
    }

    public function testRenderContainsOnsubmitValidation(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('onsubmit="return xoopsFormValidate_themeform();"', $result);
    }

    public function testRenderContainsValidationJS(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('xoopsFormValidate_themeform', $result);
    }

    public function testRenderVisibleElementWithCaption(): void
    {
        $text = new \XoopsFormText('Username', 'username', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('Username', $result);
        $this->assertStringContainsString('caption-text', $result);
    }

    public function testRenderVisibleElementWithDescription(): void
    {
        $text = new \XoopsFormText('Email', 'email', 25, 100);
        $text->setDescription('Your email address');
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('Your email address', $result);
        $this->assertStringContainsString('xoops-form-element-help', $result);
    }

    public function testRenderRequiredElementHasRequiredClass(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text, true);

        $result = $this->form->render();
        $this->assertStringContainsString('xoops-form-element-caption-required', $result);
    }

    public function testRenderNonRequiredElementHasNormalCaptionClass(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text, false);

        $result = $this->form->render();
        $this->assertStringContainsString('xoops-form-element-caption', $result);
    }

    public function testRenderHiddenElementsAfterTable(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $hidden = new \XoopsFormHidden('secret', 'val');
        $this->form->addElement($text);
        $this->form->addElement($hidden);

        $result = $this->form->render();
        $tableEndPos = strpos($result, '</table>');
        $hiddenPos = strpos($result, 'type="hidden"');
        $this->assertGreaterThan($tableEndPos, $hiddenPos, 'Hidden elements should appear after </table>');
    }

    public function testRenderWithRequiredFooter(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text, true);

        $result = $this->form->render();
        $this->assertStringContainsString('* = Required', $result);
    }

    public function testRenderWithoutRequiredNoFooter(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text, false);

        $result = $this->form->render();
        $this->assertStringNotContainsString('* = Required', $result);
    }

    public function testRenderNocolspanElement(): void
    {
        $text = new \XoopsFormText('Editor', 'editor', 25, 100);
        $text->setNocolspan(true);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('colspan="2"', $result);
    }

    public function testRenderStringElement(): void
    {
        $this->form->addElement('<tr><td colspan="2">Custom break</td></tr>');

        $result = $this->form->render();
        $this->assertStringContainsString('Custom break', $result);
    }

    // ------------------------------------------------------------------
    //  insertBreak()
    // ------------------------------------------------------------------

    public function testInsertBreakAddsStringElement(): void
    {
        $this->form->insertBreak('Separator');

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        // insertBreak adds a string element via the renderer
        $this->assertIsString($elements[0]);
    }

    public function testInsertBreakEmptyExtraAddsNbsp(): void
    {
        $this->form->insertBreak('');

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        $this->assertIsString($elements[0]);
        $this->assertStringContainsString('&nbsp;', $elements[0]);
    }

    public function testInsertBreakWithClass(): void
    {
        $this->form->insertBreak('Break here', 'separator-class');

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        $this->assertIsString($elements[0]);
        $this->assertStringContainsString('separator-class', $elements[0]);
    }

    public function testInsertBreakWithHtmlContent(): void
    {
        $this->form->insertBreak('<hr>');

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        $this->assertIsString($elements[0]);
        $this->assertStringContainsString('<hr>', $elements[0]);
    }

    public function testInsertBreakMultipleTimes(): void
    {
        $this->form->insertBreak('First break');
        $this->form->insertBreak('Second break');

        $elements = $this->form->getElements();
        $this->assertCount(2, $elements);
    }

    public function testInsertBreakRenderedInOutput(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);
        $this->form->insertBreak('Divider content');

        $result = $this->form->render();
        $this->assertStringContainsString('Divider content', $result);
    }

    // ------------------------------------------------------------------
    //  Integration
    // ------------------------------------------------------------------

    public function testRenderWithTokenEnabled(): void
    {
        $form = new \XoopsThemeForm('Title', 'tokenform', 'action.php', 'post', true);
        $result = $form->render();
        $this->assertStringContainsString('XOOPS_TOKEN_REQUEST', $result);
    }

    public function testRenderEmptyForm(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('<form', $result);
        $this->assertStringContainsString('</form>', $result);
        $this->assertStringContainsString('<table', $result);
    }

    public function testRenderWithExtraAttributes(): void
    {
        $this->form->setExtra('enctype="multipart/form-data"');
        $result = $this->form->render();
        $this->assertStringContainsString('enctype="multipart/form-data"', $result);
    }

    public function testRenderWithGetMethod(): void
    {
        $form = new \XoopsThemeForm('Search', 'searchform', 'search.php', 'get', false);
        $result = $form->render();
        $this->assertStringContainsString('method="get"', $result);
    }

    public function testDisplayOutputsRender(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        ob_start();
        $this->form->display();
        $output = ob_get_clean();

        $this->assertSame($this->form->render(), $output);
    }

    public function testRenderMultipleElementTypes(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $hidden = new \XoopsFormHidden('id', '5');
        $this->form->addElement($text, true);
        $this->form->insertBreak('---');
        $this->form->addElement($hidden);

        $result = $this->form->render();
        $this->assertStringContainsString('Name', $result);
        $this->assertStringContainsString('---', $result);
        $this->assertStringContainsString('type="hidden"', $result);
    }

    /**
     * Data provider for insertBreak class sanitization
     *
     * @return array<string, array{string, string, bool}>
     */
    public static function insertBreakClassProvider(): array
    {
        return [
            'simple class'     => ['Break', 'my-class', true],
            'empty class'      => ['Break', '', false],
            'class with space' => ['Break', 'class one', true],
        ];
    }

    #[DataProvider('insertBreakClassProvider')]
    public function testInsertBreakClassHandling(string $extra, string $class, bool $expectClass): void
    {
        $this->form->insertBreak($extra, $class);

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        $this->assertIsString($elements[0]);

        if ($expectClass) {
            $this->assertStringContainsString("class=", $elements[0]);
        }
    }
}
