/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frxoops.fr)
 * @version         $Id: xoopsemotions.js 11937 2013-08-18 02:25:16Z beckmi $
 */

tinyMCEPopup.requireLangPack();

var XoopsemotionsDialog = {
    init : function()
    {
//        tinyMCEPopup.resizeToInnerSize();
    },

    insert : function( emotion )
    {
        // Insert the contents from the input into the document

        if ( emotion.title == null ) {emotion.title = "";}

        // XML encode
        emotion.title = emotion.title.replace(/&/g, '&amp;');
        emotion.title = emotion.title.replace(/\"/g, '&quot;');
        emotion.title = emotion.title.replace(/</g, '&lt;');
        emotion.title = emotion.title.replace(/>/g, '&gt;');
        var html = '<img src="' + emotion.src + '" border="0" alt="' + emotion.title + '" title="' + emotion.title + '" />';
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, html);
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(XoopsemotionsDialog.init, XoopsemotionsDialog);