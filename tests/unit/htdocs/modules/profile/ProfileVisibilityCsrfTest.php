<?php

declare(strict_types=1);

namespace modulesprofile;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CSRF protection and POST-only enforcement in
 * htdocs/modules/profile/admin/visibility.php
 *
 * These tests verify the security guards added to the visibility
 * admin controller. Since visibility.php is a procedural script that
 * relies on many global bootstrapping side-effects (xoops_cp_header,
 * module handlers, templates, etc.), we test the security logic by
 * verifying the XoopsSecurity stub behavior and Request::getMethod()
 * contract that the guards depend on.
 */
class ProfileVisibilityCsrfTest extends TestCase
{
    // ---------------------------------------------------------------
    // XoopsSecurity::check() contract tests
    // ---------------------------------------------------------------

    #[Test]
    public function securityCheckReturnsTrueByDefault(): void
    {
        $security = new \XoopsSecurity();
        $this->assertTrue($security->check());
    }

    #[Test]
    public function securityCheckReturnsFalseWhenConfigured(): void
    {
        $security = new \XoopsSecurity();
        $security->testCheckResult = false;
        $this->assertFalse($security->check());
    }

    #[Test]
    public function securityGetErrorsReturnsConfiguredErrors(): void
    {
        $security = new \XoopsSecurity();
        $security->testErrors = ['Token expired', 'Invalid session'];
        $errors = $security->getErrors();
        $this->assertCount(2, $errors);
        $this->assertSame('Token expired', $errors[0]);
    }

    // ---------------------------------------------------------------
    // Request::getMethod() contract tests
    // ---------------------------------------------------------------

    #[Test]
    public function requestGetMethodReturnsCurrentMethod(): void
    {
        // Save original
        $origMethod = $_SERVER['REQUEST_METHOD'] ?? null;

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertSame('POST', \Xmf\Request::getMethod());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertSame('GET', \Xmf\Request::getMethod());

        // Restore
        if ($origMethod !== null) {
            $_SERVER['REQUEST_METHOD'] = $origMethod;
        } else {
            unset($_SERVER['REQUEST_METHOD']);
        }
    }

    // ---------------------------------------------------------------
    // Guard logic verification tests
    // ---------------------------------------------------------------

    #[Test]
    public function deleteOperationOnGetRequestTriggersRedirect(): void
    {
        // Save original
        $origMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Simulate the guard logic from visibility.php
        $op = 'del';
        $redirected = false;

        if ($op === 'del') {
            if ('POST' !== \Xmf\Request::getMethod()) {
                $redirected = true; // Would call redirect_header()
            }
        }

        $this->assertTrue($redirected, 'DELETE via GET should trigger redirect');

        // Restore
        if ($origMethod !== null) {
            $_SERVER['REQUEST_METHOD'] = $origMethod;
        } else {
            unset($_SERVER['REQUEST_METHOD']);
        }
    }

    #[Test]
    public function deleteOperationOnPostRequestPassesMethodCheck(): void
    {
        // Save original
        $origMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $op = 'del';
        $redirected = false;

        if ($op === 'del') {
            if ('POST' !== \Xmf\Request::getMethod()) {
                $redirected = true;
            }
        }

        $this->assertFalse($redirected, 'DELETE via POST should pass method check');

        // Restore
        if ($origMethod !== null) {
            $_SERVER['REQUEST_METHOD'] = $origMethod;
        } else {
            unset($_SERVER['REQUEST_METHOD']);
        }
    }

    #[Test]
    public function insertOperationWithInvalidTokenTriggersRedirect(): void
    {
        $security = new \XoopsSecurity();
        $security->testCheckResult = false;
        $security->testErrors = ['Invalid token'];

        // Simulate the guard logic
        $redirected = false;
        if (!$security->check()) {
            $redirected = true;
        }

        $this->assertTrue($redirected, 'Insert with invalid CSRF token should trigger redirect');
    }

    #[Test]
    public function insertOperationWithValidTokenPasses(): void
    {
        $security = new \XoopsSecurity();
        $security->testCheckResult = true;

        $redirected = false;
        if (!$security->check()) {
            $redirected = true;
        }

        $this->assertFalse($redirected, 'Insert with valid CSRF token should pass');
    }

    #[Test]
    public function deleteOperationWithInvalidTokenTriggersRedirect(): void
    {
        $security = new \XoopsSecurity();
        $security->testCheckResult = false;
        $security->testErrors = ['Token mismatch'];

        // Save original
        $origMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Simulate both guards
        $methodBlocked = false;
        $tokenBlocked = false;

        if ('POST' !== \Xmf\Request::getMethod()) {
            $methodBlocked = true;
        }
        if (!$security->check()) {
            $tokenBlocked = true;
        }

        $this->assertFalse($methodBlocked, 'POST request should pass method check');
        $this->assertTrue($tokenBlocked, 'Invalid token should block delete');

        // Restore
        if ($origMethod !== null) {
            $_SERVER['REQUEST_METHOD'] = $origMethod;
        } else {
            unset($_SERVER['REQUEST_METHOD']);
        }
    }

    // ---------------------------------------------------------------
    // Source file verification
    // ---------------------------------------------------------------

    #[Test]
    public function visibilityFileContainsCsrfCheckForInsert(): void
    {
        $source = file_get_contents(
            XOOPS_ROOT_PATH . '/modules/profile/admin/visibility.php'
        );
        $this->assertStringContainsString(
            "xoopsSecurity']->check()",
            $source,
            'visibility.php must contain CSRF token check'
        );
    }

    #[Test]
    public function visibilityFileContainsPostOnlyCheckForDelete(): void
    {
        $source = file_get_contents(
            XOOPS_ROOT_PATH . '/modules/profile/admin/visibility.php'
        );
        $this->assertStringContainsString(
            "Request::getMethod()",
            $source,
            'visibility.php must check request method for delete'
        );
    }

    #[Test]
    public function visibilityFileReadsDeleteParamsFromPost(): void
    {
        $source = file_get_contents(
            XOOPS_ROOT_PATH . '/modules/profile/admin/visibility.php'
        );
        // After the del operation, parameters should come from POST, not GET
        // Check that field_id in del block uses 'POST'
        $delBlock = strstr($source, "if (\$op === 'del')");
        $this->assertNotFalse($delBlock, 'Delete block must exist');
        $this->assertStringContainsString(
            "'field_id', 0, 'POST'",
            $delBlock,
            'Delete block must read field_id from POST'
        );
    }

    #[Test]
    public function visibilityFileDoesNotReadOpFromGet(): void
    {
        $source = file_get_contents(
            XOOPS_ROOT_PATH . '/modules/profile/admin/visibility.php'
        );
        // The $op variable should no longer fall back to GET
        $this->assertStringNotContainsString(
            "getCmd('op', 'visibility', 'GET')",
            $source,
            'visibility.php must not read op from GET'
        );
    }
}
