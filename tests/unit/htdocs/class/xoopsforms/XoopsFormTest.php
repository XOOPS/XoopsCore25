<?php

namespace xoopsforms;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/bootstrap.php';

xoops_load('XoopsFormElement');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsForm');
xoops_load('XoopsSimpleForm');
xoops_load('XoopsFormText');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormHiddenToken');
xoops_load('XoopsFormRenderer');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');

/**
 * Tests for XoopsForm
 */
#[CoversClass(\XoopsForm::class)]
class XoopsFormTest extends TestCase
{
    /**
     * @var \XoopsForm
     */
    protected $form;

    protected function setUp(): void
    {
        // Use $addtoken=false to skip XoopsFormHiddenToken complexity
        $this->form = new \XoopsSimpleForm('Test Title', 'testform', 'submit.php', 'post', false);
    }

    // ------------------------------------------------------------------
    //  Constructor tests
    // ------------------------------------------------------------------

    public function testConstructorSetsAllProperties(): void
    {
        $form = new \XoopsSimpleForm('My Title', 'myform', 'action.php', 'post', false, 'A summary');
        $this->assertSame('My Title', $form->getTitle());
        $this->assertSame('myform', $form->getName(false));
        $this->assertSame('action.php', $form->getAction(false));
        $this->assertSame('post', $form->getMethod());
        $this->assertSame('A summary', $form->getSummary());
    }

    public function testConstructorDefaultMethodIsPost(): void
    {
        $form = new \XoopsSimpleForm('Title', 'form', 'action.php', 'post', false);
        $this->assertSame('post', $form->getMethod());
    }

    public function testConstructorWithTokenAddsHiddenTokenElement(): void
    {
        $form = new \XoopsSimpleForm('Title', 'form', 'action.php', 'post', true);
        $elements = $form->getElements();
        $this->assertGreaterThanOrEqual(1, count($elements));

        $foundToken = false;
        foreach ($elements as $ele) {
            if ($ele instanceof \XoopsFormHiddenToken) {
                $foundToken = true;
                break;
            }
        }
        $this->assertTrue($foundToken, 'XoopsFormHiddenToken should be added when $addtoken=true');
    }

    public function testConstructorWithoutTokenNoHiddenToken(): void
    {
        $form = new \XoopsSimpleForm('Title', 'form', 'action.php', 'post', false);
        $elements = $form->getElements();
        $this->assertCount(0, $elements);
    }

    // ------------------------------------------------------------------
    //  getTitle
    // ------------------------------------------------------------------

    public function testGetTitleNoEncode(): void
    {
        $form = new \XoopsSimpleForm('Title <b>bold</b>', 'f', 'a.php', 'post', false);
        $this->assertSame('Title <b>bold</b>', $form->getTitle(false));
    }

    public function testGetTitleWithEncode(): void
    {
        $form = new \XoopsSimpleForm('Title <b>bold</b>', 'f', 'a.php', 'post', false);
        $result = $form->getTitle(true);
        $this->assertSame(htmlspecialchars('Title <b>bold</b>', ENT_QUOTES | ENT_HTML5), $result);
    }

    // ------------------------------------------------------------------
    //  getName
    // ------------------------------------------------------------------

    public function testGetNameDefaultEncodes(): void
    {
        $form = new \XoopsSimpleForm('T', 'my<form>', 'a.php', 'post', false);
        // Default $encode=true
        $this->assertSame(htmlspecialchars('my<form>', ENT_QUOTES | ENT_HTML5), $form->getName());
    }

    public function testGetNameNoEncode(): void
    {
        $form = new \XoopsSimpleForm('T', 'my<form>', 'a.php', 'post', false);
        $this->assertSame('my<form>', $form->getName(false));
    }

    // ------------------------------------------------------------------
    //  getAction
    // ------------------------------------------------------------------

