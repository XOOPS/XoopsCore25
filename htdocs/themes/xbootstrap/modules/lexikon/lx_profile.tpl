<table id="moduleheader">
    <tr>
        <td width="100%"><span class="leftheader"><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a> <img
                        src='assets/images/arrow.gif' align='absmiddle'/> <a
                        href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a> <img
                        src='assets/images/arrow.gif'
                        align='absmiddle'/> <{$smarty.const._MD_LEXIKON_AUTHORPROFILE}> <{$author_name}></span>
        </td>
        </span></td>
        <td width="100"><span class="rightheader"><{$lang_modulename}></span></td>
    </tr>
</table>

<br>
<div class="clearer">
    <h3 class="cat" style="text-align: left; clear:right;"><img align="left" src='<{$user_avatarurl}>' border='0'
                                                                alt=''/><{$lang_authorprofile}> <{$author_name_with_link}>
    </h3>
</div>
<{*
<div class="clearer"><DIV style="text-align: left; font-size: small;">
<{$authorterms}>
</div></div>
*}>
<br>
<br>
<br><br>
<div class="clearer">
    <DIV style="text-align: left; font-size: small;">
        <{if $nothing==false}>
            <img src='<{$xoops_url}>/modules/<{$lang_moduledirname}>/assets/images/square-green.gif' align='absmiddle'/>
            <{$submitted}>
            <br>
            <img src='<{$xoops_url}>/modules/<{$lang_moduledirname}>/assets/images/square-red.gif' align='absmiddle'/>
            <{$waiting}>
        <{/if}>
        <br><br>
    </div>
</div>

<div class="clearer">
    <table class="outer" width="100%" border="0" cellspacing="1" cellpadding="2">
        <!--tr>
        <th colspan="4" class="odd" align="center"><{$lang_terms_by_this_author}> <{$author_name}></th>
    </tr-->
        <tr class="odd" align="center">
            <td><{$smarty.const._MD_LEXIKON_DATETERM}></td>
            <td><{$smarty.const._MD_LEXIKON_TERMS}></td>
            <td><{$smarty.const._MD_LEXIKON_HITS}></td>
        </tr>
        <{if $nothing==false}>
            <{foreach item=d from=$entries}>
                <tr class="<{cycle values="even,odd"}>">
                    <td align="center" style="font-size:11px;"><{$d.date}></td>
                    <td align="left"><a href="entry.php?entryID=<{$d.id}>"><{$d.name}></a></td>
                    <td align="center"><{$d.counter}></td>
                </tr>
            <{/foreach}>
        <{/if}>
    </table>
    <{if $navi==true}>
    <div style="text-align: right; font-size: small;">
        <{$authortermsarr.navbar}>
    </div>
</div><{/if}>

<div style="text-align: center; font-size: small;">
    <{if $nothing==true}><{$nothing}><{/if}>
</div>
