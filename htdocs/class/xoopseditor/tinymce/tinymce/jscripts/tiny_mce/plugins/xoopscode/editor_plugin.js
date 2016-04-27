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
    tinymce.PluginManager.requireLangPack('xoopscode');

    tinymce.create('tinymce.plugins.XoopscodePlugin', {
        init : function(ed, url) {
            // Register commands
            ed.addCommand('mceXoopscode', function() {
                ed.windowManager.open({
                    file : url + '/xoopscode.htm',
                    width : 460 + parseInt(ed.getLang('xoopscode.delta_width', 0)),
                    height : 300 + parseInt(ed.getLang('xoopscode.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('xoopscode', {
                title : 'xoopscode.code_desc',
                image : url + '/img/xoopscode.gif',
                cmd : 'mceXoopscode'
                });
        },

        getInfo : function() {
            return {
                longname : 'XoopsCode',
                author : 'Martin Sadera/ralf57/luciorota',
                authorurl : '',
                infourl : '',
                version : '1.1'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('xoopscode', tinymce.plugins.XoopscodePlugin);
})();
