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
 * Modern Theme Widget for Pedigree
 *
 * Dashboard statistics: published animals, species/breeds,
 * owners, and 5 most recently added animals.
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
 * Pedigree module dashboard widget
 *
 * Displays published/total animal counts, owner totals,
 * and recently added animals on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class PedigreeModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The Pedigree module object
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

        $table = $xoopsDB->prefix('pedigree_animals');

        $published = 0;
        $total = 0;
        $owners = 0;

        // Total published animals (not soft-deleted)
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table`"
            . " WHERE `is_published` = 1 AND (`is_deleted` = 0 OR `is_deleted` IS NULL)"
        );
        if ($result) {
            list($published) = $xoopsDB->fetchRow($result);
        }

        // Total animals (including unpublished, excluding deleted)
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table`"
            . " WHERE `is_deleted` = 0 OR `is_deleted` IS NULL"
        );
        if ($result) {
            list($total) = $xoopsDB->fetchRow($result);
        }

        // Distinct owners
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('pedigree_owners')
        );
        if ($result) {
            list($owners) = $xoopsDB->fetchRow($result);
        }

        // Recent animals
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `id`, `name`, `breed`, `species`, `is_published`, `created_at`"
            . " FROM `$table`"
            . " WHERE `is_deleted` = 0 OR `is_deleted` IS NULL"
            . " ORDER BY `created_at` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $breed = trim($row['breed'] . ($row['species'] ? ' (' . $row['species'] . ')' : ''));
                $recent[] = [
                    'title'        => $row['name'] ?: '(unnamed)',
                    'author'       => $breed ?: '',
                    'date'         => strtotime($row['created_at']),
                    'status'       => $row['is_published'] ? 'published' : 'draft',
                    'status_class' => $row['is_published'] ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'Pedigree',
            'icon'      => '🐾',
            'stats'     => [
                'published' => (int) $published,
                'total'     => (int) $total,
                'owners'    => (int) $owners,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/pedigree/admin/',
        ];
    }

    /**
     * Get widget display priority
     *
     * @return int Priority value (lower = shown first)
     */
    public function getWidgetPriority()
    {
        return 55;
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
