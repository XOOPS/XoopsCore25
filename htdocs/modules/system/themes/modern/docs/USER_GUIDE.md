# Modern Admin Theme — User Guide

![XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)

A practical guide for XOOPS site administrators who want to get the most out of the Modern admin theme.

## Table of Contents

- [Getting Started](#getting-started)
- [The Dashboard](#the-dashboard)
- [Dark Mode](#dark-mode)
- [The Sidebar](#the-sidebar)
- [The Customizer Panel](#the-customizer-panel)
- [Changing Colors](#changing-colors)
- [Showing and Hiding Dashboard Sections](#showing-and-hiding-dashboard-sections)
- [Content Tracking](#content-tracking)
- [Layout Options](#layout-options)
- [Module Admin Pages](#module-admin-pages)
- [Module Widgets](#module-widgets)
- [System Information](#system-information)
- [Help Sections](#help-sections)
- [Warning and Error Messages](#warning-and-error-messages)
- [Keyboard and Mouse Tips](#keyboard-and-mouse-tips)
- [Resetting Everything](#resetting-everything)
- [Frequently Asked Questions](#frequently-asked-questions)

---

## Getting Started

### Activating the Theme

1. Log in to your XOOPS admin panel
2. Go to **System Admin** > **Preferences** > **General Settings**
3. Find the **Admin GUI** dropdown and select **modern**
4. Click **Submit**
5. Go to **System Admin** > **Maintenance** and clear all caches

You should now see the new admin interface with a sidebar on the left, a header bar at the top, and a dashboard with charts and statistics.

### First Things to Know

- All your preferences (colors, layout, dark mode) are saved in browser cookies — they persist across sessions but are specific to each browser
- The floating gear icon in the bottom-right corner opens the Customizer panel where most settings live
- The hamburger icon (top-left) toggles the sidebar between full and compact modes

---

## The Dashboard

The dashboard is the first screen you see when you visit `admin.php`. It contains four sections:

### KPI Cards

Four summary cards across the top:

| Card | Shows |
|------|-------|
| **Total Users** | Total registered users + how many joined in the last 30 days |
| **Active Modules** | Number of active modules + how many are inactive |
| **Active Users** | Users who logged in during the last 30 days |
| **Server Load** | Current server load average (or "N/A" on Windows) |

### Charts

Three interactive charts powered by Chart.js:

- **User Registrations** — A line chart showing new user sign-ups over the last 6 months
- **User Groups** — A doughnut chart showing how users are distributed across groups
- **Content Distribution** — A bar chart showing content counts per module (configurable — see [Content Tracking](#content-tracking))

Hover over any chart element to see exact numbers in a tooltip.

### Module Widgets

Cards from installed modules showing their own statistics and recent items. For example, a Publisher widget might show article counts and the 5 most recent submissions. See [Module Widgets](#module-widgets) for details.

### System Information

A collapsible section at the bottom showing PHP version, MySQL version, Smarty version, server configuration, and Composer package versions. Click the section header to expand or collapse it.

---

## Dark Mode

Toggle dark mode by clicking the **moon icon** (🌙) in the top-right corner of the header bar. Click the **sun icon** (☀️) to switch back to light mode.

Dark mode affects everything: the sidebar, header, cards, charts, tables, forms, and all module admin pages. Your preference is saved in a cookie and restored automatically on your next visit.

Charts automatically adjust their colors (text labels, grid lines, legend) when you switch between light and dark mode.

---

## The Sidebar

The left sidebar is your primary navigation. It has three sections:

### Control Panel

Always visible. Contains:
- **Home** — Link back to the site frontend
- **Dashboard** — Link to the admin dashboard (`admin.php`)
- **Logout** — Sign out

### Modules

A collapsible list of all active modules with admin panels. Click any module to go to its admin area. Click the section header to collapse or expand the list.

### System

Only visible on system admin pages. Lists all system services (Users, Groups, Blocks, Preferences, etc.) with their icons.

### Sidebar Collapse

- **On desktop (> 1024px):** Click the hamburger icon (☰) in the header to toggle the sidebar between full width (260px with labels) and compact mode (80px with icons only). The main content area adjusts automatically.
- **On tablet/mobile (≤ 1024px):** The sidebar is hidden by default. Click the hamburger icon to slide it in as an overlay. Click outside the sidebar to close it.

---

## The Customizer Panel

Click the **floating gear icon** (⚙️) in the bottom-right corner to open the Customizer panel. It slides in from the right side of the screen.

The panel contains all the settings described in the sections below. Close it by clicking the **X** button at the top, the **Close** button at the bottom, or by clicking the dimmed overlay behind the panel.

On mobile devices, the panel expands to full width for easier use.

---

## Changing Colors

The Customizer panel offers **6 color presets** that change the theme's accent color throughout the entire interface — buttons, links, sidebar highlights, chart accents, and more:

| Preset | Color | Best For |
|--------|-------|----------|
| **Default** | Blue (`#2563eb`) | Professional, neutral |
| **Nature** | Green (`#10b981`) | Environmental, health |
| **Royal** | Purple (`#8b5cf6`) | Creative, education |
| **Sunset** | Orange (`#f59e0b`) | Energy, warmth |
| **Ocean** | Teal (`#14b8a6`) | Technology, calm |
| **Cherry** | Red (`#ef4444`) | Bold, attention |

Click any color swatch to apply it instantly. The change is saved in a cookie and persists across page loads.

### Custom Colors via CSS

If the 6 presets aren't enough, you can set any color by editing `css/modern.css`. Find the `:root` block at the top and change these three variables:

```css
:root {
    --primary: #2563eb;       /* Main accent color */
    --primary-dark: #1e40af;  /* Hover/active states */
    --primary-light: #3b82f6; /* Light accent */
}
```

You can also change semantic colors:

```css
:root {
    --success: #10b981;  /* Success badges and indicators */
    --warning: #f59e0b;  /* Warning messages */
    --danger: #ef4444;   /* Error messages and alerts */
    --info: #06b6d4;     /* Info badges */
}
```

After editing the CSS file, clear your browser cache to see the changes.

---

## Showing and Hiding Dashboard Sections

In the Customizer panel under **Dashboard Sections**, you can toggle each dashboard area on or off:

| Toggle | Controls |
|--------|----------|
| **KPI Cards** | The four summary cards at the top |
| **Charts** | All three charts (registrations, groups, content) |
| **Module Widgets** | The grid of module-specific widget cards |
| **System Info** | The collapsible system information table |

Unchecking a toggle hides that section immediately. Your choices are saved and restored on future visits.

This is useful if you want a minimal dashboard — for example, showing only KPI cards and widgets, without charts.

---

## Content Tracking

The **Content Distribution** bar chart shows how much content each module contains (articles, downloads, forum posts, etc.).

In the Customizer panel under **Content Tracking**, you'll see a checkbox for each module that has content. Uncheck a module to remove it from the chart. Check it to add it back. Changes take effect immediately — the chart rebuilds live without a page reload.

Your selection is saved in a cookie. This is useful when you have many modules but only want to track a few key ones.

**Supported modules** (detected automatically if installed):

Publisher, News, TDMDownloads, Jobs, XBlog, Alumni, Pedigree, RealEstate, NewBB, MyDownloads, MyLinks, Articles

---

## Layout Options

The Customizer panel has two layout sections:

### Sidebar Options

| Option | Effect |
|--------|--------|
| **Compact Sidebar** | Collapses the sidebar to icon-only mode (80px wide). Labels are hidden, only icons show. Hover over an icon to see the tooltip. |
| **Show Icons** | Toggles the emoji icons in the sidebar navigation. Uncheck to show text-only navigation. |

### Display Options

| Option | Effect |
|--------|--------|
| **Animations** | Enables/disables all CSS transitions and animations. Turn off for a snappier, instant-response feel, or if animations cause issues on slower hardware. |
| **Compact View** | Reduces padding on cards, tables, and navigation items. Fits more content on screen — useful for small monitors or when you want a denser layout. |

---

## Module Admin Pages

When you navigate to a module's admin area (e.g., Publisher, Protector, NewBB), the theme adapts:

### Module Toolbar

The module name appears at the top of the content area along with the module's admin icons (if the module provides them).

### Admin Links Bar

Below the module name, you'll see links generated by XOOPS core:

**Preferences** | **Update** | **Blocks** | **Templates** | **Comments** | **Uninstall** | **Go to module**

These are styled as a tab bar. Click any link to manage that aspect of the module.

### Tab Navigation

If the module has its own tab navigation, the tabs appear with a bottom accent bar. The currently active tab has a colored indicator.

### System Services in Header

Regardless of which module you're in, the header toolbar always shows the system service icons (Users, Groups, Blocks, etc.) so you can quickly jump to any system admin function.

---

## Module Widgets

Modules can provide dashboard widgets that display their statistics and recent activity directly on the admin homepage.

### What Widgets Show

Each widget card can contain:
- **Title and icon** — The module name with an emoji identifier
- **Statistics** — Key numbers (total items, pending, active, etc.) displayed as large counters
- **Recent items** — A list of the latest content with titles, authors, and status badges
- **"View All" link** — A direct link to the module's admin area

### Enabling Widgets

The theme ships with pre-built widgets for 11 modules. To activate a widget:

1. Find the widget file in `modules/system/themes/modern/widgets/{modulename}/class/ModernThemeWidget.php`
2. Copy `ModernThemeWidget.php` to `modules/{modulename}/class/ModernThemeWidget.php`
3. Clear the Smarty cache

For example, to enable the Publisher widget:

```text
Copy from: modules/system/themes/modern/widgets/publisher/class/ModernThemeWidget.php
Copy to:   modules/publisher/class/ModernThemeWidget.php
```

The widget appears automatically on the next dashboard load. No configuration needed.

### Available Pre-Built Widgets

| Module | Icon | Shows |
|--------|------|-------|
| Publisher | 📝 | Published/submitted articles, recent items |
| Jobs | 💼 | Active jobs, applications, companies |
| News | 📰 | Published/pending stories |
| NewBB | 💬 | Topics, posts, recent forum activity |
| Protector | 🛡️ | Blocked IPs, bad behavior events |
| TDMDownloads | 📥 | Downloads, categories, recent files |
| Alumni | 🎓 | Profiles, schools, recent graduates |
| Pedigree | 🐾 | Animals, breeds, recent entries |
| RealEstate | 🏠 | Properties, agents, recent listings |
| Vision2026 | 📊 | Articles, categories, recent content |
| XBlog | ✍️ | Posts, categories, recent blog entries |

### Hiding Widgets

To stop showing a widget, either:
- Delete the `ModernThemeWidget.php` file from the module's `class/` directory, or
- Toggle off **Module Widgets** in the Customizer to hide all widgets at once

---

## System Information

At the bottom of the dashboard, two collapsible sections provide server details:

### System Information Table

Click the header to expand. Shows:

| Field | Example |
|-------|---------|
| PHP | 8.2.15 |
| MySQL | 8.0.36 |
| Smarty | 3.1.47 |
| Server API | apache2handler |
| Operating System | Linux |
| Memory Limit | 256M |
| Upload Max Size | 64M |
| Max Execution Time | 30s |
| Post Max Size | 128M |
| File Uploads | On |

### Composer Packages

If XOOPS has Composer packages installed (in `xoops_lib/vendor/`), a second collapsible section lists all packages with their versions. This is useful for verifying that dependencies are correctly installed.

---

## Help Sections

Some XOOPS admin pages have built-in help content (blue "tips" boxes). In the Modern theme:

- Help sections are **hidden by default** to keep the interface clean
- Click the **Help** link (if visible) to show help content
- Click **Hide Help** to collapse it
- Your preference is saved — if you show help on one page, it stays visible across pages until you hide it again

---

## Warning and Error Messages

When XOOPS generates warning or error messages (e.g., "Please delete the install directory"), the Modern theme:

1. Moves the message to the top of the content area so you see it immediately
2. Slides it in with a smooth animation
3. **Auto-dismisses it after 5 seconds** with a slide-up animation

Messages are color-coded:
- **Red left border** — Errors (critical issues)
- **Orange left border** — Warnings (advisory)
- **Green left border** — Success confirmations
- **Blue left border** — Informational messages

---

## Keyboard and Mouse Tips

| Action | How |
|--------|-----|
| Toggle sidebar | Click the ☰ hamburger icon in the header |
| Toggle dark mode | Click the 🌙/☀️ icon in the header |
| Open customizer | Click the ⚙️ gear icon (bottom-right) |
| Close customizer | Click X, click Close, or click the overlay |
| Expand/collapse system info | Click the section header |
| Expand/collapse sidebar sections | Click the section header (Modules, System) |
| Navigate to module | Click its name in the sidebar |

---

## Resetting Everything

If you want to start fresh with all default settings:

1. Open the Customizer panel (gear icon)
2. Scroll to the bottom
3. Click **Reset to Defaults**
4. Confirm when prompted

This clears all preference cookies:
- Color scheme returns to blue
- All dashboard sections become visible
- Content tracking shows all modules
- Sidebar returns to full width with icons
- Animations are re-enabled
- Compact view is turned off
- Dark mode is turned off

The page reloads automatically after reset.

### Manual Reset

If the Customizer is inaccessible, clear these cookies manually in your browser's developer tools:

```text
xoops_color_scheme
xoops_dark_mode
xoops_show_kpis
xoops_show_charts
xoops_show_widgets
xoops_show_system_info
xoops_compact_sidebar
xoops_sidebar_icons
xoops_animations
xoops_compact_view
xoops_content_modules
xoops_help_visible
```

---

## Frequently Asked Questions

### The theme doesn't appear in the Admin GUI dropdown

Make sure the folder is named exactly `modern` and is located at `modules/system/themes/modern/`. Verify that `modern.php` exists inside it.

### Charts aren't showing

Chart.js loads automatically — either from a local file (`XOOPS_PATH/Frameworks/chartjs/chart.min.js`, served via `browse.php`) or from the jsDelivr CDN. If you're behind a firewall with no internet access, place `chart.min.js` in that location. Check the browser console (F12) for JavaScript errors.

### Dark mode doesn't persist

Cookies must be enabled in your browser. The cookie is named `xoops_dark_mode`. Some browser privacy extensions may block cookies on internal/localhost sites.

### Widgets don't appear on the dashboard

- The module must be installed and active
- The widget file must be at `modules/{dirname}/class/ModernThemeWidget.php`
- Clear the Smarty cache after adding a widget

### The sidebar overlaps content on mobile

The sidebar uses an overlay on screens ≤ 1024px wide. If it's stuck open, click outside the sidebar or click the hamburger icon again to close it.

### My custom CSS changes aren't showing

Clear both the browser cache and the XOOPS Smarty cache (System Admin > Maintenance).

### Can I use this theme on XOOPS 2.5.11?

The theme was designed for 2.5.12 but may work on 2.5.11. The widget system requires modules to have the `ModernThemeWidget.php` file, which is independent of XOOPS version.

### What browsers are supported?

The theme requires a modern browser: Chrome, Edge, Firefox, or Safari (latest 2 versions each). Mobile Chrome and Safari are also supported. **IE11 is not supported** — the theme relies on CSS custom properties which IE11 cannot process.

---

**Enjoy your modern XOOPS admin experience!**
