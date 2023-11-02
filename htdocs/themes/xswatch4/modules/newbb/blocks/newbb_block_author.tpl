<table class="table table-hover" cellspacing="1">

    <{if isset($block.disp_mode) &&  $block.disp_mod == 0}>
        <tr>
            <th><{$smarty.const._MB_NEWBB_AUTHOR}></th>
            <th><{$smarty.const._MB_NEWBB_COUNT}></th>
        </tr>
        <{foreach item=author from=$block.authors|default:null key=uid }>
        <tr>
            <td><a href="<{$xoops_url}>/userinfo.php?uid=<{$uid}>"><{$author.name}></a></td>
            <td align="center"><{$author.count}></td>
        </tr>
    <{/foreach}>

    <{elseif $block.disp_mode == 1}>

        <{foreach item=author from=$block.authors|default:null key=uid }>
        <tr>
            <td><a href="<{$xoops_url}>/userinfo.php?uid=<{$uid}>"><{$author.name}></a> <{$author.count}></td>
        </tr>
    <{/foreach}>

    <{/if}>

</table>
<{if !empty($block.indexNav)}>
    <div class="pagenav">
        <a class="btn btn-secondary" href="<{$xoops_url}>/modules/newbb/"><{$smarty.const._MB_NEWBB_VSTFRMS}></a>
    </div>
<{/if}>
