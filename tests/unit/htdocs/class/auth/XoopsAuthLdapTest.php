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

/**
 * Comprehensive tests for XoopsAuthLdap — the LDAP authentication adapter.
 *
 * These tests exercise all code paths that do NOT require the PHP LDAP
 * extension to be loaded.  The bootstrap provides:
 *   - xoops_getHandler('config') => XoopsTestStubConfigHandler (returns [])
 *   - xoops_utf8_encode / xoops_utf8_decode => no-op stubs
 *   - _AUTH_LDAP_* language constants
 *
 * @see \XoopsAuthLdap
 * @see \XoopsAuth
 */
#[CoversClass(\XoopsAuthLdap::class)]
class XoopsAuthLdapTest extends TestCase
{
    private \XoopsAuthLdap $ldap;

    protected function setUp(): void
    {
        $this->ldap = new \XoopsAuthLdap(null);
    }

    // =========================================================================
    // Constructor tests
    // =========================================================================

    public function testConstructorWithNullDao(): void
    {
        $auth = new \XoopsAuthLdap(null);
        $this->assertNull($auth->_dao);
    }

    public function testConstructorWithDaoInstance(): void
    {
        $dao = $GLOBALS['xoopsDB'];
        $auth = new \XoopsAuthLdap($dao);
        $this->assertSame($dao, $auth->_dao);
    }

    public function testConstructorDefaultSetsDao(): void
    {
        $auth = new \XoopsAuthLdap();
        $this->assertNull($auth->_dao);
    }

    public function testConstructorLoadsEmptyConfig(): void
    {
        // The stub config handler returns [], so no dynamic properties
        // should be set.  The declared defaults should remain.
        $auth = new \XoopsAuthLdap(null);
        $this->assertNull($auth->ldap_server);
        $this->assertSame(389, $auth->ldap_port);
    }

    public function testAuthMethodIsEmptyByDefault(): void
    {
        // Parent XoopsAuth sets auth_method = ''
        $this->assertSame('', $this->ldap->auth_method);
    }

    public function testErrorsArrayIsEmptyByDefault(): void
    {
        $this->assertSame([], $this->ldap->_errors);
    }

    // =========================================================================
    // Property default tests
    // =========================================================================

