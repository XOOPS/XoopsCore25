<?php
/**
 * Tests for Profile module update script.
 *
 * Validates that the update script follows XOOPS coding conventions:
 * - Uses isResultSet() before fetch calls
 * - Does not use deprecated queryF()
 * - Does not use @ error suppression
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\Profile
 */

declare(strict_types=1);

namespace modulesprofile;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/system/SourceFileTestTrait.php';
use Tests\Unit\System\SourceFileTestTrait;

class ProfileUpdateTest extends TestCase
{
    use SourceFileTestTrait;

    protected function setUp(): void
    {
        $this->loadSourceFile('htdocs/modules/profile/include/update.php');
    }

    #[Test]
    public function updateScriptDoesNotUseQueryF(): void
    {
        $this->assertStringNotContainsString(
            '->queryF(',
            $this->sourceContent,
            'profile/include/update.php should not use deprecated queryF()'
        );
    }

    #[Test]
    public function updateScriptDoesNotUseAtErrorSuppression(): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/@\s*(unlink|fopen|file_get_contents|mkdir|rmdir|rename|copy)\s*\(/',
            $this->sourceContent,
            'profile/include/update.php should not use @ error suppression'
        );
    }

    #[Test]
    public function updateScriptUsesIsResultSetBeforeFetch(): void
    {
        $this->assertStringContainsString(
            'isResultSet($result)',
            $this->sourceContent,
            'profile/include/update.php must use isResultSet() before fetch calls'
        );
    }

    #[Test]
    public function updateScriptDoesNotUseQuoteString(): void
    {
        $this->assertStringNotContainsString(
            '->quoteString(',
            $this->sourceContent,
            'profile/include/update.php should not use deprecated quoteString()'
        );
    }

    #[Test]
    public function updateScriptUsesIsFileBeforeUnlink(): void
    {
        if (str_contains($this->sourceContent, 'unlink(')) {
            $this->assertStringContainsString(
                'is_file(',
                $this->sourceContent,
                'profile/include/update.php should use is_file() guard before unlink()'
            );
        } else {
            $this->assertTrue(true, 'No unlink() calls found');
        }
    }
}
