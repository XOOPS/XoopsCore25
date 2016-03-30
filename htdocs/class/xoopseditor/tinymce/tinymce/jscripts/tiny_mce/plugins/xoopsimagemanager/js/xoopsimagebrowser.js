/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frxoops.fr)
 */

tinyMCEPopup.requireLangPack();

var XoopsimagebrowserDialog = {
    init : function( ed )
    {
        tinyMCEPopup.resizeToInnerSize();
    },

    insertAndClose : function( image_id )
    {
      var image = document.getElementById( image_id );

        var win = tinyMCEPopup.getWindowArg("window");
        var input_src = tinyMCEPopup.getWindowArg("input_src");
        var input_title = tinyMCEPopup.getWindowArg("input_title");
        var input_alt = tinyMCEPopup.getWindowArg("input_alt");
        var input_align = tinyMCEPopup.getWindowArg("input_align");

        win.document.getElementById(input_src).value = image.src;

        if ( input_src == 'src' ) {
            win.document.getElementById(input_title).value = image.alt;
            win.document.getElementById(input_alt).value = image.alt;

            // for image browsers: update image dimensions
            if (win.XoopsimagemanagerDialog.getImageData) win.XoopsimagemanagerDialog.updateImageData(image.src);
            if (win.XoopsimagemanagerDialog.showPreviewImage) win.XoopsimagemanagerDialog.showPreviewImage(image.src);
        }

      tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(XoopsimagebrowserDialog.init, XoopsimagebrowserDialog);