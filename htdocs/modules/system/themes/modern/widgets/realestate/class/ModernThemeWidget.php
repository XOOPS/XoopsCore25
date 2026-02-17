<?php
/**
 * Modern Theme Widget for RealEstate
 *
 * Dashboard statistics: active listings by type (for sale, for rent),
 * sold/rented, and 5 most recent property listings.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class RealestateModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function getWidgetData()
    {
        global $xoopsDB;

        $table = $xoopsDB->prefix('realestate_properties');

        // Status: for_sale, for_rent, sold, rented
        $forSale = 0;
        $forRent = 0;
        $closed = 0;

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table`"
            . " WHERE `status` = 'for_sale' AND `is_active` = 1"
        );
        if ($result) {
            list($forSale) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table`"
            . " WHERE `status` = 'for_rent' AND `is_active` = 1"
        );
        if ($result) {
            list($forRent) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table`"
            . " WHERE `status` IN ('sold', 'rented')"
        );
        if ($result) {
            list($closed) = $xoopsDB->fetchRow($result);
        }

        // Recent listings
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `property_id`, `title`, `status`, `property_type`,"
            . " `city`, `price`, `currency`, `created_at`"
            . " FROM `$table`"
            . " ORDER BY `created_at` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $isActive = ($row['status'] === 'for_sale' || $row['status'] === 'for_rent');
                $location = $row['city'] ?: '';
                $recent[] = [
                    'title'        => $row['title'] ?: $row['property_type'],
                    'date'         => $row['created_at'],
                    'author'       => $location,
                    'status'       => str_replace('_', ' ', $row['status']),
                    'status_class' => $isActive ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'Real Estate',
            'icon'      => '🏠',
            'stats'     => [
                'for_sale' => (int) $forSale,
                'for_rent' => (int) $forRent,
                'closed'   => (int) $closed,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/realestate/admin/',
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
