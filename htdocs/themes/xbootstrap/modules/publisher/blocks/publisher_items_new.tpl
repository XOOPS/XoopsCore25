<table class"table" cellpadding="0" cellspacing="0" border="0">
<{foreach item=newitems from=$block.newitems}>
    <tr class="<{cycle values=" even,odd"}>">
        <{if $newitems.image}>
            <td style="padding: 5px 0;" width="120px">
                <img style="padding: 1px; margin: 2px; border: 1px solid #c3c3c3;" width="110" src="<{$newitems.image}>" title="<{$newitems.image_name}>"
                     alt="<{$newitems.image_name}>"/>
            </td>
        <{/if}>
        <td>
            <strong><{$newitems.link}></strong>
            <br>
            <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-user"></span>&nbsp;<{$newitems.poster}></span>
            <{if $block.show_order == '1'}>
                <span style="padding: 3px 16px 0 0; font-size: 11px;"><span class="glyphicon glyphicon-calendar"></span><{$newitems.new}></span>
            <{/if}>
        </td>
    </tr>
<{/foreach}>
</table>
