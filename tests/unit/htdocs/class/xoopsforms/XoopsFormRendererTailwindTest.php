<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\TestCase;
use XoopsFormButton;
use XoopsFormElementTray;
use XoopsFormLabel;
use XoopsFormRendererTailwind;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormButton');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsFormLabel');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererTailwind');

/**
 * Unit tests for XoopsFormRendererTailwind.
 *
 * Focus areas:
 *   - renderer can be instantiated
 *   - HTML attributes are escaped (XSS defense)
 *   - renderFormLabel produces a properly closed label element
 *   - renderFormElementTray picks the correct container class for orientation
 */
class XoopsFormRendererTailwindTest extends TestCase
{
    private XoopsFormRendererTailwind $renderer;

    protected function setUp(): void
    {
        $this->renderer = new XoopsFormRendererTailwind();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(XoopsFormRendererTailwind::class, $this->renderer);
    }

    public function testRenderFormButtonEscapesValue(): void
    {
        $element = new XoopsFormButton('Caption', 'btn', '<script>alert(1)</script>');
        $html    = $this->renderer->renderFormButton($element);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('class="btn btn-neutral"', $html);
    }

    public function testRenderFormButtonEscapesName(): void
    {
        $element = new XoopsFormButton('Caption', '"><img src=x>', 'Click');
        $html    = $this->renderer->renderFormButton($element);

        $this->assertStringNotContainsString('<img src=x>', $html);
        $this->assertStringContainsString('&quot;', $html);
    }

    public function testRenderFormLabelHasClosingTag(): void
    {
        $element = new XoopsFormLabel('Caption', 'Label text', 'labelname');
        $html    = $this->renderer->renderFormLabel($element);

        $this->assertStringContainsString('<label', $html);
        $this->assertStringContainsString('</label>', $html);
        $this->assertStringContainsString('Label text', $html);
    }

    public function testRenderFormLabelEscapesValue(): void
    {
        $element = new XoopsFormLabel('Caption', '<b>bold</b>', 'name');
        $html    = $this->renderer->renderFormLabel($element);

        $this->assertStringNotContainsString('<b>bold</b>', $html);
        $this->assertStringContainsString('&lt;b&gt;', $html);
    }

    public function testRenderFormElementTrayVerticalUsesSpaceY(): void
    {
        $tray = new XoopsFormElementTray('Tray');
        $tray->setOrientation(XoopsFormElementTray::ORIENTATION_VERTICAL);
        $tray->addElement(new XoopsFormButton('A', 'a', 'A'));
        $tray->addElement(new XoopsFormButton('B', 'b', 'B'));

        $html = $this->renderer->renderFormElementTray($tray);

        $this->assertStringContainsString('space-y-2', $html);
        $this->assertStringNotContainsString('inline-flex', $html);
    }

    public function testRenderFormElementTrayHorizontalUsesFlexWrap(): void
    {
        $tray = new XoopsFormElementTray('Tray');
        $tray->setOrientation(XoopsFormElementTray::ORIENTATION_HORIZONTAL);
        $tray->addElement(new XoopsFormButton('A', 'a', 'A'));
        $tray->addElement(new XoopsFormButton('B', 'b', 'B'));

        $html = $this->renderer->renderFormElementTray($tray);

        $this->assertStringContainsString('flex flex-wrap', $html);
        $this->assertStringContainsString('inline-flex', $html);
    }
}
