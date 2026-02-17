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
 * Modern Theme Widget for TDMDownloads
 *
 * Dashboard statistics: online downloads, awaiting approval, total hits,
 * categories, and 5 most recent downloads.
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
 * TDMDownloads module dashboard widget
 *
 * Displays online/pending download counts, total hit statistics,
 * category totals, and recent downloads on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class TdmdownloadsModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The TDMDownloads module object
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

        $table = $xoopsDB->prefix('tdmdownloads_downloads');

        // Status: 0=offline, 1=online, 2=awaiting approval
        $online = 0;
        $pending = 0;
        $totalHits = 0;

        $result = $xoopsDB->query("SELECT COUNT(*) FROM `$table` WHERE `status` = 1");
        if ($result) {
            list($online) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query("SELECT COUNT(*) FROM `$table` WHERE `status` = 2");
        if ($result) {
            list($pending) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query("SELECT SUM(`hits`) FROM `$table` WHERE `status` = 1");
        if ($result) {
            $row = $xoopsDB->fetchRow($result);
            $totalHits = $row[0] ? (int) $row[0] : 0;
        }

        // Categories
        $categories = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('tdmdownloads_cat')
        );
        if ($result) {
            list($categories) = $xoopsDB->fetchRow($result);
        }

        // Recent downloads
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `lid`, `title`, `date`, `status`, `hits` FROM `$table`"
            . " ORDER BY `date` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $statusLabel = 'offline';
                $statusClass = 'warning';
                if ((int) $row['status'] === 1) {
                    $statusLabel = 'online';
                    $statusClass = 'success';
                } elseif ((int) $row['status'] === 2) {
                    $statusLabel = 'pending';
                    $statusClass = 'warning';
                }
                $recent[] = [
                    'title'        => $row['title'],
                    'date'         => $row['date'],
                    'status'       => $statusLabel,
                    'status_class' => $statusClass,
                ];
            }
        }

        return [
            'title'     => 'Downloads',
            'icon'      => '📥',
            'stats'     => [
                'online'     => (int) $online,
                'pending'    => (int) $pending,
                'total_hits' => (int) $totalHits,
                'categories' => (int) $categories,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/tdmdownloads/admin/',
        ];
    }

    /**
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 45;
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
