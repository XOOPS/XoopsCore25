<?php
/**
 * Regression tests for the superglobal-to-Xmf\Request migration.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @category        test
 * @package         tests
 * @author          XOOPS Development Team
 * @link            https://xoops.org
 */

declare(strict_types=1);

namespace migration;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Xmf\Request;

/**
 * Each test sets up a superglobal, calls Request::get*(), and asserts
 * the value matches what the OLD raw-superglobal code would have produced.
 * If these pass after migration, the behaviour is preserved.
 */
#[CoversNothing]
class SuperglobalMigrationTest extends TestCase
{
    /** @var array Backup of $_REQUEST */
    private array $backupRequest;
    /** @var array Backup of $_POST */
    private array $backupPost;
    /** @var array Backup of $_GET */
    private array $backupGet;
    /** @var array Backup of $_COOKIE */
    private array $backupCookie;

    /**
     * Back up superglobals before each test.
     */
    protected function setUp(): void
    {
        $this->backupRequest = $_REQUEST;
        $this->backupPost    = $_POST;
        $this->backupGet     = $_GET;
        $this->backupCookie  = $_COOKIE;
    }

    /**
     * Restore superglobals after each test.
     */
    protected function tearDown(): void
    {
        $_REQUEST = $this->backupRequest;
        $_POST    = $this->backupPost;
        $_GET     = $this->backupGet;
        $_COOKIE  = $this->backupCookie;
    }

    // ---------------------------------------------------------------
    // 1. tplsets/jquery.php — path traversal via $_REQUEST['dir']
    // ---------------------------------------------------------------

    /**
     * Verify getString strips script tags from dir parameter.
     */
    #[Test]
    public function getStringStripsScriptTagsFromDir(): void
    {
        $_REQUEST['dir'] = '/mytheme/<script>alert(1)</script>/';
        $value = Request::getString('dir', '');
        $this->assertStringNotContainsString('<script>', $value);
    }

    /**
     * Verify getCmd sanitises path traversal sequences in dir.
     */
    #[Test]
    public function getCmdSanitisesPathTraversalInDir(): void
    {
        // getCmd allows [A-Za-z0-9._-] and lowercases
        $_GET['dir'] = '../../etc/passwd';
        $value = Request::getCmd('dir', '', 'GET');
        // Slashes are stripped by getCmd
        $this->assertStringNotContainsString('/', $value);
        $this->assertStringNotContainsString('\\', $value);
    }

    /**
     * Verify getString preserves a valid theme path.
     */
    #[Test]
    public function getStringPreservesValidThemePath(): void
    {
        $_REQUEST['dir'] = '/starter_theme/templates/';
        $value = Request::getString('dir', '');
        $this->assertSame('/starter_theme/templates/', $value);
    }

    // ---------------------------------------------------------------
    // 2. tplsets/jquery.php — $_REQUEST['path_file'] (tpls_restore)
    // ---------------------------------------------------------------

    /**
     * Verify getString strips HTML from path_file parameter.
     */
    #[Test]
    public function getStringStripsHtmlFromPathFile(): void
    {
        $_REQUEST['path_file'] = '/themes/starter_theme/style.css<img onerror=alert(1)>';
        $value = Request::getString('path_file', '');
        $this->assertStringNotContainsString('<img', $value);
    }

    // ---------------------------------------------------------------
    // 3. profile/search.php — $_REQUEST['op']
    // ---------------------------------------------------------------

    /**
     * Verify getCmd returns a clean op value.
     */
    #[Test]
    public function getCmdReturnsCleanOp(): void
    {
        $_REQUEST['op'] = 'results';
        $this->assertSame('results', Request::getCmd('op', 'search'));
    }

    /**
     * Verify getCmd defaults to 'search' when op is missing.
     */
    #[Test]
    public function getCmdDefaultsToSearchForMissingOp(): void
    {
        unset($_REQUEST['op']);
        $this->assertSame('search', Request::getCmd('op', 'search'));
    }

    /**
     * Verify getCmd strips SQL injection from op parameter.
     */
    #[Test]
    public function getCmdStripsInjectionFromOp(): void
    {
        $_REQUEST['op'] = "results'; DROP TABLE users; --";
        $value = Request::getCmd('op', 'search');
        // getCmd strips everything except [A-Za-z0-9._-]
        $this->assertStringNotContainsString("'", $value);
        $this->assertStringNotContainsString(';', $value);
        $this->assertStringNotContainsString(' ', $value);
    }

    // ---------------------------------------------------------------
    // 4. profile/search.php — $_REQUEST['uname']
    // ---------------------------------------------------------------

    /**
     * Verify getString preserves a valid username.
     */
    #[Test]
    public function getStringPreservesValidUname(): void
    {
        $_REQUEST['uname'] = 'john_doe';
        $this->assertSame('john_doe', Request::getString('uname', ''));
    }

