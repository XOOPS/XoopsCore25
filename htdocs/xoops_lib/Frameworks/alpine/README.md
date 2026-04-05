# Alpine.js for XOOPS

Shared Alpine.js runtime for XOOPS themes.

## About Alpine.js

[Alpine.js](https://alpinejs.dev/) is a lightweight (~15 KB minified+gzipped) JavaScript framework for adding declarative interactivity via HTML attributes. It's used by Tailwind CSS themes (like xtailwind) for dropdowns, mobile navigation, modals, and toast notifications without requiring a large framework or custom JavaScript files.

## Installation

Alpine.js is vendored in this directory — no manual download required. The file `alpine.min.js` ships with XOOPS and is ready to use.

### Updating the vendored copy

To update Alpine.js to a newer version, replace `alpine.min.js` with the latest minified build from the [Alpine.js releases](https://github.com/alpinejs/alpine/releases), or via CDN:

```bash
curl -sL "https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js" -o alpine.min.js
```

## Usage from a theme

Reference Alpine via XOOPS's `browse.php` proxy, following the same pattern as jQuery:

```smarty
<script defer src="<{$xoops_url}>/browse.php?Frameworks/alpine/alpine.min.js"></script>
```

The `defer` attribute is recommended — Alpine scans the DOM after `DOMContentLoaded`, so deferring its load doesn't cause any flash of unstyled content (as long as you also use `x-cloak` on elements that should be hidden until Alpine initializes).

## Why Alpine.js is here and not in media/

XOOPS separates assets into two directories by purpose:

- **`xoops_lib/Frameworks/`** — JavaScript runtime libraries (jQuery, Chart.js, Alpine.js)
- **`media/`** — Visual assets (Font Awesome icons, file upload widgets)

Alpine.js is a runtime JavaScript framework, so it belongs alongside jQuery and Chart.js.

## License

Alpine.js is MIT licensed. See the [upstream license](https://github.com/alpinejs/alpine/blob/main/LICENSE.md).
