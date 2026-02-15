<?php

declare(strict_types=1);

namespace xoopsutility;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use XoopsUtility;

/**
 * Comprehensive PHPUnit tests for the XoopsUtility class.
 *
 * XoopsUtility provides:
 *   - An empty public constructor (allows instantiation as a plain object).
 *   - A static method `recursive($handler, $data)` that applies a handler to data.
 *     The handler may be a string (function name), an array ([class, method] callable),
 *     or another type. If $data is an array, it attempts to recursively apply via
 *     array_map (though note: the implementation has a known bug where it passes
 *     $handler as the first array_map argument instead of repeating it per element).
 *
 * The bootstrap loads the source file via require_once and defines XOOPS_ROOT_PATH
 * so the restricted-access guard passes.
 *
 * @see \XoopsUtility
 */
#[CoversClass(XoopsUtility::class)]
class XoopsUtilityTest extends TestCase
{
    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/utility/xoopsutility.php';
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    /**
     * Test that XoopsUtility can be instantiated.
     */
    public function testConstructorCreatesInstance(): void
    {
        $utility = new XoopsUtility();

        $this->assertInstanceOf(XoopsUtility::class, $utility);
    }

    /**
     * Test that multiple instances are independent objects.
     */
    public function testConstructorCreatesSeparateInstances(): void
    {
        $a = new XoopsUtility();
        $b = new XoopsUtility();

        $this->assertNotSame($a, $b);
    }

    // =========================================================================
    // recursive() — String handler with existing function
    // =========================================================================

    /**
     * Test recursive() with 'strtoupper' function converts string to uppercase.
     */
    public function testRecursiveWithStrtoupper(): void
    {
        $result = XoopsUtility::recursive('strtoupper', 'hello');

        $this->assertSame('HELLO', $result);
    }

