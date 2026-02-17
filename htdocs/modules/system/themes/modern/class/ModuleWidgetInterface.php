<?php
/**
 * Module Widget Interface
 *
 * Implement this interface in your module to provide custom dashboard widgets
 * for the Modern Admin Theme
 *
 * @package    Modern Theme
 * @subpackage Widgets
 * @since      1.0
 * @author     Mamba <mambax7@gmail.com>
 */

interface ModernThemeWidgetInterface
{
    /**
     * Get widget data for the dashboard
     *
     * This method should return an array with widget information
     * that will be displayed on the admin dashboard
     *
     * @return array Widget data structure
     *
     * Example return format:
     * [
     *     'title' => 'My Module Widget',
     *     'icon' => '📦',  // Emoji or FontAwesome class
     *     'stats' => [
     *         'total' => 100,
     *         'pending' => 5,
     *         'today' => 12
     *     ],
     *     'recent' => [
     *         ['title' => 'Item 1', 'date' => time(), 'author' => 'User'],
     *         ['title' => 'Item 2', 'date' => time(), 'author' => 'Admin']
     *     ],
     *     'actions' => [
     *         ['label' => 'Manage', 'url' => 'admin/index.php'],
     *         ['label' => 'Settings', 'url' => 'admin/settings.php']
     *     ]
     * ]
     */
    public function getWidgetData();

    /**
     * Get widget priority (lower number = higher priority)
     *
     * @return int Priority (0-100, default 50)
     */
    public function getWidgetPriority();

    /**
     * Check if widget should be displayed
     *
     * @return bool True if widget should be shown
     */
    public function isWidgetEnabled();
}
