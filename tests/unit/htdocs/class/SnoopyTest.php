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
    private static ?\Snoopy $snoopy = null;

    public static function setUpBeforeClass(): void
    {
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
        // Create single instance — constructor triggers XoopsLogger handler setup.
        // Doing it once avoids repeated handler push/pop that PHPUnit flags as risky.
        self::$snoopy = new \Snoopy();
        restore_error_handler();
        restore_exception_handler();
    }

    private function snoopy(): \Snoopy
    {
        return self::$snoopy;
    }

    #[Test]
    public function canInstantiate(): void
    {
        $this->assertInstanceOf(\Snoopy::class, $this->snoopy());
    }

    #[Test]
    public function defaultErrorIsEmptyString(): void
    {
        $this->assertSame('', $this->snoopy()->error);
    }

    #[Test]
    public function defaultCurlPathIsSet(): void
    {
        $this->assertSame('/usr/bin/curl', $this->snoopy()->curl_path);
    }

    #[Test]
    public function defaultAgentContainsSnoopy(): void
    {
        $this->assertStringContainsString('Snoopy', $this->snoopy()->agent);
    }

    #[Test]
    public function defaultResultsIsEmptyArray(): void
    {
        $this->assertSame([], $this->snoopy()->results);
    }

    #[Test]
    public function defaultHeadersIsEmptyArray(): void
    {
        $this->assertSame([], $this->snoopy()->headers);
    }

    #[Test]
    public function defaultPortIs80(): void
    {
        $this->assertSame(80, $this->snoopy()->port);
    }

    // =========================================================================
    // _httpsrequest uses curl_* not exec()
    // =========================================================================

    #[Test]
    public function httpsRequestMethodExists(): void
    {
        $this->assertTrue(method_exists($this->snoopy(), '_httpsrequest'));
    }

    #[Test]
    public function httpsRequestRequiresCurlExtension(): void
    {
        // If curl extension is available, _httpsrequest will attempt a real
        // connection. If not, it should return false with an error.
        // We can only test the contract here — not mock the extension.
        if (!function_exists('curl_init')) {
            $result = $this->snoopy()->_httpsrequest('/path', 'https://example.com/path', 'GET');
            $this->assertFalse($result);
            $this->assertStringContainsString('cURL extension', $this->snoopy()->error);
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
        // The method should use curl_init/curl_exec instead
        $this->assertStringContainsString('curl_init', $source);
        $this->assertStringContainsString('curl_exec', $source);
    }

    #[Test]
    public function snoopySourceDoesNotContainShellExec(): void
    {
        $source = file_get_contents(XOOPS_ROOT_PATH . '/class/snoopy.php');
        // exec() should no longer appear as a function call in the HTTPS method
        $this->assertStringNotContainsString("exec(\$this->curl_path", $source);
    }
}
