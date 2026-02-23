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
 * Modern Theme Widget for RealEstate
 *
 * Dashboard statistics: active listings by type (for sale, for rent),
 * sold/rented, and 5 most recent property listings.
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
 * RealEstate module dashboard widget
 *
 * Displays for-sale/for-rent/closed listing counts
 * and recent property listings on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class RealestateModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The RealEstate module object
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
                    'title'        => htmlspecialchars($row['title'] ?: $row['property_type'], ENT_QUOTES, 'UTF-8'),
                    'date'         => $row['created_at'],
                    'author'       => htmlspecialchars($location, ENT_QUOTES, 'UTF-8'),
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
