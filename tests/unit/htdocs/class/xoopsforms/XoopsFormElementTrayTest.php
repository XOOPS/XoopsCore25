<?php

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/bootstrap.php';

xoops_load('XoopsFormElement');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsFormText');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormRenderer');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');

/**
 * Tests for XoopsFormElementTray
 */
#[CoversClass(\XoopsFormElementTray::class)]
class XoopsFormElementTrayTest extends TestCase
{
    /**
     * @var \XoopsFormElementTray
     */
    protected $tray;

    protected function setUp(): void
    {
        $this->tray = new \XoopsFormElementTray('Test Caption');
    }

    // ------------------------------------------------------------------
    //  Constructor tests
    // ------------------------------------------------------------------

    public function testConstructorSetsCaption(): void
    {
        $tray = new \XoopsFormElementTray('My Caption');
        $this->assertSame('My Caption', $tray->getCaption());
    }

    public function testConstructorDefaultDelimiter(): void
    {
        $tray = new \XoopsFormElementTray('Caption');
        $this->assertSame('&nbsp;', $tray->getDelimeter());
    }

    public function testConstructorCustomDelimiter(): void
    {
        $tray = new \XoopsFormElementTray('Caption', ' | ');
        $this->assertSame(' | ', $tray->getDelimeter());
    }

    public function testConstructorDefaultNameIsEmpty(): void
    {
        $tray = new \XoopsFormElementTray('Caption');
        $this->assertSame('', $tray->getName(false));
    }

    public function testConstructorCustomName(): void
    {
        $tray = new \XoopsFormElementTray('Caption', '&nbsp;', 'myTray');
        $this->assertSame('myTray', $tray->getName(false));
    }

    // ------------------------------------------------------------------
    //  isContainer
    // ------------------------------------------------------------------

    public function testIsContainerReturnsTrue(): void
    {
        $this->assertTrue($this->tray->isContainer());
    }

    // ------------------------------------------------------------------
    //  isRequired
    // ------------------------------------------------------------------

    public function testIsRequiredReturnsFalseWhenEmpty(): void
    {
        $this->assertFalse($this->tray->isRequired());
    }

