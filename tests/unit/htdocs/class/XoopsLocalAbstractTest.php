<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use XoopsLocalAbstract;

/**
 * Comprehensive unit tests for XoopsLocalAbstract.
 *
 * XoopsLocalAbstract provides locale-aware string manipulation, encoding
 * conversion, time formatting, and number/money formatting for the XOOPS
 * core. Most methods have a multibyte path (XOOPS_USE_MULTIBYTES=1) and
 * a single-byte fallback path (XOOPS_USE_MULTIBYTES=0).
 *
 * The test bootstrap defines XOOPS_USE_MULTIBYTES=0 and _CHARSET='UTF-8',
 * so we primarily test the single-byte code paths here.
 *
 * Tested API:
 *   - substr()            Substring with trimmarker support
 *   - trim()              Whitespace trimming
 *   - convert_encoding()  Character encoding conversion
 *   - utf8_encode()       UTF-8 encoding (non-multibyte path)
 *   - utf8_decode()       UTF-8 decoding (non-multibyte path)
 *   - number_format()     Returns number unchanged (base implementation)
 *   - money_format()      Returns number unchanged (base implementation)
 *   - __call()            Magic method dispatching to PHP functions
 *   - getTimeFormatDesc() Returns the _TIMEFORMAT_DESC constant
 *   - formatTimestamp()   RSS format only (minimal dependency path)
 *
 */
#[CoversClass(XoopsLocalAbstract::class)]
class XoopsLocalAbstractTest extends TestCase
{
    /**
     * Ensure the source class is loaded before any test runs.
     */
    public static function setUpBeforeClass(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslocal.php';

        // Define _TIMEFORMAT_DESC if not already set by the bootstrap
        if (!defined('_TIMEFORMAT_DESC')) {
            define('_TIMEFORMAT_DESC', 'Use the following PHP date format characters:');
        }
    }

    // ---------------------------------------------------------------
    //  substr() tests — single-byte path (XOOPS_USE_MULTIBYTES = 0)
    // ---------------------------------------------------------------

    /**
     * A short string that fits entirely within the length limit should be
     * returned without any trimmarker appended.
     */
    public function testSubstrShortStringReturnsWithoutTrimmarker(): void
    {
        // "Hello" is 5 chars; length=10 means no trimming needed
        $result = XoopsLocalAbstract::substr('Hello', 0, 10);
        $this->assertSame('Hello', $result);
    }

    /**
     * A string whose length exactly equals the limit should be returned
     * without trimming.
     */
    public function testSubstrExactLengthReturnsWithoutTrimmarker(): void
    {
        // "Hello" is 5 chars; start=0, length=5 -> strlen(5)-0 <= 5 -> true
        $result = XoopsLocalAbstract::substr('Hello', 0, 5);
        $this->assertSame('Hello', $result);
    }

    /**
     * A string longer than the limit should be truncated and the default
     * trimmarker '...' appended.
     */
    public function testSubstrLongStringAppendsTrimmarker(): void
    {
        // "Hello World" is 11 chars; length=8 -> 11-0=11 > 8 -> truncates
        // Effective length: 8 - strlen('...') = 8 - 3 = 5 chars + '...'
        $result = XoopsLocalAbstract::substr('Hello World', 0, 8);
        $this->assertSame('Hello...', $result);
    }

    /**
     * A custom trimmarker should be used instead of the default '...'.
     */
    public function testSubstrCustomTrimmarker(): void
    {
        // "Hello World" is 11 chars; length=8 -> truncates
        // Effective length: 8 - strlen('~') = 7 chars + '~'
        $result = XoopsLocalAbstract::substr('Hello World', 0, 8, '~');
        $this->assertSame('Hello W~', $result);
    }

    /**
     * An empty trimmarker means no marker is appended, but the string is
     * still truncated to the exact length.
     */
    public function testSubstrEmptyTrimmarker(): void
    {
        // "Hello World" is 11 chars; length=5 -> truncates
        // Effective length: 5 - strlen('') = 5 chars + ''
        $result = XoopsLocalAbstract::substr('Hello World', 0, 5, '');
        $this->assertSame('Hello', $result);
    }

