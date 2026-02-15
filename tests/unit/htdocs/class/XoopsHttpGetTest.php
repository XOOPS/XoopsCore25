<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use XoopsHttpGet;

require_once XOOPS_ROOT_PATH . '/class/xoopshttpget.php';

/**
 * Testable subclass of XoopsHttpGet that overrides the protected fetch methods
 * to track which method was called without performing real HTTP requests.
 */
class TestableXoopsHttpGet extends XoopsHttpGet
{
    public bool $fetchCurlCalled = false;
    public bool $fetchFopenCalled = false;
    public string $mockResponse = 'test response';

    protected function fetchCurl()
    {
        $this->fetchCurlCalled = true;
        return $this->mockResponse;
    }

    protected function fetchFopen()
    {
        $this->fetchFopenCalled = true;
        return $this->mockResponse;
    }
}

/**
 * Comprehensive unit tests for XoopsHttpGet.
 *
 * XoopsHttpGet is a simple HTTP client that fetches content from a URL
 * using either the curl extension or file_get_contents (allow_url_fopen).
 * It prefers curl when available, and falls back to fopen wrappers.
 *
 * Tested API:
 *   - __construct($url)  Stores URL, detects curl availability
 *   - fetch()            Dispatches to fetchCurl() or fetchFopen()
 *   - getError()         Returns any error string set during fetch()
 *
 */
#[CoversClass(XoopsHttpGet::class)]
class XoopsHttpGetTest extends TestCase
{
    // ---------------------------------------------------------------
    //  Constructor tests
    // ---------------------------------------------------------------

    /**
     * Constructor stores the provided URL in the protected $url property.
     */
    public function testConstructorStoresUrl(): void
    {
        $url = 'https://example.com/test';
        $httpGet = new XoopsHttpGet($url);

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'url');
        $ref->setAccessible(true);

