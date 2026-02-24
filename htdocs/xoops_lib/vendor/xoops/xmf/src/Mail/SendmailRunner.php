<?php
declare(strict_types=1);

namespace Xmf\Mail;

/**
 * SendmailRunner safely executes sendmail commands for email delivery.
 *
 * This final class validates sendmail binary paths against a strict allowlist
 * and ensures the binary is executable. It supports optional envelope sender
 * validation and normalizes message line endings to comply with RFC 5322.
 *
 * @category  Xmf\Mail
 * @package   Xmf
 * @author    XOOPS Development Team <
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */

/**
 * Safe sendmail runner for XOOPS.
 *
 * - No shell: argv-only via proc_open([...], ..., ['bypass_shell' => true])
 * - Strict validation:
 *   • absolute ASCII path format
 *   • allowlist enforcement
 *   • canonical target check via realpath()
 *   • executable file check (optional symlink policy)
 * - Optional, validated envelope sender (-f)
 * - CRLF normalization (str_replace-based)
 * - Diagnostics: clipped stdout/stderr on failure; warns if stderr on success
 *
 * Customize:
 * - Pass a custom $allowlist (array of absolute paths) in the constructor.
 * - Toggle $allowSymlinks (default true) to allow symlinks that resolve
 *   to a canonical allowlisted target.
 * - Inject filesystem check callables (is_executable/is_link/is_file) for testing.
 */
final class SendmailRunner
{
    /** @var string[] absolute paths considered for allowlisting */
    private array $allowlist;

    /** @var string[] canonical realpaths of allowlisted binaries */
    private array $allowlistCanonical;

    /** @var bool allow symlinks that resolve to a canonical allowlist target */
    private bool $allowSymlinks;

    /** @var callable(string):bool */
    private $isExecutable;
    /** @var callable(string):bool */
    private $isLink;
    /** @var callable(string):bool */
    private $isFile;

    public function __construct(
        ?array $allowlist = null,
        ?callable $isExecutable = null,
        ?callable $isLink = null,
        ?callable $isFile = null,
        bool $allowSymlinks = true
    ) {
        $this->allowlist = $allowlist ?? [
            '/usr/sbin/sendmail',
            '/usr/lib/sendmail',
            '/usr/bin/sendmail',
            '/usr/bin/msmtp',
            '/usr/sbin/ssmtp',
            '/usr/local/sbin/sendmail',
            '/usr/local/bin/sendmail',
        ];
        $this->isExecutable  = $isExecutable ?? 'is_executable';
        $this->isLink        = $isLink       ?? 'is_link';
        $this->isFile        = $isFile       ?? 'is_file';
        $this->allowSymlinks = $allowSymlinks;

        // Build canonical allowlist by resolving real targets of allowlisted entries.
        $canon = [];
        foreach ($this->allowlist as $p) {
            $rp = realpath($p);               // string|false
            if (is_string($rp)) {
                $canon[$rp] = true;           // set-like de-dupe
            }
        }
        $this->allowlistCanonical = array_keys($canon);
    }

    /**
     * Discover installed, allowlisted binaries (literal allowlist entries that
     * currently exist and meet executable criteria). Symlinks accepted only if
     * they pass isValidBinary() and policy allows them.
     *
     * @return string[] list of literal paths from the allowlist that are valid
     */
    public function discover(): array
    {
        $found = [];
        foreach ($this->allowlist as $path) {
            $real = realpath($path); // string|false
            $ok   = $this->isValidBinary($path, is_string($real) ? $real : null);
            if ($ok) {
                $found[] = $path;
            }
        }
        // Keep literal paths for UI consistency; remove duplicates just in case.
        return array_values(array_unique($found));
    }

    /**
     * Validate an absolute ASCII path against format, allowlist policy,
     * canonical real target, and filesystem permissions.
     *
     * @return string|null the canonical (resolved) path if valid; null otherwise
     */
    public function validatePath(string $path): ?string
    {
        $path = trim($path);
        if (!preg_match('~^/(?:[A-Za-z0-9._-]+/)*[A-Za-z0-9._-]+$~', $path)) {
            return null;
        }

        $resolved = realpath($path); // string|false
        if (!is_string($resolved)) {
            return null;
        }

        if ($resolved === $path) {
            // Not a symlink: the literal path must be allowlisted.
            if (!in_array($path, $this->allowlist, true)) {
                return null;
            }
        } else {
            // Symlink: allow only if policy permits and the resolved target is canonical-allowlisted.
            if (!$this->allowSymlinks || !in_array($resolved, $this->allowlistCanonical, true)) {
                return null;
            }
        }

        return $this->isValidBinary($path, $resolved) ? $resolved : null;
    }

