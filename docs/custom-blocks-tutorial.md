# Custom PHP Blocks — Migration Guide & Tutorial

## What Changed

XOOPS 2.5.12 replaces the legacy `eval()`-based PHP blocks with a **file-based
callback** system. Custom PHP blocks now reference a PHP file and function name
instead of storing raw PHP code in the database.

**Why?** Storing executable PHP in the database and running it via `eval()` is a
critical security risk — any database compromise (SQL injection, stolen backup,
rogue admin) gives an attacker direct server-side code execution.

The new system works exactly like module blocks: your PHP code lives in a file on
disk, and the block references the file + function. No `eval()` is involved.

### Backward Compatibility

Legacy PHP blocks (raw code stored in `content`) still work **if** you set
`XOOPS_ALLOW_PHP_BLOCKS` to `true` in `mainfile.php`. Without that constant,
legacy blocks add a deprecation entry to the XOOPS debug log and produce no output.
New file-based blocks work without any special constant.

---

## Quick Start

### 1. Create Your Block File

Create a PHP file in `htdocs/custom_blocks/`:

```php
<?php
// File: htdocs/custom_blocks/my_block.php

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Display my custom block
 *
 * @return string HTML output
 */
function b_custom_my_block_show()
{
    return '<p>Hello from my custom block!</p>';
}
```

### 2. Register it in the Admin Panel

1. Go to **System Admin → Blocks → Add New Block**
2. Set **Content Type** to **PHP Script (file-based)**
3. In the **Content** field, enter:
   ```text
   my_block.php|b_custom_my_block_show
   ```
4. Set title, position, visibility, and groups as needed
5. Click **Submit**

That's it! Your block is now live.

---

## Content Field Format

The content field for PHP blocks uses this format:

```text
filename.php|function_name
```

- **filename.php** — The PHP file in `htdocs/custom_blocks/` (letters, numbers,
  hyphens, underscores, and `.php` extension only)
- **function_name** — The function to call (must return an HTML string)

The pipe `|` character separates the two parts.

### Naming Conventions

We recommend the following naming conventions (not enforced, but consistent):

| Item | Convention | Example |
|------|-----------|---------|
| File name | `my_feature.php` | `site_stats.php` |
| Function name | `b_custom_FEATURE_show` | `b_custom_site_stats_show` |

The `b_custom_` prefix avoids collisions with module block functions.

---

## Examples

Three working examples are included in `htdocs/custom_blocks/`:

### Example 1: Welcome Message (`example_welcome.php`)

A simple block that greets logged-in users by name and shows a registration
link for guests.

**Content field value:**
```text
example_welcome.php|b_custom_welcome_show
```

**What it demonstrates:**
- Accessing `$xoopsUser` (the logged-in user object)
- Accessing `$xoopsConfig` (site configuration)
- Conditional output for logged-in vs. guest users
- Proper HTML escaping with `htmlspecialchars()`

**Key code:**
```php
function b_custom_welcome_show()
{
    global $xoopsUser, $xoopsConfig;

    if (is_object($xoopsUser)) {
        $uname = htmlspecialchars($xoopsUser->getVar('uname'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return '<p>Welcome back, <strong>' . $uname . '</strong>!</p>';
    }

    $sitename = htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return '<p>Welcome to <strong>' . $sitename . '</strong>!</p>';
}
```

---

### Example 2: Recent Members with Avatars (`example_recent_members.php`)

Displays the 5 most recently registered members with their avatars.

**Content field value:**
```text
example_recent_members.php|b_custom_recent_members_show
```

**What it demonstrates:**
- Using XOOPS handlers (`xoops_getHandler('member')`)
- Building queries with `CriteriaCompo` and `Criteria`
- Iterating over `XoopsUser` objects
- Generating links to user profiles

**Key code:**
```php
function b_custom_recent_members_show()
{
    $member_handler = xoops_getHandler('member');

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('level', 0, '>'));
    $criteria->setSort('user_regdate');
    $criteria->setOrder('DESC');
    $criteria->setLimit(5);

    $users = $member_handler->getUsers($criteria);
    // ... build HTML from $users array ...
}
```

---

### Example 3: Site Statistics (`example_site_stats.php`)

Shows total members, total posts, and the newest member name.

**Content field value:**
```text
example_site_stats.php|b_custom_site_stats_show
```

