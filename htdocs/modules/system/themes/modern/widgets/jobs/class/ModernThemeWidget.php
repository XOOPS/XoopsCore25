<?php
/**
 * Modern Theme Widget for Jobs
 *
 * Dashboard statistics: active jobs, applications, companies,
 * and 5 most recent job postings.
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class JobsModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

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

    public function getWidgetPriority()
    {
        return 45;
    }

    public function isWidgetEnabled()
    {
        return true;
    }
}
