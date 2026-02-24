<?php
declare(strict_types=1);

/**
 * XOOPS password recovery rate limiter
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
 * LostPassSecurity - rate limiting for password recovery (PHP 8.2+)
 *
 * Features:
 * - Rate limiting (IP + identifier) via XoopsCache
 * - Optional Protector integration (logging via API)
 *
 * Token creation and verification are handled by XoopsTokenHandler.
 *
 * @category  Xoops
 * @package   Core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
final class LostPassSecurity
{
    private const CACHE_RL_PREFIX = 'lostpass_rl_';

    private readonly \XoopsMySQLDatabase $db;
    private readonly int $window;
    private readonly int $ipLimit;
    private readonly int $idLimit;

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

    /* ========================================================
     * Rate limiting (fixed window)
     * ====================================================== */

    /**
     * Check if a request should be rate-limited.
     *
     * Uses a fixed-window counter strategy with two buckets:
     * one for IP address (prevents volumetric attacks) and one for
     * identifier (prevents targeted harassment of a single account).
     *
     * @param string $ip         Client IP address
     * @param string $identifier Email or "uid:N" for reset attempts
     *
     * @return bool true if request should be blocked
     */
    public function isRateLimited(string $ip, string $identifier): bool
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

        // Already at or over limit — skip increment and cache write
        if ((int)($state['n'] ?? 0) >= $limit) {
            return true;
        }

        $state['n'] = (int)($state['n'] ?? 0) + 1;
        $ttl = max(1, (int)$state['exp'] - $now);
        $this->cacheWrite($key, $state, $ttl);

        return $state['n'] >= $limit;
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
