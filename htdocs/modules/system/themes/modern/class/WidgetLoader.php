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

require_once __DIR__ . '/ModuleWidgetInterface.php';

/**
 * Widget Loader
 *
 * Dynamically loads widgets from installed modules that implement
 * the ModernThemeWidgetInterface. Discovers, instantiates, and
 * sorts module widgets by priority for dashboard rendering.
 *
 * @category   Theme
 * @package    Modern Theme
 * @subpackage Widgets
 * @since      1.0
 * @author     Mamba <mambax7@gmail.com>
 * @copyright  XOOPS Project (https://xoops.org)
 * @license    GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link       https://xoops.org
 */
class ModernThemeWidgetLoader
{
    private $widgets = [];
    private $xoopsDB;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $xoopsDB;
        $this->xoopsDB = $xoopsDB;
    }

    /**
     * Load all available widgets from installed modules
     *
     * Scans all active modules for a ModernThemeWidget class file,
     * instantiates each one, checks if it implements ModernThemeWidgetInterface
     * and is enabled, then collects and sorts widget data by priority.
     *
     * @return array Associative array of widget data keyed by module dirname,
     *               sorted by priority (lower number = higher priority)
     */
    public function loadWidgets()
    {
        /** @var XoopsModuleHandler $module_handler */
        $module_handler = xoops_getHandler('module');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('isactive', 1));

        $modules = $module_handler->getObjects($criteria);
        $modulesBase = realpath(XOOPS_ROOT_PATH . '/modules/');

        foreach ($modules as $module) {
            $dirname = $module->getVar('dirname');
            $widgetFile = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/ModernThemeWidget.php';

            // Validate resolved path is within the modules directory
            $realPath = realpath($widgetFile);
            if ($realPath && strpos($realPath, $modulesBase . DIRECTORY_SEPARATOR) === 0) {
                require_once $realPath;

                $className = ucfirst($dirname) . 'ModernThemeWidget';

                if (class_exists($className)) {
                    $widget = new $className($module);

                    if ($widget instanceof ModernThemeWidgetInterface && $widget->isWidgetEnabled()) {
                        $data = $widget->getWidgetData();
                        if (!empty($data) && is_array($data)) {
                            $data['priority'] = $widget->getWidgetPriority();
                            $data['module'] = $dirname;
                            $this->widgets[$dirname] = $data;
                        }
                    }
                }
            }
        }

        // Sort by priority (uasort preserves string keys)
        uasort($this->widgets, function($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        return $this->widgets;
    }

    /**
     * Get widget data for a specific module
     *
     * @param string $moduleName Module directory name
     * @return array|null Widget data or null if not found
     */
    public function getModuleWidget($moduleName)
    {
        foreach ($this->widgets as $widget) {
            if ($widget['module'] === $moduleName) {
                return $widget;
            }
        }

        return null;
    }

    /**
     * Get all loaded widgets
     *
     * @return array All widget data
     */
    public function getWidgets()
    {
        return $this->widgets;
    }
}
