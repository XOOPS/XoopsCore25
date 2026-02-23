# DebugBar Module - Complete File List for XOOPS 2.5.12

All files required to add the DebugBar + Ray debugging functionality
to a standard XOOPS 2.5.12 installation.

## Composer Dependency (added by this PR)

The following entry is added to `xoops_lib/composer.dist.json` under `"require"`:

```json
"maximebf/debugbar": "^1.22"
```

This PR adds the `maximebf/debugbar` library and its dependencies (`psr/log`,
`monolog/monolog`) to the XOOPS vendor directory. Running `composer install` in
the `xoops_lib/` directory ensures these packages are available. No additional
Composer packages are needed for the core DebugBar functionality.

Ray is entirely optional and installed separately by the user (see Part 3).

---

## Part 1: Module Files (NEW directory)

All files under `htdocs/modules/debugbar/` -- this is a new installable module.

```
modules/debugbar/
|
|-- xoops_version.php              Module definition, configs, admin menu registration
|-- index.php                      Security guard (returns 404)
|
|-- admin/
|   |-- index.php                  Admin home: status page (library check, asset check)
|   |-- menu.php                   Admin menu entries
|
|-- assets/
|   |-- debugbar.css               DebugBar v1.x core CSS (copied from vendor on install)
|   |-- debugbar.js                DebugBar v1.x core JS  (copied from vendor on install)
|   |-- openhandler.css            DebugBar open handler CSS (copied from vendor)
|   |-- openhandler.js             DebugBar open handler JS  (copied from vendor)
|   |-- widgets.css                DebugBar widget CSS (copied from vendor)
|   |-- widgets.js                 DebugBar widget JS  (copied from vendor)
|   |-- xoops-debugbar-settings.js XOOPS custom: Settings panel (theme, position, tabs)
|   |-- xoops-debugbar-settings.css XOOPS custom: Settings panel styling
|   |
|   |-- images/
|   |   |-- logoModule.png         Module logo for admin area
|   |
|   |-- vendor/
|       |-- font-awesome/
|       |   |-- css/
|       |       |-- font-awesome.min.css   Font Awesome 6.6.0 (icons for tabs)
|       |
|       |-- highlightjs/
|       |   |-- highlight.pack.js          Syntax highlighting for SQL queries
|       |   |-- styles/
|       |       |-- github.css             Highlight.js GitHub theme
|       |
|       |-- jquery/
|           |-- dist/
|               |-- jquery.min.js          jQuery (DebugBar v1.x dependency)
|
|       |-- widgets/
|           |-- mails/
|           |   |-- widget.css             Mail collector widget CSS
|           |   |-- widget.js              Mail collector widget JS
|           |
|           |-- sqlqueries/
|           |   |-- widget.css             SQL queries widget CSS
|           |   |-- widget.js              SQL queries widget JS
|           |
|           |-- templates/
|               |-- widget.css             Templates widget CSS
|               |-- widget.js              Templates widget JS
|
|-- class/
|   |-- DebugbarLogger.php         Core: DebugbarLogger class (collects + renders all data)
|   |-- RayLogger.php              Optional: RayLogger class (forwards data to Ray app)
|
|-- docs/
|   |-- ray-integration.md         Tutorial: Ray + DebugBar usage guide
|   |-- file-list.md               This file
|
|-- include/
|   |-- install.php                Install/update callback: copies vendor assets
|
|-- language/
|   |-- english/
|       |-- main.php               Runtime language constants (tab names, labels)
|       |-- modinfo.php            Module info language constants (config labels)
|
|-- preloads/
    |-- core.php                   Event hooks: wires everything into XOOPS lifecycle
    |-- index.php                  Security guard (returns 404)
```

### File count: 30 files

---

## Part 2: Smarty Plugins (added to EXISTING directory)

These 5 files are added to the existing `htdocs/class/smarty3_plugins/` directory.
They are auto-discovered by Smarty 3 based on filename convention.

```
class/smarty3_plugins/
|
|-- function.ray.php               <{ray value=$var label="..." color="..."}>
|-- function.ray_dump.php          <{ray_dump value=$obj label="..."}>
|-- function.ray_table.php         <{ray_table value=$arr label="..."}>
|-- function.ray_context.php       <{ray_context exclude="xoops_*"}>
|-- modifier.ray.php               <{$var|ray:"label"}>
```

### File count: 5 files

---

## Part 3: Optional User-Side Requirements

These are NOT part of the XOOPS distribution. They are installed independently
by developers who want Ray support.

### Ray Desktop App
- Download from https://myray.app/
- Available for Windows, macOS, Linux

### Ray PHP Library (one of these options)

**Option A -- Global Ray (recommended for multi-project setups):**
```bash
composer global require spatie/global-ray
```
Then add to `php.ini`:
```ini
auto_prepend_file = /path/to/composer/vendor/spatie/global-ray/src/scripts/global-ray-loader.php
```

**Option B -- Per-project:**
```bash
cd xoops_lib/
composer require --dev spatie/ray
```

---

## Summary

| Category | Files | New/Existing |
|----------|-------|--------------|
| Module: `modules/debugbar/` | 30 | NEW directory |
| Smarty plugins: `class/smarty3_plugins/` | 5 | Added to existing dir |
| Composer: `xoops_lib/composer.dist.json` | 1 | Updated (added `maximebf/debugbar`) |
| **Total new files** | **35** | |

### By function

| Function | Files | Required? |
|----------|-------|-----------|
| DebugBar core (browser toolbar) | 28 | Yes |
| Ray integration (desktop debugger) | 7 | Optional |
|   - `class/RayLogger.php` | 1 | Optional |
|   - 5 Smarty plugins | 5 | Optional |
|   - `docs/ray-integration.md` | 1 | Optional |

### What each piece provides

| Component                        | What it does |
|----------------------------------|-------------|
| `DebugbarLogger.php`             | Collects queries, blocks, timers, errors, Smarty vars, included files. Renders the browser toolbar. Detects duplicate/slow queries. |
| `RayLogger.php`                 | Mirrors all debug data to Ray desktop app with color-coded labels. Zero overhead if Ray is not installed. |
| `preloads/core.php`              | Wires both loggers into the XOOPS request lifecycle via 8 event hooks. |
| `xoops-debugbar-settings.js/css` | Settings panel: theme, toolbar position, hide empty tabs, auto-show. |
| `install.php`                    | Copies vendor DebugBar assets on module install/update. |
| Smarty plugins                   | Template-level debugging: send variables, dumps, tables, context to Ray from `.tpl` files. |

### Dependencies between files

```
preloads/core.php
    |-- loads --> class/DebugbarLogger.php
    |               |-- uses --> maximebf/debugbar (vendor, via Composer)
    |               |-- uses --> psr/log (vendor, via Composer)
    |               |-- renders --> assets/*.js, assets/*.css
    |               |-- renders --> assets/xoops-debugbar-settings.js/css
    |
    |-- loads --> class/RayLogger.php  (optional)
                    |-- calls --> ray() function (user-installed, optional)

class/smarty3_plugins/function.ray*.php
class/smarty3_plugins/modifier.ray.php
    |-- checks --> RayLogger::getInstance()->isEnabled()
    |-- calls --> ray() function (user-installed, optional)
```