    /**
     * A non-zero start offset should begin extraction at the given position.
     */
    public function testSubstrWithStartOffset(): void
    {
        // "Hello World" with start=6, length=20 -> strlen(11)-6=5 <= 20 -> no trimming
        $result = XoopsLocalAbstract::substr('Hello World', 6, 20);
        $this->assertSame('World', $result);
    }

    /**
     * Start offset with truncation should work correctly together.
     */
    public function testSubstrStartOffsetWithTruncation(): void
    {
        // "Hello World" with start=6, length=4 -> strlen(11)-6=5 > 4 -> truncates
        // Effective: substr('Hello World', 6, 4-3) = substr('Hello World', 6, 1) = 'W' + '...'
        $result = XoopsLocalAbstract::substr('Hello World', 6, 4);
        $this->assertSame('W...', $result);
    }

    /**
     * An empty string should return an empty string regardless of parameters.
     */
    public function testSubstrEmptyString(): void
    {
        $result = XoopsLocalAbstract::substr('', 0, 10);
        $this->assertSame('', $result);
    }

    /**
     * When the trimmarker is longer than the allowed length, the method
     * should still function (though the result may be unusual). This tests
     * robustness rather than a specific requirement.
     */
    public function testSubstrTrimmarkerLongerThanLength(): void
    {
        // "Hello World" is 11 chars; length=2, trimmarker='...' (3 chars)
        // 11 - 0 = 11 > 2, so: substr('Hello World', 0, 2-3) = substr('Hello World', 0, -1) + '...'
        // PHP substr with negative length returns everything except last char(s)
        $result = XoopsLocalAbstract::substr('Hello World', 0, 2);
        // substr('Hello World', 0, -1) = 'Hello Worl' + '...' = 'Hello Worl...'
        $this->assertSame('Hello Worl...', $result);
    }

    /**
     * Various combinations of strings and lengths via data provider.
     */
    #[DataProvider('substrProvider')]
    public function testSubstrVariousCombinations(
        string $input,
        int $start,
        int $length,
        string $trimmarker,
        string $expected
    ): void {
        $result = XoopsLocalAbstract::substr($input, $start, $length, $trimmarker);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, int, int, string, string}>
     */
    public static function substrProvider(): array
    {
        return [
            'single char fits' => ['A', 0, 5, '...', 'A'],
            'single char exact' => ['A', 0, 1, '...', 'A'],
            'numeric string' => ['1234567890', 0, 7, '...', '1234...'],
            'spaces in string' => ['foo bar baz', 0, 7, '...', 'foo ...'],
            'unicode not multibyte path' => ['cafe', 0, 3, '.', 'ca.'],
            'start beyond string' => ['Hi', 5, 10, '...', ''],
        ];
    }

    // ---------------------------------------------------------------
    //  trim() tests
    // ---------------------------------------------------------------

    /**
     * Leading and trailing spaces should be removed.
     */
    public function testTrimRemovesSpaces(): void
    {
        $this->assertSame('Hello', XoopsLocalAbstract::trim('  Hello  '));
    }

    /**
     * Tabs should be removed by trim.
     */
    public function testTrimRemovesTabs(): void
    {
        $this->assertSame('Hello', XoopsLocalAbstract::trim("\tHello\t"));
    }

    /**
     * Newlines (LF and CR) should be removed by trim.
     */
    public function testTrimRemovesNewlines(): void
    {
        $this->assertSame('Hello', XoopsLocalAbstract::trim("\nHello\r\n"));
    }

    /**
     * Mixed whitespace characters should all be removed.
     */
    public function testTrimRemovesMixedWhitespace(): void
    {
        $this->assertSame('Hello World', XoopsLocalAbstract::trim("  \t\n Hello World \r\n\t "));
    }

    /**
     * A string with no leading/trailing whitespace should be unchanged.
     */
    public function testTrimNoWhitespace(): void
    {
        $this->assertSame('Hello', XoopsLocalAbstract::trim('Hello'));
    }

    /**
     * An empty string should remain empty.
     */
    public function testTrimEmptyString(): void
    {
        $this->assertSame('', XoopsLocalAbstract::trim(''));
    }

    /**
     * A string of only whitespace should become empty.
     */
    public function testTrimWhitespaceOnlyString(): void
    {
        $this->assertSame('', XoopsLocalAbstract::trim("  \t\n\r  "));
    }