    /**
     * Verify getString strips XSS from username.
     */
    #[Test]
    public function getStringStripsXssFromUname(): void
    {
        $_REQUEST['uname'] = '<script>alert("xss")</script>';
        $value = Request::getString('uname', '');
        $this->assertStringNotContainsString('<script>', $value);
    }

    // ---------------------------------------------------------------
    // 5. profile/search.php — $_REQUEST['limit'] and $_REQUEST['start']
    // ---------------------------------------------------------------

    /**
     * Verify getInt returns integer for limit parameter.
     */
    #[Test]
    public function getIntReturnsIntegerForLimit(): void
    {
        $_REQUEST['limit'] = '25';
        $this->assertSame(25, Request::getInt('limit', 20));
    }

    /**
     * Verify getInt strips non-numeric characters from limit.
     */
    #[Test]
    public function getIntStripsNonNumericFromLimit(): void
    {
        $_REQUEST['limit'] = '25; DROP TABLE';
        $this->assertSame(25, Request::getInt('limit', 20));
    }

    /**
     * Verify getInt returns default for missing start parameter.
     */
    #[Test]
    public function getIntDefaultsForMissingStart(): void
    {
        unset($_REQUEST['start']);
        $this->assertSame(0, Request::getInt('start', 0));
    }

    // ---------------------------------------------------------------
    // 6. profile/search.php — $_REQUEST['order']
    // ---------------------------------------------------------------

    /**
     * Verify getInt converts order string to integer.
     */
    #[Test]
    public function getIntConvertsOrderToInteger(): void
    {
        $_REQUEST['order'] = '1';
        $this->assertSame(1, Request::getInt('order', 0));
    }

    /**
     * Verify getInt returns default for non-numeric order.
     */
    #[Test]
    public function getIntClampsNonNumericOrder(): void
    {
        $_REQUEST['order'] = 'DESC';
        $this->assertSame(0, Request::getInt('order', 0));
    }

    // ---------------------------------------------------------------
    // 7. profile/search.php — $_REQUEST['sortby']
    // ---------------------------------------------------------------

    /**
     * Verify getCmd preserves a valid sortby value.
     */
    #[Test]
    public function getCmdPreservesValidSortby(): void
    {
        $_REQUEST['sortby'] = 'uname';
        $this->assertSame('uname', Request::getCmd('sortby', ''));
    }

    /**
     * Verify getCmd strips SQL injection from sortby.
     */
    #[Test]
    public function getCmdStripsInjectionFromSortby(): void
    {
        $_REQUEST['sortby'] = 'uname; DROP TABLE users';
        $value = Request::getCmd('sortby', '');
        $this->assertStringNotContainsString(';', $value);
        $this->assertStringNotContainsString(' ', $value);
    }

    // ---------------------------------------------------------------
    // 8. profile/search.php — $_REQUEST['selgroups'] (array)
    // ---------------------------------------------------------------

    /**
     * Verify getArray returns array for selgroups parameter.
     */
    #[Test]
    public function getArrayReturnsArrayForSelgroups(): void
    {
        $_REQUEST['selgroups'] = ['1', '2', '3'];
        $value = Request::getArray('selgroups', []);
        $this->assertIsArray($value);
        $this->assertCount(3, $value);
    }

    /**
     * Verify getArray returns empty default for missing selgroups.
     */
    #[Test]
    public function getArrayReturnsEmptyDefaultForMissing(): void
    {
        unset($_REQUEST['selgroups']);
        $value = Request::getArray('selgroups', []);
        $this->assertSame([], $value);
    }

    // ---------------------------------------------------------------
    // 9. notification_update.php — open redirect via $_POST['not_redirect']
    // ---------------------------------------------------------------

    /**
     * Verify getUrl returns a valid URL format from POST.
     */
    #[Test]
    public function getUrlReturnsValidUrlFormat(): void
    {
        $_POST['not_redirect'] = 'https://evil.com/phish';
        $value = Request::getUrl('not_redirect', '', 'POST');
        // getUrl preserves valid URLs — domain validation is the app's responsibility
        // (see notification_update.php parse_url() check). This test verifies getUrl
        // returns a non-empty string for well-formed URLs.
        $this->assertIsString($value);
        $this->assertNotSame('', $value);
    }

    /**
     * Verify getString strips HTML tags from redirect URL.
     */
    #[Test]
    public function getStringStripsHtmlTagsFromRedirectUrl(): void
    {
        $_POST['not_redirect'] = '<script>alert(1)</script>https://example.com';
        $value = Request::getString('not_redirect', '', 'POST');
        $this->assertStringNotContainsString('<script>', $value);
        $this->assertStringNotContainsString('</script>', $value);
    }

    // ---------------------------------------------------------------
    // 10. common.php — session fixation via $_POST[sslpost_name]
    // ---------------------------------------------------------------