    public function testIsRequiredReturnsTrueWhenChildIsRequired(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text, true);
        $this->assertTrue($this->tray->isRequired());
    }

    // ------------------------------------------------------------------
    //  addElement / getElements
    // ------------------------------------------------------------------

    public function testAddElementSimple(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text);

        $elements = $this->tray->getElements();
        $this->assertCount(1, $elements);
        $this->assertInstanceOf(\XoopsFormText::class, $elements[0]);
    }

    public function testAddElementRequired(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text, true);

        $required = $this->tray->getRequired();
        $this->assertCount(1, $required);
        $this->assertSame($text, $required[0]);
        $this->assertTrue($text->isRequired());
    }

    public function testAddElementNotRequiredDoesNotTrack(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text, false);

        $required = $this->tray->getRequired();
        $this->assertCount(0, $required);
        $this->assertFalse($text->isRequired());
    }

    public function testAddMultipleElements(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100);
        $text2 = new \XoopsFormText('Second', 'second', 25, 100);
        $this->tray->addElement($text1);
        $this->tray->addElement($text2);

        $elements = $this->tray->getElements();
        $this->assertCount(2, $elements);
    }

    // ------------------------------------------------------------------
    //  addElement with container (nested tray)
    // ------------------------------------------------------------------

    public function testAddContainerCollectsRequiredFromChild(): void
    {
        $innerTray = new \XoopsFormElementTray('Inner');
        $text = new \XoopsFormText('Required Field', 'req_field', 25, 100);
        $innerTray->addElement($text, true);

        $this->tray->addElement($innerTray);

        $required = $this->tray->getRequired();
        $this->assertCount(1, $required);
        $this->assertSame($text, $required[0]);
    }

    // ------------------------------------------------------------------
    //  getElements recursive vs non-recursive
    // ------------------------------------------------------------------

    public function testGetElementsNonRecursiveReturnsDirectChildren(): void
    {
        $innerTray = new \XoopsFormElementTray('Inner');
        $innerText = new \XoopsFormText('Inner Field', 'inner', 25, 100);
        $innerTray->addElement($innerText);

        $outerText = new \XoopsFormText('Outer Field', 'outer', 25, 100);
        $this->tray->addElement($outerText);
        $this->tray->addElement($innerTray);

        $elements = $this->tray->getElements(false);
        $this->assertCount(2, $elements);
        // Second element should be the inner tray, not the inner text
        $this->assertInstanceOf(\XoopsFormElementTray::class, $elements[1]);
    }

    public function testGetElementsRecursiveFlattensContainers(): void
    {
        $innerTray = new \XoopsFormElementTray('Inner');
        $innerText = new \XoopsFormText('Inner Field', 'inner', 25, 100);
        $innerTray->addElement($innerText);

        $outerText = new \XoopsFormText('Outer Field', 'outer', 25, 100);
        $this->tray->addElement($outerText);
        $this->tray->addElement($innerTray);

        $elements = $this->tray->getElements(true);
        $this->assertCount(2, $elements);
        // Both should be XoopsFormText, the tray itself is flattened
        $this->assertInstanceOf(\XoopsFormText::class, $elements[0]);
        $this->assertInstanceOf(\XoopsFormText::class, $elements[1]);
    }

    public function testGetElementsRecursiveDeeplyNested(): void
    {
        $innerTray = new \XoopsFormElementTray('Inner');
        $deepTray = new \XoopsFormElementTray('Deep');
        $deepText = new \XoopsFormText('Deep Field', 'deep', 25, 100);
        $deepTray->addElement($deepText);
        $innerTray->addElement($deepTray);

        $outerText = new \XoopsFormText('Outer Field', 'outer', 25, 100);
        $this->tray->addElement($outerText);
        $this->tray->addElement($innerTray);

        $elements = $this->tray->getElements(true);
        $this->assertCount(2, $elements);
        $this->assertSame('outer', $elements[0]->getName(false));
        $this->assertSame('deep', $elements[1]->getName(false));
    }

    // ------------------------------------------------------------------
    //  getRequired
    // ------------------------------------------------------------------

    public function testGetRequiredReturnsEmptyArrayByDefault(): void
    {
        $required = $this->tray->getRequired();
        $this->assertIsArray($required);
        $this->assertCount(0, $required);
    }

    public function testGetRequiredReturnsAllRequiredElements(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100);
        $text2 = new \XoopsFormText('Second', 'second', 25, 100);
        $text3 = new \XoopsFormText('Third', 'third', 25, 100);

        $this->tray->addElement($text1, true);
        $this->tray->addElement($text2, false);
        $this->tray->addElement($text3, true);

        $required = $this->tray->getRequired();
        $this->assertCount(2, $required);
    }

    // ------------------------------------------------------------------
    //  getDelimeter
    // ------------------------------------------------------------------

    public function testGetDelimeterNoEncode(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '&nbsp;');
        $this->assertSame('&nbsp;', $tray->getDelimeter(false));
    }

    public function testGetDelimeterEncoded(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '&nbsp;');
        // &nbsp; is replaced with space, then htmlspecialchars applied
        $result = $tray->getDelimeter(true);
        $this->assertSame(' ', $result);
    }

    public function testGetDelimeterCustomEncoded(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '<br>');
        $result = $tray->getDelimeter(true);
        $this->assertSame(htmlspecialchars('<br>', ENT_QUOTES | ENT_HTML5), $result);
    }

    // ------------------------------------------------------------------
    //  Orientation
    // ------------------------------------------------------------------

    public function testDefaultOrientationIsHorizontal(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '&nbsp;');
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_HORIZONTAL, $tray->getOrientation());
    }

    public function testSetOrientationVertical(): void
    {
        $this->tray->setOrientation(\XoopsFormElementTray::ORIENTATION_VERTICAL);
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_VERTICAL, $this->tray->getOrientation());
    }

    public function testSetOrientationHorizontal(): void
    {
        $this->tray->setOrientation(\XoopsFormElementTray::ORIENTATION_HORIZONTAL);
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_HORIZONTAL, $this->tray->getOrientation());
    }

    public function testSetOrientationInvalidDefaultsToHorizontal(): void
    {
        $this->tray->setOrientation('diagonal');
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_HORIZONTAL, $this->tray->getOrientation());
    }

    public function testBrDelimiterImpliesVerticalOrientation(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '<br>');
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_VERTICAL, $tray->getOrientation());
    }

    public function testBrSlashDelimiterImpliesVerticalOrientation(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '<br />');
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_VERTICAL, $tray->getOrientation());
    }

    public function testBrCaseInsensitiveDelimiterImpliesVerticalOrientation(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '<BR>');
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_VERTICAL, $tray->getOrientation());
    }

    public function testGetOrientationStripsBrFromDelimiter(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '<br>');
        $tray->getOrientation(); // triggers br stripping
        $this->assertSame('', $tray->getDelimeter());
    }

    public function testExplicitOrientationOverridesDelimiter(): void
    {
        $tray = new \XoopsFormElementTray('Cap', '<br>');
        $tray->setOrientation(\XoopsFormElementTray::ORIENTATION_HORIZONTAL);
        $this->assertSame(\XoopsFormElementTray::ORIENTATION_HORIZONTAL, $tray->getOrientation());
    }

    // ------------------------------------------------------------------
    //  Constants
    // ------------------------------------------------------------------

    public function testOrientationConstants(): void
    {
        $this->assertSame('horizontal', \XoopsFormElementTray::ORIENTATION_HORIZONTAL);
        $this->assertSame('vertical', \XoopsFormElementTray::ORIENTATION_VERTICAL);
    }

    // ------------------------------------------------------------------
    //  render
    // ------------------------------------------------------------------

    public function testRenderReturnsString(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text);

        $result = $this->tray->render();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testRenderWithMultipleElements(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100);
        $text2 = new \XoopsFormText('Second', 'second', 25, 100);
        $this->tray->addElement($text1);
        $this->tray->addElement($text2);

        $result = $this->tray->render();
        $this->assertIsString($result);
    }

    public function testRenderEmptyTray(): void
    {
        $result = $this->tray->render();
        $this->assertIsString($result);
    }

    // ------------------------------------------------------------------
    //  Edge cases
    // ------------------------------------------------------------------

    public function testNestedContainersRequiredPropagation(): void
    {
        $innerTray = new \XoopsFormElementTray('Inner');
        $text1 = new \XoopsFormText('Required1', 'req1', 25, 100);
        $text2 = new \XoopsFormText('Required2', 'req2', 25, 100);
        $innerTray->addElement($text1, true);
        $innerTray->addElement($text2, true);

        $this->tray->addElement($innerTray);

        $required = $this->tray->getRequired();
        $this->assertCount(2, $required);
        $this->assertTrue($this->tray->isRequired());
    }

    public function testGetElementsReturnsByReference(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text);

        $elements = &$this->tray->getElements();
        $this->assertSame($text, $elements[0]);
    }

    public function testGetRequiredReturnsByReference(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->tray->addElement($text, true);

        $required = &$this->tray->getRequired();
        $this->assertSame($text, $required[0]);
    }

    /**
     * Data provider for delimiter scenarios
     *
     * @return array<string, array{string, string, bool}>
     */
    public static function delimiterProvider(): array
    {
        return [
            'nbsp default'     => ['&nbsp;', '&nbsp;', false],
            'pipe'             => [' | ', ' | ', false],
            'br tag'           => ['<br>', '<br>', false],
            'empty string'     => ['', '', false],
            'nbsp encoded'     => ['&nbsp;', ' ', true],
        ];
    }

    #[DataProvider('delimiterProvider')]
    public function testGetDelimeterVariations(string $input, string $expected, bool $encode): void
    {
        $tray = new \XoopsFormElementTray('Cap', $input);
        $this->assertSame($expected, $tray->getDelimeter($encode));
    }
}
