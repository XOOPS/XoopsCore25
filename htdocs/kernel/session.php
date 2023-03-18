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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Handler for a session
 * @package             kernel
 *
 * @author              Kazumi Ono    <onokazu@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 */
class XoopsSessionHandler
{
    /**
     * Database connection
     *
     * @var object
     * @access    private
     */
    public $db;

    /**
     * Security checking level
     *
     * Possible value:
     *    0 - no check;
     *    1 - check browser characteristics (HTTP_USER_AGENT/HTTP_ACCEPT_LANGUAGE), to be implemented in the future now;
     *    2 - check browser and IP A.B;
     *    3 - check browser and IP A.B.C, recommended;
     *    4 - check browser and IP A.B.C.D;
     *
     * @var int
     * @access    public
     */
    public $securityLevel = 3;

    protected $bitMasks = array(
        2 => array('v4' => 16, 'v6' => 64),
        3 => array('v4' => 24, 'v6' => 56),
        4 => array('v4' => 32, 'v6' => 128),
    );

    /**
     * Enable regenerate_id
     *
     * @var bool
     * @access    public
     */
    public $enableRegenerateId = true;

    /**
     * Constructor
     *
     * @param XoopsDatabase $db reference to the {@link XoopsDatabase} object
     *
     */
    public function __construct(XoopsDatabase $db)
    {
        global $xoopsConfig;

        $this->db = $db;
        // after php 7.3 we just let php handle the session cookie
        $lifetime = ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
            ? $xoopsConfig['session_expire'] * 60
            : ini_get('session.cookie_lifetime');
        $secure = (XOOPS_PROT === 'https://');
        if (PHP_VERSION_ID >= 70300) {
            $options = array(
                'lifetime' => $lifetime,
                'path'     => '/',
                'domain'   => XOOPS_COOKIE_DOMAIN,
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'strict',
            );
            session_set_cookie_params($options);
        } else {
            session_set_cookie_params($lifetime, '/', XOOPS_COOKIE_DOMAIN, $secure, true);
        }
    }

    /**
     * Open a session
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Close a session
     *
     * @return bool
     */
    public function close()
    {
        $this->gc_force();

        return true;
    }

    /**
     * Read a session from the database
     *
     * @param string $sessionId ID of the session
     *
     * @return string Session data
     */
    public function read($sessionId)
    {
        $ip = \Xmf\IPAddress::fromRequest();
        $sql = sprintf(
            'SELECT sess_data, sess_ip FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quoteString($sessionId)
        );

        $result = $this->db->query($sql);
        if ($this->db->isResultSet($result)) {
            if (list($sess_data, $sess_ip) = $this->db->fetchRow($result)) {
                if ($this->securityLevel > 1) {
                    if (false === $ip->sameSubnet(
                        $sess_ip,
                        $this->bitMasks[$this->securityLevel]['v4'],
                        $this->bitMasks[$this->securityLevel]['v6']
                    )) {
                        $sess_data = '';
                    }
                }

                return $sess_data;
            }
        }

        return '';
    }

    /**
     * Write a session to the database
     *
     * @param string $sessionId
     * @param string $data
     *
     * @return bool
     **/
    public function write($sessionId, $data)
    {
        $myReturn = true;
        $remoteAddress = \Xmf\IPAddress::fromRequest()->asReadable();
        $sessionId = $this->db->quoteString($sessionId);
        $sql = sprintf(
            'UPDATE %s SET sess_updated = %u, sess_data = %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            time(),
            $this->db->quoteString($data),
            $sessionId
        );
        $this->db->queryF($sql);
        if (!$this->db->getAffectedRows()) {
            $sql = sprintf(
                'INSERT INTO %s (sess_id, sess_updated, sess_ip, sess_data) VALUES (%s, %u, %s, %s)',
                $this->db->prefix('session'),
                $sessionId,
                time(),
                $this->db->quote($remoteAddress),
                $this->db->quote($data)
            );

            $myReturn = $this->db->queryF($sql);
        }
        $this->update_cookie();
        return $myReturn;
    }

    /**
     * Destroy a session
     *
     * @param string $sessionId
     *
     * @return bool
     **/
    public function destroy($sessionId)
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quoteString($sessionId)
        );
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Garbage Collector
     *
     * @param  int $expire Time in seconds until a session expires
     * @return bool
     **/
    public function gc($expire)
    {
        if (empty($expire)) {
            return true;
        }

        $mintime = time() - (int)$expire;
        $sql     = sprintf('DELETE FROM %s WHERE sess_updated < %u', $this->db->prefix('session'), $mintime);

        return $this->db->queryF($sql);
    }

    /**
     * Force gc for situations where gc is registered but not executed
     **/
    public function gc_force()
    {
        if (mt_rand(1, 100) < 11) {
            $expire = @ini_get('session.gc_maxlifetime');
            $expire = ($expire > 0) ? $expire : 900;
            $this->gc($expire);
        }
    }

    /**
     * Update the current session id with a newly generated one
     *
     * To be refactored
     *
     * @param  bool $delete_old_session
     * @return bool
     **/
    public function regenerate_id($delete_old_session = false)
    {
        if (!$this->enableRegenerateId) {
            $success = true;
        } else {
            $success = session_regenerate_id($delete_old_session);
        }

        // Force updating cookie for session cookie
        if ($success) {
            $this->update_cookie();
        }

        return $success;
    }

    /**
     * Update cookie status for current session
     *
     * To be refactored
     * FIXME: how about $xoopsConfig['use_ssl'] is enabled?
     *
     * @param  string $sess_id session ID
     * @param  int    $expire  Time in seconds until a session expires
     * @return bool
     **/
    public function update_cookie($sess_id = null, $expire = null)
    {
        if (PHP_VERSION_ID < 70300) {
            global $xoopsConfig;
            $session_name = session_name();
            $session_expire = null !== $expire
                ? (int)$expire
                : (($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
                    ? $xoopsConfig['session_expire'] * 60
                    : ini_get('session.cookie_lifetime')
                );
            $session_id = empty($sess_id) ? session_id() : $sess_id;
            $cookieDomain = XOOPS_COOKIE_DOMAIN;
            if (2 > substr_count($cookieDomain, '.')) {
                $cookieDomain  = '.' . $cookieDomain ;
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
