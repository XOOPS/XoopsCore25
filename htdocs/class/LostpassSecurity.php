<?php
declare(strict_types=1);

/**
 * XOOPS password recovery security helper
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
 * LostpassSecurity - secure token-based password recovery (PHP 8.2+)
 *
 * Features:
 * - Strong reset tokens (hash stored, raw token emailed)
 * - Token stored in users.actkey if column is large enough; otherwise XoopsCache
 * - Rate limiting (IP + identifier) via XoopsCache
 * - Optional Protector integration (logging via API, correct trust path)
 * - No enumeration leaks
 *
 * The upgrade script (upd_2.5.11-to-2.5.12) expands the actkey column for
 * direct DB storage. Without migration, tokens fall back to cache.
 *
 * @category  Xoops
 * @package   Core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
final class LostpassSecurity
{
    /** @var int Token time-to-live in seconds (1 hour) */
    public const TOKEN_TTL = 3600;

    private const ACTKEY_PREFIX      = 'lp|';
    private const CACHE_TOKEN_PREFIX = 'lostpass_tok_';
    private const CACHE_RL_PREFIX    = 'lostpass_rl_';

    private readonly \XoopsMySQLDatabase $db;
    private readonly int $window;
    private readonly int $ipLimit;
    private readonly int $idLimit;

    /** @var int 0=not checked, -1=unknown/error, >0=actual max length */
    private int $actkeyMaxLen = 0;

    /**
     * @param \XoopsMySQLDatabase $db      Database connection
     * @param int                 $window  Rate-limit window in seconds (min 60)
     * @param int                 $ipLimit Max requests per IP per window
     * @param int                 $idLimit Max requests per identifier per window
     */
    public function __construct(
        \XoopsMySQLDatabase $db,
        int $window  = 900,  // 15 min
        int $ipLimit = 20,   // per IP per window
        int $idLimit = 3     // per identifier per window
    ) {
        $this->db      = $db;
        $this->window  = max(60, $window);
        $this->ipLimit = max(1, $ipLimit);
        $this->idLimit = max(1, $idLimit);
    }

    /* ========================================================
     * Cache helpers
     * ====================================================== */

    private function cacheReady(): bool
    {
        if (!class_exists('XoopsCache', false)) {
            if (!defined('XOOPS_ROOT_PATH')) {
                return false;
            }
            $file = XOOPS_ROOT_PATH . '/class/cache/xoopscache.php';
            if (is_file($file)) {
                require_once $file;
            }
        }
        return class_exists('XoopsCache', false);
    }

    private function cacheRead(string $key): mixed
    {
        if (!$this->cacheReady()) {
            return false;
        }
        return \XoopsCache::read($key);
    }

    private function cacheWrite(string $key, mixed $value, int $ttl): bool
    {
        if (!$this->cacheReady()) {
            return false;
        }
        return (bool)\XoopsCache::write($key, $value, max(1, $ttl));
    }

    private function cacheDelete(string $key): void
    {
        if ($this->cacheReady()) {
            \XoopsCache::delete($key);
        }
    }

    /* ========================================================
     * Rate limiting (fixed window)
     * ====================================================== */

    /**
     * Check if request should be blocked (IP + identifier bucket).
     *
     * @param string $ip         Client IP address
     * @param string $identifier Email or "uid:N" for reset attempts
     *
     * @return bool true if request should be blocked
     */
    public function isAbusing(string $ip, string $identifier): bool
    {
        $ipHash = hash('sha256', $ip);

        if ($this->rateHit('ip_' . $ipHash, $this->ipLimit)) {
            $this->protectorLog('lostpass_ip_limit');
            return true;
        }

        $idNorm = strtolower(trim($identifier));
        if ($idNorm !== '') {
            $idHash = hash('sha256', $idNorm);
            if ($this->rateHit('id_' . $idHash, $this->idLimit)) {
                $this->protectorLog('lostpass_id_limit');
                return true;
            }
        }

        return false;
    }

    private function rateHit(string $suffix, int $limit): bool
    {
        if (!$this->cacheReady()) {
            return false; // fail-open if cache unavailable
        }

        $key = self::CACHE_RL_PREFIX . $suffix;
        $now = time();

        $state = $this->cacheRead($key);

        if (!is_array($state) || (($state['exp'] ?? 0) <= $now)) {
            $state = ['n' => 0, 'exp' => $now + $this->window];
        }

        // Already over limit — skip increment and cache write
        if ((int)($state['n'] ?? 0) > $limit) {
            return true;
        }

        $state['n'] = (int)($state['n'] ?? 0) + 1;
        $ttl = max(1, (int)$state['exp'] - $now);
        $this->cacheWrite($key, $state, $ttl);

        return $state['n'] > $limit;
    }

    /* ========================================================
     * Token helpers
     * ====================================================== */

    /**
     * Generate a cryptographically secure URL-safe token.
     *
     * @return string Base64url-encoded random token (43 chars)
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * Hash a raw token for storage comparison.
     *
     * @param string $token Raw token string
     *
     * @return string SHA-256 hex digest (64 chars)
     */
    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Check whether an actkey value is a lostpass token (vs activation key).
     *
     * @param string $actkey Value from users.actkey column
     *
     * @return bool
     */
    public function isLostpassActkey(string $actkey): bool
    {
        return $actkey !== '' && str_starts_with($actkey, self::ACTKEY_PREFIX);
    }

    /**
     * Check whether a token has expired based on its issue timestamp.
     *
     * @param int $issuedAt Unix timestamp when the token was issued
     *
     * @return bool true if expired
     */
    public function isExpired(int $issuedAt): bool
    {
        return (time() - $issuedAt) > self::TOKEN_TTL;
    }

    /**
     * Pack issuedAt timestamp and hash into an actkey string.
     *
     * @param int    $issuedAt Unix timestamp
     * @param string $hash     SHA-256 hex digest of the raw token
     *
     * @return string Packed actkey value (format: "lp|{timestamp}|{hash}")
     */
    public function packActkey(int $issuedAt, string $hash): string
    {
        return self::ACTKEY_PREFIX . $issuedAt . '|' . $hash;
    }

    /**
     * Unpack an actkey string into its components.
     *
     * @param string $actkey Packed actkey value
     *
     * @return array{issuedAt: int, hash: string, source: string}|null
     */
    public function unpackActkey(string $actkey): ?array
    {
        if (!$this->isLostpassActkey($actkey)) {
            return null;
        }
        $parts = explode('|', $actkey, 3);
        if (count($parts) !== 3) {
            return null;
        }
        $issuedAt = (int)$parts[1];
        $hash     = (string)$parts[2];
        if ($issuedAt <= 0 || $hash === '') {
            return null;
        }
        return [
            'issuedAt' => $issuedAt,
            'hash'     => $hash,
            'source'   => 'actkey',
        ];
    }

    /* ========================================================
     * Payload storage (actkey or cache fallback)
     * ====================================================== */

    /**
     * Read token payload from actkey column or cache.
     *
     * @param \XoopsUser $user User object to read payload from
     *
     * @return array{issuedAt: int, hash: string, source: string}|null
     */
    public function readPayload(\XoopsUser $user): ?array
    {
        // Try actkey first
        $actkey = (string)$user->getVar('actkey');
        $payload = $this->unpackActkey($actkey);
        if ($payload !== null) {
            return $payload;
        }

        // Try cache fallback
        $uid    = (int)$user->getVar('uid');
        $cached = $this->cacheRead(self::CACHE_TOKEN_PREFIX . $uid);
        if (!is_array($cached) || !isset($cached['issuedAt'], $cached['hash'])) {
            return null;
        }
        $issuedAt = (int)$cached['issuedAt'];
        $hash     = (string)$cached['hash'];
        if ($issuedAt <= 0 || $hash === '') {
            return null;
        }
        return [
            'issuedAt' => $issuedAt,
            'hash'     => $hash,
            'source'   => 'cache',
        ];
    }

    /**
     * Store token payload in actkey (if safe + fits) or cache.
     *
     * @param \XoopsUser          $user     User object
     * @param \XoopsMemberHandler $handler  Member handler for DB persistence
     * @param int                 $issuedAt Unix timestamp
     * @param string              $hash     SHA-256 hex digest
     *
     * @return bool true on success
     */
    public function storePayload(\XoopsUser $user, \XoopsMemberHandler $handler, int $issuedAt, string $hash): bool
    {
        $packed  = $this->packActkey($issuedAt, $hash);
        $current = (string)$user->getVar('actkey');

        // Write to actkey only if empty or already ours (never clobber activation keys)
        // AND the column can fit the packed value (avoid silent truncation)
        $canOverwrite = ($current === '' || $this->isLostpassActkey($current));
        $canFit       = $this->canFitActkey($packed);

        if ($canOverwrite && $canFit) {
            $user->setVar('actkey', $packed);
            return (bool)$handler->insertUser($user, true);
        }

        // Fallback to cache
        return $this->cacheWrite(
            self::CACHE_TOKEN_PREFIX . (int)$user->getVar('uid'),
            ['issuedAt' => $issuedAt, 'hash' => $hash],
            self::TOKEN_TTL
        );
    }

    /**
     * Prepare payload for clearing.
     *
     * For actkey source: sets actkey='' on the user object in memory only.
     * Caller MUST call insertUser() to persist.
     * For cache source: deletes the cache key immediately.
     *
     * @param \XoopsUser $user   User object
     * @param string     $source Storage source ('actkey' or 'cache')
     *
     * @return void
     */
    public function clearPayloadInMemory(\XoopsUser $user, string $source): void
    {
        if ($source === 'actkey') {
            if ($this->isLostpassActkey((string)$user->getVar('actkey'))) {
                $user->setVar('actkey', '');
            }
            return;
        }

        // Cache source
        $this->cacheDelete(self::CACHE_TOKEN_PREFIX . (int)$user->getVar('uid'));
    }

    /* ========================================================
     * actkey column length detection
     * ====================================================== */

    private function canFitActkey(string $value): bool
    {
        $max = $this->getActkeyMaxLen();
        if ($max === null) {
            return true; // unknown = optimistic
        }
        return strlen($value) <= $max;
    }

    private function getActkeyMaxLen(): ?int
    {
        if ($this->actkeyMaxLen > 0) {
            return $this->actkeyMaxLen;
        }
        if ($this->actkeyMaxLen === -1) {
            return null; // already checked, unknown
        }

        $table = $this->db->prefix('users');
        $result = $this->db->query(
            "SHOW COLUMNS FROM `{$table}` LIKE " . $this->db->quote('actkey')
        );

        if (!$this->db->isResultSet($result) || !$result instanceof \mysqli_result) {
            $this->actkeyMaxLen = -1;
            return null;
        }

        $row = $this->db->fetchArray($result);
        if ($row && preg_match('/\((\d+)\)/', (string)($row['Type'] ?? ''), $m)) {
            $this->actkeyMaxLen = (int)$m[1];
            return $this->actkeyMaxLen;
        }

        $this->actkeyMaxLen = -1;
        return null;
    }

    /* ========================================================
     * Protector integration (optional)
     * ====================================================== */

    private function getProtector(): ?object
    {
        // Protector is usually already loaded by precheck during XOOPS boot
        if (!class_exists('Protector', false)) {
            if (defined('XOOPS_TRUST_PATH')) {
                $path = XOOPS_TRUST_PATH . '/modules/protector/class/protector.php';
                if (is_file($path)) {
                    require_once $path;
                }
            }
        }

        if (!class_exists('Protector', false)) {
            return null;
        }

        try {
            $p = \Protector::getInstance();
            return is_object($p) ? $p : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function protectorLog(string $type): void
    {
        $p = $this->getProtector();
        if ($p === null || !method_exists($p, 'output_log')) {
            return;
        }

        try {
            // output_log($type, $uid=0, $unique_check=false, $level=1)
            $p->output_log($type, 0, true, 1);
        } catch (\Throwable) {
            // Protector issues must not break recovery flow
        }
    }
}
