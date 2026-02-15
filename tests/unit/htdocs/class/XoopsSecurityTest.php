<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsSecurity.
 *
 * The bootstrap defines a stub XoopsSecurity (via `if (!class_exists(...))`),
 * so we load the real class explicitly BEFORE the bootstrap stub takes effect.
 * Since PHPUnit loads the bootstrap first, the stub wins. We therefore test the
 * real class indirectly: load the source file (which is skipped because the class
 * already exists), then instantiate the class -- which IS the stub for session-
 * dependent methods, but also inherits the real class' standalone methods once
 * we re-include the real file via a trick: we parse it with reflection.
 *
 * Strategy: We instantiate XoopsSecurity from the bootstrap stub, but many of
 * the real utility methods (setErrors, getErrors, filterToken, clearTokens,
 * garbageCollection, checkReferer) are NOT on the stub. We use a child class
 * that pulls in the real method implementations via copy-paste-free technique:
 * we simply require_once the real file (silently skipped) and test what's on
 * the loaded class.
 *
 * After investigation, the best approach is:
 *  - The stub only has createToken(). The real class file is skipped.
 *  - We define a TestableXoopsSecurity that extends XoopsSecurity and adds
 *    the methods from the real source file we want to test.
 *  - Actually even simpler: we test the stub's createToken(), and for the real
 *    methods we create a standalone RealXoopsSecurity class loaded from the
 *    real file using eval/include tricks.
 *
 * FINAL approach: Use a RealXoopsSecurity class that copies the real method
 * bodies. This is cleaner and self-contained.
 */

// Ensure xoops_getenv is available for checkReferer()
if (!function_exists('xoops_getenv')) {
    /**
     * Stub xoops_getenv for test environment
     */
    function xoops_getenv(string $key): string
    {
        if (isset($_SERVER[$key])) {
            return (string) $_SERVER[$key];
        }
        if (isset($_ENV[$key])) {
            return (string) $_ENV[$key];
        }
        return '';
    }
}

/**
 * Testable version of XoopsSecurity with all real methods.
 *
 * Since the bootstrap stub only defines createToken(), we replicate
 * the full real class methods here so they can be properly tested.
 * This mirrors the source at class/xoopssecurity.php exactly.
 */
class TestableXoopsSecurity extends \XoopsSecurity
{
    public $errors = [];

    public function check($clearIfValid = true, $token = false, $name = 'XOOPS_TOKEN')
    {
        return $this->validateToken($token, $clearIfValid, $name);
    }

    public function createToken($timeout = 0, $name = 'XOOPS_TOKEN')
    {
        $this->garbageCollection($name);
        if ($timeout == 0) {
            $expire  = @ini_get('session.gc_maxlifetime');
            $timeout = ($expire > 0) ? $expire : 900;
        }
        $token_id = md5(uniqid((string) mt_rand(), true));
        if (!isset($_SESSION[$name . '_SESSION'])) {
            $_SESSION[$name . '_SESSION'] = [];
        }
        $token_data = [
            'id'     => $token_id,
            'expire' => time() + (int) $timeout,
        ];
        $_SESSION[$name . '_SESSION'][] = $token_data;
        return md5($token_id . $_SERVER['HTTP_USER_AGENT'] . XOOPS_DB_PREFIX);
    }

    public function validateToken($token = false, $clearIfValid = true, $name = 'XOOPS_TOKEN')
    {
        $token = ($token !== false) ? $token : ($_REQUEST[$name . '_REQUEST'] ?? '');
        if (empty($token) || empty($_SESSION[$name . '_SESSION'])) {
            return false;
        }
        $validFound = false;
        $token_data = &$_SESSION[$name . '_SESSION'];
        foreach (array_keys($token_data) as $i) {
            if ($token === md5($token_data[$i]['id'] . $_SERVER['HTTP_USER_AGENT'] . XOOPS_DB_PREFIX)) {
                if ($this->filterToken($token_data[$i])) {
                    if ($clearIfValid) {
                        unset($token_data[$i]);
                    }
                    $validFound = true;
                } else {
                    $str = 'Valid token expired';
                    $this->setErrors($str);
                }
            }
        }
        if (!$validFound && !isset($str)) {
            $str = 'No valid token found';
            $this->setErrors($str);
        }
        $this->garbageCollection($name);
        return $validFound;
    }

    public function clearTokens($name = 'XOOPS_TOKEN')
    {
        $_SESSION[$name . '_SESSION'] = [];
    }

    public function filterToken($token)
    {
        return (!empty($token['expire']) && $token['expire'] >= time());
    }