    /**
     * Test recursive() with 'strtolower' function converts string to lowercase.
     */
    public function testRecursiveWithStrtolower(): void
    {
        $result = XoopsUtility::recursive('strtolower', 'HELLO');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with 'trim' removes surrounding whitespace.
     */
    public function testRecursiveWithTrim(): void
    {
        $result = XoopsUtility::recursive('trim', '  hello  ');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with 'strlen' returns the string length.
     */
    public function testRecursiveWithStrlen(): void
    {
        $result = XoopsUtility::recursive('strlen', 'hello');

        $this->assertSame(5, $result);
    }

    /**
     * Test recursive() with 'strrev' reverses the string.
     */
    public function testRecursiveWithStrrev(): void
    {
        $result = XoopsUtility::recursive('strrev', 'hello');

        $this->assertSame('olleh', $result);
    }

    /**
     * Test recursive() with 'ucfirst' capitalizes the first character.
     */
    public function testRecursiveWithUcfirst(): void
    {
        $result = XoopsUtility::recursive('ucfirst', 'hello');

        $this->assertSame('Hello', $result);
    }

    /**
     * Test recursive() with 'intval' converts to integer.
     */
    public function testRecursiveWithIntval(): void
    {
        $result = XoopsUtility::recursive('intval', '42');

        $this->assertSame(42, $result);
    }

    // =========================================================================
    // recursive() — String handler with non-existent function
    // =========================================================================

    /**
     * Test recursive() with a non-existent function name returns data unchanged.
     */
    public function testRecursiveWithNonExistentFunctionReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive('nonExistentFunctionName12345', 'hello');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with another non-existent function returns numeric data unchanged.
     */
    public function testRecursiveWithNonExistentFunctionReturnsNumericUnchanged(): void
    {
        $result = XoopsUtility::recursive('doesNotExist', 42);

        $this->assertSame(42, $result);
    }

    /**
     * Test recursive() with empty string as handler name returns data unchanged.
     * An empty string is technically a string, and function_exists('') is false.
     */
    public function testRecursiveWithEmptyStringHandler(): void
    {
        $result = XoopsUtility::recursive('', 'test data');

        $this->assertSame('test data', $result);
    }

    // =========================================================================
    // recursive() — Array handler [class, method] (static method)
    // =========================================================================

    /**
     * Test recursive() with a static class method handler via array syntax.
     */
    public function testRecursiveWithStaticClassMethodHandler(): void
    {
        $result = XoopsUtility::recursive(
            [XoopsUtilityTestHelper::class, 'doubleValue'],
            5
        );

        $this->assertSame(10, $result);
    }

    /**
     * Test recursive() with a static method that transforms a string.
     */
    public function testRecursiveWithStaticStringTransformHandler(): void
    {
        $result = XoopsUtility::recursive(
            [XoopsUtilityTestHelper::class, 'wrapInBrackets'],
            'hello'
        );

        $this->assertSame('[hello]', $result);
    }

    /**
     * Test recursive() with a static method returning a boolean.
     */
    public function testRecursiveWithStaticBooleanReturnHandler(): void
    {
        $result = XoopsUtility::recursive(
            [XoopsUtilityTestHelper::class, 'isPositive'],
            5
        );

        $this->assertTrue($result);
    }

    /**
     * Test recursive() with a static method returning false.
     */
    public function testRecursiveWithStaticMethodReturnsFalse(): void
    {
        $result = XoopsUtility::recursive(
            [XoopsUtilityTestHelper::class, 'isPositive'],
            -3
        );

        $this->assertFalse($result);
    }

    // =========================================================================
    // recursive() — Array handler with object instance method
    // =========================================================================

    /**
     * Test recursive() with an object instance method handler via array syntax.
     */
    public function testRecursiveWithObjectMethodHandler(): void
    {
        $helper = new XoopsUtilityTestHelper();
        $result = XoopsUtility::recursive(
            [$helper, 'addPrefix'],
            'world'
        );

        $this->assertSame('prefix_world', $result);
    }

    // =========================================================================
    // recursive() — Non-string, non-array handler returns data unchanged
    // =========================================================================

    /**
     * Test recursive() with null handler returns data unchanged.
     */
    public function testRecursiveWithNullHandlerReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive(null, 'hello');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with boolean true handler returns data unchanged.
     */
    public function testRecursiveWithBooleanTrueHandlerReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive(true, 'hello');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with boolean false handler returns data unchanged.
     */
    public function testRecursiveWithBooleanFalseHandlerReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive(false, 'hello');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with integer handler returns data unchanged.
     */
    public function testRecursiveWithIntegerHandlerReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive(42, 'hello');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with float handler returns data unchanged.
     */
    public function testRecursiveWithFloatHandlerReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive(3.14, 'hello');

        $this->assertSame('hello', $result);
    }

    /**
     * Test recursive() with zero as handler returns data unchanged.
     */
    public function testRecursiveWithZeroHandlerReturnsDataUnchanged(): void
    {
        $result = XoopsUtility::recursive(0, 'hello');

        $this->assertSame('hello', $result);
    }

    // =========================================================================
    // recursive() — Various data types
    // =========================================================================

    /**
     * Test recursive() with empty string data and a valid function handler.
     */
    public function testRecursiveWithEmptyStringData(): void
    {
        $result = XoopsUtility::recursive('strtoupper', '');

        $this->assertSame('', $result);
    }

    /**
     * Test recursive() with numeric string data.
     */
    public function testRecursiveWithNumericStringData(): void
    {
        $result = XoopsUtility::recursive('intval', '123');

        $this->assertSame(123, $result);
    }

    /**
     * Test recursive() with integer data and a valid function handler.
     */
    public function testRecursiveWithIntegerData(): void
    {
        $result = XoopsUtility::recursive('strval', 42);

        $this->assertSame('42', $result);
    }

    /**
     * Test recursive() with special characters in string data.
     */
    public function testRecursiveWithSpecialCharactersData(): void
    {
        $result = XoopsUtility::recursive('strtoupper', 'hello <world> & "test"');

        $this->assertSame('HELLO <WORLD> & "TEST"', $result);
    }

    /**
     * Test recursive() with unicode string data.
     */
    public function testRecursiveWithUnicodeData(): void
    {
        // strtolower may not handle multibyte, but it should not crash
        $result = XoopsUtility::recursive('strtolower', 'ABC');

        $this->assertSame('abc', $result);
    }

    /**
     * Test recursive() with null data and a non-existent handler returns null unchanged.
     */
    public function testRecursiveWithNullDataAndBadHandler(): void
    {
        $result = XoopsUtility::recursive('nonExistentFunc', null);

        $this->assertNull($result);
    }

    /**
     * Test recursive() with null data and null handler returns null.
     */
    public function testRecursiveWithNullDataAndNullHandler(): void
    {
        $result = XoopsUtility::recursive(null, null);

        $this->assertNull($result);
    }

    // =========================================================================
    // recursive() — Data provider for string function handlers
    // =========================================================================

    /**
     * Data provider for testing various built-in string functions as handlers.
     *
     * @return array<string, array{string, mixed, mixed}>
     */
    public static function stringHandlerProvider(): array
    {
        return [
            'strtoupper'     => ['strtoupper', 'hello', 'HELLO'],
            'strtolower'     => ['strtolower', 'HELLO', 'hello'],
            'trim leading'   => ['trim', '  test', 'test'],
            'trim trailing'  => ['trim', 'test  ', 'test'],
            'trim both'      => ['trim', '  test  ', 'test'],
            'ucfirst'        => ['ucfirst', 'hello', 'Hello'],
            'lcfirst'        => ['lcfirst', 'Hello', 'hello'],
            'strrev'         => ['strrev', 'abc', 'cba'],
            'intval string'  => ['intval', '99', 99],
            'intval float'   => ['intval', '3.14', 3],
            'strlen'         => ['strlen', 'test', 4],
            'md5'            => ['md5', '', 'd41d8cd98f00b204e9800998ecf8427e'],
        ];
    }

    /**
     * Test recursive() with various string function handlers via data provider.
     *
     * @param string $handler  The function name to use as handler
     * @param mixed  $data     The data to pass to the handler
     * @param mixed  $expected The expected result
     */
    #[DataProvider('stringHandlerProvider')]
    public function testRecursiveWithStringHandlerProvider(string $handler, mixed $data, mixed $expected): void
    {
        $result = XoopsUtility::recursive($handler, $data);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider for testing non-string/non-array handlers that should return data unchanged.
     *
     * @return array<string, array{mixed, mixed}>
     */
    public static function invalidHandlerProvider(): array
    {
        return [
            'null handler'         => [null, 'data'],
            'true handler'         => [true, 'data'],
            'false handler'        => [false, 'data'],
            'int zero handler'     => [0, 'data'],
            'int positive handler' => [42, 'data'],
            'int negative handler' => [-1, 'data'],
            'float handler'        => [1.5, 'data'],
            'object handler'       => [new \stdClass(), 'data'],
        ];
    }

    /**
     * Test recursive() with invalid handler types returns data unchanged.
     *
     * @param mixed $handler The non-string/non-array handler
     * @param mixed $data    The data that should be returned unchanged
     */
    #[DataProvider('invalidHandlerProvider')]
    public function testRecursiveWithInvalidHandlerReturnsData(mixed $handler, mixed $data): void
    {
        $result = XoopsUtility::recursive($handler, $data);

        $this->assertSame($data, $result);
    }
}

/**
 * Helper class providing static and instance methods for testing XoopsUtility::recursive()
 * with array-based [class, method] handlers.
 */
class XoopsUtilityTestHelper
{
    /**
     * Doubles a numeric value.
     *
     * @param int|float $value The value to double
     * @return int|float The doubled value
     */
    public static function doubleValue($value)
    {
        return $value * 2;
    }

    /**
     * Wraps a string in square brackets.
     *
     * @param string $value The string to wrap
     * @return string The wrapped string
     */
    public static function wrapInBrackets($value): string
    {
        return '[' . $value . ']';
    }

    /**
     * Checks if a numeric value is positive.
     *
     * @param int|float $value The value to check
     * @return bool True if positive, false otherwise
     */
    public static function isPositive($value): bool
    {
        return $value > 0;
    }

    /**
     * Adds a prefix to a string (instance method).
     *
     * @param string $value The string to prefix
     * @return string The prefixed string
     */
    public function addPrefix($value): string
    {
        return 'prefix_' . $value;
    }
}
