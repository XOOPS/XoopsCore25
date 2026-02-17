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
 * Modern Theme Widget for Alumni
 *
 * Dashboard statistics: active profiles, pending profiles,
 * total connections, and 5 most recent alumni registrations.
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
 * Alumni module dashboard widget
 *
 * Displays active/pending profile counts, total connections,
 * and recent alumni registrations on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class AlumniModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The Alumni module object
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

        $table = $xoopsDB->prefix('alumni_profiles');

        // Status is ENUM('active', 'inactive', 'pending')
        $active = 0;
        $pending = 0;

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table` WHERE `status` = 'active'"
        );
        if ($result) {
            list($active) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table` WHERE `status` = 'pending'"
        );
        if ($result) {
            list($pending) = $xoopsDB->fetchRow($result);
        }

        // Total connections
        $connections = 0;
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('alumni_connections')
        );
        if ($result) {
            list($connections) = $xoopsDB->fetchRow($result);
        }

        // Recent alumni profiles
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `profile_id`, `first_name`, `last_name`, `status`,"
            . " `graduation_year`, `created`"
            . " FROM `$table` ORDER BY `created` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $name = trim($row['first_name'] . ' ' . $row['last_name']);
                $isActive = ($row['status'] === 'active');
                $recent[] = [
                    'title'        => htmlspecialchars($name ?: '(unnamed)', ENT_QUOTES, 'UTF-8'),
                    'date'         => $row['created'],
                    'author'       => $row['graduation_year'] ? 'Class of ' . htmlspecialchars($row['graduation_year'], ENT_QUOTES, 'UTF-8') : '',
                    'status'       => $row['status'],
                    'status_class' => $isActive ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'Alumni',
            'icon'      => '🎓',
            'stats'     => [
                'active'      => (int) $active,
                'pending'     => (int) $pending,
                'connections' => (int) $connections,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/alumni/admin/',
        ];
    }

    /**
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 50;
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
