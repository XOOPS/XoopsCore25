<?php
/**
 *  Xoopsemotions plugin for tinymce
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class / xoopseditor
 * @subpackage          tinymce / xoops plugins
 * @since               2.3.0
 * @author              ralf57
 * @author              luciorota <lucio.rota@gmail.com>
 * @author              Laurent JEN <dugris@frxoops.org>
 */

// load mainfile.php
$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$xoops_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/xoopseditor/tinymce/'));
include_once $xoops_root_path . '/mainfile.php';
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

// include
include_once XOOPS_ROOT_PATH . '/modules/system/constants.php';

// check user/group
$admin = false;

$gperm_handler = xoops_getHandler('groupperm');
$groups        = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
$admin         = $gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_IMAGE, $groups);

// check categories readability/writability by group
$imgcat_handler = xoops_getHandler('imagecategory');
$catreadlist    = $imgcat_handler->getList($groups, 'imgcat_read', 1);    // get readable categories
$catwritelist   = $imgcat_handler->getList($groups, 'imgcat_write', 1);  // get writable categories

$canbrowse = ($admin || !empty($catreadlist) || !empty($catwritelist)) ? true : false;

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
?>
<head>
    <title>{#xoopsimagemanager_dlg.dialog_title}</title>
    <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
    <script type="text/javascript" src="../../utils/mctabs.js"></script>
    <script type="text/javascript" src="../../utils/form_utils.js"></script>
    <script type="text/javascript" src="../../utils/validate.js"></script>
    <script type="text/javascript" src="js/xoopsimagemanager.js"></script>
    <link href="css/xoopsimagemanager.css" rel="stylesheet" type="text/css"/>
    <base target="_self"/>
</head>

<body id="xoopsimagemanager" style="display: none;">
<form onsubmit="XoopsimagemanagerDialog.insert();return false;" action="#">
    <div class="tabs">
        <ul>
            <li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');"
                                                          onmousedown="return false;">{#xoopsimagemanager_dlg.tab_general}</a></span></li>
            <li id="appearance_tab"><span><a href="javascript:mcTabs.displayTab('appearance_tab','appearance_panel');" onmousedown="return false;">{#xoopsimagemanager_dlg.tab_appearance}</a></span>
            </li>
            <li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{#xoopsimagemanager_dlg.tab_advanced}</a></span>
            </li>
        </ul>
    </div>

    <div class="panel_wrapper">
        <div id="general_panel" class="panel current">
            <fieldset>
                <legend>{#xoopsimagemanager_dlg.general}</legend>
                <table class="properties">
                    <tr>
                        <td class="column1">
                            <label id="srclabel" for="src">{#xoopsimagemanager_dlg.src}</label>
                        </td>
                        <td colspan="2">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input name="src" type="text" id="src" value=""
                                               onchange="XoopsimagemanagerDialog.showPreviewImage(this.value);"/>
                                        <?php echo imageBrowser('src', $canbrowse); ?>
                                    </td>
                                    <td id="srcbrowsercontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="src_list">{#xoopsimagemanager_dlg.image_list}</label></td>
                        <td><select id="src_list" name="src_list"
                                    onchange="document.getElementById('src').value=this.options[this.selectedIndex].value;document.getElementById('alt').value=this.options[this.selectedIndex].text;document.getElementById('title').value=this.options[this.selectedIndex].text;XoopsimagemanagerDialog.showPreviewImage(this.options[this.selectedIndex].value);"></select>
                        </td>
                    </tr>
                    <tr>
                        <td class="column1"><label id="altlabel" for="alt">{#xoopsimagemanager_dlg.alt}</label></td>
                        <td colspan="2"><input id="alt" name="alt" type="text" value=""/></td>
                    </tr>
                    <tr>
                        <td class="column1"><label id="titlelabel" for="title">{#xoopsimagemanager_dlg.title}</label></td>
                        <td colspan="2"><input id="title" name="title" type="text" value=""/></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset>
                <legend>{#xoopsimagemanager_dlg.preview}</legend>
                <div id="prev"></div>
            </fieldset>
        </div>

        <div id="appearance_panel" class="panel">
            <fieldset>
                <legend>{#xoopsimagemanager_dlg.tab_appearance}</legend>

                <table border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td class="column1"><label id="alignlabel" for="align">{#xoopsimagemanager_dlg.align}</label></td>
                        <td>
                            <select id="align" name="align"
                                    onchange="XoopsimagemanagerDialog.updateStyle('align');XoopsimagemanagerDialog.changeAppearance();">
                                <option value="">{#not_set}</option>
                                <option value="baseline">{#xoopsimagemanager_dlg.align_baseline}</option>
                                <option value="top">{#xoopsimagemanager_dlg.align_top}</option>
                                <option value="middle">{#xoopsimagemanager_dlg.align_middle}</option>
                                <option value="bottom">{#xoopsimagemanager_dlg.align_bottom}</option>
                                <option value="text-top">{#xoopsimagemanager_dlg.align_texttop}</option>
                                <option value="text-bottom">{#xoopsimagemanager_dlg.align_textbottom}</option>
                                <option value="left">{#xoopsimagemanager_dlg.align_left}</option>
                                <option value="right">{#xoopsimagemanager_dlg.align_right}</option>
                            </select>
                        </td>
                        <td rowspan="6" valign="top">
                            <div class="alignPreview">
                                <img id="alignSampleImg" src="img/sample.gif" alt="{#xoopsimagemanager_dlg.example_img}"/>
                                Lorem ipsum, Dolor sit amet, consectetuer adipiscing loreum ipsum edipiscing elit, sed diam
                                nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.Loreum ipsum
                                edipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam
                                erat volutpat.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="widthlabel" for="width">{#xoopsimagemanager_dlg.dimensions}</label></td>
                        <td nowrap="nowrap">
                            <input name="width" type="text" id="width" value="" size="5" maxlength="5" class="size"
                                   onchange="XoopsimagemanagerDialog.changeHeight();"/> x
                            <input name="height" type="text" id="height" value="" size="5" maxlength="5" class="size"
                                   onchange="XoopsimagemanagerDialog.changeWidth();"/> px
                        </td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td><input id="constrain" type="checkbox" name="constrain" class="checkbox"/></td>
                                    <td><label id="constrainlabel" for="constrain">{#xoopsimagemanager_dlg.constrain_proportions}</label></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="vspacelabel" for="vspace">{#xoopsimagemanager_dlg.vspace}</label></td>
                        <td><input name="vspace" type="text" id="vspace" value="" size="3" maxlength="3" class="number"
                                   onchange="XoopsimagemanagerDialog.updateStyle('vspace');XoopsimagemanagerDialog.changeAppearance();"
                                   onblur="XoopsimagemanagerDialog.updateStyle('vspace');XoopsimagemanagerDialog.changeAppearance();"/>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="hspacelabel" for="hspace">{#xoopsimagemanager_dlg.hspace}</label></td>
                        <td><input name="hspace" type="text" id="hspace" value="" size="3" maxlength="3" class="number"
                                   onchange="XoopsimagemanagerDialog.updateStyle('hspace');XoopsimagemanagerDialog.changeAppearance();"
                                   onblur="XoopsimagemanagerDialog.updateStyle('hspace');XoopsimagemanagerDialog.changeAppearance();"/></td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="borderlabel" for="border">{#xoopsimagemanager_dlg.border}</label></td>
                        <td><input id="border" name="border" type="text" value="" size="3" maxlength="3" class="number"
                                   onchange="XoopsimagemanagerDialog.updateStyle('border');XoopsimagemanagerDialog.changeAppearance();"
                                   onblur="XoopsimagemanagerDialog.updateStyle('border');XoopsimagemanagerDialog.changeAppearance();"/></td>
                    </tr>

                    <tr>
                        <td><label for="class_list">{#class_name}</label></td>
                        <td><select id="class_list" name="class_list"></select></td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="stylelabel" for="style">{#xoopsimagemanager_dlg.style}</label></td>
                        <td colspan="2"><input id="style" name="style" type="text" value="" onchange="XoopsimagemanagerDialog.changeAppearance();"/>
                        </td>
                    </tr>

                    <!-- <tr>
                        <td class="column1"><label id="classeslabel" for="classes">{#xoopsimagemanager_dlg.classes}</label></td>
                        <td colspan="2"><input id="classes" name="classes" type="text" value="" onchange="selectByValue(this.form,'classlist',this.value,true);" /></td>
                    </tr> -->
                </table>
            </fieldset>
        </div>

        <div id="advanced_panel" class="panel">
            <fieldset>
                <legend>{#xoopsimagemanager_dlg.swap_image}</legend>

                <input type="checkbox" id="onmousemovecheck" name="onmousemovecheck" class="checkbox"
                       onclick="XoopsimagemanagerDialog.setSwapImage(this.checked);"/>
                <label id="onmousemovechecklabel" for="onmousemovecheck">{#xoopsimagemanager_dlg.alt_image}</label>

                <table border="0" cellpadding="4" cellspacing="0" width="100%">
                    <tr>
                        <td class="column1"><label id="onmouseoversrclabel" for="onmouseoversrc">{#xoopsimagemanager_dlg.mouseover}</label></td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input id="onmouseoversrc" name="onmouseoversrc" type="text" value=""/>
                                        <?php echo imageBrowser('onmouseoversrc', $canbrowse); ?>
                                    </td>
                                    <td id="onmouseoversrccontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="over_list">{#xoopsimagemanager_dlg.image_list}</label></td>
                        <td><select id="over_list" name="over_list"
                                    onchange="document.getElementById('onmouseoversrc').value=this.options[this.selectedIndex].value;"></select></td>
                    </tr>
                    <tr>
                        <td class="column1"><label id="onmouseoutsrclabel" for="onmouseoutsrc">{#xoopsimagemanager_dlg.mouseout}</label></td>
                        <td class="column2">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input id="onmouseoutsrc" name="onmouseoutsrc" type="text" value=""/>
                                        <?php echo imageBrowser('onmouseoutsrc', $canbrowse); ?>
                                    </td>
                                    <td id="onmouseoutsrccontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="out_list">{#xoopsimagemanager_dlg.image_list}</label></td>
                        <td><select id="out_list" name="out_list"
                                    onchange="document.getElementById('onmouseoutsrc').value=this.options[this.selectedIndex].value;"></select></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset>
                <legend>{#xoopsimagemanager_dlg.misc}</legend>

                <table border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td class="column1"><label id="idlabel" for="id">{#xoopsimagemanager_dlg.id}</label></td>
                        <td><input id="id" name="id" type="text" value=""/></td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="dirlabel" for="dir">{#xoopsimagemanager_dlg.langdir}</label></td>
                        <td>
                            <select id="dir" name="dir" onchange="XoopsimagemanagerDialog.changeAppearance();">
                                <option value="">{#not_set}</option>
                                <option value="ltr">{#xoopsimagemanager_dlg.ltr}</option>
                                <option value="rtl">{#xoopsimagemanager_dlg.rtl}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="langlabel" for="lang">{#xoopsimagemanager_dlg.langcode}</label></td>
                        <td>
                            <input id="lang" name="lang" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="usemaplabel" for="usemap">{#xoopsimagemanager_dlg.map}</label></td>
                        <td>
                            <input id="usemap" name="usemap" type="text" value=""/>
                        </td>
                    </tr>

                    <tr>
                        <td class="column1"><label id="longdesclabel" for="longdesc">{#xoopsimagemanager_dlg.long_desc}</label></td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input id="longdesc" name="longdesc" type="text" value=""/></td>
                                    <td id="longdesccontainer">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>

    <div class="mceActionPanel">
        <div style="float: left;">
            <input type="button" id="insert" name="insert" value="{#insert}" onclick="XoopsimagemanagerDialog.insert();"/>
        </div>

        <div style="float: right;">
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();"/>
        </div>
    </div>
</form>
</body>
</html>

<?php
/**
 * @param string $inputname
 * @param bool   $canbrowse
 *
 * @return string
 */
function imageBrowser($inputname = 'src', $canbrowse = false)
{
    $html = '';
    if ($canbrowse) {
        $html = "<img title=\"{#xoopsimagebrowser.desc}\" class=\"xoopsimagebrowser\" src=\"img/xoopsimagemanager.png\"
                onclick=\"javascript:XoopsImageBrowser('" . $inputname . "');\" />\n";
    }

    return $html;
}

?>
