<?php
declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/class/userutility.php';

/**
 * Unit tests for XoopsUserUtility::getIP().
 *
 * This class tests the static getIP() method which checks various proxy-related
 * $_SERVER headers and falls back to Xmf\IPAddress::fromRequest() (which uses
 * REMOTE_ADDR). The method returns either a dotted-quad string or an ip2long()
 * integer depending on the $asString parameter.
 *
 * Other methods (sendWelcome, validate, getUnameFromId, getUnameFromIds) depend
 * heavily on DB/mailer/global state and are not tested here.
 */
#[CoversClass(\XoopsUserUtility::class)]
class XoopsUserUtilityTest extends TestCase
{
    /**
     * @var array Backup of the original $_SERVER superglobal
     */
    private array $originalServer;

    /**
     * @var bool Whether Xmf\IPAddress is available in this environment
     */
    private static bool $xmfAvailable = false;

    public static function setUpBeforeClass(): void
    {
        self::$xmfAvailable = class_exists('Xmf\\IPAddress');
    }

    protected function setUp(): void
    {
        if (!self::$xmfAvailable) {
            $this->markTestSkipped('Xmf\\IPAddress class is not available in this test environment.');
        }

        $this->originalServer = $_SERVER;

        // Clear all proxy-related headers so each test starts from a clean state
        unset(
            $_SERVER['HTTP_X_FORWARDED_FOR'],
            $_SERVER['HTTP_X_FORWARDED'],
            $_SERVER['HTTP_FORWARDED_FOR'],
            $_SERVER['HTTP_FORWARDED'],
            $_SERVER['HTTP_VIA'],
            $_SERVER['HTTP_X_COMING_FROM'],
            $_SERVER['HTTP_COMING_FROM']
        );

        // Ensure REMOTE_ADDR has a known default
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // Ensure ProxyCheck does not interfere: clear xoopsConfig proxy_env
        // so ProxyCheck::getProxyEnvConfig() returns false and fromRequest()
        // uses REMOTE_ADDR directly.
        if (isset($GLOBALS['xoopsConfig'])) {
            unset($GLOBALS['xoopsConfig']['proxy_env']);
        }
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
    }

    // ---------------------------------------------------------------
    // Basic return type tests
    // ---------------------------------------------------------------

    /**
     * getIP(false) should return an integer (ip2long result) by default.
     */
    public function testGetIPDefaultReturnsInteger(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $result = \XoopsUserUtility::getIP(false);

        $this->assertIsInt($result);
    }

    /**
     * getIP(true) should return a dotted-quad string.
     */
    public function testGetIPAsStringReturnsString(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertIsString($result);
    }

    /**
     * When called with no arguments, getIP() defaults to $asString=false
     * and returns an integer.
     */
    public function testGetIPNoArgumentReturnsInteger(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';

        $result = \XoopsUserUtility::getIP();

        $this->assertIsInt($result);
    }

    // ---------------------------------------------------------------
    // REMOTE_ADDR fallback tests
    // ---------------------------------------------------------------

    /**
     * When no proxy headers are set, getIP(true) should return REMOTE_ADDR.
     */
    public function testFallsBackToRemoteAddrWhenNoProxyHeaders(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.50';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('203.0.113.50', $result);
    }

    /**
     * When no proxy headers are set, getIP(false) should return ip2long of REMOTE_ADDR.
     */
    public function testFallsBackToRemoteAddrAsIntegerWhenNoProxyHeaders(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.50';

        $result = \XoopsUserUtility::getIP(false);

        $this->assertSame(ip2long('203.0.113.50'), $result);
    }

    /**
     * When all proxy headers are set to empty string, should use REMOTE_ADDR.
     */
    public function testUsesRemoteAddrWhenAllProxyHeadersEmpty(): void
    {
        $_SERVER['REMOTE_ADDR'] = '198.51.100.25';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $_SERVER['HTTP_X_FORWARDED']     = '';
        $_SERVER['HTTP_FORWARDED_FOR']   = '';
        $_SERVER['HTTP_FORWARDED']       = '';
        $_SERVER['HTTP_VIA']             = '';
        $_SERVER['HTTP_X_COMING_FROM']   = '';
        $_SERVER['HTTP_COMING_FROM']     = '';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('198.51.100.25', $result);
    }

    // ---------------------------------------------------------------
    // Individual proxy header tests
    // ---------------------------------------------------------------

