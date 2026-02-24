# RFC: Generic Token Handler for XOOPS

**Status:** Draft for discussion
**Context:** Kevin's review of PR #1624 — tokens should be a shared mechanism, not packed into actkey

---

## Problem

PR #1624 stored password-reset tokens by packing data into the `users.actkey` column. This works but has fundamental issues:

- Overloads a column meant for activation keys
- Only one token per user at a time
- Requires column-size introspection + cache fallback (complexity)
- Not reusable for other token-based features

XOOPS needs tokens in at least two places today (activation, password reset) and potentially more in the future. Each shouldn't invent its own storage mechanism.

## Solution

One table, one handler, four methods.

### Table: `xoops_tokens`

```sql
CREATE TABLE `xoops_tokens` (
    `token_id`   int unsigned        NOT NULL AUTO_INCREMENT,
    `uid`        mediumint unsigned  NOT NULL DEFAULT 0,
    `scope`      varchar(32)         NOT NULL DEFAULT '',
    `hash`       varchar(64)         NOT NULL DEFAULT '',
    `issued_at`  int unsigned        NOT NULL DEFAULT 0,
    `expires_at` int unsigned        NOT NULL DEFAULT 0,
    `used_at`    int unsigned        NOT NULL DEFAULT 0,
    PRIMARY KEY (`token_id`),
    KEY `idx_uid_scope` (`uid`, `scope`),
    KEY `idx_hash` (`hash`)
) ENGINE=InnoDB;
```

**Design decisions:**

- `scope` — string like `'lostpass'`, `'activation'`. Simple, readable, extensible.
- `hash` — SHA-256 of the raw token. Raw token is never stored, only emailed.
- `used_at` — 0 means unused. Nonzero = consumed. Single-use enforcement without deleting rows (auditable).
- Multiple tokens per user allowed (different scopes, or even same scope for re-requests).
- No IP column — rate limiting is a separate concern handled by the caller (via XoopsCache, as it is today).

### Handler: `XoopsTokenHandler`

```php
<?php
declare(strict_types=1);

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Generic scoped token handler for XOOPS.
 *
 * Provides create/verify/revoke for any token-based flow:
 * password reset, account activation, email verification, etc.
 *
 * @category  Xoops
 * @package   Core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
final class XoopsTokenHandler
{
    private readonly \XoopsMySQLDatabase $db;

    public function __construct(\XoopsMySQLDatabase $db)
    {
        $this->db = $db;
    }

    /**
     * Create a token for a user+scope. Returns the raw token (for email).
     *
     * @param int    $uid   User ID
     * @param string $scope Token scope (e.g. 'lostpass', 'activation')
     * @param int    $ttl   Time-to-live in seconds
     *
     * @return string|false Raw token string, or false on failure
     */
    public function create(int $uid, string $scope, int $ttl = 3600): string|false
    {
        $rawToken  = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $hash      = hash('sha256', $rawToken);
        $now       = time();
        $expiresAt = $now + max(60, $ttl);

        $table = $this->db->prefix('tokens');
        $sql   = sprintf(
            "INSERT INTO `%s` (`uid`, `scope`, `hash`, `issued_at`, `expires_at`, `used_at`)"
            . " VALUES (%d, %s, %s, %d, %d, 0)",
            $table,
            $uid,
            $this->db->quote($scope),
            $this->db->quote($hash),
            $now,
            $expiresAt
        );

        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }

        return $rawToken;
    }

    /**
     * Verify a raw token for a user+scope. Marks it as used if valid.
     *
     * @param int    $uid      User ID
     * @param string $scope    Token scope
     * @param string $rawToken Raw token from the URL/form
     *
     * @return bool true if valid and now consumed
     */
    public function verify(int $uid, string $scope, string $rawToken): bool
    {
        $hash  = hash('sha256', $rawToken);
        $table = $this->db->prefix('tokens');
        $now   = time();

        // Find a matching, unused, non-expired token
        $sql = sprintf(
            "SELECT `token_id` FROM `%s`"
            . " WHERE `uid` = %d AND `scope` = %s AND `hash` = %s"
            . " AND `used_at` = 0 AND `expires_at` > %d"
            . " LIMIT 1",
            $table,
            $uid,
            $this->db->quote($scope),
            $this->db->quote($hash),
            $now
        );

        $result = $this->db->query($sql);
        if (!$this->db->isResultSet($result) || !$result instanceof \mysqli_result) {
            return false;
        }

        $row = $this->db->fetchArray($result);
        if (!$row) {
            return false;
        }

        // Mark as used (single-use)
        $tokenId = (int)$row['token_id'];
        $this->db->query(sprintf(
            "UPDATE `%s` SET `used_at` = %d WHERE `token_id` = %d",
            $table,
            $now,
            $tokenId
        ));

        return true;
    }

    /**
     * Revoke all unused tokens for a user+scope.
     *
     * @param int    $uid   User ID
     * @param string $scope Token scope
     *
     * @return void
     */
    public function revokeByScope(int $uid, string $scope): void
    {
        $table = $this->db->prefix('tokens');
        $now   = time();

        $this->db->query(sprintf(
            "UPDATE `%s` SET `used_at` = %d WHERE `uid` = %d AND `scope` = %s AND `used_at` = 0",
            $table,
            $now,
            $uid,
            $this->db->quote($scope)
        ));
    }

    /**
     * Delete expired and used tokens older than the given age.
     *
     * @param int $maxAge Seconds to keep used/expired tokens (default 7 days)
     *
     * @return void
     */
    public function purgeExpired(int $maxAge = 604800): void
    {
        $table  = $this->db->prefix('tokens');
        $cutoff = time() - max(0, $maxAge);

        // Delete tokens that are either expired or used, and older than cutoff
        $this->db->query(sprintf(
            "DELETE FROM `%s` WHERE `expires_at` < %d AND `issued_at` < %d",
            $table,
            time(),
            $cutoff
        ));
    }
}
```

