<div class="newbb-thread clearfix">
    <{if isset($forum_post_prefix) && $forum_post_prefix === null }>
        <{assign var=forum_post_prefix value="forumpost"}>
        <div id="<{$forum_post_prefix}>0"></div>
    <{/if}>
    <div class="container">
    <div class="row">
<div class="col-3 text-center newbb-user-data">
    <{$topic_post.poster.link}>

    <{if isset($topic_post.poster.uid) &&  $topic_post.poster.uid > -1}>
        <{if isset($topic_post.poster.uid) && $topic_post.poster.uid != 0}>
            <{if isset($topic_post.poster.avatar) && $topic_post.poster.ava != "blank.gif"}>
                    <img src="<{$xoops_upload_url}>/<{$topic_post.poster.avatar}>" alt="<{$topic_post.poster.name}>" class="img-circle img-thumbnail">
                <{else}>
                   <img src="<{$xoops_imageurl}>images/no-avatar.png" alt="<{$topic_post.poster.name}>" class="img-circle img-thumbnail">
            <{/if}>

            <{if !empty($topic_post.poster.rank.title)}>
                <ul class="list-unstyled">
                    <li><span class="small"><{$topic_post.poster.rank.title}></span></li>
                    <li><img class="img-fluid" src="<{$xoops_upload_url}>/<{$topic_post.poster.rank.image}>" alt="<{$topic_post.poster.rank.title}>"></li>
                </ul>
            <{/if}>

                <{if isset($infobox.show)}>
                    <button  data-toggle="collapse" data-target="#p<{$topic_post.post_id}>" title="<{$smarty.const.THEME_INFO}>" class="btn btn-primary btn-sm mb10"><span class="fa fa-info"></span></button>
                    <div id="p<{$topic_post.post_id}>" class="collapse">
                        <ul class="list-unstyled text-left">
                            <li><{$smarty.const._MD_NEWBB_JOINED}>: <{$topic_post.poster.regdate}></li>
                            <{if $topic_post.poster.from}>
                                <li><{$smarty.const._MD_NEWBB_FROM}>
                                <{$topic_post.poster.from}></li>
                            <{/if}>

                            <{if $topic_post.poster.groups}>
                                <li><{$smarty.const._MD_NEWBB_GROUP}>
                                <{foreach item=group from=$topic_post.poster.groups|default:null}>
                                <{$group}>
                                <{/foreach}></li>
                            <{/if}>

                            <li>
                            <{$smarty.const._MD_NEWBB_POSTS}>:
                            <{if isset($topic_post.poster.posts) && $topic_post.poster.posts > 0}>
                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$topic_post.poster.uid}>" title="<{$smarty.const._ALL}>">
                                    <{$topic_post.poster.posts}>
                                </a>
                            <{else}>
                                0
                            <{/if}>
                            </li>
                                <{if isset($topic_post.poster.digests) && is_array($topic_post.poster.digests) && $topic_post.poster.digests|count > 0}>
                            <li>
                                <{$smarty.const._MD_NEWBB_DIGESTS}>: <{$topic_post.poster.digests}>
                            </li>
                            <{/if}>

                            <{if isset($topic_post.poster.level)}>
                                <li><{$topic_post.poster.level}></li>
                            <{/if}>

                            <{if !empty($topic_post.poster.status)}>
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
            <{if !empty($topic_post.poster_ip)}>
                <li><span class="d-none d-sm-block small">IP: <a href="https://www.whois.sc/<{$topic_post.poster_ip}>" target="_blank"><{$topic_post.poster_ip}></a></li>
            <{/if}>

            <{if isset($topic_post.poster.uid) &&  $topic_post.poster.uid > 0}>
            <li><span class="small"><span class="d-none d-sm-block"><{$smarty.const._MD_NEWBB_POSTEDON}></span><{$topic_post.post_date}></span></li>
            <{/if}>
        </ul>

</div>

<div class="col-8 newbb-message-area">
    <br>
    <div class="newbb-forum-title">
        <strong><{$topic_post.post_title|default:''}></strong>

        <{if isset($topic_post.post_id) && $topic_post.post_id > 0}>
            <a id="<{$forum_post_prefix|default:''}><{$topic_post.post_id}>" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?post_id=<{$topic_post.post_id}>#forumpost<{$topic_post.post_no}>" title="<{$topic_post.post_no}>" class="newbb-post-anchor">
                #<{$topic_post.post_no}>
            </a>
        <{/if}>
    </div>

    <{$topic_post.post_text}>

    <{if !empty($topic_post.post_attachment)}>
    <div class="newbb-thread-attachment">
        <{$topic_post.post_attachment}>
    </div>
    <{/if}>

    <{if !empty($topic_post.post_edit)}>
        <div class="text-right">
            <small class="text-muted"><em><{$topic_post.post_edit}></em></small>
        </div>
    <{/if}>

    <{if !empty($topic_post.post_signature)}>
        <div class="newbb-user-signature">
            <{$topic_post.post_signature}>
        </div>
    <{/if}>
</div>
    </div>
</div>
</div>

<div class="row clearfix newbb-links mb10">
    <div class="col-3 mr-auto d-none d-sm-block">
    <{if !empty($topic_post.thread_action)}>
        <{foreach item=btn from=$topic_post.thread_action|default:null}>
            <a href="<{$btn.link}>&amp;post_id=<{$topic_post.post_id}>" title="<{$btn.name}>" <{if $btn.target}>target="<{$btn.target}>"<{/if}>>
                <{$btn.image|default:''}>
            </a>
        <{/foreach}>
    <{/if}>
    </div>

    <div class="col-auto">
    <{if (isset($mode) && $mode > 1) && (isset($topic_post.poster.uid) && $topic_post.poster.uid > -1)}>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.post.php?post_id=<{$topic_post.post_id}>&amp;op=split&amp;mode=1" title="<{$smarty.const._MD_NEWBB_SPLIT_ONE}>">
            <{$smarty.const._MD_NEWBB_SPLIT_ONE}>
        </a>
            |
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.post.php?post_id=<{$topic_post.post_id}>&amp;op=split&amp;mode=2" title="<{$smarty.const._MD_NEWBB_SPLIT_TREE}>">
            <{$smarty.const._MD_NEWBB_SPLIT_TREE}>
        </a>
            |
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.post.php?post_id=<{$topic_post.post_id}>&amp;op=split&amp;mode=3" title="<{$smarty.const._MD_NEWBB_SPLIT_ALL}>">
            <{$smarty.const._MD_NEWBB_SPLIT_ALL}>
        </a>
            <input type="checkbox" name="post_id[]" id="post_id[<{$topic_post.post_id}>]" value="<{$topic_post.post_id}>">
    <{else}>
        <{if !empty($topic_post.thread_buttons)}>
            <{assign var='bantext' value=`$smarty.const._MD_NEWBB_SUSPEND_MANAGEMENT`}>
                <{assign var='banprompt' value=">$bantext<"}>

                <{foreach item=btn from=$topic_post.thread_buttons|default:null}>
                   <a class="btn btn-primary btn-xs" href="<{$btn.link}>&amp;post_id=<{$topic_post.post_id}>" title="<{$btn.name}>"><{$btn.image|replace:$banprompt:'><span class="fa fa-ban" aria-hidden="true"><'|replace:forum_button:xforum_button}></a>
                <{/foreach}>
        <{/if}>
    <{/if}>
    <a class="btn btn-success btn-xs" href="#threadtop" title="<{$smarty.const._MD_NEWBB_TOP}>"><span class="fa fa-arrow-circle-up"></span></a>
    </div>
</div>
