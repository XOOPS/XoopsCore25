<?php

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/bootstrap.php';

xoops_load('XoopsFormElement');
xoops_load('XoopsForm');
xoops_load('XoopsFormText');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormHiddenToken');
xoops_load('XoopsFormRenderer');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');

// XoopsTableForm is not in the autoloader map — load it explicitly
require_once XOOPS_ROOT_PATH . '/class/xoopsform/tableform.php';

/**
 * Tests for XoopsTableForm
 */
#[CoversClass(\XoopsTableForm::class)]
class XoopsTableFormTest extends TestCase
{
    /**
     * @var \XoopsTableForm
     */
    protected $form;

    protected function setUp(): void
    {
        $this->form = new \XoopsTableForm(
            'Table Form Title',
            'tableform',
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
        $this->assertSame('Table Form Title', $this->form->getTitle());
        $this->assertSame('tableform', $this->form->getName(false));
        $this->assertSame('process.php', $this->form->getAction(false));
        $this->assertSame('post', $this->form->getMethod());
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
        $this->assertStringContainsString('name="tableform"', $result);
        $this->assertStringContainsString('id="tableform"', $result);
        $this->assertStringContainsString('action="process.php"', $result);
        $this->assertStringContainsString('method="post"', $result);
    }

    public function testRenderContainsTableStructure(): void
    {
        $text = new \XoopsFormText('Username', 'username', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('<table', $result);
        $this->assertStringContainsString('</table>', $result);
        $this->assertStringContainsString('<tr', $result);
        $this->assertStringContainsString('<td>', $result);
    }

    public function testRenderContainsTitle(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('Table Form Title', $result);
    }

    public function testRenderVisibleElementWithCaption(): void
    {
        $text = new \XoopsFormText('Username', 'username', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('Username', $result);
        // Should have caption in first <td> and element in second <td>
        $this->assertStringContainsString('<td>Username', $result);
    }

    public function testRenderVisibleElementWithDescription(): void
    {
        $text = new \XoopsFormText('Email', 'email', 25, 100);
        $text->setDescription('Enter your email address');
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('Enter your email address', $result);
    }

    public function testRenderHiddenElementsAtEnd(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $hidden = new \XoopsFormHidden('secret', 'value123');

        $this->form->addElement($text);
        $this->form->addElement($hidden);

        $result = $this->form->render();
        // Hidden elements should be after the closing </table> but before </form>
        $tableEndPos = strpos($result, '</table>');
        $hiddenPos = strpos($result, 'type="hidden"');
        $formEndPos = strpos($result, '</form>');

        $this->assertNotFalse($tableEndPos);
        $this->assertNotFalse($hiddenPos);
        $this->assertNotFalse($formEndPos);
        $this->assertGreaterThan($tableEndPos, $hiddenPos, 'Hidden elements should appear after </table>');
        $this->assertLessThan($formEndPos, $hiddenPos, 'Hidden elements should appear before </form>');
    }

    public function testRenderNocolspanElement(): void
    {
        $text = new \XoopsFormText('Editor', 'editor', 25, 100);
        $text->setNocolspan(true);
        $this->form->addElement($text);

        $result = $this->form->render();
        $this->assertStringContainsString('colspan="2"', $result);
    }

    public function testRenderNormalElementNoColspan(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $result = $this->form->render();
        // Normal element has two separate <td> cells, so check for the caption in a <td>
        $this->assertStringContainsString('<td>Name', $result);
    }

    public function testRenderEmptyForm(): void
    {
        $result = $this->form->render();
        $this->assertStringContainsString('<form', $result);
        $this->assertStringContainsString('<table', $result);
        $this->assertStringContainsString('</table>', $result);
        $this->assertStringContainsString('</form>', $result);
    }

    public function testRenderWithMultipleVisibleAndHiddenElements(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100);
        $text2 = new \XoopsFormText('Second', 'second', 25, 100);
        $hidden1 = new \XoopsFormHidden('h1', 'v1');
        $hidden2 = new \XoopsFormHidden('h2', 'v2');

        $this->form->addElement($text1);
        $this->form->addElement($hidden1);
        $this->form->addElement($text2);
        $this->form->addElement($hidden2);

        $result = $this->form->render();
        $this->assertStringContainsString('First', $result);
        $this->assertStringContainsString('Second', $result);

        // Count hidden inputs
        $hiddenCount = substr_count($result, 'type="hidden"');
        $this->assertSame(2, $hiddenCount);
    }

    public function testRenderWithExtraAttributes(): void
    {
        $this->form->setExtra('enctype="multipart/form-data"');
        $result = $this->form->render();
        $this->assertStringContainsString('enctype="multipart/form-data"', $result);
    }

    public function testRenderWithGetMethod(): void
    {
        $form = new \XoopsTableForm('Search', 'searchform', 'search.php', 'get', false);
        $text = new \XoopsFormText('Query', 'q', 40, 255);
        $form->addElement($text);

        $result = $form->render();
        $this->assertStringContainsString('method="get"', $result);
    }

    // ------------------------------------------------------------------
    //  Integration: display() inherited from XoopsForm
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

    // ------------------------------------------------------------------
    //  Integration: with token
    // ------------------------------------------------------------------

    public function testRenderWithTokenEnabled(): void
    {
        $form = new \XoopsTableForm('Title', 'tokenform', 'action.php', 'post', true);
        $result = $form->render();
        $this->assertStringContainsString('XOOPS_TOKEN_REQUEST', $result);
    }
}
