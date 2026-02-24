# RFC: Generic Token Handler for XOOPS

**Status:** Revised after peer review (4 independent reviews incorporated)
**Context:** Kevin's review of PR #1624 — tokens should be a shared mechanism, not packed into actkey
**Requires:** PHP 8.1+ (readonly properties, union return types)

---

## Problem

PR #1624 stored password-reset tokens by packing data into the `users.actkey` column. This works but has fundamental issues:

- Overloads a column meant for activation keys
- Only one token per user at a time
- Requires column-size introspection + cache fallback (complexity)
- Not reusable for other token-based features

XOOPS needs tokens in at least two places today (activation, password reset) and potentially more in the future. Each shouldn't invent its own storage mechanism.

## Solution

One table, one handler, five methods.

### Table: `xoops_tokens`

```sql
CREATE TABLE `xoops_tokens` (
    `token_id`   int unsigned        NOT NULL AUTO_INCREMENT,
    `uid`        mediumint unsigned  NOT NULL DEFAULT 0,
    `scope`      varchar(32)         NOT NULL DEFAULT '',
    `hash`       char(64)            NOT NULL DEFAULT '',
    `issued_at`  int unsigned        NOT NULL DEFAULT 0,
    `expires_at` int unsigned        NOT NULL DEFAULT 0,
    `used_at`    int unsigned        NOT NULL DEFAULT 0,
    PRIMARY KEY (`token_id`),
    KEY `idx_uid_scope_hash` (`uid`, `scope`, `hash`)
) ENGINE=InnoDB;
```

**Design decisions:**

- `scope` — string like `'lostpass'`, `'activation'`. Simple, readable, extensible.
- `hash` — `CHAR(64)`: SHA-256 hex digest is always exactly 64 characters. `CHAR` is slightly more efficient than `VARCHAR` for fixed-length data in InnoDB.
- `used_at` — 0 means unused. Nonzero = consumed. Single-use enforcement without deleting rows (auditable).
- Single composite index `(uid, scope, hash)` covers the `verify()` query as a single index lookup. Also covers `revokeByScope()` and `countRecent()` queries on the `(uid, scope)` prefix.
- No IP column — rate limiting is a separate concern handled by the caller (via XoopsCache, as it is today).

### Handler: `XoopsTokenHandler`

Five methods: `create`, `verify`, `revokeByScope`, `countRecent`, `purgeExpired`.

**Key improvements over the original draft (from peer review):**

1. **Atomic `verify()`** — single UPDATE + `getAffectedRows()` eliminates the TOCTOU race condition in the original SELECT-then-UPDATE approach.
2. **Auto-revoke on `create()`** — previous unused tokens for the same scope are revoked by default, ensuring only the latest link works (OWASP recommendation).
3. **`countRecent()` method** — keeps cooldown-check SQL inside the handler, not leaked into callers.
4. **Fixed `purgeExpired()` logic** — now correctly cleans up used-but-not-yet-expired tokens.
5. **`MIN_TTL` constant** — documents the 60-second floor instead of silent enforcement.

See `htdocs/class/XoopsTokenHandler.php` for the implementation.

---

## How it simplifies each feature

### Password Reset (`lostpass.php`)

