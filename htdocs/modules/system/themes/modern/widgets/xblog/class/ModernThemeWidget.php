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
 * Modern Theme Widget for XBlog
 *
 * Dashboard statistics: published posts, drafts, categories,
 * and 5 most recent blog posts.
 *
 * Note: XBlog uses a translations table for post titles (multi-language).
 * The widget joins xblog_posts with xblog_post_translations to get titles.
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
 * XBlog module dashboard widget
 *
 * Displays published/draft post counts, category totals,
 * and recent blog posts on the admin dashboard. Joins with
 * the translations table for multi-language title support.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class XblogModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The XBlog module object
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

        $postsTable = $xoopsDB->prefix('xblog_posts');
        $transTable = $xoopsDB->prefix('xblog_post_translations');

        // Status is ENUM('draft', 'published', 'archived')
        $published = 0;
        $drafts = 0;

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$postsTable`"
            . " WHERE `status` = 'published' AND `deleted_at` IS NULL"
        );
        if ($result) {
            list($published) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$postsTable`"
            . " WHERE `status` = 'draft' AND `deleted_at` IS NULL"
        );
        if ($result) {
            list($drafts) = $xoopsDB->fetchRow($result);
        }

        // Categories (excluding soft-deleted)
        $categories = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('xblog_categories')
            . " WHERE `deleted_at` IS NULL"
        );
        if ($result) {
            list($categories) = $xoopsDB->fetchRow($result);
        }

        // Recent posts with titles from translations (English fallback)
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT p.`id`, t.`title`, p.`status`, p.`created_at`"
            . " FROM `$postsTable` p"
            . " LEFT JOIN `$transTable` t ON p.`id` = t.`post_id` AND t.`locale` = 'en'"
            . " WHERE p.`deleted_at` IS NULL"
            . " ORDER BY p.`created_at` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $isPublished = ($row['status'] === 'published');
                $recent[] = [
                    'title'        => $row['title'] ?: '(untitled)',
                    'date'         => strtotime($row['created_at']),
                    'status'       => $row['status'],
                    'status_class' => $isPublished ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'Blog',
            'icon'      => '✍️',
            'stats'     => [
                'published'  => (int) $published,
                'drafts'     => (int) $drafts,
                'categories' => (int) $categories,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/xblog/admin/',
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
