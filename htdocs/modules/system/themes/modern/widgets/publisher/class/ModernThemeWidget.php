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
 * Modern Theme Widget for Publisher
 *
 * Provides dashboard statistics for the Publisher module:
 * published articles, pending submissions, and recent articles.
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
 * Publisher module dashboard widget
 *
 * Displays published/submitted article counts, category totals,
 * and recent articles on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class PublisherModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The Publisher module object
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
                    'title'        => htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'),
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
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 30;
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
