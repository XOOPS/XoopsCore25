<div class="newbb">
    <ul class="breadcrumb">
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_FORUMHOME}></a></li>
        <{if $parent_forum}>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$parent_forum}>"><{$parent_name}></a></li>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>"><{$forum_name}></a></li>
        <{elseif $forum_name}>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>"><{$forum_name}></a></li>
        <{/if}>
        <{if $current}>
        <li><a href="<{$current.link}>"><{$current.title}></a></li>
        <{/if}>
    </ul>
</div>
<div class="clear"></div>

<{if $mode gt 1}>
<form name="form_topics_admin" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.topic.php" method="POST" onsubmit="if(window.document.form_topics_admin.op.value &lt; 1){return false;}">
    <{/if}>

    <{if $viewer_level gt 1}>
        <!-- irmtfan hardcode removed style="padding: 5px;float: right; text-align:right;" -->
        <div class="pagenav" id="admin">
            <{if $mode gt 1}>
                <{$smarty.const._ALL}>:
                <input type="checkbox" name="topic_check1" id="topic_check1" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check1');"/>
                <select name="op">
                    <option value="0"><{$smarty.const._SELECT}></option>
                    <option value="delete"><{$smarty.const._DELETE}></option>
                    <{if $status eq "pending"}>
                        <option value="approve"><{$smarty.const._MD_APPROVE}></option>
                        <option value="move"><{$smarty.const._MD_MOVE}></option>
                    <{elseif $status eq "deleted"}>
                        <option value="restore"><{$smarty.const._MD_RESTORE}></option>
                    <{else}>
                        <option value="move"><{$smarty.const._MD_MOVE}></option>
                    <{/if}>
                </select>
                <input type="hidden" name="forum_id" value="<{$forum_id}>"/>
                <input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"/>
                |
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php" target="_self" title="<{$smarty.const._MD_TYPE_VIEW}>"><{$smarty.const._MD_TYPE_VIEW}></a>
                <!-- irmtfan remove < { elseif $mode eq 1} > to show all admin links in admin mode in the initial page loading -->
            <{else}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=active#admin" target="_self" title="<{$smarty.const._MD_TYPE_ADMIN}>"><{$smarty.const._MD_TYPE_ADMIN}></a>
                |
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=pending#admin" target="_self" title="<{$smarty.const._MD_TYPE_PENDING}>"><{$smarty.const._MD_TYPE_PENDING}></a>
                |
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=deleted#admin" target="_self" title="<{$smarty.const._MD_TYPE_DELETED}>"><{$smarty.const._MD_TYPE_DELETED}></a>
                |
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/moderate.php" target="_self" title="<{$smarty.const._MD_TYPE_SUSPEND}>"><{$smarty.const._MD_TYPE_SUSPEND}></a>
                <!-- irmtfan remove < { else } > no need for mode=1
< { else } >
<!--<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?mode=1#admin" target="_self" title="<{$smarty.const._MD_TYPE_VIEW}>"><{$smarty.const._MD_TYPE_VIEW}></a>
-->
            <{/if}>
        </div>
        <br>
    <{else}>
        <br>
    <{/if}>
    <div class="clear"></div>

    <div>
        <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="topicoption" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <{$smarty.const._MD_TOPICOPTION}>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="topicoption">
                <li><a href="<{$post_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALLPOSTS}></a></li>
                <li><a href="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_NEWPOSTS}></a></li>
                <li role="separator" class="divider"></li>
                <{foreach item=filter from=$filters}>
                <li><a href="<{$filter.link}>"><{$filter.title}></li>
                <{/foreach}>
                <li role="separator" class="divider"></li>
                <{foreach item=filter from=$types}>
                <li><a href="<{$filter.link}>"><{$filter.title}></li>
                <{/foreach}>
            </ul>
        </div>

        <div class="pagenav">
            <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
        </div>
    </div>
    <div class="clear"></div>
    <br>
    <br>
    <div class="table-responsive">
    <table class="table table-hover">
        <!-- irmtfan hardcode removed align="left" -->
        <tr class="head" class="align_left">
            <td width="5%" colspan="2">
                <{if $mode gt 1}>
                    <{$smarty.const._ALL}>:
                    <input type="checkbox" name="topic_check" id="topic_check" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check');"/>
                <{else}>
                    &nbsp;
                <{/if}>
            </td>
            <td>&nbsp;<strong><a href="<{$headers.topic.link}>"><{$headers.topic.title}></a></strong></td>
            <td width="15%" align="center" nowrap="nowrap"><strong><a href="<{$headers.forum.link}>"><{$headers.forum.title}></a></strong></td>
            <td width="5%" align="center" nowrap="nowrap"><strong><a href="<{$headers.replies.link}>"><{$headers.replies.title}></a></strong></td>
            <td width="10%" align="center" nowrap="nowrap"><strong><a href="<{$headers.poster.link}>"><{$headers.poster.title}></a></strong></td>
            <td width="5%" align="center" nowrap="nowrap"><strong><a href="<{$headers.views.link}>"><{$headers.views.title}></a></strong></td>
            <td width="15%" align="center" nowrap="nowrap"><strong><a href="<{$headers.lastpost.link}>"><{$headers.lastpost.title}></a></strong></td>
        </tr>

        <!-- start forum topic -->
        <{foreachq name=loop item=topic from=$topics}>
        <tr class="<{cycle values="even,odd"}>">
            <!-- irmtfan add topic-read/topic-new smarty variable  -->
            <td width="4%" align="center" class="<{if $topic.topic_read eq 1 }>topic-read<{else}>topic-new<{/if}>">
                <{if $mode gt 1}>
                    <input type="checkbox" name="topic_id[]" id="topic_id[<{$topic.topic_id}>]" value="<{$topic.topic_id}>"/>
                <{else}>
                    <!-- irmtfan add lock -->
                    <{$topic.topic_folder}><{$topic.lock}>
                <{/if}>
            </td>
            <!-- irmtfan add sticky, digest, poll -->
            <td width="4%" align="center"><{$topic.topic_icon}><{$topic.sticky}><br><{$topic.digest}><{$topic.poll}></td>
            <!-- irmtfan remove topic_link hardcode and add topic_excerpt -->
            <td>&nbsp;<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/<{$topic.topic_link}>" title="<{$topic.topic_excerpt}>">
                    <!-- irmtfan remove
        <{if $topic.allow_prefix AND $topic.topic_subject}>
        <{$topic.topic_subject}>
        <{/if}> -->
                    <{$topic.topic_title}></a><{$topic.attachment}> <{$topic.topic_page_jump}>
                <!-- irmtfan add topic publish time and rating -->
                <br>
        <span>
            <{$headers.publish.title}>: <{$topic.topic_time}>
        </span>
                <{if $rating_enable && $topic.votes}>
                    |&nbsp;
                    <span>
                <{$headers.votes.title}>: <{$topic.votes}>&nbsp;<{$topic.rating_img}>
            </span>
                <{/if}>
            </td>
            <!-- irmtfan hardcode removed align="left" -->
            <td class="align_left" valign="middle"><{$topic.topic_forum_link}></td>
            <td align="center" valign="middle"><{$topic.topic_replies}></td>
            <td align="center" valign="middle"><{$topic.topic_poster}></td>
            <td align="center" valign="middle"><{$topic.topic_views}></td>
            <!-- irmtfan hardcode removed align="right" -->
            <td class="align_right" valign="middle"><{$topic.topic_last_posttime}><br>
                <{$smarty.const._MD_BY}> <{$topic.topic_last_poster}>&nbsp;&nbsp;<{$topic.topic_page_jump_icon}>
            </td>
        </tr>
        <{/foreach}>
        <!-- end forum topic -->

        <{if $mode gt 1}>
