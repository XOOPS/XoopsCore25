<?php
/**
 * Tests for PM module install script.
 *
 * Validates that the install script follows XOOPS coding conventions:
 * - Uses isResultSet() before fetch calls
 * - Does not use deprecated queryF()
 * - Does not use @ error suppression
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\PM
 */

declare(strict_types=1);

namespace modulespm;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/system/SourceFileTestTrait.php';
use Tests\Unit\System\SourceFileTestTrait;

class PmInstallTest extends TestCase
{
    use SourceFileTestTrait;

    protected function setUp(): void
    {
        $this->loadSourceFile('htdocs/modules/pm/include/install.php');
    }

    #[Test]
    public function installScriptDoesNotUseQueryF(): void
    {
        $this->assertStringNotContainsString(
            '->queryF(',
            $this->sourceContent,
            'pm/include/install.php should not use deprecated queryF()'
        );
    }

    #[Test]
    public function installScriptDoesNotUseAtErrorSuppression(): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/@\s*(unlink|fopen|file_get_contents|mkdir|rmdir|rename|copy)\s*\(/',
            $this->sourceContent,
            'pm/include/install.php should not use @ error suppression'
        );
    }

    #[Test]
    public function installScriptUsesIsResultSetBeforeFetch(): void
    {
        $this->assertStringContainsString(
            'isResultSet($result)',
            $this->sourceContent,
            'pm/include/install.php must use isResultSet() before fetch calls'
        );
    }

    #[Test]
    public function installScriptDoesNotUseQuoteString(): void
    {
        $this->assertStringNotContainsString(
            '->quoteString(',
            $this->sourceContent,
            'pm/include/install.php should not use deprecated quoteString()'
        );
    }
}
