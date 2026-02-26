<{include file="db:system_header.tpl"}>
<{if isset($modifs_mods)}>
    <form action="admin.php" method="post">
        <table class="outer" cellspacing="1">
            <thead>
                <tr class="txtcenter">
                    <th><{$smarty.const._AM_SYSTEM_MODULES_MODULE}></th>
                </tr>
            </thead>
            <tbody>
            <{foreach item=row from=$modifs_mods|default:null}>
                <tr class="txtcenter <{cycle values='odd, even'}>">
                    <td>
                        <{$row.oldname}>
                        <{if $row.oldname != $row.newname}>
                        <span class="bold red">&nbsp;&raquo;&nbsp;<{$row.newname}></span>
                        <{/if}>
                        <input type="hidden" name="module[]" value="<{$row.mid}>"/>
                        <input type="hidden" name="oldname[<{$row.mid}>]" value="<{$row.oldname}>"/>
                        <input type="hidden" name="newname[<{$row.mid}>]" value="<{$row.newname}>"/>
                    </td>
                </tr>
                <{/foreach}>
            </tbody>
            <tfoot>
            <tr class="txtcenter foot">
                <td>
                    <input class="formButton" type="submit" value="<{$smarty.const._AM_SYSTEM_MODULES_SUBMIT}>"/>&nbsp;
                    <input class="formButton" type="button" value="<{$smarty.const._AM_SYSTEM_MODULES_CANCEL}>"
                           onclick="location='admin.php?fct=modulesadmin'"/>
                    <input type="hidden" name="fct" value="modulesadmin"/>
                    <input type="hidden" name="op" value="submit"/>
                    <{$input_security}>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
<{else}>
    <div id="xo-module-log">
        <{if isset($result)}>
        <div class="logger">
            <{foreach item=row from=$result|default:null}>
            <div class="spacer"><{$row}></div>
            <{/foreach}>
        </div>
        <{/if}>
        <a href="admin.php?fct=modulesadmin"><{$smarty.const._AM_SYSTEM_MODULES_BTOMADMIN}></a>
    </div>
<{/if}>