    public function testLdapServerIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_server);
    }

    public function testLdapPortDefaultIs389(): void
    {
        $this->assertSame(389, $this->ldap->ldap_port);
    }

    public function testLdapVersionDefaultIsThree(): void
    {
        $this->assertSame('3', $this->ldap->ldap_version);
    }

    public function testLdapBaseDnIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_base_dn);
    }

    public function testLdapLoginnameAsdnIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_loginname_asdn);
    }

    public function testLdapLoginldapAttrIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_loginldap_attr);
    }

    public function testLdapMailAttrIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_mail_attr);
    }

    public function testLdapNameAttrIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_name_attr);
    }

    public function testLdapSurnameAttrIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_surname_attr);
    }

    public function testLdapGivennameAttrIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_givenname_attr);
    }

    public function testLdapManagerDnIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_manager_dn);
    }

    public function testLdapManagerPassIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_manager_pass);
    }

    public function testLdapFilterPersonIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_filter_person);
    }

    public function testLdapUseTlsIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_use_TLS);
    }

    public function testLdapDomainNameIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_domain_name);
    }

    public function testLdapProvisionningIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_provisionning);
    }

    public function testLdapProvisionningUpdIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_provisionning_upd);
    }

    public function testLdapProvisionningGroupIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_provisionning_group);
    }

    public function testLdapFieldMappingIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_field_mapping);
    }

    public function testLdapUsersBypassIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_users_bypass);
    }

    public function testLdapFilterPersonAdvIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_filter_person_adv);
    }

    public function testLdapFilterAttrIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_filter_attr);
    }

    public function testLdapFilterValueIsNullByDefault(): void
    {
        $this->assertNull($this->ldap->ldap_filter_value);
    }

    // =========================================================================
    // cp1252_map tests
    // =========================================================================

    public function testCp1252MapIsArray(): void
    {
        $this->assertIsArray($this->ldap->cp1252_map);
    }

    public function testCp1252MapHas27Entries(): void
    {
        $this->assertCount(27, $this->ldap->cp1252_map);
    }

    public function testCp1252MapContainsEuroSign(): void
    {
        // "\xc2\x80" => "\xe2\x82\xac" (Euro sign)
        $this->assertArrayHasKey("\xc2\x80", $this->ldap->cp1252_map);
        $this->assertSame("\xe2\x82\xac", $this->ldap->cp1252_map["\xc2\x80"]);
    }

    public function testCp1252MapContainsTrademarkSign(): void
    {
        // "\xc2\x99" => "\xe2\x84\xa2" (Trademark sign)
        $this->assertArrayHasKey("\xc2\x99", $this->ldap->cp1252_map);
        $this->assertSame("\xe2\x84\xa2", $this->ldap->cp1252_map["\xc2\x99"]);
    }

    public function testCp1252MapKeysAreStrings(): void
    {
        foreach ($this->ldap->cp1252_map as $key => $val) {
            $this->assertIsString($key);
            $this->assertIsString($val);
        }
    }

    // =========================================================================
    // cp1252_to_utf8() tests
    // =========================================================================

    public function testCp1252ToUtf8EmptyString(): void
    {
        $this->assertSame('', $this->ldap->cp1252_to_utf8(''));
    }

    public function testCp1252ToUtf8AsciiPassesThrough(): void
    {
        $this->assertSame('Hello World', $this->ldap->cp1252_to_utf8('Hello World'));
    }

    public function testCp1252ToUtf8ReturnsString(): void
    {
        $result = $this->ldap->cp1252_to_utf8('test');
        $this->assertIsString($result);
    }

    public function testCp1252ToUtf8WithNumbers(): void
    {
        $this->assertSame('12345', $this->ldap->cp1252_to_utf8('12345'));
    }

    public function testCp1252ToUtf8WithSpecialChars(): void
    {
        $this->assertSame('a!@#$%^&*()', $this->ldap->cp1252_to_utf8('a!@#$%^&*()'));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function cp1252DataProvider(): array
    {
        return [
            'empty string'         => ['', ''],
            'plain ascii'          => ['Hello', 'Hello'],
            'digits only'          => ['0123456789', '0123456789'],
            'whitespace'           => ["  \t\n", "  \t\n"],
            'single character'     => ['A', 'A'],
            'mixed case'           => ['AbCdEf', 'AbCdEf'],
            'punctuation'          => ['!?.,;:', '!?.,;:'],
        ];
    }

    #[DataProvider('cp1252DataProvider')]
    public function testCp1252ToUtf8WithDataProvider(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->ldap->cp1252_to_utf8($input));
    }

    // =========================================================================
    // getFilter() tests
    // =========================================================================

    public function testGetFilterWithoutCustomFilterUsesLoginAttr(): void
    {
        $this->ldap->ldap_filter_person = '';
        $this->ldap->ldap_loginldap_attr = 'uid';
        $result = $this->ldap->getFilter('johndoe');
        $this->assertSame('uid=johndoe', $result);
    }

    public function testGetFilterWithNullFilterPersonUsesLoginAttr(): void
    {
        $this->ldap->ldap_filter_person = null;
        $this->ldap->ldap_loginldap_attr = 'cn';
        $result = $this->ldap->getFilter('admin');
        $this->assertSame('cn=admin', $result);
    }

    public function testGetFilterWithCustomFilterReplacesPlaceholder(): void
    {
        $this->ldap->ldap_filter_person = '(&(objectClass=person)(uid=@@loginname@@))';
        $result = $this->ldap->getFilter('jsmith');
        $this->assertSame('(&(objectClass=person)(uid=jsmith))', $result);
    }

    public function testGetFilterCustomWithoutPlaceholder(): void
    {
        $this->ldap->ldap_filter_person = '(objectClass=person)';
        $result = $this->ldap->getFilter('anyone');
        // No @@loginname@@ in filter, so str_replace is a no-op
        $this->assertSame('(objectClass=person)', $result);
    }

    public function testGetFilterWithSpecialCharsInUname(): void
    {
        $this->ldap->ldap_filter_person = '';
        $this->ldap->ldap_loginldap_attr = 'uid';
        $result = $this->ldap->getFilter('user@domain.com');
        $this->assertSame('uid=user@domain.com', $result);
    }

    public function testGetFilterWithEmptyUname(): void
    {
        $this->ldap->ldap_filter_person = '';
        $this->ldap->ldap_loginldap_attr = 'uid';
        $result = $this->ldap->getFilter('');
        $this->assertSame('uid=', $result);
    }

    public function testGetFilterReturnsString(): void
    {
        $this->ldap->ldap_filter_person = '';
        $this->ldap->ldap_loginldap_attr = 'uid';
        $this->assertIsString($this->ldap->getFilter('test'));
    }

    /**
     * @return array<string, array{string, string|null, string, string}>
     */
    public static function filterDataProvider(): array
    {
        return [
            'simple uid attr' => [
                'uid',    // ldap_loginldap_attr
                '',       // ldap_filter_person
                'bob',    // uname
                'uid=bob' // expected
            ],
            'cn attr with empty filter' => [
                'cn',
                '',
                'alice',
                'cn=alice'
            ],
            'sAMAccountName attr' => [
                'sAMAccountName',
                '',
                'testuser',
                'sAMAccountName=testuser'
            ],
            'custom filter with placeholder' => [
                'uid',
                '(&(objectClass=user)(sAMAccountName=@@loginname@@))',
                'john',
                '(&(objectClass=user)(sAMAccountName=john))'
            ],
            'custom filter with multiple placeholders' => [
                'uid',
                '(|(uid=@@loginname@@)(mail=@@loginname@@))',
                'admin',
                '(|(uid=admin)(mail=admin))'
            ],
            'custom filter no placeholder' => [
                'uid',
                '(objectClass=*)',
                'nobody',
                '(objectClass=*)'
            ],
            'uname with dots' => [
                'uid',
                '',
                'first.last',
                'uid=first.last'
            ],
            'uname with spaces' => [
                'uid',
                '',
                'John Doe',
                'uid=John Doe'
            ],
        ];
    }

    #[DataProvider('filterDataProvider')]
    public function testGetFilterWithDataProvider(
        string $loginAttr,
        ?string $filterPerson,
        string $uname,
        string $expected
    ): void {
        $this->ldap->ldap_loginldap_attr = $loginAttr;
        $this->ldap->ldap_filter_person = $filterPerson;
        $this->assertSame($expected, $this->ldap->getFilter($uname));
    }

    // =========================================================================
    // authenticate() tests — LDAP extension not loaded
    // =========================================================================

    public function testAuthenticateReturnsFalseWithoutLdapExtension(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $result = $this->ldap->authenticate('testuser', 'password');
        $this->assertFalse($result);
    }

    public function testAuthenticateSetsErrorWhenLdapNotLoaded(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $this->ldap->authenticate('testuser', 'password');
        $errors = $this->ldap->getErrors();
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame(_AUTH_LDAP_EXTENSION_NOT_LOAD, $errors[0]);
    }

    public function testAuthenticateErrorMessageContent(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $this->ldap->authenticate('anyuser');
        $errors = $this->ldap->getErrors();
        $this->assertSame('PHP LDAP extension not loaded', $errors[0]);
    }

    public function testAuthenticateWithNullPassword(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $result = $this->ldap->authenticate('user', null);
        $this->assertFalse($result);
    }

    public function testAuthenticateWithEmptyUsername(): void
    {
        if (extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is loaded; cannot test no-extension path.');
        }
        $result = $this->ldap->authenticate('', '');
        $this->assertFalse($result);
    }

    // =========================================================================
    // Inheritance tests
    // =========================================================================

    public function testExtendsXoopsAuth(): void
    {
        $this->assertInstanceOf(\XoopsAuth::class, $this->ldap);
    }

    public function testIsInstanceOfXoopsAuthLdap(): void
    {
        $this->assertInstanceOf(\XoopsAuthLdap::class, $this->ldap);
    }

    public function testSetErrorsAndGetErrors(): void
    {
        $this->ldap->setErrors(1, 'Test error');
        $errors = $this->ldap->getErrors();
        $this->assertArrayHasKey(1, $errors);
        $this->assertSame('Test error', $errors[1]);
    }

    public function testSetErrorsMultiple(): void
    {
        $this->ldap->setErrors(0, 'First error');
        $this->ldap->setErrors(1, 'Second error');
        $errors = $this->ldap->getErrors();
        $this->assertCount(2, $errors);
        $this->assertSame('First error', $errors[0]);
        $this->assertSame('Second error', $errors[1]);
    }

    public function testSetErrorsOverwritesSameKey(): void
    {
        $this->ldap->setErrors(0, 'Original');
        $this->ldap->setErrors(0, 'Replaced');
        $errors = $this->ldap->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('Replaced', $errors[0]);
    }

    public function testGetHtmlErrorsWithDebugMode(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $this->ldap->setErrors(0, 'Test error message');
        $html = $this->ldap->getHtmlErrors();
        $this->assertStringContainsString('Test error message', $html);
        $this->assertStringContainsString('<br>', $html);
        unset($GLOBALS['xoopsConfig']);
    }

    public function testGetHtmlErrorsWithoutDebugMode(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 0];
        $html = $this->ldap->getHtmlErrors();
        $this->assertStringContainsString(_US_INCORRECTLOGIN, $html);
        unset($GLOBALS['xoopsConfig']);
    }

    public function testGetHtmlErrorsWithDebugModeShowsAuthMethod(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 2];
        $this->ldap->auth_method = 'ldap';
        $html = $this->ldap->getHtmlErrors();
        $this->assertStringContainsString('ldap', $html);
        unset($GLOBALS['xoopsConfig']);
    }

    public function testGetHtmlErrorsWithEmptyErrorsShowsNone(): void
    {
        $GLOBALS['xoopsConfig'] = ['debug_mode' => 1];
        $html = $this->ldap->getHtmlErrors();
        $this->assertStringContainsString(_NONE, $html);
        unset($GLOBALS['xoopsConfig']);
    }

    // =========================================================================
    // Type safety tests
    // =========================================================================

    public function testCp1252MapIsArrayType(): void
    {
        $this->assertIsArray($this->ldap->cp1252_map);
    }

    public function testLdapPortIsIntType(): void
    {
        $this->assertIsInt($this->ldap->ldap_port);
    }

    public function testLdapVersionIsStringType(): void
    {
        $this->assertIsString($this->ldap->ldap_version);
    }

    public function testAuthMethodIsStringType(): void
    {
        $this->assertIsString($this->ldap->auth_method);
    }

    public function testErrorsIsArrayType(): void
    {
        $this->assertIsArray($this->ldap->_errors);
    }

    // =========================================================================
    // Property mutability tests (simulating config loading)
    // =========================================================================

    public function testLdapServerCanBeSet(): void
    {
        $this->ldap->ldap_server = 'ldap.example.com';
        $this->assertSame('ldap.example.com', $this->ldap->ldap_server);
    }

    public function testLdapPortCanBeSet(): void
    {
        $this->ldap->ldap_port = 636;
        $this->assertSame(636, $this->ldap->ldap_port);
    }

    public function testLdapVersionCanBeSet(): void
    {
        $this->ldap->ldap_version = '2';
        $this->assertSame('2', $this->ldap->ldap_version);
    }

    public function testLdapBaseDnCanBeSet(): void
    {
        $this->ldap->ldap_base_dn = 'dc=example,dc=com';
        $this->assertSame('dc=example,dc=com', $this->ldap->ldap_base_dn);
    }

    public function testLdapUseTlsCanBeSet(): void
    {
        $this->ldap->ldap_use_TLS = true;
        $this->assertTrue($this->ldap->ldap_use_TLS);
    }

    // =========================================================================
    // Method existence tests
    // =========================================================================

    public function testHasAuthenticateMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'authenticate'));
    }

    public function testHasGetUserDNMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'getUserDN'));
    }

    public function testHasGetFilterMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'getFilter'));
    }

    public function testHasLoadXoopsUserMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'loadXoopsUser'));
    }

    public function testHasCp1252ToUtf8Method(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'cp1252_to_utf8'));
    }

    public function testHasSetErrorsMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'setErrors'));
    }

    public function testHasGetErrorsMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'getErrors'));
    }

    public function testHasGetHtmlErrorsMethod(): void
    {
        $this->assertTrue(method_exists($this->ldap, 'getHtmlErrors'));
    }
}
