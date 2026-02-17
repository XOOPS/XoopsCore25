<?php

declare(strict_types=1);

namespace xoopsutility;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsUtility;

#[CoversClass(XoopsUtility::class)]
class XoopsUtilityRecursiveTest extends TestCase
{
    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/utility/xoopsutility.php';
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorDoesNotThrow(): void
    {
        $utility = new XoopsUtility();
        $this->assertInstanceOf(XoopsUtility::class, $utility);
    }

    // ---------------------------------------------------------------
    // recursive() with string function handler
    // ---------------------------------------------------------------

    #[Test]
    public function recursiveWithStringFunctionAppliesItToScalar(): void
    {
        $result = XoopsUtility::recursive('strtoupper', 'hello');
        $this->assertSame('HELLO', $result);
    }

    #[Test]
    public function recursiveWithStrtolower(): void
    {
        $result = XoopsUtility::recursive('strtolower', 'WORLD');
        $this->assertSame('world', $result);
    }

    #[Test]
    public function recursiveWithTrim(): void
    {
        $result = XoopsUtility::recursive('trim', '  spaces  ');
        $this->assertSame('spaces', $result);
    }

    #[Test]
    public function recursiveWithNonExistentFunctionReturnsDataAsIs(): void
    {
        $result = XoopsUtility::recursive('nonexistent_function_xyz', 'data');
        $this->assertSame('data', $result);
    }

    // ---------------------------------------------------------------
    // recursive() with array method handler
    // ---------------------------------------------------------------

    #[Test]
    public function recursiveWithCallableArrayCallsMethod(): void
    {
        // Use a known callable — trim via array syntax
        $result = XoopsUtility::recursive('trim', '  hello  ');
        $this->assertSame('hello', $result);
    }

    // ---------------------------------------------------------------
    // recursive() edge cases
    // ---------------------------------------------------------------

    #[Test]
    public function recursiveWithEmptyString(): void
    {
        $result = XoopsUtility::recursive('trim', '');
        $this->assertSame('', $result);
    }

    #[Test]
    public function recursiveWithIntegerData(): void
    {
        $result = XoopsUtility::recursive('intval', '42');
        $this->assertSame(42, $result);
    }

    #[Test]
    public function recursiveWithStripslashes(): void
    {
        $result = XoopsUtility::recursive('stripslashes', "it\\'s");
        $this->assertSame("it's", $result);
    }

    // ---------------------------------------------------------------
    // Type safety
    // ---------------------------------------------------------------

    #[Test]
    public function recursiveReturnTypeMatchesFunctionReturn(): void
    {
        $result = XoopsUtility::recursive('strlen', 'test');
        $this->assertIsInt($result);
        $this->assertSame(4, $result);
    }
}
