<?php
declare(strict_types=1);

/**
 * LostpassSecurity - XOOPS password recovery security helper (PHP 8.2+)
 *
 * Features:
 * - Strong reset tokens (hash stored, raw token emailed)
 * - Token stored in users.actkey if column is large enough; otherwise XoopsCache
 * - Rate limiting (IP + identifier) via XoopsCache
 * - Optional Protector integration (logging via API, correct trust path)
 * - No enumeration leaks
 *
 * Run upgrade_lostpass.php to expand actkey column for direct DB storage.
 * Without migration, tokens are stored in cache (works but less durable).
 *
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package   core
 */

final class LostpassSecurity
{
    public const TOKEN_TTL = 3600; // 1 hour

    private const ACTKEY_PREFIX      = 'lp|';
    private const CACHE_TOKEN_PREFIX = 'lostpass_tok_';
    private const CACHE_RL_PREFIX    = 'lostpass_rl_';

    private readonly \XoopsDatabase $db;
    private readonly int $window;
    private readonly int $ipLimit;
    private readonly int $idLimit;

    /** @var int 0=not checked, -1=unknown/error, >0=actual max length */
    private int $actkeyMaxLen = 0;

    public function __construct(
        \XoopsDatabase $db,
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
     * Identifier is typically email or "uid:N" for reset attempts.
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

        $state['n'] = (int)($state['n'] ?? 0) + 1;
        $ttl = max(1, (int)$state['exp'] - $now);
        $this->cacheWrite($key, $state, $ttl);

        return $state['n'] > $limit;
    }

    /* ========================================================
     * Token helpers
     * ====================================================== */

    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function isLostpassActkey(string $actkey): bool
    {
        return $actkey !== '' && str_starts_with($actkey, self::ACTKEY_PREFIX);
    }

    public function isExpired(int $issuedAt): bool
    {
        return (time() - $issuedAt) > self::TOKEN_TTL;
    }

    public function packActkey(int $issuedAt, string $hash): string
    {
        return self::ACTKEY_PREFIX . $issuedAt . '|' . $hash;
    }

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
     * Returns array with 'issuedAt', 'hash', 'source' or null.
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
     * For actkey source: sets actkey='' on the user object in memory only.
     *   Caller MUST call insertUser() to persist.
     * For cache source: deletes the cache key immediately.
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
        $res = $this->db->queryF(
            "SHOW COLUMNS FROM `{$table}` LIKE " . $this->db->quoteString('actkey')
        );

        if (!$res) {
            $this->actkeyMaxLen = -1;
            return null;
        }

        $row = $this->db->fetchArray($res);
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
