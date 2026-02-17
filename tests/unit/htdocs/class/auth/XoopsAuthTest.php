<?php

declare(strict_types=1);

namespace xoopsauth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsAuth;
use XoopsTestStubDatabase;

#[CoversClass(XoopsAuth::class)]
class XoopsAuthTest extends TestCase
{
    private XoopsAuth $auth;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/auth/auth.php';
        $this->auth = new XoopsAuth();
    }

    protected function tearDown(): void
    {
        // Reset global config between tests
        unset($GLOBALS['xoopsConfig']);
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorWithNullDaoDefault(): void
    {
        $auth = new XoopsAuth();
        $this->assertNull($auth->_dao);
    }

    #[Test]
    public function constructorWithExplicitNullDao(): void
    {
        $auth = new XoopsAuth(null);
        $this->assertNull($auth->_dao);
    }

    #[Test]
    public function constructorWithStubDatabase(): void
    {
        $db = new XoopsTestStubDatabase();
        $auth = new XoopsAuth($db);
        $this->assertSame($db, $auth->_dao);
    }

    #[Test]
    public function constructorDefaultErrorsIsEmptyArray(): void
    {
        $auth = new XoopsAuth();
        $this->assertSame([], $auth->_errors);
    }

    #[Test]
    public function constructorDefaultAuthMethodIsEmptyString(): void
    {
        $auth = new XoopsAuth();
        $this->assertSame('', $auth->auth_method);
    }

    // ---------------------------------------------------------------
    // authenticate() tests
    // ---------------------------------------------------------------

    #[Test]
    public function authenticateAlwaysReturnsFalse(): void
    {
        $result = $this->auth->authenticate('admin');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateReturnsFalseWithEmptyUsername(): void
    {
        $result = $this->auth->authenticate('');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateReturnsFalseWithNumericUsername(): void
    {
        $result = $this->auth->authenticate('12345');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateReturnsFalseWithSpecialChars(): void
    {
        $result = $this->auth->authenticate('user@domain.com');
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateReturnsFalseWithLongString(): void
    {
        $result = $this->auth->authenticate(str_repeat('a', 1000));
        $this->assertFalse($result);
    }

    #[Test]
    public function authenticateReturnTypeIsBool(): void
    {
        $result = $this->auth->authenticate('testuser');
        $this->assertIsBool($result);
    }

    #[Test]
    public function authenticateDoesNotSetErrors(): void
    {
        $this->auth->authenticate('admin');
        $this->assertSame([], $this->auth->getErrors());
    }

    // ---------------------------------------------------------------
    // setErrors / getErrors tests
    // ---------------------------------------------------------------

    #[Test]
    public function setErrorsSingleError(): void
    {
        $this->auth->setErrors(1, 'Login failed');
        $errors = $this->auth->getErrors();
        $this->assertSame([1 => 'Login failed'], $errors);
    }

    #[Test]
    public function setErrorsMultipleErrorsWithDifferentKeys(): void
    {
        $this->auth->setErrors(1, 'First error');
        $this->auth->setErrors(2, 'Second error');
        $errors = $this->auth->getErrors();
        $this->assertSame([1 => 'First error', 2 => 'Second error'], $errors);
    }

    #[Test]
    public function setErrorsTrimsWhitespace(): void
    {
        $this->auth->setErrors(1, '  trimmed  ');
        $errors = $this->auth->getErrors();
        $this->assertSame('trimmed', $errors[1]);
    }

    #[Test]
    public function setErrorsTrimsNewlines(): void
    {
        $this->auth->setErrors(1, "\n\ttabbed and newlined\n");
        $errors = $this->auth->getErrors();
        $this->assertSame("tabbed and newlined", $errors[1]);
    }

    #[Test]
    public function setErrorsOverwriteExistingKeyPreservesNewValue(): void
    {
        $this->auth->setErrors(1, 'Original');
        $this->auth->setErrors(1, 'Updated');
        $errors = $this->auth->getErrors();
        $this->assertSame('Updated', $errors[1]);
        $this->assertCount(1, $errors);
    }

    #[Test]
    public function getErrorsReturnsEmptyArrayWhenNoErrorsSet(): void
    {
        $this->assertSame([], $this->auth->getErrors());
    }

    #[Test]
    public function setErrorsWithNumericKeyZero(): void
    {
        $this->auth->setErrors(0, 'Error at zero');
        $errors = $this->auth->getErrors();
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame('Error at zero', $errors[0]);
    }

    #[Test]
    public function setErrorsWithStringKey(): void
    {
        $this->auth->setErrors('ldap_error', 'Connection failed');
        $errors = $this->auth->getErrors();
        $this->assertArrayHasKey('ldap_error', $errors);
        $this->assertSame('Connection failed', $errors['ldap_error']);
    }

    #[Test]
    public function setErrorsWithLargeNumericKey(): void
    {
        $this->auth->setErrors(999, 'Error 999');
        $errors = $this->auth->getErrors();
        $this->assertArrayHasKey(999, $errors);
        $this->assertSame('Error 999', $errors[999]);
    }

    #[Test]
    public function setErrorsPreservesOrderOfInsertion(): void
    {
        $this->auth->setErrors(3, 'Third');
        $this->auth->setErrors(1, 'First');
        $this->auth->setErrors(2, 'Second');
        $keys = array_keys($this->auth->getErrors());
        $this->assertSame([3, 1, 2], $keys);
    }

    #[Test]
    public function setErrorsWithEmptyString(): void
    {
        $this->auth->setErrors(1, '');
        $errors = $this->auth->getErrors();
        $this->assertSame('', $errors[1]);
    }

    #[Test]
    public function getErrorsReturnsArrayType(): void
    {
        $this->assertIsArray($this->auth->getErrors());
    }

    #[Test]
    public function setErrorsMultipleCallsSameKeyOnlyLastSurvives(): void
    {
        $this->auth->setErrors(1, 'Attempt 1');
        $this->auth->setErrors(1, 'Attempt 2');
        $this->auth->setErrors(1, 'Attempt 3');
        $this->assertSame('Attempt 3', $this->auth->getErrors()[1]);
        $this->assertCount(1, $this->auth->getErrors());
    }

    // ---------------------------------------------------------------
    // getHtmlErrors() tests
    // ---------------------------------------------------------------

    #[Test]
    public function getHtmlErrorsNonDebugModeReturnsIncorrectLogin(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $result = $this->auth->getHtmlErrors();
        $this->assertStringContainsString(_US_INCORRECTLOGIN, $result);
    }

    #[Test]
    public function getHtmlErrorsStartsWithBrTag(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $result = $this->auth->getHtmlErrors();
        $this->assertStringStartsWith('<br>', $result);
    }

    #[Test]
    #[DataProvider('debugModeProvider')]
    public function getHtmlErrorsDebugModeShowsErrorsAndAuthMethod(int $debugMode): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => $debugMode];
        $this->auth->setErrors(1, 'Login failed');
        $this->auth->auth_method = 'xoops';
        $result = $this->auth->getHtmlErrors();
        $this->assertStringContainsString('Login failed', $result);
        $this->assertStringContainsString('xoops', $result);
    }

    public static function debugModeProvider(): array
    {
        return [
            'debug_mode 1' => [1],
            'debug_mode 2' => [2],
        ];
    }

    #[Test]
    #[DataProvider('debugModeProvider')]
    public function getHtmlErrorsDebugModeNoErrorsShowsNone(int $debugMode): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => $debugMode];
        $result = $this->auth->getHtmlErrors();
        $this->assertStringContainsString(_NONE, $result);
    }

    #[Test]
    public function getHtmlErrorsDebugModeMultipleErrorsShowsAll(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->setErrors(1, 'Error one');
        $this->auth->setErrors(2, 'Error two');
        $this->auth->setErrors(3, 'Error three');
        $result = $this->auth->getHtmlErrors();
        $this->assertStringContainsString('Error one', $result);
        $this->assertStringContainsString('Error two', $result);
        $this->assertStringContainsString('Error three', $result);
    }

    #[Test]
    public function getHtmlErrorsDebugModeAuthMethodAppearsInOutput(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->auth_method = 'ldap';
        $result = $this->auth->getHtmlErrors();
        $this->assertStringContainsString('ldap', $result);
    }

    #[Test]
    public function getHtmlErrorsDebugModeUsesAuthMsgFormat(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->auth_method = 'xoops';
        $result = $this->auth->getHtmlErrors();
        $expected = sprintf(_AUTH_MSG_AUTH_METHOD, 'xoops');
        $this->assertStringContainsString($expected, $result);
    }

    #[Test]
    public function getHtmlErrorsDebugModeContainsBrSeparators(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->setErrors(1, 'Error A');
        $this->auth->setErrors(2, 'Error B');
        $result = $this->auth->getHtmlErrors();
        // Each error is followed by <br>
        $this->assertStringContainsString('Error A<br>', $result);
        $this->assertStringContainsString('Error B<br>', $result);
    }

    #[Test]
    public function getHtmlErrorsNonDebugModeDoesNotShowIndividualErrors(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $this->auth->setErrors(1, 'Secret error info');
        $result = $this->auth->getHtmlErrors();
        $this->assertStringNotContainsString('Secret error info', $result);
    }

    #[Test]
    public function getHtmlErrorsNonDebugModeDoesNotShowAuthMethod(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $this->auth->auth_method = 'ldap';
        $result = $this->auth->getHtmlErrors();
        $this->assertStringNotContainsString(sprintf(_AUTH_MSG_AUTH_METHOD, 'ldap'), $result);
    }

    #[Test]
    #[DataProvider('nonDebugModeProvider')]
    public function getHtmlErrorsVariousNonDebugModesReturnIncorrectLogin(int $debugMode): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => $debugMode];
        $result = $this->auth->getHtmlErrors();
        $this->assertStringContainsString(_US_INCORRECTLOGIN, $result);
    }

    public static function nonDebugModeProvider(): array
    {
        return [
            'debug_mode 0' => [0],
            'debug_mode 3' => [3],
            'debug_mode 4' => [4],
            'debug_mode 99' => [99],
        ];
    }

    #[Test]
    public function getHtmlErrorsDebugModeEmptyAuthMethod(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->auth->auth_method = '';
        $result = $this->auth->getHtmlErrors();
        $expected = sprintf(_AUTH_MSG_AUTH_METHOD, '');
        $this->assertStringContainsString($expected, $result);
    }

    #[Test]
    public function getHtmlErrorsDebugModeOutputStructure(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 2];
        $this->auth->setErrors(1, 'Only error');
        $this->auth->auth_method = 'test_method';
        $result = $this->auth->getHtmlErrors();

        // Starts with <br>
        $this->assertStringStartsWith('<br>', $result);
        // Contains the error followed by <br>
        $this->assertStringContainsString('Only error<br>', $result);
        // Ends with auth method message
        $expected = sprintf(_AUTH_MSG_AUTH_METHOD, 'test_method');
        $this->assertStringEndsWith($expected, $result);
    }

    // ---------------------------------------------------------------
    // Property access tests
    // ---------------------------------------------------------------

    #[Test]
    public function daoPropIsAccessible(): void
    {
        $this->assertNull($this->auth->_dao);
    }

    #[Test]
    public function errorsPropIsAccessible(): void
    {
        $this->assertIsArray($this->auth->_errors);
    }

    #[Test]
    public function authMethodPropIsAccessible(): void
    {
        $this->assertIsString($this->auth->auth_method);
    }

    #[Test]
    public function daoCanBeSetToNull(): void
    {
        $db = new XoopsTestStubDatabase();
        $auth = new XoopsAuth($db);
        $auth->_dao = null;
        $this->assertNull($auth->_dao);
    }

    #[Test]
    public function daoCanBeSetToDatabaseObject(): void
    {
        $this->auth->_dao = new XoopsTestStubDatabase();
        $this->assertInstanceOf(XoopsTestStubDatabase::class, $this->auth->_dao);
    }

    #[Test]
    public function authMethodCanBeChanged(): void
    {
        $this->auth->auth_method = 'ldap';
        $this->assertSame('ldap', $this->auth->auth_method);
    }

    #[Test]
    public function errorsCanBeSetDirectly(): void
    {
        $this->auth->_errors = [1 => 'direct set'];
        $this->assertSame([1 => 'direct set'], $this->auth->_errors);
    }

    // ---------------------------------------------------------------
    // Type safety tests
    // ---------------------------------------------------------------

    #[Test]
    public function errorsPropertyIsAlwaysArray(): void
    {
        $this->assertIsArray($this->auth->_errors);
        $this->auth->setErrors(1, 'test');
        $this->assertIsArray($this->auth->_errors);
    }

    #[Test]
    public function authMethodPropertyIsAlwaysString(): void
    {
        $this->assertIsString($this->auth->auth_method);
        $this->auth->auth_method = 'xoops';
        $this->assertIsString($this->auth->auth_method);
    }

    #[Test]
    public function getErrorsReturnTypeIsArray(): void
    {
        $result = $this->auth->getErrors();
        $this->assertIsArray($result);
    }

    #[Test]
    public function authenticateReturnTypeStrictlyFalse(): void
    {
        $result = $this->auth->authenticate('anything');
        $this->assertSame(false, $result);
    }

    #[Test]
    public function getHtmlErrorsReturnsString(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $result = $this->auth->getHtmlErrors();
        $this->assertIsString($result);
    }
}
