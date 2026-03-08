# XOOPS Core 2.5 — Unified Remediation Proposal (Revised)

**Date:** 2026-03-08 (updated after recent commits)
**Based on:** `security-critical-audit-2026-03-08.md` + `migration_audit_findings.md`
**Status:** Re-validated against current codebase — 9 items already fixed, 13 remain

---

## Executive Summary

Two independent audits identified **26 distinct issues**. After the initial proposal, several have been fixed in recent commits. This revised proposal reflects the **current state**: 9 items fixed, 4 deferred, and **13 items still requiring action**.

---

## Already Fixed (no action needed)

| Item | What was fixed | How |
|------|----------------|-----|
| 1A (5 of 6 files) | Password fields in `changepass.php`, `edituser.php`, `register.php`, `profile/register.php`, `system/admin/users/main.php` | Changed to `Request::getVar()` with `MASK_ALLOW_RAW \| MASK_NO_TRIM` flags |
| 2E partial | Null guards in `profile/admin/step.php`, `category.php`, `field.php` | Added `!is_object()` checks after `get()` calls |
| 4A | Smarty `{php}` tags in `system_imagemanager.tpl` and `system_imagemanager2.tpl` | Removed `{php}` blocks |
| 4C | Type safety for `$groups_failed` in SQL | Values already cast to `(int)` in loop |

---

## Phase 1: Critical — Fix Immediately

### 1A-remaining. Password field in `lostpass.php` still uses `getString()` [Migration Cat 1.5]

**File:** `htdocs/lostpass.php` (lines 84-85)

**Current code:**
```php
$pass  = Request::getString('pass', '', 'POST');
$vpass = Request::getString('vpass', '', 'POST');
```

**Fix:** Change both to `Request::getVar()` with raw flags, matching the pattern used in the other fixed files:
```php
$pass  = Request::getVar('pass', '', 'POST', 'string', Request::MASK_ALLOW_RAW | Request::MASK_NO_TRIM);
$vpass = Request::getVar('vpass', '', 'POST', 'string', Request::MASK_ALLOW_RAW | Request::MASK_NO_TRIM);
```

---

### 1B. Insecure password reset flow in Profile module [Security #1]

**Status: STILL EXISTS — CRITICAL**

The profile preload still redirects all password resets to the weak profile endpoint. The core `htdocs/lostpass.php` now has proper `XoopsTokenHandler` + `LostPassSecurity` with expiring one-time tokens, but it is never reached because the preload intercepts first.

**Files:**

| File | Current state |
|------|---------------|
| `htdocs/modules/profile/preloads/core.php` | `eventCoreLostpassStart()` still redirects to `./modules/profile/lostpass.php` |
| `htdocs/modules/profile/lostpass.php` | Still uses `substr(md5($user->getVar('pass')), 0, 5)` — 5-hex deterministic token |

**Fix (recommended — simplest):**
1. In `core.php`, change `eventCoreLostpassStart()` body to just `return;` — stops the redirect, lets the secure core flow run.
2. In `profile/lostpass.php`, add a redirect to `XOOPS_URL . '/lostpass.php'` at the top, so direct bookmarks still work.

---

### 1C. Missing null guards in system admin users [Migration Cat 4.1]

**Status: STILL EXISTS — all 3 instances**

**File:** `htdocs/modules/system/admin/users/main.php`

| Line | Code | Fix |
|------|------|-----|
| ~85 | `$user = $member_handler->getUser($uid);` then `$groups = $user->getGroups();` | Add `if (!is_object($user)) { redirect_header('admin.php?fct=users', 2, 'User not found'); }` |
| ~132 | Bulk delete loop: `$user = $member_handler->getUser($del); $groups = $user->getGroups();` | Add `if (!is_object($user)) { continue; }` |
| ~355 | `$obj = $member_handler->getUser($uid);` used outside `if` block — `$obj` may be undefined | Add `if (!is_object($obj)) { redirect_header('admin.php?fct=users', 2, 'User not found'); }` |

---

## Phase 2: High — Fix Soon

### 2A. Reflected XSS via `$_SERVER['PHP_SELF']` [Security #2]

**Status: STILL EXISTS**

**File:** `htdocs/class/xoopscomments.php` (lines 268, 444)

Both lines output raw `$_SERVER['PHP_SELF']` into HTML attributes without escaping.

**Fix:** Replace both with:
```php
htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES | ENT_HTML5)
```
Using `SCRIPT_NAME` instead of `PHP_SELF` is safer — it excludes PATH_INFO which is the attack vector.

---

### 2B. Raw `REQUEST_URI` in site-closed template [Security #8]

**Status: STILL EXISTS**

**File:** `htdocs/themes/default/modules/system/system_siteclosed.tpl` (line 52)

**Current:** `<{$smarty.server.REQUEST_URI}>`
**Fix:** `<{$smarty.server.REQUEST_URI|escape}>`

---

### 2C. Content/HTML fields using `getString()` [Migration Cat 2]

**Status: STILL EXISTS — all instances**

| File | Lines | Fields | Change |
|------|-------|--------|--------|
| `htdocs/include/comment_post.php` | 188 | `com_text` | `getString` → `getText` |
| `htdocs/edituser.php` | 92, 103 | `user_sig`, `bio` | `getString` → `getText` |
| `htdocs/modules/system/admin/users/main.php` | 206, 218, 302, 315 | `user_sig`, `bio` | `getString` → `getText` |

