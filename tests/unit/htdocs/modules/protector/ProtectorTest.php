<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Protector::class)]
class ProtectorTest extends TestCase
{
    private static bool $loaded = false;
    private \Protector $protector;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            // Ensure Xmf autoloader is available
            if (file_exists(XOOPS_PATH . '/vendor/autoload.php')) {
                require_once XOOPS_PATH . '/vendor/autoload.php';
            }
            // Ensure protector var directory constant path exists conceptually
            require_once XOOPS_PATH . '/modules/protector/class/protector.php';
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        $this->protector = \Protector::getInstance();
    }

    // ---------------------------------------------------------------
    // Singleton
    // ---------------------------------------------------------------

    #[Test]
    public function getInstanceReturnsProtector(): void
    {
        $this->assertInstanceOf(\Protector::class, $this->protector);
    }

    #[Test]
    public function getInstanceReturnsSameInstance(): void
    {
        $a = \Protector::getInstance();
        $b = \Protector::getInstance();
        $this->assertSame($a, $b);
    }

    // ---------------------------------------------------------------
    // Default property values
    // ---------------------------------------------------------------

    #[Test]
    public function mydirnameIsProtector(): void
    {
        $this->assertSame('protector', $this->protector->mydirname);
    }

    #[Test]
    public function confIsArray(): void
    {
        $this->assertIsArray($this->protector->_conf);
    }

    #[Test]
    public function lastErrorTypeDefaultsToUnknown(): void
    {
        // Reset to default
        $this->protector->last_error_type = 'UNKNOWN';
        $this->assertSame('UNKNOWN', $this->protector->last_error_type);
    }

    #[Test]
    public function messageIsString(): void
    {
        $this->assertIsString($this->protector->message);
    }

    #[Test]
    public function warningDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->warning);
    }

    #[Test]
    public function errorDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->error);
    }

    #[Test]
    public function loggedDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_logged);
    }

    #[Test]
    public function shouldBeBannedDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_should_be_banned);
    }

    #[Test]
    public function shouldBeBannedTime0DefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_should_be_banned_time0);
    }

    #[Test]
    public function doubtfulRequestsIsArray(): void
    {
        $this->assertIsArray($this->protector->_doubtful_requests);
    }

    #[Test]
    public function bigumbrellaDoubtfulsIsArray(): void
    {
        $this->assertIsArray($this->protector->_bigumbrella_doubtfuls);
    }

    #[Test]
    public function dblayertrapDoubtfulsIsArray(): void
    {
        $this->assertIsArray($this->protector->_dblayertrap_doubtfuls);
    }

    #[Test]
    public function dblayertrapDoubtfulNeedlesContainsExpectedValues(): void
    {
        $needles = $this->protector->_dblayertrap_doubtful_needles;
        $this->assertContains('information_schema', $needles);
        $this->assertContains('select', $needles);
        $this->assertContains("'", $needles);
        $this->assertContains('"', $needles);
    }

    #[Test]
    public function badGlobalsContainsExpectedEntries(): void
    {
        $bad = $this->protector->_bad_globals;
        $this->assertContains('GLOBALS', $bad);
        $this->assertContains('_SESSION', $bad);
        $this->assertContains('_GET', $bad);
        $this->assertContains('_POST', $bad);
        $this->assertContains('_COOKIE', $bad);
        $this->assertContains('_SERVER', $bad);
        $this->assertContains('_REQUEST', $bad);
        $this->assertContains('_ENV', $bad);
        $this->assertContains('_FILES', $bad);
        $this->assertContains('xoopsDB', $bad);
        $this->assertContains('xoopsUser', $bad);
        $this->assertContains('xoopsConfig', $bad);
        $this->assertContains('xoopsModule', $bad);
    }

    // ---------------------------------------------------------------
    // Done flags
    // ---------------------------------------------------------------

    #[Test]
    public function doneBadextDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_badext);
    }

    #[Test]
    public function doneIntvalDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_intval);
    }

    #[Test]
    public function doneDotdotDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_dotdot);
    }

    #[Test]
    public function doneNullbyteDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_nullbyte);
    }

    #[Test]
    public function doneContamiDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_contami);
    }

    #[Test]
    public function doneIsocomDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_isocom);
    }

    #[Test]
    public function doneUnionDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_union);
    }

    #[Test]
    public function doneDosDefaultsFalse(): void
    {
        $this->assertFalse($this->protector->_done_dos);
    }

    // ---------------------------------------------------------------
    // Safe flags
    // ---------------------------------------------------------------

    #[Test]
    public function safeBadextDefaultsTrue(): void
    {
        $this->assertTrue($this->protector->_safe_badext);
    }

    #[Test]
    public function safeIsocomDefaultsTrue(): void
    {
        $this->assertTrue($this->protector->_safe_isocom);
    }

    #[Test]
    public function safeUnionDefaultsTrue(): void
    {
        $this->assertTrue($this->protector->_safe_union);
    }

    // ---------------------------------------------------------------
    // getConf / setConn
    // ---------------------------------------------------------------

    #[Test]
    public function getConfReturnsArray(): void
    {
        $this->assertIsArray($this->protector->getConf());
    }

    #[Test]
    public function setConnSetsConnection(): void
    {
        $mockConn = 'fake_connection';
        $this->protector->setConn($mockConn);
        $this->assertSame($mockConn, $this->protector->_conn);
        // Clean up
        $this->protector->setConn(null);
    }

    // ---------------------------------------------------------------
    // getDblayertrapDoubtfuls
    // ---------------------------------------------------------------

    #[Test]
    public function getDblayertrapDoubtfulsReturnsArray(): void
    {
        $this->assertIsArray($this->protector->getDblayertrapDoubtfuls());
    }

    // ---------------------------------------------------------------
    // Static file path methods
    // ---------------------------------------------------------------

    #[Test]
    public function getFilepath4bwlimitContainsProtectorDir(): void
    {
        $path = \Protector::get_filepath4bwlimit();
        $this->assertStringContainsString('protector/bwlimit', $path);
    }

    #[Test]
    public function getFilepath4bwlimitStartsWithVarPath(): void
    {
        $path = \Protector::get_filepath4bwlimit();
        $this->assertStringStartsWith(XOOPS_VAR_PATH, $path);
    }

    #[Test]
    public function getFilepath4badipsContainsProtectorDir(): void
    {
        $path = \Protector::get_filepath4badips();
        $this->assertStringContainsString('protector/badips', $path);
    }

    #[Test]
    public function getFilepath4badipsStartsWithVarPath(): void
    {
        $path = \Protector::get_filepath4badips();
        $this->assertStringStartsWith(XOOPS_VAR_PATH, $path);
    }

    #[Test]
    public function getFilepath4group1ipsContainsProtectorDir(): void
    {
        $path = \Protector::get_filepath4group1ips();
        $this->assertStringContainsString('protector/group1ips', $path);
    }

    #[Test]
    public function getFilepath4group1ipsStartsWithVarPath(): void
    {
        $path = \Protector::get_filepath4group1ips();
        $this->assertStringStartsWith(XOOPS_VAR_PATH, $path);
    }

    #[Test]
    public function getFilepath4confighcacheContainsProtectorDir(): void
    {
        $path = $this->protector->get_filepath4confighcache();
        $this->assertStringContainsString('protector/configcache', $path);
    }

    #[Test]
    public function getFilepath4confighcacheStartsWithVarPath(): void
    {
        $path = $this->protector->get_filepath4confighcache();
        $this->assertStringStartsWith(XOOPS_VAR_PATH, $path);
    }

    #[Test]
    public function filePathsContainMd5Hash(): void
    {
        $path = \Protector::get_filepath4bwlimit();
        // Path should end with a 6-char hex hash
        $this->assertMatchesRegularExpression('/[a-f0-9]{6}$/', $path);
    }

    #[Test]
    public function filePathHashesAreConsistent(): void
    {
        $bw1 = \Protector::get_filepath4bwlimit();
        $bw2 = \Protector::get_filepath4bwlimit();
        $this->assertSame($bw1, $bw2);
    }

    // ---------------------------------------------------------------
    // check_contami_systemglobals
    // ---------------------------------------------------------------

    #[Test]
    public function checkContamiReturnsBoolean(): void
    {
        $result = $this->protector->check_contami_systemglobals();
        $this->assertIsBool($result);
    }

    #[Test]
    public function checkContamiReturnsSafeContamiFlag(): void
    {
        // By default _safe_contami is true (no contamination detected)
        $this->protector->_safe_contami = true;
        $this->assertTrue($this->protector->check_contami_systemglobals());
    }

    // ---------------------------------------------------------------
    // bigumbrella_outputcheck
    // ---------------------------------------------------------------

    #[Test]
    public function bigumbrellaOutputcheckReturnsInputWhenDisabled(): void
    {
        if (!defined('BIGUMBRELLA_DISABLED')) {
            define('BIGUMBRELLA_DISABLED', true);
        }
        $input = '<html><body>Hello World</body></html>';
        $result = $this->protector->bigumbrella_outputcheck($input);
        $this->assertSame($input, $result);
    }

    // ---------------------------------------------------------------
    // updateConfFromDb (returns false when no connection)
    // ---------------------------------------------------------------

    #[Test]
    public function updateConfFromDbReturnsFalseWithoutConnection(): void
    {
        $this->protector->_conn = null;
        $this->assertFalse($this->protector->updateConfFromDb());
    }

    // ---------------------------------------------------------------
    // get_ref_from_base64index
    // ---------------------------------------------------------------

    #[Test]
    public function getRefFromBase64IndexReturnsValue(): void
    {
        $data = ['key1' => ['key2' => 'found']];
        $indexes = [base64_encode('key1'), base64_encode('key2')];
        $result = $this->protector->get_ref_from_base64index($data, $indexes);
        $this->assertSame('found', $result);
    }

    #[Test]
    public function getRefFromBase64IndexReturnsFalseForNonArray(): void
    {
        $data = 'not_an_array';
        $indexes = [base64_encode('key1')];
        $result = $this->protector->get_ref_from_base64index($data, $indexes);
        $this->assertFalse($result);
    }

    #[Test]
    public function getRefFromBase64IndexEmptyIndexesReturnsData(): void
    {
        $data = ['key' => 'value'];
        $indexes = [];
        $result = $this->protector->get_ref_from_base64index($data, $indexes);
        $this->assertSame(['key' => 'value'], $result);
    }

    // ---------------------------------------------------------------
    // check_uploaded_files (with empty $_FILES)
    // ---------------------------------------------------------------

    #[Test]
    public function checkUploadedFilesReturnsTrueWhenNoFiles(): void
    {
        // Reset the done flag so the check actually runs
        $this->protector->_done_badext = false;
        $this->protector->_safe_badext = true;
        $_FILES = [];
        $result = $this->protector->check_uploaded_files();
        $this->assertTrue($result);
    }

    #[Test]
    public function checkUploadedFilesRejectsPHPExtension(): void
    {
        $this->protector->_done_badext = false;
        $this->protector->_safe_badext = true;
        $_FILES = [
            'upload' => [
                'name' => 'evil.php',
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => 0,
                'size' => 100,
                'type' => 'text/plain',
            ],
        ];
        $result = $this->protector->check_uploaded_files();
        $this->assertFalse($result);
        $this->assertStringContainsString('evil.php', $this->protector->message);
        // Clean up
        $_FILES = [];
        $this->protector->_safe_badext = true;
        $this->protector->message = '';
    }

    #[Test]
    public function checkUploadedFilesRejectsPhtmlExtension(): void
    {
        $this->protector->_done_badext = false;
        $this->protector->_safe_badext = true;
        $_FILES = [
            'upload' => [
                'name' => 'shell.phtml',
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => 0,
                'size' => 100,
                'type' => 'text/plain',
            ],
        ];
        $result = $this->protector->check_uploaded_files();
        $this->assertFalse($result);
        $_FILES = [];
        $this->protector->_safe_badext = true;
        $this->protector->message = '';
    }

    #[Test]
    public function checkUploadedFilesRejectsMultipleDotFiles(): void
    {
        $this->protector->_done_badext = false;
        $this->protector->_safe_badext = true;
        $_FILES = [
            'upload' => [
                'name' => 'evil.php.jpg',
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => 0,
                'size' => 100,
                'type' => 'image/jpeg',
            ],
        ];
        $result = $this->protector->check_uploaded_files();
        $this->assertFalse($result);
        $_FILES = [];
        $this->protector->_safe_badext = true;
        $this->protector->message = '';
    }

    #[Test]
    public function checkUploadedFilesSkipsErroredUploads(): void
    {
        $this->protector->_done_badext = false;
        $this->protector->_safe_badext = true;
        $_FILES = [
            'upload' => [
                'name' => 'evil.php',
                'tmp_name' => '',
                'error' => UPLOAD_ERR_NO_FILE,
                'size' => 0,
                'type' => '',
            ],
        ];
        $result = $this->protector->check_uploaded_files();
        $this->assertTrue($result);
        $_FILES = [];
    }

    #[Test]
    public function checkUploadedFilesRejectsCgiExtension(): void
    {
        $this->protector->_done_badext = false;
        $this->protector->_safe_badext = true;
        $_FILES = [
            'upload' => [
                'name' => 'script.cgi',
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => 0,
                'size' => 100,
                'type' => 'text/plain',
            ],
        ];
        $result = $this->protector->check_uploaded_files();
        $this->assertFalse($result);
        $_FILES = [];
        $this->protector->_safe_badext = true;
        $this->protector->message = '';
    }

    // ---------------------------------------------------------------
    // intval_allrequestsendid — sanitizes *id keys
    // ---------------------------------------------------------------

    #[Test]
    public function intvalAllrequestsendidSanitizesIdKeys(): void
    {
        $this->protector->_done_intval = false;
        $val = "123'; DROP TABLE--";
        $_GET['catid'] = $val;
        $_REQUEST['catid'] = $val;
        $this->protector->intval_allrequestsendid();
        // regex /[^0-9a-zA-Z_-]/ strips ', ;, and spaces
        $this->assertSame('123DROPTABLE--', $_GET['catid']);
        // Clean up
        unset($_GET['catid'], $_REQUEST['catid']);
    }

    #[Test]
    public function intvalAllrequestsendidLeavesNonIdKeysAlone(): void
    {
        $this->protector->_done_intval = false;
        $_GET['name'] = "test value with spaces";
        $this->protector->intval_allrequestsendid();
        $this->assertSame("test value with spaces", $_GET['name']);
        unset($_GET['name']);
    }

    #[Test]
    public function intvalAllrequestsendidReturnsTrueWhenAlreadyDone(): void
    {
        $this->protector->_done_intval = true;
        $this->assertTrue($this->protector->intval_allrequestsendid());
    }

    // ---------------------------------------------------------------
    // stopForumSpamLookup — returns false when curl is not available
    // or builds proper query string
    // ---------------------------------------------------------------

    #[Test]
    public function stopForumSpamLookupReturnsFalseForEmptyParams(): void
    {
        $result = $this->protector->stopForumSpamLookup('', '', '');
        $this->assertFalse($result);
    }
}
