<?php
/**
 * XOOPS session handler
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * XOOPS session handler (PHP 8.0+ with full types and lazy timestamp updates)
 * @package             kernel
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              XOOPS Development Team
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 */
class XoopsSessionHandler implements
    \SessionHandlerInterface,
    \SessionUpdateTimestampHandlerInterface
{
    /** @var XoopsDatabase */
    public $db;

    /** @var int */
    public $securityLevel = 3;

    protected array $bitMasks = [
        2 => ['v4' => 16, 'v6' => 64],
        3 => ['v4' => 24, 'v6' => 56],
        4 => ['v4' => 32, 'v6' => 128],
    ];

    public bool $enableRegenerateId = true;

    public function __construct(XoopsDatabase $db)
    {
        global $xoopsConfig;
        $this->db = $db;

        $lifetime = ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
            ? $xoopsConfig['session_expire'] * 60
            : ini_get('session.cookie_lifetime');

        $secure = (XOOPS_PROT === 'https://');

        $host = parse_url(XOOPS_URL, PHP_URL_HOST);
        if (!is_string($host)) {
            $host = '';
        }
        $cookieDomain = XOOPS_COOKIE_DOMAIN;
        if (class_exists('\Xoops\RegDom\RegisteredDomain')) {
            if (!\Xoops\RegDom\RegisteredDomain::domainMatches($host, $cookieDomain)) {
                $cookieDomain = '';
            }
        }

        $options = [
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => $cookieDomain,
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        session_set_cookie_params($options);
    }

    // --- SessionHandlerInterface (typed) ---

    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        $this->gc_force();
        return true;
    }

    public function read(string $sessionId): string|false
    {
        $ip = \Xmf\IPAddress::fromRequest();
        $sql = sprintf(
            'SELECT sess_data, sess_ip FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quote($sessionId)
        );

        $result = $this->db->queryF($sql);
        if (!$this->db->isResultSet($result)) {
            return false; // storage failure
        }

        $row = $this->db->fetchRow($result);
        if ($row === false) {
            return ''; // not found → empty string
        }

        [$sess_data, $sess_ip] = $row;

        if ($this->securityLevel > 1) {
            if (false === $ip->sameSubnet(
                    $sess_ip,
                    $this->bitMasks[$this->securityLevel]['v4'],
                    $this->bitMasks[$this->securityLevel]['v6']
                )) {
                return ''; // IP mismatch → treat as no data
            }
        }

        return $sess_data;
    }

    public function write(string $sessionId, string $data): bool
    {
        $remoteAddress = \Xmf\IPAddress::fromRequest()->asReadable();
        $sid = $this->db->quote($sessionId);
        $now = time();

        $sql = sprintf(
            'INSERT INTO %s (sess_id, sess_updated, sess_ip, sess_data)
             VALUES (%s, %u, %s, %s)
             ON DUPLICATE KEY UPDATE
             sess_updated = %u, sess_data = %s',
            $this->db->prefix('session'),
            $sid,
            $now,
            $this->db->quote($remoteAddress),
            $this->db->quote($data),
            $now,
            $this->db->quote($data)
        );

        $ok = $this->db->exec($sql);
        // update_cookie() only affects PHP versions with PHP_VERSION_ID < 70300 (see session74.php); on PHP 8+ it is effectively a no-op
        $this->update_cookie();
        return (bool)$ok;
    }

    public function destroy(string $sessionId): bool
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quote($sessionId)
        );
        return (bool)$this->db->exec($sql);
    }

    public function gc(int $max_lifetime): int|false
    {
        if ($max_lifetime <= 0) {
            return 0;
        }
        $mintime = time() - $max_lifetime;
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_updated < %u',
            $this->db->prefix('session'),
            $mintime
        );
        if ($this->db->exec($sql)) {
            return (int)$this->db->getAffectedRows();
        }
        return false;
    }

    // --- SessionUpdateTimestampHandlerInterface (8.0+) ---

    public function validateId(string $id): bool
    {
        $sql = sprintf(
            'SELECT 1 FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quote($id)
        );
        $res = $this->db->queryF($sql, 1, 0);
        return $this->db->isResultSet($res) && $this->db->fetchRow($res) !== false;
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        $sql = sprintf(
            'UPDATE %s SET sess_updated = %u WHERE sess_id = %s',
            $this->db->prefix('session'),
            time(),
            $this->db->quote($id)
        );
        return (bool)$this->db->exec($sql);
    }

    // --- Helpers (same behavior as your current code) ---

    public function gc_force(): void
    {
        /*
         * Probabilistic garbage collection:
         * - We run GC with approximately 10% probability on each call.
         * - \random_int() is used instead of mt_rand() for modern, secure randomness.
         *   The range [1, 100] and threshold < 11 are chosen to preserve the
         *   original ~10% behavior from the mt_rand()-based implementation.
         * - Any failure of \random_int() is ignored so that session handling
         *   is not disrupted if a random source is temporarily unavailable.
         */
        try {
            if (\random_int(1, 100) < 11) {
                $expire = (int) @ini_get('session.gc_maxlifetime');
                if ($expire <= 0) {
                    $expire = 900;
                }
                $this->gc($expire);
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function regenerate_id(bool $delete_old_session = false): bool
    {
        $success = $this->enableRegenerateId
            ? session_regenerate_id($delete_old_session)
            : true;

        if ($success) {
            $this->update_cookie();
        }
        return $success;
    }

    public function update_cookie($sess_id = null, $expire = null): void
    {
        // no-op for 8.0+; retained for parity with 7.4 implementation
    }
}
