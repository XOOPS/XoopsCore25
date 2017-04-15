xBootstrap
==========

xBootstrap is a theme for XOOPS (www.xoops.org) developed with Bootstrap.

Online demo: http://demo.xoopsfire.com

###Shareaholic support
Previously, social sharing links through Shareaholic were automatically enabled.
These are now optional and disabled by default. To enable Shareaholic support
follow these steps:

- Visit https://shareaholic.com to create an account and register your site
- Take note of the Site Id code Shareaholic assigns to your site
- Open the template file tpl/shareaholic-script.tpl in your editor
- Replace the *n/a* in this line `<{assign var='siteId' value='n/a'}>` with the *Site Id* Shareaholic assigned to you, and save the file

You can customize how Shareaholic interacts with your site in the Site Tools
Dashboard on shareaholic.com. You may want to create an Inline App Location
on that page to customize the Share Buttons. These buttons will be shown in
templates such as modules/publisher/publisher_item.tpl with this line:
`<div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>`

###Headhesive menus
In the file `tpl/nav-menu.tpl` there is an option at the top of the file in
the line `<{assign var='stickyHeader' value='yes'}>`. This option keeps the
menu bar stuck to the top of the display. If you would prefer to let the menu
disapper as you scroll down the page, change the setting to `value='no'` and
save the file.

