<?php
/*
 You may not change or alter any portion of this comment or credits of supporting
 developers from this source code or any supporting source code which is considered
 copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Safe, modern cookie setter with RFC 6265 domain validation and SameSite support.
 *
 * This function replaces the legacy func_get_args() version with an explicit
 * signature, making it more robust and easier for static analysis to validate.
 *
 * @copyright       Copyright 2021-2025 The XOOPS Project https://xoops.org
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author          Richard Griffith <richard@geekwright.com>
 *
 * @param string $name The name of the cookie.
 * @param string|null $value The value of the cookie.
 * @param int $expire The time the cookie expires. This is a Unix timestamp.
 * @param string $path The path on the server in which the cookie will be available on.
 * @param string $domain The (sub)domain that the cookie is available to.
 * @param bool|null $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection.
 * If null, it will be auto-detected.
 * @param bool $httponly When TRUE the cookie will be made accessible only through the HTTP protocol.
 * @param string $samesite The SameSite attribute ('Lax', 'Strict', 'None').
 * @return bool True on success, false on failure.
 */
function xoops_setcookie(
    string $name,
    ?string $value = '',
    int $expire = 0,
    string $path = '/',
    string $domain = '',
    ?bool $secure = null,
    bool $httponly = true,
    string $samesite = 'Lax'
): bool {
    if (headers_sent()) {
        return false;
    }

    // THE FIX: Ensure a null value is converted to an empty string.
    $value = $value ?? '';

    $host = $_SERVER['HTTP_HOST'] ?? '';

    // Validate the domain BEFORE using it.
    if (class_exists('\Xoops\RegDom\RegisteredDomain')) {
        if (!\Xoops\RegDom\RegisteredDomain::domainMatches($host, $domain)) {
            $originalDomain = $domain;
            $domain = ''; // Auto-correct to a safe, host-only cookie

            if (defined('XOOPS_DEBUG_MODE') && XOOPS_DEBUG_MODE) {
                error_log(
                    sprintf(
                        '[XOOPS Cookie] Invalid domain "%s" for host "%s" (cookie: %s) - using host-only.',
                        $originalDomain,
                        $host,
                        $name
                    )
                );
            }
        }
    }

    // Auto-detect 'secure' flag if not explicitly set
    if ($secure === null) {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }

    // Use modern array syntax for PHP 7.3+
    if (PHP_VERSION_ID >= 70300) {
        $options = [
            'expires'  => $expire,
            'path'     => $path,
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];
        if ($domain !== '') {
            $options['domain'] = $domain;
        }
        return setcookie($name, $value, $options);
    }

    // Fallback for older PHP versions
    return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

/**
 * @param array $args 'name', 'value' and 'options' corresponding to php 7.3 arguments to setcookie()
 *
 * @return string
 */
function xoops_buildCookieHeader($args)
{
    //$optionsKeys = array('expires', 'path', 'domain', 'secure', 'httponly', 'samesite');
    $options = $args['options'];

    $header = 'Set-Cookie: ' . $args['name'] . '=' . rawurlencode($args['value']) . ' ';

    if (isset($options['expires']) && 0 !== $options['expires']) {
        $dateTime = new DateTime();
        if (time() >= $options['expires']) {
            $dateTime->setTimestamp(0);
            $header = 'Set-Cookie: ' . $args['name'] . '=deleted ; expires=' . $dateTime->format(DateTime::COOKIE) . ' ; Max-Age=0 ';
        } else {
            $dateTime->setTimestamp($options['expires']);
            $header .= '; expires=' . $dateTime->format(DateTime::COOKIE) . ' ';
        }
    }

    if (isset($options['path']) && '' !== $options['path']) {
        $header .= '; path=' . $options['path'] . ' ';
    }

    if (isset($options['domain']) && '' !== $options['domain']) {
        $header .= '; domain=' . $options['domain'] . ' ';
    }

    if (isset($options['secure']) && true === (bool) $options['secure']) {
        $header .= '; Secure ';
    }

    if (isset($options['httponly']) && true === (bool) $options['httponly']) {
        $header .= '; HttpOnly ';
    }

    if (isset($options['samesite']) && '' !== $options['samesite']) {
        $header .= '; samesite=' . $options['samesite'] . ' ';
    }

    return $header;
}
