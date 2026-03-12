<?php
/**
 * Tests for system module update script.
 *
 * Validates that the update script follows XOOPS coding conventions:
 * - Uses isResultSet() before fetch calls
 * - Does not use deprecated queryF()
 * - Does not use @ error suppression
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\System
 */

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/SourceFileTestTrait.php';
use Tests\Unit\System\SourceFileTestTrait;

class SystemUpdateTest extends TestCase
{
    use SourceFileTestTrait;

    protected function setUp(): void
    {
        $this->loadSourceFile('htdocs/modules/system/include/update.php');
    }

    #[Test]
    public function updateScriptDoesNotUseQueryF(): void
    {
        $this->assertStringNotContainsString(
            '->queryF(',
            $this->sourceContent,
            'system/include/update.php should not use deprecated queryF()'
        );
    }

    #[Test]
    public function updateScriptDoesNotUseAtErrorSuppression(): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/@\s*(unlink|fopen|file_get_contents|mkdir|rmdir|rename|copy)\s*\(/',
            $this->sourceContent,
            'system/include/update.php should not use @ error suppression'
        );
    }

    #[Test]
    public function updateScriptUsesIsResultSetBeforeFetchArray(): void
    {
        // Every fetchArray() call should be preceded by an isResultSet() check
        // in the same function scope
        $this->assertStringContainsString(
            'isResultSet($result)',
            $this->sourceContent,
            'system/include/update.php must use isResultSet() before fetchArray()'
        );
    }

    #[Test]
    public function updateScriptUsesProperResultSetCheckPattern(): void
    {
        // The SHOW INDEX query should use isResultSet() + instanceof guard
        $this->assertMatchesRegularExpression(
            '/isResultSet\(\$result\)\s*\|\|\s*!\$result\s+instanceof\s+\\\\mysqli_result/',
            $this->sourceContent,
            'system/include/update.php must use isResultSet() || !$result instanceof \\mysqli_result pattern'
        );
    }

    #[Test]
    public function updateScriptDoesNotUseQuoteString(): void
    {
        $this->assertStringNotContainsString(
            '->quoteString(',
            $this->sourceContent,
            'system/include/update.php should not use deprecated quoteString()'
        );
    }
}
