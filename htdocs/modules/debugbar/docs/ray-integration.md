# Ray + DebugBar Integration for XOOPS 2.5.12

## What Is This?

The XOOPS DebugBar module provides two complementary debugging tools:

1. **DebugBar** (always available) -- an in-browser toolbar that shows queries, timers, blocks, Smarty variables, errors, and included files directly at the bottom of every page.

2. **Ray** (optional) -- sends the same debug data PLUS template-level debug info to the [Ray desktop app](https://myray.app/), giving you a separate, searchable, color-coded debug window.

They work **independently**. You can use DebugBar without Ray. If Ray is installed, both run simultaneously -- DebugBar in the browser, Ray on your desktop.

---

## Part 1: Installing Ray

You have two options. Choose **one**.

### Option A: Global Ray (Recommended)

Install Ray once for ALL your PHP projects -- no per-project Composer changes needed.

```bash
composer global require spatie/global-ray
```

Then add this line to your `php.ini`:

```ini
auto_prepend_file = C:/Users/YourName/AppData/Roaming/Composer/vendor/spatie/global-ray/src/scripts/global-ray-loader.php
```

On Linux/Mac the path would be something like:

```ini
auto_prepend_file = /home/yourname/.composer/vendor/spatie/global-ray/src/scripts/global-ray-loader.php
```

Restart your web server (Apache/Nginx) after editing `php.ini`.

**Advantages:**
- Every PHP project on your machine gets Ray automatically
- No changes to any project's `composer.json`
- If a project has its own `spatie/ray` in `composer.json`, global-ray detects it and steps aside

### Option B: Per-Project Install

```bash
cd /path/to/xoops/htdocs/xoops_lib
composer require --dev spatie/ray
```

This only affects the current XOOPS installation.

### Download the Ray Desktop App

Both options require the Ray desktop app:

- Download from [https://myray.app/](https://myray.app/)
- Available for Windows, macOS, and Linux
- Free trial available; paid license for continued use

---

## Part 2: Module Configuration

Go to **System Admin > Modules > DebugBar > Settings** (or Preferences).

| Setting | Default | Description |
|---------|---------|-------------|
| Display DebugBar | Yes | Master switch for the in-browser toolbar |
| Enable Smarty Debug | Yes | Show Smarty template variables in DebugBar |
| Enable Included Files Tab | Yes | Show all PHP files loaded during the request |
| Slow Query Threshold | 0.05 | Queries slower than this (in seconds) are highlighted red |
| Enable Ray Integration | Yes | Send debug data to Ray desktop app |

**Important:** Setting "Enable Ray Integration" to **No** disables all Ray output -- both the automatic data feed AND the Smarty template plugins. This is the master off-switch.

---

## Part 3: What Ray Shows Automatically

Once Ray is installed and the Ray desktop app is running, you will automatically see:

### Queries (purple)
Every database query, with:
- Query number (`Query #1`, `Query #2`, ...)
- Execution time in milliseconds
- **Duplicate detection** -- repeated queries show `[DUP x3]` in orange
- **Slow queries** -- queries exceeding the threshold show in red with `SLOW` label

### Blocks (green/blue)
- Cached blocks show in **green** with cache duration
- Non-cached blocks show in **blue**

### Errors (red)
PHP errors, database errors, and exceptions.

### Deprecation Notices (orange)
Deprecated function calls and API usage warnings.

### Extra Debug Info (gray)
Any custom debug data added via `$xoopsLogger->log()`.

You don't need to write any code for this -- it happens automatically on every page load.

---

## Part 4: Smarty Template Plugins

These are the real power of the Ray integration. Drop them into any `.tpl`
template file to inspect data without disrupting the page output.

**Note:** XOOPS uses `<{` and `}>` as Smarty delimiters (not `{` and `}`).
All examples below use the XOOPS convention.

### `<{ray}>` -- Send a value to Ray

```smarty
<{* Send a simple message *}>
<{ray msg="Reached the user profile section"}>

<{* Send a variable with a label *}>
<{ray value=$user label="Current User" color="green"}>

<{* Send module config *}>
<{ray value=$xoops_module_header label="Module Header"}>
```

**Parameters:**
| Parameter | Required | Description |
|-----------|----------|-------------|
| `value` | No* | Variable to send to Ray |
| `msg` | No* | String message to send |
| `label` | No | Label displayed in Ray |
| `color` | No | `green`, `red`, `blue`, `orange`, `purple`, `gray` |

*At least one of `value` or `msg` is required.

### `<{ray_dump}>` -- Deep-inspect a variable

```smarty
<{* Full dump of a complex object *}>
<{ray_dump value=$xoopsUser label="XoopsUser Object"}>

<{* Inspect module config array *}>
<{ray_dump value=$xoopsModuleConfig label="Module Config"}>
```

Shows the complete structure -- nested arrays, object properties, types. Displays in purple in Ray.

### `<{ray_table}>` -- Show arrays as formatted tables

```smarty
<{* Show all block data as a table *}>
<{ray_table value=$block label="Block Data"}>

<{* List all users as a table *}>
<{ray_table value=$users label="Online Users"}>
```

Only works with arrays. Ray renders them as a clean, sortable table.

### `<{ray_context}>` -- Dump ALL template variables

```smarty
<{* See everything available at this point in the template *}>
<{ray_context}>

<{* With a label to identify where in the template you are *}>
<{ray_context label="Before User Loop"}>

<{* Filter out noisy variables *}>
<{ray_context label="Clean Context" exclude="xoops_*,smarty"}>
```

This is incredibly useful when you inherit a theme or module and want to know
what variables are available. Sends a sorted table to Ray with variable names,
types, and truncated values.

**Parameters:**
| Parameter | Required | Description |
|-----------|----------|-------------|
| `label` | No | Label in Ray (default: "Template Context") |
| `exclude` | No | Comma-separated names/prefixes to hide. Use `*` for prefix matching: `xoops_*` hides all variables starting with `xoops_` |

### `|ray` -- Inline pass-through modifier

```smarty
<{* Debug a value without changing the output *}>
<h1><{$user.name|ray:"Username"}></h1>

<{* The value is sent to Ray AND still renders in the template *}>
<span class="<{$blockClass|ray:'Block CSS Class'}>">content</span>

<{* Chain with other modifiers *}>
<{$content|strip_tags|truncate:200|ray:"Truncated Content"}>
```

The `|ray` modifier is unique -- it sends the value to Ray and then **passes
it through unchanged**. The template output is not affected. Perfect for quick
inline debugging.

---

## Part 5: Practical Examples

### Example 1: Debugging a module template

You're working on `modules/mymodule/templates/item_list.tpl` and items aren't showing:

```smarty
<{* Add this at the top of the template to see what's available *}>
<{ray_context label="item_list.tpl top" exclude="smarty"}>

<{* Check if items array exists and what's in it *}>
<{ray_dump value=$items label="Items Array"}>

<{foreach item=item from=$items}>
    <{* See each item as it loops *}>
    <{ray value=$item label="Loop Item" color="blue"}>
    <div><{$item.title|ray:"Item Title"}></div>
<{/foreach}>
```

### Example 2: Tracking block rendering

```smarty
<{* In a theme's theme.tpl, check what block data looks like *}>
<{foreach item=block from=$xoops_lblocks}>
    <{ray_table value=$block label="Left Block"}>
    <div class="block"><{$block.content}></div>
<{/foreach}>
```

### Example 3: Finding slow queries

No code needed. Just:
1. Open Ray desktop app
2. Load a page in XOOPS
3. Look for **orange** entries (duplicates) and **red** entries (slow queries)
4. Each shows the full SQL, execution time, and duplicate count

### Example 4: Understanding theme variables

Drop this into any `theme.tpl` to see everything the theme engine provides:

```smarty
<{ray_context label="Theme Variables" exclude="smarty"}>
```

---

## Part 6: How It Works Under the Hood

```
Browser Request
    |
    v
[XoopsLogger] ---- dispatches log() calls ----> [DebugbarLogger] --> Browser Toolbar
    |                                        |
    |                                        +-> [RayLogger] -------> Ray Desktop App
    |
    v
[Smarty Templates]
    |
    +-- <{ray}> / <{ray_dump}> / <{ray_table}> / <{ray_context}> / |ray
    |       |
    |       +-- checks: function_exists('ray') AND RayLogger enabled?
    |               |
    |               +-- Yes --> sends to Ray Desktop App
    |               +-- No  --> silently does nothing
    |
    v
[Browser Output] <---- DebugBar toolbar injected at bottom
```

**Key design principle:** Everything degrades gracefully.

| Scenario | DebugBar | Ray | Smarty plugins |
|----------|----------|-----|----------------|
| Ray not installed | Works | Nothing | Silent no-op |
| Ray installed, app not running | Works | Calls timeout (~2s) | Calls timeout |
| Ray installed, app running | Works | Full output | Full output |
| `ray_enable = No` in settings | Works | Disabled | Silent no-op |
| Non-admin user | Hidden | Disabled | Silent no-op |

**Warning about Ray desktop app not running:** When Ray is installed (globally
or per-project), the `ray()` function exists and will attempt to connect to
the desktop app on `localhost:23517`. If the app is not running, each call
incurs a ~2 second connection timeout. With many queries and Smarty plugins on
a page, this can add significant delay. **Always open the Ray app before
browsing, or set `ray_enable = No` in module settings when you're not
actively using it.**

---

## Part 7: Tips & Best Practices

1. **Use `<{ray_context}>` first** -- When working on an unfamiliar template, this tells you everything you can work with.

2. **Leave `<{ray}>` calls in templates during development** -- They produce zero overhead when Ray is disabled or not installed. Remove them before committing to production.

3. **Use colors consistently** -- Pick a color scheme: green for success paths, red for error conditions, blue for data inspection, orange for warnings.

4. **Watch for duplicate queries** -- Orange entries in Ray often reveal N+1 query problems. If you see `[DUP x15]`, there's likely a loop that should be optimized.

5. **Use the `|ray` modifier for quick checks** -- It's the fastest way to inspect a value without adding extra lines to your template: `<{$myVar|ray:"check"}>`.

6. **Set a meaningful slow query threshold** -- The default 0.05s (50ms) is reasonable for development. Adjust based on your database and dataset size.

7. **Toggle via module settings, not php.ini** -- Use the `Enable Ray Integration` setting in module preferences. Don't remove global-ray from php.ini just to disable it for XOOPS.

---

## Quick Reference Card

| Plugin | Purpose | Example |
|--------|---------|---------|
| `<{ray}>` | Send value/message | `<{ray value=$user label="User" color="green"}>` |
| `<{ray_dump}>` | Deep variable dump | `<{ray_dump value=$config label="Config"}>` |
| `<{ray_table}>` | Array as table | `<{ray_table value=$items label="Items"}>` |
| `<{ray_context}>` | All template vars | `<{ray_context exclude="xoops_*"}>` |
| `\|ray` | Inline pass-through | `<{$name\|ray:"Debug"}>` |
