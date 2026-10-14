<?php

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf;

/**
 * ProxyCheck
 *
 * Finds the client address in a proxy header. The header named in
 * $xoopsConfig['proxy_env'] is only used when the direct peer (REMOTE_ADDR)
 * is listed in $xoopsConfig['proxy_trusted']. Header entries are read from
 * right to left, skipping trusted proxies; the first untrusted entry is the
 * client. Any entry that is not an IP address stops the search.
 *
 * @category  Xmf\ProxyCheck
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
class ProxyCheck
{
    public const PROXY_ENVIRONMENT_VARIABLE = 'proxy_env';

    public const TRUSTED_PROXIES_VARIABLE = 'proxy_trusted';

    public const FORWARDED = 'HTTP_FORWARDED';

    /** @var string|false header name determines how to process */
    protected $proxyHeaderName = false;

    /** @var string|false header data to process */
    protected $proxyHeader = false;

    /** @var string[] trusted proxy addresses and CIDR ranges */
    protected $trustedProxies = [];

    /** @var string|false address of the direct peer */
    protected $remoteAddr = false;

    /**
     * ProxyCheck constructor.
     */
    public function __construct()
    {
        /* must declare expected proxy in $xoopsConfig['proxy_env'] */
        $this->proxyHeaderName = $this->getProxyEnvConfig();
        $this->proxyHeader = $this->getProxyHeader();
        $this->trustedProxies = $this->getTrustedProxiesConfig();
        $this->remoteAddr = (isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR']))
            ? $_SERVER['REMOTE_ADDR']
            : false;
    }

    /**
     * Get IP address from proxy header specified in $xoopsConfig['proxy_env']
     *
     * Returns proxy revealed valid client address, or false if such address was
     * not found or the request did not come from a trusted proxy.
     *
     * @return string|false
     */
    public function get()
    {
        if (false === $this->proxyHeaderName || !is_string($this->proxyHeader)) {
            return false;
        }
        if (!is_string($this->remoteAddr) || !$this->isTrustedProxy($this->remoteAddr)) {
            return false;
        }

        if ($this->proxyHeaderName === static::FORWARDED) {
            $candidates = $this->parseForwarded($this->proxyHeader);
        } else {
            $candidates = $this->splitOnComma($this->proxyHeader);
        }
        if (false === $candidates) {
            return false;
        }

        // walk from the proxy closest to us towards the client
        for ($i = count($candidates) - 1; $i >= 0; $i--) {
            $ip = $this->cleanNode($candidates[$i]);
            if (false === $ip) {
                return false;
            }
            if (!$this->isTrustedProxy($ip)) {
                return $this->validateRoutableIP($ip);
            }
        }

        return false;
    }

    /**
     * Split comma delimited string
     *
     * @param string $header
     *
     * @return string[]
     */
    protected function splitOnComma($header)
    {
        $parts = explode(',', $header);
        return array_map('trim', $parts);
    }

    /**
     * get configured proxy environment variable
     *
     * @return string|false
     */
    protected function getProxyEnvConfig()
    {
        global $xoopsConfig;

        /* must declare expected proxy in $xoopsConfig['proxy_env'] */
        if (
            !isset($xoopsConfig[static::PROXY_ENVIRONMENT_VARIABLE])
            || empty($xoopsConfig[static::PROXY_ENVIRONMENT_VARIABLE])
        ) {
            return false;
        }
        return trim($xoopsConfig[static::PROXY_ENVIRONMENT_VARIABLE]);
    }

    /**
     * get the trusted proxy list from $xoopsConfig['proxy_trusted']
     *
     * Accepts an array or a comma separated string of addresses and CIDR ranges.
     *
     * @return string[]
     */
    protected function getTrustedProxiesConfig()
    {
        global $xoopsConfig;

        if (!is_array($xoopsConfig) || !isset($xoopsConfig[self::TRUSTED_PROXIES_VARIABLE])) {
            return [];
        }
        $list = $xoopsConfig[self::TRUSTED_PROXIES_VARIABLE];
        if (is_string($list)) {
            $list = explode(',', $list);
        }
        if (!is_array($list)) {
            return [];
        }
        $trusted = [];
        foreach ($list as $entry) {
            if (is_string($entry) && trim($entry) !== '') {
                $trusted[] = trim($entry);
            }
        }
        return $trusted;
    }

    /**
     * get the configured proxy header
     *
     * @return string|false
     */
    protected function getProxyHeader()
    {
        if (false === $this->proxyHeaderName || empty($_SERVER[$this->proxyHeaderName])) {
            return false;
        }

        // Use PHP 5.3 compatible type casting
        return (string)$_SERVER[$this->proxyHeaderName];
    }

    /**
     * Get the 'for' node of each element of a FORWARDED header as in RFC 7239
     *
     * Delimiters inside quoted strings are respected and parameter names are
     * case-insensitive.
     *
     * @param string $header
     *
     * @return string[]|false one node per element, or false if the header is malformed
     *                        or an element does not have exactly one 'for' parameter
     */
    protected function parseForwarded($header)
    {
        $nodes = [];
        $element = [];
        $pair = '';
        $inQuotes = false;
        $length = strlen($header);
        for ($i = 0; $i <= $length; $i++) {
            $char = ($i < $length) ? $header[$i] : ',';
            if ($inQuotes) {
                if ($i === $length) {
                    return false;
                }
                if ($char === '\\' && $i + 1 < $length) {
                    $pair .= $header[++$i];
                } elseif ($char === '"') {
                    $inQuotes = false;
                } else {
                    $pair .= $char;
                }
                continue;
            }
            if ($char === '"') {
                $inQuotes = true;
            } elseif ($char === ';' || $char === ',') {
                $element[] = $pair;
                $pair = '';
                if ($char === ',') {
                    $node = $this->getForNode($element);
                    if (false === $node) {
                        return false;
                    }
                    $nodes[] = $node;
                    $element = [];
                }
            } else {
                $pair .= $char;
            }
        }

        return $nodes;
    }

    /**
     * Find the single 'for' value among the name=value pairs of one element
     *
     * @param string[] $pairs
     *
     * @return string|false
     */
    protected function getForNode(array $pairs)
    {
        $node = false;
        foreach ($pairs as $pair) {
            $parts = explode('=', $pair, 2);
            if (count($parts) === 2 && strcasecmp(trim($parts[0]), 'for') === 0) {
                if (false !== $node) {
                    return false;
                }
                $node = trim($parts[1]);
            }
        }

        return $node;
    }

    /**
     * Reduce a header node to a bare IP address, removing brackets and port
     *
     * @param string $node
     *
     * @return string|false IP address, or false if the node is not an IP address
     */
    protected function cleanNode($node)
    {
        $node = trim($node);
        // "[v6]", "[v6]:port" or "v4:port"; anything else after the address is rejected
        if (preg_match('/^\[([^\]]*)\](?::\d+)?\z/', $node, $matches)) {
            $node = $matches[1];
        } elseif (preg_match('/^([^:]*):\d+\z/', $node, $matches)) {
            $node = $matches[1];
        }

        return (false === filter_var($node, FILTER_VALIDATE_IP)) ? false : $node;
    }

    /**
     * Check an address against the trusted proxy list
     *
     * @param string $ip
     *
     * @return bool
     */
    protected function isTrustedProxy($ip)
    {
        $address = @inet_pton($ip);
        if (false === $address) {
            return false;
        }
        $address = $this->unmapIPv4($address);
        foreach ($this->trustedProxies as $entry) {
            $parts = explode('/', $entry, 2);
            $packed = @inet_pton(trim($parts[0]));
            if (false === $packed) {
                continue;
            }
            $network = $this->unmapIPv4($packed);
            if (strlen($network) !== strlen($address)) {
                continue;
            }
            $bits = strlen($address) * 8;
            if (isset($parts[1])) {
                $prefix = trim($parts[1]);
                if (!ctype_digit($prefix)) {
                    continue;
                }
                $prefix = (int) $prefix;
                if (strlen($packed) !== strlen($network)) {
                    // a mapped entry's prefix counts the 96 bit ::ffff:0:0 part
                    $prefix -= 96;
                }
                if ($prefix < 0 || $prefix > $bits) {
                    continue;
                }
                $bits = $prefix;
            }
            $bytes = intdiv($bits, 8);
            if (substr($address, 0, $bytes) !== substr($network, 0, $bytes)) {
                continue;
            }
            $rest = $bits % 8;
            if ($rest === 0) {
                return true;
            }
            $mask = (0xff << (8 - $rest)) & 0xff;
            if ((ord($address[$bytes]) & $mask) === (ord($network[$bytes]) & $mask)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reduce an IPv4-mapped IPv6 address (::ffff:a.b.c.d, as a dual-stack listener
     * reports an IPv4 peer) to its 4 byte IPv4 form so it matches IPv4 trust entries
     *
     * @param string $packed address from inet_pton()
     *
     * @return string
     */
    protected function unmapIPv4($packed)
    {
        if (strlen($packed) === 16 && strncmp($packed, "\0\0\0\0\0\0\0\0\0\0\xff\xff", 12) === 0) {
            return substr($packed, 12);
        }
        return $packed;
    }

    /**
     * Process an X-Forwarded-For or Client-IP style header
     *
     * @param string $ip expected to be an IP address
     *
     * @return string|false IP address, or false if invalid
     */
    protected function getXForwardedFor($ip)
    {
        return $this->validateRoutableIP($ip);
    }

    /**
     * Validate that an IP address is routable
     *
     * @param string $ip an IP address to validate
     *
     * @return string|false IP address or false if invalid
     */
    protected function validateRoutableIP($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }
        return $ip;
    }
}
