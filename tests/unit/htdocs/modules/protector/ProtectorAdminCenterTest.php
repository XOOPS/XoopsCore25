<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Protector admin center.php exec() error handling.
 *
 * These tests verify that the deleteall and compactlog actions
 * correctly check exec() return values and show appropriate
 * error messages on failure.
 */
class ProtectorAdminCenterTest extends TestCase
{
    /**
     * Verify the _AM_MSG_DELFAILED language constant exists.
     */
    #[Test]
    public function delFailedConstantIsDefined(): void
    {
        // Load the language file
        $langFile = XOOPS_PATH . '/modules/protector/language/english/admin.php';
        if (!defined('_AM_MSG_DELFAILED')) {
            require_once $langFile;
        }
        $this->assertTrue(defined('_AM_MSG_DELFAILED'));
        $this->assertNotEmpty(constant('_AM_MSG_DELFAILED'));
    }

    /**
     * Verify the _AM_MSG_REMOVED language constant still exists.
     */
    #[Test]
    public function removedConstantIsDefined(): void
    {
        $langFile = XOOPS_PATH . '/modules/protector/language/english/admin.php';
        if (!defined('_AM_MSG_REMOVED')) {
            require_once $langFile;
        }
        $this->assertTrue(defined('_AM_MSG_REMOVED'));
        $this->assertNotEmpty(constant('_AM_MSG_REMOVED'));
    }

    /**
     * Verify that center.php source contains exec() return checks for deleteall.
     */
    #[Test]
    public function deleteallActionChecksExecReturn(): void
    {
        $source = file_get_contents(
            XOOPS_PATH . '/modules/protector/admin/center.php'
        );

        // The deleteall block should check exec() return with if()
        $this->assertStringContainsString(
            'if ($db->exec("DELETE FROM $log_table"))',
            $source,
            'deleteall action must check exec() return value'
        );

        // Should show error message on failure
        $this->assertStringContainsString(
            '_AM_MSG_DELFAILED',
            $source,
            'deleteall action must use _AM_MSG_DELFAILED on failure'
        );
    }

    /**
     * Verify that center.php source contains exec() return checks for compactlog.
     */
    #[Test]
    public function compactlogActionChecksExecReturn(): void
    {
        $source = file_get_contents(
            XOOPS_PATH . '/modules/protector/admin/center.php'
        );

        // The compactlog block should check exec() return with if(!...)
        $this->assertStringContainsString(
            'if (!$db->exec("DELETE FROM $log_table WHERE lid IN (',
            $source,
            'compactlog action must check exec() return value'
        );
    }

    /**
     * Verify that deleteall action does NOT silently redirect on failure.
     * The source should have conditional redirect, not unconditional.
     */
    #[Test]
    public function deleteallDoesNotSilentlyRedirectOnFailure(): void
    {
        $source = file_get_contents(
            XOOPS_PATH . '/modules/protector/admin/center.php'
        );

        // Find the deleteall block - exec should be inside an if condition
        // NOT as a bare statement followed by unconditional redirect
        $pattern = '/deleteall.*?\$db->exec\([^)]+\);\s*\n\s*redirect_header/s';
        $this->assertDoesNotMatchRegularExpression(
            $pattern,
            $source,
            'deleteall must not have bare exec() followed by unconditional redirect'
        );
    }
}