    /**
     * Deliver an RFC 5322 message via sendmail -t -i, optionally with -f.
     *
     * @param string      $sendmailPath validated absolute path (literal form)
     * @param string      $rfc822       headers + CRLF CRLF + body
     * @param string|null $envelopeFrom optional envelope sender (validated)
     *
     * @throws \RuntimeException on failures to start, write, or non-zero exit
     */
    public function deliver(string $sendmailPath, string $rfc822, ?string $envelopeFrom = null): void
    {
        $validatedPath = $this->validatePath($sendmailPath);
        if ($validatedPath === null) {
            throw new \RuntimeException('Invalid sendmail path.');
        }

        // Normalize line endings to CRLF for RFC 5322 compliance (two-step, no double expansion).
        $rfc822 = str_replace("\r\n", "\n", $rfc822);
        $rfc822 = str_replace("\n", "\r\n", $rfc822);

        // Prefer the literal path if it resolves to the same canonical target; else use canonical.
        $literal  = $sendmailPath;
        $resolved = realpath($literal);
        if (!is_string($resolved) || $resolved !== $validatedPath) {
            $literal = $validatedPath;
        }

        $argv = [$literal];

        // Optional, strictly-validated envelope sender (-f).
        $validatedEnvelopeFrom = $this->validateEnvelopeFrom($envelopeFrom);
        if ($validatedEnvelopeFrom !== null) {
            $argv[] = '-f';
            $argv[] = $validatedEnvelopeFrom;
        }

        // Safe flags only.
        $argv[] = '-t';
        $argv[] = '-i';

        $spec = [
            0 => ['pipe', 'w'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $proc = proc_open($argv, $spec, $pipes, null, null, ['bypass_shell' => true]);
        if (!is_resource($proc)) {
            throw new \RuntimeException('Failed to start sendmail process.');
        }

        $stdout = '';
        $stderr = '';
        $code   = null;

        try {
            // Robust write loop (handle partial writes / broken pipe)
            $len = strlen($rfc822);
            $off = 0;
            while ($off < $len) {
                $chunk = substr($rfc822, $off);
                $n     = fwrite($pipes[0], $chunk);
                if ($n === false) {
                    throw new \RuntimeException('Failed to write message to sendmail (broken pipe).');
                }
                if ($n === 0) {
                    if (!is_resource($pipes[0]) || feof($pipes[0])) {
                        throw new \RuntimeException('sendmail closed the input pipe prematurely.');
                    }
                    usleep(10000);
                    continue;
                }
                $off += $n;
            }
            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]) ?: '';
            $stderr = stream_get_contents($pipes[2]) ?: '';
            fclose($pipes[1]);
            fclose($pipes[2]);
        } finally {
            if (is_resource($proc)) {
                $code = proc_close($proc);
            }
        }

        // Warn if stderr contains content despite success.
        if ($code === 0 && $stderr !== '') {
            error_log('sendmail warning (success): ' . $this->clipForLog($stderr));
        }

        if ($code !== 0) {
            $sOut  = $this->clipForLog($stdout);
            $sErr  = $this->clipForLog($stderr);
            $first = $this->firstLine($stderr);
            error_log("sendmail failure: path={$literal} code={$code} stderr=\"{$sErr}\" stdout=\"{$sOut}\"");
            throw new \RuntimeException('Sendmail exited with code ' . $code . ($first !== '' ? ': ' . $first : ''));
        }
    }

    /* ====================== helpers ====================== */

    /**
     * Filesystem checks for the target binary.
     * Uses $real (canonical target) when provided; otherwise uses $path.
     */
    private function isValidBinary(string $path, ?string $real = null): bool
    {
        $target = $real ?? $path;

        if (!($this->isFile)($target) || !($this->isExecutable)($target)) {
            return false;
        }
        // If symlinks are globally disallowed, reject when the input is a symlink.
        if (!$this->allowSymlinks && ($this->isLink)($path)) {
            return false;
        }
        return true;
    }

    /**
     * Validate an email address for use in -f (envelope sender).
     * Returns sanitized address or null if unusable.
     */
    private function validateEnvelopeFrom(?string $addr): ?string
    {
        if ($addr === null || $addr === '') {
            return null;
        }
        // Extract <email@host> if a "Name <email>" form was supplied.
        if (preg_match('/<([^>]+)>/', $addr, $m)) {
            $addr = $m[1];
        }
        // Forbid any whitespace/control to prevent header/arg injection.
        if (preg_match('/\s/', $addr) || preg_match('/[\r\n]/', $addr)) {
            return null;
        }
        return filter_var($addr, FILTER_VALIDATE_EMAIL) ? $addr : null;
    }

    /** Clip a string for logs (remove most control chars, escape line breaks, limit length). */
    private function clipForLog(string $s, int $max = 400): string
    {
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $s) ?? '';
        $s = str_replace(["\r", "\n"], ['\\r', '\\n'], $s);
        if (strlen($s) > $max) {
            $s = substr($s, 0, $max) . '…';
        }
        return $s;
    }

    /** Get the first (non-empty) line from a blob, for concise error messages. */
    private function firstLine(string $s): string
    {
        $pos  = strpos($s, "\n");
        $line = $pos === false ? $s : substr($s, 0, $pos);
        return $this->clipForLog($line, 200);
    }
}
