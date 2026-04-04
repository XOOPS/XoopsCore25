xSwatch5
=======

A Bootstrap 5 theme for [XOOPS 2.5.12](https://xoops.org), based on [Bootswatch](https://bootswatch.com/) 5.3.8.

The default variant is [Bootswatch Cerulean](https://bootswatch.com/cerulean/).

## Features

- **21 Bootswatch themes in one** — switch variants live from the navbar
- **Light/dark mode** — Bootstrap 5 Color Modes API with one-click toggle
- **RTL support** — automatic layout mirroring for right-to-left languages
- **Mobile-first** responsive design
- **Self-hosted** — no off-domain resources required
- **Cookie consent** banner (Silktide)
- **Administrator toolbar** with inline block editing
- **Module templates** for 13 popular XOOPS modules

## Theme Switcher

The navbar includes two controls:

**Dark/Light toggle** — switches between light and dark color modes. Shows the current mode icon (sun/moon) and the label for the opposite mode.

**Theme dropdown** — lists all available Bootswatch variants. Selecting one instantly swaps the CSS. The list is built dynamically from `css-*/` directories — drop in a new variant folder with a `bootstrap.min.css` and it appears automatically.

Both preferences are saved to the visitor's browser (`localStorage`) and persist across sessions. On first visit, the color mode follows the OS/browser preference.

### How it works

1. `theme_autorun.php` scans `css-*/` directories with `glob()` and passes the list to Smarty
2. `tpl/nav-menu.tpl` renders the dropdown from that list
3. JavaScript swaps the CSS `<link>` href and saves the choice to `localStorage`
4. A blocking `<script>` in `<head>` restores saved preferences before the page paints (no flash)

## Configuration

### Server-side default

Edit `xswatch5.conf` to set the default Bootswatch variant for first-time visitors:

```
xswatchCss = "css-cerulean"
```

Valid values are any `css-*` directory name (e.g., `css-slate`, `css-darkly`, `css-flatly`).

### Custom CSS

Edit `css/my_xoops.css` to add custom styles or override Bootstrap. For dark-mode-specific overrides, use the `[data-bs-theme="dark"]` selector:

```css
[data-bs-theme="dark"] .my-element {
    background-color: #1a1a2e;
}
```

### RTL

RTL activates automatically when the XOOPS language pack defines `_TEXT_DIRECTION` as `'rtl'` (e.g., Arabic, Hebrew, Persian). Bootstrap 5 logical properties (`ms-`/`me-`, `text-start`/`text-end`) handle the layout mirroring. Custom submenu positioning overrides are included in `css/my_xoops.css`.

## Customization

- **Navigation bar** — `tpl/nav-menu.tpl` and `language/*/main.php`
- **Jumbotron** — `theme.tpl` and `tpl/jumbotron.tpl`
- **Slider** — uncomment in `theme.tpl`, configure in `tpl/slider.tpl`
- **Cookie consent** — `tpl/cookieConsent.tpl`, or disable include in `theme.tpl`
- **Inbox alert** — `tpl/inboxAlert.tpl`, or disable include in `theme.tpl`
- **xmnews block** — copy `xmnews_block_colonnes.tpl` to `xmnews_block.tpl` for column layout
- **Adding a variant** — drop a `css-{name}/` directory containing `bootstrap.min.css`, `xoops.css`, and `cookieconsent.css`

For best experience, install both the PM and Profile modules.

## Module Templates

Bootstrap 5 templates are included for the following modules:

| Module | Repository |
|--------|------------|
| contact | [XoopsModules25x/contact](https://github.com/XoopsModules25x/contact) |
| extcal | [XoopsModules25x/extcal](https://github.com/XoopsModules25x/extcal) |
| newbb | [XoopsModules25x/newbb](https://github.com/XoopsModules25x/newbb) |
| obituaries | [mambax7/obituaries](https://github.com/mambax7/obituaries) |
| publisher | [XoopsModules25x/publisher](https://github.com/XoopsModules25x/publisher) |
| tag | [XoopsModules25x/tag](https://github.com/XoopsModules25x/tag) |
| tdmdownloads | [XoopsModules25x/tdmdownloads](https://github.com/XoopsModules25x/tdmdownloads) |
| wggallery | [XoopsModules25x/wggallery](https://github.com/XoopsModules25x/wggallery) |
| xmcontact | [GregMage/xmcontact](https://github.com/GregMage/xmcontact) |
| xmcontent | [GregMage/xmcontent](https://github.com/GregMage/xmcontent) |
| xmdoc | [GregMage/xmdoc](https://github.com/GregMage/xmdoc) |
| xmnews | [GregMage/xmnews](https://github.com/GregMage/xmnews) |
| xoopsfaq | [XoopsModules25x/xoopsfaq](https://github.com/XoopsModules25x/xoopsfaq) |

## Requirements

- XOOPS 2.5.12+
- PHP 8.2+

## Credits

- Angelo Rocha — [xBootStrap](https://github.com/angelorocha/xbootstrap) (original theme base)
- Thomas Park — [Bootswatch](https://bootswatch.com/) (CSS variants)
- Grégory Mage — [xm module templates](https://github.com/GregMage)
- Alain01 — 21 themes in 1 theme + xmnews templates
- Michael Beck (Mamba) - conversion to Bootstrap 5.3.8 
- Silktide — [Cookie Consent](https://silktide.com/tools/cookie-consent/)
- Klaus Hartl — [js-cookie](https://github.com/js-cookie/js-cookie)
- [Bootstrap](https://getbootstrap.com) — framework

## License

GPL v3 — see the XOOPS project license. Third-party assets are MIT-licensed — see [LICENSES.md](LICENSES.md).
