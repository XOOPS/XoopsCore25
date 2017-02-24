<{if $results}>
<table class="table table-hover">
    <caption><{$search_info}></caption>
    <thead>
    <tr>
        <th><{$smarty.const._MD_FORUMC}></th>
        <th><{$smarty.const._MD_SUBJECT}></th>
        <th><{$smarty.const._MD_AUTHOR}></th>
        <th><{$smarty.const._MD_POSTTIME}></th>
    </tr>
    </thead>
    <tbody>
    <{section name=i loop=$results}>
    <tr>
        <td><a href="<{$results[i].forum_link}>"><{$results[i].forum_name}></a></td>
        <td><a href="<{$results[i].link}>"><{$results[i].title}></a>
            <{if $results[i].post_text }>
            <div><{$results[i].post_text}></div>
            <{/if}>
        </td>
        <td class="even"><{$results[i].poster}></a></td>
        <td class="odd"><{$results[i].post_time}></td>
    </tr>
    <{/section}>
    </tbody>
</table>
    <{if $search_prev_url}><a href="<{$search_prev_url}>" class="btn btn-primary" role="button" title="<{$smarty.const._SR_PREVIOUS}>"><span class="glyphicon glyphicon-arrow-left"></span></a><{/if}>
    <{if $search_next_url}><a href="<{$search_next_url}>" class="btn btn-primary" role="button" title="<{$smarty.const._SR_NEXT}>"><span class="glyphicon glyphicon-arrow-right"></span></a><{/if}>
<{/if}>
<{if $lang_nomatch}>
    <div class="resultMsg"> <{$lang_nomatch}> </div>
    <br>
<{/if}>