    /**
     * Interior whitespace should be preserved.
     */
    public function testTrimPreservesInteriorWhitespace(): void
    {
        $this->assertSame("Hello\t World", XoopsLocalAbstract::trim("  Hello\t World  "));
    }

    // ---------------------------------------------------------------
    //  convert_encoding() tests
    // ---------------------------------------------------------------

    /**
     * Empty text should be returned unchanged.
     */
    public function testConvertEncodingEmptyStringReturnsEmpty(): void
    {
        $this->assertSame('', XoopsLocalAbstract::convert_encoding(''));
    }

    /**
     * Null-like empty value should be returned as-is.
     */
    public function testConvertEncodingNullReturnsNull(): void
    {
        $result = XoopsLocalAbstract::convert_encoding(null);
        $this->assertNull($result);
    }

    /**
     * When source and target encodings are the same, the text should be
     * returned unchanged (case-insensitive comparison).
     */
    public function testConvertEncodingSameEncodingReturnsSameText(): void
    {
        $text = 'Hello World';
        $result = XoopsLocalAbstract::convert_encoding($text, 'UTF-8', 'UTF-8');
        $this->assertSame($text, $result);
    }

    /**
     * Case-insensitive encoding comparison should also return same text.
     */
    public function testConvertEncodingSameEncodingCaseInsensitive(): void
    {
        $text = 'Hello World';
        $result = XoopsLocalAbstract::convert_encoding($text, 'utf-8', 'UTF-8');
        $this->assertSame($text, $result);
    }

    /**
     * When $to is empty, the text should be returned unchanged.
     */
    public function testConvertEncodingEmptyTargetReturnsSameText(): void
    {
        $text = 'Hello World';
        $result = XoopsLocalAbstract::convert_encoding($text, '', 'UTF-8');
        $this->assertSame($text, $result);
    }

    /**
     * When $from is empty, it should default to _CHARSET (UTF-8 in tests).
     * Since default $to is 'utf-8' and _CHARSET is 'UTF-8', same-encoding
     * short circuit should apply.
     */
    public function testConvertEncodingDefaultFromMatchesDefaultTo(): void
    {
        $text = 'Hello World';
        // $to defaults to 'utf-8', $from defaults to _CHARSET='UTF-8'
        // strcasecmp('utf-8', 'UTF-8') === 0, so returns $text
        $result = XoopsLocalAbstract::convert_encoding($text);
        $this->assertSame($text, $result);
    }

    /**
     * When $from is empty and xlanguage charset_base global is set,
     * it should use that as the source encoding.
     */
    public function testConvertEncodingUsesXlanguageCharsetBase(): void
    {
        $originalGlobal = isset($GLOBALS['xlanguage']['charset_base'])
            ? $GLOBALS['xlanguage']['charset_base']
            : null;

        // Set xlanguage charset to something that matches $to
        $GLOBALS['xlanguage']['charset_base'] = 'UTF-8';
        $text = 'Hello World';
        $result = XoopsLocalAbstract::convert_encoding($text, 'utf-8');
        $this->assertSame($text, $result);

        // Restore
        if ($originalGlobal === null) {
            unset($GLOBALS['xlanguage']);
        } else {
            $GLOBALS['xlanguage']['charset_base'] = $originalGlobal;
        }
    }

    /**
     * Actual conversion using iconv (XOOPS_USE_MULTIBYTES=0, so mb_ is
     * skipped, but iconv should be available).
     */
    public function testConvertEncodingActualConversionViaIconv(): void
    {
        if (!function_exists('iconv')) {
            $this->markTestSkipped('iconv extension not available');
        }

        // Convert a simple ASCII string from ISO-8859-1 to UTF-8
        $text = 'Hello';
        $result = XoopsLocalAbstract::convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        $this->assertSame('Hello', $result);
    }

    /**
     * Converting a Latin-1 encoded character via iconv should produce
     * correct UTF-8 output.
     */
    public function testConvertEncodingLatin1SpecialCharToUtf8(): void
    {
        if (!function_exists('iconv')) {
            $this->markTestSkipped('iconv extension not available');
        }

        // chr(0xE9) is e-acute in ISO-8859-1
        $latin1Text = chr(0xE9);
        $result = XoopsLocalAbstract::convert_encoding($latin1Text, 'UTF-8', 'ISO-8859-1');
        // UTF-8 encoding of e-acute is \xC3\xA9
        $this->assertSame("\xC3\xA9", $result);
    }