**Before (PR #1624):**
```php
$security = new LostPassSecurity($xoopsDB);
$rawToken = $security->generateToken();
$hash     = $security->hashToken($rawToken);
$issuedAt = time();
$security->storePayload($user, $member_handler, $issuedAt, $hash);
// ... later ...
$payload = $security->readPayload($user);
$security->isExpired($payload['issuedAt']);
hash_equals($payload['hash'], $security->hashToken($token));
$security->clearPayloadInMemory($user, $payload['source']);
```

**After:**
```php
$tokenHandler = new XoopsTokenHandler($xoopsDB);
$rawToken = $tokenHandler->create($uid, 'lostpass', 3600);
// ... later ...
if ($tokenHandler->verify($uid, 'lostpass', $rawToken)) {
    // set new password
}
```

The entire readPayload/storePayload/clearPayloadInMemory/isExpired/hashToken/
packActKey/unpackActKey/isLostPassActKey/canFitActKey/getActKeyMaxLen chain
collapses into two method calls.

### Registration Activation (`register.php`)

**Before (current):**
```php
$actKey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
$newuser->setVar('actkey', $actKey, true);
// ... activation link: register.php?op=actv&id=$uid&actkey=$actKey ...
// ... later ...
if ($thisuser->getVar('actkey') != $actKey) { /* reject */ }
```

**After:**
```php
$tokenHandler = new XoopsTokenHandler($xoopsDB);
$rawToken = $tokenHandler->create($uid, 'activation', 86400);
// ... activation link: register.php?op=actv&id=$uid&token=$rawToken ...
// ... later ...
if ($tokenHandler->verify($uid, 'activation', $rawToken)) {
    // activate user
}
```

Same pattern. md5/mt_rand replaced with random_bytes. actkey column no longer needed.

### Future features

Any new feature that needs "give user a link, verify it later" just calls
`create()` and `verify()` with a new scope string. No new tables, no new classes.

---

## What happens to existing code

### `LostPassSecurity` — simplify to rate limiter only

Strip out everything token-related. Keep only:
- `isRateLimited()` — rate limiting via XoopsCache
- `rateHit()` — fixed-window counter
- Protector integration
- Cache helpers (used by rate limiter)

Keep as `LostPassSecurity` for now (lostpass-specific rate limiting).
Can be generalized to `XoopsRateLimiter` in a future PR.

### `lostpass.php` — much simpler

```php
require_once __DIR__ . '/class/XoopsTokenHandler.php';
require_once __DIR__ . '/class/LostPassSecurity.php';  // rate limiting only

$tokenHandler = new XoopsTokenHandler($xoopsDB);
$rateLimiter  = new LostPassSecurity();

// MODE B: Request reset
if ($rateLimiter->isRateLimited($ip, $email)) { /* block */ }
if ($tokenHandler->countRecent($uid, 'lostpass', 900) > 0) { /* cooldown */ }
$rawToken = $tokenHandler->create($uid, 'lostpass', 3600);
// ... send email ...

// MODE A: Verify + set password
if ($rateLimiter->isRateLimited($ip, 'uid:' . $uid)) { /* block */ }
if ($tokenHandler->verify($uid, 'lostpass', $rawToken)) {
    $user->setVar('pass', password_hash($pass, PASSWORD_DEFAULT));
    $member_handler->insertUser($user, true);
}
```

No more payload reading, source tracking ('actkey' vs 'cache'), deferred clearing,
or column introspection. The token handler does one thing: create and verify tokens.

### `users.actkey` column — phase out gradually

1. This PR: lostpass stops using actkey, uses tokens table instead
2. Next PR: registration stops using actkey, uses tokens table instead
3. Eventually: deprecate and remove actkey column

The VARCHAR(100) expansion from PR #1624 is harmless and can stay until actkey is removed.

---

## Migration (upgrade script)

Added to `upgrade/upd_2.5.11-to-2.5.12/index.php` as a `createtokenstable` task.
Also added to `mysql.structure.sql` for new installations.

---

## What we keep from PR #1624

- Smarty template (`system_lostpass.tpl`)
- `lostpass_assign_form()` helper
- Rate limiting via XoopsCache (in simplified LostPassSecurity)
- Protector integration
- Anti-enumeration (generic responses)
- CSRF on reset form
- Password validation logic
- Test patterns and bootstrap constants

## What we remove

- `LostPassSecurity::packActKey()` / `unpackActKey()`
- `LostPassSecurity::isLostPassActKey()`
- `LostPassSecurity::canFitActKey()` / `getActKeyMaxLen()`
- `LostPassSecurity::readPayload()` / `storePayload()` / `clearPayloadInMemory()`
- Cache fallback for token storage (`CACHE_TOKEN_PREFIX`)
- `LostPassSecurity::isExpired()` (the DB handles expiry now)
- `LostPassSecurity::generateToken()` / `hashToken()` (moved to handler)
- actkey column introspection (`SHOW COLUMNS`)

## Complexity comparison

| Metric | Before (PR #1624) | After |
|--------|-------------------|-------|
| Token storage methods | 8 (pack/unpack/read/store/clear/isLostPassActKey/canFit/getMaxLen) | 5 (create/verify/revoke/countRecent/purge) |
| Storage locations | 2 (actkey column + XoopsCache) | 1 (tokens table) |
| Column introspection | Yes (SHOW COLUMNS at runtime) | No |
| Cache fallback | Yes | No |
| Source tracking | Yes ('actkey' vs 'cache') | No |
| Reusable for other features | No | Yes |
| Lines in handler | ~300 (LostPassSecurity) | ~130 (XoopsTokenHandler) |
| Race condition | Possible (non-atomic verify) | None (atomic UPDATE) |

---

## Resolved questions (from peer review consensus)

1. **`purgeExpired()` — cron or lazy?**
   Cron via XOOPS preload event recommended. Lazy probabilistic fallback (1-in-N on `create()`) can be added later if needed.

2. **Kernel handler or standalone?**
   Standalone. The lightweight approach is easier to test and maintain. Can be registered via `xoops_getHandler('token')` in a follow-up if desired.

3. **Rename `LostPassSecurity`?**
   Keep as-is for now. Generalize to `XoopsRateLimiter` in a separate PR when other features need rate limiting.

4. **Migration of existing lostpass tokens?**
   Accept the loss — users can request a new reset link. Writing a migration for ephemeral 1-hour tokens is poor ROI.
