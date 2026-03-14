<?php

declare(strict_types=1);

namespace modulesprotector\admin;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for M-3: prefix_manager.php prefix validation.
 *
 * The protector prefix manager validates DB prefix input using
 * validatePrefix() which rejects (dies) on invalid characters
 * rather than silently stripping them. This prevents destructive
 * operations from targeting the wrong table set.
 */
class PrefixManagerSanitizationTest extends TestCase
{
    /** Same pattern used in prefix_manager.php */
    private const INVALID_CHAR_PATTERN = '/[^0-9A-Za-z_-]/';

    /**
     * Simulate validatePrefix(): return value if valid, null if it would die.
     */
    private function isValidPrefix(string $value): bool
    {
        return !preg_match(self::INVALID_CHAR_PATTERN, $value);
    }

    #[Test]
    public function acceptsUppercaseLetters(): void
    {
        $this->assertTrue($this->isValidPrefix('MyPrefix'));
    }

    #[Test]
    public function acceptsLowercaseLetters(): void
    {
        $this->assertTrue($this->isValidPrefix('xoops'));
    }

    #[Test]
    public function acceptsMixedCase(): void
    {
        $this->assertTrue($this->isValidPrefix('XoOps_Test'));
    }

    #[Test]
    public function acceptsUnderscores(): void
    {
        $this->assertTrue($this->isValidPrefix('my_prefix_2'));
    }

    #[Test]
    public function acceptsNumbers(): void
    {
        $this->assertTrue($this->isValidPrefix('prefix123'));
    }

    #[Test]
    public function acceptsHyphens(): void
    {
        $this->assertTrue($this->isValidPrefix('my-prefix'));
    }

    #[Test]
    public function rejectsSpecialCharacters(): void
    {
        $this->assertFalse($this->isValidPrefix('pre!fix'));
    }

    #[Test]
    public function rejectsSpaces(): void
    {
        $this->assertFalse($this->isValidPrefix('my prefix'));
    }

    #[Test]
    public function rejectsSqlInjectionChars(): void
    {
        $this->assertFalse($this->isValidPrefix("xoops'; DROP TABLE --"));
    }

    #[Test]
    public function acceptsEmptyString(): void
    {
        // Empty prefix is valid at the pattern level (copy action generates a random one)
        $this->assertTrue($this->isValidPrefix(''));
    }

    public static function validPrefixProvider(): array
    {
        return [
            'simple lowercase'     => ['xoops'],
            'with underscore'      => ['xoops_test'],
            'uppercase'            => ['XOOPS'],
            'mixed case'           => ['XoopsDB'],
            'numbers'              => ['x2prefix3'],
            'underscore separated' => ['my_Db_prefix'],
            'with hyphens'         => ['my-prefix-2'],
        ];
    }

    #[Test]
    #[DataProvider('validPrefixProvider')]
    public function acceptsValidPrefixes(string $input): void
    {
        $this->assertTrue($this->isValidPrefix($input));
    }

    public static function invalidPrefixProvider(): array
    {
        return [
            'exclamation'   => ['prod!old'],
            'semicolon'     => ["xoops';"],
            'space'         => ['my prefix'],
            'dot'           => ['xoops.test'],
            'at sign'       => ['prefix@db'],
            'backtick'      => ['prefix`test'],
        ];
    }

    #[Test]
    #[DataProvider('invalidPrefixProvider')]
    public function rejectsInvalidPrefixes(string $input): void
    {
        $this->assertFalse($this->isValidPrefix($input));
    }

    #[Test]
    public function sourceFileUsesValidatePrefixNotPregReplace(): void
    {
        $source = file_get_contents(
            XOOPS_PATH . '/modules/protector/admin/prefix_manager.php'
        );
        $this->assertNotFalse($source, 'Failed to read prefix_manager.php');

        // The file should use validatePrefix() helper, not inline preg_replace for prefix values
        $this->assertStringContainsString('validatePrefix(', $source,
            'prefix_manager.php must use validatePrefix() to reject invalid input');
        // Verify getCmd is NOT used for prefix values
        $this->assertStringNotContainsString('getCmd', $source,
            'prefix_manager.php must not use getCmd() which lowercases values');
        // Verify no inline preg_replace sanitization of prefix values remains
        $this->assertStringNotContainsString('preg_replace(PREFIX_INVALID_CHAR_PATTERN,', $source,
            'prefix_manager.php must not silently strip characters — use validatePrefix() to reject');
    }

    #[Test]
    public function getCmdWouldLowercaseButValidateDoesNot(): void
    {
        // Demonstrate the problem: strtolower (what getCmd does) loses case
        $input = 'MyPrefix_Test';
        $getCmdResult = strtolower($input);

        $this->assertSame('myprefix_test', $getCmdResult, 'getCmd lowercases');
        $this->assertTrue($this->isValidPrefix($input), 'validatePrefix accepts mixed case');
    }
}
