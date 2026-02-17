<?php

declare(strict_types=1);

namespace xoopsauth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsAuth;
use XoopsAuthXoops;
use XoopsTestStubDatabase;

#[CoversClass(XoopsAuthXoops::class)]
class XoopsAuthXoopsTest extends TestCase
{
    private XoopsAuthXoops $auth;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/auth/auth.php';
        require_once XOOPS_ROOT_PATH . '/class/auth/auth_xoops.php';
        $this->auth = new XoopsAuthXoops();

        // The bootstrap's xoops_getHandler('member') creates XoopsMemberHandler
        // via newInstanceWithoutConstructor(), leaving its userHandler as null.
        // loginUser() calls $this->userHandler->getObjects() which crashes.
        // Fix: inject a stub userHandler that returns empty from getObjects().
        $memberHandler = xoops_getHandler('member');
        $ref = new ReflectionClass($memberHandler);
        $prop = $ref->getProperty('userHandler');
        $prop->setAccessible(true);

        // Only inject if not already set (avoids re-injection on repeated setUp calls)
        if ($prop->getValue($memberHandler) === null) {
            $stubUserHandler = new class {
                public function getObjects($criteria = null, $id_as_key = false, $as_object = true): array
                {
                    return [];
                }

                public function loginUser($uname, $pwd, $md5 = false)
                {
                    return false;
                }
            };
            $prop->setValue($memberHandler, $stubUserHandler);
        }
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['xoopsConfig']);
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorSetsAuthMethodToXoops(): void
    {
        $this->assertSame('xoops', $this->auth->auth_method);
    }

    #[Test]
    public function constructorStoresDaoParameter(): void
    {
        $db = new XoopsTestStubDatabase();
        $auth = new XoopsAuthXoops($db);
        $this->assertSame($db, $auth->_dao);
    }

    #[Test]
    public function constructorNullDaoByDefault(): void
    {
        $auth = new XoopsAuthXoops();
        $this->assertNull($auth->_dao);
    }

    #[Test]
    public function constructorWithExplicitNullDao(): void
    {
        $auth = new XoopsAuthXoops(null);
        $this->assertNull($auth->_dao);
    }

    #[Test]
    public function constructorWithStubDatabase(): void
    {
        $db = new XoopsTestStubDatabase();
        $auth = new XoopsAuthXoops($db);
        $this->assertInstanceOf(XoopsTestStubDatabase::class, $auth->_dao);
    }

    #[Test]
    public function inheritsFromXoopsAuth(): void
    {
        $this->assertInstanceOf(XoopsAuth::class, $this->auth);
    }

    #[Test]
    public function constructorInitializesEmptyErrors(): void
    {
        $this->assertSame([], $this->auth->_errors);
    }

    // ---------------------------------------------------------------
    // authenticate() tests
    // ---------------------------------------------------------------

    #[Test]
    public function authenticateReturnsFalseWhenLoginUserFails(): void
    {
        $result = $this->auth->authenticate('nonexistent', 'password');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateSetsErrorOnFailure(): void
    {
        $this->auth->authenticate('nonexistent', 'password');
        $errors = $this->auth->getErrors();
        $this->assertArrayHasKey(1, $errors);
        $this->assertSame(_US_INCORRECTLOGIN, $errors[1]);
    }

    #[Test]
    public function authenticateOnlySetsErrorAtKeyOne(): void
    {
        $this->auth->authenticate('testuser', 'testpass');
        $errors = $this->auth->getErrors();
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey(1, $errors);
    }

    #[Test]
    public function authenticateErrorsAccessibleViaGetErrors(): void
    {
        $this->auth->authenticate('user', 'pass');
        $errors = $this->auth->getErrors();
        $this->assertNotEmpty($errors);
    }

    #[Test]
    public function authenticateGetHtmlErrorsShowsMessageInDebugMode(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->authenticate('user', 'pass');
        $html = $this->auth->getHtmlErrors();
        $this->assertStringContainsString(_US_INCORRECTLOGIN, $html);
        $this->assertStringContainsString('xoops', $html);
    }

    #[Test]
    public function authenticateWithEmptyUsername(): void
    {
        $result = $this->auth->authenticate('', 'password');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateWithEmptyPassword(): void
    {
        $result = $this->auth->authenticate('admin', '');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateWithBothEmpty(): void
    {
        $result = $this->auth->authenticate('', '');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateWithNullPasswordDefault(): void
    {
        $result = $this->auth->authenticate('admin');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateWithNullPasswordExplicit(): void
    {
        $result = $this->auth->authenticate('admin', null);
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateSetsErrorOnEachCall(): void
    {
        $this->auth->authenticate('user1', 'pass1');
        $errors1 = $this->auth->getErrors();
        $this->assertArrayHasKey(1, $errors1);

        // Second call should overwrite the same key
        $this->auth->authenticate('user2', 'pass2');
        $errors2 = $this->auth->getErrors();
        $this->assertCount(1, $errors2);
        $this->assertSame(_US_INCORRECTLOGIN, $errors2[1]);
    }

    #[Test]
    public function authenticateDoesNotClearPreviouslySetErrors(): void
    {
        // Set a custom error at a different key first
        $this->auth->setErrors(99, 'Pre-existing error');
        $this->auth->authenticate('user', 'pass');
        $errors = $this->auth->getErrors();
        // Should have both errors: key 99 from manual set, key 1 from authenticate
        $this->assertArrayHasKey(99, $errors);
        $this->assertArrayHasKey(1, $errors);
        $this->assertCount(2, $errors);
    }

    #[Test]
    #[DataProvider('usernameProvider')]
    public function authenticateReturnsFalseForVariousUsernames(string $uname): void
    {
        $result = $this->auth->authenticate($uname, 'somepassword');
        $this->assertFalse($result);
    }

    public static function usernameProvider(): array
    {
        return [
            'simple name'       => ['john'],
            'email format'      => ['user@example.com'],
            'numeric'           => ['12345'],
            'special chars'     => ['user_name-test'],
            'unicode'           => ['usuario'],
            'long string'       => [str_repeat('x', 255)],
            'single char'       => ['a'],
            'with spaces'       => ['user name'],
        ];
    }

    #[Test]
    #[DataProvider('passwordProvider')]
    public function authenticateReturnsFalseForVariousPasswords(string $pwd): void
    {
        $result = $this->auth->authenticate('admin', $pwd);
        $this->assertFalse($result);
    }

    public static function passwordProvider(): array
    {
        return [
            'simple'     => ['password'],
            'complex'    => ['P@$$w0rd!#%'],
            'numeric'    => ['123456'],
            'long'       => [str_repeat('z', 500)],
            'empty'      => [''],
            'whitespace' => ['   '],
        ];
    }

    // ---------------------------------------------------------------
    // Inherited methods tests
    // ---------------------------------------------------------------

    #[Test]
    public function inheritedSetErrorsWorks(): void
    {
        $this->auth->setErrors(42, 'Custom error');
        $errors = $this->auth->getErrors();
        $this->assertSame('Custom error', $errors[42]);
    }

    #[Test]
    public function inheritedGetErrorsWorks(): void
    {
        $this->assertSame([], $this->auth->getErrors());
        $this->auth->setErrors(1, 'test');
        $this->assertCount(1, $this->auth->getErrors());
    }

    #[Test]
    public function inheritedGetHtmlErrorsWorksWithXoopsAuthMethod(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->setErrors(1, 'Test error');
        $html = $this->auth->getHtmlErrors();
        $expected = sprintf(_AUTH_MSG_AUTH_METHOD, 'xoops');
        $this->assertStringContainsString($expected, $html);
    }

    #[Test]
    public function inheritedErrorsStartEmpty(): void
    {
        $this->assertSame([], $this->auth->_errors);
    }

    #[Test]
    public function inheritedDaoIsSetCorrectly(): void
    {
        $db = new XoopsTestStubDatabase();
        $auth = new XoopsAuthXoops($db);
        $this->assertSame($db, $auth->_dao);
    }

    #[Test]
    public function inheritedGetHtmlErrorsNonDebugMode(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $this->auth->authenticate('user', 'pass');
        $html = $this->auth->getHtmlErrors();
        $this->assertStringContainsString(_US_INCORRECTLOGIN, $html);
        $this->assertStringNotContainsString(sprintf(_AUTH_MSG_AUTH_METHOD, 'xoops'), $html);
    }

    #[Test]
    public function inheritedGetHtmlErrorsDebugModeShowsXoopsMethod(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 2];
        $this->auth->authenticate('user', 'pass');
        $html = $this->auth->getHtmlErrors();
        $expected = sprintf(_AUTH_MSG_AUTH_METHOD, 'xoops');
        $this->assertStringContainsString($expected, $html);
    }

    // ---------------------------------------------------------------
    // Type safety tests
    // ---------------------------------------------------------------

    #[Test]
    public function authMethodIsXoopsString(): void
    {
        $this->assertIsString($this->auth->auth_method);
        $this->assertSame('xoops', $this->auth->auth_method);
    }

    #[Test]
    public function authenticateReturnTypeWhenLoginFails(): void
    {
        $result = $this->auth->authenticate('user', 'pass');
        // loginUser returns false via stub, so authenticate returns false
        $this->assertFalse($result);
    }

    #[Test]
    public function errorsArrayTypeAfterAuthenticate(): void
    {
        $this->auth->authenticate('user', 'pass');
        $this->assertIsArray($this->auth->getErrors());
    }

    #[Test]
    public function getHtmlErrorsReturnsStringType(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $result = $this->auth->getHtmlErrors();
        $this->assertIsString($result);
    }

    #[Test]
    public function daoPropertyTypeNullable(): void
    {
        $auth = new XoopsAuthXoops();
        $this->assertNull($auth->_dao);
        $auth->_dao = new XoopsTestStubDatabase();
        $this->assertInstanceOf(XoopsTestStubDatabase::class, $auth->_dao);
        $auth->_dao = null;
        $this->assertNull($auth->_dao);
    }
}
