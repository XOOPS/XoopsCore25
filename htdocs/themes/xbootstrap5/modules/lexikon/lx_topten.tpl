<table id="moduleheader">
    <tbody>
    <tr>
        <td width="100%"><span class="leftheader"><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a>
                → <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a>
                → <{$intro}></span></td>
        <td width="100"><span class="rightheader"><nobr><{$lang_modulename}></nobr></span>
        </td>
    </tr>
    </tbody>
</table>

<div align="center" style="width: 100%;">
    <h3 class="cat" align="center"><b><{$xoops_pagetitle}></b></h3>
</div>
<br><br>

<!-- Start ranking loop -->
<{foreach item=ranking from=$rankings|default:null}>

    <{**}>

    <{foreach item=terms from=$ranking.terms|default:null}>
        <{*">*}>

        <{**}>

    <{/foreach}>
    <table>
        <tbody>
        <tr>
            <th class="head" colspan="6"><{if $multicats == 1}><{$lang_category}>: <{/if}><a style="color:#FFFFFF;" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$ranking.cid}>"><{$ranking.title}></a>
                (<{$lang_sortby}>)
            </th>
        </tr>
        <tr>
            <td class="head" width="7%"><{$lang_rank}></td>
            <td class="head" width="53%"><{$lang_term}></td>
            <td class="head" width="5%" align="center"><{$lang_hits}></td>
            <td class="head" width="9%" align="center">
                <nobr><{$lang_date}></nobr>
            </td>
            <td class="head" width="8%" align="right"><{$lang_def}></td>
        </tr>
        <!-- Start links loop -->
        <tr class="<{cycle values=" even,odd"}>=""></tr><tr>
        <td class="even"><{$terms.rank}></td>
<td class=" odd
        "><a title="<{$terms.definition}>" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/entry.php?entryID=<{$terms.id}>"><{$terms.title}></a>
        </td>
        <td class="even" align="center"><{$terms.counter}></td>
        <td class="odd" align="center">
            <nobr><{$terms.datesub}></nobr>
        </td>
        <td class="even" align="right"><{$terms.definition}></td>
        </tr><!-- End links loop-->
        </tbody>
    </table>
    <br>
<{/foreach}>
<!-- End ranking loop -->