        $this->assertSame($url, $ref->getValue($httpGet));
    }

    /**
     * Constructor sets useCurl to true when curl_init is available.
     *
     * On this system (PHP 8.4 with curl extension), curl_init() exists,
     * so useCurl should remain true (its default).
     */
    public function testConstructorSetUseCurlTrueWhenCurlAvailable(): void
    {
        $this->assertTrue(
            function_exists('curl_init'),
            'Precondition: curl_init must be available on this system'
        );

        $httpGet = new XoopsHttpGet('https://example.com');

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'useCurl');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($httpGet));
    }

    /**
     * Constructor accepts an empty URL without throwing.
     *
     * The constructor does not validate the URL; it only stores it.
     */
    public function testConstructorAcceptsEmptyUrl(): void
    {
        $httpGet = new XoopsHttpGet('');

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'url');
        $ref->setAccessible(true);

        $this->assertSame('', $ref->getValue($httpGet));
    }

    /**
     * Constructor stores various URL formats correctly.
     */
    #[DataProvider('urlProvider')]
    public function testConstructorStoresVariousUrlFormats(string $url): void
    {
        $httpGet = new XoopsHttpGet($url);

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'url');
        $ref->setAccessible(true);

        $this->assertSame($url, $ref->getValue($httpGet));
    }

    /**
     * Provides various URL formats for testing.
     *
     * @return array<string, array{string}>
     */
    public static function urlProvider(): array
    {
        return [
            'https url'              => ['https://example.com'],
            'http url'               => ['http://example.com'],
            'url with path'          => ['https://example.com/path/to/resource'],
            'url with query string'  => ['https://example.com/search?q=test&page=1'],
            'url with fragment'      => ['https://example.com/page#section'],
            'url with port'          => ['https://example.com:8080/api'],
            'url with auth'          => ['https://user:pass@example.com'],
            'ip address url'         => ['http://192.168.1.1/status'],
            'localhost url'          => ['http://localhost/test'],
            'ftp url'                => ['ftp://files.example.com/pub/file.txt'],
            'url with unicode'       => ['https://example.com/path?name=%E4%B8%AD%E6%96%87'],
            'empty string'           => [''],
            'just a string'          => ['not-a-real-url'],
        ];
    }

    // ---------------------------------------------------------------
    //  getError() tests
    // ---------------------------------------------------------------

    /**
     * getError() returns null initially when no error has been set.
     */
    public function testGetErrorReturnsNullInitially(): void
    {
        $httpGet = new XoopsHttpGet('https://example.com');
        $this->assertNull($httpGet->getError());
    }

    /**
     * getError() returns the error string after it has been set via reflection.
     */
    public function testGetErrorReturnsErrorStringAfterSetting(): void
    {
        $httpGet = new XoopsHttpGet('https://example.com');

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'error');
        $ref->setAccessible(true);
        $ref->setValue($httpGet, 'Something went wrong');

        $this->assertSame('Something went wrong', $httpGet->getError());
    }

    /**
     * getError() returns the exact error value that was set, including empty string.
     */
    public function testGetErrorReturnsEmptyStringWhenSetToEmpty(): void
    {
        $httpGet = new XoopsHttpGet('https://example.com');

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'error');
        $ref->setAccessible(true);
        $ref->setValue($httpGet, '');

        $this->assertSame('', $httpGet->getError());
    }

    // ---------------------------------------------------------------
    //  fetch() dispatching tests (using TestableXoopsHttpGet)
    // ---------------------------------------------------------------

    /**
     * fetch() dispatches to fetchCurl() when useCurl is true.
     */
    public function testFetchDispatchesToFetchCurlWhenUseCurlIsTrue(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');

        // Ensure useCurl is true (default when curl is available)
        $ref = new ReflectionProperty(XoopsHttpGet::class, 'useCurl');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($httpGet), 'Precondition: useCurl should be true');

        $result = $httpGet->fetch();

        $this->assertTrue($httpGet->fetchCurlCalled, 'fetchCurl() should have been called');
        $this->assertFalse($httpGet->fetchFopenCalled, 'fetchFopen() should NOT have been called');
        $this->assertSame('test response', $result);
    }

    /**
     * fetch() dispatches to fetchFopen() when useCurl is false.
     */
    public function testFetchDispatchesToFetchFopenWhenUseCurlIsFalse(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');

        // Force useCurl to false via reflection
        $ref = new ReflectionProperty(XoopsHttpGet::class, 'useCurl');
        $ref->setAccessible(true);
        $ref->setValue($httpGet, false);

        $result = $httpGet->fetch();

        $this->assertFalse($httpGet->fetchCurlCalled, 'fetchCurl() should NOT have been called');
        $this->assertTrue($httpGet->fetchFopenCalled, 'fetchFopen() should have been called');
        $this->assertSame('test response', $result);
    }

    /**
     * fetch() returns the response from fetchCurl() when useCurl is true.
     */
    public function testFetchReturnsCurlResponse(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');
        $httpGet->mockResponse = 'curl response data';

        $result = $httpGet->fetch();

        $this->assertSame('curl response data', $result);
    }

    /**
     * fetch() returns the response from fetchFopen() when useCurl is false.
     */
    public function testFetchReturnsFopenResponse(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');
        $httpGet->mockResponse = 'fopen response data';

        // Force useCurl to false
        $ref = new ReflectionProperty(XoopsHttpGet::class, 'useCurl');
        $ref->setAccessible(true);
        $ref->setValue($httpGet, false);

        $result = $httpGet->fetch();

        $this->assertSame('fopen response data', $result);
    }

    // ---------------------------------------------------------------
    //  Protected property default values
    // ---------------------------------------------------------------

    /**
     * The $error property defaults to null (uninitialized).
     */
    public function testErrorPropertyDefaultsToNull(): void
    {
        $httpGet = new XoopsHttpGet('https://example.com');

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'error');
        $ref->setAccessible(true);

        $this->assertNull($ref->getValue($httpGet));
    }

    /**
     * The $useCurl property defaults to true before constructor logic.
     *
     * Since curl IS available on this system, the constructor does not
     * change it, so it remains true.
     */
    public function testUseCurlDefaultsToTrue(): void
    {
        $httpGet = new XoopsHttpGet('https://example.com');

        $ref = new ReflectionProperty(XoopsHttpGet::class, 'useCurl');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($httpGet));
    }

    // ---------------------------------------------------------------
    //  Instance type tests
    // ---------------------------------------------------------------

    /**
     * Constructor returns an instance of XoopsHttpGet.
     */
    public function testConstructorReturnsCorrectInstance(): void
    {
        $httpGet = new XoopsHttpGet('https://example.com');
        $this->assertInstanceOf(XoopsHttpGet::class, $httpGet);
    }

    /**
     * TestableXoopsHttpGet is a subclass of XoopsHttpGet.
     */
    public function testTestableSubclassIsInstanceOfXoopsHttpGet(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');
        $this->assertInstanceOf(XoopsHttpGet::class, $httpGet);
    }

    // ---------------------------------------------------------------
    //  Multiple fetch calls
    // ---------------------------------------------------------------

    /**
     * fetch() can be called multiple times on the same instance.
     */
    public function testFetchCanBeCalledMultipleTimes(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');

        $result1 = $httpGet->fetch();
        $result2 = $httpGet->fetch();

        $this->assertSame('test response', $result1);
        $this->assertSame('test response', $result2);
        $this->assertTrue($httpGet->fetchCurlCalled);
    }

    /**
     * Switching useCurl between calls changes the dispatch target.
     */
    public function testFetchSwitchesDispatchWhenUseCurlChanges(): void
    {
        $httpGet = new TestableXoopsHttpGet('https://example.com');

        // First call uses curl (default)
        $httpGet->fetch();
        $this->assertTrue($httpGet->fetchCurlCalled);
        $this->assertFalse($httpGet->fetchFopenCalled);

        // Reset tracking flags
        $httpGet->fetchCurlCalled = false;
        $httpGet->fetchFopenCalled = false;

        // Force useCurl to false
        $ref = new ReflectionProperty(XoopsHttpGet::class, 'useCurl');
        $ref->setAccessible(true);
        $ref->setValue($httpGet, false);

        // Second call uses fopen
        $httpGet->fetch();
        $this->assertFalse($httpGet->fetchCurlCalled);
        $this->assertTrue($httpGet->fetchFopenCalled);
    }
}
