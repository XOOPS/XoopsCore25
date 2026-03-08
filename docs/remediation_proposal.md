# XOOPS Core 2.5 — Security Audit Remediation Record

**Date:** 2026-03-08 (final)
**Based on:** `security-critical-audit-2026-03-08.md` + `migration_audit_findings.md`
**Status:** All 22 actionable items fixed. 4 items deferred as acceptable.

---

## Executive Summary

Two independent audits identified **26 distinct issues** across security, compatibility, and correctness categories. **22 have been fixed** across two rounds of commits. 4 items are deferred as intentional design or already correct.

---

## Fixed Items

### Phase 1: Critical (3 items, 10 instances)

| Item | What was fixed | How |
|------|----------------|-----|
| 1A (6 files: `lostpass.php`, `edituser.php`, `register.php`, `profile/changepass.php`, `profile/register.php`, `system/admin/users/main.php`) | Password fields using `getString()` | Changed to `Request::getVar()` with `MASK_ALLOW_RAW \| MASK_NO_TRIM` |
| 1B (2 files: `profile/preloads/core.php`, `profile/lostpass.php`) | Insecure profile lostpass (md5 5-char token) | Disabled preload redirect; `profile/lostpass.php` now redirects to secure core flow |
| 1C (1 file, 3 instances: `system/admin/users/main.php` lines ~85, ~132, ~355) | Missing null guards after `getUser()` in system admin | Added `!is_object()` guards with `_AM_SYSTEM_USERS_NO_SUCH_USER` redirect |

### Phase 2: High (5 items, 11 instances)

| Item | What was fixed | How |
|------|----------------|-----|
| 2A (1 file, 2 instances: `class/xoopscomments.php` lines 268, 444) | Reflected XSS via `$_SERVER['PHP_SELF']` | Replaced with `htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES \| ENT_HTML5, 'UTF-8')` |
| 2B (1 file: `themes/default/modules/system/system_siteclosed.tpl`) | Raw `REQUEST_URI` in site-closed template | Added `\|escape` Smarty modifier |
| 2C (3 files, 6 instances: `include/comment_post.php`, `edituser.php`, `system/admin/users/main.php`) | Content fields (`com_text`, `user_sig`, `bio`) using `getString()` | Changed to `getText()` pinned to `'POST'` |
| 2D (1 file: `system/admin/users/main.php`) | CSRF: `ok` parameter not pinned to POST | Changed to `Request::getInt('ok', 0, 'POST')` |
| 2E (4 files: `include/findusers.php`, `profile/admin/step.php`, `profile/admin/category.php`, `profile/admin/field.php`) | Missing null guards after `get()` | Added `is_object()` + `rank_id > 0` check in findusers; profile admin files fixed in prior commits |

### Phase 3: Security Hardening (3 items, 8 instances)

| Item | What was fixed | How |
|------|----------------|-----|
| 3A (1 file: `class/cache/file.php`) | `eval()` in file cache engine | Removed `eval()` path; changed default `serialize` to `true`; legacy entries treated as cache miss |
| 3B (2 files: `kernel/block.php`, `modules/system/class/block.php`) | `eval()` for custom PHP blocks | Gated behind `XOOPS_ALLOW_PHP_BLOCKS === true` constant check |
| 3C (5 instances: `kernel/object.php` x2, `kernel/configitem.php`, `class/cache/model.php`, `modules/profile/include/update.php`) | Unsafe `unserialize()` without `allowed_classes` | Added `['allowed_classes' => false]`; `configitem.php` uses `set_error_handler()` for corrupted data |

### Phase 4: Compatibility (3 items)

| Item | What was fixed | How |
|------|----------------|-----|
| 4A (2 files: `system/templates/system_imagemanager.tpl`, `system_imagemanager2.tpl`) | Smarty `{php}` tags | Removed `{php}` blocks (fixed in prior commits) |
| 4B (1 directory: `xoops_lib/vendor/smottt/wideimage/demo/`) | PHP 8.2 fatal (`__autoload()`) | Deleted demo directory |
| 4C (1 file: `system/admin/users/main.php`) | Type safety for `$groups_failed` in SQL | Already cast to `(int)` in loop (confirmed correct) |

### Phase 5: Consistency (1 item, 2 files)

| Item | What was fixed | How |
|------|----------------|-----|
| 5A (2 files: `xoops_lib/modules/protector/class/gtickets.php`, `xoops_lib/modules/protector/filters_disabled/postcommon_register_stopforumspam.php`) | Raw superglobals | Migrated to `Xmf\Request::getString()` / `hasVar()` |

---

## Deferred / No Action Needed

| Finding | Reason |
|---------|--------|
| Migration Cat 6.2 (profile/search.php `quoteString`) | Already correct — `quoteString` provides SQL escaping |
| Migration Cat 7.1-7.3 (GET/POST source inconsistencies) | Acceptable — REQUEST is intentional for flows receiving IDs from both GET links and POST forms |
| Protector superglobal modification (Cat 5.3 partial) | Intentional design — module needs to sanitize superglobals directly |
| Security check `$_REQUEST` iteration (Cat 5.4) | Intentional — iterating dynamic variable names for security filtering |

---

## Files Changed

| Category | Files |
|----------|-------|
| Password reset | `modules/profile/preloads/core.php`, `modules/profile/lostpass.php`, `lostpass.php` |
| XSS fixes | `class/xoopscomments.php`, `themes/default/modules/system/system_siteclosed.tpl` |
| eval/unserialize | `class/cache/file.php`, `kernel/block.php`, `modules/system/class/block.php`, `kernel/object.php`, `kernel/configitem.php`, `class/cache/model.php`, `modules/profile/include/update.php` |
| Null guards and CSRF | `modules/system/admin/users/main.php`, `include/findusers.php` |
| Content fields | `edituser.php`, `include/comment_post.php`, `modules/system/admin/users/main.php` |
| Superglobals | `xoops_lib/modules/protector/class/gtickets.php`, `xoops_lib/modules/protector/filters_disabled/postcommon_register_stopforumspam.php` |
| PHP 8.2 compat | `xoops_lib/vendor/smottt/wideimage/demo/` (deleted) |

---

## Upgrade Notes

- **Cache**: The file cache engine default changed from `serialize => false` to `serialize => true`. Existing non-serialized cache files will be treated as cache misses and regenerated automatically. For immediate effect, clear the configured cache directory (typically `XOOPS_VAR_PATH/caches/xoops_cache/`) after upgrade.
- **PHP Blocks**: Custom PHP blocks (`c_type = 'P'`) are now disabled by default. To re-enable, define `XOOPS_ALLOW_PHP_BLOCKS` as `true` in `mainfile.php`.
- **Password Reset**: Pending password reset emails sent before upgrade will not work with the new secure flow. Users who received a reset link prior to upgrade must request a new password reset.