**Note:** `com_title` stays as `getString()` — titles should not contain HTML.

---

### 2D. CSRF: `ok` parameter not pinned to POST [Migration Cat 3.1]

**Status: STILL EXISTS**

**File:** `htdocs/modules/system/admin/users/main.php` (line 86)

**Current:** `Request::getInt('ok', 0)` (reads from REQUEST)
**Fix:** `Request::getInt('ok', 0, 'POST')`

---

### 2E-remaining. Missing null guard in `findusers.php` [Migration Cat 4.2]

**Status: STILL EXISTS** (the profile admin files are fixed, but findusers is not)

**File:** `htdocs/include/findusers.php` (lines 570-571)

**Current:**
```php
$rank_obj = $rank_handler->get(Request::getInt('rank', 0, 'POST'));
if ($rank_obj->getVar('rank_special')) {
```

**Fix:** Add null guard:
```php
$rank_obj = $rank_handler->get(Request::getInt('rank', 0, 'POST'));
if (is_object($rank_obj) && $rank_obj->getVar('rank_special')) {
```

---

## Phase 3: High — Security Hardening

### 3A. Remove `eval()` in file cache engine [Security #4]

**Status: STILL EXISTS**

**File:** `htdocs/class/cache/file.php` (line 222) — `$data = eval($data);`

**Fix:**
1. Change default `$this->settings['serialize']` to `true`.
2. Remove the `eval($data)` branch entirely, replace with `json_decode()`.
3. Clear cache directory on upgrade (existing cache files may contain eval-able strings).

---

### 3B. Restrict `eval()` for custom PHP blocks [Security #3]

**Status: STILL EXISTS — both files**

| File | Line |
|------|------|
| `htdocs/kernel/block.php` | 342: `echo eval($this->getVar('content', 'n'));` |
| `htdocs/modules/system/class/block.php` | 277: `echo eval($this->getVar('content', 'n'));` |

**Fix (pragmatic):**
1. Add constant `XOOPS_ALLOW_PHP_BLOCKS` (default `false`) in system config.
2. Guard the `eval()`: only execute if constant is explicitly `true`.
3. When blocked, display "PHP blocks disabled" notice + log warning.

---

### 3C. Unsafe `unserialize()` without `allowed_classes` [Security #6]

**Status: STILL EXISTS — all 5 instances**

| File | Line | Note |
|------|------|------|
| `htdocs/kernel/object.php` | 508 | `XOBJ_DTYPE_UNICODE_ARRAY` case |
| `htdocs/kernel/object.php` | 528 | `XOBJ_DTYPE_ARRAY` case |
| `htdocs/kernel/configitem.php` | 198 | Also uses `@` error suppression — remove that too |
| `htdocs/class/cache/model.php` | 163 | `return unserialize($data[0]);` |
| `htdocs/modules/profile/include/update.php` | 73 | `unserialize($myrow['field_options'])` |

**Fix:** All become `unserialize($data, ['allowed_classes' => false])`. Additionally, remove the `@` suppression in `configitem.php`.

---

## Phase 4: Medium — Compatibility

### 4B. PHP 8.2+ fatal in vendor demo file [Security #7]

**Status: STILL EXISTS**

**File:** `htdocs/xoops_lib/vendor/smottt/wideimage/demo/helpers/common.php` (line 24) — uses `__autoload()`.

**Fix:** Delete the `demo/` directory from deployed package. Demo code should not ship in production.

---

## Phase 5: Low Priority — Consistency

### 5A. Remaining raw superglobals in Protector module [Migration Cat 5]

**Status: STILL EXISTS — both files**

| File | Lines | Fix |
|------|-------|-----|
| `gtickets.php` | 185-190 | `$_POST['XOOPS_G_TICKET']` / `$_GET[...]` → `Request::getString(...)` |
| `postcommon_register_stopforumspam.php` | 29-38 | `$_POST['email']` etc → `Request::hasVar()` / `Request::getString()` |

---

## Deferred / No Action Needed

| Finding | Reason |
|---------|--------|
| Migration Cat 6.2 (profile/search.php `quoteString`) | Already correct |
| Migration Cat 7.1-7.3 (GET/POST source inconsistencies) | Acceptable — REQUEST is intentional |
| Protector superglobal modification (Cat 5.3 partial) | Intentional design |
| Security check `$_REQUEST` iteration (Cat 5.4) | Intentional design |

---

## Revised Implementation Summary

| Phase | Remaining Items | Files | Complexity |
|-------|-----------------|-------|------------|
| **Phase 1** — Critical | 1A (1 file), 1B (2 files), 1C (1 file) | 4 files | Low |
| **Phase 2** — High | 2A-2E (5 items) | 5 files | Low |
| **Phase 3** — Hardening | 3A-3C (3 items) | 6 files | Medium |
| **Phase 4** — Compatibility | 4B (1 item) | 1 file (delete) | Trivial |
| **Phase 5** — Consistency | 5A (2 files) | 2 files | Low |

**Total remaining: 13 fixes across ~16 files. 9 items already fixed, 4 deferred.**

Phases 1 and 2 are mechanical changes that can be done quickly. Phase 3 requires behavioral testing. Phase 4 is a single directory deletion.
