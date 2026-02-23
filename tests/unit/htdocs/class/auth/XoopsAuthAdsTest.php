<?php

declare(strict_types=1);

namespace xoopsauth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// Load auth class files in dependency order
require_once XOOPS_ROOT_PATH . '/class/auth/auth.php';
require_once XOOPS_ROOT_PATH . '/class/auth/auth_provisionning.php';
require_once XOOPS_ROOT_PATH . '/class/auth/auth_ldap.php';
require_once XOOPS_ROOT_PATH . '/class/auth/auth_ads.php';

/**
 * Comprehensive tests for XoopsAuthAds — the Active Directory authentication adapter.
 *
 * XoopsAuthAds extends XoopsAuthLdap and adds:
 *   - getUPN() — builds userPrincipalName (user@domain)
 *   - authenticate() override — uses UPN instead of DN for binding
 *
 * These tests exercise all code paths that do NOT require the PHP LDAP
 * extension to be loaded.  The bootstrap provides:
 *   - xoops_getHandler('config') => XoopsTestStubConfigHandler (returns [])
 *   - xoops_utf8_encode / xoops_utf8_decode => no-op stubs
 *   - _AUTH_LDAP_* language constants
 *
 * @see \XoopsAuthAds
 * @see \XoopsAuthLdap
 * @see \XoopsAuth
 */
#[CoversClass(\XoopsAuthAds::class)]
class XoopsAuthAdsTest extends TestCase
{
    private \XoopsAuthAds $ads;

    protected function setUp(): void
    {
        $this->ads = new \XoopsAuthAds(null);
    }

    // =========================================================================
    // Constructor tests
    // =========================================================================

    public function testConstructorWithNullDao(): void
    {
        $auth = new \XoopsAuthAds(null);
        $this->assertNull($auth->_dao);
    }

    public function testConstructorWithDaoInstance(): void
    {
        $dao = $GLOBALS['xoopsDB'];
        $auth = new \XoopsAuthAds($dao);
        $this->assertSame($dao, $auth->_dao);
    }

    public function testConstructorDefaultArgument(): void
    {
        $auth = new \XoopsAuthAds();
        $this->assertNull($auth->_dao);
    }

    public function testConstructorInheritsLdapProperties(): void
    {
        $auth = new \XoopsAuthAds(null);
        // All LDAP defaults should be inherited via parent::__construct()
        $this->assertNull($auth->ldap_server);
        $this->assertSame(389, $auth->ldap_port);
        $this->assertSame('3', $auth->ldap_version);
        $this->assertNull($auth->ldap_base_dn);
    }

    public function testConstructorInheritsCp1252Map(): void
    {
        $auth = new \XoopsAuthAds(null);
        $this->assertIsArray($auth->cp1252_map);
        $this->assertCount(27, $auth->cp1252_map);
    }

    public function testConstructorErrorsArrayEmpty(): void
    {
        $this->assertSame([], $this->ads->_errors);
    }

    public function testConstructorAuthMethodEmpty(): void
    {
        $this->assertSame('', $this->ads->auth_method);
    }

    // =========================================================================
    // getUPN() tests
    // =========================================================================

    public function testGetUpnReturnsUsernameAtDomain(): void
    {
        $this->ads->ldap_domain_name = 'example.com';
        $result = $this->ads->getUPN('johndoe');
        $this->assertSame('johndoe@example.com', $result);
    }

    public function testGetUpnWithNullDomainName(): void
    {
        // ldap_domain_name defaults to null, so concatenation yields "user@"
        $this->ads->ldap_domain_name = null;
        $result = $this->ads->getUPN('user');
        $this->assertSame('user@', $result);
    }

    public function testGetUpnWithEmptyDomainName(): void
    {
        $this->ads->ldap_domain_name = '';
        $result = $this->ads->getUPN('user');
        $this->assertSame('user@', $result);
    }

    public function testGetUpnWithEmptyUsername(): void
    {
        $this->ads->ldap_domain_name = 'corp.local';
        $result = $this->ads->getUPN('');
        $this->assertSame('@corp.local', $result);
    }

    public function testGetUpnReturnsString(): void
    {
        $this->ads->ldap_domain_name = 'test.org';
        $this->assertIsString($this->ads->getUPN('user'));
    }

    public function testGetUpnWithDotsInDomainName(): void
    {
        $this->ads->ldap_domain_name = 'sub.domain.example.com';
        $result = $this->ads->getUPN('admin');
        $this->assertSame('admin@sub.domain.example.com', $result);
    }

    public function testGetUpnWithSpecialCharsInUname(): void
    {
        $this->ads->ldap_domain_name = 'example.com';
        $result = $this->ads->getUPN('user.name');
        $this->assertSame('user.name@example.com', $result);
    }

    public function testGetUpnWithHyphenInUname(): void
    {
        $this->ads->ldap_domain_name = 'example.com';
        $result = $this->ads->getUPN('first-last');
        $this->assertSame('first-last@example.com', $result);
    }

