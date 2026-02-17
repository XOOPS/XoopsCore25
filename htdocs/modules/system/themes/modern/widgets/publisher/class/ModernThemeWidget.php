<?php
/**
 * Modern Theme Widget for Publisher
 *
 * Provides dashboard statistics for the Publisher module:
 * published articles, pending submissions, and recent articles.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class PublisherModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Get widget data for the dashboard
     *
     * @return array|false Widget data or false on failure
     */
    public function getWidgetData()
    {
        global $xoopsDB;

        $table = $xoopsDB->prefix('publisher_items');

        // Status constants from Publisher\Constants:
        // 1 = Submitted, 2 = Published, 3 = Offline, 4 = Rejected
        $published = 0;
        $submitted = 0;

        // Total published articles
        $result = $xoopsDB->query("SELECT COUNT(*) FROM $table WHERE status = 2");
        if ($result) {
            list($published) = $xoopsDB->fetchRow($result);
        }

        // Pending / submitted articles
        $result = $xoopsDB->query("SELECT COUNT(*) FROM $table WHERE status = 1");
        if ($result) {
            list($submitted) = $xoopsDB->fetchRow($result);
        }

        // Categories count
        $categories = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('publisher_categories')
        );
        if ($result) {
            list($categories) = $xoopsDB->fetchRow($result);
        }

        // Recent articles (5 most recent, any status)
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT itemid, title, datesub, status FROM $table"
            . " ORDER BY datesub DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $statusLabel = 'draft';
                $statusClass = 'warning';
                switch ((int) $row['status']) {
                    case 2:
                        $statusLabel = 'published';
                        $statusClass = 'success';
                        break;
                    case 1:
                        $statusLabel = 'submitted';
                        $statusClass = 'warning';
                        break;
                    case 3:
                        $statusLabel = 'offline';
                        $statusClass = 'warning';
                        break;
                    case 4:
                        $statusLabel = 'rejected';
                        $statusClass = 'warning';
                        break;
                }
                $recent[] = [
                    'title'        => $row['title'],
                    'date'         => $row['datesub'],
                    'status'       => $statusLabel,
                    'status_class' => $statusClass,
                ];
            }
        }

        return [
            'title'     => 'Publisher',
            'icon'      => '📝',
            'stats'     => [
                'published'  => (int) $published,
                'submitted'  => (int) $submitted,
                'categories' => (int) $categories,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/publisher/admin/',
        ];
    }

    /**
     * @return int Priority (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 30;
    }

    /**
     * @return bool
     */
    public function isWidgetEnabled()
    {
        return true;
    }
}
