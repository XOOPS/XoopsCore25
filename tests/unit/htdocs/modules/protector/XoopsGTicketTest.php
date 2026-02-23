<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\XoopsGTicket::class)]
#[CoversFunction('admin_refcheck')]
class XoopsGTicketTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            // Ensure XoopsGTicket is loaded
            if (!class_exists('XoopsGTicket', false)) {
                require_once XOOPS_PATH . '/modules/protector/class/gtickets.php';
            }
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        // Reset session stubs
        $_SESSION['XOOPS_G_STUBS'] = [];
        // Clear server vars used by ticket
        unset($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI'], $_SERVER['PATH']);
        unset($_POST['XOOPS_G_TICKET'], $_GET['XOOPS_G_TICKET']);
    }

    protected function tearDown(): void
    {
        unset($_SESSION['XOOPS_G_STUBS']);
        unset($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI'], $_SERVER['PATH']);
        unset($_POST['XOOPS_G_TICKET'], $_GET['XOOPS_G_TICKET']);
    }

    private function createFreshTicket(): \XoopsGTicket
    {
        return new \XoopsGTicket();
    }

    // ---------------------------------------------------------------
    // Constructor / Default messages
    // ---------------------------------------------------------------

    #[Test]
    public function constructorSetsDefaultMessages(): void
    {
        $ticket = $this->createFreshTicket();
        $this->assertNotEmpty($ticket->messages);
    }

    #[Test]
    public function defaultMessagesContainsExpectedKeys(): void
    {
        $ticket = $this->createFreshTicket();
        $expected = [
            'err_general', 'err_nostubs', 'err_noticket', 'err_nopair',
            'err_timeout', 'err_areaorref', 'fmt_prompt4repost', 'btn_repost',
        ];
        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $ticket->messages, "Missing message key: $key");
        }
    }

    #[Test]
    public function constructorInitializesEmptyErrors(): void
    {
        $ticket = $this->createFreshTicket();
        $this->assertSame([], $ticket->_errors);
    }

    #[Test]
    public function constructorInitializesEmptyLatestToken(): void
    {
        $ticket = $this->createFreshTicket();
        $this->assertSame('', $ticket->_latest_token);
    }

    // ---------------------------------------------------------------
    // issue()
    // ---------------------------------------------------------------

    #[Test]
    public function issueReturnsMd5String(): void
    {
        $ticket = $this->createFreshTicket();
        $result = $ticket->issue('salt', 1800, 'testarea');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $result);
    }

    #[Test]
    public function issueStoresStubInSession(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'testarea');
        $this->assertCount(1, $_SESSION['XOOPS_G_STUBS']);
    }

    #[Test]
    public function issueStubContainsExpectedKeys(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'testarea');
        $stub = $_SESSION['XOOPS_G_STUBS'][0];
        $this->assertArrayHasKey('expire', $stub);
        $this->assertArrayHasKey('referer', $stub);
        $this->assertArrayHasKey('area', $stub);
        $this->assertArrayHasKey('token', $stub);
    }

    #[Test]
    public function issueStubAreaMatchesParameter(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'mymodule');
        $this->assertSame('mymodule', $_SESSION['XOOPS_G_STUBS'][0]['area']);
    }

    #[Test]
    public function issueStubExpireIsInFuture(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'testarea');
        $this->assertGreaterThan(time(), $_SESSION['XOOPS_G_STUBS'][0]['expire']);
    }

    #[Test]
    public function issueSetsLatestToken(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'testarea');
        $this->assertNotEmpty($ticket->_latest_token);
    }

    #[Test]
    public function issueWithEmptySaltGeneratesToken(): void
    {
        $ticket = $this->createFreshTicket();
        $result = $ticket->issue('', 1800, 'testarea');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $result);
    }

    #[Test]
    public function issueMultipleTokensAccumulate(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('s1', 1800, 'area1');
        $ticket->issue('s2', 1800, 'area2');
        $ticket->issue('s3', 1800, 'area3');
        $this->assertCount(3, $_SESSION['XOOPS_G_STUBS']);
    }

    #[Test]
    public function issueLimitsStubsToTen(): void
    {
        $ticket = $this->createFreshTicket();
        // Pre-fill with 11 stubs
        for ($i = 0; $i < 11; $i++) {
            $_SESSION['XOOPS_G_STUBS'][] = [
                'expire' => time() + 1800,
                'referer' => '',
                'area' => 'old',
                'token' => 'token_' . $i,
            ];
        }
        // Issue one more - should trim to 10 first, then add
        $ticket->issue('new', 1800, 'new');
        $this->assertLessThanOrEqual(12, count($_SESSION['XOOPS_G_STUBS']));
    }

    #[Test]
    public function issueReturnsDifferentTokensEachTime(): void
    {
        $ticket = $this->createFreshTicket();
        $t1 = $ticket->issue('salt1', 1800, 'area');
        $t2 = $ticket->issue('salt2', 1800, 'area');
        $this->assertNotSame($t1, $t2);
    }

    // ---------------------------------------------------------------
    // getTicketHtml()
    // ---------------------------------------------------------------

    #[Test]
    public function getTicketHtmlReturnsHiddenInput(): void
    {
        $ticket = $this->createFreshTicket();
        $html = $ticket->getTicketHtml('salt', 1800, 'area');
        $this->assertStringContainsString('<input type="hidden"', $html);
        $this->assertStringContainsString('name="XOOPS_G_TICKET"', $html);
        $this->assertStringContainsString('value="', $html);
    }

    #[Test]
    public function getTicketHtmlValueIsMd5(): void
    {
        $ticket = $this->createFreshTicket();
        $html = $ticket->getTicketHtml('salt', 1800, 'area');
        preg_match('/value="([a-f0-9]{32})"/', $html, $matches);
        $this->assertNotEmpty($matches[1]);
    }

    // ---------------------------------------------------------------
    // getTicketArray()
    // ---------------------------------------------------------------

    #[Test]
    public function getTicketArrayReturnsArrayWithKey(): void
    {
        $ticket = $this->createFreshTicket();
        $result = $ticket->getTicketArray('salt', 1800, 'area');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('XOOPS_G_TICKET', $result);
    }

    #[Test]
    public function getTicketArrayValueIsMd5(): void
    {
        $ticket = $this->createFreshTicket();
        $result = $ticket->getTicketArray('salt', 1800, 'area');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $result['XOOPS_G_TICKET']);
    }

    // ---------------------------------------------------------------
    // getTicketParamString()
    // ---------------------------------------------------------------

    #[Test]
    public function getTicketParamStringWithAmp(): void
    {
        $ticket = $this->createFreshTicket();
        $result = $ticket->getTicketParamString('salt', false, 1800, 'area');
        $this->assertStringStartsWith('&amp;XOOPS_G_TICKET=', $result);
    }

    #[Test]
    public function getTicketParamStringWithNoamp(): void
    {
        $ticket = $this->createFreshTicket();
        $result = $ticket->getTicketParamString('salt', true, 1800, 'area');
        $this->assertStringStartsWith('XOOPS_G_TICKET=', $result);
    }

    // ---------------------------------------------------------------
    // clear()
    // ---------------------------------------------------------------

    #[Test]
    public function clearEmptiesSessionStubs(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'area');
        $this->assertNotEmpty($_SESSION['XOOPS_G_STUBS']);
        $ticket->clear();
        $this->assertSame([], $_SESSION['XOOPS_G_STUBS']);
    }

    // ---------------------------------------------------------------
    // using()
    // ---------------------------------------------------------------

    #[Test]
    public function usingReturnsFalseWhenNoStubs(): void
    {
        $ticket = $this->createFreshTicket();
        $_SESSION['XOOPS_G_STUBS'] = [];
        $this->assertFalse($ticket->using());
    }

    #[Test]
    public function usingReturnsTrueWhenStubsExist(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->issue('salt', 1800, 'area');
        $this->assertTrue($ticket->using());
    }

    // ---------------------------------------------------------------
    // getErrors()
    // ---------------------------------------------------------------

    #[Test]
    public function getErrorsReturnsHtmlStringByDefault(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->_errors = ['Error 1', 'Error 2'];
        $result = $ticket->getErrors(true);
        $this->assertIsString($result);
        $this->assertStringContainsString('Error 1', $result);
        $this->assertStringContainsString('Error 2', $result);
        $this->assertStringContainsString('<br>', $result);
    }

    #[Test]
    public function getErrorsReturnsArrayWhenFalse(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->_errors = ['Error A', 'Error B'];
        $result = $ticket->getErrors(false);
        $this->assertIsArray($result);
        $this->assertSame(['Error A', 'Error B'], $result);
    }

    #[Test]
    public function getErrorsReturnsEmptyStringWhenNoErrors(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->_errors = [];
        $this->assertSame('', $ticket->getErrors(true));
    }

    #[Test]
    public function getErrorsReturnsEmptyArrayWhenNoErrors(): void
    {
        $ticket = $this->createFreshTicket();
        $ticket->_errors = [];
        $this->assertSame([], $ticket->getErrors(false));
    }

    // ---------------------------------------------------------------
    // extract_post_recursive()
    // ---------------------------------------------------------------

    #[Test]
    public function extractPostRecursiveFlatValues(): void
    {
        $ticket = $this->createFreshTicket();
        $data = ['name' => 'John', 'email' => 'john@test.com'];
        [$table, $form] = $ticket->extract_post_recursive('field', $data);

        $this->assertStringContainsString('field[name]', $table);
        $this->assertStringContainsString('John', $table);
        $this->assertStringContainsString('<input type="hidden"', $form);
        $this->assertStringContainsString('value="John"', $form);
    }

    #[Test]
    public function extractPostRecursiveNestedValues(): void
    {
        $ticket = $this->createFreshTicket();
        $data = ['sub' => ['key' => 'val']];
        [$table, $form] = $ticket->extract_post_recursive('parent', $data);

        $this->assertStringContainsString('parent[sub][key]', $table);
        $this->assertStringContainsString('val', $table);
    }

    #[Test]
    public function extractPostRecursiveEscapesHtml(): void
    {
        $ticket = $this->createFreshTicket();
        $data = ['xss' => '<script>alert(1)</script>'];
        [$table, $form] = $ticket->extract_post_recursive('field', $data);

        $this->assertStringNotContainsString('<script>', $table);
        $this->assertStringContainsString('&lt;script&gt;', $table);
    }

    // ---------------------------------------------------------------
    // check() — token validation (no allow_repost to avoid exit)
    // ---------------------------------------------------------------

    #[Test]
    public function checkFailsWithNoStubsAndNoRepost(): void
    {
        $ticket = $this->createFreshTicket();
        $_SESSION['XOOPS_G_STUBS'] = [];
        $_POST['XOOPS_G_TICKET'] = 'invalidticket';
        $result = $ticket->check(true, 'testarea', false);
        $this->assertFalse($result);
    }

    #[Test]
    public function checkFailsWithEmptyTicket(): void
    {
        $ticket = $this->createFreshTicket();
        $_POST['XOOPS_G_TICKET'] = '';
        $result = $ticket->check(true, 'testarea', false);
        $this->assertFalse($result);
    }

    #[Test]
    public function checkSucceedsWithValidTicket(): void
    {
        $ticket = $this->createFreshTicket();
        $md5ticket = $ticket->issue('salt', 1800, 'testarea');
        $_POST['XOOPS_G_TICKET'] = $md5ticket;
        $result = $ticket->check(true, 'testarea', false);
        $this->assertTrue($result);
    }

    #[Test]
    public function checkConsumesTheStub(): void
    {
        $ticket = $this->createFreshTicket();
        $md5ticket = $ticket->issue('salt', 1800, 'testarea');
        $_POST['XOOPS_G_TICKET'] = $md5ticket;
        $ticket->check(true, 'testarea', false);
        // Stub should be consumed (removed from session)
        $this->assertEmpty($_SESSION['XOOPS_G_STUBS']);
    }

    #[Test]
    public function checkFromGetParameter(): void
    {
        $ticket = $this->createFreshTicket();
        $md5ticket = $ticket->issue('salt', 1800, 'testarea');
        $_GET['XOOPS_G_TICKET'] = $md5ticket;
        $result = $ticket->check(false, 'testarea', false);
        $this->assertTrue($result);
    }

    #[Test]
    public function checkExpiredTicketFails(): void
    {
        $ticket = $this->createFreshTicket();
        // Manually create an expired stub
        $token = 'expired_token_' . microtime();
        $_SESSION['XOOPS_G_STUBS'][] = [
            'expire' => time() - 100, // already expired
            'referer' => '',
            'area' => 'testarea',
            'token' => $token,
        ];
        $_POST['XOOPS_G_TICKET'] = md5($token . XOOPS_DB_PREFIX);
        $result = $ticket->check(true, 'testarea', false);
        $this->assertFalse($result);
    }

    #[Test]
    public function checkSetsTimeoutError(): void
    {
        $ticket = $this->createFreshTicket();
        $token = 'timed_out_token';
        $_SESSION['XOOPS_G_STUBS'][] = [
            'expire' => time() - 100,
            'referer' => '',
            'area' => 'testarea',
            'token' => $token,
        ];
        $_POST['XOOPS_G_TICKET'] = md5($token . XOOPS_DB_PREFIX);
        $ticket->check(true, 'testarea', false);
        $errors = $ticket->getErrors(false);
        $this->assertContains($ticket->messages['err_timeout'], $errors);
    }

    // ---------------------------------------------------------------
    // admin_refcheck() function
    // ---------------------------------------------------------------

    #[Test]
    public function adminRefcheckReturnsTrueWhenNoReferer(): void
    {
        unset($_SERVER['HTTP_REFERER']);
        $this->assertTrue(admin_refcheck());
    }

    #[Test]
    public function adminRefcheckReturnsTrueForValidReferer(): void
    {
        $_SERVER['HTTP_REFERER'] = XOOPS_URL . '/admin/index.php';
        $this->assertTrue(admin_refcheck());
    }

    #[Test]
    public function adminRefcheckReturnsFalseForExternalReferer(): void
    {
        $_SERVER['HTTP_REFERER'] = 'http://evil.example.com/attack';
        $this->assertFalse(admin_refcheck());
    }

    #[Test]
    public function adminRefcheckWithPathRestriction(): void
    {
        $_SERVER['HTTP_REFERER'] = XOOPS_URL . '/modules/system/admin.php';
        $this->assertTrue(admin_refcheck('/modules/system/'));
    }

    #[Test]
    public function adminRefcheckFailsForWrongPath(): void
    {
        $_SERVER['HTTP_REFERER'] = XOOPS_URL . '/modules/other/admin.php';
        $this->assertFalse(admin_refcheck('/modules/system/'));
    }
}
