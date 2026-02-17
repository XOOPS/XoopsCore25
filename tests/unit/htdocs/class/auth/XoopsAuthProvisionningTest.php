<?php

declare(strict_types=1);

namespace xoopsauth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsAuth;
use XoopsAuthProvisionning;
use XoopsMemberHandler;
use XoopsUser;
use XoopsUserHandler;

/**
 * Extended test DB stub that safely overrides error() and errno()
 * to avoid TypeError when mysqli_error() is called with null connection.
 * The bootstrap's XoopsTestStubDatabase doesn't override these methods,
 * causing TypeError in PHP 8.x when code paths try to report query errors.
 */
/**
 * Extended test DB stub for auth provisioning tests.
 *
 * Fixes two issues with the bootstrap's XoopsTestStubDatabase:
 * 1. Overrides error()/errno() to avoid TypeError when mysqli_error()
 *    is called with null connection in PHP 8.x.
 * 2. Makes query() return a truthy sentinel value that isResultSet()
 *    recognizes, so the read model doesn't throw RuntimeException.
 *    fetchArray() still returns false, so getObjects() returns [].
 */
class ProvisionningTestDbStub extends \XoopsTestStubDatabase
{
    /** @var object Sentinel object returned by query() to pass isResultSet() */
    private object $emptyResult;

    public function __construct()
    {
        parent::__construct();
        // Create a simple object as an "empty result set" sentinel
        $this->emptyResult = new \stdClass();
    }

    public function error(): string
    {
        return '';
    }

    public function errno(): int
    {
        return 0;
    }

    public function query(string $sql, ?int $limit = null, ?int $start = null)
    {
        // Return a sentinel object instead of false.
        // isResultSet() will return true for objects, and fetchArray() returns false,
        // so getObjects() loops zero times and returns [].
        return $this->emptyResult;
    }

    public function queryF($sql, $limit = 0, $start = 0)
    {
        return $this->emptyResult;
    }

    public function isResultSet($result): bool
    {
        // Accept our sentinel object as a valid result set
        return $result === $this->emptyResult;
    }

    public function fetchArray($result)
    {
        // No rows in the fake result
        return false;
    }

    public function getRowsNum($result)
    {
        return 0;
    }

    public function freeRecordSet($result)
    {
        // No-op for fake results
    }
}

#[CoversClass(XoopsAuthProvisionning::class)]
class XoopsAuthProvisionningTest extends TestCase
{
    private XoopsAuthProvisionning $provis;

    /** @var \XoopsDatabase Original DB reference for restoration */
    private $originalDb;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/auth/auth.php';
        require_once XOOPS_ROOT_PATH . '/class/auth/auth_provisionning.php';

        // Use reflection to create instance without constructor (constructor depends on config)
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->provis = $ref->newInstanceWithoutConstructor();

