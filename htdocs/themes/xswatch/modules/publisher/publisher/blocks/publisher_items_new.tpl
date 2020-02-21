<table class"table" cellpadding="0" cellspacing="0" border="0">
<{foreach item=newitems from=$block.newitems}>
    <tr class="<{cycle values=" even,odd"}>">
        <{if $newitems.image}>
            <td style="padding: 5px 0;" width="120px">
                <a href="<{$newitems.itemurl}>"><img style="padding: 1px; margin: 2px; border: 1px solid #c3c3c3;" width="110" src="<{$newitems.image}>" title="<{$newitems.alt}>"
                     alt="<{$newitems.alt}>"/></a>
            </td>
        <{/if}>
        <td>
            <strong><{$newitems.link}></strong>
            <{if $block.show_order == '1'}>
                    (<{$newitems.new}>)
                <{/if}>
            <br>
           <{if $block.show_summary == '1'}><{$newitems.summary}><br><{/if}>
           <{if $block.show_category == '1'}>
            <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-tag"></span>&nbsp;<{$newitems.categorylink}></span>
            <{/if}>
            <{if $block.show_poster == '1'}>
            <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-user"></span>&nbsp;<{$newitems.poster}></span>
            <{/if}>
            <{if $block.show_date == '1'}>
                <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$newitems.date}></span>
            <{/if}>
            <{if $block.show_hits == '1'}>
                <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$newitems.hits}> <{$newitems.lang_hits}> </span>
            <{/if}>
            <{if $block.show_comment == '1' && $newitems.cancomment && $newitems.comment != -1}>
                <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-comment"></span>&nbsp;<{$newitems.comment}></span>
            <{/if}>
            <{if $block.show_rating == '1'}>
                <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-star"></span>&nbsp;<{$newitems.rating}></span>
            <{/if}>
        


</td>
    </tr>
<{/foreach}>
</table>