    // ---------------------------------------------------------------
    //  utf8_encode() tests — non-multibyte path
    // ---------------------------------------------------------------

    /**
     * With XOOPS_USE_MULTIBYTES=0, utf8_encode() should fall through to
     * PHP's utf8_encode() (or its polyfill). Pure ASCII input should pass
     * through unchanged.
     */
    public function testUtf8EncodeAsciiText(): void
    {
        $text = 'Hello World';
        $result = XoopsLocalAbstract::utf8_encode($text);
        $this->assertSame('Hello World', $result);
    }

    /**
     * Empty string should return empty string.
     */
    public function testUtf8EncodeEmptyString(): void
    {
        $result = XoopsLocalAbstract::utf8_encode('');
        $this->assertSame('', $result);
    }

    /**
     * Latin-1 encoded e-acute should be converted to UTF-8.
     */
    public function testUtf8EncodeLatin1Char(): void
    {
        // chr(0xE9) is e-acute in Latin-1
        $result = XoopsLocalAbstract::utf8_encode(chr(0xE9));
        // PHP's utf8_encode converts Latin-1 to UTF-8: \xC3\xA9
        $this->assertSame("\xC3\xA9", $result);
    }

    // ---------------------------------------------------------------
    //  utf8_decode() tests — non-multibyte path
    // ---------------------------------------------------------------

    /**
     * With XOOPS_USE_MULTIBYTES=0, utf8_decode() should fall through to
     * PHP's utf8_decode() (or its polyfill). Pure ASCII input should pass
     * through unchanged.
     */
    public function testUtf8DecodeAsciiText(): void
    {
        $text = 'Hello World';
        $result = XoopsLocalAbstract::utf8_decode($text);
        $this->assertSame('Hello World', $result);
    }

    /**
     * Empty string should return empty string.
     */
    public function testUtf8DecodeEmptyString(): void
    {
        $result = XoopsLocalAbstract::utf8_decode('');
        $this->assertSame('', $result);
    }

    /**
     * UTF-8 encoded e-acute should be decoded back to Latin-1.
     */
    public function testUtf8DecodeUtf8Char(): void
    {
        // \xC3\xA9 is e-acute in UTF-8
        $result = XoopsLocalAbstract::utf8_decode("\xC3\xA9");
        // PHP's utf8_decode converts UTF-8 to Latin-1: chr(0xE9)
        $this->assertSame(chr(0xE9), $result);
    }

    // ---------------------------------------------------------------
    //  number_format() tests
    // ---------------------------------------------------------------

