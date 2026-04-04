xSwatch5
=======

xSwatch5 is a theme for [XOOPS 2.5.12](https://xoops.org) that is based on [Bootstrap](https://getbootstrap.com) 5.3 ([Bootswatch](https://bootswatch.com/))

The default theme is based on [Bootswatch Cerulean](https://bootswatch.com/cerulean/).

Features:

- Emphasis on better mobile experience
- Self-hosted, with no off-domain resources required
- Built-in cookie consent
- Administrator toolbar with block edit feature
- Light/dark mode via Bootstrap 5 Color Modes API with navbar toggle button
- RTL support via `dir` attribute from XOOPS language settings

Customize xSwatch:

- 21 themes in 1 theme (Preview : [Bootswatch](https://bootswatch.com/))
- Edit _xswatch5.conf_ to pick your Bootswatch variant (e.g., css-cerulean, css-slate).
  Each variant includes both light and dark color modes in a single CSS file.
- The navbar toggle button allows users to switch between light and dark modes.
  User preference is saved to localStorage and overrides OS preference.
- On first visit, the theme follows the user's browser or OS preference for light or dark.
- Customize the Navigation Bar in tpl/nav-menu.tpl and language/*/main.php to match your system and installed modules
- Customize the Jumbotron in theme.tpl and tpl/jumbotron.tpl
- Enable a slider in theme.tpl and tpl/slider.tpl
- Customize cookie consent in tpl/cookieConsent.tpl, or disable include in theme.tpl
- Customize or disable inbox alert in theme.tpl and tpl/inboxAlert.tpl
- For best experience install both the PM and Profile modules
- Customize xmnews block : for column blocks, copy xmnews_block_colonnes.tpl to xmnews_block.tpl (copy xmnews_block_lignes.tpl to xmnews_block.tpl to return to initial state)
- Customize _css/my_xoops.css_ to add your CSS definitions and override Bootstrap definitions. Use `[data-bs-theme="dark"]` selectors for dark mode overrides.

In addition to templates for the modules included in XOOPS (pm, profile and system,) Bootstrap 5 templates are included for the following modules:

- contact - [XoopsModules25x/contact](https://github.com/XoopsModules25x/contact)
- extcal - [XoopsModules25x/extcal](https://github.com/XoopsModules25x/extcal)
- newbb - [XoopsModules25x/newbb](https://github.com/XoopsModules25x/newbb)
- obituaries - [mambax7/obituaries](https://github.com/mambax7/obituaries)
- publisher - [XoopsModules25x/publisher](https://github.com/XoopsModules25x/publisher)
- tag - [XoopsModules25x/tag](https://github.com/XoopsModules25x/tag)
- tdmdownloads - [XoopsModules25x/tdmdownloads](https://github.com/XoopsModules25x/tdmdownloads)
- wggallery - [XoopsModules25x/wggallery](https://github.com/XoopsModules25x/wggallery)
- xmcontact - [GregMage/xmcontact](https://github.com/GregMage/xmcontact)
- xmcontent - [GregMage/xmcontent](https://github.com/GregMage/xmcontent)
- xmdoc - [GregMage/xmdoc](https://github.com/GregMage/xmdoc)
- xmnews - [GregMage/xmnews](https://github.com/GregMage/xmnews)
- xoopsfaq - [XoopsModules25x/xoopsfaq](https://github.com/XoopsModules25x/xoopsfaq)


Credits:

- Grégory Mage - [xm modules templates](https://github.com/GregMage)
- Twitter Bootstrap - [Bootstrap](https://getbootstrap.com)
- Angelo Rocha - [xBootStrap](https://github.com/angelorocha/xbootstrap)
- Thomas Park - [BootSwatch](https://bootswatch.com/)
- Silktide - [Cookie Consent](https://silktide.com/tools/cookie-consent/)
- Klaus Hartl - [js-cookie](https://github.com/js-cookie/js-cookie)
- Alain01 - 21 themes in 1 theme + xmnews templates
