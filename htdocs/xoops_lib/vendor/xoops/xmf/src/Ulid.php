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
 * Generate ULID
 *
 * @category  Xmf\Ulid
 * @package   Xmf
 * @author    Michael Beck <mambax7@gmail.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
class Ulid
{
    const ENCODING_CHARS = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    const ENCODING_LENGTH = 32;

    /**
     * Generate a new ULID.
     *
     * @return string The generated ULID.
     */
    public static function generate($upperCase = true)
    {
        $time = self::microtimeToUlidTime(\microtime(true));
        $timeChars = self::encodeTime($time);
        $randChars = self::encodeRandomness();
        $ulid      = $timeChars . $randChars;

        $ulid = $upperCase ? \strtoupper($ulid) : \strtolower($ulid);

        return $ulid;
    }

    /**
     * @param int $time
     *
     * @return string
     */
    public static function encodeTime($time)
    {
        $encodingCharsArray = str_split(self::ENCODING_CHARS);
        $timeChars = '';
        for ($i = 0; $i < 10; $i++) {
            $mod = \floor($time % self::ENCODING_LENGTH);
            $timeChars = $encodingCharsArray[$mod] . $timeChars;
            $time = (int)(($time - $mod) / self::ENCODING_LENGTH);
        }
        return $timeChars;
    }

    public static function encodeRandomness()
    {
        $encodingCharsArray = str_split(self::ENCODING_CHARS);
        $randomBytes = \random_bytes(10); // 80 bits
        // Check if the random bytes were generated successfully.
        if (false === $randomBytes) {
            throw new \RuntimeException('Failed to generate random bytes');
        }

        $randChars   = '';
        for ($i = 0; $i < 16; $i++) {
            $randValue = \ord($randomBytes[$i % 10]);
            if (0 === $i % 2) {
                $randValue >>= 3; // take the upper 5 bits
            } else {
                $randValue &= 31; // take the lower 5 bits
            }
            $randChars .= $encodingCharsArray[$randValue];
        }
        return $randChars;
    }

    /**
     * @param string $ulid
     *
     * @return array
     */
    public static function decode($ulid)
    {
        if (!self::isValid($ulid)) {
            throw new \InvalidArgumentException('Invalid ULID string');
        }

        $time = self::decodeTime($ulid);
        $rand = self::decodeRandomness($ulid);

        return [
            'time' => $time,
            'rand' => $rand,
        ];
    }

    /**
     * @param string $ulid
     *
     * @return int
     */
    public static function decodeTime($ulid)
    {
//        $encodingCharsArray = str_split(self::ENCODING_CHARS);

        // Check if the ULID string is valid.
        if (!self::isValid($ulid)) {
            throw new \InvalidArgumentException('Invalid ULID string');
        }

        $time = 0;
        for ($i = 0; $i < 10; $i++) {
            $char = $ulid[$i];
            $value = \strpos(self::ENCODING_CHARS, $char);
            $exponent = 9 - $i;
            $time += $value * \bcpow((string)self::ENCODING_LENGTH, (string)$exponent);
        }

        return $time;
    }

    /**
     * @param string $ulid
     *
     * @return int
     */
    public static function decodeRandomness($ulid)
    {
        if (26 !== strlen($ulid)) {
            throw new \InvalidArgumentException('Invalid ULID length');  // Changed line
        }

        $rand = 0;
        for ($i = 10; $i < 26; $i++) {
            $char = $ulid[$i];
            $value = \strpos(self::ENCODING_CHARS, $char);

            // Check if the random value is within the valid range.
            if ($value < 0 || $value >= self::ENCODING_LENGTH) {
                throw new \InvalidArgumentException('Invalid ULID random value');
            }
            $exponent = 15 - $i;
            $rand += $value * \bcpow((string)self::ENCODING_LENGTH, (string)$exponent);
        }

        return $rand;
    }

    /**
     * @param string $ulid
     *
     * @return bool
     */
    public static function isValid($ulid)
    {
        // Check the length of the ULID string before throwing an exception.
        if (26 !== strlen($ulid)) {
            return false;
        }

        // Throw an exception if the ULID is invalid.
        try {
            self::decodeRandomness($ulid);
        } catch (\InvalidArgumentException $e) {
                return false;
}

        return true;
    }

    /**
     * @param float $microtime
     *
     * @return int
     */
    public static function microtimeToUlidTime($microtime)
    {
        $timestamp = $microtime * 1000000;
        $unixEpoch = 946684800000000; // Microseconds since the Unix epoch.

        return (int)($timestamp - $unixEpoch);
    }
}
