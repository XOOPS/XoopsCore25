<?php
/**
 * XOOPS comment renderer
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
 * @subpackage          comment
 * @since               2.0.0
 * @author              Kazumi Ono <onokazu@xoops.org>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Display comments
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @access public
 */
class XoopsCommentRenderer
{
    /**
     * *#@+
     *
     * @access private
     */
    public $_tpl;
    public $_comments;
    public $_useIcons    = true;
    public $_doIconCheck = false;
    public $_memberHandler;
    public $_statusText;
    /**
     * *#@-
     */

    /**
     * Constructor
     *
     * @param XoopsTpl $tpl
     * @param boolean  $use_icons
     * @param boolean  $do_iconcheck
     *
     */
    public function __construct(XoopsTpl $tpl, $use_icons = true, $do_iconcheck = false)
    {
        $this->_tpl           = $tpl;
        $this->_useIcons      = (bool)$use_icons;
        $this->_doIconCheck   = (bool)$do_iconcheck;
        /* @var $this->_memberHandler XoopsMemberHandler  */
        $this->_memberHandler = xoops_getHandler('member');
        $this->_statusText    = array(
            XOOPS_COMMENT_PENDING => '<span style="text-decoration: none; font-weight: bold; color: #00ff00;">' . _CM_PENDING . '</span>',
            XOOPS_COMMENT_ACTIVE  => '<span style="text-decoration: none; font-weight: bold; color: #ff0000;">' . _CM_ACTIVE . '</span>',
            XOOPS_COMMENT_HIDDEN  => '<span style="text-decoration: none; font-weight: bold; color: #0000ff;">' . _CM_HIDDEN . '</span>');
    }

