/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frxoops.fr)
 */

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('xoopsmlcontent');

    tinymce.create('tinymce.plugins.XoopsmlcontentPlugin', {

        init : function(ed, url)
        {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand('mceXoopsmlcontent', function() {
                ed.windowManager.open({
                    file : url + '/xoopsmlcontent.php',
                    width : 520 + parseInt(ed.getLang('xoopsmlcontent.delta_width', 0)),
                    height : 350 + parseInt(ed.getLang('xoopsmlcontent.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url, // Plugin absolute URL
                    some_custom_arg : 'custom arg' // Custom argument
                });
            });

            // Register example button
            ed.addButton('xoopsmlcontent', {
                title : 'xoopsmlcontent.desc',
                cmd : 'mceXoopsmlcontent',
                image : url + '/img/xoopsmlcontent.png'
            });
        },

        getInfo : function() {
            return {
                longname : 'Xoops Multilaguage Content plugin',
                author : 'ralf57 / luciorota (lucio.rota@gmail.com) / dugris (dugris@frxoops.fr)',
                version : "1.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('xoopsmlcontent', tinymce.plugins.XoopsmlcontentPlugin);
})();