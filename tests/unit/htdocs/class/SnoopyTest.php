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
 * Verifies that shell execution in _httpsrequest has been disabled
 * and that basic class instantiation still works.
 */
#[CoversClass(\Snoopy::class)]
class SnoopyTest extends TestCase
{
    protected function setUp(): void
    {
        // Snoopy constructor calls $GLOBALS['xoopsLogger']->addDeprecated()
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
    // _httpsrequest shell exec disabled
    // =========================================================================

    #[Test]
    public function httpsRequestReturnsFalse(): void
    {
        $snoopy = new \Snoopy();
        $result = $snoopy->_httpsrequest('/path', 'https://example.com/path', 'GET');
        $this->assertFalse($result);
    }

    #[Test]
    public function httpsRequestSetsSecurityErrorMessage(): void
    {
        $snoopy = new \Snoopy();
        $snoopy->_httpsrequest('/path', 'https://example.com/path', 'GET');
        $this->assertStringContainsString(
            'shell execution has been disabled',
            $snoopy->error
        );
    }

    #[Test]
    public function httpsRequestErrorSuggestsCurl(): void
    {
        $snoopy = new \Snoopy();
        $snoopy->_httpsrequest('/path', 'https://example.com/path', 'GET');
        $this->assertStringContainsString('cURL', $snoopy->error);
    }

    #[Test]
    public function httpsRequestWithPostAlsoReturnsFalse(): void
    {
        $snoopy = new \Snoopy();
        $result = $snoopy->_httpsrequest(
            '/submit',
            'https://example.com/submit',
            'POST',
            'application/x-www-form-urlencoded',
            'key=value'
        );
        $this->assertFalse($result);
    }
}
