<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for avatar delete error-handling (M-5).
 *
 * Verifies the delfileok case uses a sequential reset-then-delete approach
 * with compensating restore on failure (MyISAM tables do not support
 * real transactions).
 */
class AvatarDeleteTransactionTest extends TestCase
{
    private string $source;

    protected function setUp(): void
    {
        $this->source = file_get_contents(
            XOOPS_ROOT_PATH . '/modules/system/admin/avatars/main.php'
        );
    }

    #[Test]
    public function doesNotUseTransactionKeywords(): void
    {
        // MyISAM tables ignore transactions, so the code must NOT rely on them
        $this->assertStringNotContainsString(
            'START TRANSACTION',
            $this->source,
            'MyISAM tables do not support transactions — do not use START TRANSACTION'
        );
    }

    #[Test]
    public function userResetHappensBeforeDelete(): void
    {
        $resetPos  = strpos($this->source, "SET user_avatar='blank.gif'");
        $deletePos = strpos($this->source, '$avt_handler->delete($avatar)');
        $this->assertNotFalse($resetPos);
        $this->assertNotFalse($deletePos);
        $this->assertLessThan(
            $deletePos,
            $resetPos,
            'User avatar reset must happen before avatar record delete'
        );
    }

    #[Test]
    public function abortsOnResetFailure(): void
    {
        // After a failed reset ($resetOk = false), should redirect before deleting
        $resetOkPos = strpos($this->source, '$resetOk');
        $this->assertNotFalse($resetOkPos, 'Should use $resetOk flag for reset result');

        $abortPos = strpos($this->source, "if (!\$resetOk)");
        $this->assertNotFalse($abortPos, 'Should check $resetOk and abort on failure');

        $deletePos = strpos($this->source, '$avt_handler->delete($avatar)');
        $this->assertLessThan(
            $deletePos,
            $abortPos,
            'Reset-failure abort must come before the delete call'
        );
    }

    #[Test]
    public function compensatingRestoreOnDeleteFailure(): void
    {
        // After a failed delete, the code should restore the original avatar filename
        $deletePos = strpos($this->source, '$avt_handler->delete($avatar)');
        $this->assertNotFalse($deletePos);

        $afterDelete = substr($this->source, $deletePos, 800);
        $this->assertStringContainsString(
            'SET user_avatar=',
            $afterDelete,
            'After failed delete, should restore original avatar filename on affected users'
        );
    }

    #[Test]
    public function usesSpecificErrorMessageOnDeleteFailure(): void
    {
        $deletePos = strpos($this->source, '$avt_handler->delete($avatar)');
        $this->assertNotFalse($deletePos);

        $afterDelete = substr($this->source, $deletePos, 800);
        $this->assertStringContainsString(
            '_AM_SYSTEM_AVATAR_FAILDEL',
            $afterDelete,
            'Should use specific _AM_SYSTEM_AVATAR_FAILDEL message, not generic _AM_SYSTEM_DBERROR'
        );
    }

    #[Test]
    public function redirectsAfterFailedDelete(): void
    {
        $deletePos = strpos($this->source, '$avt_handler->delete($avatar)');
        $this->assertNotFalse($deletePos);

        $afterDelete = substr($this->source, $deletePos, 800);
        $this->assertStringContainsString(
            'redirect_header',
            $afterDelete,
            'After failed delete + compensating restore, should redirect with error'
        );
    }
}
