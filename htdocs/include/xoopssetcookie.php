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
 * Near drop-in replacement for PHP's setcookie()
 *
 * @copyright       Copyright 2021 The XOOPS Project https://xoops.org
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author          Richard Griffith <richard@geekwright.com>
 *
 * This exists to bring samesite support to php versions before 7.3, and
 * it treats the default as samesite=strict
 *
 * It supports both of the two declared signatures:
 * - setcookie ( string $name , string $value = "" , int $expires = 0 , string $path = "" , string $domain = "" , bool $secure = false , bool $httponly = false ) : bool
 * - setcookie ( string $name , string $value = "" , array $options = [] ) : bool
 */
function xoops_setcookie()
{
    if (headers_sent()) {
        return false;
    }
    $argNames    = array('name', 'value', 'expires', 'path', 'domain', 'secure', 'httponly');
    //$argDefaults = array(null,   '',       0,        '',     '',        false,    false);
    //$optionsKeys = array('expires', 'path', 'domain', 'secure', 'httponly', 'samesite');
    $rawArgs = func_get_args();
    $args = array();
    foreach ($rawArgs as $key => $value) {
        if (2 === $key && \is_array($value)) {
            // modern call
            $args['options'] = array();
            foreach ($value as $optionKey => $optionValue) {
                $args['options'][strtolower($optionKey)] = $optionValue;
            }
            break;
        }
        if ($key>1) {
            if (null !== $value) {
                $args['options'][$argNames[$key]] = $value;
            }
        } else {
            $args[$argNames[$key]] = $value;
        }
    }

    // make samesite=strict the default
    $args['options']['samesite'] = isset($args['options']['samesite']) ? $args['options']['samesite'] : 'strict';
    if (!isset($args['value'])){
        $args['value'] = '';
    }
    // after php 7.3 we just let php do it
    if (PHP_VERSION_ID >= 70300) {
        return setcookie($args['name'], (string)$args['value'], $args['options']);
    }
    // render and send our own headers below php 7.3
    header(xoops_buildCookieHeader($args), false);
    return true;
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
