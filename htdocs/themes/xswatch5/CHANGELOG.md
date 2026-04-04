# Changelog

All notable changes to the xSwatch theme family are documented in this file.

## [2.0.0] - 2026-04-04

Repository: [mambax7/xswatch5](https://github.com/mambax7/xswatch5)

Complete rewrite from Bootstrap 5.2.3 to Bootstrap 5.3.8 with new features.

### Added
- **Theme Switcher** — navbar dropdown to switch between Bootswatch variants live. Variants are discovered dynamically from `css-*/` directories via `glob()`. User selection persists to `localStorage`.
- **Light/Dark toggle** — navbar button with translatable text label to switch color modes using the Bootstrap 5 Color Modes API (`data-bs-theme`). Respects OS preference on first visit, user override persists to `localStorage`.
- **RTL support** — `dir` attribute on `<html>` from XOOPS `_TEXT_DIRECTION` constant. All CSS uses Bootstrap 5 logical properties (`ms-`/`me-`, `text-start`/`text-end`). Custom RTL overrides for dropdown submenus.
- **FOUC prevention** — blocking `<script>` in `<head>` restores saved color mode and variant before the page paints.
- **Language constants** — `THEME_DARK_MODE`, `THEME_LIGHT_MODE`, `THEME_SWITCHER` for translation support.
- `LICENSES.md` — full MIT license text for bundled third-party assets.
- `CHANGELOG.md` — this file.
- Standard XOOPS copyright header on all `index.php` directory guards.

### Changed
- **Bootstrap** 5.2.3 → 5.3.8 (Bootswatch 5.3.8).
- **All templates** migrated from Bootstrap 4 patterns: `data-toggle` → `data-bs-toggle`, `mr-`/`ml-` → `me-`/`ms-`, `text-right`/`text-left` → `text-end`/`text-start`, and all other BS4 → BS5 class renames.
- **Dark mode** — replaced dual-CSS `prefers-color-scheme` media query approach with single CSS + `data-bs-theme` attribute (halves CSS payload).
- **Jumbotron** — `.jumbotron` class replaced with BS5 utility classes.
- **Forms** — `.form-inline` → `d-flex`, `.input-group-prepend`/`.input-group-append` wrappers removed.
- **Close buttons** — `.close` with `&times;` → `.btn-close` (BS5 component).
- **Carousel controls** — `<a>` → `<button>`, `data-slide` → `data-bs-slide`.
- **Badges** — `.badge-primary` → `.bg-primary`, `.badge-pill` → `.rounded-pill`.
- **Accessibility** — `.sr-only` → `.visually-hidden`, WCAG AA contrast compliance for all color combinations.
- **JavaScript** — all Bootstrap API calls converted from jQuery to vanilla JS.
- **Form renderer** — `XoopsFormRendererBootstrap4` → `XoopsFormRendererBootstrap5`.
- **Config** — `xswatch4.conf` → `xswatch5.conf`, `xswatchDarkCss` setting removed (handled by Color Modes API).
- **Image paths** — absolute `/images/` → relative `../../../images/` in `xoops.css` (fixes subdirectory installs).
- **Deprecated API** — `xoops_getModuleOption()` → direct config handler in `theme_autorun.php`.
- **README** — complete rewrite with theme switcher docs, configuration guide, RTL section.

### Removed
- jQuery dependency for Bootstrap (Bootstrap 5 is vanilla JS).
- Dual-CSS dark mode approach (two full stylesheets via media queries).
- External CDN reference in `cookieconsent.css`.
- IE conditional comments and `shrink-to-fit=no` viewport directive.
- `xswatch4.conf`.
- Empty CSS placeholder blocks and dead CSS rules from `style.css`.

### Fixed
- `index.php` guards now terminate with `http_response_code(404); exit;`.
- `.marg7` class corrected from `margin: 8px` to `margin: 7px`.
- Malformed double comment opener in `xoops.css`.
- Pre-existing HTML bug in carousel control `<span>` tags.
- `pull-right` (BS3 leftover) → `float-end`.

---

## [1.0.0] - 2023-01-26

Repository: [alain01/xswatch5](https://github.com/alain01/xswatch5)

Migration from Bootstrap 4.6.1 to Bootstrap 5.2.3.

### Added
- 4 new Bootswatch variants: Morph, Quartz, Vapor, Zephyr (25 total).
- Light/dark theme switching via `prefers-color-scheme` CSS media queries.
- Dual theme configuration: `xswatchCss.tpl` (light) + `xswatchDarkCss.tpl` (dark).
- Module templates for 13 modules: contact, extcal, newbb, obituaries, publisher, tag, tdmdownloads, wggallery, xmcontact, xmcontent, xmdoc, xmnews, xoopsfaq.
- Toast-based inbox alert for unread PMs.

### Changed
- Bootstrap 4.6.1 → 5.2.3 (partial migration — some BS4 patterns remained).
- Jumbotron, slider, navigation, and admin toolbar updated for BS5.
- Badge classes updated to BS5 color utilities.
- Close buttons updated with BS5 positioning.
- Login and main menu system blocks updated.

---

## Prior Versions

### xSwatch4 — [geekwright/xswatch4](https://github.com/geekwright/xswatch4)

Released December 2019. Upgraded from Bootstrap 3 to **Bootstrap 4.4.1**. Introduced Bootswatch 4 variants with Cerulean (light) and Slate (dark) defaults. Added module templates for system, profile, PM, and 10+ community modules. Cookie consent updated. 114 commits.

### xSwatch3 — [geekwright/xswatch3](https://github.com/geekwright/xswatch3)

Released May 2023 (extracted from XoopsCore25 2.5.11-Beta2). Based on **Bootstrap 3.3.7**. Improved modularity for theme swapping — users could replace `bootstrap.min.css` to change Bootswatch variant. Dark mode via CSS directory swap. 82 commits.

### xSwatch — [geekwright/xswatch](https://github.com/geekwright/xswatch)

Released August 2016. Original theme built on **Bootstrap 3.3.7** and inspired by Angelo Rocha's [xBootStrap](https://github.com/angelorocha/xbootstrap). Introduced the core architecture: self-hosted assets, Bootswatch Cerulean/Slate variants, admin toolbar with block editing, EU cookie compliance, mobile-first responsive design. 33 commits.
