// created 2005-1-12 by Martin Sadera (sadera@e-d-a.info)
// ported to Xoops CMS by ralf57
// updated to TinyMCE v3.0.1 / 2008-02-29 / by luciorota
tinyMCEPopup.requireLangPack();

var XoopscodeDialog = {
    init : function()
    {
        var formObj = document.forms[0];
        // Get the selected contents as text and place it in the input
        formObj.ctext.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
    },
    insert : function()
    {
        // Insert the contents from the input into the document
        var formObj = document.forms[0];
        //if (window.opener) {
            var ctext = formObj.ctext.value;
            ctext.replace(new RegExp("<",'g'), "&lt;");
            ctext.replace(new RegExp(">",'g'), "&gt;");
            var html = '<div class="xoopsCode">';
            html += ctext+'</div><br />';
            tinyMCEPopup.editor.execCommand('mceInsertContent', true, html);
        //}
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(XoopscodeDialog.init, XoopscodeDialog);
