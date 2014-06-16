 ######################
 Xquotecode plugin for tinyeditor
 by ralf57
 10-03-2005
######################

This plugin allows you and your site's users
to add code and quoted text in a nice formatted box.
It is also a workaround for cut/copy/paste protection in Gecko browsers.
It simply adds a 
<div class="xoopsQuote">your quoted text</div>
when inserting quote or
<div class="xoopsCode">your code</div>
when inserting code into tinyeditor area.
As you may have noticed it uses two commonly used .css classes to format the container:
xoopsQuote and xoopsCode
so be sure to have them inside your theme's css file or else you won't get any benefit from
this plugin.

Installation instructions:
  * Copy the "xquotecode" directory to the plugins directory of tinyeditor (/xoops/modules/tinyeditor/editor/plugins).
  * The plugin should be enabled by default;if not so,enable it from tinyeditor's config options
  * Add one or both of the two buttons to tinyeditor toolbar using the toolbar configurator

That's all!