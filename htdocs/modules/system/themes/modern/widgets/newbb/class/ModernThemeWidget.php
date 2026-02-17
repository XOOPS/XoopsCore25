<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Modern Theme Widget for NewBB (Forum)
 *
 * Dashboard statistics: total topics, total posts,
 * today's posts, and 5 most recent forum posts.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

/**
 * NewBB (Forum) module dashboard widget
 *
 * Displays topic/post counts, today's activity,
 * and recent forum posts on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class NewbbModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The NewBB module object
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Get widget data for the dashboard
     *
     * @return array|false Widget data array or false on failure
     */
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
                    'title'  => htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8'),
                    'date'   => $row['post_time'],
                    'author' => htmlspecialchars($row['poster_name'], ENT_QUOTES, 'UTF-8'),
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

    /**
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 35;
    }

    /**
     * Check if the widget is enabled
     *
     * @return bool True if widget should be displayed
     */
    public function isWidgetEnabled()
    {
        return true;
    }
}
