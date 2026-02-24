<?php
/**
 * Unit tests for LostpassSecurity
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
use LostpassSecurity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for LostpassSecurity class.
 *
 * @category  Test
 * @package   core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
#[CoversClass(LostpassSecurity::class)]
class LostpassSecurityTest extends KernelTestCase
{
    private LostpassSecurity $security;

    protected function setUp(): void
    {
        parent::setUp();
        require_once XOOPS_ROOT_PATH . '/kernel/user.php';
        require_once XOOPS_ROOT_PATH . '/kernel/member.php';
        require_once XOOPS_ROOT_PATH . '/class/LostpassSecurity.php';
        $db = $this->createMockDatabase();
        $this->security = new LostpassSecurity($db);
    }

    /* ========================================================
     * Token generation
     * ====================================================== */

    #[Test]
    public function generateTokenReturnsBase64UrlSafeString(): void
    {
        $token = $this->security->generateToken();
        $this->assertNotEmpty($token);
        // 32 random bytes → base64url → 43 chars (no padding)
        $this->assertGreaterThanOrEqual(40, strlen($token));
        // Must be URL-safe: no +, /, or =
        $this->assertDoesNotMatchRegularExpression('/[+\/=]/', $token);
    }

    #[Test]
    public function generateTokenIsUnique(): void
    {
        $tokens = [];
        for ($i = 0; $i < 50; $i++) {
            $tokens[] = $this->security->generateToken();
        }
        $this->assertCount(50, array_unique($tokens), 'Tokens should be unique');
    }

    /* ========================================================
     * Token hashing
     * ====================================================== */

    #[Test]
    public function hashTokenReturnsSha256Hex(): void
    {
        $token = $this->security->generateToken();
        $hash = $this->security->hashToken($token);
        $this->assertSame(64, strlen($hash), 'SHA-256 hex digest should be 64 chars');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }

    #[Test]
    public function hashTokenIsDeterministic(): void
    {
        $token = 'test-token-abc123';
        $hash1 = $this->security->hashToken($token);
        $hash2 = $this->security->hashToken($token);
        $this->assertSame($hash1, $hash2);
    }

    #[Test]
    public function hashTokenDiffersForDifferentTokens(): void
    {
        $hash1 = $this->security->hashToken('token-aaa');
        $hash2 = $this->security->hashToken('token-bbb');
        $this->assertNotSame($hash1, $hash2);
    }

    /* ========================================================
     * isLostpassActkey
     * ====================================================== */

    #[Test]
    public function isLostpassActkeyRecognizesOwnPrefix(): void
    {
        $hash = $this->security->hashToken('test');
        $packed = $this->security->packActkey(time(), $hash);
        $this->assertTrue($this->security->isLostpassActkey($packed));
    }

    #[Test]
    public function isLostpassActkeyRejectsRegularActkey(): void
    {
        $this->assertFalse($this->security->isLostpassActkey('abc12345'));
        $this->assertFalse($this->security->isLostpassActkey(''));
        $this->assertFalse($this->security->isLostpassActkey('random_activation_key'));
    }

    /* ========================================================
     * isExpired
     * ====================================================== */

    #[Test]
    public function isExpiredReturnsFalseForRecentTimestamp(): void
    {
        $this->assertFalse($this->security->isExpired(time()));
        $this->assertFalse($this->security->isExpired(time() - 100));
    }

    #[Test]
    public function isExpiredReturnsTrueAfterTtl(): void
    {
        $expired = time() - LostpassSecurity::TOKEN_TTL - 1;
        $this->assertTrue($this->security->isExpired($expired));
    }

    #[Test]
    public function isExpiredBoundaryExactlyAtTtl(): void
    {
        // At exactly TOKEN_TTL seconds, (time() - issuedAt) == TOKEN_TTL,
        // which is NOT > TOKEN_TTL, so should NOT be expired
        $atBoundary = time() - LostpassSecurity::TOKEN_TTL;
        $this->assertFalse($this->security->isExpired($atBoundary));
    }

    /* ========================================================
     * packActkey / unpackActkey round-trip
     * ====================================================== */

    #[Test]
    public function packUnpackRoundTrip(): void
    {
        $issuedAt = time();
        $hash = $this->security->hashToken('my-token');
        $packed = $this->security->packActkey($issuedAt, $hash);

        $unpacked = $this->security->unpackActkey($packed);
        $this->assertNotNull($unpacked);
        $this->assertSame($issuedAt, $unpacked['issuedAt']);
        $this->assertSame($hash, $unpacked['hash']);
        $this->assertSame('actkey', $unpacked['source']);
    }

    #[Test]
    public function packActkeyFormat(): void
    {
        $issuedAt = 1700000000;
        $hash = str_repeat('a', 64);
        $packed = $this->security->packActkey($issuedAt, $hash);

        $this->assertStringStartsWith('lp|', $packed);
        $this->assertStringContainsString('1700000000', $packed);
        $this->assertStringContainsString($hash, $packed);
    }

    #[Test]
    public function unpackActkeyRejectsGarbage(): void
    {
        $this->assertNull($this->security->unpackActkey(''));
        $this->assertNull($this->security->unpackActkey('random'));
        $this->assertNull($this->security->unpackActkey('lp|'));
        $this->assertNull($this->security->unpackActkey('lp|notanumber|hash'));
        $this->assertNull($this->security->unpackActkey('lp|0|hash'));
        $this->assertNull($this->security->unpackActkey('lp|12345|'));
    }

    #[Test]
    public function unpackActkeyRejectsNonLostpassPrefix(): void
    {
        $this->assertNull($this->security->unpackActkey('xx|12345|somehash'));
    }

    /* ========================================================
     * Token TTL constant
     * ====================================================== */

    #[Test]
    public function tokenTtlIsOneHour(): void
    {
        $this->assertSame(3600, LostpassSecurity::TOKEN_TTL);
    }

    /* ========================================================
     * Packed token length (regression: must fit in VARCHAR(100))
     * ====================================================== */

    #[Test]
    public function packedTokenFitsInVarchar100(): void
    {
        $issuedAt = time();
        $hash = $this->security->hashToken($this->security->generateToken());
        $packed = $this->security->packActkey($issuedAt, $hash);

        // 'lp|' (3) + timestamp (~10) + '|' (1) + sha256 hex (64) = ~78 chars
        $this->assertLessThanOrEqual(100, strlen($packed),
            'Packed token must fit in VARCHAR(100) after migration');
    }

    /* ========================================================
     * Rate limiting (isAbusing)
     * ====================================================== */

    #[Test]
    public function isAbusingReturnsFalseWhenCacheUnavailable(): void
    {
        // With no XoopsCache available, rate limiting should fail-open
        $this->assertFalse($this->security->isAbusing('127.0.0.1', 'test@example.com'));
    }

    /* ========================================================
     * readPayload with actkey source
     * ====================================================== */

    #[Test]
    public function readPayloadFromActkey(): void
    {
        $issuedAt = time();
        $hash = $this->security->hashToken('test-token');
        $packed = $this->security->packActkey($issuedAt, $hash);

        $user = $this->createMockUser($packed, 1);

        $payload = $this->security->readPayload($user);
        $this->assertNotNull($payload);
        $this->assertSame($issuedAt, $payload['issuedAt']);
        $this->assertSame($hash, $payload['hash']);
        $this->assertSame('actkey', $payload['source']);
    }

    #[Test]
    public function readPayloadReturnsNullForEmptyActkey(): void
    {
        $user = $this->createMockUser('', 1);
        // No cache available either, so should return null
        $payload = $this->security->readPayload($user);
        $this->assertNull($payload);
    }

    #[Test]
    public function readPayloadReturnsNullForRegularActkey(): void
    {
        // A regular activation key (not lostpass) should not be parsed
        $user = $this->createMockUser('abc12345', 1);
        $payload = $this->security->readPayload($user);
        $this->assertNull($payload);
    }

    /* ========================================================
     * clearPayloadInMemory for actkey source
     * ====================================================== */

    #[Test]
    public function clearPayloadInMemoryClearsActkey(): void
    {
        $issuedAt = time();
        $hash = $this->security->hashToken('test-token');
        $packed = $this->security->packActkey($issuedAt, $hash);

        // Create a real-ish user mock that tracks setVar calls
        $user = $this->createMock(\XoopsUser::class);
        $user->method('getVar')->willReturnMap([
            ['actkey', $packed],
            ['uid', 1],
        ]);
        $user->expects($this->once())
            ->method('setVar')
            ->with('actkey', '');

        $this->security->clearPayloadInMemory($user, 'actkey');
    }

    #[Test]
    public function clearPayloadInMemoryDoesNotClearForeignActkey(): void
    {
        // If actkey is not a lostpass key, don't touch it
        $user = $this->createMock(\XoopsUser::class);
        $user->method('getVar')->willReturnMap([
            ['actkey', 'regular_activation_key'],
            ['uid', 1],
        ]);
        $user->expects($this->never())
            ->method('setVar');

        $this->security->clearPayloadInMemory($user, 'actkey');
    }

    /* ========================================================
     * End-to-end: generate → hash → pack → unpack → verify
     * ====================================================== */

    #[Test]
    public function endToEndTokenLifecycle(): void
    {
        // 1. Generate token
        $rawToken = $this->security->generateToken();
        $this->assertNotEmpty($rawToken);

        // 2. Hash it
        $hash = $this->security->hashToken($rawToken);

        // 3. Pack into actkey
        $issuedAt = time();
        $packed = $this->security->packActkey($issuedAt, $hash);
        $this->assertTrue($this->security->isLostpassActkey($packed));

        // 4. Unpack
        $unpacked = $this->security->unpackActkey($packed);
        $this->assertNotNull($unpacked);

        // 5. Verify the token hash matches
        $this->assertTrue(
            hash_equals($unpacked['hash'], $this->security->hashToken($rawToken))
        );

        // 6. Token should not be expired
        $this->assertFalse($this->security->isExpired($unpacked['issuedAt']));
    }

    #[Test]
    public function endToEndTokenRejectionWithWrongToken(): void
    {
        $rawToken = $this->security->generateToken();
        $hash = $this->security->hashToken($rawToken);
        $packed = $this->security->packActkey(time(), $hash);

        $unpacked = $this->security->unpackActkey($packed);
        $this->assertNotNull($unpacked);

        // A different token should NOT match
        $wrongToken = $this->security->generateToken();
        $this->assertFalse(
            hash_equals($unpacked['hash'], $this->security->hashToken($wrongToken))
        );
    }

    /* ========================================================
     * Constructor parameter enforcement
     * ====================================================== */

    #[Test]
    public function constructorEnforcesMinimumWindow(): void
    {
        $db = $this->createMockDatabase();
        $sec = new LostpassSecurity($db, window: 10); // below 60 minimum
        // We can't directly inspect private readonly, but we can verify
        // the object was created without errors
        $this->assertInstanceOf(LostpassSecurity::class, $sec);
    }

    #[Test]
    public function constructorEnforcesMinimumLimits(): void
    {
        $db = $this->createMockDatabase();
        $sec = new LostpassSecurity($db, ipLimit: 0, idLimit: 0);
        $this->assertInstanceOf(LostpassSecurity::class, $sec);
    }

    /* ========================================================
     * storePayload (actkey path — column fits)
     * ====================================================== */

    #[Test]
    public function storePayloadUsesActkeyWhenColumnFits(): void
    {
        // Mock DB to report actkey column as VARCHAR(100)
        $db = $this->createMockDatabase();
        $mockResult = $this->createMock(\mysqli_result::class);
        $db->method('query')->willReturn($mockResult);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['Type' => 'varchar(100)']);

        $sec = new LostpassSecurity($db);

        $issuedAt = time();
        $hash = $sec->hashToken('tok');

        // User with empty actkey → can overwrite
        $user = $this->createMock(\XoopsUser::class);
        $user->method('getVar')->willReturnMap([
            ['actkey', ''],
            ['uid', 42],
        ]);
        $user->expects($this->once())
            ->method('setVar')
            ->with('actkey', $this->stringStartsWith('lp|'));

        // Mock member handler
        $handler = $this->createMock(\XoopsMemberHandler::class);
        $handler->expects($this->once())
            ->method('insertUser')
            ->with($user, true)
            ->willReturn(true);

        $result = $sec->storePayload($user, $handler, $issuedAt, $hash);
        $this->assertTrue($result);
    }

    #[Test]
    public function storePayloadDoesNotClobberActivationKey(): void
    {
        // Mock DB to report actkey column as VARCHAR(100)
        $db = $this->createMockDatabase();
        $mockResult = $this->createMock(\mysqli_result::class);
        $db->method('query')->willReturn($mockResult);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['Type' => 'varchar(100)']);

        $sec = new LostpassSecurity($db);

        // User with a regular activation key → should NOT overwrite
        $user = $this->createMock(\XoopsUser::class);
        $user->method('getVar')->willReturnMap([
            ['actkey', 'abc12345'],
            ['uid', 42],
        ]);
        // setVar should NOT be called with actkey
        $user->expects($this->never())->method('setVar');

        // Non-lostpass actkey prevents overwrite; cacheWrite fails without XoopsCache in tests
        $handler = $this->createMock(\XoopsMemberHandler::class);
        $handler->expects($this->never())->method('insertUser');

        $result = $sec->storePayload($user, $handler, time(), $sec->hashToken('tok'));
        $this->assertFalse($result);
    }

    /* ========================================================
     * Helper to create mock XoopsUser
     * ====================================================== */

    private function createMockUser(string $actkey, int $uid): \XoopsUser
    {
        $user = $this->createMock(\XoopsUser::class);
        $user->method('getVar')->willReturnMap([
            ['actkey', $actkey],
            ['uid', $uid],
        ]);
        return $user;
    }
}
