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
 * XOOPS session handler (PHP 7.4 compatible)
 * @package             kernel
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              XOOPS Development Team
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 */


class XoopsSessionHandler implements \SessionHandlerInterface
{
    /** @var XoopsDatabase */
    public $db;

    /** @var int */
    public $securityLevel = 3;

    protected $bitMasks = [
        2 => ['v4' => 16, 'v6' => 64],
        3 => ['v4' => 24, 'v6' => 56],
        4 => ['v4' => 32, 'v6' => 128],
    ];

    /** @var bool */
    public $enableRegenerateId = true;

    public function __construct(XoopsDatabase $db)
    {
        global $xoopsConfig;
        $this->db = $db;

        $lifetime = ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
            ? $xoopsConfig['session_expire'] * 60
            : ini_get('session.cookie_lifetime');

        $secure = (XOOPS_PROT === 'https://');

        // Domain validation from your current code
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

        if (PHP_VERSION_ID >= 70300) {
            $options = [
                'lifetime' => $lifetime,
                'path'     => '/',
                'domain'   => $cookieDomain,
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ];
            session_set_cookie_params($options);
        } else {
            session_set_cookie_params($lifetime, '/', $cookieDomain, $secure, true);
        }
    }

    // --- SessionHandlerInterface (untyped for 7.4) ---

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        $this->gc_force();
        return true;
    }

    public function read($sessionId)
    {
        $ip = \Xmf\IPAddress::fromRequest();
        $sql = sprintf(
            'SELECT sess_data, sess_ip FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quote($sessionId)
        );
        $result = $this->db->queryF($sql);
        if (!$this->db->isResultSet($result)) {
            return false; // failure -> false for consistency with session80.php
        }
        $row = $this->db->fetchRow($result);
        if ($row === false) {
            return ''; // not found -> empty string
        }
        [$sess_data, $sess_ip] = $row;
        if ($this->securityLevel > 1) {
            if (false === $ip->sameSubnet(
                    $sess_ip,
                    $this->bitMasks[$this->securityLevel]['v4'],
                    $this->bitMasks[$this->securityLevel]['v6']
                )) {
                return ''; // IP mismatch -> empty string
            }
        }
        return (string)$sess_data;
    }

    public function write($sessionId, $data)
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
        $this->update_cookie();
        return (bool)$ok;
    }

    public function destroy($sessionId)
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quote($sessionId)
        );
        return (bool)$this->db->exec($sql);
    }

    public function gc($max_lifetime)
    {
        if ($max_lifetime <= 0) {
            return 0; // return int for 7.4
        }

        $mintime = time() - (int)$max_lifetime;
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_updated < %u',
            $this->db->prefix('session'),
            $mintime
        );

        if ($this->db->exec($sql)) {
            return (int)$this->db->getAffectedRows();
        }
        return 0; // int on failure
    }

    // --- Helpers from your current code ---

    public function gc_force(): void
    {
        // Use \random_int() instead of mt_rand() to get a cryptographically secure,
        // uniformly distributed random number for probabilistic garbage collection.
        // This preserves the approximate 10% chance of forcing gc() (1–100 < 11),
        // while acknowledging that the underlying RNG changed from mt_rand().
        // The try/catch ensures that a failure in \random_int() (e.g. missing CSPRNG)
        // does not break session handling; in that case, gc() is simply skipped here.
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

    public function regenerate_id($delete_old_session = false)
    {
        $success = $this->enableRegenerateId
            ? session_regenerate_id((bool)$delete_old_session)
            : true;

        if ($success) {
            $this->update_cookie();
        }
        return $success;
    }

    public function update_cookie($sess_id = null, $expire = null)
    {
        if (PHP_VERSION_ID < 70300) {
            global $xoopsConfig;
            $session_name = session_name();
            $session_expire = null !== $expire
                ? (int)$expire
                : (
                ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
                    ? $xoopsConfig['session_expire'] * 60
                    : ini_get('session.cookie_lifetime')
                );
            $session_id = empty($sess_id) ? session_id() : $sess_id;
            $cookieDomain = XOOPS_COOKIE_DOMAIN;
            if (2 > substr_count($cookieDomain, '.')) {
                $cookieDomain = '.' . $cookieDomain;
            }

            xoops_setcookie(
                $session_name,
                $session_id,
                $session_expire ? time() + $session_expire : 0,
                '/',
                $cookieDomain,
                (XOOPS_PROT === 'https://'),
                true
            );
        }
    }
}
