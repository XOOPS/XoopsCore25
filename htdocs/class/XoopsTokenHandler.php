<?php
declare(strict_types=1);

/**
 * XOOPS generic scoped token handler
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

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Generic scoped token handler for XOOPS.
 *
 * Provides create/verify/revoke/purge for any token-based flow:
 * password reset, account activation, email verification, etc.
 *
 * Recommended scopes: 'lostpass', 'activation', 'emailchange'.
 *
 * Requires PHP 8.1+ (readonly properties, union return types).
 *
 * @category  Xoops
 * @package   Core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
final class XoopsTokenHandler
{
    /** @var int Minimum TTL floor in seconds */
    private const MIN_TTL = 60;

    private readonly \XoopsMySQLDatabase $db;

    /**
     * @param \XoopsMySQLDatabase $db Database connection
     */
    public function __construct(\XoopsMySQLDatabase $db)
    {
        $this->db = $db;
    }

    /**
     * Create a token for a user+scope. Returns the raw token (for email).
     *
     * By default, revokes any previous unused tokens for the same user+scope
     * before inserting, so only the latest link is valid (OWASP recommendation).
     *
     * @param int    $uid            User ID
     * @param string $scope          Token scope (e.g. 'lostpass', 'activation')
     * @param int    $ttl            Time-to-live in seconds (minimum 60)
     * @param bool   $revokePrevious Revoke unused tokens for same scope first
     *
     * @return string|false Raw token string, or false on DB failure
     */
    public function create(
        int    $uid,
        string $scope,
        int    $ttl = 3600,
        bool   $revokePrevious = true
    ): string|false {
        if ($uid <= 0 || trim($scope) === '') {
            trigger_error(
                basename(__FILE__) . ': create() requires uid > 0 and non-empty scope',
                E_USER_WARNING
            );
            return false;
        }

        if ($revokePrevious) {
            $this->revokeByScope($uid, $scope);
        }

        try {
            $bytes = random_bytes(32);
        } catch (\Throwable $e) {
            trigger_error(
                sprintf('%s::create() failed to generate secure random token: %s', __CLASS__, $e->getMessage()),
                E_USER_WARNING
            );
            return false;
        }
        $rawToken  = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
        $hash      = hash('sha256', $rawToken);
        $now       = time();
        $expiresAt = $now + max(self::MIN_TTL, $ttl);

        $table = $this->db->prefix('tokens');
        $sql   = sprintf(
            "INSERT INTO `%s` (`uid`, `scope`, `hash`, `issued_at`, `expires_at`, `used_at`)"
            . " VALUES (%d, %s, %s, %d, %d, 0)",
            $table,
            $uid,
            $this->db->quote($scope),
            $this->db->quote($hash),
            $now,
            $expiresAt
        );

        return $this->db->query($sql) ? $rawToken : false;
    }

    /**
     * Atomically verify and consume a token. Single DB round-trip, no race condition.
     *
     * Uses UPDATE with WHERE conditions instead of SELECT-then-UPDATE to prevent
     * TOCTOU (time-of-check-to-time-of-use) double-consumption under concurrency.
     *
     * @param int    $uid      User ID
     * @param string $scope    Token scope
     * @param string $rawToken Raw token from the URL/form
     *
     * @return bool true if the token was valid and has now been consumed
     */
    public function verify(int $uid, string $scope, string $rawToken): bool
    {
        $hash  = hash('sha256', $rawToken);
        $table = $this->db->prefix('tokens');
        $now   = time();

        $result = $this->db->query(sprintf(
            "UPDATE `%s` SET `used_at` = %d"
            . " WHERE `uid` = %d AND `scope` = %s AND `hash` = %s"
            . " AND `used_at` = 0 AND `expires_at` > %d",
            $table,
            $now,
            $uid,
            $this->db->quote($scope),
            $this->db->quote($hash),
            $now
        ));

        return $result && $this->db->getAffectedRows() === 1;
    }

    /**
     * Revoke all unused tokens for a user+scope.
     *
     * @param int    $uid   User ID
     * @param string $scope Token scope
     *
     * @return void
     */
    public function revokeByScope(int $uid, string $scope): void
    {
        $table = $this->db->prefix('tokens');
        $now   = time();

        $this->db->query(sprintf(
            "UPDATE `%s` SET `used_at` = %d"
            . " WHERE `uid` = %d AND `scope` = %s AND `used_at` = 0",
            $table,
            $now,
            $uid,
            $this->db->quote($scope)
        ));
    }

    /**
     * Count tokens issued for a user+scope within a recent time window.
     *
     * Use for cooldown checks: "already requested in the last N minutes".
     *
     * @param int    $uid    User ID
     * @param string $scope  Token scope
     * @param int    $window Lookback window in seconds
     *
     * @return int Number of tokens issued in the window, or 0 on failure
     */
    public function countRecent(int $uid, string $scope, int $window): int
    {
        $table = $this->db->prefix('tokens');
        $since = time() - max(0, $window);

        $result = $this->db->query(sprintf(
            "SELECT COUNT(*) AS `cnt` FROM `%s`"
            . " WHERE `uid` = %d AND `scope` = %s AND `issued_at` > %d",
            $table,
            $uid,
            $this->db->quote($scope),
            $since
        ));

        if (!$this->db->isResultSet($result) || !$result instanceof \mysqli_result) {
            return 0;
        }

        $row = $this->db->fetchArray($result);

        return is_array($row) && isset($row['cnt']) ? (int)$row['cnt'] : 0;
    }

    /**
     * Delete tokens old enough to be past the retention window,
     * where they are either expired or already consumed.
     *
     * @param int $maxAge Retention window in seconds (default 7 days)
     *
     * @return void
     */
    public function purgeExpired(int $maxAge = 604800): void
    {
        $table  = $this->db->prefix('tokens');
        $now    = time();
        $cutoff = $now - max(0, $maxAge);

        $this->db->query(sprintf(
            "DELETE FROM `%s`"
            . " WHERE `issued_at` < %d"
            . " AND (`expires_at` < %d OR `used_at` > 0)",
            $table,
            $cutoff,
            $now
        ));
    }
}
