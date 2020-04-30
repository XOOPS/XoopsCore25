xSwatch4
=======

xSwatch4 is a theme for [XOOPS 2.5.11](https://xoops.org) that is based on [Bootstrap](https://getbootstrap.com) 4.4.1.

The default theme is based on [Bootswatch Cerulean](https://bootswatch.com/cerulean/).

Features:

- Emphasis on better mobile experience
- Self hosted, with no off domain resources required
- Built in cookie consent
- Administrator toolbar with block edit feature

Customize xSwatch:

- 21 themes in 1 theme (Preview : [Bootswatch](https://bootswatch.com/))
  In the file _tpl/xswatchCss.tpl_, edit the bottom line to match the Bootswatch theme of your 
  choice. By default, the line reads **css-cerulean**. To change to a dark theme, like the one 
  used in the original xswatch for example, change it to **css-slate**.
  You can pick from any of the 21 variations listed in the comments in _tpl/xswatchCss.tpl_  
- customize the Navigation Bar in tpl/nav-menu.tpl and language/*/main.php to match your system and installed modules
- customize the Jumbotron in theme.tpl and tpl/jumbotron.tpl
- enable a slider in theme.tpl and tpl/slider.tpl
- customize cookie consent in tpl/cookieConsent.tpl, or disable include in theme.tpl
- customize or disable inbox alert in theme.tpl and tpl/inboxAlert.tpl
- for best experience install both the PM and Profile modules
- customize xmnews block : for column blocks, copy xmnews_block_colonnes.tpl to xmnews_block.tpl (copy xmnews_block_lignes.tpl to xmnews_block.tpl to return to initial state)

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