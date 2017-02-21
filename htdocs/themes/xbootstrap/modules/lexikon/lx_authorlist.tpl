<table id="moduleheader">
    <tr>
        <td width="100%"><span class="leftheader"><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a> <img
                        src='assets/images/arrow.gif' align='absmiddle'/> <a
                        href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a> <img
                        src='assets/images/arrow.gif' align='absmiddle'/> <a
                        href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/authorlist.php"><{$smarty.const._MD_LEXIKON_CONTRIBUTORS}></a></span>
        </td>
        </span></td>
        <td width="100"><span class="rightheader"><{$lang_modulename}></span></td>
    </tr>
</table>
<br>
<div class="clearer">

    <h3 class="cat" style="text-align: left; clear:right;"><{$smarty.const._MD_LEXIKON_CONTRIBUTORS}></h3>
    <br>
    <br>
    <DIV style="text-align: left; font-size: small;">
        <{$smarty.const._MD_LEXIKON_CONTRIBUTORSLIST}></DIV>
    <br>

    <div class="clearer">
        <table class="outer" width="100%" border="0" cellspacing="1" cellpadding="2">
            <!--tr>
        <td colspan="7" class="odd" align="center"><strong><{$smarty.const._MD_LEXIKON_CONTRIBUTORS}></strong></td>
    </tr-->
            <tr class="odd" align="center">
                <td>#</td>
                <{if $authorlistext}>
                    <td><{$smarty.const._MD_LEXIKON_MAIL}></td>
                <{/if}>
                <td><{$smarty.const._MD_LEXIKON_AUTHOR}></td>
                <td><{$smarty.const._MD_LEXIKON_TOTAL}></td>
                <{if $authorlistext}>
                    <td><{$smarty.const._MD_LEXIKON_PM}></td>
                    <td><{$smarty.const._MD_LEXIKON_LOCATION}></td>
                    <td><{$smarty.const._MD_LEXIKON_WWW}></td>
                <{/if}>
            </tr>
            <{foreach item=author from=$authors}>
                <tr class="<{cycle values="even,odd"}>">
                    <td align="center"><{$author.id}></td>
                    <{if $authorlistext}>
                        <td align="center"><{$author.email}></td>
                    <{/if}>
                    <td align="center" style="font-size:12px;"><a
                                href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/profile.php?uid=<{$author.uid}>"><{$author.name}></a>
                    </td>
                    <td align="center"><{$author.total}></td>
                    <{if $authorlistext}>
                        <td align="center"><{$author.pm}></td>
                        <td align="center"><{$author.location}></td>
                        <td align="center"><{$author.url}></td>
                    <{/if}>
                </tr>
            <{/foreach}>
        </table>