    public function testGetActionDefaultEncodes(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'action.php?a=1&b=2', 'post', false);
        $result = $form->getAction(true);
        // getAction converts &amp; to & before encoding, so raw & stays as &amp;
        $this->assertStringContainsString('&amp;', $result);
    }

    public function testGetActionNoEncode(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'action.php?a=1&b=2', 'post', false);
        $this->assertSame('action.php?a=1&b=2', $form->getAction(false));
    }

    public function testGetActionConvertsAmpEntitiesBack(): void
    {
        // If the action was stored with &amp;, getAction(true) should still produce correct output
        $form = new \XoopsSimpleForm('T', 'f', 'action.php?a=1&amp;b=2', 'post', false);
        $result = $form->getAction(true);
        // &amp; is converted back to & then re-encoded
        $this->assertSame(htmlspecialchars('action.php?a=1&b=2', ENT_QUOTES | ENT_HTML5), $result);
    }

    // ------------------------------------------------------------------
    //  getMethod
    // ------------------------------------------------------------------

    public function testGetMethodPost(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', 'post', false);
        $this->assertSame('post', $form->getMethod());
    }

    public function testGetMethodGet(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', 'get', false);
        $this->assertSame('get', $form->getMethod());
    }

    public function testGetMethodGetCaseInsensitive(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', 'GET', false);
        $this->assertSame('get', $form->getMethod());
    }

    public function testGetMethodUnknownDefaultsToPost(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', 'put', false);
        $this->assertSame('post', $form->getMethod());
    }

    public function testGetMethodEmptyDefaultsToPost(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', '', false);
        $this->assertSame('post', $form->getMethod());
    }

    // ------------------------------------------------------------------
    //  getSummary / setSummary
    // ------------------------------------------------------------------

    public function testGetSummaryDefault(): void
    {
        $this->assertSame('', $this->form->getSummary());
    }

    public function testGetSummaryNoEncode(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', 'post', false, 'Sum<b>mary</b>');
        $this->assertSame('Sum<b>mary</b>', $form->getSummary(false));
    }

    public function testGetSummaryEncoded(): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', 'post', false, 'Sum<b>mary</b>');
        $this->assertSame(
            htmlspecialchars('Sum<b>mary</b>', ENT_QUOTES | ENT_HTML5),
            $form->getSummary(true)
        );
    }

    public function testSetSummaryStripsTags(): void
    {
        $this->form->setSummary('<p>Clean summary</p>');
        // setSummary sets $this->summary (note: different property name — _summary vs summary)
        // The setSummary method stores to $this->summary, not $this->_summary
        $this->assertSame('Clean summary', $this->form->summary);
    }

    // ------------------------------------------------------------------
    //  addElement
    // ------------------------------------------------------------------

    public function testAddElementWithFormElement(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        $this->assertInstanceOf(\XoopsFormText::class, $elements[0]);
    }

    public function testAddElementWithString(): void
    {
        $this->form->addElement('<tr><td>Break</td></tr>');

        $elements = $this->form->getElements();
        $this->assertCount(1, $elements);
        $this->assertIsString($elements[0]);
    }

    public function testAddElementRequiredTracksElement(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text, true);

        $required = $this->form->getRequired();
        $this->assertCount(1, $required);
        $this->assertTrue($text->isRequired());
    }

    public function testAddElementContainerCollectsRequired(): void
    {
        $tray = new \XoopsFormElementTray('Tray');
        $text = new \XoopsFormText('Field', 'field', 25, 100);
        $tray->addElement($text, true);

        $this->form->addElement($tray);

        $required = $this->form->getRequired();
        $this->assertCount(1, $required);
    }

    // ------------------------------------------------------------------
    //  getElements recursive / non-recursive
    // ------------------------------------------------------------------

    public function testGetElementsNonRecursive(): void
    {
        $tray = new \XoopsFormElementTray('Tray');
        $innerText = new \XoopsFormText('Inner', 'inner', 25, 100);
        $tray->addElement($innerText);

        $outerText = new \XoopsFormText('Outer', 'outer', 25, 100);
        $this->form->addElement($outerText);
        $this->form->addElement($tray);

        $elements = $this->form->getElements(false);
        $this->assertCount(2, $elements);
    }

    public function testGetElementsRecursiveFlattens(): void
    {
        $tray = new \XoopsFormElementTray('Tray');
        $innerText = new \XoopsFormText('Inner', 'inner', 25, 100);
        $tray->addElement($innerText);

        $outerText = new \XoopsFormText('Outer', 'outer', 25, 100);
        $this->form->addElement($outerText);
        $this->form->addElement($tray);

        $elements = $this->form->getElements(true);
        $this->assertCount(2, $elements);
        // Both should be non-containers
        foreach ($elements as $ele) {
            $this->assertInstanceOf(\XoopsFormText::class, $ele);
        }
    }

    public function testGetElementsRecursiveSkipsStrings(): void
    {
        $this->form->addElement('<tr><td>Break</td></tr>');
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $elements = $this->form->getElements(true);
        // String elements are skipped in recursive mode (is_object check)
        $this->assertCount(1, $elements);
    }

    // ------------------------------------------------------------------
    //  getElementNames
    // ------------------------------------------------------------------

    public function testGetElementNames(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100);
        $text2 = new \XoopsFormText('Second', 'second', 25, 100);
        $this->form->addElement($text1);
        $this->form->addElement($text2);

        $names = $this->form->getElementNames();
        $this->assertContains('first', $names);
        $this->assertContains('second', $names);
    }

    public function testGetElementNamesWithNestedContainer(): void
    {
        $tray = new \XoopsFormElementTray('Tray');
        $innerText = new \XoopsFormText('Inner', 'inner', 25, 100);
        $tray->addElement($innerText);

        $outerText = new \XoopsFormText('Outer', 'outer', 25, 100);
        $this->form->addElement($outerText);
        $this->form->addElement($tray);

        $names = $this->form->getElementNames();
        $this->assertContains('outer', $names);
        $this->assertContains('inner', $names);
    }

    // ------------------------------------------------------------------
    //  getElementByName
    // ------------------------------------------------------------------

    public function testGetElementByNameFound(): void
    {
        $text = new \XoopsFormText('Name', 'username', 25, 100);
        $this->form->addElement($text);

        $found = $this->form->getElementByName('username');
        $this->assertNotNull($found);
        $this->assertSame('username', $found->getName(false));
    }

    public function testGetElementByNameNotFound(): void
    {
        $text = new \XoopsFormText('Name', 'username', 25, 100);
        $this->form->addElement($text);

        $found = $this->form->getElementByName('nonexistent');
        $this->assertNull($found);
    }

    public function testGetElementByNameSearchesRecursively(): void
    {
        $tray = new \XoopsFormElementTray('Tray');
        $innerText = new \XoopsFormText('Inner', 'deep_field', 25, 100);
        $tray->addElement($innerText);
        $this->form->addElement($tray);

        $found = $this->form->getElementByName('deep_field');
        $this->assertNotNull($found);
        $this->assertSame('deep_field', $found->getName(false));
    }

    // ------------------------------------------------------------------
    //  setElementValue / getElementValue
    // ------------------------------------------------------------------

    public function testSetElementValueAndGet(): void
    {
        $text = new \XoopsFormText('Name', 'username', 25, 100, '');
        $this->form->addElement($text);

        $this->form->setElementValue('username', 'JohnDoe');
        $value = $this->form->getElementValue('username');
        $this->assertSame('JohnDoe', $value);
    }

    public function testGetElementValueEncoded(): void
    {
        $text = new \XoopsFormText('Name', 'username', 25, 100, '<script>alert(1)</script>');
        $this->form->addElement($text);

        $encoded = $this->form->getElementValue('username', true);
        $this->assertStringNotContainsString('<script>', $encoded);
    }

    public function testGetElementValueReturnsNullForMissing(): void
    {
        $result = $this->form->getElementValue('nonexistent');
        $this->assertNull($result);
    }

    public function testSetElementValueForNonexistentIsNoop(): void
    {
        // Should not throw an error
        $this->form->setElementValue('nonexistent', 'value');
        $this->assertNull($this->form->getElementValue('nonexistent'));
    }

    // ------------------------------------------------------------------
    //  setElementValues / getElementValues
    // ------------------------------------------------------------------

    public function testSetElementValuesAndGetAll(): void
    {
        $text1 = new \XoopsFormText('First', 'first', 25, 100, '');
        $text2 = new \XoopsFormText('Second', 'second', 25, 100, '');
        $this->form->addElement($text1);
        $this->form->addElement($text2);

        $this->form->setElementValues([
            'first'  => 'Value1',
            'second' => 'Value2',
        ]);

        $values = $this->form->getElementValues();
        $this->assertSame('Value1', $values['first']);
        $this->assertSame('Value2', $values['second']);
    }

    public function testSetElementValuesIgnoresNonexistentNames(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100, '');
        $this->form->addElement($text);

        $this->form->setElementValues([
            'name'        => 'Hello',
            'nonexistent' => 'Ignored',
        ]);

        $this->assertSame('Hello', $this->form->getElementValue('name'));
    }

    public function testGetElementValuesEncodesValues(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100, '<b>Bold</b>');
        $this->form->addElement($text);

        $values = $this->form->getElementValues(true);
        $this->assertArrayHasKey('name', $values);
        $this->assertStringNotContainsString('<b>', $values['name']);
    }

    // ------------------------------------------------------------------
    //  setClass / getClass
    // ------------------------------------------------------------------

    public function testGetClassReturnsFalseByDefault(): void
    {
        $this->assertFalse($this->form->getClass());
    }

    public function testSetClassAndGet(): void
    {
        $this->form->setClass('form-horizontal');
        $this->assertSame('form-horizontal', $this->form->getClass());
    }

    public function testSetMultipleClasses(): void
    {
        $this->form->setClass('form-horizontal');
        $this->form->setClass('my-form');
        $result = $this->form->getClass();
        $this->assertStringContainsString('form-horizontal', $result);
        $this->assertStringContainsString('my-form', $result);
    }

    public function testSetClassTrimsWhitespace(): void
    {
        $this->form->setClass('  spaced-class  ');
        $this->assertSame('spaced-class', $this->form->getClass());
    }

    public function testSetClassEmptyStringIgnored(): void
    {
        $this->form->setClass('');
        $this->assertFalse($this->form->getClass());
    }

    public function testGetClassEscapesHtml(): void
    {
        $this->form->setClass('class<xss>');
        $result = $this->form->getClass();
        $this->assertStringNotContainsString('<xss>', $result);
    }

    // ------------------------------------------------------------------
    //  setExtra / getExtra
    // ------------------------------------------------------------------

    public function testGetExtraDefaultIsEmptyString(): void
    {
        $extra = $this->form->getExtra();
        $this->assertSame('', $extra);
    }

    public function testSetExtraAndGet(): void
    {
        $this->form->setExtra('enctype="multipart/form-data"');
        $extra = $this->form->getExtra();
        $this->assertStringContainsString('enctype="multipart/form-data"', $extra);
    }

    public function testSetExtraMultipleTimes(): void
    {
        $this->form->setExtra('data-id="1"');
        $this->form->setExtra('data-name="test"');
        $extra = $this->form->getExtra();
        $this->assertStringContainsString('data-id="1"', $extra);
        $this->assertStringContainsString('data-name="test"', $extra);
    }

    // ------------------------------------------------------------------
    //  setRequired / getRequired
    // ------------------------------------------------------------------

    public function testGetRequiredEmptyByDefault(): void
    {
        $required = $this->form->getRequired();
        $this->assertIsArray($required);
        $this->assertCount(0, $required);
    }

    public function testSetRequired(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->setRequired($text);

        $required = $this->form->getRequired();
        $this->assertCount(1, $required);
    }

    // ------------------------------------------------------------------
    //  renderValidationJS
    // ------------------------------------------------------------------

    public function testRenderValidationJSWithTags(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $js = $this->form->renderValidationJS(true);
        $this->assertIsString($js);
        $this->assertStringContainsString('<script', $js);
        $this->assertStringContainsString('</script>', $js);
        $this->assertStringContainsString('xoopsFormValidate_', $js);
    }

    public function testRenderValidationJSWithoutTags(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $this->form->addElement($text);

        $js = $this->form->renderValidationJS(false);
        $this->assertIsString($js);
        $this->assertStringNotContainsString('<script', $js);
        $this->assertStringContainsString('xoopsFormValidate_', $js);
    }

    public function testRenderValidationJSContainsFormName(): void
    {
        $js = $this->form->renderValidationJS(false);
        $this->assertStringContainsString('xoopsFormValidate_testform', $js);
    }

    public function testRenderValidationJSReturnsTrue(): void
    {
        $js = $this->form->renderValidationJS(false);
        $this->assertStringContainsString('return true;', $js);
    }

    // ------------------------------------------------------------------
    //  display
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
    //  getObjectID
    // ------------------------------------------------------------------

    public function testGetObjectIDSha1(): void
    {
        $hash = $this->form->getObjectID($this->form, 'sha1');
        $this->assertIsString($hash);
        $this->assertSame(40, strlen($hash), 'SHA1 hash should be 40 characters');
    }

    public function testGetObjectIDMd5(): void
    {
        $hash = $this->form->getObjectID($this->form, 'md5');
        $this->assertIsString($hash);
        $this->assertSame(32, strlen($hash), 'MD5 hash should be 32 characters');
    }

    public function testGetObjectIDDefaultIsSha1(): void
    {
        $hash = $this->form->getObjectID($this->form);
        $this->assertSame(40, strlen($hash));
    }

    public function testGetObjectIDNonObjectUsesThis(): void
    {
        // Passing non-object should default to $this
        $hash = $this->form->getObjectID('not_an_object');
        $this->assertIsString($hash);
        $this->assertSame(40, strlen($hash));
    }

    public function testGetObjectIDDifferentHashInfoProducesDifferentResults(): void
    {
        $sha1 = $this->form->getObjectID($this->form, 'sha1');
        $md5 = $this->form->getObjectID($this->form, 'md5');
        $this->assertNotSame($sha1, $md5);
    }

    // ------------------------------------------------------------------
    //  getArrayID
    // ------------------------------------------------------------------

    public function testGetArrayIDWithScalar(): void
    {
        $result = $this->form->getArrayID('value', 'key', '', 'sha1');
        $this->assertIsString($result);
        $this->assertSame(40, strlen($result));
    }

    public function testGetArrayIDWithArray(): void
    {
        $result = $this->form->getArrayID(['a', 'b'], 'key', '', 'sha1');
        $this->assertIsString($result);
        $this->assertSame(40, strlen($result));
    }

    public function testGetArrayIDMd5(): void
    {
        $result = $this->form->getArrayID('value', 'key', '', 'md5');
        $this->assertIsString($result);
        $this->assertSame(32, strlen($result));
    }

    public function testGetArrayIDMd5WithArray(): void
    {
        $result = $this->form->getArrayID(['a', 'b'], 'key', '', 'md5');
        $this->assertIsString($result);
        $this->assertSame(32, strlen($result));
    }

    // ------------------------------------------------------------------
    //  Edge cases and integration
    // ------------------------------------------------------------------

    public function testFormWithMixedElements(): void
    {
        $text = new \XoopsFormText('Name', 'name', 25, 100);
        $hidden = new \XoopsFormHidden('id', '42');
        $this->form->addElement($text);
        $this->form->addElement($hidden);
        $this->form->addElement('<tr><td>separator</td></tr>');

        $elements = $this->form->getElements(false);
        $this->assertCount(3, $elements);
    }

    public function testGetElementsRecursiveWithMixedTypes(): void
    {
        $tray = new \XoopsFormElementTray('Tray');
        $innerText = new \XoopsFormText('Inner', 'inner', 25, 100);
        $tray->addElement($innerText);

        $text = new \XoopsFormText('Outer', 'outer', 25, 100);
        $this->form->addElement($text);
        $this->form->addElement($tray);
        $this->form->addElement('<tr><td>string</td></tr>');

        $elements = $this->form->getElements(true);
        // String elements are skipped, tray is flattened
        $this->assertCount(2, $elements);
    }

    /**
     * Data provider for HTTP method normalization
     *
     * @return array<string, array{string, string}>
     */
    public static function methodProvider(): array
    {
        return [
            'post lowercase'    => ['post', 'post'],
            'POST uppercase'    => ['POST', 'post'],
            'get lowercase'     => ['get', 'get'],
            'GET uppercase'     => ['GET', 'get'],
            'Get mixed'         => ['Get', 'get'],
            'put falls to post' => ['put', 'post'],
            'empty falls post'  => ['', 'post'],
            'delete to post'    => ['delete', 'post'],
        ];
    }

    #[DataProvider('methodProvider')]
    public function testGetMethodNormalization(string $input, string $expected): void
    {
        $form = new \XoopsSimpleForm('T', 'f', 'a.php', $input, false);
        $this->assertSame($expected, $form->getMethod());
    }

    public function testTokenElementNameContainsRequest(): void
    {
        $form = new \XoopsSimpleForm('Title', 'form', 'action.php', 'post', true);
        $elements = $form->getElements(true);

        $tokenFound = false;
        foreach ($elements as $ele) {
            if ($ele instanceof \XoopsFormHiddenToken) {
                $this->assertStringContainsString('XOOPS_TOKEN_REQUEST', $ele->getName(false));
                $tokenFound = true;
                break;
            }
        }
        $this->assertTrue($tokenFound, 'Token element should be found');
    }

    public function testFormGetExtraReturnsByReference(): void
    {
        $extra = &$this->form->getExtra();
        $this->assertIsString($extra);
    }

    public function testFormGetRequiredReturnsByReference(): void
    {
        $required = &$this->form->getRequired();
        $this->assertIsArray($required);
    }
}
