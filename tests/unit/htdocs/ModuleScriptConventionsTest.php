<?php
declare(strict_types=1);

namespace modulescripts;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Verify that module update/install scripts follow XOOPS 2.5.12 conventions:
 * - No deprecated queryF() or quoteString() calls
 * - No @ error suppression
 * - isResultSet() + instanceof guard before fetchArray/fetchRow/fetchBoth
 * - is_file() guard before every unlink()
 */
#[CoversNothing]
class ModuleScriptConventionsTest extends TestCase
{
    /** Patterns that must not appear in any module script. */
    private const FORBIDDEN_PATTERNS = [
        '/->queryF\s*\(/'                 => 'must not use deprecated queryF()',
        '/(?<!\* )@[a-zA-Z_]\w*\s*\(/'   => 'must not use @ error suppression on function calls',
        '/->quoteString\s*\(/'            => 'must not use deprecated quoteString()',
    ];

    public static function scriptProvider(): array
    {
        return [
            'system/include/update.php' => ['/modules/system/include/update.php'],
            'pm/include/update.php'     => ['/modules/pm/include/update.php'],
            'pm/include/install.php'    => ['/modules/pm/include/install.php'],
            'profile/include/update.php'=> ['/modules/profile/include/update.php'],
        ];
    }

    private function readScript(string $relativePath): string
    {
        $path = XOOPS_ROOT_PATH . $relativePath;
        $source = file_get_contents($path);
        $this->assertNotFalse($source, 'Unable to read: ' . $path);
        return $source;
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function noForbiddenPatterns(string $script): void
    {
        $source = $this->readScript($script);

        foreach (self::FORBIDDEN_PATTERNS as $pattern => $message) {
            $this->assertDoesNotMatchRegularExpression($pattern, $source, "$script $message");
        }
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function fetchCallsHaveProperResultSetGuards(string $script): void
    {
        $source = $this->readScript($script);

        $fetchCount = preg_match_all('/->fetch(Array|Row|Both)\s*\(/', $source);
        if ($fetchCount === 0) {
            $this->assertTrue(true, 'No fetch calls found');
            return;
        }

        // Every fetch block must be preceded by an isResultSet() guard
        $guardCount = preg_match_all('/isResultSet\s*\(\s*\$result\s*\)/', $source);
        $this->assertGreaterThanOrEqual(
            $fetchCount,
            $guardCount,
            "$script must have at least one isResultSet() guard per fetch block"
        );

        // Guard must also include instanceof \mysqli_result for Scrutinizer
        $this->assertMatchesRegularExpression(
            '/isResultSet\s*\(\s*\$result\s*\).*!\s*\(\s*\$result\s+instanceof\s+\\\\mysqli_result\s*\)/s',
            $source,
            "$script must combine isResultSet() with instanceof \\mysqli_result"
        );
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function everyUnlinkIsGuardedByIsFile(string $script): void
    {
        $source = $this->readScript($script);

        $totalUnlinks = preg_match_all('/\bunlink\s*\(/', $source);
        if ($totalUnlinks === 0) {
            $this->assertTrue(true, 'No unlink() calls found');
            return;
        }

        // Match if (is_file(...)) { ... unlink(...) ... } pattern
        $guardedUnlinks = preg_match_all(
            '/if\s*\([^)]*\bis_file\s*\([^)]*\)[^)]*\)\s*\{[^}]*\bunlink\s*\(/s',
            $source
        );

        $this->assertSame(
            $totalUnlinks,
            $guardedUnlinks,
            "$script: every unlink() must be wrapped in an if (is_file(...)) guard"
        );
    }
}
