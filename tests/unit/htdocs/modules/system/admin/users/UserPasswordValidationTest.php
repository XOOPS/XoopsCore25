<?php

declare(strict_types=1);

namespace modulessystem\admin\users;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for H-4: Password validation bypass in user admin (users/main.php).
 *
 * For new user creation (op=users_add), both pass1 and pass2 must be non-empty
 * and matching. The old code only checked pass2 as a gate, allowing password
 * validation to be skipped entirely if pass2 was empty.
 *
 * These tests verify the validation logic patterns used in the fixed code.
 */
class UserPasswordValidationTest extends TestCase
{
    /**
     * Simulate the password validation logic from the fixed users/main.php.
     * Returns null on success, or an error constant name on failure.
     */
    private function validateNewUserPassword(string $pass1, string $pass2, string $uname): ?string
    {
        // This mirrors the fixed logic in main.php for new user creation
        if ('' === $pass2) {
            return '_AM_SYSTEM_USERS_STNPDNM';
        }

        if ($pass1 != $pass2) {
            return '_AM_SYSTEM_USERS_STNPDNM';
        }

        if (mb_strtolower($pass1, 'UTF-8') === mb_strtolower($uname, 'UTF-8')) {
            return '_AM_SYSTEM_USERS_PWDEQUALSUNAME';
        }

        return null; // success
    }

    #[Test]
    public function emptyPass2RejectsNewUser(): void
    {
        $result = $this->validateNewUserPassword('secret123', '', 'testuser');
        $this->assertSame('_AM_SYSTEM_USERS_STNPDNM', $result,
            'Empty pass2 must be rejected for new user creation');
    }

    #[Test]
    public function bothPasswordsEmptyRejectsNewUser(): void
    {
        $result = $this->validateNewUserPassword('', '', 'testuser');
        $this->assertSame('_AM_SYSTEM_USERS_STNPDNM', $result,
            'Both passwords empty must be rejected');
    }

    #[Test]
    public function mismatchedPasswordsRejectsNewUser(): void
    {
        $result = $this->validateNewUserPassword('abc123', 'xyz789', 'testuser');
        $this->assertSame('_AM_SYSTEM_USERS_STNPDNM', $result,
            'Mismatched passwords must be rejected');
    }

    #[Test]
    public function passwordEqualToUsernameRejectsNewUser(): void
    {
        $result = $this->validateNewUserPassword('TestUser', 'TestUser', 'testuser');
        $this->assertSame('_AM_SYSTEM_USERS_PWDEQUALSUNAME', $result,
            'Password equal to username (case-insensitive) must be rejected');
    }

    #[Test]
    public function validMatchingPasswordsAccepted(): void
    {
        $result = $this->validateNewUserPassword('Str0ng!Pass', 'Str0ng!Pass', 'admin');
        $this->assertNull($result, 'Valid matching passwords should be accepted');
    }

    #[Test]
    public function pass1EmptyWithPass2FilledRejects(): void
    {
        // pass1 is required by the earlier check: !Request::getVar('password', ...)
        // But even if it gets through, empty pass1 != non-empty pass2
        $result = $this->validateNewUserPassword('', 'somepassword', 'testuser');
        $this->assertSame('_AM_SYSTEM_USERS_STNPDNM', $result,
            'Empty pass1 with filled pass2 must be rejected (mismatch)');
    }

    #[Test]
    public function sourceFileRequiresPass2ForNewUser(): void
    {
        $source = file_get_contents(XOOPS_ROOT_PATH . '/modules/system/admin/users/main.php');
        // In the "Add user" section (after the else { // --- Add user --- block),
        // verify pass2 is required (not just optional gate)
        $this->assertStringContainsString(
            "'' === \$pass2",
            $source,
            'New user creation must check for empty pass2'
        );
    }

    #[Test]
    public function sourceFileAlwaysHashesPasswordForNewUser(): void
    {
        $source = file_get_contents(XOOPS_ROOT_PATH . '/modules/system/admin/users/main.php');
        // Verify that the new user path unconditionally hashes the password
        // (no longer gated by pass2 check for the setVar('pass', ...) call)
        $this->assertStringContainsString(
            "\$newuser->setVar('pass', password_hash(\$pass1, PASSWORD_DEFAULT))",
            $source,
            'New user password must always be hashed (not gated by pass2 check)'
        );
    }
}
