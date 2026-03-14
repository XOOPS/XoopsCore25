<?php
declare(strict_types=1);

namespace modulescripts;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Verify that module update/install scripts follow XOOPS 2.5.12 conventions.
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

    /**
     * Run all convention checks against a single script in one pass.
     */
    #[Test]
    #[DataProvider('scriptProvider')]
    public function scriptFollowsConventions(string $script): void
    {
        $path   = XOOPS_ROOT_PATH . $script;
        $source = file_get_contents($path);
        $this->assertNotFalse($source, 'Unable to read: ' . $path);

        // No deprecated queryF()
        $this->assertDoesNotMatchRegularExpression(
            '/->queryF\s*\(/', $source, "$script must not use deprecated queryF()"
        );

        // No @ error suppression
        $this->assertDoesNotMatchRegularExpression(
            '/(?<!\* )@[a-zA-Z_]\w*\s*\(/', $source, "$script must not use @ error suppression"
        );

        // No deprecated quoteString()
        $this->assertDoesNotMatchRegularExpression(
            '/->quoteString\s*\(/', $source, "$script must not use deprecated quoteString()"
        );

        // isResultSet() guard before every fetch call
        $fetchCount = preg_match_all('/->fetch(Array|Row|Both)\s*\(/', $source);
        if ($fetchCount > 0) {
            $guardCount = preg_match_all('/isResultSet\s*\(\s*\$result\s*\)/', $source);
            $this->assertGreaterThanOrEqual(
                $fetchCount, $guardCount,
                "$script must have at least one isResultSet() guard per fetch block"
            );
            $this->assertMatchesRegularExpression(
                '/isResultSet\s*\(\s*\$result\s*\).*!\s*\(\s*\$result\s+instanceof\s+\\\\mysqli_result\s*\)/s',
                $source,
                "$script must combine isResultSet() with instanceof \\mysqli_result"
            );
        }

        // is_file() guard before every unlink()
        $totalUnlinks = preg_match_all('/\bunlink\s*\(/', $source);
        if ($totalUnlinks > 0) {
            $guardedUnlinks = preg_match_all(
                '/\bis_file\s*\([^)]*\).*?\bunlink\s*\(/s', $source
            );
            $this->assertSame(
                $totalUnlinks, $guardedUnlinks,
                "$script: every unlink() must be preceded by an is_file() guard"
            );
        }
    }
}