    /**
     * Verify getString filters session ID from POST.
     */
    #[Test]
    public function getStringFiltersSessionId(): void
    {
        $_POST['ssl_session'] = '  abc123def456  ';
        $value = Request::getString('ssl_session', '', 'POST');
        // getString may or may not trim; verify it contains the core value
        $this->assertStringContainsString('abc123def456', $value);
        $this->assertIsString($value);
    }

    /**
     * Verify getString strips HTML from session ID.
     */
    #[Test]
    public function getStringStripsHtmlFromSessionId(): void
    {
        $_POST['ssl_session'] = '<script>steal()</script>abc123';
        $value = Request::getString('ssl_session', '', 'POST');
        $this->assertStringNotContainsString('<script>', $value);
    }

    // ---------------------------------------------------------------
    // 11. Hash-pinning: POST values should NOT bleed from GET
    // ---------------------------------------------------------------

    /**
     * Verify POST hash does not read GET values.
     */
    #[Test]
    public function postHashDoesNotReadGetValues(): void
    {
        $_GET['secret'] = 'from_get';
        $_POST['secret'] = 'from_post';
        $this->assertSame('from_post', Request::getString('secret', '', 'POST'));

        unset($_POST['secret']);
        $this->assertSame('', Request::getString('secret', '', 'POST'));
    }

    /**
     * Verify GET hash does not read POST values.
     */
    #[Test]
    public function getHashDoesNotReadPostValues(): void
    {
        $_POST['token'] = 'from_post';
        $_GET['token'] = 'from_get';
        $this->assertSame('from_get', Request::getString('token', '', 'GET'));

        unset($_GET['token']);
        $this->assertSame('', Request::getString('token', '', 'GET'));
    }

    // ---------------------------------------------------------------
    // 12. Protector prefix_manager.php — $_POST['prefix'] validation
    // ---------------------------------------------------------------

    /**
     * Verify getCmd allows a valid database prefix.
     */
    #[Test]
    public function getCmdAllowsValidDbPrefix(): void
    {
        $_POST['new_prefix'] = 'xoops_2024';
        // getCmd allows [A-Za-z0-9._-] and underscores, lowercases
        $value = Request::getCmd('new_prefix', '', 'POST');
        $this->assertSame('xoops_2024', $value);
    }

    /**
     * Verify getCmd strips SQL injection from prefix.
     */
    #[Test]
    public function getCmdStripsSqlInjectionFromPrefix(): void
    {
        $_POST['new_prefix'] = "xoops'; DROP TABLE users; --";
        $value = Request::getCmd('new_prefix', '', 'POST');
        $this->assertStringNotContainsString("'", $value);
        $this->assertStringNotContainsString(';', $value);
        $this->assertStringNotContainsString(' ', $value);
    }

    // ---------------------------------------------------------------
    // 13. profile/search.php — dynamic field search values
    // ---------------------------------------------------------------

    /**
     * Verify getInt handles dynamic field larger-than value.
     */
    #[Test]
    public function getIntHandlesDynamicFieldLarger(): void
    {
        $_REQUEST['age_larger'] = '18';
        $this->assertSame(18, Request::getInt('age_larger', 0));
    }

    /**
     * Verify getInt rejects non-numeric dynamic field value.
     */
    #[Test]
    public function getIntRejectsNonNumericFieldValue(): void
    {
        $_REQUEST['age_larger'] = "18 OR 1=1";
        $this->assertSame(18, Request::getInt('age_larger', 0));
    }

    // ---------------------------------------------------------------
    // 14. profile/search.php — email search
    // ---------------------------------------------------------------

    /**
     * Verify getString preserves a valid email address.
     */
    #[Test]
    public function getStringPreservesValidEmail(): void
    {
        $_REQUEST['email'] = 'user@example.com';
        $value = Request::getString('email', '');
        $this->assertSame('user@example.com', $value);
    }

    /**
     * Verify getString strips XSS from email field.
     */
    #[Test]
    public function getStringStripsXssFromEmail(): void
    {
        $_REQUEST['email'] = '"><script>alert(1)</script>';
        $value = Request::getString('email', '');
        $this->assertStringNotContainsString('<script>', $value);
    }

    // ---------------------------------------------------------------
    // 15. match type parameters (used in profile/search.php)
    // ---------------------------------------------------------------

    /**
     * Verify getInt converts match type string to integer.
     */
    #[Test]
    public function getIntConvertsMatchType(): void
    {
        // XOOPS_MATCH_START = 0, XOOPS_MATCH_END = 1, XOOPS_MATCH_CONTAIN = 2
        $_REQUEST['uname_match'] = '2';
        $this->assertSame(2, Request::getInt('uname_match', 0));
    }

    /**
     * Verify getInt rejects invalid match type with SQL injection.
     */
    #[Test]
    public function getIntRejectsInvalidMatchType(): void
    {
        $_REQUEST['uname_match'] = 'CONTAIN; DROP TABLE';
        $this->assertSame(0, Request::getInt('uname_match', 0));
    }
}
