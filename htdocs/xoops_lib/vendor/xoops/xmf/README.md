<h1 align="center">
  <br />
  <img src="https://xoops.org/images/logoXoopsPhp81.png" alt="XOOPS" height="60" />
  <br />
  XMF &mdash; XOOPS Module Framework
  <br />
</h1>

<p align="center">
  <strong>The toolkit that makes XOOPS module development faster, safer, and more consistent.</strong>
</p>

<p align="center">
  <a href="https://github.com/XOOPS/xmf/actions"><img src="https://img.shields.io/github/actions/workflow/status/XOOPS/xmf/ci.yml?branch=master&label=CI&logo=github" alt="CI Status" /></a>
  <a href="https://scrutinizer-ci.com/g/XOOPS/xmf/"><img src="https://img.shields.io/scrutinizer/quality/g/XOOPS/xmf/master?logo=scrutinizer" alt="Scrutinizer" /></a>
  <a href="https://packagist.org/packages/xoops/xmf"><img src="https://img.shields.io/packagist/v/xoops/xmf?label=stable&logo=packagist" alt="Packagist Version" /></a>
  <a href="https://packagist.org/packages/xoops/xmf"><img src="https://img.shields.io/packagist/dt/xoops/xmf?logo=packagist&color=blue" alt="Downloads" /></a>
  <a href="https://packagist.org/packages/xoops/xmf"><img src="https://img.shields.io/packagist/php-v/xoops/xmf?logo=php" alt="PHP Version" /></a>
  <a href="https://www.gnu.org/licenses/gpl-2.0.html"><img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue" alt="License" /></a>
</p>

---

## Why XMF?

Building XOOPS modules means solving the same problems over and over: filtering input, managing sessions, handling permissions, generating meta tags, working with databases. **XMF gives you battle-tested solutions for all of them** so you can focus on what makes your module unique.

| | What you get | Why it matters |
|---|---|---|
| **Input & Security** | Request handling, input filtering, IP address validation | Stop writing your own sanitization &mdash; use proven, audited code |
| **Database** | Schema migrations, table management, bulk loading | Evolve your database safely across module versions |
| **Authentication** | JWT tokens, key management, secure storage | Add token-based auth without pulling in heavyweight packages |
| **Module Helpers** | Admin panels, permissions, sessions, caching | Common module tasks reduced to one-liners |
| **Content Tools** | Meta tag generation, SEO titles, YAML config, search summaries | Improve SEO and content handling with zero effort |
| **Identifiers** | ULID and UUID generation | Generate unique, sortable identifiers out of the box |
| **Developer Tools** | Debugging (Kint), YAML import/export, assertions | Debug and inspect with a single call |

## Quick Start

```bash
composer require xoops/xmf
```

```php
use Xmf\Request;
use Xmf\FilterInput;
use Xmf\Metagen;

// Safe input handling
$id = Request::getInt('id', 0, 'GET');
$name = Request::getString('name', '', 'POST');

// Generate SEO-friendly meta tags
Metagen::generateMetaTags($title, $body);

// Generate a ULID
$ulid = \Xmf\Ulid::generate();
```

## Components

```
xmf/src/
  |
  |-- Request.php          HTTP request handling & input retrieval
  |-- FilterInput.php      Input sanitization & XSS prevention
  |-- IPAddress.php        IPv4/IPv6 validation & subnet checks
  |-- ProxyCheck.php       Proxy detection for real client IPs
  |
  |-- Database/
  |     |-- Tables.php     Schema definition & ALTER operations
  |     |-- Migrate.php    Module schema migrations
  |     +-- TableLoad.php  Bulk data import
  |
  |-- Jwt/
  |     |-- JsonWebToken   Create & decode signed JWT tokens
  |     |-- TokenFactory   Convenient token builder
  |     +-- TokenReader    Token verification & claim extraction
  |
  |-- Key/
  |     |-- Basic.php      Key pair generation
  |     |-- FileStorage    Persistent key storage (filesystem)
  |     +-- ArrayStorage   In-memory key storage (testing)
  |
  |-- Module/
  |     |-- Admin.php      Admin panel rendering & config display
  |     +-- Helper/
  |           |-- Permission   Group permission management
  |           |-- Session      Secure session read/write
  |           |-- Cache        Module-scoped caching
  |           +-- GenericHelper  Common helper utilities
  |
  |-- Metagen.php          Meta keywords, descriptions & SEO titles
  |-- Highlighter.php      Search term highlighting
  |-- StopWords.php        Keyword filtering (multi-language)
  |
  |-- Ulid.php             ULID generation (monotonic & standard)
  |-- Uuid.php             UUID v4 generation
  |-- Random.php           Cryptographically secure random bytes
  |
  |-- Yaml.php             YAML read/write with PHP-wrapped security
  |-- Language.php         Safe language file loading
  |-- Debug.php            Kint-powered variable inspection
  +-- Assert.php           Runtime assertion helpers
```

## Requirements

| Requirement | Version |
|---|---|
| PHP | 7.4+ |
| XOOPS | 2.5.x or 2.6.x |
| Composer | Required |

### Key Dependencies

- [`firebase/php-jwt`](https://github.com/firebase/php-jwt) &mdash; JWT encoding/decoding
- [`symfony/yaml`](https://github.com/symfony/yaml) &mdash; YAML parsing
- [`kint-php/kint`](https://github.com/kint-php/kint) &mdash; Debug output
- [`webmozart/assert`](https://github.com/webmozarts/assert) &mdash; Assertion library

## Installation

**Via Composer (recommended):**

```bash
composer require xoops/xmf
```

**As part of XOOPS:**

XMF is included in XOOPS 2.5.8+ as a core library. No separate installation needed.

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Static analysis
composer analyse

# Code style check
composer lint

# Auto-fix code style
composer fix

# Run all CI checks
composer ci

# Regenerate PHPStan baseline
composer baseline
```

## Contributing

Contributions are welcome! Please see our [Contributing Guide](.github/CONTRIBUTING.md) for details.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Make your changes and add tests
4. Run `composer ci` to verify
5. Submit a Pull Request

## Links

- [XOOPS Project](https://xoops.org)
- [XMF on Packagist](https://packagist.org/packages/xoops/xmf)
- [XMF on GitHub](https://github.com/XOOPS/xmf)
- [Issue Tracker](https://github.com/XOOPS/xmf/issues)
- [Changelog](CHANGELOG.md)

## License

XMF is licensed under the [GPL-2.0-or-later](docs/license.md).

## Acknowledgments

<a href="https://www.jetbrains.com/community/opensource/">
  <img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" alt="JetBrains Logo" width="120" />
</a>

Thank you to [JetBrains](https://www.jetbrains.com/community/opensource/) for supporting open-source development by providing free IDE licenses to this project.

---

<p align="center">
  Made with ❤️ by the <a href="https://github.com/XOOPS">XOOPS Project</a> community
</p>
