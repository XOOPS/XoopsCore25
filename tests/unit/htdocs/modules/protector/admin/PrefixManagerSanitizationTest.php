<?php

declare(strict_types=1);

namespace modulesprotector\admin;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for M-3: getCmd() lowercases case-sensitive DB prefix values (prefix_manager.php).
 *
 * The protector prefix manager previously used getCmd() which lowercases output,
 * but DB table prefixes can contain uppercase letters. The fix uses getString()
 * with manual sanitization via preg_replace('/[^a-zA-Z0-9_\-]/', '', $value).
 */
class PrefixManagerSanitizationTest extends TestCase
{
    /**
     * Apply the same sanitization used in the fixed prefix_manager.php.
     */
    private function sanitizePrefix(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $value);
    }

    #[Test]
    public function preservesUppercaseLetters(): void
    {
        $this->assertSame('MyPrefix', $this->sanitizePrefix('MyPrefix'));
    }

    #[Test]
    public function preservesLowercaseLetters(): void
    {
        $this->assertSame('xoops', $this->sanitizePrefix('xoops'));
    }

    #[Test]
    public function preservesMixedCase(): void
    {
        $this->assertSame('XoOps_Test', $this->sanitizePrefix('XoOps_Test'));
    }

    #[Test]
    public function preservesUnderscores(): void
    {
        $this->assertSame('my_prefix_2', $this->sanitizePrefix('my_prefix_2'));
    }

    #[Test]
    public function preservesNumbers(): void
    {
        $this->assertSame('prefix123', $this->sanitizePrefix('prefix123'));
    }

    #[Test]
    public function stripsSpecialCharacters(): void
    {
        $this->assertSame('prefix', $this->sanitizePrefix('pre!fix'));
    }

    #[Test]
    public function stripsSpaces(): void
    {
        $this->assertSame('myprefix', $this->sanitizePrefix('my prefix'));
    }

    #[Test]
    public function stripsSqlInjectionChars(): void
    {
        // Letters, numbers, and hyphens are preserved; other special chars are stripped
        $this->assertSame('xoopsDROPTABLE--', $this->sanitizePrefix("xoops'; DROP TABLE --"));
    }

    #[Test]
    public function preservesHyphens(): void
    {
        // Hyphens are valid in MySQL table prefixes and are allowed by
        // PREFIX_INVALID_CHAR_PATTERN, so the sanitization must preserve them.
        $this->assertSame('my-prefix', $this->sanitizePrefix('my-prefix'));
    }

    #[Test]
    public function emptyStringRemainsEmpty(): void
    {
        $this->assertSame('', $this->sanitizePrefix(''));
    }

    public static function validPrefixProvider(): array
    {
        return [
            'simple lowercase'     => ['xoops', 'xoops'],
            'with underscore'      => ['xoops_test', 'xoops_test'],
            'uppercase'            => ['XOOPS', 'XOOPS'],
            'mixed case'           => ['XoopsDB', 'XoopsDB'],
            'numbers'              => ['x2prefix3', 'x2prefix3'],
            'underscore separated' => ['my_Db_prefix', 'my_Db_prefix'],
        ];
    }

    #[Test]
    #[DataProvider('validPrefixProvider')]
    public function preservesValidPrefixes(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizePrefix($input));
    }

    #[Test]
    public function sourceFileUsesPregReplaceNotGetCmd(): void
    {
        $source = file_get_contents(
            XOOPS_PATH . '/modules/protector/admin/prefix_manager.php'
        );
        // The file should use preg_replace for sanitization, not getCmd
        $this->assertStringContainsString("preg_replace('/[^a-zA-Z0-9_\\-]/', ''", $source,
            'prefix_manager.php must use preg_replace for case-preserving sanitization');
        // Verify getCmd is NOT used for prefix values
        $this->assertStringNotContainsString('getCmd', $source,
            'prefix_manager.php must not use getCmd() which lowercases values');
    }

    #[Test]
    public function getCmdWouldLowercaseButSanitizeDoesNot(): void
    {
        // Demonstrate the problem: strtolower (what getCmd does) vs our fix
        $input = 'MyPrefix_Test';
        $getCmdResult = strtolower($input); // what getCmd would do
        $fixedResult = $this->sanitizePrefix($input);

        $this->assertSame('myprefix_test', $getCmdResult, 'getCmd lowercases');
        $this->assertSame('MyPrefix_Test', $fixedResult, 'Our fix preserves case');
        $this->assertNotSame($getCmdResult, $fixedResult, 'Results must differ for mixed case');
    }
}
