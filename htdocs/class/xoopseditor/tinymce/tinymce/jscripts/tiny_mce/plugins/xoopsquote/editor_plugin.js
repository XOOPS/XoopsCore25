/**
 * 
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */


// created 2005-1-12 by Martin Sadera (sadera@e-d-a.info)
// ported to Xoops CMS by ralf57
// updated to TinyMCE v3.0.1 / 2008-02-29 / by luciorota


(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('xoopsquote');

    tinymce.create('tinymce.plugins.XoopsquotePlugin', {
        init : function(ed, url) {
            // Register commands
            ed.addCommand('mceXoopsquote', function() {
                ed.windowManager.open({
                    file : url + '/xoopsquote.htm',
                    width : 460 + parseInt(ed.getLang('xoopsquote.delta_width', 0)),
                    height : 300 + parseInt(ed.getLang('xoopsquote.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('xoopsquote', {
                title : 'xoopsquote.quote_desc',
                image : url + '/img/xoopsquote.gif',
                cmd : 'mceXoopsquote'
                });
        },

        getInfo : function() {
            return {
                longname : 'Xoopsquote',
                author : 'Martin Sadera/ralf57/luciorota/phppp',
                authorurl : '',
                infourl : '',
                version : '1.1'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('xoopsquote', tinymce.plugins.XoopsquotePlugin);
})();
