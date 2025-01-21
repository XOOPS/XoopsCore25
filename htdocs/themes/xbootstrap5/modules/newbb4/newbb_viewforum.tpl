<div class="newbb">
    <ol class="breadcrumb">
        <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$forum_index_title}></a></li>

        <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_FORUMHOME}></a></li>

        <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.id}>"><{$category.title}></a></li>

        <!-- If is subforum-->
        <{if isset($parentforum)}>
            <{foreach item=forum from=$parentforum|default:null}>
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>"><{$forum.forum_name}></a></li>
        <{/foreach}>
        <{/if}>

        <li class="active"><{$forum_name}></li>
    </ol>
    <div class="row">
        <div class="col-xs-12">
            <{if $viewer_level >= 1}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?forum=<{$forum_id}>" title="<{$smarty.const.THEME_FORUM_NEWTOPIC}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_NEWTOPIC}></a>
            <{else}>
                <{if isset($xoops_isuser)}>
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?forum=<{$forum_id}>" title="<{$smarty.const.THEME_FORUM_NEWTOPIC}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_NEWTOPIC}></a>
                <{else}>
                    <a href="<{$xoops_url}>/user.php" title="<{$smarty.const.THEME_FORUM_REGISTER}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_REGISTER}></a>
                <{/if}>
            <{/if}>

            <{if isset($forum_topictype)}><{$forum_topictype}><{/if}>

            <{if isset($forum_topicstatus)}>
                <span class="btn btn-info"><{$forum_topicstatus}></span>
            <{else}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&status=digest" title="<{$smarty.const._MD_DIGEST}>" class="btn btn-info">
                    <{$smarty.const._MD_DIGEST}>
                </a>
            <{/if}>

            <a data-bs-toggle="collapse" href="#forum-search" title="<{$smarty.const.THEME_FORUM_SEARCH}>" class="btn btn-info">
                <span class="fa fa-search"></span>
            </a>

            <{if isset($subforum)}>
                <{include file="db:newbb_viewforum_subforum.tpl"}>
            <{/if}>
        </div>
    </div>
    <!-- Forum Search -->
    <div class="row collapse forum-search" id="forum-search">
        <div class="col-sm-9 col-md-9">
            <form class="input-group" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get" role="search">
                <input name="term" id="term" type="text" class="form-control" placeholder="<{$smarty.const.THEME_NEWBB_SEARCH_FORUM}>">
                <input type="hidden" name="forum" id="forum" value="<{$forum_id}>">
                <input type="hidden" name="sortby" id="sortby" value="p.post_time desc">
                <input type="hidden" name="since" id="since" value="<{$forum_since}>">
                <input type="hidden" name="action" id="action" value="yes">
                <input type="hidden" name="searchin" id="searchin" value="both">
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-primary" value="<{$smarty.const.THEME_FORUM_SEARCH}>">
                </span>
            </form>
        </div>
        <div class="col-sm-3 col-md-3">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" class="btn btn-primary w-100" title="<{$smarty.const._MD_ADVSEARCH}>"><{$smarty.const.THEME_FORUM_ADVSEARCH}></a>
        </div>
    </div>

    <!-- Newbb topics list -->
    <div class="newbb-topicslist mb10">
        <div class="newbb-topic-options row mb10 mt10">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-8 col-xs-12">
                        <{if $mode >= 1}>
                        <form name="form_topics_admin" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.topic.php" method="POST" onsubmit="if(window.document.form_topics_admin.op.value < 1){return false;}">
                            <{/if}>

                            <{if $viewer_level >= 1}>
                                <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?op=add&forum=<{$forum_id}>" title="<{$smarty.const.THEME_ADD_POLL}>"><{$smarty.const.THEME_ADD_POLL}></a>
                                <{if $mode >= 1}>
                                    <{$smarty.const._ALL}>:
                                    <input type="checkbox" name="topic_check1" id="topic_check1" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check1');"/>
                                    <select name="op">
                                        <option value="0"><{$smarty.const._SELECT}></option>
                                        <option value="delete"><{$smarty.const._DELETE}></option>
                                        <{if $status == "pending"}>
                                            <option value="approve"><{$smarty.const._MD_APPROVE}></option>
                                            <option value="move"><{$smarty.const._MD_MOVE}></option>
                                        <{elseif $status == "deleted"}>
                                            <option value="restore"><{$smarty.const._MD_RESTORE}></option>
                                        <{else}>
                                            <option value="move"><{$smarty.const._MD_MOVE}></option>
                                        <{/if}>
                                    </select>
                                    <input type="hidden" name="forum_id" value="<{$forum_id}>">
                                    <input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>">
                                    |
                                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>" title="<{$smarty.const._MD_TYPE_VIEW}>"><{$smarty.const._MD_TYPE_VIEW}></a>
                                <{else}>
                                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&status=active#admin" title="<{$smarty.const._MD_TYPE_ADMIN}>"><{$smarty.const._MD_TYPE_ADMIN}></a>
                                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&status=pending#admin" title="<{$smarty.const._MD_TYPE_PENDING}>"><{$smarty.const._MD_TYPE_PENDING}></a>
                                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>&status=deleted#admin" title="<{$smarty.const._MD_TYPE_DELETED}>"><{$smarty.const._MD_TYPE_DELETED}></a>
                                    <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/moderate.php?forum=<{$forum_id}>" title="<{$smarty.const._MD_TYPE_SUSPEND}>"><{$smarty.const._MD_TYPE_SUSPEND}></a>
                                <{/if}>

                            <{else}>
                                <a class="btn btn-xs btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?op=add&forum=<{$forum_id}>" title="<{$smarty.const.THEME_ADD_POLL}>"><{$smarty.const.THEME_ADD_POLL}></a>
                            <{/if}>
                    </div>
                    <div class="col-md-4 col-xs-12 pull-right">
                        <{include file="db:newbb_viewforum_menu.tpl"}>
                    </div>
                </div>
                <{if $mode >= 1}>
                    <{$smarty.const._ALL}>:
                    <input type="checkbox" name="topic_check" id="topic_check" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check');">
                <{else}>
                    &nbsp;
                <{/if}>
            </div>
            <div class="generic-pagination col-sm-12 text-end">
                <{$forum_pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
            </div>

        </div><!-- .newbb-topic-options -->

        <div class="newbb-topiclist-loop">
            <div class="newbb-topiclist-header clearfix">
                <div class="col-xs-6 col-sm-6 col-md-3"><a href="<{$h_topic_link}>" title="<{$smarty.const._MD_TOPICS}>"><{$smarty.const._MD_TOPICS}></a></div>
                <div class="col-md-2 visible-lg visible-md"><a href="<{$h_poster_link}>" title="<{$smarty.const._MD_TOPICPOSTER}>"><{$smarty.const._MD_TOPICPOSTER}></a></div>
                <div class="col-md-2 visible-lg visible-md"><a href="<{$h_publish_link}>" title="<{$smarty.const._MD_TOPICTIME}>"><{$smarty.const._MD_TOPICTIME}></a></div>
                <div class="col-md-1 visible-lg visible-md"><a href="<{$h_reply_link}>" title="<{$smarty.const._MD_REPLIES}>"><{$smarty.const._MD_REPLIES}></a></div>
                <div class="col-md-1 visible-lg visible-md"><a href="<{$h_views_link}>" title="<{$smarty.const._MD_VIEWS}>"><{$smarty.const._MD_VIEWS}></a></div>

                <{if isset($rating_enable)}>
                    <div class="col-md-1 visible-lg"><a href="<{$h_rating_link}>" title="<{$smarty.const._MD_RATINGS}>"><{$smarty.const._MD_RATINGS}></a></div>
                <{/if}>

                <div class="<{if isset($rating_enable)}>col-xs-6 col-sm-6 col-md-2<{else}>col-xs-6 col-sm-6 col-md-3<{/if}>"><a href="<{$h_date_link}>" title="<{$smarty.const._MD_LASTPOSTTIME}>"><{$smarty.const._MD_LASTPOSTTIME}></a></div>
            </div><!-- .newbb-topiclist-header -->

            <{if $sticky > 0}>
                <{if isset($rating_enable)}>
                    <{$smarty.const._MD_IMTOPICS}>
                <{else}>
                    <{$smarty.const._MD_IMTOPICS}>
                <{/if}>
            <{/if}>

            <{foreach name=loop item=topic from=$topics}>
            <div class="clearfix newbb-topiclist-itens <{cycle values="even,odd"}>">
                <!--
        <{if $topic.stick AND $smarty.foreach.loop.iteration == $sticky+1}>
            <{if isset($rating_enable)}>
                <{$smarty.const._MD_NOTIMTOPICS}>
            <{else}>
                <{$smarty.const._MD_NOTIMTOPICS}>
            <{/if}>
        <{/if}>
-->
                <div class="col-xs-6 col-sm-6 col-md-3">
        <span>
            <{if $mode >= 1}>
                <input type="checkbox" name="topic_id[]" id="topic_id[<{$topic.topic_id}>]" value="<{$topic.topic_id}>">
            <{else}>
                <{$topic.topic_folder}>
            <{/if}>
            <{$topic.topic_icon}>
        </span>

                    <a class="<{if $topic.topic_read == 1 }>read-topic<{else}>new-topic<{/if}>" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/<{$topic.topic_link}>" title="<{$topic.topic_excerpt}>">
                        <{$topic.topic_title}>
                    </a></div>

                <!-- <{$topic.attachment}> <{$topic.topic_page_jump}> -->

                <div class="col-md-2 visible-lg visible-md"><label class="label label-info"><span class="fa fa-user"></span> <{$topic.topic_poster}></label></div>
                <div class="col-md-2 visible-lg visible-md"><{$topic.topic_time}></div>
                <div class="col-md-1 visible-lg visible-md text-center"><{$topic.topic_replies}></div>
                <div class="col-md-1 visible-lg visible-md text-center"><{$topic.topic_views}></div>
                <{if isset($rating_enable)}>
                    <div class="col-md-1 visible-lg"><{$topic.rating_img}></div><{/if}>
                <div class="<{if isset($rating_enable)}>col-xs-6 col-sm-6 col-md-2<{else}>col-xs-6 col-sm-6 col-md-3<{/if}>"><{$topic.topic_last_posttime}> <{$smarty.const._MD_BY}> <{$topic.topic_last_poster}> <{$topic.topic_page_jump_icon}></div>

            </div><!-- .newbb-topiclist-itens -->
            <{/foreach}>

        </div><!-- .newbb-topiclist-loop -->

        <{if $mode >= 1}>
            </form>
        <{/if}>

        <{if isset($rating_enable)}>
            <!-- do do something -->
        <{else}>
            <!-- do do something -->
        <{/if}>
    </div><!-- .newbb-topicslist -->

    <div class="text-end generic-pagination"><{$forum_pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}></div>

    <div class="row mb10">
        <div class="col-md-12">
            <{strip}>
                <form class="xoopsform" method="get" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php">
                    <ul class="list-inline">
                        <li><strong><{$smarty.const._MD_SORTEDBY}>:</strong></li>
                        <li><{$forum_selection_sort}></li>
                        <li><{$forum_selection_order}></li>
                        <li><{$forum_selection_since}></li>
                        <input type="hidden" name="forum" id="forum" value="<{$forum_id}>">
                        <input type="hidden" name="status" value="<{$status}>">
                        <li><input type="submit" value="<{$smarty.const._SUBMIT}>" class="btn btn-primary"></li>
                    </ul>
                </form>
            <{/strip}>
        </div>

        <div class="col-sm-2 col-md-2"><a data-bs-toggle="collapse" href="#forum-info" class="btn btn-info" title=""><span class="fa fa-info-sign"></span></a></div>
        <div class="col-sm-10 col-md-10 text-end xoopsform"><{$forum_jumpbox}></div>
        <!--<{$forum_addpoll}>-->
    </div>

    <div class="row collapse" id="forum-info">
        <div class="col-sm-6 col-md-6">
            <{foreach item=perm from=$permission_table|default:null}>
            <{$perm}>
            <{/foreach}>
        </div>

        <div class="col-sm-6 col-md-6">
            <ul class="list-unstyled">
                <li><{$img_newposts}> = <{$smarty.const._MD_NEWPOSTS}> (<{$img_hotnewposts}> = <{$smarty.const._MD_MORETHAN}>)</li>
                <li><{$img_folder}> = <{$smarty.const._MD_NONEWPOSTS}> (<{$img_hotfolder}> = <{$smarty.const._MD_MORETHAN2}>)</li>
                <li><{$img_locked}> = <{$smarty.const._MD_TOPICLOCKED}></li>
                <li><{$img_sticky}> = <{$smarty.const._MD_TOPICSTICKY}></li>
                <li><{$img_digest}> = <{$smarty.const._MD_TOPICDIGEST}></li>
                <li><{$img_poll}> = <{$smarty.const._MD_TOPICHASPOLL}></li>
            </ul>
        </div>
    </div>

    <{if isset($online)}>
        <{include file="db:newbb_online.tpl"}>
    <{/if}>

    <a title="NewBB" href="https://www.simple-xoops.de" class="btn btn-xs btn-success">NewBB Version <{$version}></a>
    <{if isset($rss_button)}>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/rss.php?f=<{$forum_id}>" target="_blank" title="RSS FEED">
            <{$rss_button}>
        </a>
    <{/if}>
    <{include file='db:newbb_notification_select.tpl'}>
</div><!-- .newbb -->
