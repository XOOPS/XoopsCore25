<?php
namespace Geekwright\RegDom;

/**
 * Manage the Public Suffix List (PSL) data. This includes, downloading, converting to an array tree
 * structure for access in PHP, and caching the results.
 *
 * @package   Geekwright\RegDom
 * @author    Florian Sager, 06.08.2008, <sager@agitos.de>
 * @author    Marcus Bointon (https://github.com/Synchro/regdom-php)
 * @author    Richard Griffith <richard@geekwright.com>
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
class PublicSuffixList
{
    protected $sourceURL = 'https://publicsuffix.org/list/public_suffix_list.dat';
    protected $localPSL = 'public_suffix_list.dat';
    protected $cachedPrefix = 'cached_';

    protected $tree;
    protected $url;
    protected $dataDir = '/../data/'; // relative to __DIR__

    /**
     * PublicSuffixList constructor.
     * @param string|null $url URL for the PSL or null to use default
     */
    public function __construct($url = null)
    {
        $this->setURL($url);
    }

    /**
     * Set the URL, and clear any existing tree
     *
     * @param string|null $url URL for the PSL or null to use default
     *
     * @return void
     */
    public function setURL($url)
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
    protected function setFallbackURL()
    {
        $this->setLocalPSLName($this->url);
        if (null === $this->url) {
            $this->url = file_exists(__DIR__ . $this->localPSL) ? $this->localPSL : $this->sourceURL;
        }
    }

    /**
     * load the PSL tree, automatically handling caches
     *
     * @return void (results in $this->tree)
     *
     * @throws \RuntimeException
     */
    protected function loadTree()
    {
        $this->setFallbackURL();

        $this->tree = $this->readCachedPSL($this->url);
        if (false !== $this->tree) {
            return;
        }

        $this->tree = array();
        $list = $this->readPSL();

        if (false===$list) {
            $e = new \RuntimeException('Cannot read ' . $this->url);
            throw $e;
        }

        $this->parsePSL($list);
        $this->cachePSL($this->url);
    }

    /**
     * Parse the PSL data
     *
     * @param string $fileData the PSL data
     *
     * @return void (results in $this->tree)
     */
    protected function parsePSL($fileData)
    {
        $lines = explode("\n", $fileData);

        foreach ($lines as $line) {
            if ($this->startsWith($line, "//") || $line == '') {
                continue;
            }

            // this line should be a TLD
            $tldParts = explode('.', $line);

            $this->buildSubDomain($this->tree, $tldParts);
        }
    }

    /**
     * Does $search start with $startString?
     *
     * @param string $search      the string to test
     * @param string $startString the starting string to match
     *
     * @return bool
     */
    protected function startsWith($search, $startString)
    {
        return (substr($search, 0, strlen($startString)) == $startString);
    }

    /**
     * Add domains to tree
     *
     * @param array    $node     tree array by reference
     * @param string[] $tldParts array of domain parts
     *
     * @return void - changes made to $node by reference
     */
    protected function buildSubDomain(&$node, $tldParts)
    {
        $dom = trim(array_pop($tldParts));

        $isNotDomain = false;
        if ($this->startsWith($dom, "!")) {
            $dom = substr($dom, 1);
            $isNotDomain = true;
        }

        if (!array_key_exists($dom, $node)) {
            if ($isNotDomain) {
                $node[$dom] = array("!" => "");
            } else {
                $node[$dom] = array();
            }
        }

        if (!$isNotDomain && count($tldParts) > 0) {
            $this->buildSubDomain($node[$dom], $tldParts);
        }
    }

    /**
     * Return the current tree, loading it if needed
     *
     * @return array the PSL tree
     */
    public function getTree()
    {
        if (null===$this->tree) {
            $this->loadTree();
        }
        return $this->tree;
    }

    /**
     * Read PSL from the URL or file specified in $this->url.
     * If we process a remote URL, save a local copy.
     *
     * @return bool|string PSL file contents or false on error
     */
    protected function readPSL()
    {
        $parts = parse_url($this->url);
        $remote = isset($parts['scheme']) || isset($parts['host']);
        // try to read with file_get_contents
        $newPSL = file_get_contents(($remote ? '' : __DIR__) . $this->url);
        if (false !== $newPSL) {
            if ($remote) {
                $this->saveLocalPSL($newPSL);
            }
            return $newPSL;
        }

        // try again with curl if file_get_contents failed
        if (function_exists('curl_init') && false !== ($curlHandle  = curl_init())) {
            curl_setopt($curlHandle, CURLOPT_URL, $this->url);
            curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);
            $curlReturn = curl_exec($curlHandle);
            curl_close($curlHandle);
            if (false !== $curlReturn) {
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
     *
     * @return string cache file name for given resource
     */
    protected function getCacheFileName($url)
    {
        return __DIR__ . $this->dataDir . $this->cachedPrefix . md5($url);
    }

    /**
     * Attempt to load a cached Public Suffix List tree for a given source
     *
     * @param string $url URL/filename of source PSL
     *
     * @return bool|string[] PSL tree
     */
    protected function readCachedPSL($url)
    {
        $cacheFile = $this->getCacheFileName($url);
        if (file_exists($cacheFile)) {
            $cachedTree = file_get_contents($cacheFile);
            return unserialize($cachedTree);
        }
        return false;
    }

    /**
     * Cache the current Public Suffix List tree and associate with the specified source
     *
     * @param string $url URL/filename of source PSL
     *
     * @return bool|int the number of bytes that were written to the file, or false on failure
     */
    protected function cachePSL($url)
    {
        return file_put_contents($this->getCacheFileName($url), serialize($this->tree));
    }

    /**
     * Save a local copy of a retrieved Public Suffix List
     *
     * @param string $fileContents URL/filename of source PSL
     *
     * @return bool|int the number of bytes that were written to the file, or false on failure
     */
    protected function saveLocalPSL($fileContents)
    {
        return file_put_contents(__DIR__ . $this->localPSL, $fileContents);
    }

    /**
     * Set localPSL name based on URL
     *
     * @param null|string $url the URL for the PSL
     *
     * @return void (sets $this->localPSL)
     */
    protected function setLocalPSLName($url)
    {
        if (null === $url) {
            $url = $this->sourceURL;
        }
        $parts = parse_url($url);
        $fileName = basename($parts['path']);
        $this->localPSL = $this->dataDir . $fileName;
    }

    /**
     * Delete files in the data directory
     *
     * @param bool $cacheOnly true to limit clearing to cached serialized PSLs, false to clear all
     *
     * @return void
     */
    public function clearDataDirectory($cacheOnly = false)
    {
        $dir = __DIR__ . $this->dataDir;
        if (is_dir($dir)) {
            if ($dirHandle = opendir($dir)) {
                while (($file = readdir($dirHandle)) !== false) {
                    if (filetype($dir . $file) === 'file'
                        && (false === $cacheOnly || $this->startsWith($file, $this->cachedPrefix)))
                    {
                        unlink($dir . $file);
                    }
                }
                closedir($dirHandle);
            }
        }
    }
}
