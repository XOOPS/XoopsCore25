<?php
/**
 * XOOPS comments
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
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/kernel/object.php';
include_once XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/comment.php';

$GLOBALS['xoopsLogger']->addDeprecated("'/class/xoopscommments.php' is deprecated since XOOPS 2.5.4, please use '/kernel/comment.php' instead.");

/**
 * Xoops Comments Object Class
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @author              John Neill <catzwolf@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             kernel
 * @subpackage          comments
 * @access              public
 */
class XoopsComments extends XoopsObject
{
    public $ctable;
    public $db;

    /**
     * @param      $ctable
     * @param null|array $id
     */
    public function __construct($ctable, $id = null)
    {
        $this->ctable = $ctable;
        $this->db     = XoopsDatabaseFactory::getDatabaseConnection();
        parent::__construct();
        $this->initVar('comment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('item_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('order', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mode', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('comment', XOBJ_DTYPE_TXTAREA, null, false, null);
        $this->initVar('ip', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('pid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('date', XOBJ_DTYPE_INT, null, false);
        $this->initVar('nohtml', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('nosmiley', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('noxcode', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('user_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('icon', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('prefix', XOBJ_DTYPE_OTHER, null, false);
        if (!empty($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $this->load((int)$id);
            }
        }
    }

    /**
     * Load Comment by ID
     *
     * @param int $id
     */
    public function load($id)
    {
        $id  = (int)$id;
        $sql = 'SELECT * FROM ' . $this->ctable . ' WHERE comment_id=' . $id;
        $arr = $this->db->fetchArray($this->db->query($sql));
        $this->assignVars($arr);
    }

    /**
     * Save Comment
     *
     * @return int
     */
    public function store()
    {
        if (!$this->cleanVars()) {
            return false;
        }
        foreach ($this->cleanVars as $k => $v) {
            $$k = $v;
        }
        $isnew = false;
        if (empty($comment_id)) {
            $isnew      = true;
            $comment_id = $this->db->genId($this->ctable . '_comment_id_seq');
            $sql        = sprintf("INSERT INTO %s (comment_id, pid, item_id, date, user_id, ip, subject, comment, nohtml, nosmiley, noxcode, icon) VALUES (%u, %u, %u, %u, %u, '%s', '%s', '%s', %u, %u, %u, '%s')", $this->ctable, $comment_id, $pid, $item_id, time(), $user_id, $ip, $subject, $comment, $nohtml, $nosmiley, $noxcode, $icon);
        } else {
            $sql = sprintf("UPDATE %s SET subject = '%s', comment = '%s', nohtml = %u, nosmiley = %u, noxcode = %u, icon = '%s'  WHERE comment_id = %u", $this->ctable, $subject, $comment, $nohtml, $nosmiley, $noxcode, $icon, $comment_id);
        }
        if (!$result = $this->db->query($sql)) {
            //echo $sql;
            return false;
        }
        if (empty($comment_id)) {
            $comment_id = $this->db->getInsertId();
        }
        if ($isnew != false) {
            $sql = sprintf('UPDATE %s SET posts = posts+1 WHERE uid = %u', $this->db->prefix('users'), $user_id);
            if (!$result = $this->db->query($sql)) {
                echo 'Could not update user posts.';
            }
        }

        return $comment_id;
    }

    /**
     * Enter description here...
     *
     * @return int
     */
    public function delete()
    {
        $sql = sprintf('DELETE FROM %s WHERE comment_id = %u', $this->ctable, $this->getVar('comment_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('UPDATE %s SET posts = posts-1 WHERE uid = %u', $this->db->prefix('users'), $this->getVar('user_id'));
        if (!$result = $this->db->query($sql)) {
            echo 'Could not update user posts.';
        }
        $mytree = new XoopsTree($this->ctable, 'comment_id', 'pid');
        $arr    = $mytree->getAllChild($this->getVar('comment_id'), 'comment_id');
        $size   = count($arr);
        if ($size > 0) {
            for ($i = 0; $i < $size; ++$i) {
                $sql = sprintf('DELETE FROM %s WHERE comment_bid = %u', $this->ctable, $arr[$i]['comment_id']);
                if (!$result = $this->db->query($sql)) {
                    echo 'Could not delete comment.';
                }
                $sql = sprintf('UPDATE %s SET posts = posts-1 WHERE uid = %u', $this->db->prefix('users'), $arr[$i]['user_id']);
                if (!$result = $this->db->query($sql)) {
                    echo 'Could not update user posts.';
                }
            }
        }

        return ($size + 1);
    }

    /**
     * Get Comments Tree
     *
     * @return unknown
     */
    public function getCommentTree()
    {
        $mytree = new XoopsTree($this->ctable, 'comment_id', 'pid');
        $ret    = array();
        $tarray = $mytree->getChildTreeArray($this->getVar('comment_id'), 'comment_id');
        foreach ($tarray as $ele) {
            $ret[] = new XoopsComments($this->ctable, $ele);
        }

        return $ret;
    }

    /**
     * Get All Comments using criteria match
     *
     * @param  array  $criteria
     * @param  bool   $asobject
     * @param  string $orderby
     * @param  int    $limit
     * @param  int    $start
     * @return array
     */
    public function getAllComments($criteria = array(), $asobject = true, $orderby = 'comment_id ASC', $limit = 0, $start = 0)
    {
        $ret         = array();
        $where_query = '';
        if (is_array($criteria) && count($criteria) > 0) {
            $where_query = ' WHERE';
            foreach ($criteria as $c) {
                $where_query .= " $c AND";
            }
            $where_query = substr($where_query, 0, -4);
        }
        if (!$asobject) {
            $sql    = 'SELECT comment_id FROM ' . $this->ctable . "$where_query ORDER BY $orderby";
            $result = $this->db->query($sql, $limit, $start);
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[] = $myrow['comment_id'];
            }
        } else {
            $sql    = 'SELECT * FROM ' . $this->ctable . '' . $where_query . " ORDER BY $orderby";
            $result = $this->db->query($sql, $limit, $start);
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[] = new XoopsComments($this->ctable, $myrow);
            }
        }

        //echo $sql;
        return $ret;
    }

    /**
     * Enter printNavBar
     *
     * @param int    $item_id
     * @param string $mode
     * @param int    $order
     */
    public function printNavBar($item_id, $mode = 'flat', $order = 1)
    {
        global $xoopsConfig, $xoopsUser;
        echo "<form method='get' action='" . $_SERVER['PHP_SELF'] . "'><table width='100%' border='0' cellspacing='1' cellpadding='2'><tr><td class='bg1' align='center'><select name='mode'><option value='nocomments'";
        if ($mode === 'nocomments') {
            echo " selected";
        }
        echo '>' . _NOCOMMENTS . "</option><option value='flat'";
        if ($mode === 'flat') {
            echo " selected";
        }
        echo '>' . _FLAT . "</option><option value='thread'";
        if ($mode === 'thread' || $mode == '') {
            echo " selected";
        }
        echo '>' . _THREADED . "</option></select><select name='order'><option value='0'";
        if ($order != 1) {
            echo " selected";
        }
        echo '>' . _OLDESTFIRST . "</option><option value='1'";
        if ($order == 1) {
            echo " selected";
        }
        echo '>' . _NEWESTFIRST . "</option></select><input type='hidden' name='item_id' value='" . (int)$item_id . "' /><input type='submit' value='" . _CM_REFRESH . "' />";
        if ($xoopsConfig['anonpost'] == 1 || $xoopsUser) {
            if ($mode !== 'flat' || $mode !== 'nocomments' || $mode !== 'thread') {
                $mode = 'flat';
            }
            echo "&nbsp;<input type='button' onclick='location=\"newcomment.php?item_id=" . (int)$item_id . '&amp;order=' . (int)$order . '&amp;mode=' . $mode . "\"' value='" . _CM_POSTCOMMENT . "' />";
        }
        echo '</td></tr></table></form>';
    }

    /**
     * Show Thread
     *
     */
    public function showThreadHead()
    {
        openThread();
    }

    /**
     * Enter description here...
     *
     * @param string $order
     * @param string $mode
     * @param int    $adminview
     * @param int    $color_num
     */
    public function showThreadPost($order, $mode, $adminview = 0, $color_num = 1)
    {
        global $xoopsConfig, $xoopsUser;
        $edit_image   = '';
        $reply_image  = '';
        $delete_image = '';
        $post_date    = formatTimestamp($this->getVar('date'), 'm');
        if ($this->getVar('user_id') != 0) {
            $poster = new XoopsUser($this->getVar('user_id'));
            if (!$poster->isActive()) {
                $poster = 0;
            }
        } else {
            $poster = 0;
        }
        if ($this->getVar('icon') != null && $this->getVar('icon') != '') {
            $subject_image = "<a name='" . $this->getVar('comment_id') . "' id='" . $this->getVar('comment_id') . "'></a><img src='" . XOOPS_URL . '/images/subject/' . $this->getVar('icon') . "' alt='' />";
        } else {
            $subject_image = "<a name='" . $this->getVar('comment_id') . "' id='" . $this->getVar('comment_id') . "'></a><img src='" . XOOPS_URL . "/images/icons/no_posticon.gif' alt='' />";
        }
        if ($adminview) {
            $ip_image = "<img src='" . XOOPS_URL . "/images/icons/ip.gif' alt='" . $this->getVar('ip') . "' />";
        } else {
            $ip_image = "<img src='" . XOOPS_URL . "/images/icons/ip.gif' alt='' />";
        }
        if ($adminview || ($xoopsUser && $this->getVar('user_id') == $xoopsUser->getVar('uid'))) {
            $edit_image = "<a href='editcomment.php?comment_id=" . $this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . (int)$order . "'><img src='" . XOOPS_URL . "/images/icons/edit.gif' alt='" . _EDIT . "' /></a>";
        }
        if ($xoopsConfig['anonpost'] || $xoopsUser) {
            $reply_image = "<a href='replycomment.php?comment_id=" . $this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . (int)$order . "'><img src='" . XOOPS_URL . "/images/icons/reply.gif' alt='" . _REPLY . "' /></a>";
        }
        if ($adminview) {
            $delete_image = "<a href='deletecomment.php?comment_id=" . $this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . (int)$order . "'><img src='" . XOOPS_URL . "/images/icons/delete.gif' alt='" . _DELETE . "' /></a>";
        }

        if ($poster) {
            $text = $this->getVar('comment');
            if ($poster->getVar('attachsig')) {
                $text .= '<p><br>_________________<br>' . $poster->user_sig() . '</p>';
            }
            $reg_date = _CM_JOINED;
            $reg_date .= formatTimestamp($poster->getVar('user_regdate'), 's');
            $posts = _CM_POSTS;
            $posts .= $poster->getVar('posts');
            $user_from = _CM_FROM;
            $user_from .= $poster->getVar('user_from');
            $rank = $poster->rank();
            if ($rank['image'] != '') {
                $rank['image'] = "<img src='" . XOOPS_UPLOAD_URL . '/' . $rank['image'] . "' alt='' />";
            }
            $avatar_image = "<img src='" . XOOPS_UPLOAD_URL . '/' . $poster->getVar('user_avatar') . "' alt='' />";
            $online_image = '';
            if ($poster->isOnline()) {
                $online_image = "<span style='color:#ee0000;font-weight:bold;'>" . _CM_ONLINE . '</span>';
            }
            $profile_image = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $poster->getVar('uid') . "'><img src='" . XOOPS_URL . "/images/icons/profile.gif' alt='" . _PROFILE . "' /></a>";
            $pm_image      = '';
            if ($xoopsUser) {
                $pm_image = "<a href='javascript:openWithSelfMain(\"" . XOOPS_URL . '/pmlite.php?send2=1&amp;to_userid=' . $poster->getVar('uid') . "\",\"pmlite\",565,500);'><img src='" . XOOPS_URL . "/images/icons/pm.gif' alt='" . sprintf(_SENDPMTO, $poster->getVar('uname', 'E')) . "' /></a>";
            }
            $email_image = '';
            if ($poster->getVar('user_viewemail')) {
                $email_image = "<a href='mailto:" . $poster->getVar('email', 'E') . "'><img src='" . XOOPS_URL . "/images/icons/email.gif' alt='" . sprintf(_SENDEMAILTO, $poster->getVar('uname', 'E')) . "' /></a>";
            }
            $posterurl = $poster->getVar('url');
            $www_image = '';
            if ($posterurl != '') {
                $www_image = "<a href='$posterurl' rel='external'><img src='" . XOOPS_URL . "/images/icons/www.gif' alt='" . _VISITWEBSITE . "' /></a>";
            }
            $icq_image = '';
            if ($poster->getVar('user_icq') != '') {
                $icq_image = "<a href='http://wwp.icq.com/scripts/search.dll?to=" . $poster->getVar('user_icq', 'E') . "'><img src='" . XOOPS_URL . "/images/icons/icq_add.gif' alt='" . _ADD . "' /></a>";
            }
            $aim_image = '';
            if ($poster->getVar('user_aim') != '') {
                $aim_image = "<a href='aim:goim?screenname=" . $poster->getVar('user_aim', 'E') . '&message=Hi+' . $poster->getVar('user_aim') . "+Are+you+there?'><img src='" . XOOPS_URL . "/images/icons/aim.gif' alt='aim' /></a>";
            }
            $yim_image = '';
            if ($poster->getVar('user_yim') != '') {
                $yim_image = "<a href='http://edit.yahoo.com/config/send_webmesg?.target=" . $poster->getVar('user_yim', 'E') . "&.src=pg'><img src='" . XOOPS_URL . "/images/icons/yim.gif' alt='yim' /></a>";
            }
            $msnm_image = '';
            if ($poster->getVar('user_msnm') != '') {
                $msnm_image = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $poster->getVar('uid') . "'><img src='" . XOOPS_URL . "/images/icons/msnm.gif' alt='msnm' /></a>";
            }
            showThread($color_num, $subject_image, $this->getVar('subject'), $text, $post_date, $ip_image, $reply_image, $edit_image, $delete_image, $poster->getVar('uname'), $rank['title'], $rank['image'], $avatar_image, $reg_date, $posts, $user_from, $online_image, $profile_image, $pm_image, $email_image, $www_image, $icq_image, $aim_image, $yim_image, $msnm_image);
        } else {
            showThread($color_num, $subject_image, $this->getVar('subject'), $this->getVar('comment'), $post_date, $ip_image, $reply_image, $edit_image, $delete_image, $xoopsConfig['anonymous']);
        }
    }

    /**
     * Show Thread Footer
     *
     */
    public function showThreadFoot()
    {
        closeThread();
    }

    /**
     * Show Thread Head
     *
     * @param int|string $width
     */
    public function showTreeHead($width = '100%')
    {
        echo "<table border='0' class='outer' cellpadding='0' cellspacing='0' align='center' width='$width'><tr class='bg3' align='center'><td colspan='3'>" . _CM_REPLIES . "</td></tr><tr class='bg3' align='left'><td width='60%' class='fg2'>" . _CM_TITLE . "</td><td width='20%' class='fg2'>" . _CM_POSTER . "</td><td class='fg2'>" . _CM_POSTED . '</td></tr>';
    }

    /**
     * Show Tree Items
     *
     * @param string $order
     * @param string $mode
     * @param int    $color_num
     */
    public function showTreeItem($order, $mode, $color_num)
    {
        $bg = 'odd';
        if ($color_num == 1) {
            $bg = 'even';
        }
        $prefix = str_replace('.', '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getVar('prefix'));
        $date   = formatTimestamp($this->getVar('date'), 'm');
        $icon   = 'icons/no_posticon.gif';
        if ($this->getVar('icon') != '') {
            $icon = 'subject/' . $this->getVar('icon', 'E');
        }
        echo "<tr class='$bg' align='left'><td>" . $prefix . "<img src='" . XOOPS_URL . '/images/' . $icon . "'>&nbsp;<a href='" . $_SERVER['PHP_SELF'] . '?item_id=' . $this->getVar('item_id') . '&amp;comment_id=' . $this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . $order . '#' . $this->getVar('comment_id') . "'>" . $this->getVar('subject') . "</a></td><td><a href='" . XOOPS_URL . '/userinfo.php?uid=' . $this->getVar('user_id') . "'>" . XoopsUser::getUnameFromId($this->getVar('user_id')) . '</a></td><td>' . $date . '</td></tr>';
    }

    /**
     * Show Thread Foot
     *
     */
    public function showTreeFoot()
    {
        echo '</table><br>';
    }
}
