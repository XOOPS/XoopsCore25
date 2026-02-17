<?php
/**
 * Modern Theme Widget for Alumni
 *
 * Dashboard statistics: active profiles, pending profiles,
 * total connections, and 5 most recent alumni registrations.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class AlumniModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

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
                    'title'        => $name ?: '(unnamed)',
                    'date'         => $row['created'],
                    'author'       => $row['graduation_year'] ? 'Class of ' . $row['graduation_year'] : '',
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

    public function getWidgetPriority()
    {
        return 50;
    }

    public function isWidgetEnabled()
    {
        return true;
    }
}