        // Swap global DB with our extended stub that safely handles error()
        $this->originalDb = $GLOBALS['xoopsDB'];
        $GLOBALS['xoopsDB'] = new ProvisionningTestDbStub();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['xoopsConfig']);
        // Restore original DB stub
        $GLOBALS['xoopsDB'] = $this->originalDb;
    }

    /**
     * Helper: set a protected property on an object via reflection.
     */
    private function setPropertyOn(object $object, string $name, $value): void
    {
        $ref = new ReflectionClass($object);
        $prop = $ref->getProperty($name);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }

    /**
     * Helper: get a protected property value via reflection.
     *
     * @return mixed
     */
    private function getPropertyFrom(object $object, string $name)
    {
        $ref = new ReflectionClass($object);
        $prop = $ref->getProperty($name);
        $prop->setAccessible(true);
        return $prop->getValue($object);
    }

    /**
     * Helper: create a configured XoopsAuthProvisionning via reflection.
     * Sets all required typed properties to prevent uninitialized property errors.
     */
    private function createConfiguredInstance(?XoopsAuth $auth = null): XoopsAuthProvisionning
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $instance = $ref->newInstanceWithoutConstructor();

        // Set the auth instance
        $this->setPropertyOn($instance, '_auth_instance', $auth);

        // Set defaults for all typed properties
        $defaults = [
            'ldap_provisionning'       => '',
            'ldap_provisionning_upd'   => '',
            'ldap_provisionning_group' => [],
            'ldap_field_mapping'       => '',
            'default_TZ'               => '0.0',
            'theme_set'                => 'default',
            'com_mode'                 => 'flat',
            'com_order'                => '0',
        ];
        foreach ($defaults as $key => $val) {
            $this->setPropertyOn($instance, $key, $val);
        }

        return $instance;
    }

    /**
     * Helper: Ensure the global member handler has properly initialized sub-handlers.
     * The bootstrap creates XoopsMemberHandler via newInstanceWithoutConstructor(),
     * leaving userHandler/membershipHandler null. We inject a proper userHandler
     * using the current global DB stub.
     */
    private function ensureMemberHandlerReady(): void
    {
        $handler = xoops_getHandler('member');
        if ($handler instanceof XoopsMemberHandler) {
            $ref = new ReflectionClass($handler);
            $db = $GLOBALS['xoopsDB'];

            // Create a fresh XoopsUserHandler. Note: XoopsPersistableObjectHandler's
            // constructor ignores the passed $db and calls XoopsDatabaseFactory instead.
            // So we must manually override the db property after construction.
            $userHandler = new XoopsUserHandler($db);
            $uhRef = new ReflectionClass($userHandler);
            // Walk up the class hierarchy to find and set the 'db' property
            $current = $uhRef;
            while ($current) {
                if ($current->hasProperty('db')) {
                    $dbProp = $current->getProperty('db');
                    $dbProp->setAccessible(true);
                    $dbProp->setValue($userHandler, $db);
                    break;
                }
                $current = $current->getParentClass();
            }

            // Inject the userHandler into the member handler
            $userProp = $ref->getProperty('userHandler');
            $userProp->setAccessible(true);
            $userProp->setValue($handler, $userHandler);
        }
    }

    // ---------------------------------------------------------------
    // Class structure tests
    // ---------------------------------------------------------------

    #[Test]
    public function classExists(): void
    {
        $this->assertTrue(class_exists('XoopsAuthProvisionning'));
    }

    #[Test]
    public function classHasGetInstanceMethod(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasMethod('getInstance'));
    }

    #[Test]
    public function getInstanceMethodIsStatic(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('getInstance');
        $this->assertTrue($method->isStatic());
    }

    #[Test]
    public function getInstanceMethodIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('getInstance');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function classHasConstructor(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertNotNull($ref->getConstructor());
    }

    #[Test]
    public function constructorAcceptsNullableXoopsAuth(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $constructor = $ref->getConstructor();
        $params = $constructor->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('auth_instance', $params[0]->getName());
        $this->assertTrue($params[0]->allowsNull());
    }

    // ---------------------------------------------------------------
    // Constructor tests (documents that it fails with empty config)
    // ---------------------------------------------------------------

    #[Test]
    public function constructorFailsWithEmptyConfig(): void
    {
        // The constructor accesses $config_gen['default_TZ'] etc. which will be undefined
        // when the stub config handler returns []. This results in a warning (PHP 8.x).
        // PHPUnit 11 removed expectWarning(), so we capture the error manually.
        $errorTriggered = false;
        set_error_handler(function (int $errno) use (&$errorTriggered): bool {
            $errorTriggered = true;
            return true; // suppress
        });

        try {
            new XoopsAuthProvisionning(null);
        } catch (\Throwable $e) {
            // In strict mode, accessing undefined array key may throw
            $errorTriggered = true;
        } finally {
            restore_error_handler();
        }

        $this->assertTrue($errorTriggered, 'Constructor should trigger an error/warning with empty config');
    }

    // ---------------------------------------------------------------
    // Protected properties existence tests
    // ---------------------------------------------------------------

    #[Test]
    public function hasAuthInstanceProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('_auth_instance'));
    }

    #[Test]
    public function authInstancePropertyIsProtected(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $prop = $ref->getProperty('_auth_instance');
        $this->assertTrue($prop->isProtected());
    }

    #[Test]
    public function hasLdapProvisionningProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('ldap_provisionning'));
    }

    #[Test]
    public function hasLdapProvisionningUpdProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('ldap_provisionning_upd'));
    }

    #[Test]
    public function hasLdapProvisionningGroupProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('ldap_provisionning_group'));
    }

    #[Test]
    public function hasLdapFieldMappingProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('ldap_field_mapping'));
    }

    #[Test]
    public function hasDefaultTZProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('default_TZ'));
    }

    #[Test]
    public function hasThemeSetProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('theme_set'));
    }

    #[Test]
    public function hasComModeProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('com_mode'));
    }

    #[Test]
    public function hasComOrderProperty(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $this->assertTrue($ref->hasProperty('com_order'));
    }

    // ---------------------------------------------------------------
    // _auth_instance property tests (via reflection)
    // ---------------------------------------------------------------

    #[Test]
    public function authInstanceStoredViaReflection(): void
    {
        $auth = new XoopsAuth(null);
        $this->setPropertyOn($this->provis, '_auth_instance', $auth);
        $this->assertSame($auth, $this->getPropertyFrom($this->provis, '_auth_instance'));
    }

    #[Test]
    public function authInstanceCanBeNull(): void
    {
        $this->setPropertyOn($this->provis, '_auth_instance', null);
        $this->assertNull($this->getPropertyFrom($this->provis, '_auth_instance'));
    }

    // ---------------------------------------------------------------
    // getXoopsUser() tests
    //
    // The stub DB returns false for query(), and isResultSet returns false,
    // causing the read model to throw RuntimeException. getObjects() in
    // XoopsPersistableObjectHandler catches this and returns [].
    // With count([]) == 0 != 1, getXoopsUser returns false.
    // ---------------------------------------------------------------

    #[Test]
    public function getXoopsUserReturnsFalseWhenNoUsersFound(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser('nonexistent');
        $this->assertFalse($result);
    }

    #[Test]
    public function getXoopsUserReturnsFalseWithEmptyUsername(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser('');
        $this->assertFalse($result);
    }

    #[Test]
    public function getXoopsUserReturnsFalseWithSpecialChars(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser('user@domain.com');
        $this->assertFalse($result);
    }

    #[Test]
    public function getXoopsUserReturnType(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser('admin');
        // Should return either XoopsUser or false; with stub DB it's always false
        $this->assertFalse($result);
    }

    #[Test]
    public function getXoopsUserReturnsFalseWithNumericUsername(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser('12345');
        $this->assertFalse($result);
    }

    #[Test]
    public function getXoopsUserReturnsFalseWithLongUsername(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser(str_repeat('a', 500));
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // sync() tests -- user not found, provisioning disabled
    // ---------------------------------------------------------------

    #[Test]
    public function syncWithNoUserAndProvisionningDisabledSetsErrorOnAuthInstance(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);

        // ldap_provisionning is empty string (falsy) by default
        $result = $instance->sync([], 'nonexistent');

        // Should set error on auth instance
        $errors = $auth->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey(0, $errors);
        $this->assertStringContainsString('nonexistent', $errors[0]);
    }

    #[Test]
    public function syncWithNoUserAndProvisionningDisabledReturnsXoopsUserFalse(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);
        $result = $instance->sync([], 'nonexistent');
        $this->assertFalse($result);
    }

    #[Test]
    public function syncWithNoUserNoAuthInstanceTriggersWarning(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance(null);

        $warningTriggered = false;
        set_error_handler(function (int $errno) use (&$warningTriggered): bool {
            if ($errno === E_USER_WARNING) {
                $warningTriggered = true;
            }
            return true;
        });

        try {
            $instance->sync([], 'nonexistent');
        } finally {
            restore_error_handler();
        }

        $this->assertTrue($warningTriggered, 'sync() should trigger E_USER_WARNING when no auth instance is set');
    }

    #[Test]
    public function syncWithNoUserNoAuthInstanceWarningContainsUname(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance(null);

        $warningMessage = '';
        set_error_handler(function (int $errno, string $errstr) use (&$warningMessage): bool {
            if ($errno === E_USER_WARNING) {
                $warningMessage = $errstr;
            }
            return true;
        });

        try {
            $instance->sync([], 'testuser123');
        } finally {
            restore_error_handler();
        }

        $this->assertStringContainsString('testuser123', $warningMessage);
    }

    #[Test]
    public function syncErrorMessageUsesLdapXoopsUserNotfoundFormat(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);
        $instance->sync([], 'someuser');

        $errors = $auth->getErrors();
        $expected = sprintf(_AUTH_LDAP_XOOPS_USER_NOTFOUND, 'someuser');
        $this->assertSame($expected, $errors[0]);
    }

    // ---------------------------------------------------------------
    // sync() tests -- user not found, provisioning enabled
    // ---------------------------------------------------------------

    #[Test]
    public function syncWithNoUserAndProvisionningEnabledCallsAdd(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);

        // Enable provisioning
        $this->setPropertyOn($instance, 'ldap_provisionning', '1');

        // add() will call redirect_header when password is null/empty
        $this->expectException(\RedirectHeaderException::class);
        $instance->sync([], 'newuser', null);
    }

    #[Test]
    public function syncWithNoUserAndProvisionningEnabledWithPasswordCallsAdd(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);

        // Enable provisioning
        $this->setPropertyOn($instance, 'ldap_provisionning', '1');

        // add() with valid password attempts to insert (stub DB returns false),
        // then calls redirect_header
        $this->expectException(\RedirectHeaderException::class);
        $instance->sync([], 'newuser', 'password123');
    }

    // ---------------------------------------------------------------
    // add() tests
    // ---------------------------------------------------------------

    #[Test]
    public function addWithNullPasswordTriggersRedirect(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        $this->expectException(\RedirectHeaderException::class);
        $this->expectExceptionMessage('Password cannot be empty');
        $instance->add([], 'newuser', null);
    }

    #[Test]
    public function addWithEmptyPasswordTriggersRedirect(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        $this->expectException(\RedirectHeaderException::class);
        $this->expectExceptionMessage('Password cannot be empty');
        $instance->add([], 'newuser', '');
    }

    #[Test]
    public function addWithWhitespaceOnlyPasswordTriggersRedirect(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        $this->expectException(\RedirectHeaderException::class);
        $this->expectExceptionMessage('Password cannot be empty');
        $instance->add([], 'newuser', '   ');
    }

    #[Test]
    public function addRedirectUrlPointsToUserPage(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        try {
            $instance->add([], 'newuser', null);
            $this->fail('Expected RedirectHeaderException');
        } catch (\RedirectHeaderException $e) {
            $this->assertSame(XOOPS_URL . '/user.php', $e->url);
        }
    }

    #[Test]
    public function addRedirectTimeIsFiveSeconds(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        try {
            $instance->add([], 'newuser', null);
            $this->fail('Expected RedirectHeaderException');
        } catch (\RedirectHeaderException $e) {
            $this->assertSame(5, $e->time);
        }
    }

    #[Test]
    public function addWithValidPasswordAttemptsInsert(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        // insertUser returns false (stub DB), which triggers redirect_header
        $this->expectException(\RedirectHeaderException::class);
        $instance->add([], 'newuser', 'validpassword');
    }

    // ---------------------------------------------------------------
    // change() tests
    // ---------------------------------------------------------------

    #[Test]
    public function changeWithFailedInsertTriggersRedirect(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        // Create a XoopsUser with proper initialization
        $user = new XoopsUser();

        // The insertUser call will return false (stub DB), triggering redirect
        $this->expectException(\RedirectHeaderException::class);
        $instance->change($user, [], 'testuser', 'password');
    }

    #[Test]
    public function changeRedirectUrlPointsToUserPage(): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();

        $user = new XoopsUser();

        try {
            $instance->change($user, [], 'testuser', 'password');
            $this->fail('Expected RedirectHeaderException');
        } catch (\RedirectHeaderException $e) {
            $this->assertSame(XOOPS_URL . '/user.php', $e->url);
        }
    }

    // ---------------------------------------------------------------
    // delete(), suspend(), restore(), resetpwd() stub tests
    // ---------------------------------------------------------------

    #[Test]
    public function deleteReturnsNull(): void
    {
        $instance = $this->createConfiguredInstance();
        $result = $instance->delete();
        $this->assertNull($result);
    }

    #[Test]
    public function suspendReturnsNull(): void
    {
        $instance = $this->createConfiguredInstance();
        $result = $instance->suspend();
        $this->assertNull($result);
    }

    #[Test]
    public function restoreReturnsNull(): void
    {
        $instance = $this->createConfiguredInstance();
        $result = $instance->restore();
        $this->assertNull($result);
    }

    #[Test]
    public function resetpwdReturnsNull(): void
    {
        $instance = $this->createConfiguredInstance();
        $result = $instance->resetpwd();
        $this->assertNull($result);
    }

    #[Test]
    public function deleteIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('delete');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function suspendIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('suspend');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function restoreIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('restore');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function resetpwdIsPublic(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('resetpwd');
        $this->assertTrue($method->isPublic());
    }

    // ---------------------------------------------------------------
    // Method signature tests
    // ---------------------------------------------------------------

    #[Test]
    public function getXoopsUserAcceptsOneParameter(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('getXoopsUser');
        $this->assertCount(1, $method->getParameters());
    }

    #[Test]
    public function syncAcceptsThreeParameters(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('sync');
        $params = $method->getParameters();
        $this->assertCount(3, $params);
        $this->assertSame('datas', $params[0]->getName());
        $this->assertSame('uname', $params[1]->getName());
        $this->assertSame('pwd', $params[2]->getName());
    }

    #[Test]
    public function syncThirdParameterIsOptional(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('sync');
        $params = $method->getParameters();
        $this->assertTrue($params[2]->isOptional());
        $this->assertNull($params[2]->getDefaultValue());
    }

    #[Test]
    public function addAcceptsThreeParameters(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('add');
        $params = $method->getParameters();
        $this->assertCount(3, $params);
    }

    #[Test]
    public function changeAcceptsFourParameters(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $method = $ref->getMethod('change');
        $params = $method->getParameters();
        $this->assertCount(4, $params);
        $this->assertSame('xoopsUser', $params[0]->getName());
        $this->assertSame('datas', $params[1]->getName());
        $this->assertSame('uname', $params[2]->getName());
        $this->assertSame('pwd', $params[3]->getName());
    }

    // ---------------------------------------------------------------
    // Property type declarations tests
    // ---------------------------------------------------------------

    #[Test]
    public function ldapProvisionningPropertyIsString(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $prop = $ref->getProperty('ldap_provisionning');
        $type = $prop->getType();
        $this->assertNotNull($type);
        $this->assertSame('string', $type->getName());
    }

    #[Test]
    public function ldapProvisionningGroupPropertyIsArray(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $prop = $ref->getProperty('ldap_provisionning_group');
        $type = $prop->getType();
        $this->assertNotNull($type);
        $this->assertSame('array', $type->getName());
    }

    #[Test]
    public function authInstancePropertyIsNullableXoopsAuth(): void
    {
        $ref = new ReflectionClass(XoopsAuthProvisionning::class);
        $prop = $ref->getProperty('_auth_instance');
        $type = $prop->getType();
        $this->assertNotNull($type);
        $this->assertTrue($type->allowsNull());
    }

    // ---------------------------------------------------------------
    // Data provider tests for multiple usernames
    // ---------------------------------------------------------------

    #[Test]
    #[DataProvider('usernameProvider')]
    public function getXoopsUserReturnsFalseForVariousUsernames(string $uname): void
    {
        $this->ensureMemberHandlerReady();
        $instance = $this->createConfiguredInstance();
        $result = $instance->getXoopsUser($uname);
        $this->assertFalse($result);
    }

    public static function usernameProvider(): array
    {
        return [
            'simple name'      => ['admin'],
            'email format'     => ['user@example.com'],
            'unicode'          => ['utilisateur'],
            'with spaces'      => ['john doe'],
            'numeric'          => ['42'],
            'empty string'     => [''],
            'special chars'    => ['user<script>'],
            'sql injection'    => ["admin' OR '1'='1"],
        ];
    }

    // ---------------------------------------------------------------
    // sync() additional edge case tests
    // ---------------------------------------------------------------

    #[Test]
    public function syncReturnsFalseWhenNoUserAndNoProvisioning(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);
        $result = $instance->sync([], 'unknown_user', 'pwd');
        $this->assertFalse($result);
    }

    #[Test]
    public function syncSetsErrorKeyZero(): void
    {
        $this->ensureMemberHandlerReady();
        $auth = new XoopsAuth(null);
        $instance = $this->createConfiguredInstance($auth);
        $instance->sync([], 'unknown');
        $errors = $auth->getErrors();
        // The error key should be 0 (first arg to setErrors)
        $this->assertArrayHasKey(0, $errors);
    }
}
