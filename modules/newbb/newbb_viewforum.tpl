<div class="newbb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$forum_index_title}></a></li>

        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_NEWBB_FORUMHOME}></a></li>

        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.id}>"><{$category.title}></a></li>

    <!-- If is subforum-->
    <{if $parentforum}>
        <{foreach item=forum from=$parentforum}>
             <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>"><{$forum.forum_name}></a></li>
        <{/foreach}>
    <{/if}>

        <li class="breadcrumb-item active"><{$forum_name}></li>
    </ol>
    <div>
        <div class="col-xs-12">
        <{if $viewer_level gt 1}>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?forum=<{$forum_id}>" title="<{$smarty.const.THEME_FORUM_NEWTOPIC}>" class="btn btn-success"><{$smarty.const.THEME_FORUM_NEWTOPIC}></a>
        <{else}>
            <{if $xoops_isuser}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?forum=<{$forum_id}>" title="<{$smarty.const.THEME_FORUM_NEWTOPIC}>" class="btn btn-success"><{$smarty.const.THEME_FORUM_NEWTOPIC}></a>
            <{else}>
                <a href="<{$xoops_url}>/user.php" title="<{$smarty.const.THEME_FORUM_REGISTER}>" class="btn btn-success"><{$smarty.const.THEME_FORUM_REGISTER}></a>
            <{/if}>
        <{/if}>

        <{if $forum_topictype}><{$forum_topictype}><{/if}>

        <{if $forum_topicstatus}>
            <span class="btn btn-info"><{$forum_topicstatus}></span>
        <{else}>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&amp;status=digest" title="<{$smarty.const._MD_NEWBB_DIGEST}>" class="btn btn-info">
                <{$smarty.const._MD_NEWBB_DIGEST}>
            </a>
        <{/if}>

        <a data-toggle="collapse" href="#forum-search" title="<{$smarty.const.THEME_FORUM_SEARCH}>" class="btn btn-info">
            <span class="fa fa-search"></span>
        </a>

        <{if $subforum}>
            <{include file="db:newbb_viewforum_subforum.tpl"}>
        <{/if}>
        </div>
    </div>
    <!-- Forum Search -->
    <div class="row collapse forum-search" id="forum-search">
        <div class="col-sm-9 col-md-9">
            <form class="input-group" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get" role="search">
                <input name="term" id="term" type="text" class="form-control" placeholder="<{$smarty.const.THEME_NEWBB_SEARCH_FORUM}>">
                <input type="hidden" name="forum" value="<{$forum_id}>">
                <input type="hidden" name="sortby" value="p.post_time desc">
                <input type="hidden" name="since" value="<{$forum_since}>">
                <input type="hidden" name="action" value="yes">
                <input type="hidden" name="searchin" value="both">
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-primary" value="<{$smarty.const.THEME_FORUM_SEARCH}>">
                </span>
            </form>
        </div>
        <div class="col-sm-3 col-md-3">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" class="btn btn-primary btn-block" title="<{$smarty.const._MD_NEWBB_ADVSEARCH}>"><{$smarty.const.THEME_FORUM_ADVSEARCH}></a>
        </div>
    </div>
