# Module Integration Guide for Modern Theme

## Overview

The Modern Admin Theme provides a **widget system** that allows XOOPS modules to display custom widgets on the admin dashboard. This guide shows you how to integrate your module with the Modern theme.

## Quick Start

### 1. Create Widget Class

Create a file in your module: `modules/yourmodule/class/ModernThemeWidget.php`

```php
<?php
/**
 * Modern Theme Widget for YourModule
 */

require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class YourmoduleModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function getWidgetData()
    {
        global $xoopsDB;

        $total = 0;
        $pending = 0;
        $recent = [];

        // Count total items (with null-safety)
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('yourmodule_items')
        );
        if ($result) {
            list($total) = $xoopsDB->fetchRow($result);
        }

        // Count pending items
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('yourmodule_items')
            . " WHERE status = 'pending'"
        );
        if ($result) {
            list($pending) = $xoopsDB->fetchRow($result);
        }

        // Get recent items (limit to 5)
        $result = $xoopsDB->query(
            "SELECT title, created FROM " . $xoopsDB->prefix('yourmodule_items')
            . " ORDER BY created DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $recent[] = [
                    'title' => $row['title'],
                    'date' => $row['created'],
                ];
            }
        }

        return [
            'title' => 'Your Module',
            'icon' => '📦',
            'stats' => [
                'total' => $total,
                'pending' => $pending,
                'today' => 0,
            ],
            'recent' => $recent,
            'admin_url' => XOOPS_URL . '/modules/yourmodule/admin/',
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
```

### 2. That's It!

Your widget will automatically appear on the Modern theme dashboard when:
1. Your module is installed and active
2. The Modern theme is the active admin GUI
3. The widget file exists at the path above
4. `getWidgetData()` returns a non-empty array

## Pre-Built Widgets

The theme ships with ready-to-use widgets for common XOOPS modules in the `widgets/` folder:

```
themes/modern/widgets/
├── alumni/class/ModernThemeWidget.php
├── jobs/class/ModernThemeWidget.php
├── newbb/class/ModernThemeWidget.php
├── news/class/ModernThemeWidget.php
├── pedigree/class/ModernThemeWidget.php
├── protector/class/ModernThemeWidget.php
├── publisher/class/ModernThemeWidget.php
├── realestate/class/ModernThemeWidget.php
├── tdmdownloads/class/ModernThemeWidget.php
├── vision2026/class/ModernThemeWidget.php
└── xblog/class/ModernThemeWidget.php
```

### Installation

To enable a pre-built widget, copy its `class/` folder into your module directory:

```
# Example: enable the Publisher widget
Copy:  themes/modern/widgets/publisher/class/ModernThemeWidget.php
  To:  modules/publisher/class/ModernThemeWidget.php
```

The WidgetLoader auto-discovers the file — no configuration needed. Just copy and reload the dashboard.

### Customizing a Pre-Built Widget

The copied file is yours to modify. Common customizations:
- Change the `'title'` or `'icon'` to match your branding
- Adjust `getWidgetPriority()` to reorder widgets on the dashboard
- Add/remove stat keys in the `'stats'` array
- Modify the SQL queries to filter by different criteria

## How It Works

The `WidgetLoader` scans all active modules for `class/ModernThemeWidget.php`. If found, it instantiates the class, checks `isWidgetEnabled()`, calls `getWidgetData()`, and stores the result keyed by module dirname. The widget template (`xo_widgets.tpl`) then iterates over all loaded widgets dynamically.

## Widget Data Structure

The `getWidgetData()` method must return an associative array. Return `false` or an empty array to skip rendering.