    /**
     * The base implementation returns the number unchanged.
     */
    public function testNumberFormatReturnsNumberUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(42, $local->number_format(42));
    }

    /**
     * Float values should also be returned unchanged.
     */
    public function testNumberFormatReturnsFloatUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(3.14, $local->number_format(3.14));
    }

    /**
     * String numeric value should be returned unchanged.
     */
    public function testNumberFormatReturnsStringNumberUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame('1000', $local->number_format('1000'));
    }

    /**
     * Zero should be returned unchanged.
     */
    public function testNumberFormatReturnsZeroUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(0, $local->number_format(0));
    }

    /**
     * Negative number should be returned unchanged.
     */
    public function testNumberFormatReturnsNegativeUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(-99, $local->number_format(-99));
    }

    // ---------------------------------------------------------------
    //  money_format() tests
    // ---------------------------------------------------------------

    /**
     * The base implementation returns the number (second argument) unchanged.
     */
    public function testMoneyFormatReturnsNumberUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(100, $local->money_format('%.2n', 100));
    }

    /**
     * Float money value should be returned unchanged.
     */
    public function testMoneyFormatReturnsFloatUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(49.99, $local->money_format('%.2n', 49.99));
    }

    /**
     * The format string is ignored in the base implementation.
     */
    public function testMoneyFormatIgnoresFormatString(): void
    {
        $local = new XoopsLocalAbstract();
        $result1 = $local->money_format('%.2n', 100);
        $result2 = $local->money_format('%i', 100);
        $this->assertSame($result1, $result2);
    }

    /**
     * Zero value for money should be returned unchanged.
     */
    public function testMoneyFormatReturnsZeroUnchanged(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame(0, $local->money_format('%.2n', 0));
    }

    // ---------------------------------------------------------------
    //  __call() tests — magic method dispatcher
    // ---------------------------------------------------------------

    /**
     * Calling an existing PHP function like strtolower via __call should
     * invoke it and return the correct result.
     */
    public function testCallExistingFunctionStrtolower(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->strtolower('HELLO');
        $this->assertSame('hello', $result);
    }

    /**
     * Calling strtoupper via __call should work.
     */
    public function testCallExistingFunctionStrtoupper(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->strtoupper('hello');
        $this->assertSame('HELLO', $result);
    }

    /**
     * Calling strlen via __call should return the string length.
     */
    public function testCallExistingFunctionStrlen(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->strlen('Hello');
        $this->assertSame(5, $result);
    }

    /**
     * Calling a function with multiple arguments via __call.
     */
    public function testCallExistingFunctionWithMultipleArgs(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->str_repeat('ab', 3);
        $this->assertSame('ababab', $result);
    }

    /**
     * Calling a non-existent function via __call should return null.
     */
    public function testCallNonExistentFunctionReturnsNull(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->totally_nonexistent_function_xyz_12345('test');
        $this->assertNull($result);
    }

    /**
     * Calling another non-existent function without arguments returns null.
     */
    public function testCallNonExistentFunctionNoArgsReturnsNull(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->bogus_function_that_does_not_exist();
        $this->assertNull($result);
    }

    /**
     * Calling ucfirst via __call should capitalize the first character.
     */
    public function testCallExistingFunctionUcfirst(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->ucfirst('hello world');
        $this->assertSame('Hello world', $result);
    }

    // ---------------------------------------------------------------
    //  getTimeFormatDesc() tests
    // ---------------------------------------------------------------

    /**
     * getTimeFormatDesc() should return the _TIMEFORMAT_DESC constant.
     */
    public function testGetTimeFormatDescReturnsConstant(): void
    {
        $result = XoopsLocalAbstract::getTimeFormatDesc();
        $this->assertSame(_TIMEFORMAT_DESC, $result);
    }

    /**
     * The return value should be a non-empty string.
     */
    public function testGetTimeFormatDescReturnsNonEmptyString(): void
    {
        $result = XoopsLocalAbstract::getTimeFormatDesc();
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    // ---------------------------------------------------------------
    //  formatTimestamp() tests — RSS format only
    // ---------------------------------------------------------------

    /**
     * The 'rss' format should produce an RFC 2822-style date string
     * using gmdate (GMT), without depending on user timezone or global
     * configs beyond server_TZ.
     */
    public function testFormatTimestampRssFormatWithoutServerTz(): void
    {
        // Ensure server_TZ is not set so the TIME_ZONE suffix is empty
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = [];

        $timestamp = 1000000000; // Sun, 09 Sep 2001 01:46:40 GMT
        $result = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');

        // Should be the gmdate output without timezone suffix
        $expected = gmdate('D, d M Y H:i:s', 1000000000);
        $this->assertSame($expected, $result);

        // Restore
        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * The 'r' alias should produce the same output as 'rss'.
     */
    public function testFormatTimestampRAlias(): void
    {
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = [];

        $timestamp = 1000000000;
        $rssResult = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');
        $rResult   = XoopsLocalAbstract::formatTimestamp($timestamp, 'r');

        $this->assertSame($rssResult, $rResult);

        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * The 'RSS' format (uppercase) should work identically since
     * formatTimestamp lowercases the format string.
     */
    public function testFormatTimestampRssIsCaseInsensitive(): void
    {
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = [];

        $timestamp = 1000000000;
        $lower = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');
        $upper = XoopsLocalAbstract::formatTimestamp($timestamp, 'RSS');

        $this->assertSame($lower, $upper);

        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * RSS format with a positive server_TZ should append a positive timezone offset.
     */
    public function testFormatTimestampRssWithPositiveServerTz(): void
    {
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = ['server_TZ' => 5.0];

        $timestamp = 1000000000;
        $result = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');

        // Should contain ' +' prefix for positive timezone
        $this->assertStringContainsString(' +', $result);
        // The base date should still be gmdate
        $baseDate = gmdate('D, d M Y H:i:s', 1000000000);
        $this->assertStringStartsWith($baseDate, $result);

        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * RSS format with a negative server_TZ should append a negative timezone offset.
     */
    public function testFormatTimestampRssWithNegativeServerTz(): void
    {
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = ['server_TZ' => -5.0];

        $timestamp = 1000000000;
        $result = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');

        // Should contain ' -' prefix for negative timezone
        $this->assertStringContainsString(' -', $result);

        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * RSS format with server_TZ = 0 should append ' +0000'.
     */
    public function testFormatTimestampRssWithZeroServerTz(): void
    {
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = ['server_TZ' => 0];

        $timestamp = 1000000000;
        $result = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');

        // TZ=0 -> abs(0)=0, prefix=' +', date('Hi', 0) = '0000' (on UTC)
        // The exact output depends on PHP's date() with epoch 0
        // But since server_TZ >= 0, prefix is ' +'
        $this->assertStringContainsString(' +', $result);

        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * RSS format with a known timestamp should produce the expected output.
     */
    #[DataProvider('rssTimestampProvider')]
    public function testFormatTimestampRssKnownDates(int $timestamp, string $expectedBase): void
    {
        $originalConfig = isset($GLOBALS['xoopsConfig']) ? $GLOBALS['xoopsConfig'] : null;
        $GLOBALS['xoopsConfig'] = [];

        $result = XoopsLocalAbstract::formatTimestamp($timestamp, 'rss');
        $this->assertSame($expectedBase, $result);

        if ($originalConfig !== null) {
            $GLOBALS['xoopsConfig'] = $originalConfig;
        } else {
            unset($GLOBALS['xoopsConfig']);
        }
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function rssTimestampProvider(): array
    {
        return [
            'unix epoch' => [0, gmdate('D, d M Y H:i:s', 0)],
            'Y2K' => [946684800, gmdate('D, d M Y H:i:s', 946684800)],
            'billion seconds' => [1000000000, gmdate('D, d M Y H:i:s', 1000000000)],
        ];
    }

    // ---------------------------------------------------------------
    //  Edge case and integration tests
    // ---------------------------------------------------------------

    /**
     * The class should be instantiable (needed for instance methods like
     * number_format, money_format, __call).
     */
    public function testClassIsInstantiable(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertInstanceOf(XoopsLocalAbstract::class, $local);
    }

    /**
     * Static methods should be callable without instantiation.
     */
    public function testStaticMethodsCallableWithoutInstance(): void
    {
        // Just verify these don't throw errors
        $this->assertIsString(XoopsLocalAbstract::trim('test'));
        $this->assertIsString(XoopsLocalAbstract::getTimeFormatDesc());
    }

    /**
     * substr() with a single-character string and length=1 should return
     * that character without trimmarker.
     */
    public function testSubstrSingleCharFitsExactly(): void
    {
        $result = XoopsLocalAbstract::substr('X', 0, 1);
        $this->assertSame('X', $result);
    }

    /**
     * convert_encoding() with the integer 0 (falsy but not empty string)
     * should still be treated as empty by the empty() check and returned as-is.
     */
    public function testConvertEncodingFalsyZeroReturnsAsIs(): void
    {
        $result = XoopsLocalAbstract::convert_encoding(0);
        $this->assertSame(0, $result);
    }

    /**
     * convert_encoding() with boolean false (empty check) returns as-is.
     */
    public function testConvertEncodingFalseReturnsAsIs(): void
    {
        $result = XoopsLocalAbstract::convert_encoding(false);
        $this->assertFalse($result);
    }

    /**
     * Multiple __call invocations on the same instance should all work
     * independently.
     */
    public function testCallMultipleInvocations(): void
    {
        $local = new XoopsLocalAbstract();
        $this->assertSame('hello', $local->strtolower('HELLO'));
        $this->assertSame('WORLD', $local->strtoupper('world'));
        $this->assertSame(3, $local->strlen('abc'));
    }

    /**
     * __call with a function that returns a non-string type.
     */
    public function testCallFunctionReturningArray(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->array_reverse([1, 2, 3]);
        $this->assertSame([3, 2, 1], $result);
    }

    /**
     * __call with a function that returns boolean.
     */
    public function testCallFunctionReturningBool(): void
    {
        $local = new XoopsLocalAbstract();
        $result = $local->is_string('test');
        $this->assertTrue($result);
    }
}
