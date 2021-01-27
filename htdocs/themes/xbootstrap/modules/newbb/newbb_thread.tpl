<div class="newbb-thread clearfix">
    <{if $forum_post_prefix|default:'' === null }>
        <{assign var=forum_post_prefix value="forumpost"}>
        <div id="<{$forum_post_prefix}>0"></div>
    <{/if}>
<div class="col-sm-3 col-md-3 text-center newbb-user-data">
    <{$topic_post.poster.link}>

    <{if $topic_post.poster.uid|default:'' gt -1}>
        <{if $topic_post.poster.uid != 0}>
            <{if $topic_post.poster.avatar != "blank.gif"}>
                    <img src="<{$xoops_upload_url}>/<{$topic_post.poster.avatar}>" alt="<{$topic_post.poster.name}>" class="img-circle img-thumbnail">
                <{else}>
                   <img src="<{$xoops_imageurl}>images/newbb-noavatar.png" alt="<{$topic_post.poster.name}>" class="img-circle img-thumbnail">
            <{/if}>

            <{if $topic_post.poster.rank.title !=""}>
                <ul class="list-unstyled">
                    <li><{$topic_post.poster.rank.title}></li>
                    <li><img src="<{$xoops_upload_url}>/<{$topic_post.poster.rank.image}>" alt="<{$topic_post.poster.rank.title}>"></li>
                </ul>
            <{/if}>

                <{if $infobox.show}>
                    <a data-toggle="collapse" href="#<{$topic_post.post_id}>" title="<{$smarty.const.THEME_INFO}>" class="btn btn-primary btn-sm mb10"><span class="glyphicon glyphicon-info-sign"></span></a>
                    <div id="<{$topic_post.post_id}>" class="collapse">
                        <ul class="list-unstyled text-left">
                            <li><{$smarty.const._MD_NEWBB_JOINED}>: <{$topic_post.poster.regdate}></li>
                            <{if $topic_post.poster.from}>
                                <li><{$smarty.const._MD_NEWBB_FROM}>
                                <{$topic_post.poster.from}></li>
                            <{/if}>

                            <{if $topic_post.poster.groups}>
                                <li><{$smarty.const._MD_NEWBB_GROUP}>
                                <{foreach item=group from=$topic_post.poster.groups}>
                                <{$group}>
                                <{/foreach}></li>
                            <{/if}>

                            <li>
                            <{$smarty.const._MD_NEWBB_POSTS}>:
                            <{if $topic_post.poster.posts gt 0}>
                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$topic_post.poster.uid}>" title="<{$smarty.const._ALL}>">
                                    <{$topic_post.poster.posts}>
                                </a>
                            <{else}>
                                0
                            <{/if}>
                            </li>

                            <{if $topic_post.poster.digests gt 0}>
                            <li>
                                <{$smarty.const._MD_NEWBB_DIGESTS}>: <{$topic_post.poster.digests}>
                            </li>
                            <{/if}>

                            <{if $topic_post.poster.level}>
                                <li><{$topic_post.poster.level}></li>
                            <{/if}>

                            <{if $topic_post.poster.status}>
                                <li><{$topic_post.poster.status}></li>
                            <{/if}>
                        </ul>
                    </div>
                <{/if}>
                <{else}>
                <div class="comUserRankText"><{$anonymous_prefix}><{$topic_post.poster.name}></div>
            <{/if}>
            <{else}>
            &nbsp;
        <{/if}>

        <ul class="list-unstyled">
            <{if $topic_post.poster_ip}>
                <li>IP: <a href="http://www.whois.sc/<{$topic_post.poster_ip}>" target="_blank"><{$topic_post.poster_ip}></a></li>
            <{/if}>

            <{if $topic_post.poster.uid|default:'' gt 0}>
                <li><{$smarty.const._MD_NEWBB_POSTEDON}><{$topic_post.post_date}></li>
            <{/if}>
        </ul>

</div>

<div class="col-sm-9 col-md-9 newbb-message-area">
    <div class="newbb-forum-title">
        <strong><{$topic_post.post_title}></strong>

        <{if $topic_post.post_id > 0}>
            <a id="<{$forum_post_prefix|default:''}><{$topic_post.post_id}>" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?post_id=<{$topic_post.post_id}>#forumpost<{$topic_post.post_no}>" title="<{$topic_post.post_no}>" class="newbb-post-anchor">
                #<{$topic_post.post_no}>
            </a>
        <{/if}>
    </div><!-- .newbb-forum-title -->

    <{$topic_post.post_text}>

    <{if $topic_post.post_attachment}>
    <div class="newbb-thread-attachment">
        <{$topic_post.post_attachment}>
    </div>
    <{/if}>

    <{if $topic_post.post_edit}>
        <div class="text-right">
            <small class="text-muted"><em><{$topic_post.post_edit}></em></small>
        </div>
    <{/if}>

    <{if $topic_post.post_signature}>
        <div class="newbb-user-signature">
            <{$topic_post.post_signature}>
        </div>
    <{/if}>
</div>
</div><!-- .newbb-thread -->

<div class="clearfix newbb-links mb10">
    <div class="col-md-6 nompl hidden-xs">
    <{if $topic_post.thread_action}>
        <{foreach item=btn from=$topic_post.thread_action}>
            <a href="<{$btn.link}>&amp;post_id=<{$topic_post.post_id}>" title="<{$btn.name}>" <{if $btn.target}>target="<{$btn.target}>"<{/if}>>
                <{$btn.image}>
            </a>
        <{/foreach}>
    <{/if}>
    </div>

    <div class="col-md-6 text-right nompl">
    <{if $mode gt 1 && $topic_post.poster.uid gt -1}>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.post.php?post_id=<{$topic_post.post_id}>&amp;op=split&amp;mode=1" title="<{$smarty.const._MD_NEWBB_SPLIT_ONE}>">
            <{$smarty.const._MD_NEWBB_SPLIT_ONE}>
        </a>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.post.php?post_id=<{$topic_post.post_id}>&amp;op=split&amp;mode=2" title="<{$smarty.const._MD_NEWBB_SPLIT_TREE}>">
            <{$smarty.const._MD_NEWBB_SPLIT_TREE}>
        </a>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.post.php?post_id=<{$topic_post.post_id}>&amp;op=split&amp;mode=3" title="<{$smarty.const._MD_NEWBB_SPLIT_ALL}>">
            <{$smarty.const._MD_NEWBB_SPLIT_ALL}>
        </a>
            <input type="checkbox" name="post_id[]" id="post_id[<{$topic_post.post_id}>]" value="<{$topic_post.post_id}>">
    <{else}>
        <{if $topic_post.thread_buttons}>

                <{foreach item=btn from=$topic_post.thread_buttons}>
                    <a class="btn btn-primary btn-xs" href="<{$btn.link}>&amp;post_id=<{$topic_post.post_id}>" title="<{$btn.name}>"><{$btn.image}></a>
                <{/foreach}>
        <{/if}>
    <{/if}>
    <a class="btn btn-success btn-xs" href="#threadtop" title="<{$smarty.const._MD_NEWBB_TOP}>"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>
    </div>
</div>
