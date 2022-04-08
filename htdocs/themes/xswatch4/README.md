xSwatch4
=======

xSwatch4 is a theme for [XOOPS 2.5.11](https://xoops.org) that is based on [Bootstrap](https://getbootstrap.com) 4.6

The default theme is based on [Bootswatch Cerulean](https://bootswatch.com/cerulean/).

Features:

- Emphasis on better mobile experience
- self-hosted, with no off-domain resources required
- Built-in cookie consent
- Administrator toolbar with block edit feature
- Switch between light and dark theme based on media queries

Customize xSwatch:

- 21 themes in 1 theme (Preview : [Bootswatch](https://bootswatch.com/))
- Dual theme light and dark media query responsive operation - 
  Two files control which themes are used, _tpl/xswatchCss.tpl_ and _tpl/xswatchDarkCss.tpl_.
  In the file _tpl/xswatchCss.tpl_, edit the bottom line to match the Bootswatch theme of your
  choice for use with prefers-color-scheme light media queries. By default, the line reads 
  **css-cerulean**. In the file _tpl/xswatchDarkCss.tpl_, edit the bottom line to match the Bootswatch theme of your
  choice for use with dark media queries.  By default, the line reads **css-slate**. 
  Your site will then follow the use's browser or OS preference to choose light or dark.
- Single theme operation - 
  Edit _tpl/xswatchDarkCss.tpl_ and delete the line with the file name, and the theme will
  be locked to whatever theme is specified in the file _tpl/xswatchCss.tpl_. No consideration
  of prefers-color-scheme queries will be made. In the file _tpl/xswatchCss.tpl_, edit the 
  bottom line to match the Bootswatch theme of your choice. By default, the line reads 
  **css-cerulean**. To change to a dark theme, for example, change it to **css-slate**.
  You can pick from any of the 21 variations listed in the comments in _tpl/xswatchCss.tpl_  
- Customize the Navigation Bar in tpl/nav-menu.tpl and language/*/main.php to match your system and installed modules
- Customize the Jumbotron in theme.tpl and tpl/jumbotron.tpl
- Enable a slider in theme.tpl and tpl/slider.tpl
- Customize cookie consent in tpl/cookieConsent.tpl, or disable include in theme.tpl
- Customize or disable inbox alert in theme.tpl and tpl/inboxAlert.tpl
- For best experience install both the PM and Profile modules
- Customize xmnews block : for column blocks, copy xmnews_block_colonnes.tpl to xmnews_block.tpl (copy xmnews_block_lignes.tpl to xmnews_block.tpl to return to initial state)
- Customize _css/my_xoops.css to add your css definitions and override Bootstrap definitions for the light variant or the unique variant
- Customize _css/my_xoops_dark.css to add your css definitions and override Bootstrap definitions for the dark variant

In addition to templates for the modules included in XOOPS (pm, profile and system,) Bootstrap v4.4 templates are included for the following modules:

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
