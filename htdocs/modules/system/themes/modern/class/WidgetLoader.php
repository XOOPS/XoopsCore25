<?php
/**
 * Widget Loader
 *
 * Dynamically loads widgets from installed modules
 *
 * @package    Modern Theme
 * @subpackage Widgets
 * @since      1.0
 * @author     Mamba <mambax7@gmail.com>
 */

require_once __DIR__ . '/ModuleWidgetInterface.php';

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
     * @return array Array of widget data
     */
    public function loadWidgets()
    {
        $module_handler = xoops_getHandler('module');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('isactive', 1));

        $modules = $module_handler->getObjects($criteria);

        foreach ($modules as $module) {
            $dirname = $module->getVar('dirname');
            $widgetFile = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/ModernThemeWidget.php';

            if (file_exists($widgetFile)) {
                require_once $widgetFile;

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
