<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
    <meta http-equiv="content-language" content="<{$xoops_langcode}>">
    <title><{$sitename}> <{$lang_imgmanager}></title>
    <script type="text/javascript">
        <!--//
        function appendCode(addCode) {
            var targetDom = window.opener.xoopsGetElementById('<{$target}>');
            if (targetDom.createTextRange && targetDom.caretPos) {
                var caretPos = targetDom.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? addCode + ' ' : addCode;
            } else if (targetDom.getSelection && targetDom.caretPos) {
                var caretPos = targetDom.caretPos;
                caretPos.text = caretPos.text.charat(caretPos.text.length - 1) == ' ' ? addCode + ' ' : addCode;
            } else {
                targetDom.value = targetDom.value + addCode;
            }
            window.close();
//    return;
        }
        //-->
    </script>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl xoops.css}>">
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl modules/system/css/imagemanager.css}>">
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl media/font-awesome/css/font-awesome.min.css}>">

    <{php}>
        $language = $GLOBALS['xoopsConfig']['language'];
        if(file_exists(XOOPS_ROOT_PATH.'/language/'.$language.'/style.css')){
        echo "
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"language/$language/style.css\">
        ";
        }
    <{/php}>

</head>

<body onload="window.resizeTo(<{$xsize}>, <{$ysize}>);">
<form action="imagemanager.php" method="get">
    <table cellspacing="0" id="imagenav">
        <tr>
            <td>
                <select name="cat_id"
                        onchange="location='<{$xoops_url}>/imagemanager.php?target=<{$target}>&cat_id='+this.options[this.selectedIndex].value"><{$cat_options}></select>
                <input type="hidden" name="target" value="<{$target}>"/>
                <input type="submit" value="<{$lang_go}>"/>
            </td>

            <{if $show_cat > 0}>
                <td id="addimage" class="txtright"><a href="<{$xoops_url}>/imagemanager.php?target=<{$target}>&op=upload&imgcat_id=<{$show_cat}>"
                                                      title="<{$lang_addimage}>"><{$lang_addimage}></a></td>
            <{/if}>

        </tr>
    </table>
</form>
<div id="pagenav"><{$pagenav}></div>
<{if $image_total > 0}>
    <table cellspacing="0" id="imagemain">
        <tr>
            <th><{$lang_imagename}></th>
            <th><{$lang_image}></th>
            <th><{$lang_imagemime}></th>
            <th><{$lang_align}></th>
        </tr>

        <{section name=i loop=$images}>
            <tr class="txtcenter">
                <td><input type="hidden" name="image_id[]" value="<{$images[i].id}>"/><{$images[i].nicename}></td>
                <td><img style="max-width:200px;" src="<{$images[i].src}>" alt=""/></td>
                <td><{$images[i].mimetype}></td>
                <td><button type="button" class="btn btn-default" onclick="appendCode('<{$images[i].lxcode}>');" title="<{$smarty.const._LEFT}>" aria-label="Left Align"><span class="fa fa-align-left" aria-hidden="true"></span></button>
                    <button type="button" class="btn btn-default" onclick="appendCode('<{$images[i].xcode}>');" title="<{$smarty.const._CENTER}>" aria-label="Center Align"><span class="fa fa-align-center" aria-hidden="true"></span></button>
                    <button type="button" class="btn btn-default" onclick="appendCode('<{$images[i].rxcode}>');" title="<{$smarty.const._RIGHT}>" aria-label="Right Align"><span class="fa fa-align-right" aria-hidden="true"></span></button>
            </tr>
        <{/section}>
    </table>
<{else}>
    <div id="welcomenot"></div>
<{/if}>

<div id="pagenav"><{$pagenav}></div>

<div id="footer">
    <input value="<{$lang_close}>" type="button" onclick="window.close();"/>
</div>

</body>
</html>
