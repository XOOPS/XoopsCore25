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
 * Modern Theme Widget for Protector
 *
 * Provides security dashboard statistics mirroring the data from
 * admin/stats.php: attack events grouped by time period, banned IPs,
 * and recent log entries.
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
 * Protector module dashboard widget
 *
 * Displays security event counts by time period, banned IPs,
 * and recent log entries on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class ProtectorModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The Protector module object
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Get widget data for the dashboard
     *
     * Shows the same time-bucketed stats that admin/stats.php displays:
     * events in the last hour, day, week, and month plus banned IPs.
     *
     * @return array|false Widget data array or false on failure
     */
    public function getWidgetData()
    {
        global $xoopsDB;

        $dirname  = $this->module->getVar('dirname');
        $logTable = $xoopsDB->prefix($dirname . '_log');

        // Count events by time period (same buckets as admin/stats.php)
        $lastHour  = 0;
        $lastDay   = 0;
        $lastWeek  = 0;
        $lastMonth = 0;

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$logTable`"
            . " WHERE `timestamp` > NOW() - INTERVAL 3600 SECOND"
        );
        if ($result) {
            list($lastHour) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$logTable`"
            . " WHERE `timestamp` > NOW() - INTERVAL 86400 SECOND"
        );
        if ($result) {
            list($lastDay) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$logTable`"
            . " WHERE `timestamp` > NOW() - INTERVAL 604800 SECOND"
        );
        if ($result) {
            list($lastWeek) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$logTable`"
            . " WHERE `timestamp` > NOW() - INTERVAL 2592000 SECOND"
        );
        if ($result) {
            list($lastMonth) = $xoopsDB->fetchRow($result);
        }

        // Banned IPs (access table entries with future expiry)
        $bannedIPs = 0;
        $accessTable = $xoopsDB->prefix($dirname . '_access');
        $result = $xoopsDB->query(
            "SELECT COUNT(DISTINCT ip) FROM `$accessTable`"
            . " WHERE `expire` > UNIX_TIMESTAMP()"
        );
        if ($result) {
            list($bannedIPs) = $xoopsDB->fetchRow($result);
        }

        // Recent log entries (5 most recent)
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `lid`, `type`, `ip`, `timestamp` FROM `$logTable`"
            . " ORDER BY `timestamp` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $recent[] = [
                    'title'        => htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'),
                    'author'       => htmlspecialchars($row['ip'], ENT_QUOTES, 'UTF-8'),
                    'date'         => strtotime($row['timestamp']),
                    'status'       => 'blocked',
                    'status_class' => 'warning',
                ];
            }
        }

        return [
            'title'     => 'Security',
            'icon'      => '🛡️',
            'stats'     => [
                'last_hour'  => (int) $lastHour,
                'last_day'   => (int) $lastDay,
                'last_week'  => (int) $lastWeek,
                'last_month' => (int) $lastMonth,
                'banned_ips' => (int) $bannedIPs,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/' . $dirname . '/admin/center.php',
        ];
    }

    /**
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 20;
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
