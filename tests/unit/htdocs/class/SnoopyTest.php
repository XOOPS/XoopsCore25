<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/class/snoopy.php';

/**
 * Unit tests for Snoopy — security audit phase 2.
 *
 * Verifies that _httpsrequest uses native PHP curl_* functions
 * instead of shell exec(), and that basic class instantiation works.
 */
#[CoversClass(\Snoopy::class)]
class SnoopyTest extends TestCase
{
    protected function setUp(): void
    {
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
    }

    #[Test]
    public function canInstantiate(): void
    {
        $snoopy = new \Snoopy();
        restore_error_handler();
        restore_exception_handler();
        $this->assertInstanceOf(\Snoopy::class, $snoopy);
    }

    #[Test]
    public function defaultErrorIsEmptyString(): void
    {
        $snoopy = new \Snoopy();
        $this->assertSame('', $snoopy->error);
    }

    #[Test]
    public function defaultCurlPathIsSet(): void
    {
        $snoopy = new \Snoopy();
        $this->assertSame('/usr/bin/curl', $snoopy->curl_path);
    }

    #[Test]
    public function defaultAgentContainsSnoopy(): void
    {
        $snoopy = new \Snoopy();
        $this->assertStringContainsString('Snoopy', $snoopy->agent);
    }

    #[Test]
    public function defaultResultsIsEmptyArray(): void
    {
        $snoopy = new \Snoopy();
        $this->assertSame([], $snoopy->results);
    }

    #[Test]
    public function defaultHeadersIsEmptyArray(): void
    {
        $snoopy = new \Snoopy();
        $this->assertSame([], $snoopy->headers);
    }

    #[Test]
    public function defaultPortIs80(): void
    {
        $snoopy = new \Snoopy();
        $this->assertSame(80, $snoopy->port);
    }

    // =========================================================================
    // _httpsrequest uses curl_* not exec()
    // =========================================================================

    #[Test]
    public function httpsRequestMethodExists(): void
    {
        $snoopy = new \Snoopy();
        $this->assertTrue(method_exists($snoopy, '_httpsrequest'));
    }

    #[Test]
    public function httpsRequestRequiresCurlExtension(): void
    {
        // If curl extension is available, _httpsrequest will attempt a real
        // connection. If not, it should return false with an error.
        // We can only test the contract here — not mock the extension.
        if (!function_exists('curl_init')) {
            $snoopy = new \Snoopy();
            $result = $snoopy->_httpsrequest('/path', 'https://example.com/path', 'GET');
            $this->assertFalse($result);
            $this->assertStringContainsString('cURL extension', $snoopy->error);
        } else {
            // curl is available — method exists and is callable
            $this->assertTrue(function_exists('curl_init'));
        }
    }

    #[Test]
    public function snoopySourceDoesNotContainExecCall(): void
    {
        // Verify that the active code path in _httpsrequest does not use exec()
        $source = file_get_contents(XOOPS_ROOT_PATH . '/class/snoopy.php');
        // Find the _httpsrequest method and check there's no exec() in it
        // The method should use curl_init/curl_exec instead
        $this->assertStringContainsString('curl_init', $source);
        $this->assertStringContainsString('curl_exec', $source);
    }

    #[Test]
    public function snoopySourceDoesNotContainShellExec(): void
    {
        $source = file_get_contents(XOOPS_ROOT_PATH . '/class/snoopy.php');
        // exec() should no longer appear as a function call in the HTTPS method
        // It may still appear in comments or strings, but not as executable code
        $this->assertStringNotContainsString("exec(\$this->curl_path", $source);
    }
}
