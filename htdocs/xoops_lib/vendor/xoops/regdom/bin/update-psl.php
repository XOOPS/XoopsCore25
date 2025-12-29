#!/usr/bin/env php
<?php
/**
 * XOOPS RegDom Public Suffix List Updater
 *
 * Downloads and caches the Mozilla Public Suffix List.
 * This script is safe to run in any environment and handles multiple failure scenarios gracefully.
 *
 * @license The PSL is licensed under MPL-2.0 by Mozilla Foundation
 * @source  https://publicsuffix.org/
 */

echo "Updating XOOPS RegDom Public Suffix List...\n";

// --- Configuration ---
$sourceUrl = 'https://publicsuffix.org/list/public_suffix_list.dat';
$packageDir = dirname(__DIR__);
$dataDir = $packageDir . '/data';
$bundledCachePath = $dataDir . '/psl.cache.php';
$metaPath = $dataDir . '/psl.meta.json';
$runtimeCachePath = null;

// Determine runtime cache path if in a XOOPS context
if (defined('XOOPS_VAR_PATH')) {
    $runtimeCacheDir = XOOPS_VAR_PATH . '/cache/regdom';
    if (!is_dir($runtimeCacheDir)) {
        if (!mkdir($runtimeCacheDir, 0777, true) && !is_dir($runtimeCacheDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $runtimeCacheDir));
        }
    }
    if (is_writable($runtimeCacheDir)) {
        $runtimeCachePath = $runtimeCacheDir . '/psl.cache.php';
    } else {
        echo "WARNING: Runtime cache directory is not writable: {$runtimeCacheDir}\n";
    }
}

// --- HTTP Conditional Download ---
$headers = ['User-Agent: XOOPS-RegDom/1.1 (https://xoops.org)'];
$meta = file_exists($metaPath) ? json_decode(file_get_contents($metaPath), true) : [];
if (!empty($meta['etag'])) $headers[] = "If-None-Match: {$meta['etag']}";
if (!empty($meta['last_modified'])) $headers[] = "If-Modified-Since: {$meta['last_modified']}";

echo "Downloading from publicsuffix.org...\n";
$context = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 20, 'header' => implode("\r\n", $headers), 'ignore_errors' => true]]);
$latestList = @file_get_contents($sourceUrl, false, $context);
$responseHeaders = $http_response_header ?? [];
$statusCode = 0;
foreach ($responseHeaders as $header) if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $header, $m)) $statusCode = (int)$m[1];

if ($statusCode === 304) {
    echo "SUCCESS: Public Suffix List is already up-to-date (304 Not Modified).\n";
    exit(0);
}

if ($latestList === false || $statusCode !== 200) {
    echo "WARNING: Failed to download PSL (HTTP {$statusCode}). Keeping existing cache.\n";
    exit(0); // Not a fatal error; existing cache is still valid.
}

// --- Parse and Generate Cache ---
echo "Parsing rules...\n";
$lines = explode("\n", $latestList);
$rules = ['NORMAL' => [], 'WILDCARD' => [], 'EXCEPTION' => []];
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '//') === 0) continue;
    if (strpos($line, '!') === 0) $rules['EXCEPTION'][substr($line, 1)] = true;
    elseif (strpos($line, '*.') === 0) $rules['WILDCARD'][substr($line, 2)] = true;
    else $rules['NORMAL'][$line] = true;
}

$totalRules = count($rules['NORMAL']) + count($rules['WILDCARD']) + count($rules['EXCEPTION']);
if ($totalRules < 1000) {
    echo "ERROR: Parsed rule count ({$totalRules}) is suspiciously low. Aborting update.\n";
    exit(1);
}

$cacheHeader = "<?php\n/**\n * Public Suffix List Cache\n * Generated: " . date('Y-m-d H:i:s T') . "\n * Source: {$sourceUrl}\n * License: Mozilla Public License 2.0 (https://publicsuffix.org/)\n */\n";
$cacheContent = $cacheHeader . "\nreturn " . var_export($rules, true) . ";\n";

// --- Atomic Write to Caches ---
$writePaths = ['bundled' => $bundledCachePath];
if ($runtimeCachePath) $writePaths['runtime'] = $runtimeCachePath;

foreach ($writePaths as $type => $cachePath) {
    $tmpPath = $cachePath . '.tmp.' . getmypid();
    if (file_put_contents($tmpPath, $cacheContent, LOCK_EX) && rename($tmpPath, $cachePath)) {
        echo "SUCCESS: {$type} cache updated with {$totalRules} rules.\n";
    } else {
        echo "WARNING: Could not write {$type} cache to {$cachePath}.\n";
        // Attempt to delete the temporary file.
        if (file_exists($tmpPath) && !unlink($tmpPath)) {
            // If unlink() fails, throw an exception to halt execution and signal an error.
            throw new \RuntimeException(
                    "CRITICAL: Failed to delete temporary file at: {$tmpPath}. Please check file permissions."
            );
        }
    }
}

// --- Save Metadata ---
$newMeta = ['updated' => date('c'), 'etag' => null, 'last_modified' => null];
foreach ($responseHeaders as $header) {
    if (stripos($header, 'ETag:') === 0) $newMeta['etag'] = trim(substr($header, 5));
    if (stripos($header, 'Last-Modified:') === 0) $newMeta['last_modified'] = trim(substr($header, 14));
}
file_put_contents($metaPath, json_encode($newMeta, JSON_PRETTY_PRINT));

echo "Update complete.\n";
exit(0);
