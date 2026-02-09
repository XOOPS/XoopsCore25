![alt XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)

# RegDom - Registered Domain Check in PHP

Object oriented PHP library for querying Mozilla's Public Suffix List to
determine the registrable domain portion of URLs and validate cookie domains
per RFC 6265. Adapted from Florian Sager's regdom library.

For more information on public suffixes, see [publicsuffix.org](https://publicsuffix.org/).

## Requirements

- PHP 7.4 or later
- `ext-intl` recommended (for internationalized domain name support)

## Installation

```bash
composer require xoops/regdom
```

## Usage

### Extracting the Registered Domain

```php
use Xoops\RegDom\RegisteredDomain;

$regdom = new RegisteredDomain();

echo $regdom->getRegisteredDomain('https://www.google.com/');
// Output: google.com

echo $regdom->getRegisteredDomain('theregister.co.uk');
// Output: theregister.co.uk

var_dump($regdom->getRegisteredDomain('co.uk'));
// Output: NULL (co.uk is a public suffix, not a registrable domain)

// IDN support (requires ext-intl)
echo $regdom->getRegisteredDomain('www.münchen.de');
// Output: münchen.de
```

### Cookie Domain Validation (RFC 6265)

```php
use Xoops\RegDom\RegisteredDomain;

// Validates if a cookie domain is appropriate for a given host
RegisteredDomain::domainMatches('www.example.com', 'example.com');  // true
RegisteredDomain::domainMatches('example.com', 'com');              // false (public suffix)
RegisteredDomain::domainMatches('google.com', 'facebook.com');      // false (cross-domain)
RegisteredDomain::domainMatches('192.168.1.1', '192.168.1.1');      // false (IP addresses)
```

### Querying the Public Suffix List

```php
use Xoops\RegDom\PublicSuffixList;

$psl = new PublicSuffixList();

$psl->isPublicSuffix('com');        // true
$psl->isPublicSuffix('co.uk');      // true
$psl->isPublicSuffix('example.com'); // false

$psl->getPublicSuffix('www.example.co.uk'); // 'co.uk'

$psl->isException('www.ck'); // true (PSL exception rule)
```

### Checking PSL Cache Status

```php
$metadata = $psl->getMetadata();
// Returns: active_cache, last_updated, days_old, rule_counts, needs_update
```

### Updating the Public Suffix List

```bash
composer run update-psl
```

Set the `XOOPS_SKIP_PSL_UPDATE` environment variable to skip automatic PSL
updates during `composer install`/`update` (useful in CI or restricted networks).

## Configuration

### XOOPS_COOKIE_DOMAIN_USE_PSL

When defined and set to `false`, disables PSL-based validation in
`RegisteredDomain::domainMatches()`. Defaults to `true` when undefined.

### XOOPS_SKIP_PSL_UPDATE

Environment variable. When set (to any value), prevents automatic PSL download
during Composer post-install/update hooks.

## Development

```bash
composer install          # Install dependencies
composer ci               # Run all checks: lint + analyse + test
composer test             # Run PHPUnit tests
composer lint             # Check code style (PSR-12)
composer analyse          # Run PHPStan (level max)
composer fix              # Auto-fix code style issues
```

## Credits

Reg-dom was written by Florian Sager, 2009-02-05, sager@agitos.de

Marcus Bointon's adapted code: https://github.com/Synchro/regdom-php

## License

The PHP library code in this repository is licensed under the Apache License 2.0.
See [LICENSE.txt](LICENSE.txt) for the full Apache 2.0 license text.

The bundled Public Suffix List data/cache is derived from Mozilla's Public Suffix List
and is available under the Mozilla Public License 2.0 (MPL-2.0). For details, see
https://www.mozilla.org/en-US/MPL/2.0/ and https://publicsuffix.org/.