```php
[
    // Required
    'title' => 'Widget Title',           // Displayed in the widget header
    'icon' => '📦',                      // Emoji shown before the title

    // Recommended
    'stats' => [                         // Key-value pairs rendered as stat boxes
        'published' => 42,               // Key becomes the label (underscores → spaces)
        'pending' => 3,                  // "pending" → "Pending"
        'new_today' => 5,               // "new_today" → "New Today"
    ],
    'admin_url' => XOOPS_URL . '/modules/yourmod/admin/',  // "View All →" link

    // Optional
    'recent' => [                        // Recent items list (max 5 recommended)
        [
            'title' => 'Item name',      // Required
            'date' => 1234567890,        // Unix timestamp (optional)
            'author' => 'Username',      // Shown as "by Username" (optional)
            'status' => 'published',     // Status badge text (optional)
            'status_class' => 'success', // Badge color: success|warning (optional)
        ]
    ],
]
```

### Important: Stat Key Naming

The `stats` array keys are used as labels in the UI. The template applies `|replace:'_':' '|capitalize` to each key:

| Key | Displayed As |
|-----|-------------|
| `total` | Total |
| `pending` | Pending |
| `today` | Today |
| `new_today` | New Today |
| `total_posts` | Total Posts |

Choose short, descriptive keys that read well as labels.

### Null-Safety on Queries

Always check `$result` before calling `fetchRow()` or `fetchArray()`. If the module's table doesn't exist (e.g., module not fully installed), the query returns `false`:

```php
$result = $xoopsDB->query("SELECT COUNT(*) FROM " . $xoopsDB->prefix('mymod_items'));
if ($result) {
    list($count) = $xoopsDB->fetchRow($result);
} else {
    $count = 0;
}
```

If `getWidgetData()` returns `false` or a non-array value, the WidgetLoader skips it gracefully.

## Icon Options

Choose an appropriate emoji icon for your module:

| Module Type | Suggested Icons |
|-------------|-----------------|
| News/Articles | 📰 📝 📄 |
| Forums | 💬 💭 🗨️ |
| Downloads | 📥 📦 💾 |
| Gallery/Images | 🖼️ 📷 🎨 |
| Calendar/Events | 📅 🗓️ ⏰ |
| E-commerce | 🛒 💳 🏪 |
| Users/Profiles | 👥 👤 🙋 |
| Comments | 💭 💬 📢 |
| Messages/PM | ✉️ 📧 💌 |
| Statistics | 📊 📈 📉 |

## Priority System

Widget priority determines display order (lower number = higher priority):

```php
public function getWidgetPriority()
{
    return 10;   // High priority (shows first)
    return 30;   // Above normal
    return 50;   // Normal (default)
    return 70;   // Below normal
    return 90;   // Low priority (shows last)
}
```

Widgets with the same priority are ordered by module dirname.

## Conditional Display

Control when your widget appears:

```php
public function isWidgetEnabled()
{
    global $xoopsUser;

    // Only show to webmasters (group 1)
    if (!in_array(1, $xoopsUser->getGroups())) {
        return false;
    }

    // Only show if module has data
    global $xoopsDB;
    $result = $xoopsDB->query(
        "SELECT COUNT(*) FROM " . $xoopsDB->prefix('yourmod_items')
    );
    if (!$result) {
        return false;
    }
    list($count) = $xoopsDB->fetchRow($result);
    if ((int)$count === 0) {
        return false;
    }

    return true;
}
```

## Class Naming Convention

The class name **must** follow this pattern:

```
{Ucfirst_dirname}ModernThemeWidget
```

| Module dirname | Class name |
|---------------|------------|
| `news` | `NewsModernThemeWidget` |
| `newbb` | `NewbbModernThemeWidget` |
| `publisher` | `PublisherModernThemeWidget` |
| `tdmdownloads` | `TdmdownloadsModernThemeWidget` |

The WidgetLoader uses `ucfirst($dirname) . 'ModernThemeWidget'` to find the class.

## Complete Example: News Module

