<div class="newbb">

    <div class="jumbotron newbb-header">
        <h3>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php" title="<{$index_title}>">
                <{$index_title}>
            </a>
        </h3>

        <p><{$index_desc}></p>

        <div class="dropdown pull-right">
            <button class="btn btn-primary btn-block dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <{$smarty.const._MD_MAINFORUMOPT}> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" style="min-width:20em;">
                <li><a href="<{$mark_read}>"><{$smarty.const._MD_MARK_ALL_FORUMS}>&nbsp;<{$smarty.const._MD_MARK_READ}></li>
                <li><a href="<{$mark_unread}>"><{$smarty.const._MD_MARK_ALL_FORUMS}>&nbsp;<{$smarty.const._MD_MARK_UNREAD}></li>
                <li role="separator" class="divider"></li>
                <li><a href="<{$post_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALLPOSTS}></li>
                <li><a href="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_NEWPOSTS}></li>
                <li><a href="<{$all_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALL}></li>
                <li><a href="<{$digest_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_DIGEST}></li>
                <li><a href="<{$unreplied_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_UNREPLIED}></li>
                <li><a href="<{$unread_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_UNREAD}></li>
                <{if $forum_index_cpanel}>
                <li role="separator" class="divider"></li>
                <li><a href="<{$forum_index_cpanel.link}>"><{$forum_index_cpanel.name}></li>
                <{/if}>
            </ul>
        </div>

        <div class="newbb-header-icons hidden-xs">
            <{if $viewer_level gt 1}>
                <div>
                    <strong><{$smarty.const._MD_TOPIC}>:</strong>
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=active#admin" title="<{$smarty.const._MD_TYPE_ADMIN}>"
                       class="btn btn-xs btn-primary">
                        <{$smarty.const._MD_TYPE_ADMIN}>
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=pending#admin" title="<{$smarty.const._MD_TYPE_PENDING}>"
                       class="btn btn-xs btn-primary">
                        <{if $wait_new_topic}><span class="badge"><{$wait_new_topic}></span><{/if}> <{$smarty.const._MD_TYPE_PENDING}>
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=deleted#admin" title="<{$smarty.const._MD_TYPE_DELETED}>"
                       class="btn btn-xs btn-danger">
                       <{$smarty.const._MD_TYPE_DELETED}> <span class="badge"><{$delete_topic}></span>
                    </a>
                </div>
                <div>
                    <strong><{$smarty.const._MD_POST2}>:</strong>
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=active#admin" title="<{$smarty.const._MD_TYPE_ADMIN}>"
                       class="btn btn-xs btn-primary">
                        <{$smarty.const._MD_TYPE_ADMIN}>
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=pending#admin" title="<{$smarty.const._MD_TYPE_PENDING}>"
                       class="btn btn-xs btn-primary">
                       <{$smarty.const._MD_TYPE_PENDING}> <span class="badge"><{$wait_new_post}></span>
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=deleted#admin" title="<{$smarty.const._MD_TYPE_DELETED}>"
                       class="btn btn-xs btn-primary">
                       <{$smarty.const._MD_TYPE_DELETED}> <span class="badge"><{$delete_post}></span>
                    </a>

                    <{if $report_post}>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/admin_report.php" title="<{$smarty.const._MD_REPORT}>" class="btn btn-xs btn-primary">
                            <{$smarty.const._MD_REPORT}> <span class="badge"><{$reported_count}></span>
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

    <div class="panel-group xoops-newbb-category-list" id="accordion">
        <{foreach item=category from=$categories}><!-- Forum categories -->
        <div class="panel panel-default mb10">
            <div class="panel-heading">
                <div class="panel-title xoops-newbb-forum-title">
                    <h4>
                    <{if $category.forums}>
                        <a data-toggle="collapse" data-parent="#accordion" href="#<{$category.cat_element_id}>"
                           title="<{$smarty.const.THEME_NEWBB_TOPIC}>">
                            <span class="glyphicon glyphicon-list"></span>
                        </a>
                    <{/if}>
                    <{if $category.cat_image}>
                        <img src="<{$category.cat_image}>" alt="<{$category.cat_title}>">
                    <{/if}>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.cat_id}>" title="<{$category.cat_title}>">
                        <{$category.cat_title}>
                    </a>
                    </h4>
                    <p class="text-muted"><{$category.cat_description}>
                        <{if $category.cat_sponsor}>
                        <a href="<{$category.cat_sponsor.link}>" title="<{$smarty.const.THEME_FORUM_SPONSORBY}> <{$category.cat_sponsor.title}>"
                           target="_blank" class="pull-right btn btn-xs btn-success">
                            <{$category.cat_sponsor.title}>
                        </a>
                        <{/if}>
                    </p>


                </div>
            </div><!-- .panel-heading -->

            <div id="<{$category.cat_element_id}>" class="panel-collapse collapse in <{if $subforum_display == 'expand'}>in<{/if}>">
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
                    <{foreach item=forum from=$category.forums}>
                    <div class="row xoops-newbb-list-foruns mb10">
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
                            <p class="text-muted"><{$forum.forum_desc}></p>
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
                                <{$smarty.const._MD_NOTOPIC}>
                            <{/if}>

                            <{if $forum.subforum}>
                                <{$smarty.const._MD_SUBFORUMS}><{$img_subforum}>
                                <{foreach item=subforum from=$forum.subforum}>
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
                <button class="btn btn-primary" type="submit" id="submit"><{$smarty.const._MD_SEARCH}></button>
            </span>
            </form>
        </div>
        <div class="col-md-4">
            <a class="btn btn-primary btn-block" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const._MD_ADVSEARCH}></a>
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

    <ul class="list-unstyled">
        <li><a title="NewBB" href="http://www.simple-xoops.de" class="btn btn-xs btn-success">NewBB Version <{$version/100}></a></li>
        <{if $rss_button}>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/rss.php?c=<{$viewcat}>" target="_blank" title="RSS feed" class="btn btn-xs btn-warning">
            RSS
        </a>
        <{/if}>
    </ul>

    <{includeq file='db:newbb_notification_select.tpl'}>

</div><!-- .xoops-newbb -->