**That's the entire handler. Four methods, no magic, no inheritance.**

---

## How it simplifies each feature

### Password Reset (`lostpass.php`)

**Before (current):**
```php
$security = new LostpassSecurity($xoopsDB);
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
packActkey/unpackActkey/isLostpassActkey/canFitActkey/getActkeyMaxLen chain
collapses into two method calls.

### Registration Activation (`register.php`)

**Before (current):**
```php
$actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
$newuser->setVar('actkey', $actkey, true);
// ... activation link: register.php?op=actv&id=$uid&actkey=$actkey ...
// ... later ...
if ($thisuser->getVar('actkey') != $actkey) { /* reject */ }
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

Same pattern. md5/mt_rand replaced with random_bytes. Actkey column no longer needed.

### Future features

Any new feature that needs "give user a link, verify it later" just calls
`create()` and `verify()` with a new scope string. No new tables, no new classes.

---

## What happens to existing code

### `LostpassSecurity` — simplify to rate limiter only

Strip out everything token-related. Keep only:
- `isAbusing()` — rate limiting via XoopsCache
- `rateHit()` — fixed-window counter
- Protector integration
- Cache helpers (used by rate limiter)

Rename to `RateLimiter` or keep as `LostpassSecurity` (since it's lostpass-specific rate limiting).

### `lostpass.php` — much simpler

```php
require_once __DIR__ . '/class/XoopsTokenHandler.php';
require_once __DIR__ . '/class/LostpassSecurity.php';  // rate limiting only

$tokenHandler = new XoopsTokenHandler($xoopsDB);
$rateLimiter  = new LostpassSecurity($xoopsDB);

// MODE B: Request reset
if ($rateLimiter->isAbusing($ip, $email)) { /* block */ }
$rawToken = $tokenHandler->create($uid, 'lostpass', 3600);
// ... send email ...

// MODE A: Verify + set password
if ($rateLimiter->isAbusing($ip, 'uid:' . $uid)) { /* block */ }
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

Add to `upgrade/upd_2.5.11-to-2.5.12/index.php`:

```php
public function check_createtokenstable()
{
    $table = $GLOBALS['xoopsDB']->prefix('tokens');
    $result = $GLOBALS['xoopsDB']->query("SHOW TABLES LIKE " . $GLOBALS['xoopsDB']->quote($table));
    if (!$GLOBALS['xoopsDB']->isResultSet($result) || !$result instanceof \mysqli_result) {
        return false;
    }
    return (bool)$GLOBALS['xoopsDB']->fetchArray($result);
}

public function apply_createtokenstable()
{
    $table = $GLOBALS['xoopsDB']->prefix('tokens');
    $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
        `token_id`   int unsigned        NOT NULL AUTO_INCREMENT,
        `uid`        mediumint unsigned  NOT NULL DEFAULT 0,
        `scope`      varchar(32)         NOT NULL DEFAULT '',
        `hash`       varchar(64)         NOT NULL DEFAULT '',
        `issued_at`  int unsigned        NOT NULL DEFAULT 0,
        `expires_at` int unsigned        NOT NULL DEFAULT 0,
        `used_at`    int unsigned        NOT NULL DEFAULT 0,
        PRIMARY KEY (`token_id`),
        KEY `idx_uid_scope` (`uid`, `scope`),
        KEY `idx_hash` (`hash`)
    ) ENGINE=InnoDB;";

    $result = $GLOBALS['xoopsDB']->query($sql);
    if (!$result) {
        $this->logs[] = 'Failed to create tokens table.';
        return false;
    }
    return true;
}
```

Also add to `mysql.structure.sql` for new installations.

---

## Cooldown check (Kevin's suggestion)

"You have already requested a reset token in the last 15 minutes" — this is just a query:

```php
// In lostpass.php, before creating a new token:
$table = $xoopsDB->prefix('tokens');
$sql = sprintf(
    "SELECT COUNT(*) AS cnt FROM `%s` WHERE `uid` = %d AND `scope` = 'lostpass' AND `issued_at` > %d",
    $table, $uid, time() - 900
);
// If cnt > 0, skip sending another email
```

This doesn't need to be in the handler. The caller knows its own business rules.

---

## What we keep from PR #1624

- Smarty template (`system_lostpass.tpl`)
- `lostpass_assign_form()` helper
- Rate limiting via XoopsCache (in simplified LostpassSecurity)
- Protector integration
- Anti-enumeration (generic responses)
- CSRF on reset form
- Password validation logic
- Test patterns and bootstrap constants

## What we remove

- `LostpassSecurity::packActkey()` / `unpackActkey()`
- `LostpassSecurity::isLostpassActkey()`
- `LostpassSecurity::canFitActkey()` / `getActkeyMaxLen()`
- `LostpassSecurity::readPayload()` / `storePayload()` / `clearPayloadInMemory()`
- Cache fallback for token storage (`CACHE_TOKEN_PREFIX`)
- `LostpassSecurity::isExpired()` (the DB handles expiry now)
- `LostpassSecurity::generateToken()` / `hashToken()` (moved to handler)
- actkey column introspection (`SHOW COLUMNS`)

## Complexity comparison

| Metric | Current (PR #1624) | Proposed |
|--------|-------------------|----------|
| Token storage methods | 8 (pack/unpack/read/store/clear/isLostpass/canFit/getMaxLen) | 2 (create/verify) |
| Storage locations | 2 (actkey column + XoopsCache) | 1 (tokens table) |
| Column introspection | Yes (SHOW COLUMNS at runtime) | No |
| Cache fallback | Yes | No |
| Source tracking | Yes ('actkey' vs 'cache') | No |
| Reusable for other features | No | Yes |
| Lines in handler | ~300 (LostpassSecurity) | ~120 (XoopsTokenHandler) |

---

## Open questions for discussion

1. **Should `purgeExpired()` run on a cron, or lazily on each request?**
   Suggestion: cron via XOOPS preload event, with a lazy fallback.

2. **Should the handler be a kernel handler (extends XoopsObjectHandler)?**
   I kept it standalone for simplicity, but it could follow the XOOPS handler pattern if preferred.

3. **Should we rename `LostpassSecurity` to something more generic like `RateLimiter`?**
   The rate limiting logic isn't lostpass-specific. Other features might want it too.

4. **Migration of existing lostpass tokens in actkey column?**
   Users with a pending `lp|...` actkey after the upgrade would lose their reset link.
   Options: (a) accept it — they can request a new one, (b) migrate during upgrade.
   Suggestion: (a) — it's a one-time edge case.
