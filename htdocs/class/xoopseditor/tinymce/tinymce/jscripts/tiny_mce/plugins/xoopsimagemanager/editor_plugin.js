/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frxoops.fr)
 */

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('xoopsimagemanager');

    tinymce.create('tinymce.plugins.XoopsimagemanagerPlugin', {
        init : function(ed, url)
        {
            // Register commands
            ed.addCommand('mceXoopsimagemanager', function() {
                var e = ed.selection.getNode();

                // Internal image object like a flash placeholder
                if (ed.dom.getAttrib(e, 'class').indexOf('mceItem') != -1)
                    return;

                ed.windowManager.open({
                    file : url + '/xoopsimagemanager.php',
                    width : 480 + parseInt(ed.getLang('xoopsimagemanager.delta_width', 0)),
                    height : 385 + parseInt(ed.getLang('xoopsimagemanager.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('xoopsimagemanager', {
                title : 'xoopsimagemanager.desc',
                cmd : 'mceXoopsimagemanager',
                image : url + '/img/xoopsimagemanager.png'
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('xoopsimagemanager', n.nodeName == 'IMG');
            });
        },

        getInfo : function()
        {
            return {
                longname : 'Xoops Advanced Image Manager',
                author : 'luciorota (lucio.rota@gmail.com) / dugris (dugris@frxoops.fr)',
                version : "1.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('xoopsimagemanager', tinymce.plugins.XoopsimagemanagerPlugin);
})();