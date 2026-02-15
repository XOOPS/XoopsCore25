<?php
namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for XoopsFormHiddenToken.
 *
 * Source: class/xoopsform/formhiddentoken.php
 */
class XoopsFormHiddenTokenTest extends TestCase
{
    protected function setUp(): void
    {
        xoops_load('XoopsFormElement');
        xoops_load('XoopsFormHidden');
        xoops_load('XoopsFormHiddenToken');
    }

    /**
     * Default constructor sets name to 'XOOPS_TOKEN_REQUEST'.
     */
    public function testDefaultNameIsXoopsTokenRequest(): void
    {
        $element = new \XoopsFormHiddenToken();

        $this->assertSame('XOOPS_TOKEN_REQUEST', $element->getName());
    }

    /**
     * Default constructor uses xoopsSecurity to generate token value.
     */
    public function testDefaultValueIsTokenFromSecurity(): void
    {
        $element = new \XoopsFormHiddenToken();

        $this->assertSame('test_token_XOOPS_TOKEN', $element->getValue());
    }

    /**
     * Custom name is appended with _REQUEST.
     */
    public function testCustomNameAppendsRequest(): void
    {
        $element = new \XoopsFormHiddenToken('MY_TOKEN');

        $this->assertSame('MY_TOKEN_REQUEST', $element->getName());
    }

    /**
     * Custom name is used to generate the token via xoopsSecurity.
     */
    public function testCustomNameUsedForTokenGeneration(): void
    {
        $element = new \XoopsFormHiddenToken('MY_TOKEN');

        // The stub xoopsSecurity->createToken($timeout, $name) returns 'test_token_' . $name
        $this->assertSame('test_token_MY_TOKEN', $element->getValue());
    }

    /**
     * The element must be flagged as hidden.
     */
    public function testIsHiddenReturnsTrue(): void
    {
        $element = new \XoopsFormHiddenToken();

        $this->assertTrue($element->isHidden());
    }

    /**
     * The element must have an empty caption.
     */
    public function testCaptionIsEmpty(): void
    {
        $element = new \XoopsFormHiddenToken();

        $this->assertSame('', $element->getCaption());
    }

    /**
     * render() must contain a hidden input element.
     */
    public function testRenderContainsHiddenInput(): void
    {
        $element = new \XoopsFormHiddenToken();
        $html = $element->render();

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertNotFalse(
            strpos($html, 'type="hidden"'),
            'Rendered HTML must contain type="hidden"'
        );
    }

    /**
     * render() must contain the element name.
     */
    public function testRenderContainsName(): void
    {
        $element = new \XoopsFormHiddenToken();
        $html = $element->render();

        $this->assertNotFalse(
            strpos($html, 'XOOPS_TOKEN_REQUEST'),
            'Rendered HTML must contain the element name'
        );
    }

    /**
     * render() must contain the token value.
     */
    public function testRenderContainsTokenValue(): void
    {
        $element = new \XoopsFormHiddenToken();
        $html = $element->render();

        $this->assertNotFalse(
            strpos($html, 'test_token_XOOPS_TOKEN'),
            'Rendered HTML must contain the generated token value'
        );
    }

    /**
     * The element is an instance of XoopsFormHidden.
     */
    public function testInheritsXoopsFormHidden(): void
    {
        $element = new \XoopsFormHiddenToken();

        $this->assertInstanceOf(\XoopsFormHidden::class, $element);
    }

    /**
     * The element is an instance of XoopsFormElement.
     */
    public function testInheritsXoopsFormElement(): void
    {
        $element = new \XoopsFormHiddenToken();

        $this->assertInstanceOf(\XoopsFormElement::class, $element);
    }

    /**
     * Timeout parameter is passed to createToken but does not affect the name.
     */
    public function testTimeoutDoesNotAffectName(): void
    {
        $element = new \XoopsFormHiddenToken('XOOPS_TOKEN', 300);

        $this->assertSame('XOOPS_TOKEN_REQUEST', $element->getName());
    }

    /**
     * Timeout parameter is passed to createToken. Token still uses the name.
     */
    public function testTimeoutDoesNotAffectTokenName(): void
    {
        $element = new \XoopsFormHiddenToken('XOOPS_TOKEN', 300);

        // Our stub ignores timeout, but token is based on name
        $this->assertSame('test_token_XOOPS_TOKEN', $element->getValue());
    }

    /**
     * Data provider: various custom token names.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function customNameProvider(): array
    {
        return [
            'default'    => ['XOOPS_TOKEN', 'XOOPS_TOKEN_REQUEST', 'test_token_XOOPS_TOKEN'],
            'custom'     => ['MY_FORM', 'MY_FORM_REQUEST', 'test_token_MY_FORM'],
            'module'     => ['PUBLISHER_TOKEN', 'PUBLISHER_TOKEN_REQUEST', 'test_token_PUBLISHER_TOKEN'],
            'short'      => ['X', 'X_REQUEST', 'test_token_X'],
        ];
    }

    /**
     * Various custom names must produce correct name and token.
     */
    #[DataProvider('customNameProvider')]
    public function testCustomNames(string $inputName, string $expectedName, string $expectedToken): void
    {
        $element = new \XoopsFormHiddenToken($inputName);

        $this->assertSame($expectedName, $element->getName());
        $this->assertSame($expectedToken, $element->getValue());
    }

    /**
     * getValue with encode=true must HTML-encode the value.
     */
    public function testGetValueWithEncode(): void
    {
        $element = new \XoopsFormHiddenToken();
        $encoded = $element->getValue(true);

        // Our token has no special chars, so it should be the same
        $this->assertSame('test_token_XOOPS_TOKEN', $encoded);
    }

    /**
     * xoopsSecurity global must be available.
     */
    public function testXoopsSecurityGlobalExists(): void
    {
        $this->assertArrayHasKey('xoopsSecurity', $GLOBALS);
        $this->assertIsObject($GLOBALS['xoopsSecurity']);
    }

    /**
     * xoopsSecurity->createToken must return expected stub value.
     */
    public function testXoopsSecurityCreateToken(): void
    {
        $token = $GLOBALS['xoopsSecurity']->createToken(0, 'TEST');

        $this->assertSame('test_token_TEST', $token);
    }
}
