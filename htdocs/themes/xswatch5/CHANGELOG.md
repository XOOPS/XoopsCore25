# Changelog

All notable changes to the xSwatch5 theme are documented in this file.

## [1.0.0] - 2026-04-04

Complete rewrite from Bootstrap 4.6.1 (xSwatch4) to Bootstrap 5.3.8.

### Added
- **Theme Switcher** — navbar dropdown to switch between 21 Bootswatch variants live. Variants are discovered dynamically from `css-*/` directories via `glob()`. User selection persists to `localStorage`.
- **Light/Dark toggle** — navbar button with text label to switch color modes using the Bootstrap 5 Color Modes API (`data-bs-theme`). Respects OS preference on first visit, user override persists to `localStorage`.
- **RTL support** — `dir` attribute on `<html>` from XOOPS `_TEXT_DIRECTION` constant. All CSS uses Bootstrap 5 logical properties (`ms-`/`me-`, `text-start`/`text-end`). Custom RTL overrides for dropdown submenus in `css/my_xoops.css`.
- **FOUC prevention** — blocking `<script>` in `<head>` restores saved color mode and variant before the page paints.
- **LICENSES.md** — full MIT license text for bundled third-party assets.
- **Language constants** for theme switcher labels (`THEME_DARK_MODE`, `THEME_LIGHT_MODE`, `THEME_SWITCHER`).
- Standard XOOPS copyright header on all directory guard `index.php` files.

### Changed
- **Bootstrap** 4.6.1 → 5.3.8 (Bootswatch).
- **All templates** migrated: `data-toggle` → `data-bs-toggle`, `mr-`/`ml-` → `me-`/`ms-`, `text-right`/`text-left` → `text-end`/`text-start`, and all other BS4 → BS5 class renames.
- **Jumbotron** — `.jumbotron` class replaced with BS5 utility classes (`px-4 py-5 mb-4 bg-body-tertiary rounded-3`).
- **Forms** — `.form-inline` replaced with `d-flex`/`d-inline`, `.input-group-prepend`/`.input-group-append` wrappers removed.
- **Close buttons** — `.close` with `&times;` replaced with `.btn-close` (BS5 component).
- **Carousel controls** — `<a>` replaced with `<button>`, `data-slide` → `data-bs-slide`.
- **Badges** — `.badge-primary` → `.bg-primary`, `.badge-pill` → `.rounded-pill`.
- **Accessibility** — `.sr-only` → `.visually-hidden`, `.btn-block` → `.w-100`, `hidden-xs`/`hidden-sm` → BS5 display utilities.
- **JavaScript** — all Bootstrap API calls converted from jQuery to vanilla JS (toolbar, toast, modals, collapse).
- **Form renderer** — switched from `XoopsFormRendererBootstrap4` to `XoopsFormRendererBootstrap5`.
- **CSS loading** — replaced dual-CSS `prefers-color-scheme` media queries with single CSS + `data-bs-theme` attribute (halves CSS payload).
- **Deprecated API** — replaced `xoops_getModuleOption()` calls with direct config handler in `theme_autorun.php`.
- **WCAG AA contrast** — fixed all low-contrast color combinations in `style.css` and dark variant `xoops.css` files.
- **Image paths** — absolute `/images/` paths in `xoops.css` changed to relative `../../../images/` (fixes subdirectory installs).

### Removed
- jQuery dependency for Bootstrap (Bootstrap 5 is vanilla JS; jQuery is still loaded for XOOPS core).
- Dual-CSS dark mode approach (replaced by Color Modes API).
- External CDN reference in `cookieconsent.css`.
- IE conditional comments and `shrink-to-fit=no` viewport directive.
- `xswatch4.conf` (replaced by `xswatch5.conf`).
- Empty CSS placeholder blocks from `style.css`.
- Dead `.jumbotron p` CSS rule.

### Fixed
- `index.php` directory guards now properly terminate with `http_response_code(404); exit;`.
- `.marg7` CSS class corrected from `margin: 8px` to `margin: 7px`.
- Malformed double comment opener in `xoops.css` files.
- Pre-existing HTML bug in carousel control `<span>` tags (missing closing `>`).
- `pull-right` (BS3 leftover) replaced with `float-end`.
