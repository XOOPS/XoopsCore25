# XoopsCore25 (2.5.x) — Copilot Instructions

<!-- Generic XOOPS conventions: see .github/xoops-copilot-template.md for reuse in other repos -->

## About This Repository

XoopsCore25 is the XOOPS 2.5.x CMS framework. It provides the core content management system including user management, module framework, theme engine, and database abstraction layer for building dynamic web applications.

## Project Layout

```text
htdocs/                       # Web root (public files)
htdocs/kernel/                # Core kernel classes (ORM, handlers)
htdocs/class/                 # Core class library
htdocs/class/libraries/       # Composer-managed libraries (XMF, etc.)
htdocs/include/               # Core include files
htdocs/modules/               # Built-in and installable modules
htdocs/themes/                # Theme files
htdocs/language/              # Language packs
htdocs/install/               # Installation wizard
htdocs/xoops_data/            # Runtime data (caches, configs, logs)
htdocs/xoops_lib/             # Additional libraries
tests/                        # PHPUnit tests
.github/workflows/ci.yml     # GitHub Actions: tests, coverage
```

## Build & Test

```bash
# Libraries are managed via Composer in htdocs/class/libraries/
cd htdocs/class/libraries
composer install              # Install dependencies
vendor/bin/phpunit            # Run PHPUnit tests
```

The CI workflow dynamically creates `composer.json` in `htdocs/class/libraries/` with `xoops/base-requires25` as the base dependency package.

## PHP Compatibility

Code must run on PHP 7.4 through 8.5. Do not use features exclusive to PHP 8.0+ (named arguments, match expressions, union type hints in signatures, enums, fibers, readonly properties, intersection types, `never` return type, first-class callable syntax, constructor promotion, attributes `#[...]`, nullsafe operator `?->`, explicit `mixed` type). CI tests PHP 7.4-8.5.

## Coding Conventions

- Follow PSR-12 coding standard.
- Every source file begins with the XOOPS copyright header block.
- Class docblocks include `@category`, `@package`, `@author`, `@copyright`, `@license`, and `@link` tags.
- Use `self::` for class constants (not `static::`). PHPStan level max cannot resolve late static binding on constants and reports `mixed`.
- Prefer `\Throwable` in catch blocks over `\Exception` to cover both exceptions and errors.
- Use `trigger_error()` with `E_USER_WARNING` for non-fatal failures. Use `basename()` in error messages to avoid exposing server paths.

## XOOPS 2.5.x Architecture

- **Global-based**: Core objects via `$GLOBALS['xoopsModule']`, `$GLOBALS['xoopsConfig']`, `$GLOBALS['xoopsDB']`.
- **Handler pattern**: Data access via `xoops_getHandler('user')`, `xoops_getModuleHandler('item', 'modulename')`.
- **Module system**: Modules in `htdocs/modules/` with `xoops_version.php` manifest.
- **Template engine**: Smarty-based with templates in `htdocs/themes/` and module `templates/`.
- **XMF integration**: Modern utilities via `Xmf\Request`, `Xmf\Module\Helper`, etc. in `htdocs/class/libraries/`.

## Security Practices

- All user input must be filtered. Use `Xmf\Request::getVar()` or `Xmf\FilterInput::clean()` — never access `$_GET`, `$_POST`, or `$_REQUEST` directly.
- Escape all output with `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` or use Smarty auto-escaping.
- Use parameterized queries via XOOPS database handlers — never concatenate user input into SQL.
- Pass `['allowed_classes' => false]` to any `unserialize()` calls to prevent PHP Object Injection.
- Validate file paths with `realpath()` and boundary checks to prevent directory traversal.

## Testing Guidelines

- Test classes extend `\PHPUnit\Framework\TestCase`.
- Tests must work across PHPUnit 9.6, 10.5, and 11.x.
- Tests must be fully isolated — no XOOPS installation required for unit tests.
- Name test methods `test{MethodName}` or `test{MethodName}{Scenario}`.

## Pull Request Checklist

1. Code follows PSR-12 and passes code style checks.
2. Static analysis passes with no new errors beyond the baseline.
3. Tests pass on all supported PHP versions (7.4-8.5).
4. New public methods have PHPDoc with `@param`, `@return`, and `@throws` tags.
5. New functionality has corresponding unit tests.
6. Changes are documented in the changelog.
7. No direct superglobal access — use `Xmf\Request` or equivalent.
