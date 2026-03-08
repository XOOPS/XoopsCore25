# XOOPS Core 2.5 — Security Audit Remediation Record

**Date:** 2026-03-08
**Based on:** `security-critical-audit-2026-03-08.md` + `migration_audit_findings.md`
**Status:** All 22 actionable items fixed. 4 items deferred as acceptable.

---

## Executive Summary

Two independent audits identified **26 distinct issues** across security, compatibility, and correctness categories. **22 have been fixed** across two rounds of commits. 4 items are deferred as intentional design or already correct.

---

## Fixed Items

### Phase 1: Critical

| Item | What was fixed | How |
|------|----------------|-----|
| 1A | Password fields using `getString()` (all 6 files) | Changed to `Request::getVar()` with `MASK_ALLOW_RAW \| MASK_NO_TRIM` |
| 1B | Insecure profile lostpass (md5 5-char token) | Disabled preload redirect in `core.php`; `profile/lostpass.php` now redirects to secure core flow |
| 1C | Missing null guards after `getUser()` in system admin (3 instances) | Added `!is_object()` guards with `_AM_SYSTEM_USERS_NO_SUCH_USER` redirect |

### Phase 2: High

| Item | What was fixed | How |
|------|----------------|-----|
| 2A | Reflected XSS via `$_SERVER['PHP_SELF']` in `xoopscomments.php` | Replaced with `htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES \| ENT_HTML5, 'UTF-8')` |
| 2B | Raw `REQUEST_URI` in `system_siteclosed.tpl` | Added `\|escape` Smarty modifier |
| 2C | Content fields (`com_text`, `user_sig`, `bio`) using `getString()` | Changed to `getText()` pinned to `'POST'` |
| 2D | CSRF: `ok` parameter not pinned to POST | Changed to `Request::getInt('ok', 0, 'POST')` |
| 2E | Missing null guards in `findusers.php` and profile admin files | Added `is_object()` + `rank_id > 0` check; profile admin files already fixed |

### Phase 3: Security Hardening

| Item | What was fixed | How |
|------|----------------|-----|
| 3A | `eval()` in file cache engine | Removed `eval()` path; changed default `serialize` to `true`; legacy non-serialized entries treated as cache miss |
| 3B | `eval()` for custom PHP blocks | Gated behind `XOOPS_ALLOW_PHP_BLOCKS` constant (default `false`) using `defined()/constant()` pattern |
| 3C | Unsafe `unserialize()` (5 instances) | Added `['allowed_classes' => false]` to all calls; `configitem.php` uses `set_error_handler()` for corrupted data |

### Phase 4: Compatibility

| Item | What was fixed | How |
|------|----------------|-----|
| 4A | Smarty `{php}` tags in imagemanager templates | Removed `{php}` blocks (fixed in prior commits) |
| 4B | PHP 8.2 fatal in vendor demo (`__autoload()`) | Deleted `wideimage/demo/` directory |
| 4C | Type safety for `$groups_failed` in SQL | Already cast to `(int)` in loop (confirmed correct) |

### Phase 5: Consistency

| Item | What was fixed | How |
|------|----------------|-----|
| 5A | Raw superglobals in `gtickets.php` and `stopforumspam.php` | Migrated to `Xmf\Request::getString()` / `hasVar()` |

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
| XSS fixes | `class/xoopscomments.php`, `themes/default/.../system_siteclosed.tpl` |
| eval/unserialize | `class/cache/file.php`, `kernel/block.php`, `modules/system/class/block.php`, `kernel/object.php`, `kernel/configitem.php`, `class/cache/model.php`, `modules/profile/include/update.php` |
| Null guards and CSRF | `modules/system/admin/users/main.php`, `include/findusers.php` |
| Content fields | `edituser.php`, `include/comment_post.php`, `modules/system/admin/users/main.php` |
| Superglobals | `xoops_lib/modules/protector/class/gtickets.php`, `xoops_lib/.../postcommon_register_stopforumspam.php` |
| PHP 8.2 compat | `xoops_lib/vendor/smottt/wideimage/demo/` (deleted) |

---

## Upgrade Notes

- **Cache**: The file cache engine default changed from `serialize => false` to `serialize => true`. Existing non-serialized cache files will be treated as cache misses and regenerated automatically. For immediate effect, clear the cache directory (`xoops_data/caches/xoops_cache/`) after upgrade.
- **PHP Blocks**: Custom PHP blocks (`c_type = 'P'`) are now disabled by default. To re-enable, define `XOOPS_ALLOW_PHP_BLOCKS` as `true` in `mainfile.php`.
- **Password Reset**: Pending password reset emails sent before upgrade will not work with the new secure flow. Users who received a reset link prior to upgrade must request a new password reset.
