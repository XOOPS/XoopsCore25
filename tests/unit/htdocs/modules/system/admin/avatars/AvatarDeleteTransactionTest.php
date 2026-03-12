<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for avatar delete transactional integrity (M-5).
 *
 * Verifies the delfileok case uses START TRANSACTION / COMMIT / ROLLBACK
 * instead of non-transactional reset + delete.
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
    public function delfileokCaseUsesStartTransaction(): void
    {
        $this->assertStringContainsString(
            "START TRANSACTION",
            $this->source,
            'The delfileok case should use START TRANSACTION'
        );
    }

    #[Test]
    public function delfileokCaseUsesCommit(): void
    {
        $this->assertStringContainsString(
            "COMMIT",
            $this->source,
            'The delfileok case should COMMIT on success'
        );
    }

    #[Test]
    public function delfileokCaseUsesRollback(): void
    {
        $this->assertStringContainsString(
            "ROLLBACK",
            $this->source,
            'The delfileok case should ROLLBACK on failure'
        );
    }

    #[Test]
    public function transactionStartsBeforeUserReset(): void
    {
        // START TRANSACTION should appear before the UPDATE ... blank.gif
        $txPos    = strpos($this->source, 'START TRANSACTION');
        $resetPos = strpos($this->source, "SET user_avatar='blank.gif'");
        $this->assertNotFalse($txPos);
        $this->assertNotFalse($resetPos);
        $this->assertLessThan(
            $resetPos,
            $txPos,
            'Transaction must start before user avatar reset'
        );
    }

    #[Test]
    public function commitComesAfterDelete(): void
    {
        // COMMIT should appear after $avt_handler->delete
        $deletePos = strpos($this->source, '$avt_handler->delete($avatar)');
        $commitPos = strpos($this->source, "COMMIT");
        $this->assertNotFalse($deletePos);
        $this->assertNotFalse($commitPos);
        $this->assertLessThan(
            $commitPos,
            $deletePos,
            'COMMIT must appear after avatar record delete'
        );
    }

    #[Test]
    public function rollbackRedirectsOnFailure(): void
    {
        // After ROLLBACK, there should be a redirect_header
        $rollbackPos  = strpos($this->source, 'ROLLBACK');
        $this->assertNotFalse($rollbackPos);
        $afterRollback = substr($this->source, $rollbackPos, 200);
        $this->assertStringContainsString(
            'redirect_header',
            $afterRollback,
            'After ROLLBACK, should redirect with error'
        );
    }
}
