<?php
/**
 * Modern Theme Widget for News
 *
 * Dashboard statistics: published stories, pending stories,
 * today's posts, and 5 most recent news stories.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class NewsModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

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
                    'title'        => $row['title'],
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

    public function getWidgetPriority()
    {
        return 35;
    }

    public function isWidgetEnabled()
    {
        return true;
    }
}
