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
 * Modern Theme Widget for News
 *
 * Dashboard statistics: published stories, pending stories,
 * today's posts, and 5 most recent news stories.
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
 * News module dashboard widget
 *
 * Displays published/pending story counts, today's activity,
 * and recent news stories on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class NewsModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The News module object
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

        $table = $xoopsDB->prefix('news_stories');

        // Total published
        $published = 0;
        $result = $xoopsDB->query("SELECT COUNT(*) FROM `$table` WHERE `published` > 0");
        if ($result) {
            list($published) = $xoopsDB->fetchRow($result);
        }

        // Pending approval
        $pending = 0;
        $result = $xoopsDB->query("SELECT COUNT(*) FROM `$table` WHERE `published` = 0");
        if ($result) {
            list($pending) = $xoopsDB->fetchRow($result);
        }

        // Today's stories
        $today = 0;
        $todayStart = mktime(0, 0, 0);
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table` WHERE `created` >= $todayStart"
        );
        if ($result) {
            list($today) = $xoopsDB->fetchRow($result);
        }

        // Recent stories
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `storyid`, `title`, `published`, `created`"
            . " FROM `$table` ORDER BY `created` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $recent[] = [
                    'title'        => htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'),
                    'date'         => $row['created'],
                    'status'       => $row['published'] > 0 ? 'published' : 'pending',
                    'status_class' => $row['published'] > 0 ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'News',
            'icon'      => '📰',
            'stats'     => [
                'published' => (int) $published,
                'pending'   => (int) $pending,
                'today'     => (int) $today,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/news/admin/',
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
