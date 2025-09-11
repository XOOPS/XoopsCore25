<?php declare(strict_types=1);

namespace Xoops\RegDom;

/**
 * Manage the Public Suffix List (PSL) data. This includes, downloading, converting to an array tree
 * structure for access in PHP, and caching the results.
 *
 * @package   Xoops\RegDom
 * @author    Florian Sager, 06.08.2008, <sager@agitos.de>
 * @author    Marcus Bointon (https://github.com/Synchro/regdom-php)
 * @author    Richard Griffith <richard@geekwright.com>
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
class PublicSuffixList
{
    private string $sourceURL = 'https://publicsuffix.org/list/public_suffix_list.dat';
    private string $localPSL = 'public_suffix_list.dat';
    private string $cachedPrefix = 'cached_';
    private ?array $tree = null;
    private ?string $url = null;
    private string $dataDir = '/../data/'; // relative to __DIR__
    /**
     * PublicSuffixList constructor.
     * @param string|null $url URL for the PSL or null to use default
     */
    public function __construct(?string $url = null)
    {
        $this->setURL($url);
    }

    /**
     * Set the URL, and clear any existing tree
     *
     * @param string|null $url URL for the PSL or null to use default
     * @return void
     */
    public function setURL(?string $url): void
    {
        $this->url = $url;
        $this->tree = null;
    }

    /**
     * Set a fallback (default) for the URL. If we have a locally saved version, prefer it, but use a
     * remote URL if there is no local source.
     *
     * @return void
     */
    private function setFallbackURL(): void
    {
        $this->setLocalPSLName($this->url);
        if (null === $this->url) {
            $this->url = \file_exists(__DIR__ . $this->localPSL) ? $this->localPSL : $this->sourceURL;
        }
    }

    /**
     * Load the PSL tree, automatically handling caches
     *
     * @return void (results in $this->tree)
     * @throws \RuntimeException
     */
    private function loadTree(): void
    {
        $this->setFallbackURL();

        $this->tree = $this->readCachedPSL($this->url);
        if (null !== $this->tree) {
            return;
        }

        $this->tree = [];
        $list = $this->readPSL();

        if (false === $list) {
            throw new \RuntimeException('Cannot read ' . $this->url);
        }

        $this->parsePSL($list);
        $this->cachePSL($this->url);
    }

    /**
     * Parse the PSL data
     *
     * @param string $fileData the PSL data
     * @return void (results in $this->tree)
     */
    private function parsePSL(string $fileData): void
    {
        $lines = \explode("\n", $fileData);

        foreach ($lines as $line) {
            if ('' === $line || $this->startsWith($line, '//'))   {
                continue;
            }

            // Ensure $this->tree is an array
            if (null === $this->tree) {
                $this->tree = [];
            }

            // This line should be a TLD
            $tldParts = \explode('.', $line);

            $this->buildSubDomain($this->tree, $tldParts);
        }
    }

    /**
     * Does $search start with $startString?
     *
     * @param string $search the string to test
     * @param string $startString the starting string to match
     * @return bool
     */
    private function startsWith(string $search, string $startString): bool
    {
        return (0 === \strpos($search, $startString));
    }

    /**
     * Add domains to tree
     *
     * @param array $node tree array by reference
     * @param string[] $tldParts array of domain parts
     * @return void - changes made to $node by reference
     */
    private function buildSubDomain(array &$node, array $tldParts): void
    {
        $dom = \trim(\array_pop($tldParts));

        $isNotDomain = false;
        if ($this->startsWith($dom, '!')) {
            $dom = \substr($dom, 1);
            $isNotDomain = true;
        }

        if (!\array_key_exists($dom, $node)) {
            if ($isNotDomain) {
                $node[$dom] = ['!' => ''];
            } else {
                $node[$dom] = [];
            }
        }

        if (!$isNotDomain && 0 < \count($tldParts)) {
            $this->buildSubDomain($node[$dom], $tldParts);
        }
    }

    /**
     * Return the current tree, loading it if needed
     *
     * @return array the PSL tree
     * @throws \RuntimeException if PSL cannot be loaded
     */
    public function getTree(): array
    {
        if (null === $this->tree) {
            $this->loadTree();
        }
        return $this->tree;
    }

    /**
     * Read PSL from the URL or file specified in $this->url.
     * If we process a remote URL, save a local copy.
     *
     * @return string|false PSL file contents or false on error
     */
    private function readPSL()
    {
        $parts = \parse_url($this->url);
        $remote = \is_array($parts) && !empty($parts) && (isset($parts['scheme']) || isset($parts['host']));
        // try to read with file_get_contents
        $newPSL = \file_get_contents(($remote ? '' : __DIR__) . $this->url);
        if (false !== $newPSL) {
            if ($remote) {
                $this->saveLocalPSL($newPSL);
            }
            return $newPSL;
        }

        // try again with curl if file_get_contents failed
        if (\function_exists('curl_init') && false !== ($curlHandle = \curl_init())) {
            \curl_setopt($curlHandle, \CURLOPT_URL, $this->url);
            \curl_setopt($curlHandle, \CURLOPT_FAILONERROR, true);
            \curl_setopt($curlHandle, \CURLOPT_RETURNTRANSFER, 1);
            \curl_setopt($curlHandle, \CURLOPT_CONNECTTIMEOUT, 5);
            $curlReturn = \curl_exec($curlHandle);
            \curl_close($curlHandle);
            if (false !== $curlReturn && \is_string($curlReturn)) {
                if ($remote) {
                    $this->saveLocalPSL($curlReturn);
                }
                return $curlReturn;
            }
        }
        return false;
    }

    /**
     * Determine cache file name for a specified source
     *
     * @param string $url URL/filename of source PSL
     * @return string cache file name for given resource
     */
    private function getCacheFileName(string $url): string
    {
        return __DIR__ . $this->dataDir . $this->cachedPrefix . \md5($url);
    }

    /**
     * Attempt to load a cached Public Suffix List tree for a given source
     *
     * @param string $url URL/filename of source PSL
     * @return array|null PSL tree
     */
    private function readCachedPSL(string $url): ?array
    {
        $cacheFile = $this->getCacheFileName($url);
        return \file_exists($cacheFile)
            ? \unserialize(\file_get_contents($cacheFile), ['allowed_classes' => false])
            : null;
    }

    /**
     * Cache the current Public Suffix List tree and associate with the specified source
     *
     * @param string $url URL/filename of source PSL
     * @return bool|int the number of bytes that were written to the file, or false on failure
     */
    private function cachePSL(string $url)
    {
        return \file_put_contents($this->getCacheFileName($url), \serialize($this->tree));
    }

    /**
     * Save a local copy of a retrieved Public Suffix List
     *
     * @param string $fileContents URL/filename of source PSL
     * @return bool|int the number of bytes that were written to the file, or false on failure
     */
    private function saveLocalPSL(string $fileContents)
    {
        return \file_put_contents(__DIR__ . $this->localPSL, $fileContents);
    }

    /**
     * Set localPSL name based on URL
     *
     * @param string|null $url the URL for the PSL
     * @return void (sets $this->localPSL)
     */
    private function setLocalPSLName(?string $url): void
    {
        if (null === $url) {
            $url = $this->sourceURL;
        }
        $parts = \parse_url($url);
        $fileName = \basename($parts['path']);
        $this->localPSL = $this->dataDir . $fileName;
    }

    /**
     * Delete files in the data directory
     *
     * @param bool $cacheOnly true to limit clearing to cached serialized PSLs, false to clear all
     * @return void
     */
    public function clearDataDirectory(bool $cacheOnly = false): void
    {
        $dir = __DIR__ . $this->dataDir;
        if (\is_dir($dir)) {
            if (false !== ($dirHandle = \opendir($dir))) {
                while (false !== ($file = \readdir($dirHandle))) {
                    if ('file' === \filetype($dir . $file)
                        && (!$cacheOnly || $this->startsWith($file, $this->cachedPrefix))) {
                        \unlink($dir . $file);
                    }
                }
                \closedir($dirHandle);
            }
        }
    }
}
