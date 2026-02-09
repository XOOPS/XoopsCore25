<?php

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

declare(strict_types=1);

namespace Xoops\RegDom;

/**
 * Manages and queries the Public Suffix List (PSL) from a pre-generated cache.
 *
 * @package   Xoops\RegDom
 * @author    Florian Sager, 06.08.2008, <sager@agitos.de>
 * @author    Marcus Bointon (https://github.com/Synchro/regdom-php)
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Michael Beck <mamba@xoops.org>
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * @phpstan-type PslRules array{'NORMAL': array<string, true>, 'WILDCARD': array<string, true>, 'EXCEPTION': array<string, true>}
 */

class PublicSuffixList
{
    /**
     * @var array{'NORMAL': array<string, true>, 'WILDCARD': array<string, true>, 'EXCEPTION': array<string, true>}|null
     */
    private static ?array $rules = null;

    /**
     * @throws \Xoops\RegDom\Exception\PslCacheNotFoundException If no valid PSL cache file is found.
     */
    public function __construct()
    {
        if (self::$rules === null) {
            self::$rules = $this->loadRules();
        }
    }

    /**
     * @return array{'NORMAL': array<string, true>, 'WILDCARD': array<string, true>, 'EXCEPTION': array<string, true>}
     * @throws \Xoops\RegDom\Exception\PslCacheNotFoundException If no valid PSL cache file is found.
     */
    private function loadRules(): array
    {
        $paths = [];
        if (defined('XOOPS_VAR_PATH') && is_string(XOOPS_VAR_PATH) && XOOPS_VAR_PATH !== '') {
            $paths[] = XOOPS_VAR_PATH . '/cache/regdom/psl.cache.php';
        }
        $paths[] = __DIR__ . '/../data/psl.cache.php';

        foreach ($paths as $path) {
            if (is_file($path) && is_readable($path)) {
                $fileSize = filesize($path);
                if ($fileSize === false || $fileSize < 100000 || $fileSize > 10000000) {
                    continue;
                }

                $rules = include $path;

                if (
                    is_array($rules)
                    && isset($rules['NORMAL'], $rules['WILDCARD'], $rules['EXCEPTION'])
                    && is_array($rules['NORMAL'])
                    && is_array($rules['WILDCARD'])
                    && is_array($rules['EXCEPTION'])
                ) {
                    $totalRules = count($rules['NORMAL']) + count($rules['WILDCARD']);
                    if ($totalRules > 1000 && $totalRules < 100000) {
                        /** @var array{'NORMAL': array<string, true>, 'WILDCARD': array<string, true>, 'EXCEPTION': array<string, true>} $rules */
                        return $rules;
                    }
                }
            }
        }
        // Last resort: throw an exception instead of logging
        throw new \Xoops\RegDom\Exception\PslCacheNotFoundException('No valid PSL cache found. Run `composer run update-psl` to generate one.');
    }

    /**
     * Checks if a given domain is a public suffix (e.g., 'com', 'co.uk').
     */
    public function isPublicSuffix(string $domain): bool
    {
        $domain = self::normalizeDomain($domain);
        if ($domain === '' || filter_var($domain, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (isset(self::$rules['EXCEPTION'][$domain])) {
            return false;
        }
        if (isset(self::$rules['NORMAL'][$domain])) {
            return true;
        }

        $parts = explode('.', $domain);
        if (count($parts) >= 2) {
            array_shift($parts);
            $parent = implode('.', $parts);
            if (isset(self::$rules['WILDCARD'][$parent])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the public suffix portion of a full domain.
     */
    public function getPublicSuffix(string $domain): ?string
    {
        $domain = self::normalizeDomain($domain);
        if ($domain === '' || filter_var($domain, FILTER_VALIDATE_IP)) {
            return null;
        }

        $parts = explode('.', $domain);
        $n = count($parts);
        for ($i = 0; $i < $n; $i++) {
            $testSuffix = implode('.', array_slice($parts, $i));
            if (isset(self::$rules['EXCEPTION'][$testSuffix])) {
                // PSL exception: the public suffix is the exception rule minus its leftmost label.
                // e.g. exception "!city.kawasaki.jp" → public suffix is "kawasaki.jp"
                return ($i + 1 < $n) ? implode('.', array_slice($parts, $i + 1)) : null;
            }
            if (isset(self::$rules['NORMAL'][$testSuffix])) {
                return $testSuffix;
            }
            if ($i < $n - 1) {
                $parent = implode('.', array_slice($parts, $i + 1));
                if (isset(self::$rules['WILDCARD'][$parent])) {
                    return $testSuffix;
                }
            }
        }
        return null;
    }

    /**
     * Gets metadata about the loaded PSL cache, including a warning flag if the data is stale.
     *
     * @return array{
     * active_cache: string|null,
     * last_updated: string|null,
     * days_old: int|null,
     * rule_counts: array<string, int>,
     * needs_update: bool,
     * error?: string
     * } Metadata about the active cache.
     */
    public function getMetadata(): array
    {
        // Add a guard clause to handle the case where rules are not loaded.
        if (self::$rules === null) {
            return [
                'active_cache' => null,
                'last_updated' => null,
                'days_old'     => null,
                'rule_counts'  => ['normal' => 0, 'wildcard' => 0, 'exception' => 0],
                'needs_update' => true,
                'error'        => 'Rules not loaded',
            ];
        }

        $runtimePath = null;
        // Add is_string() to ensure the constant is safe to use.
        if (defined('XOOPS_VAR_PATH') && is_string(XOOPS_VAR_PATH) && XOOPS_VAR_PATH !== '') {
            $runtimePath = XOOPS_VAR_PATH . '/cache/regdom/psl.cache.php';
        }
        $bundledPath = __DIR__ . '/../data/psl.cache.php';

        $activeCache = null;
        $lastUpdated = null;

        if ($runtimePath && file_exists($runtimePath)) {
            $activeCache = 'runtime';
            $lastUpdated = filemtime($runtimePath);
        } elseif (file_exists($bundledPath)) {
            $activeCache = 'bundled';
            $lastUpdated = filemtime($bundledPath);
        }

        // Cast the result of floor() to an integer to match the docblock.
        $daysOld = $lastUpdated ? (int) floor((time() - $lastUpdated) / 86400) : null;

        $metadata = [
            'active_cache' => $activeCache,
            'last_updated' => $lastUpdated ? date('Y-m-d H:i:s T', $lastUpdated) : null,
            'days_old'     => $daysOld,
            'rule_counts'  => [
                'normal'    => count(self::$rules['NORMAL']),
                'wildcard'  => count(self::$rules['WILDCARD']),
                'exception' => count(self::$rules['EXCEPTION']),
            ],
            'needs_update' => false,
        ];

        if ($metadata['days_old'] && $metadata['days_old'] > 180) {
            $metadata['needs_update'] = true;
        }

        return $metadata;
    }

    /**
     * Normalizes a domain string for consistent processing.
     */
    private static function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        // Handles both leading and trailing dots
        $domain = trim($domain, '.');

        if ($domain !== '' && function_exists('idn_to_ascii')) {
            $domain = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $domain;
        }

        return $domain;
    }

    /**
     * Checks if a domain is an explicit exception in the PSL.
     * @param string $domain The domain to check.
     * @return bool
     */
    public function isException(string $domain): bool
    {
        $domain = self::normalizeDomain($domain);
        return isset(self::$rules['EXCEPTION'][$domain]);
    }
}
