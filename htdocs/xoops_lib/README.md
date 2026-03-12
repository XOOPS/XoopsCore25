# XOOPS Libraries (`xoops_lib`)

Third-party PHP libraries for XOOPS CMS 2.5.12+, managed by [Composer](https://getcomposer.org/).

> ## Security Notice:
> For production deployments, move this folder outside the document root. The included `.htaccess` and `index.php` block direct web access, but placing it outside the web root is the strongest protection.

## For XOOPS Users

You do **not** need Composer to run XOOPS. The distribution ships with all libraries pre-built in the `vendor/` directory.

## For Developers

Power users may use Composer to manage or customize dependencies.

### Getting Started

1. Copy `composer.dist.json` to `composer.json`:
   ```
   cp composer.dist.json composer.json
   ```
2. Run Composer:
   ```
   composer install --prefer-dist --no-dev
   ```

### How It Works

- `composer.dist.json` lists all required libraries directly. Starting with XOOPS 2.5.12, the external `xoops/base-requires25` metapackage has been dropped; all dependencies are now defined inline in `composer.dist.json`.
- When XOOPS is updated, `composer.dist.json` may change, but your custom `composer.json` will not be overwritten.
- You are responsible for merging any upstream changes into your customized file.

### Build Script

The `build` script rebuilds the vendor directory for distribution:

```bash
COMPOSER='composer.dist.json' composer update --prefer-dist --no-dev -a
# remove unneeded files from wideimage (no .gitattributes in package)
rm -r vendor/smottt/wideimage/demo
rm -r vendor/smottt/wideimage/test
# freshen public suffix list
vendor/bin/update-psl.php
```

### Managed Libraries

All runtime dependencies are defined in `composer.dist.json`. Key packages include:

| Package | Purpose |
|---|---|
| [xoops/xmf](https://github.com/XOOPS/xmf) | XOOPS Module Framework |
| [xoops/regdom](https://github.com/XOOPS/RegDom) | Registered domain detection (PSL) |
| [erusev/parsedown](https://github.com/erusev/parsedown) | Markdown parser |
| [ezyang/htmlpurifier](https://github.com/ezyang/htmlpurifier) | HTML sanitization |
| [firebase/php-jwt](https://github.com/firebase/php-jwt) | JWT authentication |
| [monolog/monolog](https://github.com/Seldaek/monolog) | Logging (PSR-3) |
| [php-debugbar/php-debugbar](https://github.com/maximebf/php-debugbar) | Debug toolbar |
| [phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer) | Email sending |
| [psr/log](https://github.com/php-fig/log) | PSR-3 logger interface |
| [punic/punic](https://github.com/punic/punic) | Unicode/CLDR utilities |
| [smarty/smarty](https://github.com/smarty-php/smarty) | Template engine (v4) |
| [tecnickcom/tcpdf](https://github.com/tecnickcom/TCPDF) | PDF generation |

See `composer.dist.json` for the full dependency list.