**What it demonstrates:**
- Using multiple XOOPS handlers (`member`, `comment`)
- Building queries with `Criteria`
- Handler-based `getCount()` instead of raw SQL
- Number formatting for display

**Key code:**
```php
function b_custom_site_stats_show()
{
    $member_handler = xoops_getHandler('member');
    $totalUsers = $member_handler->getUserCount(new Criteria('level', 0, '>'));

    $comment_handler = xoops_getHandler('comment');
    $totalPosts = $comment_handler->getCount();
    // ... format as HTML table ...
}
```

---

## Migrating Existing PHP Blocks

If you have existing PHP blocks that store raw code in the database, follow
these steps to migrate them:

### Step 1: Extract the PHP Code

Go to **System Admin → Blocks**, find your PHP block, and click Edit.
Copy the PHP code from the Content field.

### Step 2: Create a Block File

Create a new file in `htdocs/custom_blocks/`, e.g., `my_old_block.php`:

```php
<?php
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

function b_custom_my_old_block_show()
{
    // Paste your old PHP code here, but instead of echoing,
    // build and return an HTML string.

    $html = '';
    // ... your logic here ...
    $html .= '<p>Block output</p>';

    return $html;
}
```

**Important differences from legacy PHP blocks:**

| Legacy (eval) | New (file-based) |
|--------------|-----------------|
| Code runs in `eval()` context | Code runs as a normal PHP function |
| Output via `echo` / `print` | **Return** the HTML string (echoed output is also captured) |
| Code stored in database | Code stored in a file on disk |
| No function wrapper | Must be inside a named function |
| Requires `XOOPS_ALLOW_PHP_BLOCKS` | Works without any special constant |

### Step 3: Update the Block

Edit the block in admin, and replace the PHP code in the Content field with:
```text
my_old_block.php|b_custom_my_old_block_show
```

### Step 4: Test

View a page where the block should appear. If the block shows nothing, check
the XOOPS debug log for warnings about missing files or functions.

### Step 5: Remove Legacy Flag

Once all PHP blocks are migrated, you can remove `XOOPS_ALLOW_PHP_BLOCKS`
from `mainfile.php` (or set it to `false`). This eliminates the last
`eval()` path in the block system.

---

## Available XOOPS APIs in Block Functions

Your block functions run in the normal XOOPS context with full access to:

| API | How to access | Use for |
|-----|---------------|---------|
| Current user | `global $xoopsUser` | User info, permissions |
| Site config | `global $xoopsConfig` | Site name, language, etc. |
| Database | `XoopsDatabaseFactory::getDatabaseConnection()` | Direct SQL queries |
| Handlers | `xoops_getHandler('name')` | ORM-style data access |
| Module handlers | `xoops_getModuleHandler('name', 'dirname')` | Module-specific data |
| Text sanitizer | `\MyTextSanitizer::getInstance()` | Safe HTML formatting |
| Request data | `\Xmf\Request::getString('key', '', 'GET')` | GET/POST parameters |

### Security Reminders

- Always escape output with `htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8')`
- Use `$db->quote()` or prepared statements for SQL values
- Use `\Xmf\Request` instead of raw `$_GET`/`$_POST`
- Never include user input in file paths

---

## Troubleshooting

**Block shows nothing (no error):**
- Check that the content field matches the format `filename.php|function_name` exactly
- Verify the file exists in `htdocs/custom_blocks/`
- Enable XOOPS debug mode to see warnings in the log

**"PHP block file not found" warning:**
- The filename in the content field doesn't match an actual file in `custom_blocks/`
- Check for typos and ensure the `.php` extension is included

**"PHP block function not found" warning:**
- The function name in the content field doesn't match a function in the file
- Make sure the function is defined at the top level (not inside a class)

**Legacy block shows "Migrate to file-based format" warning:**
- This block still contains raw PHP code in the database
- Either migrate it (see above) or set `XOOPS_ALLOW_PHP_BLOCKS = true` in
  `mainfile.php` for temporary backward compatibility

**Block shows "content contains | but did not match file-based format":**
- The content looks like it could be a file-based reference (contains `|`) but
  doesn't match the required `filename.php|function_name` format
- Check for typos in the content field — the function name must be a valid PHP
  identifier (letters, digits, underscores; cannot start with a digit)
- If this is legacy PHP code that legitimately uses `|` (string literals,
  bitwise OR), set `XOOPS_ALLOW_PHP_BLOCKS = true` to enable legacy execution
