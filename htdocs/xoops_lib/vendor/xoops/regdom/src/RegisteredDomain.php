<?php

declare(strict_types=1);

namespace Xoops\RegDom;

/**
 * Class RegisteredDomain
 *
 * Determine the registrable domain portion of a URL, respecting the public suffix list conventions
 *
 * @package   Xoops\RegDom
 * @author    Florian Sager, 06.08.2008, <sager@agitos.de>
 * @author    Marcus Bointon (https://github.com/Synchro/regdom-php)
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Michael Beck <mamba@xoops.org>
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class RegisteredDomain
{
    private PublicSuffixList $psl;
    private static ?PublicSuffixList $pslInstance = null;

    public function __construct(?PublicSuffixList $psl = null)
    {
        $this->psl = $psl ?? new PublicSuffixList();
    }

    /**
     * Extracts the registrable domain from a host string.
     *
     * @param string $host The host string to process.
     * @param bool   $utf8 Return UTF-8 (true) or ASCII/Punycode (false).
     * @return string|null The registrable domain or null if invalid.
     */
    public function getRegisteredDomain(string $host, bool $utf8 = true): ?string
    {
        $normalizedHost = self::normalizeHost($host);
        if ($normalizedHost === '') {
            return null;
        }

        // Add a check for exception rules first
        if ($this->psl->isException($normalizedHost)) {
            return $normalizedHost;
        }

        $hostAscii = self::toAscii($normalizedHost);
        $publicSuffix = $this->psl->getPublicSuffix($hostAscii);

        if ($publicSuffix === null || $hostAscii === $publicSuffix) {
            return null;
        }

        $hostParts = explode('.', $hostAscii);
        $suffixParts = explode('.', $publicSuffix);
        $registrableParts = array_slice($hostParts, - (count($suffixParts) + 1));
        $registrableAscii = implode('.', $registrableParts);

        if ($utf8 && function_exists('idn_to_utf8')) {
            return idn_to_utf8($registrableAscii, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $registrableAscii;
        }
        return $registrableAscii;
    }

    /**
     * Validates if a cookie domain is appropriate for a given host per RFC 6265 & PSL rules.
     */
    public static function domainMatches(string $host, string $domain): bool
    {
        $host   = self::normalizeHost($host);
        $domain = self::normalizeHost(ltrim($domain, '.'));
        if ($domain === '') {
            return true;
        }
        if ($domain === 'localhost') {
            return false;
        }
        if (filter_var($host, FILTER_VALIDATE_IP) || filter_var($domain, FILTER_VALIDATE_IP)) {
            return false;
        }
        $usePSL = !defined('XOOPS_COOKIE_DOMAIN_USE_PSL') || XOOPS_COOKIE_DOMAIN_USE_PSL;
        if ($usePSL) {
            self::$pslInstance ??= new PublicSuffixList();
            if (self::$pslInstance->isPublicSuffix($domain)) {
                return false;
            }
            $regdomInstance = new self(self::$pslInstance);
            $hostRegisteredDomain = $regdomInstance->getRegisteredDomain($host, false);
            $domainRegisteredDomain = $regdomInstance->getRegisteredDomain($domain, false);
            if ($hostRegisteredDomain && $domainRegisteredDomain && $hostRegisteredDomain !== $domainRegisteredDomain) {
                return false;
            }
        }
        $host   = self::toAscii($host);
        $domain = self::toAscii($domain);
        if ($host === $domain) {
            return true;
        }
        return (strlen($host) > strlen($domain)) && (substr_compare($host, '.' . $domain, -1 - strlen($domain)) === 0);
    }

    private static function normalizeHost(string $input): string
    {
        $host = (strpos($input, '/') !== false) ? parse_url($input, PHP_URL_HOST) : $input;
        if (!is_string($host)) {
            $host = '';
        }
        $host = trim(mb_strtolower($host, 'UTF-8'));
        if ($host !== '' && $host[0] === '[') {
            $host = trim($host, '[]');
        }
        $host = preg_replace('/:\d+$/', '', $host) ?? $host;
        return rtrim($host, '.');
    }

    private static function toAscii(string $host): string
    {
        if ($host === '') {
            return '';
        }
        return function_exists('idn_to_ascii') ? (idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $host) : $host;
    }

    /**
     * @internal This method is for testing purposes only.
     */
    public static function setTestPslInstance(?PublicSuffixList $psl): void
    {
        self::$pslInstance = $psl;
    }
}
