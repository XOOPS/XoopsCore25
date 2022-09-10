/**
 * XOOOPS Image Manager plugin
 *
 * @category  XoopsEditor
 * @package   TinyMCE5
 * @author    ForMuss
 * @copyright 2020 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
 (function (domGlobals) {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');
    var ed;
    var url;

    var xoopsimagemanager = function(cb, value, meta) {
        ed.focus(true);

        window.addEventListener('message', function receiveMessage(event) {
			window.removeEventListener('message', receiveMessage, false);
			if (event.data.sender === 'responsivefilemanager') {
				callback(event.data.url);
			}
		}, false);

        ed.windowManager.openUrl({
            title: 'XOOPS Imagemanager',
            url: url + '/xoopsimagemanager.php?target=src',
            buttons: [
              {
                type: 'cancel',
                text: 'Close'
              }
            ],
            width: 800,
            height: 600,
            inline: 1,
			resizable: true,
			maximizable: true,
            onMessage: function(instance, event) {
              switch (event.mceAction)
              {
                  case 'insertImage':
                      
                      if (event.data.src != '')
                      {
                          setTimeout(() => {
                                cb(event.data.src, { title: event.data.title });
				                tinymce.activeEditor.windowManager.close();  
                          }, 300);
                      }
                      break;
              }
            }
          });

    };

    var register = function (editor, pluginUrl) {

        editor.settings.file_picker_types = 'image';
	    editor.settings.file_picker_callback = xoopsimagemanager;

    };

    function Plugin () {
        global.add('xoopsimagemanager', function (editor, pluginUrl) {
            ed = editor;
            url = pluginUrl;
            register(editor, pluginUrl);
        });
    }

    Plugin();

}(window)); 