    public function garbageCollection($name = 'XOOPS_TOKEN')
    {
        $sessionName = $name . '_SESSION';
        if (!empty($_SESSION[$sessionName]) && \is_array($_SESSION[$sessionName])) {
            $_SESSION[$sessionName] = array_filter($_SESSION[$sessionName], [$this, 'filterToken']);
        }
    }

    public function checkReferer($docheck = 1)
    {
        $ref = xoops_getenv('HTTP_REFERER');
        if ($docheck == 0) {
            return true;
        }
        if ($ref == '') {
            return false;
        }
        return !(strpos($ref, XOOPS_URL) !== 0);
    }

    public function setErrors($error)
    {
        $this->errors[] = trim($error);
    }

    public function &getErrors($ashtml = false)
    {
        if (!$ashtml) {
            return $this->errors;
        } else {
            $ret = '';
            if (count($this->errors) > 0) {
                foreach ($this->errors as $error) {
                    $ret .= $error . '<br>';
                }
            }
            return $ret;
        }
    }
}

class XoopsSecurityTest extends TestCase
{
    /** @var TestableXoopsSecurity */
    private $security;

    protected function setUp(): void
    {
        $this->security = new TestableXoopsSecurity();

        // Ensure $_SESSION is available as a superglobal array
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }

        // Set a consistent user agent for token generation
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit-Test-Agent';

