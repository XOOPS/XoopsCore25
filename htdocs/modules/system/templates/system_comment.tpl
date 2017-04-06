<!-- start comment post -->
<tr>
    <td class="head"><a id="comment<{$comment.id}>"></a> <{$comment.poster.uname}></td>
    <td class="head">
        <div class="comDate"><span class="comDateCaption"><{$lang_posted}>:</span> <{$comment.date_posted}>&nbsp;&nbsp;
		<{if $comment.date_posted != $comment.date_modified}>
			<span class="comDateCaption"><{$lang_updated}>:</span><{$comment.date_modified}>
		<{/if}>
        </div>
    </td>
</tr>
<tr>

    <{if $comment.poster.id != 0}>
        <td class="odd">
            <div class="comUserRank">
                <div class="comUserRankText"><{$comment.poster.rank_title}></div>
                <img class="comUserRankImg" src="<{$xoops_upload_url}>/<{$comment.poster.rank_image}>" alt=""/>
            </div>
            <img class="comUserImg" src="<{$xoops_upload_url}>/<{$comment.poster.avatar}>" alt=""/>

            <div class="comUserStat"><span class="comUserStatCaption"><{$lang_joined}>:</span> <{$comment.poster.regdate}></div>
            <div class="comUserStat"><span class="comUserStatCaption"><{$lang_from}>:</span> <{$comment.poster.from}></div>
            <div class="comUserStat"><span class="comUserStatCaption"><{$lang_posts}>:</span> <{$comment.poster.postnum}></div>
            <div class="comUserStatus"><{$comment.poster.status}></div>
        </td>
    <{else}>
        <td class="odd"></td>
    <{/if}>

    <td class="odd">
        <div class="comTitle"><{$comment.image}><{$comment.title}></div>
        <div class="comText"><{$comment.text}></div>
    </td>
</tr>
<tr>
    <td class="even"></td>

    <{if $xoops_iscommentadmin == true}>
        <td class="even txtright">
            <button type="button" class="btn btn-default" onclick="window.location.href = '<{$editcomment_link}>&amp;com_id=<{$comment.id}>'" title="<{$lang_edit}>"><span class="fa fa-pencil-square-o fa-fw"></span></button>
            <button type="button" class="btn btn-default" onclick="window.location.href = '<{$deletecomment_link}>&amp;com_id=<{$comment.id}>'" title="<{$lang_delete}>"><span class="fa fa-remove fa-fw"></span></button>
            <button type="button" class="btn btn-default" onclick="window.location.href = '<{$replycomment_link}>&amp;com_id=<{$comment.id}>'" title="<{$lang_reply}>"><span class="fa fa-reply fa-fw"></span></button>
        </td>
    <{elseif $xoops_isuser == true && $xoops_userid == $comment.poster.id}>
        <td class="even txtright">
            <button type="button" class="btn btn-default" onclick="window.location.href = '<{$editcomment_link}>&amp;com_id=<{$comment.id}>'" title="<{$lang_edit}>"><span class="fa fa-pencil-square-o fa-fw"></span></button>
            <button type="button" class="btn btn-default" onclick="window.location.href = '<{$replycomment_link}>&amp;com_id=<{$comment.id}>'" title="<{$lang_reply}>"><span class="fa fa-reply fa-fw"></span></button>
        </td>
    <{elseif $xoops_isuser == true || $anon_canpost == true}>
        <td class="even txtright">
            <button type="button" class="btn btn-default" onclick="window.location.href = '<{$replycomment_link}>&amp;com_id=<{$comment.id}>'" title="<{$lang_reply}>"><span class="fa fa-reply fa-fw"></span></button>
        </td>
    <{else}>
        <td class="even"></td>
    <{/if}>

</tr>
<!-- end comment post -->