    /**
     * HTTP_X_FORWARDED_FOR should be used when present.
     */
    public function testReadsHttpXForwardedFor(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.10';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.1.10', $result);
    }

    /**
     * HTTP_X_FORWARDED should be used when present (and X_FORWARDED_FOR absent).
     */
    public function testReadsHttpXForwarded(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED'] = '192.168.2.20';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.2.20', $result);
    }

    /**
     * HTTP_FORWARDED_FOR should be used when present (and higher-priority headers absent).
     */
    public function testReadsHttpForwardedFor(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_FORWARDED_FOR'] = '192.168.3.30';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.3.30', $result);
    }

    /**
     * HTTP_FORWARDED should be used when present (and higher-priority headers absent).
     */
    public function testReadsHttpForwarded(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_FORWARDED'] = '192.168.4.40';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.4.40', $result);
    }

    /**
     * HTTP_VIA should be used when present (and higher-priority headers absent).
     */
    public function testReadsHttpVia(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_VIA'] = '192.168.5.50';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.5.50', $result);
    }

    /**
     * HTTP_X_COMING_FROM should be used when present (and higher-priority headers absent).
     */
    public function testReadsHttpXComingFrom(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_COMING_FROM'] = '192.168.6.60';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.6.60', $result);
    }

    /**
     * HTTP_COMING_FROM should be used when present (and higher-priority headers absent).
     */
    public function testReadsHttpComingFrom(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_COMING_FROM'] = '192.168.7.70';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('192.168.7.70', $result);
    }

    // ---------------------------------------------------------------
    // Priority / precedence tests
    // ---------------------------------------------------------------

    /**
     * HTTP_X_FORWARDED_FOR takes precedence over all other proxy headers.
     */
    public function testXForwardedForTakesPrecedenceOverOtherHeaders(): void
    {
        $_SERVER['REMOTE_ADDR']          = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.1.1.1';
        $_SERVER['HTTP_X_FORWARDED']     = '10.2.2.2';
        $_SERVER['HTTP_FORWARDED_FOR']   = '10.3.3.3';
        $_SERVER['HTTP_FORWARDED']       = '10.4.4.4';
        $_SERVER['HTTP_VIA']             = '10.5.5.5';
        $_SERVER['HTTP_X_COMING_FROM']   = '10.6.6.6';
        $_SERVER['HTTP_COMING_FROM']     = '10.7.7.7';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('10.1.1.1', $result);
    }

    /**
     * HTTP_X_FORWARDED takes precedence when HTTP_X_FORWARDED_FOR is absent.
     */
    public function testXForwardedTakesPrecedenceOverLowerHeaders(): void
    {
        $_SERVER['REMOTE_ADDR']        = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED']   = '10.2.2.2';
        $_SERVER['HTTP_FORWARDED_FOR'] = '10.3.3.3';
        $_SERVER['HTTP_FORWARDED']     = '10.4.4.4';
        $_SERVER['HTTP_VIA']           = '10.5.5.5';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('10.2.2.2', $result);
    }

    /**
     * HTTP_FORWARDED_FOR takes precedence when higher-priority headers are absent.
     */
    public function testForwardedForTakesPrecedenceOverLowerHeaders(): void
    {
        $_SERVER['REMOTE_ADDR']        = '127.0.0.1';
        $_SERVER['HTTP_FORWARDED_FOR'] = '10.3.3.3';
        $_SERVER['HTTP_FORWARDED']     = '10.4.4.4';
        $_SERVER['HTTP_VIA']           = '10.5.5.5';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('10.3.3.3', $result);
    }

    // ---------------------------------------------------------------
    // IPv4 correctness tests
    // ---------------------------------------------------------------

    /**
     * A standard public IPv4 address should be returned as-is in string mode.
     */
    public function testHandlesPublicIpv4Correctly(): void
    {
        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('8.8.8.8', $result);
    }

    /**
     * The ip2long value should match PHP's ip2long() for the same address.
     */
    public function testIp2longMatchesPhpIp2long(): void
    {
        $_SERVER['REMOTE_ADDR'] = '172.16.254.1';

        $result = \XoopsUserUtility::getIP(false);

        $this->assertSame(ip2long('172.16.254.1'), $result);
    }

    /**
     * Test with the loopback address 127.0.0.1.
     */
    public function testHandlesLoopbackAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $resultString  = \XoopsUserUtility::getIP(true);
        $resultInteger = \XoopsUserUtility::getIP(false);

        $this->assertSame('127.0.0.1', $resultString);
        $this->assertSame(ip2long('127.0.0.1'), $resultInteger);
    }

    /**
     * Test with the address 0.0.0.0.
     */
    public function testHandlesZeroAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '0.0.0.0';

        $resultString = \XoopsUserUtility::getIP(true);

        $this->assertSame('0.0.0.0', $resultString);
    }

    /**
     * Test with 255.255.255.255 (broadcast address).
     */
    public function testHandlesBroadcastAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '255.255.255.255';

        $resultString  = \XoopsUserUtility::getIP(true);
        $resultInteger = \XoopsUserUtility::getIP(false);

        $this->assertSame('255.255.255.255', $resultString);
        $this->assertSame(ip2long('255.255.255.255'), $resultInteger);
    }

    // ---------------------------------------------------------------
    // Invalid proxy IP tests (falls back to REMOTE_ADDR)
    // ---------------------------------------------------------------

    /**
     * When the proxy header contains an invalid IP, getIP should fall back
     * to Xmf\IPAddress::fromRequest() which uses REMOTE_ADDR.
     */
    public function testInvalidProxyIpFallsBackToRemoteAddr(): void
    {
        $_SERVER['REMOTE_ADDR']          = '198.51.100.10';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'not-a-valid-ip';

        $result = \XoopsUserUtility::getIP(true);

        // The invalid proxy IP causes Xmf\IPAddress->asReadable() to return false,
        // so getIP falls back to Xmf\IPAddress::fromRequest() which uses REMOTE_ADDR.
        $this->assertSame('198.51.100.10', $result);
    }

    /**
     * A proxy header containing "unknown" (commonly sent by proxies) should
     * fall back to REMOTE_ADDR.
     */
    public function testProxyHeaderUnknownFallsBackToRemoteAddr(): void
    {
        $_SERVER['REMOTE_ADDR']          = '198.51.100.20';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'unknown';

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame('198.51.100.20', $result);
    }

    // ---------------------------------------------------------------
    // Proxy header with valid IP in integer mode
    // ---------------------------------------------------------------

    /**
     * When a proxy header contains a valid IP and asString is false,
     * the result should be the ip2long() of that proxy IP.
     */
    public function testProxyHeaderReturnsIntegerWhenAsStringFalse(): void
    {
        $_SERVER['REMOTE_ADDR']          = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.20.30.40';

        $result = \XoopsUserUtility::getIP(false);

        $this->assertSame(ip2long('10.20.30.40'), $result);
    }

    // ---------------------------------------------------------------
    // Data provider tests for multiple IPs
    // ---------------------------------------------------------------

    /**
     * @return array<string, array{string, int}>
     */
    public static function ipAddressProvider(): array
    {
        return [
            'class A private'  => ['10.0.0.1',       ip2long('10.0.0.1')],
            'class B private'  => ['172.16.0.1',      ip2long('172.16.0.1')],
            'class C private'  => ['192.168.0.1',     ip2long('192.168.0.1')],
            'public DNS'       => ['8.8.4.4',         ip2long('8.8.4.4')],
            'loopback'         => ['127.0.0.1',       ip2long('127.0.0.1')],
            'high octets'      => ['250.200.150.100', ip2long('250.200.150.100')],
        ];
    }

    /**
     * @param string $ip       The IP address to set as REMOTE_ADDR
     * @param int    $expected The expected ip2long() value
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('ipAddressProvider')]
    public function testVariousIpAddressesReturnCorrectIp2long(string $ip, int $expected): void
    {
        $_SERVER['REMOTE_ADDR'] = $ip;

        $result = \XoopsUserUtility::getIP(false);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function ipStringProvider(): array
    {
        return [
            'class A private'  => ['10.0.0.1'],
            'class B private'  => ['172.16.0.1'],
            'class C private'  => ['192.168.0.1'],
            'public DNS'       => ['8.8.4.4'],
            'loopback'         => ['127.0.0.1'],
            'high octets'      => ['250.200.150.100'],
        ];
    }

    /**
     * @param string $ip The IP address to set as REMOTE_ADDR
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('ipStringProvider')]
    public function testVariousIpAddressesReturnCorrectString(string $ip): void
    {
        $_SERVER['REMOTE_ADDR'] = $ip;

        $result = \XoopsUserUtility::getIP(true);

        $this->assertSame($ip, $result);
    }

    // ---------------------------------------------------------------
    // Edge case: REMOTE_ADDR missing entirely
    // ---------------------------------------------------------------

    /**
     * When REMOTE_ADDR is not set (e.g., CLI), Xmf\IPAddress::fromRequest()
     * defaults to '0.0.0.0'. The result should reflect that.
     */
    public function testMissingRemoteAddrDefaultsToZeroAddress(): void
    {
        unset($_SERVER['REMOTE_ADDR']);

        $result = \XoopsUserUtility::getIP(true);

        // Xmf\IPAddress::fromRequest() uses '0.0.0.0' when REMOTE_ADDR is absent
        $this->assertSame('0.0.0.0', $result);
    }
}
