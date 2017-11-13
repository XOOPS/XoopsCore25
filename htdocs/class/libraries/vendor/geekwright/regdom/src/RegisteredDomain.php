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
            $utf8Host = $utf8Host . (($utf8Host === '') ? '' : '.') . $this->convertPunycode($part);
        }

        return mb_strtolower($utf8Host);
    }

    /**
     * Convert a punycode string to UTF-8 if needed
     *
     * @param string $part host component
     *
     * @return string host component as UTF-8
     */
    protected function convertPunycode($part)
    {
        if (strpos($part, 'xn--')===0) {
            if (function_exists('idn_to_utf8')) {
                if (defined('INTL_IDNA_VARIANT_UTS46')) { // PHP 7.2
                    return idn_to_utf8($part, 0, INTL_IDNA_VARIANT_UTS46);
                }
                return idn_to_utf8($part);
            } else {
                return $this->decodePunycode($part);
            }
        }
        return $part;
    }

    /**
     * convert punycode to UTF-8 (the hard way) Used only if idn_to_utf8() is not available
     *
     * This fallback adapted from https://ckon.wordpress.com/2010/08/24/punycode-to-unicode-converter-php/
     *
     * @param string $encoded
     * @return string
     */
    protected function decodePunycode($encoded)
    {
        $prefix = 'xn--';
        $safe_char = 0xFFFC;
        $base = 36;
        $tmin = 1;
        $tmax = 26;
        $skew = 38;
        $damp = 700;

        if (strpos($encoded, $prefix) !== 0 || strlen(trim(str_replace($prefix, '', $encoded))) == 0) {
            return $encoded;
        }

        $is_first = true;
        $bias = 72;
        $idx = 0;
        $char = 0x80;
        $decoded = array();
        $output = '';

        $delim_pos = strrpos($encoded, '-');
        if ($delim_pos > strlen($prefix)) {
            for ($k = strlen($prefix); $k < $delim_pos; ++$k) {
                $decoded[] = ord($encoded{$k});
            }
        }
        $deco_len = count($decoded);
        $enco_len = strlen($encoded);

        for ($enco_idx = $delim_pos ? ($delim_pos + 1) : 0; $enco_idx < $enco_len; ++$deco_len) {
            for ($old_idx = $idx, $w = 1, $k = $base; 1; $k += $base) {
                $cp = ord($encoded{$enco_idx++});
                $digit = ($cp - 48 < 10) ? $cp - 22 : (($cp - 65 < 26) ? $cp - 65 : (($cp - 97 < 26) ? $cp - 97 : $base));
                $idx += $digit * $w;
                $t = ($k <= $bias) ? $tmin : (($k >= $bias + $tmax) ? $tmax : ($k - $bias));
                if ($digit < $t) {
                    break;
                }
                $w = (int)($w * ($base - $t));
            }
            $delta = $idx - $old_idx;
            $delta = intval($is_first ? ($delta / $damp) : ($delta / 2));
            $delta += intval($delta / ($deco_len + 1));
            for ($k = 0; $delta > (($base - $tmin) * $tmax) / 2; $k += $base) {
                $delta = intval($delta / ($base - $tmin));
            }
            $bias = intval($k + ($base - $tmin + 1) * $delta / ($delta + $skew));
            $is_first = false;
            $char += (int)($idx / ($deco_len + 1));
            $idx %= ($deco_len + 1);
            if ($deco_len > 0) {
                for ($i = $deco_len; $i > $idx; $i--) {
                    $decoded[$i] = $decoded[($i - 1)];
                }
            }
            $decoded[$idx++] = $char;
        }

        foreach ($decoded as $k => $v) {
            if ($v < 128) {
                $output .= chr($v);
            } // 7bit are transferred literally
            elseif ($v < (1 << 11)) {
                $output .= chr(192 + ($v >> 6)) . chr(128 + ($v & 63));
            } // 2 bytes
            elseif ($v < (1 << 16)) {
                $output .= chr(224 + ($v >> 12)) . chr(128 + (($v >> 6) & 63)) . chr(128 + ($v & 63));
            } // 3 bytes
            elseif ($v < (1 << 21)) {
                $output .= chr(240 + ($v >> 18)) . chr(128 + (($v >> 12) & 63)) . chr(128 + (($v >> 6) & 63)) . chr(128 + ($v & 63));
            } // 4 bytes
            else {
                $output .= $safe_char;
            } //  'Conversion from UCS-4 to UTF-8 failed: malformed input at byte '.$k
        }
        return $output;
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