</form>
<{/if}>

</table>
</div>
<!-- end forum main table -->

<div class="text-right generic-pagination"><{$forum_pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}></div>

<div class="row mb10">
    <div class="col-md-12">
        <{strip}>
        <form class="xoopsform" method="get" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php">
            <ul class="list-inline">
                <li><strong><{$smarty.const._MD_SORTEDBY}>:</strong></li>
                <li><{$selection.sort}></li>
                <li><{$selection.order}></li>
                <li><{$selection.since}></li>
                <li><input type="submit" value="<{$smarty.const._SUBMIT}>" class="btn btn-primary"></li>
            </ul>
            <{foreach item=hidval key=hidvar from=$selection.vars}>
            <{if $hidval && $hidvar neq "sort" && $hidvar neq "order" && $hidvar neq "since"}>
            <input type="hidden" name="<{$hidvar}>" value="<{$hidval}>"/>
            <{/if}>
            <{/foreach}>
        </form>
        <{/strip}>
    </div>
    <div class="col-sm-5 col-md-5">
        <form class="input-group" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="post" name="search" id="search"
              role="search">
            <input name="term" id="term" type="text" class="form-control" placeholder="<{$smarty.const.THEME_NEWBB_SEARCH_FORUM}>">
            <input type="hidden" name="forum" id="forum" value="all">
            <input type="hidden" name="sortby" id="sortby" value="p.post_time desc">
            <input type="hidden" name="searchin" id="searchin" value="both">

            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit" id="submit"><{$smarty.const._MD_SEARCH}></button>
            </span>
        </form>
    </div>
    <div class="col-sm-3 col-md-3">
        <a class="btn btn-primary btn-block" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const._MD_ADVSEARCH}></a>
    </div>
    <div class="col-sm-4 col-md-4 text-right xoopsform"><{$forum_jumpbox}></div>
    <!--<{$forum_addpoll}>-->
</div>

<div>
    <div class="left floatleft">
        <{$img_newposts}> = <{$smarty.const._MD_NEWPOSTS}> (<{$img_hotnewposts}> = <{$smarty.const._MD_MORETHAN}>) <br>
        <{$img_folder}> = <{$smarty.const._MD_NONEWPOSTS}> (<{$img_hotfolder}> = <{$smarty.const._MD_MORETHAN2}>) <br>
        <{$img_locked}> = <{$smarty.const._MD_TOPICLOCKED}> <br>
        <{$img_sticky}> = <{$smarty.const._MD_TOPICSTICKY}> <br>
        <{$img_digest}> = <{$smarty.const._MD_TOPICDIGEST}> <br>
        <{$img_poll}> = <{$smarty.const._MD_TOPICHASPOLL}>
    </div>
    <!-- irmtfan hardcode removed style="float: right; text-align: right;" -->
</div>
<div class="clear"></div>
<br>

<{if $online}><{includeq file="db:newbb_online.tpl"}><{/if}>
<{includeq file='db:newbb_notification_select.tpl'}>
<!-- end module contents -->
