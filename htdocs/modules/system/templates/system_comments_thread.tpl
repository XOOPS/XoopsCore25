<{section name=i loop=$comments}>
    <br>
    <table cellspacing="1" class="outer">
        <tr>
            <th class="width20"><{$lang_poster}></th>
            <th><{$lang_thread}></th>
        </tr>
        <{include file="db:system_comment.tpl" comment=$comments[i]}>
    </table>
    <{if $show_threadnav == true}>
        <div class="txtleft marg3 pad5">
            <a href="<{$comment_url}>" title="<{$lang_top}>"><{$lang_top}></a> | <a
                    href="<{$comment_url}>&amp;com_id=<{$comments[i].pid}>&amp;com_rootid=<{$comments[i].rootid}>#newscomment<{$comments[i].pid}>"><{$lang_parent}></a>
        </div>
    <{/if}>

    <{if $comments[i].show_replies == true}>
        <!-- start comment tree -->
        <br>
        <table cellspacing="1" class="outer">
            <tr>
                <th class="width50"><{$lang_subject}></th>
                <th class="width20 txtcenter"><{$lang_poster}></th>
                <th class="txtright"><{$lang_posted}></th>
            </tr>
            <{foreach item=reply from=$comments[i].replies}>
                <tr>
                    <td class="even"><{$reply.prefix}> <a href="<{$comment_url}>&amp;com_id=<{$reply.id}>&amp;com_rootid=<{$reply.root_id}>" title=""><{$reply.title}></a>
                    </td>
                    <td class="odd txtcenter"><{$reply.poster.uname}></td>
                    <td class="even right"><{$reply.date_posted}></td>
                </tr>
            <{/foreach}>
        </table>
        <!-- end comment tree -->
    <{/if}>

<{/section}>
<{if $commentform}>
    <div class="commentform"><{$commentform}></div><{/if}>
