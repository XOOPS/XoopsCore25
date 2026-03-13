<?php
declare(strict_types=1);

namespace modulescripts;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Verify that module update/install scripts follow XOOPS 2.5.12 conventions:
 * - No deprecated queryF() calls
 * - No @ error suppression
 * - No deprecated quoteString() calls
 * - isResultSet() guard before fetchArray/fetchRow/fetchBoth
 * - is_file() guard before every unlink()
 * - instanceof \mysqli_result used with isResultSet()
 */
#[CoversNothing]
class ModuleScriptConventionsTest extends TestCase
{
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
    public function noDeprecatedQueryF(string $script): void
    {
        $source = $this->readScript($script);
        $this->assertDoesNotMatchRegularExpression(
            '/->queryF\s*\(/',
            $source,
            "$script must not use deprecated queryF()"
        );
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function noErrorSuppression(string $script): void
    {
        $source = $this->readScript($script);
        $this->assertDoesNotMatchRegularExpression(
            '/@\s*(unlink|fopen|file_get_contents|mkdir|rmdir)\s*\(/',
            $source,
            "$script must not use @ error suppression"
        );
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function noDeprecatedQuoteString(string $script): void
    {
        $source = $this->readScript($script);
        $this->assertDoesNotMatchRegularExpression(
            '/->quoteString\s*\(/',
            $source,
            "$script must not use deprecated quoteString()"
        );
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function everyFetchHasResultSetGuard(string $script): void
    {
        $source = $this->readScript($script);

        // Count fetch calls
        $fetchCount = preg_match_all('/->fetch(Array|Row|Both)\s*\(/', $source);
        if ($fetchCount === 0) {
            $this->assertTrue(true, 'No fetch calls found');
            return;
        }

        // Count isResultSet guards
        $guardCount = preg_match_all('/isResultSet\s*\(\s*\$result\s*\)/', $source);
        $this->assertGreaterThanOrEqual(
            $fetchCount,
            $guardCount,
            "$script must have at least one isResultSet() guard per fetch block"
        );
    }

    #[Test]
    #[DataProvider('scriptProvider')]
    public function resultSetGuardIncludesInstanceof(string $script): void
    {
        $source = $this->readScript($script);

        if (!str_contains($source, 'isResultSet(')) {
            $this->assertTrue(true, 'No isResultSet() calls found');
            return;
        }

        // Accept flexible guard forms: isResultSet() combined with instanceof \mysqli_result
        $this->assertMatchesRegularExpression(
            '/isResultSet\s*\(\s*\$result\s*\).*\$result\s+instanceof\s+\\\\mysqli_result/s',
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
