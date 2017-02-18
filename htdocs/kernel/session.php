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
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
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
        $this->db = $db;
    }

    /**
     * Open a session
     *
     * @param string $save_path
     * @param string $session_name
     *
     * @return bool
     */
    public function open($save_path, $session_name)
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
     * @param string $sess_id ID of the session
     *
     * @return array Session data
     */
    public function read($sess_id)
    {
        $ip = \Xmf\IPAddress::fromRequest();
        $sql = sprintf(
            'SELECT sess_data, sess_ip FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quoteString($sess_id)
        );
//        if (false != $result = $this->db->query($sql)) {
        $result = $this->db->query($sql);
        if (!empty($result)) {
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
     * @param string $sess_id
     * @param string $sess_data
     *
     * @return bool
     **/
    public function write($sess_id, $sess_data)
    {
        $remoteAddress = \Xmf\IPAddress::fromRequest()->asReadable();
        $sess_id = $this->db->quoteString($sess_id);
        $sql = sprintf(
            'UPDATE %s SET sess_updated = %u, sess_data = %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            time(),
            $this->db->quoteString($sess_data),
            $sess_id
        );
        $this->db->queryF($sql);
        if (!$this->db->getAffectedRows()) {
            $sql = sprintf(
                'INSERT INTO %s (sess_id, sess_updated, sess_ip, sess_data) VALUES (%s, %u, %s, %s)',
                $this->db->prefix('session'),
                $sess_id,
                time(),
                $this->db->quote($remoteAddress),
                $this->db->quote($sess_data)
            );

            return $this->db->queryF($sql);
        }

        return true;
    }

    /**
     * Destroy a session
     *
     * @param string $sess_id
     *
     * @return bool
     **/
    public function destroy($sess_id)
    {
        $sql = sprintf(
            'DELETE FROM %s WHERE sess_id = %s',
            $this->db->prefix('session'),
            $this->db->quoteString($sess_id)
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
        global $xoopsConfig;
        $session_name = ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
            ? $xoopsConfig['session_name']
            : session_name();
        $session_expire = null !== $expire
            ? (int)$expire
            : (($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '')
                ? $xoopsConfig['session_expire'] * 60
                : ini_get('session.cookie_lifetime')
            );
        $session_id     = empty($sess_id) ? session_id() : $sess_id;
        setcookie(
            $session_name,
            $session_id,
            $session_expire ? time() + $session_expire : 0,
            '/',
            XOOPS_COOKIE_DOMAIN,
            (XOOPS_PROT === 'https://'),
            true
        );
    }
}
