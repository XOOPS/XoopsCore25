<{includeq file="db:system_header.tpl"}>
<{if $modifs_mods}>
    <form action="admin.php" method="post">
        <table class="outer" cellspacing="1">
            <thead>
            <tr class="txtcenter">
                <th><{$smarty.const._AM_SYSTEM_MODULES_MODULE}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_ACTION}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_ORDER}></th>
            </tr>
            </thead>
            <tbody>
            <{foreach item=row from=$modifs_mods}>
                <tr class="txtcenter <{cycle values='odd, even'}>">
                    <td>
                        <{$row.oldname}>
                        <{if $row.oldname != $row.newname}>
                            <span class="bold red">&nbsp;&raquo;&nbsp;<{$row.newname}></span>
                        <{/if}>
                    </td>
                    <td>
                        <{if $row.oldstatus != $row.newstatus}>
                            <{if $row.newstatus == 1}>
                                <div class="bold red"><{$smarty.const._AM_SYSTEM_MODULES_ACTIVATE}></div>
                            <{else}>
                                <div class="bold red"><{$smarty.const._AM_SYSTEM_MODULES_DEACTIVATE}></div>
                            <{/if}>
                        <{else}>
                            <{$smarty.const._AM_SYSTEM_MODULES_NOCHANGE}>
                        <{/if}>
                    </td>
                    <td>
                        <{if $row.oldweight != $row.weight}>
                            <div class="bold red"><{$row.weight}></div>
                        <{else}>
                            <{$row.weight}>
                        <{/if}>
                        <input type="hidden" name="module[]" value="<{$row.mid}>"/>
                        <input type="hidden" name="oldname[<{$row.mid}>]" value="<{$row.oldname}>"/>
                        <input type="hidden" name="newname[<{$row.mid}>]" value="<{$row.newname}>"/>
                        <input type="hidden" name="oldstatus[<{$row.mid}>]" value="<{$row.oldstatus}>"/>
                        <input type="hidden" name="newstatus[<{$row.mid}>]" value="<{$row.newstatus}>"/>
                        <input type="hidden" name="oldweight[<{$row.mid}>]" value="<{$row.oldweight}>"/>
                        <input type="hidden" name="weight[<{$row.mid}>]" value="<{$row.weight}>"/>
                    </td>
                </tr>
            <{/foreach}>
            </tbody>
            <tfoot>
            <tr class="txtcenter foot">
                <td colspan="3">
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
        <{if $result}>
            <div class="logger">
                <{foreach item=row from=$result}>
                    <div class="spacer"><{$row}></div>
                <{/foreach}>
            </div>
        <{/if}>
        <a href="admin.php?fct=modulesadmin"><{$smarty.const._AM_SYSTEM_MODULES_BTOMADMIN}></a>
    </div>
<{/if}>
