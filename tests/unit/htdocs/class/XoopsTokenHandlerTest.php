<?php
/**
 * Unit tests for XoopsTokenHandler
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package   core
 * @since     2.5.12
 */

declare(strict_types=1);

namespace xoopsclass;

use kernel\KernelTestCase;
use XoopsTokenHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for XoopsTokenHandler class.
 *
 * @category  Test
 * @package   core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
#[CoversClass(XoopsTokenHandler::class)]
class XoopsTokenHandlerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once XOOPS_ROOT_PATH . '/class/XoopsTokenHandler.php';
    }

    /* ========================================================
     * create()
     * ====================================================== */

    #[Test]
    public function createReturnsUrlSafeToken(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(true);
        $db->method('getAffectedRows')->willReturn(0);

        $handler  = new XoopsTokenHandler($db);
        $rawToken = $handler->create(1, 'lostpass', 3600, false);

        $this->assertIsString($rawToken);
        $this->assertNotEmpty($rawToken);
        // 32 random bytes → base64url → 43 chars (no padding)
        $this->assertGreaterThanOrEqual(40, strlen($rawToken));
        // Must be URL-safe: no +, /, or =
        $this->assertDoesNotMatchRegularExpression('/[+\\/=]/', $rawToken);
    }

    #[Test]
    public function createReturnsFalseOnDbFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);

        $handler = new XoopsTokenHandler($db);
        $result  = $handler->create(1, 'lostpass', 3600, false);

        $this->assertFalse($result);
    }

    #[Test]
    public function createRevokesExistingTokensByDefault(): void
    {
        $queries = [];
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$queries) {
            $queries[] = $sql;
            return true;
        });
        $db->method('getAffectedRows')->willReturn(0);

        $handler = new XoopsTokenHandler($db);
        $handler->create(42, 'lostpass');

        // First query should be the UPDATE (revoke), second the INSERT (create)
        $this->assertCount(2, $queries);
        $this->assertStringContainsString('UPDATE', $queries[0]);
        $this->assertStringContainsString('INSERT', $queries[1]);
    }

    #[Test]
    public function createSkipsRevokeWhenFlagIsFalse(): void
    {
        $queries = [];
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$queries) {
            $queries[] = $sql;
            return true;
        });

        $handler = new XoopsTokenHandler($db);
        $handler->create(42, 'lostpass', 3600, false);

        // Only the INSERT query, no revoke UPDATE
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('INSERT', $queries[0]);
    }

    #[Test]
    public function createTokensAreUnique(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(true);

        $handler = new XoopsTokenHandler($db);
        $tokens  = [];
        for ($i = 0; $i < 50; $i++) {
            $tokens[] = $handler->create(1, 'lostpass', 3600, false);
        }
        $this->assertCount(50, array_unique($tokens), 'Tokens should be unique');
    }

    #[Test]
    public function createEnforcesMinimumTtl(): void
    {
        $capturedSql = '';
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });

        $handler = new XoopsTokenHandler($db);
        $handler->create(1, 'test', 10, false); // 10 seconds, below MIN_TTL of 60

        // The expires_at should be at least now + 60, not now + 10
        $now = time();
        $this->assertMatchesRegularExpression('/\d+, \d+, 0\)$/', $capturedSql);
        // Extract the expires_at value from the SQL
        preg_match('/VALUES \(\d+, .+?, .+?, (\d+), (\d+), 0\)/', $capturedSql, $m);
        $issuedAt  = (int)$m[1];
        $expiresAt = (int)$m[2];
        $this->assertGreaterThanOrEqual(60, $expiresAt - $issuedAt);
    }

    #[Test]
    public function createInsertsCorrectScope(): void
    {
        $capturedSql = '';
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });

        $handler = new XoopsTokenHandler($db);
        $handler->create(99, 'activation', 86400, false);

        $this->assertStringContainsString("'activation'", $capturedSql);
        $this->assertStringContainsString('xoops_tokens', $capturedSql);
    }

    /* ========================================================
     * verify() — atomic UPDATE approach
     * ====================================================== */

    #[Test]
    public function verifyReturnsTrueWhenTokenIsValid(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(true);
        $db->method('getAffectedRows')->willReturn(1);

        $handler = new XoopsTokenHandler($db);
        $result  = $handler->verify(1, 'lostpass', 'some-raw-token');

        $this->assertTrue($result);
    }

    #[Test]
    public function verifyReturnsFalseWhenNoRowAffected(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(true);
        $db->method('getAffectedRows')->willReturn(0);

        $handler = new XoopsTokenHandler($db);
        $result  = $handler->verify(1, 'lostpass', 'wrong-token');

        $this->assertFalse($result);
    }

    #[Test]
    public function verifyReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);

        $handler = new XoopsTokenHandler($db);
        $result  = $handler->verify(1, 'lostpass', 'some-token');

        $this->assertFalse($result);
    }

    #[Test]
    public function verifyUsesAtomicUpdate(): void
    {
        $capturedSql = '';
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });
        $db->method('getAffectedRows')->willReturn(1);

        $handler = new XoopsTokenHandler($db);
        $handler->verify(1, 'lostpass', 'test-token');

        // Should be a single UPDATE, not a SELECT
        $this->assertStringContainsString('UPDATE', $capturedSql);
        $this->assertStringNotContainsString('SELECT', $capturedSql);
        $this->assertStringContainsString('used_at', $capturedSql);
        $this->assertStringContainsString('expires_at', $capturedSql);
    }

    #[Test]
    public function verifyHashesTokenWithSha256(): void
    {
        $capturedSql = '';
        $rawToken = 'my-test-token-abc';
        $expectedHash = hash('sha256', $rawToken);

        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });
        $db->method('getAffectedRows')->willReturn(0);

        $handler = new XoopsTokenHandler($db);
        $handler->verify(1, 'lostpass', $rawToken);

        $this->assertStringContainsString($expectedHash, $capturedSql);
    }

    /* ========================================================
     * revokeByScope()
     * ====================================================== */

    #[Test]
    public function revokeByScopeUpdatesUnusedTokens(): void
    {
        $capturedSql = '';
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });

        $handler = new XoopsTokenHandler($db);
        $handler->revokeByScope(42, 'lostpass');

        $this->assertStringContainsString('UPDATE', $capturedSql);
        $this->assertStringContainsString('`used_at` = 0', $capturedSql);
        $this->assertStringContainsString("'lostpass'", $capturedSql);
    }

    /* ========================================================
     * countRecent()
     * ====================================================== */

    #[Test]
    public function countRecentReturnsCountFromDb(): void
    {
        $mockResult = $this->createMock(\mysqli_result::class);
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn($mockResult);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['cnt' => '3']);

        $handler = new XoopsTokenHandler($db);
        $count   = $handler->countRecent(1, 'lostpass', 900);

        $this->assertSame(3, $count);
    }

    #[Test]
    public function countRecentReturnsZeroOnFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = new XoopsTokenHandler($db);
        $count   = $handler->countRecent(1, 'lostpass', 900);

        $this->assertSame(0, $count);
    }

    #[Test]
    public function countRecentQueriesCorrectScope(): void
    {
        $capturedSql = '';
        $mockResult = $this->createMock(\mysqli_result::class);
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql, $mockResult) {
            $capturedSql = $sql;
            return $mockResult;
        });
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['cnt' => '0']);

        $handler = new XoopsTokenHandler($db);
        $handler->countRecent(42, 'activation', 1800);

        $this->assertStringContainsString('COUNT(*)', $capturedSql);
        $this->assertStringContainsString("'activation'", $capturedSql);
    }

    /* ========================================================
     * purgeExpired()
     * ====================================================== */

    #[Test]
    public function purgeExpiredDeletesOldUsedAndExpiredTokens(): void
    {
        $capturedSql = '';
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });

        $handler = new XoopsTokenHandler($db);
        $handler->purgeExpired(604800);

        $this->assertStringContainsString('DELETE FROM', $capturedSql);
        // Must handle both expired and used tokens
        $this->assertStringContainsString('expires_at', $capturedSql);
        $this->assertStringContainsString('used_at', $capturedSql);
        $this->assertStringContainsString('issued_at', $capturedSql);
    }

    #[Test]
    public function purgeExpiredIncludesUsedTokensInDeletion(): void
    {
        $capturedSql = '';
        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(function ($sql) use (&$capturedSql) {
            $capturedSql = $sql;
            return true;
        });

        $handler = new XoopsTokenHandler($db);
        $handler->purgeExpired();

        // The query must include OR `used_at` > 0 to clean up consumed tokens
        $this->assertStringContainsString('`used_at` > 0', $capturedSql);
    }

    /* ========================================================
     * End-to-end: create → verify round-trip
     * ====================================================== */

    #[Test]
    public function endToEndCreateAndVerifyShareSameHash(): void
    {
        $insertedHash = '';
        $verifiedHash = '';
        $queryCount   = 0;

        $db = $this->createMockDatabase();
        $db->method('query')->willReturnCallback(
            function ($sql) use (&$insertedHash, &$verifiedHash, &$queryCount) {
                $queryCount++;
                // Capture hash from INSERT (create, after revoke)
                if (str_contains($sql, 'INSERT')) {
                    preg_match("/hash.*?'([a-f0-9]{64})'/", $sql, $m);
                    if (!empty($m[1])) {
                        $insertedHash = $m[1];
                    }
                }
                // Capture hash from UPDATE (verify)
                if (str_contains($sql, 'UPDATE') && str_contains($sql, 'expires_at')) {
                    preg_match("/'([a-f0-9]{64})'/", $sql, $m);
                    if (!empty($m[1])) {
                        $verifiedHash = $m[1];
                    }
                }
                return true;
            }
        );
        $db->method('getAffectedRows')->willReturn(1);

        $handler  = new XoopsTokenHandler($db);
        $rawToken = $handler->create(1, 'lostpass', 3600, false);
        $this->assertIsString($rawToken);

        $handler->verify(1, 'lostpass', $rawToken);

        $this->assertNotEmpty($insertedHash);
        $this->assertNotEmpty($verifiedHash);
        $this->assertSame($insertedHash, $verifiedHash, 'create() and verify() must use the same hash');
    }
}
