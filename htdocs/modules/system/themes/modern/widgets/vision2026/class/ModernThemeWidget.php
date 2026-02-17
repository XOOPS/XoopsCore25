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
 * Modern Theme Widget for Vision2026
 *
 * Dashboard statistics: published articles, drafts, categories,
 * and 5 most recent articles.
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
 * Vision2026 module dashboard widget
 *
 * Displays published/draft article counts, category totals,
 * and recent articles on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class Vision2026ModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The Vision2026 module object
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

        $table = $xoopsDB->prefix('vision2026_articles');

        // Status is ENUM('draft', 'published', 'archived')
        $published = 0;
        $drafts = 0;

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table` WHERE `status` = 'published'"
        );
        if ($result) {
            list($published) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table` WHERE `status` = 'draft'"
        );
        if ($result) {
            list($drafts) = $xoopsDB->fetchRow($result);
        }

        // Categories
        $categories = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('vision2026_categories')
        );
        if ($result) {
            list($categories) = $xoopsDB->fetchRow($result);
        }

        // Recent articles
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `id`, `title`, `status`, `views`, `created_at`"
            . " FROM `$table`"
            . " ORDER BY `created_at` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $isPublished = ($row['status'] === 'published');
                $recent[] = [
                    'title'        => $row['title'],
                    'date'         => strtotime($row['created_at']),
                    'status'       => $row['status'],
                    'status_class' => $isPublished ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'Vision 2026',
            'icon'      => '🔮',
            'stats'     => [
                'published'  => (int) $published,
                'drafts'     => (int) $drafts,
                'categories' => (int) $categories,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/vision2026/admin/',
        ];
    }

    /**
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 40;
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
