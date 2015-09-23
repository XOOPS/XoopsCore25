<table cellspacing="1" class="outer width100">
    <{foreach item=comment from=$block.comments}>
        <tr class="<{cycle values='even,odd'}>">
            <td class="txtcenter"><img src="<{$xoops_url}>/images/subject/<{$comment.icon}>" alt=""/></td>
            <td><{$comment.title}></td>
            <td class="txtcenter"><{$comment.module}></td>
            <td class="txtcenter"><{$comment.poster}></td>
            <td class="txtright"><{$comment.time}></td>
        </tr>
    <{/foreach}>
</table>