```php
<?php
require_once XOOPS_ROOT_PATH . '/modules/system/themes/modern/class/ModuleWidgetInterface.php';

class NewsModernThemeWidget implements ModernThemeWidgetInterface
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function getWidgetData()
    {
        global $xoopsDB;
        $prefix = $xoopsDB->prefix('news_stories');

        $published = 0;
        $pending = 0;
        $recent = [];

        $result = $xoopsDB->query("SELECT COUNT(*) FROM $prefix WHERE published > 0");
        if ($result) {
            list($published) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query("SELECT COUNT(*) FROM $prefix WHERE published = 0");
        if ($result) {
            list($pending) = $xoopsDB->fetchRow($result);
        }

        $result = $xoopsDB->query(
            "SELECT storyid, title, published FROM $prefix ORDER BY created DESC LIMIT 5"
        );
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $recent[] = [
                    'title' => $row['title'],
                    'date' => $row['published'],
                    'status' => $row['published'] > 0 ? 'published' : 'pending',
                    'status_class' => $row['published'] > 0 ? 'success' : 'warning',
                ];
            }
        }

        return [
            'title' => 'Recent News',
            'icon' => '📰',
            'stats' => [
                'published' => $published,
                'pending' => $pending,
                'today' => $this->getTodayCount(),
            ],
            'recent' => $recent,
            'admin_url' => XOOPS_URL . '/modules/news/admin/',
        ];
    }

    private function getTodayCount()
    {
        global $xoopsDB;
        $today = mktime(0, 0, 0);
        $result = $xoopsDB->query(
            "SELECT COUNT(*) FROM " . $xoopsDB->prefix('news_stories')
            . " WHERE created >= " . (int)$today
        );
        if ($result) {
            list($count) = $xoopsDB->fetchRow($result);
            return (int)$count;
        }
        return 0;
    }

    public function getWidgetPriority()
    {
        return 40;
    }

    public function isWidgetEnabled()
    {
        return true;
    }
}
```

## Testing Your Widget

1. **Enable your module** — Install and activate it in XOOPS admin
2. **Create the widget file** — `modules/{dirname}/class/ModernThemeWidget.php`
3. **Verify class name** — Must be `{Ucfirst_dirname}ModernThemeWidget`
4. **Activate Modern theme** — System Admin > Preferences > General Settings > Admin GUI
5. **Clear cache** — System Admin > Maintenance > Clear all caches
6. **Check dashboard** — Go to `admin.php` and look for your widget in the widgets grid

## Debugging

If your widget doesn't appear, add temporary logging:

```php
public function getWidgetData()
{
    error_log('[ModernWidget] ' . get_class($this) . ' loaded');

    $data = [/* ... your data ... */];

    error_log('[ModernWidget] data: ' . print_r($data, true));

    return $data;
}
```

Common issues:
- **Class not found** — Check that class name matches `ucfirst($dirname) . 'ModernThemeWidget'`
- **Empty widget** — `getWidgetData()` returned `false`, `null`, or an empty array
- **Table not found** — Query failed because module tables aren't installed; add `if ($result)` guards
- **Cached template** — Clear Smarty cache after changes

## Performance Tips

1. **Limit queries** — 2-3 COUNT queries + 1 recent items query is ideal
2. **Limit recent items** — Return 5 items max, not hundreds
3. **Use `isWidgetEnabled()`** — Skip the widget entirely when not needed
4. **Add table indexes** — Ensure commonly filtered columns are indexed
5. **Avoid joins** — Simple queries keep the dashboard fast

## Best Practices

- Always guard DB queries with `if ($result)` null checks
- Use `$xoopsDB->prefix()` for table names
- Keep `stats` keys short and descriptive (they become UI labels)
- Return `false` from `getWidgetData()` if data can't be loaded
- Don't leave `error_log()` calls in production code
- Test with the module both installed and uninstalled

## Support

- Email: mambax7@gmail.com
- XOOPS Forums: https://xoops.org/
- Theme README: [../README.md](../README.md)

---

**Make your module shine on the Modern theme dashboard!**
