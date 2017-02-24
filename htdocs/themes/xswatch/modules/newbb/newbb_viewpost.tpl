<div class="newbb">
    <ol class="breadcrumb">
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_FORUMHOME}></a></li>

        <{if $parent_forum}>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$parent_forum}>"><{$parent_name}></a></li>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>"><{$forum_name}></a></li>
        <{elseif $forum_name}>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>"><{$forum_name}></a></li>
        <{/if}>
        <li><{$lang_title}></li>
    </ol>
</div>
<div class="clear"></div>
<{if $viewer_level gt 1}>
    <div class="right" id="admin">
        <{if $mode gt 1}>
        <form id="form_posts_admin" name="form_posts_admin" action="action.post.php" method="POST" onsubmit="if(window.document.form_posts_admin.op.value &lt; 1){return false;}">
            <{$smarty.const._ALL}>: <input type="checkbox" name="post_check" id="post_check" value="1" onclick="xoopsCheckAll('form_posts_admin', 'post_check');"/>
            <select name="op" class="btn btn-default">
                <option value="0"><{$smarty.const._SELECT}></option>
                <option value="delete"><{$smarty.const._DELETE}></option>
                <{if $status eq "pending"}>
                    <option value="approve"><{$smarty.const._MD_APPROVE}></option>
                <{elseif $status eq "deleted"}>
                    <option value="restore"><{$smarty.const._MD_RESTORE}></option>
                <{/if}>
            </select>
            <input type="hidden" name="uid" value="<{$uid}>"/>
            <input class="btn btn-default" type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"/>
            <a class="btn btn-default" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>" target="_self" title="<{$smarty.const._MD_TYPE_VIEW}>"><{$smarty.const._MD_TYPE_VIEW}></a>
        </form>
        <{else}>
            <a class="btn btn-default" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>&amp;status=active#admin" target="_self" title="<{$smarty.const._MD_TYPE_ADMIN}>"><{$smarty.const._MD_TYPE_ADMIN}></a>
            <a class="btn btn-default" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>&amp;status=pending#admin" target="_self" title="<{$smarty.const._MD_TYPE_PENDING}>"><{$smarty.const._MD_TYPE_PENDING}></a>
            <a class="btn btn-default" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>&amp;status=deleted#admin" target="_self" title="<{$smarty.const._MD_TYPE_DELETED}>"><{$smarty.const._MD_TYPE_DELETED}></a>
        <{/if}>
    </div>
    <div class="clear"></div>
<{/if}>

<div style="padding: 5px;">
    <a id="threadtop"></a><a href="#threadbottom"><span class="glyphicon glyphicon-arrow-down"></span> <{$smarty.const._MD_BOTTOM}></a>
</div>

<br>
<div>
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="topicoption" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <{$smarty.const._MD_TOPICOPTION}>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="topicoption">
            <li><a href="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_NEWPOSTS}></a></li>
            <li><a href="<{$all_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALL}></a></li>
        </ul>
    </div>
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="viewmode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <{$smarty.const._MD_VIEWMODE}>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="viewmode">
            <{foreach item=act from=$viewmode_options}>
            <li><a href="<{$act.link}>"><{$act.title}></li>
            <{/foreach}>
        </ul>
    </div>
    <div class="pagenav">
        <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}> <!-- irmtfan to solve nested forms and id="xo-pagenav" issue -->
    </div>
</div>
<div class="clear"></div>
<br>
<br>

<{foreachq item=post from=$posts}>
<{includeq file="db:newbb_thread.tpl" topic_post=$post}>
<!-- irmtfan hardcode removed style="padding: 5px;float: right; text-align:right;" -->
<div class="pagenav">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?topic_id=<{$post.topic_id}>"><strong><{$smarty.const._MD_VIEWTOPIC}></strong></a>
    <{if !$forum_name }>
        |
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$post.forum_id}>"><strong><{$smarty.const._MD_VIEWFORUM}></strong></a>
    <{/if}>
</div>
<div class="clear"></div>
<br>
<br>
<{/foreach}>

<br>
<div>
    <div class="icon_left">
        <a id="threadbottom"></a><a href="#threadtop"><span class="glyphicon glyphicon-arrow-up"></span> <{$smarty.const._MD_TOP}></a>
    </div>
    <div class="icon_right">
        <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}> <!-- irmtfan to solve nested forms and id="xo-pagenav" issue -->
    </div>
</div>
<div class="clear"></div>

<br>
<br>
<div>
    <{if $mode lte 1}>
    <div class="col-md-4" id="forum-search">
            <form class="input-group" id="search-topic" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get" role="search">
                <input name="term" id="term" type="text" class="form-control" placeholder="<{$smarty.const.THEME_FORUM_SEARCH}>">
                <input type="hidden" name="forum" id="forum" value="<{$forum_id}>">
                <input type="hidden" name="sortby" id="sortby" value="p.post_time desc">
                <input type="hidden" name="action" id="action" value="yes">
                <input type="hidden" name="searchin" id="searchin" value="both">
                <input type="hidden" name="show_search" id="show_search" value="post_text">
                    <span class="input-group-btn">
                        <input type="submit" class="btn btn-primary" value="<{$smarty.const._MD_SEARCH}>">
                    </span>
            </form>
    </div>
    <{/if}>
    <div class="xoopsform col-sm-4 col-md-4">
        <{$forum_jumpbox}>
    </div>
</div>
<div class="clear"></div>
<br>
<{if $online}>
    <br>
    <{includeq file="db:newbb_online.tpl"}>
<{/if}>
<{includeq file='db:newbb_notification_select.tpl'}>
<!-- end module contents -->
