<div class="newbb">

    <div class="jumbotron newbb-header">
        <h3>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php" title="<{$index_title}>">
                <{$index_title}>
            </a>
        </h3>

        <p><{$index_desc}></p>
        <div class="row">

            <div class="newbb-header-icons hidden-xs">
                <{if $viewer_level gt 1}>
                    <div class="col-md-6 mb10">
                        <strong><{$smarty.const._MD_TOPIC}>:</strong>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=active#admin" title="<{$smarty.const._MD_TYPE_ADMIN}>" class="btn btn-xs btn-primary">
                            <{$smarty.const._MD_TYPE_ADMIN}>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=pending#admin" title="<{$smarty.const._MD_TYPE_PENDING}>" class="btn btn-xs btn-primary">
                            <{if $wait_new_topic}><span class="badge"><{$wait_new_topic}></span><{/if}> <{$smarty.const._MD_TYPE_PENDING}>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=deleted#admin" title="<{$smarty.const._MD_TYPE_DELETED}>" class="btn btn-xs btn-danger">
                            <{if $delete_topic}><span class="badge"><{$delete_topic}></span><{/if}> <{$smarty.const._MD_TYPE_DELETED}>
                        </a>
                    </div>
                <{/if}>

                <div class="<{if $viewer_level gt 1}>col-md-6<{else}>col-md-12<{/if}> text-right"><{includeq file="db:newbb_index_menu.tpl"}></div>

                <{if $viewer_level gt 1}>
                    <div class="col-md-12">
                        <strong><{$smarty.const._MD_POST2}>:</strong>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=active#admin" title="<{$smarty.const._MD_TYPE_ADMIN}>" class="btn btn-xs btn-primary">
                            <{$smarty.const._MD_TYPE_ADMIN}>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=pending#admin" title="<{$smarty.const._MD_TYPE_PENDING}>" class="btn btn-xs btn-primary">
                            <{if $wait_new_post}>(<span style="color:red;"><{$wait_new_post}></span>)<{/if}> <{$smarty.const._MD_TYPE_PENDING}>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=deleted#admin" title="<{$smarty.const._MD_TYPE_DELETED}>" class="btn btn-xs btn-primary">
                            <{if $delete_post}>(<span style="color:red;"><{$delete_post}></span>)<{/if}> <{$smarty.const._MD_TYPE_DELETED}>
                        </a>

                        <{if $report_post}>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/admin_report.php" title="<{$report_post}>" class="btn btn-xs btn-primary">
                                <{$report_post}>
                            </a>
                        <{/if}>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/moderate.php" title="<{$smarty.const._MD_TYPE_SUSPEND}>"
                           class="btn btn-xs btn-primary">
                            <{$smarty.const._MD_TYPE_SUSPEND}>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/index.php" title="<{$smarty.const._MD_ADMINCP}>"
                           class="btn btn-xs btn-primary">
                            <{$smarty.const._MD_ADMINCP}>
                        </a>
                    </div>
                <{/if}>
            </div><!-- .newbb-header-icons -->
        </div><!-- .newbb-header -->
    </div>

        <div class="panel-group newbb-category-list" id="accordion">
            <{foreachq item=category from=$categories}><!-- Forum categories -->
            <div class="panel panel-default mb10">
                <div class="panel-heading">
                    <h4 class="panel-title newbb-forum-title">
                        <{if $category.forums}>
                            <a data-toggle="collapse" data-parent="#accordion" href="#<{$category.cat_element_id}>"
                               title="<{$smarty.const.THEME_NEWBB_TOPIC}>">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                            </a>
                        <{/if}>
                        <{if $category.cat_image}>
                            <img src="<{$category.cat_image}>" alt="<{$category.cat_title}>">
                        <{/if}>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.cat_id}>" title="<{$category.cat_title}>">
                            <{$category.cat_title}>
                        </a>

                        <{if $category.cat_sponsor}>
                            <a href="<{$category.cat_sponsor.link}>" title="<{$smarty.const.THEME_FORUM_SPONSORBY}> <{$category.cat_sponsor.title}>"
                               target="_blank" class="pull-right btn btn-xs btn-success">
                                <{$category.cat_sponsor.title}>
                            </a>
                        <{/if}>

                        <{if $category.cat_description}>
                            <a href="#forum-desc-<{$category.cat_element_id}>" title="<{$smarty.const.THEME_FORUM_DESCRIPTION}>" data-toggle="modal"
                               data-target="#forum-desc-<{$category.cat_element_id}>" class="btn btn-xs btn-info pull-right">
                                <span class="glyphicon glyphicon-info-sign"></span>
                            </a>
                        <{/if}>
                    </h4>
                    <{if $category.cat_description}>
                        <div class="modal fade" id="forum-desc-<{$category.cat_element_id}>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel"><{$category.cat_title}></h4>
                                    </div>
                                    <div class="modal-body">
                                        <p><{$category.cat_description}></p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.cat_id}>"
                                           title="<{$smarty.const.THEME_GOTOTHEFORUM}>" class="btn btn-default">
                                            <{$smarty.const.THEME_GOTOTHEFORUM}>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <{/if}>
                </div><!-- .panel-heading -->

                <div id="<{$category.cat_element_id}>" class="panel-collapse collapse <{if $subforum_display == 'expand'}>in<{/if}>">
                    <div class="panel-body">
                        <{if $category.forums}>
                            <div class="row hidden-xs">
                                <{if $subforum_display == "expand"}>
                                    <div class="col-sm-6 col-md-6"><strong><{$smarty.const._MD_FORUM}></strong></div>
                                <{else}>
                                    <div class="col-sm-6 col-md-6"><strong><{$smarty.const._MD_FORUM}></strong></div>
                                <{/if}>
                                <div class="col-sm-1 col-md-1"><strong><{$smarty.const._MD_TOPICS}></strong></div>
                                <div class="col-sm-1 col-md-1"><strong><{$smarty.const._MD_POSTS}></strong></div>
                                <div class="col-sm-4 col-md-4"><strong><{$smarty.const._MD_LASTPOST}></strong></div>
                            </div>
                        <{/if}>
                        <{foreachq item=forum from=$category.forums}>
                        <div class="row newbb-list-foruns mb10">
                            <div class="col-sm-6 col-md-6">
                                <{if $forum.subforum}>
                                    <div class="<{if $forum.forum_read eq 1 }>forum-read<{else}>forum-new2<{/if}> pull-left">
                                        <{$forum.forum_folder}>
                                    </div>
                                <{else}>
                                    <div class="<{if $forum.forum_read eq 1 }>forum-read<{else}>forum-new2<{/if}> pull-left">
                                        <{$forum.forum_folder}>
                                    </div>
                                <{/if}>

                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>" title="<{$forum.forum_name}>">
                                    <{$forum.forum_name}>
                                </a>

                                <{if $rss_enable}>
                                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/rss.php?f=<{$forum.forum_id}>" target="_blank" title="RSS feed"
                                       class="pull-right btn btn-xs btn-warning">
                                        RSS
                                    </a>
                                <{/if}>
                                <!-- Forum description -->
                                <{if $forum.forum_desc != ""}>
                                    <button class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#forumDesc-<{$forum.forum_id}>"><span
                                                class="glyphicon glyphicon-info-sign"></span></button>
                                    <div class="modal fade" id="forumDesc-<{$forum.forum_id}>" tabindex="-1" role="dialog" aria-labelledby="ForumDescription"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="ForumDescription"><{$smarty.const.THEME_FORUM_DESC}>: <{$category.cat_title}>
                                                        - <{$forum.forum_name}></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <{$forum.forum_desc}>
                                                </div>
                                                <div class="modal-footer">
                                                    <{if $forum.forum_moderators}>
                                                        <div class="pull-left"><span class="label label-info"><{$smarty.const._MD_MODERATOR}>: <{$forum.forum_moderators}></span>
                                                        </div>
                                                    <{/if}>
                                                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.cat_id}>"
                                                       class="btn btn-default"
                                                       title="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.cat_id}>">
                                                        <{$smarty.const.THEME_GOTOTHEFORUM}>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <{/if}>
                                <!-- End forum description-->
                            </div>

                            <div class="col-sm-1 col-md-1 text-center hidden-xs">
                                <{if $stats[$forum.forum_id].topic.day}>
                                    <strong><{$stats[$forum.forum_id].topic.day}></strong>
                                    /
                                <{/if}>
                                <{$forum.forum_topics}>
                            </div>

                            <div class="col-sm-1 col-md-1 text-center hidden-xs">
                                <{if $stats[$forum.forum_id].post.day}>
                                    <strong><{$stats[$forum.forum_id].post.day}></strong>
                                    /
                                <{/if}>
                                <{$forum.forum_posts}>
                            </div>

                            <div class="col-sm-4 col-md-4 hidden-xs">
                                <{if $forum.forum_lastpost_subject}>
                                    <{$forum.forum_lastpost_time}> <{$smarty.const._MD_BY}> <{$forum.forum_lastpost_user}>
                                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?post_id=<{$forum.forum_lastpost_id}>">
                                        <{$forum.forum_lastpost_subject}>
                                        <{$forum.forum_lastpost_icon}>
                                    </a>
                                <{else}>
                                    <{$smarty.const._AM_NEWBB_NOTOPIC}>
                                <{/if}>

                                <{if $forum.subforum}>
                                    <{$smarty.const._MD_SUBFORUMS}><{$img_subforum}>
                                    <{foreachq item=subforum from=$forum.subforum}>
                                    [
                                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$subforum.forum_id}>"><{$subforum.forum_name}></a>
                                    ]
                                <{/foreach}>
                                <{/if}>
                            </div>
                        </div>
                        <{/foreach}>
                    </div><!-- .panel-body -->
                </div><!-- .panel-collapse .collapse -->
            </div><!-- .panel .panel-default -->
            <{/foreach}><!-- End Forum Categories -->
        </div><!-- .panel-group -->

        <div class="row mb10">
            <div class="col-md-12">
                <{$img_forum_new}> = <{$smarty.const._MD_NEWPOSTS}>
                <{$img_forum}> = <{$smarty.const._MD_NONEWPOSTS}>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb10">
                <form class="input-group" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="post" name="search" id="search"
                      role="search">
                    <input name="term" id="term" type="text" class="form-control" placeholder="<{$smarty.const.THEME_NEWBB_SEARCH_FORUM}>">
                    <input type="hidden" name="forum" id="forum" value="all">
                    <input type="hidden" name="sortby" id="sortby" value="p.post_time desc">
                    <input type="hidden" name="searchin" id="searchin" value="both">

                    <span class="input-group-btn">
                <button class="btn btn-primary" type="submit" id="submit"><{$smarty.const.THEME_FORUM_SEARCH}></button>
            </span>
                </form>
            </div>
            <div class="col-md-4">
                <a class="btn btn-primary btn-block" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const.THEME_FORUM_ADVSEARCH}></a>
            </div>
        </div>

        <{if $currenttime}>
            <div class="row">
                <div class="col-lg-12"><h3 class="nompl"><{$online.statistik}> <{$smarty.const._MD_NEWBB_STATS}></h3></div>
                <div class="col-sm-6 col-md-6">
                    <ul class="list-unstyled lw30">
                        <li><{$currenttime}></li>
                        <li><{$lastvisit}></li>

                        <li><{$smarty.const._MD_TOTALTOPICSC}>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php" title="<{$smarty.const._MD_ALL}>">
                                <{$stats[0].topic.total}>
                            </a></li>

                        <li><{$smarty.const._MD_TOTALPOSTSC}>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php" title="<{$smarty.const._MD_ALLPOSTS}>">
                                <{$stats[0].post.total}>
                            </a></li>
                        <{if $stats[0].digest.total}>
                            <li><{$smarty.const._MD_TOTALDIGESTSC}>
                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=digest" title="<{$smarty.const._MD_TOTALDIGESTSC}>">
                                    <{$stats[0].digest.total}>
                                </a></li>
                        <{/if}>

                        <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=new"
                               title="<{$smarty.const._MD_VIEW_NEWPOSTS}>">
                                <{$smarty.const._MD_VIEW_NEWPOSTS}>
                            </a></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-md-6">
                    <ul class="list-unstyled lw30">
                        <{if $userstats}>
                            <li><{*$userstats.lastvisit*}>
                                <{$userstats.lastpost}>
                            </li>
                        <{/if}>

                        <li><{$smarty.const._MD_TODAYTOPICSC}> <{$stats[0].topic.day|default:0}></li>
                        <li><{$smarty.const._MD_TODAYPOSTSC}> <{$stats[0].post.day|default:0}></li>

                        <{if $userstats}>
                            <li><{$userstats.topics}> | <{$userstats.posts}></li>
                            <{if $userstats.digests}>
                                <li><{$userstats.digests}></li>
                            <{/if}>
                        <{/if}>
                    </ul>
                </div>

            </div>
        <{/if}>

        <{if $online}>
            <{includeq file="db:newbb_online.tpl"}>
        <{/if}>

        <a title="NewBB" href="http://www.simple-xoops.de" class="btn btn-xs btn-success">NewBB Version <{$version/100}></a>
        <{if $rss_button}>
            <div class="text-right">
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/rss.php?c=<{$viewcat}>" target="_blank" title="RSS FEED">
                    <{$rss_button}>
                </a>
            </div>
        <{/if}>

        <{includeq file='db:newbb_notification_select.tpl'}>

    </div><!-- .xoops-newbb -->