    public function testGetUpnWithUnderscoreInUname(): void
    {
        $this->ads->ldap_domain_name = 'example.com';
        $result = $this->ads->getUPN('first_last');
        $this->assertSame('first_last@example.com', $result);
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function upnDataProvider(): array
    {
        return [
            'simple user and domain' => [
                'jsmith',
                'example.com',
                'jsmith@example.com'
            ],
            'admin with internal domain' => [
                'administrator',
                'corp.local',
                'administrator@corp.local'
            ],
            'dotted username' => [
                'first.last',
                'company.org',
                'first.last@company.org'
            ],
            'numeric username' => [
                '12345',
                'test.net',
                '12345@test.net'
            ],
            'complex domain' => [
                'user',
                'sub1.sub2.domain.co.uk',
                'user@sub1.sub2.domain.co.uk'
            ],
            'hyphenated domain' => [
                'admin',
                'my-company.com',
                'admin@my-company.com'
            ],
            'single char username' => [
                'a',
                'x.com',
                'a@x.com'
            ],
            'uppercase in username' => [
                'JohnDoe',
                'Example.COM',
                'JohnDoe@Example.COM'
            ],
        ];
    }

    #[DataProvider('upnDataProvider')]
    public function testGetUpnWithDataProvider(
        string $uname,
        string $domain,
        string $expected
    ): void {
        $this->ads->ldap_domain_name = $domain;
        $this->assertSame($expected, $this->ads->getUPN($uname));
    }

    // =========================================================================
    // authenticate() tests — LDAP extension not loaded
    // =========================================================================

    public function testAuthenticateReturnsFalseWithoutLdapExtension(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $result = $this->ads->authenticate('testuser', 'password');
        $this->assertFalse($result);
    }

    public function testAuthenticateSetsErrorWhenLdapNotLoaded(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $this->ads->authenticate('testuser', 'password');
        $errors = $this->ads->getErrors();
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame(_AUTH_LDAP_EXTENSION_NOT_LOAD, $errors[0]);
    }

    public function testAuthenticateErrorKeyIsZero(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $this->ads->authenticate('user', 'pass');
        $errors = $this->ads->getErrors();
        $this->assertArrayHasKey(0, $errors);
    }

    public function testAuthenticateErrorMessageMatchesConstant(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $this->ads->authenticate('user');
        $errors = $this->ads->getErrors();
        $this->assertSame('PHP LDAP extension not loaded', $errors[0]);
    }

    public function testAuthenticateWithNullPassword(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $result = $this->ads->authenticate('user', null);
        $this->assertFalse($result);
    }

    public function testAuthenticateWithEmptyCredentials(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $result = $this->ads->authenticate('', '');
        $this->assertFalse($result);
    }

    // =========================================================================
    // Inheritance chain tests
    // =========================================================================

    public function testExtendsXoopsAuthLdap(): void
    {
        $this->assertInstanceOf(\XoopsAuthLdap::class, $this->ads);
    }

    public function testExtendsXoopsAuth(): void
    {
        $this->assertInstanceOf(\XoopsAuth::class, $this->ads);
    }

    public function testIsInstanceOfXoopsAuthAds(): void
    {
        $this->assertInstanceOf(\XoopsAuthAds::class, $this->ads);
    }

    public function testInheritedCp1252ToUtf8Method(): void
    {
        $this->assertTrue(method_exists($this->ads, 'cp1252_to_utf8'));
        $this->assertSame('Hello', $this->ads->cp1252_to_utf8('Hello'));
    }

    public function testInheritedGetFilterMethod(): void
    {
        $this->assertTrue(method_exists($this->ads, 'getFilter'));
        $this->ads->ldap_filter_person = '';
        $this->ads->ldap_loginldap_attr = 'uid';
        $this->assertSame('uid=test', $this->ads->getFilter('test'));
    }

    public function testInheritedSetErrorsGetErrors(): void
    {
        $this->ads->setErrors(42, 'Error from ADS');
        $errors = $this->ads->getErrors();
        $this->assertArrayHasKey(42, $errors);
        $this->assertSame('Error from ADS', $errors[42]);
    }

    public function testInheritedGetHtmlErrors(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->ads->setErrors(0, 'ADS Error');
        $html = $this->ads->getHtmlErrors();
        $this->assertStringContainsString('ADS Error', $html);
        unset($GLOBALS['xoopsConfig']);
    }

    // =========================================================================
    // Type safety tests
    // =========================================================================

    public function testGetUpnReturnType(): void
    {
        $this->ads->ldap_domain_name = 'test.com';
        $result = $this->ads->getUPN('user');
        $this->assertIsString($result);
    }

    public function testAuthMethodType(): void
    {
        $this->assertIsString($this->ads->auth_method);
    }

    public function testLdapPortInheritedType(): void
    {
        $this->assertIsInt($this->ads->ldap_port);
    }

    public function testLdapVersionInheritedType(): void
    {
        $this->assertIsString($this->ads->ldap_version);
    }

    public function testCp1252MapInheritedType(): void
    {
        $this->assertIsArray($this->ads->cp1252_map);
    }

    // =========================================================================
    // Method existence tests
    // =========================================================================

    public function testHasGetUpnMethod(): void
    {
        $this->assertTrue(method_exists($this->ads, 'getUPN'));
    }

    public function testHasAuthenticateMethod(): void
    {
        $this->assertTrue(method_exists($this->ads, 'authenticate'));
    }

    public function testHasGetUserDNMethodInherited(): void
    {
        $this->assertTrue(method_exists($this->ads, 'getUserDN'));
    }

    public function testHasLoadXoopsUserMethodInherited(): void
    {
        $this->assertTrue(method_exists($this->ads, 'loadXoopsUser'));
    }

    // =========================================================================
    // Property accessibility tests
    // =========================================================================

    public function testLdapDomainNameCanBeSet(): void
    {
        $this->ads->ldap_domain_name = 'newdomain.org';
        $this->assertSame('newdomain.org', $this->ads->ldap_domain_name);
    }

    public function testLdapServerCanBeSetOnAds(): void
    {
        $this->ads->ldap_server = 'ads.corp.local';
        $this->assertSame('ads.corp.local', $this->ads->ldap_server);
    }

    public function testLdapPortCanBeSetOnAds(): void
    {
        $this->ads->ldap_port = 636;
        $this->assertSame(636, $this->ads->ldap_port);
    }
}
