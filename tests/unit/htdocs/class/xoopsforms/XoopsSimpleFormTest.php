<?php

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/bootstrap.php';

xoops_load('XoopsFormElement');
xoops_load('XoopsForm');
xoops_load('XoopsSimpleForm');
xoops_load('XoopsFormText');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormHiddenToken');
xoops_load('XoopsFormRenderer');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');

/**
 * Tests for XoopsSimpleForm
 */
#[CoversClass(\XoopsSimpleForm::class)]
class XoopsSimpleFormTest extends TestCase
{
    /**
     * @var \XoopsSimpleForm
     */
    protected $form;

    protected function setUp(): void
    {
        $this->form = new \XoopsSimpleForm(
            'Simple Form Title',
            'simpleform',
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
        $this->assertSame('Simple Form Title', $this->form->getTitle());
        $this->assertSame('simpleform', $this->form->getName(false));
        $this->assertSame('process.php', $this->form->getAction(false));
        $this->assertSame('post', $this->form->getMethod());
    }

    public function testConstructorWithGetMethod(): void
    {
        $form = new \XoopsSimpleForm('Title', 'f', 'search.php', 'get', false);
        $this->assertSame('get', $form->getMethod());
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

    public function testRenderContainsFormName(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString("name='simpleform'", $result);
        $this->assertStringContainsString("id='simpleform'", $result);
    }

    public function testRenderContainsAction(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString("action='process.php'", $result);
    }

    public function testRenderContainsMethod(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString("method='post'", $result);
    }

    public function testRenderContainsTitle(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('Simple Form Title', $result);
    }

    public function testRenderWithVisibleElement(): void
    {
        $text = new \XoopsFormText('Username', 'username', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        // Non-hidden elements get bold caption
        $this->assertStringContainsString('<strong>Username</strong>', $result);
    }

    public function testRenderWithHiddenElement(): void
    {
        $hidden = new \XoopsFormHidden('secret', 'value123');
        $this->form->addElement($hidden);

        $result = $this->form->render();
        // Hidden elements should render but without bold caption
        $this->assertStringContainsString('type="hidden"', $result);
        $this->assertStringContainsString('secret', $result);
        $this->assertStringNotContainsString('<strong></strong>', $result);
    }

    public function testRenderHiddenElementWithoutCaption(): void
    {
        $hidden = new \XoopsFormHidden('token', 'abc');
        $this->form->addElement($hidden);

        $result = $this->form->render();
        // Hidden elements do not get the <strong> wrapper around their caption
        // They are rendered inline without the caption markup
        $this->assertStringContainsString('input type="hidden"', $result);
    }

    public function testRenderMultipleElements(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100);
        $text2 = new \XoopsFormText('Second', 'second', 25, 100);
        $hidden = new \XoopsFormHidden('hid', 'val');

        $this->form->addElement($text1);
        $this->form->addElement($text2);
        $this->form->addElement($hidden);

        $result = $this->form->render();
        $this->assertStringContainsString('<strong>First</strong>', $result);
        $this->assertStringContainsString('<strong>Second</strong>', $result);
        $this->assertStringContainsString('type="hidden"', $result);
    }

    public function testRenderFormClosesCorrectly(): void
    {
        $result = $this->form->render();
        $this->assertStringEndsWith("</form>\n", $result);
    }

    public function testRenderEmptyForm(): void
    {
        $result = $this->form->render();
        // Even with no elements, should still have the opening/closing form tags
        $this->assertStringContainsString('<form', $result);
        $this->assertStringContainsString('</form>', $result);
    }

    public function testRenderWithExtraAttributes(): void
    {
        $this->form->setExtra('enctype="multipart/form-data"');
        $text = new \XoopsFormText('File', 'file', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('enctype="multipart/form-data"', $result);
    }

    public function testRenderWithGetMethod(): void
    {
        $form = new \XoopsSimpleForm('Search', 'searchform', 'search.php', 'get', false);
        $text = new \XoopsFormText('Query', 'q', 40, 255);
        $form->addElement($text);

        $result = $form->render();
        $this->assertStringContainsString("method='get'", $result);
    }

    // ------------------------------------------------------------------
    //  Integration with token
    // ------------------------------------------------------------------

    public function testRenderWithTokenEnabled(): void
    {
        $form = new \XoopsSimpleForm('Title', 'tokenform', 'action.php', 'post', true);

        $result = $form->render();
        // Token element should be rendered as a hidden input
        $this->assertStringContainsString('XOOPS_TOKEN_REQUEST', $result);
    }

    // ------------------------------------------------------------------
    //  display() inherited from XoopsForm
    // ------------------------------------------------------------------

    public function testDisplayOutputsRender(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        ob_start();
        $this->form->display();
        $output = ob_get_clean();

        $this->assertSame($this->form->render(), $output);
    }
}