<br>
<!-- Newbb topics list -->
<div class="newbb-topicslist mb10">
    <div class="newbb-topic-options row mb10 mt10">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-8 col-xs-12">
                <{if $mode gt 1}>
                    <form class="form-inline" name="form_topics_admin" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.topic.php" method="POST" onsubmit="if(window.document.form_topics_admin.op.value &lt; 1){return false;}">
                <{/if}>

                <{if $viewer_level gt 1}>
                <{if $mode gt 1}>
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <div class="form-check mb-2">
                                    <label for="topic_check1">
                                        <{$smarty.const._ALL}>:
                                    </label>
                                    <input type="checkbox" name="topic_check1" id="post_check" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check1');">
                                </div>
                            </div>
                            <div class="col-auto">
                                <select name="op" class="custom-select mb-2">                        <option value="0"><{$smarty.const._SELECT}></option>
                                    <option value="delete"><{$smarty.const._DELETE}></option>
                                    <{if $status eq "pending"}>
                                    <option value="approve"><{$smarty.const._MD_NEWBB_APPROVE}></option>
                                    <option value="move"><{$smarty.const._MD_NEWBB_MOVE}></option>
                                    <{elseif $status eq "deleted"}>
                                    <option value="restore"><{$smarty.const._MD_NEWBB_RESTORE}></option>
                                    <{else}>
                                    <option value="move"><{$smarty.const._MD_NEWBB_MOVE}></option>
                                    <{/if}>
                                </select>
                            </div>
                            <input type="hidden" name="forum_id" value="<{$forum_id}>">
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary mb-2">Submit</button>
                            </div>
                            <div class="col-auto">
                                <a class="btn btn-primary mb-2" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>" target="_self"
                                   title="<{$smarty.const._MD_NEWBB_TYPE_VIEW}>"><{$smarty.const._MD_NEWBB_TYPE_VIEW}></a>
                            </div>
                        </div>
                <{else}>
                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?op=add&forum=<{$forum_id}>" title="<{$smarty.const.THEME_ADD_POLL}>"><{$smarty.const.THEME_ADD_POLL}></a>
                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&amp;status=active#admin" title="<{$smarty.const._MD_NEWBB_TYPE_ADMIN}>"><{$smarty.const._MD_NEWBB_TYPE_ADMIN}></a>
                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&amp;status=pending#admin" title="<{$smarty.const._MD_NEWBB_TYPE_PENDING}>"><{$smarty.const._MD_NEWBB_TYPE_PENDING}></a>
                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&amp;status=deleted#admin" title="<{$smarty.const._MD_NEWBB_TYPE_DELETED}>"><{$smarty.const._MD_NEWBB_TYPE_DELETED}></a>
                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/moderate.php?forum=<{$forum_id}>" title="<{$smarty.const._MD_NEWBB_TYPE_SUSPEND}>"><span class="fa fa-ban" aria-hidden="true"></span></a>
                <{/if}>

                <{else}>
                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?op=add&forum=<{$forum_id}>" title="<{$smarty.const.THEME_ADD_POLL}>"><{$smarty.const.THEME_ADD_POLL}></a>
                <{/if}>
            </div>
            <div class="col-md-4 col-xs-12 pull-right mb10">
                <{include file="db:newbb_viewforum_menu.tpl"}>
            </div>
        </div>
    </div>
    <div class="generic-pagination col text-right">
        <{$forum_pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
    </div>

    </div><!-- .newbb-topic-options -->
    <{* assign var='desctext' value=`$smarty.const._MD_NEWBB_FORUMDESCRIPTION` *}>
    <{* $desctext|regex_replace:"/:$/":"" *}>
    <div>
        <h3><{$forum_name}></h3>
        <{$forumDescription}>
    </div>
    <table class="table table-hover">
        <tbody>
        <{foreach item=topic from=$topics}>
            <tr>
                <{if $mode gt 1}>
                <td><input type="checkbox" name="topic_id[]" id="topic_id[<{$topic.topic_id}>]" value="<{$topic.topic_id}>"></td>
                <{/if}>
                <td class="d-none d-sm-table-cell"><{$topic.topic_folder}></td>
                <td><a class="<{if $topic.topic_read eq 1 }>read-topic<{else}>new-topic<{/if}>" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/<{$topic.topic_link}>" title="<{$topic.topic_excerpt}>"><{$topic.topic_title}></a></td>
                <td class="d-none d-sm-table-cell"><span class="fa fa-user"></span> <{$topic.topic_poster}></td>
                <td><{$topic.topic_time}></td>
                <td class="d-none d-sm-table-cell"><{$topic.topic_replies}></td>
                <td class="d-none d-sm-table-cell"><{$topic.topic_views}></td>
                <{if $rating_enable}>
                <td class="d-none d-sm-table-cell"><{$topic.rating_img}></td>
                <{/if}>
                <{assign var='golast' value=`$smarty.const._MD_NEWBB_GOTOLASTPOST`}>
                <{assign var='golastimg' value="<span class=\"fa fa-forward\" aria-hidden=\"true\" title=\"`$golast`\"></span>"}>
                <td><{$topic.topic_last_posttime}> <{$smarty.const._MD_NEWBB_BY}> <{$topic.topic_last_poster}> <{$topic.topic_page_jump_icon|regex_replace:'/<img .*>/':$golastimg}></td>
            </tr>
            <{/foreach}>
        </tbody>
        <thead>
        <tr>
        <{if $mode gt 1}>
            <th> </th>
        <{/if}>
        <th class="d-none d-sm-table-cell"> </th>
        <th scope="col"><a href="<{$h_topic_link}>" title="<{$smarty.const._MD_NEWBB_TOPICS}>"><{$smarty.const._MD_NEWBB_TOPICS}> <span class="fa fa-sort" aria-hidden="true"></span></a></th>
        <th class="d-none d-sm-table-cell" scope="col"><a href="<{$h_poster_link}>" title="<{$smarty.const._MD_NEWBB_TOPICPOSTER}>"><{$smarty.const._MD_NEWBB_TOPICPOSTER}> <span class="fa fa-sort" aria-hidden="true"></a></span></th>
        <th scope="col"><a href="<{$h_publish_link}>" title="<{$smarty.const._MD_NEWBB_TOPICTIME}>"><{$smarty.const._MD_NEWBB_TOPICTIME}> <span class="fa fa-sort" aria-hidden="true"></span></a></th>
        <th class="d-none d-sm-table-cell" scope="col"><a href="<{$h_reply_link}>" title="<{$smarty.const._MD_NEWBB_REPLIES}>"><{$smarty.const._MD_NEWBB_REPLIES}> <span class="fa fa-sort" aria-hidden="true"></span></a></th>
        <th class="d-none d-sm-table-cell" scope="col"><a href="<{$h_views_link}>" title="<{$smarty.const._MD_NEWBB_VIEWS}>"><{$smarty.const._MD_NEWBB_VIEWS}> <span class="fa fa-sort" aria-hidden="true"></span></a></th>
        <{if $rating_enable}>
            <th class="d-none d-sm-table-cell" scope="col"><a href="<{$h_rating_link}>" title="<{$smarty.const._MD_NEWBB_RATINGS}>"><{$smarty.const._MD_NEWBB_RATINGS}> <span class="fa fa-sort" aria-hidden="true"></span></a></th>
        <{/if}>
        <th scope="col"><a href="<{$h_date_link}>" title="<{$smarty.const._MD_NEWBB_LASTPOSTTIME}>"><{$smarty.const._MD_NEWBB_LASTPOSTTIME}> <span class="fa fa-sort" aria-hidden="true"></span></a></th>
        </tr>
        </thead>
    </table>
    <{if empty($topics)}>
    <div class="alert alert-warning" role="alert"><{$smarty.const._MD_NEWBB_NOTOPIC}></div>
    <{/if}>
    <{if $mode gt 1}>
        </form>
    <{/if}>

</div>

<div class="text-right generic-pagination"><{$forum_pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}></div>

<div class="row mb10">
    <div class="col-md-12">
        <{strip}>
            <form class="xoopsform" method="get" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php">
            <ul class="list-inline">
            <li><strong><{$smarty.const._MD_NEWBB_SORTEDBY}>:</strong></li>
            <li><{$forum_selection_sort|replace:'<select name':'<select class="form-control" name'}></li>
            <li><{$forum_selection_order|replace:'<select name':'<select class="form-control" name'}></li>
            <li><{$forum_selection_since}></li>
            <input type="hidden" name="forum" value="<{$forum_id}>">
            <input type="hidden" name="status" value="<{$status}>">
            <li><input type="submit" value="<{$smarty.const._SUBMIT}>" class="btn btn-primary"></li>
            </ul>
            </form>
        <{/strip}>
    </div>

    <div class="col-sm-2 col-md-2"><a data-toggle="collapse" href="#forum-info" class="btn btn-info" title="<{$smarty.const.THEME_PERMISSIONS_LEGEND}>"><span class="fa fa-info"></span></a></div>
    <div class="col-sm-10 col-md-10 text-right xoopsform"><{$forum_jumpbox|replace:' class="select"':' class="btn btn-light"'|replace:"'button'":'"btn btn-sm btn-light"'}></div>
<!--<{$forum_addpoll}>-->
</div>

<div class="row collapse" id="forum-info">
    <div class="col-sm-6 col-md-6">
        <{foreach item=perm from=$permission_table}>
            <{$perm}>
        <{/foreach}>
    </div>

    <div class="col-sm-6 col-md-6">
        <ul class="list-unstyled">
            <li><{$img_newposts}> = <{$smarty.const._MD_NEWBB_NEWPOSTS}> (<{$img_hotnewposts}> = <{$smarty.const._MD_NEWBB_MORETHAN}>)</li>
            <li><{$img_folder}> = <{$smarty.const._MD_NEWBB_NONEWPOSTS}> (<{$img_hotfolder}> = <{$smarty.const._MD_NEWBB_MORETHAN2}>)</li>
            <li><{$img_locked}> = <{$smarty.const._MD_NEWBB_TOPICLOCKED}></li>
            <li><{$img_sticky}> = <{$smarty.const._MD_NEWBB_TOPICSTICKY}></li>
            <li><{$img_digest}> = <{$smarty.const._MD_NEWBB_TOPICDIGEST}></li>
            <li><{$img_poll}> = <{$smarty.const._MD_NEWBB_TOPICHASPOLL}></li>
        </ul>
    </div>
</div>

<{if $online}>
    <{include file="db:newbb_online.tpl"}>
<{/if}>

<a title="NewBB" href="https://xoops.org" class="btn btn-xs btn-success">NewBB Version  <{$version/100}></a>
<{if $rss_button}>
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/rss.php?f=<{$forum_id}>" target="_blank" title="RSS FEED">
        <{$rss_button}>
    </a>
<{/if}>
<{include file='db:system_notification_select.tpl'}>
</div><!-- .newbb -->
