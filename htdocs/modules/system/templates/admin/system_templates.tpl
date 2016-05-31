<{includeq file="db:system_header.tpl"}>

<{if $index}>
    <br class="clear"/>
    <div class="spacer">
        <table class="outer ui-corner-all" cellspacing="1">
            <tr>
                <th><{$smarty.const._AM_SYSTEM_TEMPLATES_YOUR_THEMES}></th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <td class="aligntop width10">
                    <div id="fileTree" class="display_folder"></div>
                </td>
                <td class="aligntop">
                    <div id="display_form"><{$form}></div>
                    <div id="display_contenu"></div>
                    <div id='display_message' class="txtcenter" style="display:none;"></div>
                    <div id='loading' class="txtcenter" style="display:none;"><br><br><img src="images/loading.gif" title="Loading" alt="Loading"/>
                    </div>
                </td>
            </tr>
        </table>

        <br class="clear"/>
    </div>
<{else}>
    <br>
    <{if $verif}>
        <{$infos}>
    <{else}>
        <div class="txtcenter"><{$smarty.const._AM_SYSTEM_TEMPLATES_NOT_CREATED}></div>
    <{/if}>
<{/if}>
