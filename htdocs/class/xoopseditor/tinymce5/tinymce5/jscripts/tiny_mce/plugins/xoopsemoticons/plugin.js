/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * XOOOPS emoticons plugin
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

    var openDialog = function (editor, pluginUrl) {
        return editor.windowManager.openUrl({
          title: 'XOOPS Emoticons',
          url: pluginUrl + '/xoopsemoticons.php',
          buttons: [
            {
              type: 'cancel',
              text: 'Close'
            }
          ],
          width: 600,
          height: 300,
          onMessage: function(instance, data) {
            switch (data.mceAction)
            {
                case 'insertEmot':
                    if (data.data.src != '')
                    {
                        setTimeout(() => {
                            editor.insertContent('<img src="' + data.data.src + '" alt="' + data.data.title + '" title="' + data.data.title + '">');
                            instance.close();
                        }, 300);
                    }
                    break;
            }
          }
        });
    };   

    var register = function (editor, pluginUrl) {
        var onAction = function () {
            return openDialog(editor, pluginUrl);
        };
        editor.ui.registry.addButton('xoopsemoticons', {
            tooltip: 'XOOPS Emoticons',
            icon: 'emoji',
            onAction: onAction
        });
        editor.ui.registry.addMenuItem('xoopsemoticons', {
            text: 'XOOPS Emoticons...',
            icon: 'emoji',
            onAction: onAction
        });
    };

    function Plugin () {
        global.add('xoopsemoticons', function (editor, pluginUrl) {
            register(editor, pluginUrl);
        });
    }

    Plugin();

}(window));