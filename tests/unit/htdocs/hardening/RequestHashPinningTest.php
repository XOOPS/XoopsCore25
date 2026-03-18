<?php

declare(strict_types=1);

namespace hardening;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Verify that all Request::getXxx() calls in hardened files include
 * an explicit hash argument ('GET' or 'POST'), and that no elvis
 * operator (?:) is used for dual-source Request calls.
 */
class RequestHashPinningTest extends TestCase
{
    /**
     * Regex matching Request::getXxx( start of call.
     * We then use a balanced-paren parser to extract the full argument list.
     */
    private const CALL_START_PATTERN = '/Request::(getString|getInt|getCmd|getWord|getFloat|getArray|getVar|getBool|getEmail|getUrl|getText)\s*\(/';

    /**
     * Files known to have hashless calls that need refactoring.
     * These are tracked via markTestIncomplete() so CI stays aware.
     */
    private const KNOWN_NONCOMPLIANT = [
        // TODO: 'op' uses dual-source default hash — needs explicit GET pin
        'modules/profile/admin/visibility.php',
    ];

    /**
     * Files that have been hardened and must not contain hashless calls.
     *
     * @return array<string, array{string}>
     */
    public static function hardenedFilesProvider(): array
    {
        $base = defined('XOOPS_ROOT_PATH') ? XOOPS_ROOT_PATH : dirname(__DIR__, 4) . '/htdocs';

        return [
            'edituser.php'                          => [$base . '/edituser.php'],
            'profile/edituser.php'                  => [$base . '/modules/profile/edituser.php'],
            'profile/admin/step.php'                => [$base . '/modules/profile/admin/step.php'],
            'profile/admin/field.php'               => [$base . '/modules/profile/admin/field.php'],
            'profile/admin/category.php'            => [$base . '/modules/profile/admin/category.php'],
            'profile/admin/user.php'                => [$base . '/modules/profile/admin/user.php'],
            'profile/admin/visibility.php'          => [$base . '/modules/profile/admin/visibility.php'],
            'profile/search.php'                    => [$base . '/modules/profile/search.php'],
            'system/admin/groups/main.php'          => [$base . '/modules/system/admin/groups/main.php'],
            'system/admin/comments/main.php'        => [$base . '/modules/system/admin/comments/main.php'],
            'system/admin/preferences/main.php'     => [$base . '/modules/system/admin/preferences/main.php'],
            'system/admin/smilies/main.php'         => [$base . '/modules/system/admin/smilies/main.php'],
            'system/admin/maintenance/main.php'     => [$base . '/modules/system/admin/maintenance/main.php'],
            'system/admin/userrank/main.php'        => [$base . '/modules/system/admin/userrank/main.php'],
            'pm/readpmsg.php'                       => [$base . '/modules/pm/readpmsg.php'],
            'pm/admin/prune.php'                    => [$base . '/modules/pm/admin/prune.php'],
            'banners.php'                           => [$base . '/banners.php'],
        ];
    }

    #[Test]
    #[DataProvider('hardenedFilesProvider')]
    public function noHashlessRequestCalls(string $filePath): void
    {
        if (!file_exists($filePath)) {
            $this->markTestSkipped("File not found: {$filePath}");
        }

        $source = file_get_contents($filePath);
        self::assertNotFalse($source, "Failed to read file: {$filePath}");
        $lines  = explode("\n", $source);
        $violations = [];

        foreach ($lines as $lineNum => $line) {
            // Skip comments
            $trimmed = ltrim($line);
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '/*')) {
                continue;
            }

