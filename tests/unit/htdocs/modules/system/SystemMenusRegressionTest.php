<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for the system menu feature.
 *
 * These tests verify critical behavioral contracts by inspecting
 * the source files directly. They guard against regressions in
 * schema conventions, security sanitization, and permission scoping.
 */
#[CoversNothing]
class SystemMenusRegressionTest extends TestCase
{
    private function readSourceFile(string $relativePath): string
    {
        $fullPath = XOOPS_ROOT_PATH . '/' . $relativePath;
        $this->assertFileExists($fullPath, "Source file not found: {$relativePath}");

        return file_get_contents($fullPath);
    }

    // ── Schema conventions (from GitHub comment on XoopsCore27 PR #21) ──

    #[Test]
    public function updateScriptUsesZeroAsRootParentSentinel(): void
    {
        $source = $this->readSourceFile('modules/system/include/update.php');

        $this->assertMatchesRegularExpression(
            '/items_pid.*INT\s+NOT\s+NULL\s+DEFAULT\s+0/i',
            $source,
            'CREATE TABLE must use 0 as root parent default, not NULL'
        );
        $this->assertStringContainsString(
            'SET `items_pid` = 0 WHERE `items_pid` IS NULL',
            $source,
            'Upgrade must normalize NULL parent IDs to 0'
        );
    }

    #[Test]
    public function updateScriptEnforcesNotNullOnAffixColumns(): void
    {
        $source = $this->readSourceFile('modules/system/include/update.php');

        foreach (['category_prefix', 'category_suffix', 'items_prefix', 'items_suffix'] as $col) {
            $this->assertMatchesRegularExpression(
                '/`' . $col . '`\s+TEXT\s+NOT\s+NULL/i',
                $source,
                "CREATE TABLE must define {$col} as TEXT NOT NULL"
            );
        }
    }

    #[Test]
    public function updateScriptScopesPermissionCleanupToSystemModule(): void
    {
        $source = $this->readSourceFile('modules/system/include/update.php');

        $this->assertStringContainsString(
            "gperm_name` IN ('menus_category_view', 'menus_items_view')",
            $source,
            'Permission cleanup must be scoped by name to menu permissions only'
        );
    }

    // ── Security: javascript: URL rejection ──

    #[Test]
    public function adminControllerRejectsJavascriptUrls(): void
    {
        $source = $this->readSourceFile('modules/system/admin/menus/main.php');

        $this->assertStringContainsString(
            'javascript',
            $source,
            'Admin controller must check for javascript: protocol'
        );
    }

    #[Test]
    public function updateScriptMigratesUnsafeJavascriptUrls(): void
    {
        $source = $this->readSourceFile('modules/system/include/update.php');

        $this->assertStringContainsString(
            "items_url` LIKE 'javascript:%'",
            $source,
            'Upgrade must migrate javascript: URLs to safe values'
        );
    }

    // ── Security: affix sanitization order ──

    #[Test]
    public function themeAffixRendererExpandsPlaceholderBeforeSanitizing(): void
    {
        $source = $this->readSourceFile('class/theme.php');

        $inboxPos = strpos($source, 'xoInboxCount');
        $stripPos = strpos($source, 'strip_tags');

        $this->assertNotFalse($inboxPos, 'theme.php must handle xoInboxCount');
        $this->assertNotFalse($stripPos, 'theme.php must call strip_tags');
        $this->assertLessThan(
            $stripPos,
            $inboxPos,
            'xoInboxCount expansion must happen BEFORE strip_tags sanitization'
        );
    }

    #[Test]
    public function themeAffixRendererStripsEventHandlers(): void
    {
        $source = $this->readSourceFile('class/theme.php');

        $this->assertMatchesRegularExpression(
            '/on\\\\w\+/i',
            $source,
            'Affix renderer must strip on* event handler attributes'
        );
    }

    // ── Toolbar safety ──

    #[Test]
    public function updateScriptSeedsToolbarWithSafeFragmentUrl(): void
    {
        $source = $this->readSourceFile('modules/system/include/update.php');

        $this->assertStringContainsString(
            '#xswatch-toolbar-toggle',
            $source,
            'Toolbar seed must use fragment URL, not javascript:'
        );
    }

    // ── Disabled item guards ──

    #[Test]
    public function adminControllerBlocksEditingInactiveItems(): void
    {
        $source = $this->readSourceFile('modules/system/admin/menus/main.php');

        $this->assertStringContainsString(
            '_AM_SYSTEM_MENUS_ERROR_ITEMEDIT',
            $source,
            'Admin controller must block editing inactive items'
        );
    }

    #[Test]
    public function adminControllerBlocksDeletingInactiveItems(): void
    {
        $source = $this->readSourceFile('modules/system/admin/menus/main.php');

        $this->assertStringContainsString(
            '_AM_SYSTEM_MENUS_ERROR_ITEMDISABLE',
            $source,
            'Admin controller must block deleting inactive items'
        );
    }
}
