<?php
/**
 * Modern Theme Widget for NewBB (Forum)
 *
 * Dashboard statistics: total topics, total posts,
 * today's posts, and 5 most recent forum posts.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class NewbbModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function getWidgetData()
    {
        global $xoopsDB;

        // Total topics
        $topics = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('newbb_topics')
        );
        if ($result) {
            list($topics) = $xoopsDB->fetchRow($result);
        }

        // Total posts
        $posts = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('newbb_posts')
        );
        if ($result) {
            list($posts) = $xoopsDB->fetchRow($result);
        }

        // Today's posts
        $today = 0;
        $todayStart = mktime(0, 0, 0);
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('newbb_posts')
            . " WHERE `post_time` >= $todayStart"
        );
        if ($result) {
            list($today) = $xoopsDB->fetchRow($result);
        }

        // Recent posts
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `subject`, `post_time`, `poster_name`"
            . " FROM " . $xoopsDB->prefix('newbb_posts')
            . " ORDER BY `post_time` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $recent[] = [
                    'title'  => $row['subject'],
                    'date'   => $row['post_time'],
                    'author' => $row['poster_name'],
                ];
            }
        }

        return [
            'title'     => 'Forum',
            'icon'      => '💬',
            'stats'     => [
                'topics' => (int) $topics,
                'posts'  => (int) $posts,
                'today'  => (int) $today,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/newbb/admin/',
        ];
    }

    public function getWidgetPriority()
    {
        return 35;
    }

    public function isWidgetEnabled()
    {
        return true;
    }
}