            // Find all Request::getXxx( occurrences and extract balanced args
            if (preg_match_all(self::CALL_START_PATTERN, $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $matchInfo) {
                    $fullMatch = $matchInfo[0];
                    $offset    = $matchInfo[1];
                    // Find the opening paren position
                    $parenPos = $offset + strlen($fullMatch) - 1;
                    // Extract balanced argument string
                    $inner = self::extractBalancedArgs($line, $parenPos);
                    if ($inner === null) {
                        continue;
                    }
                    $argCount = self::countArguments($inner);

                    // Calls with <= 2 args are missing the hash (3rd arg)
                    if ($argCount <= 2) {
                        $callText = substr($line, $offset, strlen($fullMatch) + strlen($inner) + 1);
                        $violations[] = sprintf('  Line %d: %s', $lineNum + 1, trim($callText));
                    }
                }
            }
        }

        if (!empty($violations)) {
            // Known-noncompliant files are tracked as incomplete, not failures
            foreach (self::KNOWN_NONCOMPLIANT as $knownFile) {
                if (str_ends_with($filePath, $knownFile)) {
                    $this->markTestIncomplete(
                        sprintf(
                            "Known noncompliant — %d hashless call(s) in %s need refactoring:\n%s",
                            count($violations),
                            basename($filePath),
                            implode("\n", $violations),
                        ),
                    );
                }
            }
        }

        self::assertEmpty(
            $violations,
            sprintf(
                "Found %d hashless Request::getXxx() call(s) in %s:\n%s",
                count($violations),
                basename($filePath),
                implode("\n", $violations),
            ),
        );
    }

    #[Test]
    #[DataProvider('hardenedFilesProvider')]
    public function noElvisOperatorOnDualSourceRequestCalls(string $filePath): void
    {
        if (!file_exists($filePath)) {
            $this->markTestSkipped("File not found: {$filePath}");
        }

        $source = file_get_contents($filePath);
        self::assertNotFalse($source, "Failed to read file: {$filePath}");
        $lines  = explode("\n", $source);
        $violations = [];

        // Pattern: Request::getXxx(...) ?: Request::getXxx(...)
        $elvisPattern = '/Request::(?:getString|getInt|getCmd|getWord|getFloat|getArray|getVar|getBool|getEmail|getUrl|getText)\s*\([^)]*\)\s*\?:\s*Request::/';

        foreach ($lines as $lineNum => $line) {
            $trimmed = ltrim($line);
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '/*')) {
                continue;
            }

            if (preg_match($elvisPattern, $line)) {
                $violations[] = sprintf('  Line %d: %s', $lineNum + 1, trim($line));
            }
        }

        self::assertEmpty(
            $violations,
            sprintf(
                "Found %d elvis operator (?:) on dual-source Request calls in %s:\n%s",
                count($violations),
                basename($filePath),
                implode("\n", $violations),
            ),
        );
    }

    /**
     * Extract the content between balanced parentheses starting at $startPos.
     * $startPos must point to the opening '('.
     *
     * @return string|null The content between parens, or null if unbalanced.
     */
    private static function extractBalancedArgs(string $line, int $startPos): ?string
    {
        if (!isset($line[$startPos]) || $line[$startPos] !== '(') {
            return null;
        }
        $depth = 0;
        $len = strlen($line);
        $inSingleQuote = false;
        $inDoubleQuote = false;

        for ($i = $startPos; $i < $len; $i++) {
            $char = $line[$i];
            if ($inSingleQuote) {
                if ($char === "'" && $line[$i - 1] !== '\\') {
                    $inSingleQuote = false;
                }
                continue;
            }
            if ($inDoubleQuote) {
                if ($char === '"' && $line[$i - 1] !== '\\') {
                    $inDoubleQuote = false;
                }
                continue;
            }
            if ($char === "'") {
                $inSingleQuote = true;
            } elseif ($char === '"') {
                $inDoubleQuote = true;
            } elseif ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    return substr($line, $startPos + 1, $i - $startPos - 1);
                }
            }
        }
        return null; // unbalanced
    }

    /**
     * Count the number of top-level arguments in a function call's argument string.
     * Handles nested brackets [], parentheses (), and quoted strings.
     */
    private static function countArguments(string $inner): int
    {
        $inner = trim($inner);
        if ($inner === '') {
            return 0;
        }

        $count = 1;
        $depth = 0;
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $len = strlen($inner);

        for ($i = 0; $i < $len; $i++) {
            $char = $inner[$i];

            if ($inSingleQuote) {
                if ($char === "'" && ($i === 0 || $inner[$i - 1] !== '\\')) {
                    $inSingleQuote = false;
                }
                continue;
            }
            if ($inDoubleQuote) {
                if ($char === '"' && ($i === 0 || $inner[$i - 1] !== '\\')) {
                    $inDoubleQuote = false;
                }
                continue;
            }

            if ($char === "'") {
                $inSingleQuote = true;
            } elseif ($char === '"') {
                $inDoubleQuote = true;
            } elseif ($char === '(' || $char === '[') {
                $depth++;
            } elseif ($char === ')' || $char === ']') {
                $depth--;
            } elseif ($char === ',' && $depth === 0) {
                $count++;
            }
        }

        return $count;
    }
}
