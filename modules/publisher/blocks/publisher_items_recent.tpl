<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <th class="head"><{$block.lang_title}></th>
        <th class="head" align="left"><{$block.lang_category}></th>
        <th class="head" align="center" width="100"><{$block.lang_poster}></th>
        <th class="head" align="right" width="120"><{$block.lang_date}></th>
    </tr>
    </thead>
    <tbody>
    <{foreach item=item from=$block.items}>
        <tr>
            <td><{$item.itemlink}></td>
            <td align="left"><{$item.categorylink}></td>
            <td align="center"><{$item.poster}></td>
            <td align="right"><{$item.date}></td>
        </tr>
    <{/foreach}>
    </tbody>

</table>

<div style="text-align:right; padding: 5px;">
    <a class="btn btn-primary btn-xs" href="<{$publisher_url}>"><{$block.lang_visitItem}></a>
</div>
