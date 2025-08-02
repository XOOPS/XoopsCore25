(function () {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var openDialog = function (editor) {
        return editor.windowManager.open({
            title: 'XOOPS Code',
            size: 'medium',
            body: {
                type: 'panel',
                items: [{
                    type: 'textarea',
                    name: 'xoopscode'
                }]
            },
            buttons: [
                {
                    type: 'cancel',
                    name: 'cancel',
                    text: 'Cancel'
                },
                {
                    type: 'submit',
                    name: 'save',
                    text: 'Save',
                    primary: true
                }
            ],
            onSubmit: function (api) {
                //setContent(editor, api.getData().xoopscode);
                var ctext = api.getData().xoopscode;
                ctext.replace(new RegExp("<",'g'), "&lt;");
                ctext.replace(new RegExp(">",'g'), "&gt;");
                editor.insertContent('<div class="xoopsCode"><code>' + ctext + '</code></div><br>');
                api.close();
            }
        });
    };  

    var register = function (editor) {
        editor.ui.registry.addButton('xoopscode', {
            icon: 'code-sample',
            tooltip: 'XOOPS code',
            onAction: function () {
                return openDialog(editor);
            }
        });
        editor.ui.registry.addMenuItem('xoopscode', {
            icon: 'code-sample',
            text: 'XOOPS code',
            onAction: function () {
              return openDialog(editor);
            }
         });
    };

    function Plugin () {
        global.add('xoopscode', function (editor) {
            register(editor);
            //register$1(editor);
            return {};
        });
    }
  
    Plugin();
}());