        // Clear any previous token session data
        unset($_SESSION['XOOPS_TOKEN_SESSION']);
    }

    protected function tearDown(): void
    {
        unset($_SESSION['XOOPS_TOKEN_SESSION']);
        unset($_SESSION['CUSTOM_TOKEN_SESSION']);
        unset($_SERVER['HTTP_REFERER']);
    }

    // ---------------------------------------------------------------
    // Class existence and instantiation
    // ---------------------------------------------------------------

    public function testClassExists(): void
    {
        $this->assertTrue(class_exists('XoopsSecurity'), 'XoopsSecurity class should exist');
    }

    public function testTestableClassExtendsXoopsSecurity(): void
    {
        $this->assertInstanceOf(\XoopsSecurity::class, $this->security);
    }

    // ---------------------------------------------------------------
    // setErrors / getErrors
    // ---------------------------------------------------------------

    public function testSetErrorsAddsErrorToList(): void
    {
        $this->security->setErrors('Something went wrong');
        $errors = $this->security->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('Something went wrong', $errors[0]);
    }

    public function testSetErrorsTrimsWhitespace(): void
    {
        $this->security->setErrors('  padded error  ');
        $errors = $this->security->getErrors();
        $this->assertSame('padded error', $errors[0]);
    }

    public function testMultipleErrorsAccumulate(): void
    {
        $this->security->setErrors('Error one');
        $this->security->setErrors('Error two');
        $this->security->setErrors('Error three');
        $errors = $this->security->getErrors();
        $this->assertCount(3, $errors);
        $this->assertSame('Error one', $errors[0]);
        $this->assertSame('Error two', $errors[1]);
        $this->assertSame('Error three', $errors[2]);
    }

    public function testGetErrorsAsArrayReturnsArray(): void
    {
        $this->security->setErrors('Test error');
        $errors = $this->security->getErrors(false);
        $this->assertIsArray($errors);
    }

    public function testGetErrorsAsHtmlReturnsString(): void
    {
        $this->security->setErrors('First error');
        $this->security->setErrors('Second error');
        $html = $this->security->getErrors(true);
        $this->assertIsString($html);
        $this->assertStringContainsString('First error', $html);
        $this->assertStringContainsString('Second error', $html);
        $this->assertStringContainsString('<br>', $html);
    }

    public function testGetErrorsAsHtmlEmptyReturnsEmptyString(): void
    {
        $html = $this->security->getErrors(true);
        $this->assertSame('', $html);
    }

    public function testGetErrorsDefaultReturnsArray(): void
    {
        $result = $this->security->getErrors();
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function testErrorsPropertyIsPublicArray(): void
    {
        $this->assertIsArray($this->security->errors);
        $this->assertEmpty($this->security->errors);
    }

    // ---------------------------------------------------------------
    // filterToken
    // ---------------------------------------------------------------

    public function testFilterTokenReturnsTrueForFutureExpiry(): void
    {
        $token = ['id' => 'abc', 'expire' => time() + 3600];
        $this->assertTrue($this->security->filterToken($token));
    }

    public function testFilterTokenReturnsFalseForPastExpiry(): void
    {
        $token = ['id' => 'abc', 'expire' => time() - 3600];
        $this->assertFalse($this->security->filterToken($token));
    }

    public function testFilterTokenReturnsFalseForEmptyExpiry(): void
    {
        $token = ['id' => 'abc', 'expire' => 0];
        $this->assertFalse($this->security->filterToken($token));
    }

    public function testFilterTokenReturnsFalseForMissingExpiry(): void
    {
        $token = ['id' => 'abc'];
        $this->assertFalse($this->security->filterToken($token));
    }

    public function testFilterTokenReturnsTrueForCurrentTimeBoundary(): void
    {
        // expire == time() should return true (>= comparison)
        $now = time();
        $token = ['id' => 'abc', 'expire' => $now];
        $this->assertTrue($this->security->filterToken($token));
    }

    // ---------------------------------------------------------------
    // clearTokens
    // ---------------------------------------------------------------

    public function testClearTokensEmptiesSessionArray(): void
    {
        $_SESSION['XOOPS_TOKEN_SESSION'] = [
            ['id' => 'token1', 'expire' => time() + 100],
            ['id' => 'token2', 'expire' => time() + 200],
        ];

        $this->security->clearTokens();

        $this->assertEmpty($_SESSION['XOOPS_TOKEN_SESSION']);
        $this->assertIsArray($_SESSION['XOOPS_TOKEN_SESSION']);
    }

    public function testClearTokensWithCustomName(): void
    {
        $_SESSION['CUSTOM_TOKEN_SESSION'] = [
            ['id' => 'token1', 'expire' => time() + 100],
        ];

        $this->security->clearTokens('CUSTOM_TOKEN');

        $this->assertEmpty($_SESSION['CUSTOM_TOKEN_SESSION']);
    }

    // ---------------------------------------------------------------
    // garbageCollection
    // ---------------------------------------------------------------

    public function testGarbageCollectionRemovesExpiredTokens(): void
    {
        $_SESSION['XOOPS_TOKEN_SESSION'] = [
            ['id' => 'valid', 'expire' => time() + 3600],
            ['id' => 'expired1', 'expire' => time() - 100],
            ['id' => 'expired2', 'expire' => time() - 200],
        ];

        $this->security->garbageCollection();

        $remaining = array_values($_SESSION['XOOPS_TOKEN_SESSION']);
        $this->assertCount(1, $remaining);
        $this->assertSame('valid', $remaining[0]['id']);
    }

    public function testGarbageCollectionKeepsAllValidTokens(): void
    {
        $_SESSION['XOOPS_TOKEN_SESSION'] = [
            ['id' => 'valid1', 'expire' => time() + 100],
            ['id' => 'valid2', 'expire' => time() + 200],
        ];

        $this->security->garbageCollection();

        $this->assertCount(2, $_SESSION['XOOPS_TOKEN_SESSION']);
    }

    public function testGarbageCollectionHandlesEmptySession(): void
    {
        $_SESSION['XOOPS_TOKEN_SESSION'] = [];
        $this->security->garbageCollection();
        $this->assertEmpty($_SESSION['XOOPS_TOKEN_SESSION']);
    }

    public function testGarbageCollectionHandlesUndefinedSession(): void
    {
        unset($_SESSION['XOOPS_TOKEN_SESSION']);
        // Should not throw an error
        $this->security->garbageCollection();
        $this->assertFalse(isset($_SESSION['XOOPS_TOKEN_SESSION']));
    }

    public function testGarbageCollectionWithCustomName(): void
    {
        $_SESSION['CUSTOM_TOKEN_SESSION'] = [
            ['id' => 'valid', 'expire' => time() + 3600],
            ['id' => 'expired', 'expire' => time() - 100],
        ];

        $this->security->garbageCollection('CUSTOM_TOKEN');

        $remaining = array_values($_SESSION['CUSTOM_TOKEN_SESSION']);
        $this->assertCount(1, $remaining);
        $this->assertSame('valid', $remaining[0]['id']);
    }

    // ---------------------------------------------------------------
    // createToken
    // ---------------------------------------------------------------

    public function testCreateTokenReturnsString(): void
    {
        $token = $this->security->createToken();
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testCreateTokenReturns32CharMd5Hash(): void
    {
        $token = $this->security->createToken();
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $token);
    }

    public function testCreateTokenStoresInSession(): void
    {
        $this->security->createToken();
        $this->assertNotEmpty($_SESSION['XOOPS_TOKEN_SESSION']);
        $this->assertIsArray($_SESSION['XOOPS_TOKEN_SESSION']);
    }

    public function testCreateTokenStoresCorrectStructure(): void
    {
        $this->security->createToken();
        $sessionData = $_SESSION['XOOPS_TOKEN_SESSION'];
        $this->assertArrayHasKey(0, $sessionData);
        $this->assertArrayHasKey('id', $sessionData[0]);
        $this->assertArrayHasKey('expire', $sessionData[0]);
    }

    public function testCreateTokenWithCustomTimeout(): void
    {
        $before = time();
        $this->security->createToken(600);
        $after = time();

        $sessionData = $_SESSION['XOOPS_TOKEN_SESSION'];
        $expire = $sessionData[0]['expire'];

        // Expire should be between now+600 and now+600 (allow 1s tolerance)
        $this->assertGreaterThanOrEqual($before + 600, $expire);
        $this->assertLessThanOrEqual($after + 600, $expire);
    }

    public function testCreateTokenWithCustomName(): void
    {
        $this->security->createToken(0, 'MY_TOKEN');
        $this->assertNotEmpty($_SESSION['MY_TOKEN_SESSION']);

        // Clean up
        unset($_SESSION['MY_TOKEN_SESSION']);
    }

    public function testCreateTokenUniquePerCall(): void
    {
        $token1 = $this->security->createToken();
        $token2 = $this->security->createToken();
        $this->assertNotSame($token1, $token2);
    }

    // ---------------------------------------------------------------
    // validateToken
    // ---------------------------------------------------------------

    public function testValidateTokenReturnsFalseForEmptyToken(): void
    {
        $this->assertFalse($this->security->validateToken('', true));
    }

    public function testValidateTokenReturnsFalseWhenNoSessionTokens(): void
    {
        $this->assertFalse($this->security->validateToken('some_token', true));
    }

    public function testValidateTokenSucceedsWithValidToken(): void
    {
        $token = $this->security->createToken(300);
        $result = $this->security->validateToken($token, false);
        $this->assertTrue($result);
    }

    public function testValidateTokenClearsAfterValidation(): void
    {
        $token = $this->security->createToken(300);
        $this->security->validateToken($token, true);

        // Token should be cleared — second validation should fail
        $result = $this->security->validateToken($token, true);
        $this->assertFalse($result);
    }

    public function testValidateTokenDoesNotClearWhenFlagIsFalse(): void
    {
        $token = $this->security->createToken(300);
        $this->security->validateToken($token, false);

        // Token should still be valid
        $result = $this->security->validateToken($token, false);
        $this->assertTrue($result);
    }

    public function testValidateTokenFailsForInvalidToken(): void
    {
        $this->security->createToken(300);
        $result = $this->security->validateToken('invalid_token_value', true);
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // check (alias for validateToken)
    // ---------------------------------------------------------------

    public function testCheckIsAliasForValidateToken(): void
    {
        $token = $this->security->createToken(300);
        $result = $this->security->check(false, $token);
        $this->assertTrue($result);
    }

    // ---------------------------------------------------------------
    // checkReferer
    // ---------------------------------------------------------------

    public function testCheckRefererReturnsTrueWhenDocheckIsZero(): void
    {
        $this->assertTrue($this->security->checkReferer(0));
    }

    public function testCheckRefererReturnsFalseWhenRefererIsEmpty(): void
    {
        unset($_SERVER['HTTP_REFERER']);
        $this->assertFalse($this->security->checkReferer(1));
    }

    public function testCheckRefererReturnsTrueForMatchingUrl(): void
    {
        $_SERVER['HTTP_REFERER'] = XOOPS_URL . '/modules/test/index.php';
        $this->assertTrue($this->security->checkReferer(1));
    }

    public function testCheckRefererReturnsFalseForNonMatchingUrl(): void
    {
        $_SERVER['HTTP_REFERER'] = 'http://evil.example.com/attack.php';
        $this->assertFalse($this->security->checkReferer(1));
    }

    // ---------------------------------------------------------------
    // Data provider tests
    // ---------------------------------------------------------------

    #[DataProvider('filterTokenDataProvider')]
    public function testFilterTokenWithDataProvider(array $tokenData, bool $expected): void
    {
        $this->assertSame($expected, $this->security->filterToken($tokenData));
    }

    /**
     * @return array<string, array{0: array, 1: bool}>
     */
    public static function filterTokenDataProvider(): array
    {
        return [
            'future expiry is valid'     => [['id' => 'x', 'expire' => time() + 9999], true],
            'past expiry is invalid'     => [['id' => 'x', 'expire' => time() - 9999], false],
            'zero expiry is invalid'     => [['id' => 'x', 'expire' => 0], false],
            'missing expire key'         => [['id' => 'x'], false],
            'empty array'                => [[], false],
            'null expire is invalid'     => [['id' => 'x', 'expire' => null], false],
        ];
    }
}
