<?php
namespace Geekwright\RegDom;

/**
 * Class RegisteredDomain
 *
 * Determine the registrable domain portion of a URL, respecting the public suffix list conventions
 *
 * @package   Geekwright\RegDom
 * @author    Florian Sager, 06.08.2008, <sager@agitos.de>
 * @author    Marcus Bointon (https://github.com/Synchro/regdom-php)
 * @author    Richard Griffith <richard@geekwright.com>
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
class RegisteredDomain
{
    protected $tree;
    protected $psl;

    /**
     * RegisteredDomain constructor.
     *
     * @param PublicSuffixList|null $psl PublicSuffixList object, or null to use defaults
     */
    public function __construct(PublicSuffixList $psl = null)
    {
        if (null === $psl) {
            $psl = new PublicSuffixList();
        }
        $this->psl = $psl;
    }

    /**
     * Given a URL or bare host name, return a normalized host name, converting punycode to UTF-8
     * and converting to lower case
     *
     * @param string $url URL or host name
     *
     * @return string
     */
    protected function normalizeHost($url)
    {
        $host = (false!==strpos($url, '/')) ? parse_url($url, PHP_URL_HOST) : $url;
        $parts = explode('.', $host);
        $utf8Host = '';
        foreach ($parts as $part) {
            $utf8Host = $utf8Host . (($utf8Host === '') ? '' : '.') . idn_to_utf8($part);
        }

        return mb_strtolower($utf8Host);
    }

    /**
     * Determine the registered domain portion of the supplied host string
     *
     * @param string $host a host name or URL containing a host name
     *
     * @return string|null shortest registrable domain portion of the supplied host or null if invalid
     */
    public function getRegisteredDomain($host)
    {
        $this->tree = $this->psl->getTree();

        $signingDomain = $this->normalizeHost($host);
        $signingDomainParts = explode('.', $signingDomain);

        $result = $this->findRegisteredDomain($signingDomainParts, $this->tree);

        if (empty($result)) {
            // this is an invalid domain name
            return null;
        }

        // assure there is at least 1 TLD in the stripped signing domain
        if (!strpos($result, '.')) {
            $cnt = count($signingDomainParts);
            if ($cnt == 1 || $signingDomainParts[$cnt-2] == '') {
                return null;
            }
            return $signingDomainParts[$cnt-2] . '.' . $signingDomainParts[$cnt-1];
        }
        return $result;
    }

    /**
     * Recursive helper method to query the PSL tree
     *
     * @param string[] $remainingSigningDomainParts parts of domain being queried
     * @param string[] $treeNode                    subset of tree array by reference
     *
     * @return null|string
     */
    protected function findRegisteredDomain($remainingSigningDomainParts, &$treeNode)
    {
        $sub = array_pop($remainingSigningDomainParts);

        $result = null;
        if (isset($treeNode['!'])) {
            return '';
        } elseif (is_array($treeNode) && array_key_exists($sub, $treeNode)) {
            $result = $this->findRegisteredDomain($remainingSigningDomainParts, $treeNode[$sub]);
        } elseif (is_array($treeNode) && array_key_exists('*', $treeNode)) {
            $result = $this->findRegisteredDomain($remainingSigningDomainParts, $treeNode['*']);
        } else {
            return $sub;
        }

        if ($result === '') {
            return $sub;
        } elseif (strlen($result)>0) {
            return $result . '.' . $sub;
        }
        return null;
    }
}
