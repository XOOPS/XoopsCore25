<?php
/**
 * Modern Theme Widget for TDMDownloads
 *
 * Dashboard statistics: online downloads, awaiting approval, total hits,
 * categories, and 5 most recent downloads.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class TdmdownloadsModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

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

    public function getWidgetPriority()
    {
        return 45;
    }

    public function isWidgetEnabled()
    {
        return true;
    }
}
