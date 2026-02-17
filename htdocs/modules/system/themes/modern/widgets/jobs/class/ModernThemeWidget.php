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
 * Modern Theme Widget for Jobs
 *
 * Dashboard statistics: active jobs, applications, companies,
 * and 5 most recent job postings.
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
 * Jobs module dashboard widget
 *
 * Displays active job listings, application and company counts,
 * and recent job postings on the admin dashboard.
 *
 * @category    Theme
 * @package     Modern Theme
 * @subpackage  Widgets
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link        https://xoops.org
 */
class JobsModernThemeWidget implements ModernThemeWidgetInterface
{
    /** @var \XoopsModule */
    private $module;

    /**
     * Constructor
     *
     * @param \XoopsModule $module The Jobs module object
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

        $table = $xoopsDB->prefix('jobs_jobs');

        $activeJobs = 0;
        $applications = 0;
        $companies = 0;

        // Active job listings (status = 'active' or 1)
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM `$table` WHERE `status` = 'active' OR `status` = 1"
        );
        if ($result) {
            list($activeJobs) = $xoopsDB->fetchRow($result);
        }

        // Total applications
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('jobs_applications')
        );
        if ($result) {
            list($applications) = $xoopsDB->fetchRow($result);
        }

        // Total companies
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('jobs_companies')
        );
        if ($result) {
            list($companies) = $xoopsDB->fetchRow($result);
        }

        // Recent job postings
        $recent = [];
        $result = $xoopsDB->query(
            "SELECT `job_id`, `title`, `created`, `status`, `location`"
            . " FROM `$table` ORDER BY `created` DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $isActive = ($row['status'] === 'active' || $row['status'] === '1');
                $recent[] = [
                    'title'        => $row['title'],
                    'date'         => $row['created'],
                    'author'       => $row['location'] ?: '',
                    'status'       => $isActive ? 'active' : 'closed',
                    'status_class' => $isActive ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title'     => 'Jobs',
            'icon'      => '💼',
            'stats'     => [
                'active_jobs'  => (int) $activeJobs,
                'applications' => (int) $applications,
                'companies'    => (int) $companies,
            ],
            'recent'    => $recent,
            'admin_url' => XOOPS_URL . '/modules/jobs/admin/',
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