    /**
     * Access the only instance of this class
     *
     * @param  XoopsTpl $tpl reference to a {@link Smarty} object
     * @param  boolean  $use_icons
     * @param  boolean  $do_iconcheck
     * @return \XoopsCommentRenderer
     */
    public static function instance(XoopsTpl $tpl, $use_icons = true, $do_iconcheck = false)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new XoopsCommentRenderer($tpl, $use_icons, $do_iconcheck);
        }

        return $instance;
    }

    /**
     * Accessor
     *
     * @param array $comments_arr array of {@link XoopsComment} objects
     */
    public function setComments(&$comments_arr)
    {
        if (isset($this->_comments)) {
            unset($this->_comments);
        }
        $this->_comments =& $comments_arr;
    }

    /**
     * Render the comments in flat view
     *
     * @param boolean $admin_view
     */
    public function renderFlatView($admin_view = false)
    {
        $count = count($this->_comments);
        for ($i = 0; $i < $count; ++$i) {
            if (false !== $this->_useIcons) {
                $title = $this->_getTitleIcon($this->_comments[$i]->getVar('com_icon')) . '&nbsp;' . $this->_comments[$i]->getVar('com_title');
            } else {
                $title = $this->_comments[$i]->getVar('com_title');
            }
            // Start edit by voltan
            $poster = $this->_getPosterArray($this->_comments[$i]->getVar('com_uid'), $this->_comments[$i]->getVar('com_user'), $this->_comments[$i]->getVar('com_url'));
            if (false !== (bool)$admin_view) {
                $com_email = $this->_comments[$i]->getVar('com_email');
                $text      = $this->_comments[$i]->getVar('com_text');
                $text .= '<div style="text-align:right; margin-top: 2px; margin-bottom: 0; margin-right: 2px;">';
                $text .= _CM_STATUS . ': ' . $this->_statusText[$this->_comments[$i]->getVar('com_status')] . '<br>';
                $text .= 'IP: <span style="font-weight: bold;">' . $this->_comments[$i]->getVar('com_ip') . '</span>';
                if (!empty($com_email)) {
                    $text .= '<br>' . _CM_EMAIL . ' :<span style="font-weight: bold;"><a href="mailto:' . $com_email . '" title="' . $com_email . '">' . $com_email . '</a></span>';
                }
                $text .= '</div>';
            } else {
                // hide comments that are not active
                if (XOOPS_COMMENT_ACTIVE != $this->_comments[$i]->getVar('com_status')) {
                    continue;
                } else {
                    $text = $this->_comments[$i]->getVar('com_text');
                }
            }
            // End edit by voltan
            $this->_tpl->append('comments', array(
                'id'            => $this->_comments[$i]->getVar('com_id'),
                'title'         => $title,
                'text'          => $text,
                'date_posted'   => formatTimestamp($this->_comments[$i]->getVar('com_created'), 'm'),
                'date_modified' => formatTimestamp($this->_comments[$i]->getVar('com_modified'), 'm'),
                'poster'        => $poster));
        }
    }

    /**
     * Render the comments in thread view
     *
     * This method calls itself recursively
     *
     * @param  integer $comment_id Should be "0" when called by client
     * @param  boolean $admin_view
     * @param  boolean $show_nav
     * @return void
     */
    public function renderThreadView($comment_id = 0, $admin_view = false, $show_nav = true)
    {
        include_once $GLOBALS['xoops']->path('class/tree.php');
        // construct comment tree
        $xot  = new XoopsObjectTree($this->_comments, 'com_id', 'com_pid', 'com_rootid');
        $tree =& $xot->getTree();

        if (false !== $this->_useIcons) {
            $title = $this->_getTitleIcon($tree[$comment_id]['obj']->getVar('com_icon')) . '&nbsp;' . $tree[$comment_id]['obj']->getVar('com_title');
        } else {
            $title = $tree[$comment_id]['obj']->getVar('com_title');
        }
        if (false !== (bool)$show_nav && $tree[$comment_id]['obj']->getVar('com_pid') != 0) {
            $this->_tpl->assign('lang_top', _CM_TOP);
            $this->_tpl->assign('lang_parent', _CM_PARENT);
            $this->_tpl->assign('show_threadnav', true);
        } else {
            $this->_tpl->assign('show_threadnav', false);
        }
        $admin_view = (bool)$admin_view;
        if (false !== $admin_view) {
            // admins can see all
            $com_email = $tree[$comment_id]['obj']->getVar('com_email');
            $text      = $tree[$comment_id]['obj']->getVar('com_text');
            $text .= '<div style="text-align:right; margin-top: 2px; margin-bottom: 0; margin-right: 2px;">';
            $text .= _CM_STATUS . ': ' . $this->_statusText[$tree[$comment_id]['obj']->getVar('com_status')] . '<br>';
            $text .= 'IP: <span style="font-weight: bold;">' . $tree[$comment_id]['obj']->getVar('com_ip') . '</span>';
            if (!empty($com_email)) {
                $text .= '<br>' . _CM_EMAIL . ' :<span style="font-weight: bold;"><a href="mailto:' . $com_email . '" title="' . $com_email . '">' . $com_email . '</a></span>';
            }
            $text .= '</div>';
        } else {
            // hide comments that are not active
            if (XOOPS_COMMENT_ACTIVE != $tree[$comment_id]['obj']->getVar('com_status')) {
                // if there are any child comments, display them as root comments
                if (isset($tree[$comment_id]['child']) && !empty($tree[$comment_id]['child'])) {
                    foreach ($tree[$comment_id]['child'] as $child_id) {
                        $this->renderThreadView($child_id, $admin_view, false);
                    }
                }

                return null;
            } else {
                $text = $tree[$comment_id]['obj']->getVar('com_text');
            }
        }
        $replies = array();
        $this->_renderThreadReplies($tree, $comment_id, $replies, '&nbsp;&nbsp;', $admin_view);
        $show_replies = (count($replies) > 0);// ? true : false;
        // Start edit by voltan
        $this->_tpl->append('comments', array(
            'pid'           => $tree[$comment_id]['obj']->getVar('com_pid'),
            'id'            => $tree[$comment_id]['obj']->getVar('com_id'),
            'itemid'        => $tree[$comment_id]['obj']->getVar('com_itemid'),
            'rootid'        => $tree[$comment_id]['obj']->getVar('com_rootid'),
            'title'         => $title,
            'text'          => $text,
            'date_posted'   => formatTimestamp($tree[$comment_id]['obj']->getVar('com_created'), 'm'),
            'date_modified' => formatTimestamp($tree[$comment_id]['obj']->getVar('com_modified'), 'm'),
            'poster'        => $this->_getPosterArray($tree[$comment_id]['obj']->getVar('com_uid'), $tree[$comment_id]['obj']->getVar('com_user'), $tree[$comment_id]['obj']->getVar('com_url')),
            'replies'       => $replies,
            'show_replies'  => $show_replies));
        // End edit by voltan
    }

    /**
     * Render replies to a thread
     *
     * @param array   $thread
     * @param int     $key
     * @param array   $replies
     * @param string  $prefix
     * @param bool    $admin_view
     * @param integer $depth
     * @param string  $current_prefix
     * @access   private
     */
    public function _renderThreadReplies(&$thread, $key, &$replies, $prefix, $admin_view, $depth = 0, $current_prefix = '')
    {
        $admin_view = (bool)$admin_view;
        if ($depth > 0) {
            if (false !== $this->_useIcons) {
                $title = $this->_getTitleIcon($thread[$key]['obj']->getVar('com_icon')) . '&nbsp;' . $thread[$key]['obj']->getVar('com_title');
            } else {
                $title = $thread[$key]['obj']->getVar('com_title');
            }
            $title = (false !== $admin_view) ? $title . ' ' . $this->_statusText[$thread[$key]['obj']->getVar('com_status')] : $title;
            // Start edit by voltan
            $replies[] = array(
                'id'          => $key,
                'prefix'      => $current_prefix,
                'date_posted' => formatTimestamp($thread[$key]['obj']->getVar('com_created'), 'm'),
                'title'       => $title,
                'root_id'     => $thread[$key]['obj']->getVar('com_rootid'),
                'status'      => $this->_statusText[$thread[$key]['obj']->getVar('com_status')],
                'poster'      => $this->_getPosterName($thread[$key]['obj']->getVar('com_uid'), $thread[$key]['obj']->getVar('com_user'), $thread[$key]['obj']->getVar('com_url')));
            // End edit by voltan
            $current_prefix .= $prefix;
        }
        if (isset($thread[$key]['child']) && !empty($thread[$key]['child'])) {
            ++$depth;
            foreach ($thread[$key]['child'] as $childkey) {
                if (!$admin_view && $thread[$childkey]['obj']->getVar('com_status') != XOOPS_COMMENT_ACTIVE) {
                    // skip this comment if it is not active and continue on processing its child comments instead
                    if (isset($thread[$childkey]['child']) && !empty($thread[$childkey]['child'])) {
                        foreach ($thread[$childkey]['child'] as $childchildkey) {
                            $this->_renderThreadReplies($thread, $childchildkey, $replies, $prefix, $admin_view, $depth);
                        }
                    }
                } else {
                    $this->_renderThreadReplies($thread, $childkey, $replies, $prefix, $admin_view, $depth, $current_prefix);
                }
            }
        }
    }

    /**
     * Render comments in nested view
     *
     * Danger: Recursive!
     *
     * @param  integer $comment_id Always "0" when called by client.
     * @param  boolean $admin_view
     * @return void
     */
    public function renderNestView($comment_id = 0, $admin_view = false)
    {
        include_once $GLOBALS['xoops']->path('class/tree.php');
        $xot  = new XoopsObjectTree($this->_comments, 'com_id', 'com_pid', 'com_rootid');
        $tree =& $xot->getTree();
        if (false !== $this->_useIcons) {
            $title = $this->_getTitleIcon($tree[$comment_id]['obj']->getVar('com_icon')) . '&nbsp;' . $tree[$comment_id]['obj']->getVar('com_title');
        } else {
            $title = $tree[$comment_id]['obj']->getVar('com_title');
        }
        $admin_view = (bool)$admin_view;
        if (false !== $admin_view) {
            $com_email = $tree[$comment_id]['obj']->getVar('com_email');
            $text      = $tree[$comment_id]['obj']->getVar('com_text');
            $text .= '<div style="text-align:right; margin-top: 2px; margin-bottom: 0; margin-right: 2px;">';
            $text .= _CM_STATUS . ': ' . $this->_statusText[$tree[$comment_id]['obj']->getVar('com_status')] . '<br>';
            $text .= 'IP: <span style="font-weight: bold;">' . $tree[$comment_id]['obj']->getVar('com_ip') . '</span>';
            if (!empty($com_email)) {
                $text .= '<br>' . _CM_EMAIL . ' :<span style="font-weight: bold;"><a href="mailto:' . $com_email . '" title="' . $com_email . '">' . $com_email . '</a></span>';
            }
            $text .= '</div>';
        } else {
            // skip this comment if it is not active and continue on processing its child comments instead
            if (XOOPS_COMMENT_ACTIVE != $tree[$comment_id]['obj']->getVar('com_status')) {
                // if there are any child comments, display them as root comments
                if (isset($tree[$comment_id]['child']) && !empty($tree[$comment_id]['child'])) {
                    foreach ($tree[$comment_id]['child'] as $child_id) {
                        $this->renderNestView($child_id, $admin_view);
                    }
                }

                return null;
            } else {
                $text = $tree[$comment_id]['obj']->getVar('com_text');
            }
        }
        $replies = array();
        $this->_renderNestReplies($tree, $comment_id, $replies, 25, $admin_view);
        // Start edit by voltan
        $this->_tpl->append('comments', array(
            'pid'           => $tree[$comment_id]['obj']->getVar('com_pid'),
            'id'            => $tree[$comment_id]['obj']->getVar('com_id'),
            'itemid'        => $tree[$comment_id]['obj']->getVar('com_itemid'),
            'rootid'        => $tree[$comment_id]['obj']->getVar('com_rootid'),
            'title'         => $title,
            'text'          => $text,
            'date_posted'   => formatTimestamp($tree[$comment_id]['obj']->getVar('com_created'), 'm'),
            'date_modified' => formatTimestamp($tree[$comment_id]['obj']->getVar('com_modified'), 'm'),
            'poster'        => $this->_getPosterArray($tree[$comment_id]['obj']->getVar('com_uid'), $tree[$comment_id]['obj']->getVar('com_user'), $tree[$comment_id]['obj']->getVar('com_url')),
            'replies'       => $replies));
        // End edit by voltan
    }

    /**
     * Render replies in nested view
     *
     * @param array   $thread
     * @param int     $key
     * @param array   $replies
     * @param string|int  $prefix
     * @param bool    $admin_view
     * @param integer $depth
     * @access private
     */
    public function _renderNestReplies(&$thread, $key, &$replies, $prefix, $admin_view, $depth = 0)
    {
        if ($depth > 0) {
            if (false !== $this->_useIcons) {
                $title = $this->_getTitleIcon($thread[$key]['obj']->getVar('com_icon')) . '&nbsp;' . $thread[$key]['obj']->getVar('com_title');
            } else {
                $title = $thread[$key]['obj']->getVar('com_title');
            }
            $admin_view = (bool)$admin_view;
            $text = (false !== $admin_view) ? $thread[$key]['obj']->getVar('com_text') . '<div style="text-align:right; margin-top: 2px; margin-right: 2px;">' . _CM_STATUS . ': ' . $this->_statusText[$thread[$key]['obj']->getVar('com_status')] . '<br>IP: <span style="font-weight: bold;">' . $thread[$key]['obj']->getVar('com_ip') . '</span><br>' . _CM_EMAIL . ' :<span style="font-weight: bold;">' . $thread[$key]['obj']->getVar('com_email') . '</span></div>' : $thread[$key]['obj']->getVar('com_text');
            // Start edit by voltan
            $replies[] = array(
                'id'            => $key,
                'prefix'        => $prefix,
                'pid'           => $thread[$key]['obj']->getVar('com_pid'),
                'itemid'        => $thread[$key]['obj']->getVar('com_itemid'),
                'rootid'        => $thread[$key]['obj']->getVar('com_rootid'),
                'title'         => $title,
                'text'          => $text,
                'date_posted'   => formatTimestamp($thread[$key]['obj']->getVar('com_created'), 'm'),
                'date_modified' => formatTimestamp($thread[$key]['obj']->getVar('com_modified'), 'm'),
                'poster'        => $this->_getPosterArray($thread[$key]['obj']->getVar('com_uid'), $thread[$key]['obj']->getVar('com_user'), $thread[$key]['obj']->getVar('com_url')));
            // End edit by voltan
            $prefix += 25;
        }
        if (isset($thread[$key]['child']) && !empty($thread[$key]['child'])) {
            ++$depth;
            foreach ($thread[$key]['child'] as $childkey) {
                if (!$admin_view && $thread[$childkey]['obj']->getVar('com_status') != XOOPS_COMMENT_ACTIVE) {
                    // skip this comment if it is not active and continue on processing its child comments instead
                    if (isset($thread[$childkey]['child']) && !empty($thread[$childkey]['child'])) {
                        foreach ($thread[$childkey]['child'] as $childchildkey) {
                            $this->_renderNestReplies($thread, $childchildkey, $replies, $prefix, $admin_view, $depth);
                        }
                    }
                } else {
                    $this->_renderNestReplies($thread, $childkey, $replies, $prefix, $admin_view, $depth);
                }
            }
        }
    }

    /**
     * Get the name of the poster
     *
     * @param  int    $poster_id
     * @param         $poster_user
     * @param         $poster_website
     * @return string
     * @access private
     */
    // Start edit by voltan
    public function _getPosterName($poster_id, $poster_user, $poster_website)
    {
        $poster['id'] = (int)$poster_id;
        if ($poster['id'] > 0) {
            $com_poster = $this->_memberHandler->getUser($poster_id);
            if (is_object($com_poster)) {
                $poster['uname'] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $poster['id'] . '">' . $com_poster->getVar('uname') . '</a>';
            }
        } elseif ($poster['id'] == 0 && $poster_user != '') {
            $poster['id'] = 0; // to cope with deleted user accounts
            if (!empty($poster_website)) {
                $poster['uname'] = '<a href="' . $poster_website . '">' . $poster_user . '</a>';
            } else {
                $poster['uname'] = $poster_user;
            }
        } else {
            $poster['id']    = 0; // to cope with deleted user accounts
            $poster['uname'] = $GLOBALS['xoopsConfig']['anonymous'];
        }

        return $poster;
    }
    // End edit by voltan

    /**
     * Get an array with info about the poster
     *
     * @param  int   $poster_id
     * @param        $poster_user
     * @param        $poster_website
     * @return array
     * @access private
     */
    // Start edit by voltan
    public function _getPosterArray($poster_id, $poster_user, $poster_website)
    {
        $poster['id'] = (int)$poster_id;
        if ($poster['id'] > 0) {
            /* @var  $com_poster XoopsUser */
            $com_poster = $this->_memberHandler->getUser($poster['id']);
            if (is_object($com_poster)) {
                $poster['uname']      = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $poster['id'] . '">' . $com_poster->getVar('uname') . '</a>';
                $poster_rank          = $com_poster->rank();
                $poster['rank_image'] = ($poster_rank['image'] != '') ? $poster_rank['image'] : 'blank.gif';
                $poster['rank_title'] = $poster_rank['title'];
                $poster['avatar']     = $com_poster->getVar('user_avatar');
                $poster['regdate']    = formatTimestamp($com_poster->getVar('user_regdate'), 's');
                $poster['from']       = $com_poster->getVar('user_from');
                $poster['postnum']    = $com_poster->getVar('posts');
                $poster['status']     = $com_poster->isOnline() ? _CM_ONLINE : '';
            }
        } elseif ($poster['id'] == 0 && $poster_user != '') {
            if (!empty($poster_website)) {
                $poster['uname'] = '<a href="' . $poster_website . '">' . $poster_user . '</a>';
            } else {
                $poster['uname'] = $poster_user;
            }
            $poster['id']         = 0; // to cope with deleted user accounts
            $poster['rank_title'] = '';
            $poster['avatar']     = 'blank.gif';
            $poster['regdate']    = '';
            $poster['from']       = '';
            $poster['postnum']    = 0;
            $poster['status']     = '';
        } else {
            $poster['uname']      = $GLOBALS['xoopsConfig']['anonymous'];
            $poster['id']         = 0; // to cope with deleted user accounts
            $poster['rank_title'] = '';
            $poster['avatar']     = 'blank.gif';
            $poster['regdate']    = '';
            $poster['from']       = '';
            $poster['postnum']    = 0;
            $poster['status']     = '';
        }

        return $poster;
    }
    // End edit by voltan

    /**
     * Get the IMG tag for the title icon
     *
     * @param  string $icon_image
     * @return string HTML IMG tag
     * @access private
     */
    public function _getTitleIcon($icon_image)
    {
        $icon_image = htmlspecialchars(trim($icon_image));
        if ($icon_image != '') {
            if (false !== $this->_doIconCheck) {
                if (!file_exists($GLOBALS['xoops']->path('images/subject/' . $icon_image))) {
                    return '<img src="' . XOOPS_URL . '/images/icons/no_posticon.gif" alt="" />';
                } else {
                    return '<img src="' . XOOPS_URL . '/images/subject/' . $icon_image . '" alt="" />';
                }
            } else {
                return '<img src="' . XOOPS_URL . '/images/subject/' . $icon_image . '" alt="" />';
            }
        }

        return '<img src="' . XOOPS_URL . '/images/icons/no_posticon.gif" alt="" />';
    }
}
