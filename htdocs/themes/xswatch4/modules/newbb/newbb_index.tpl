<div class="newbb">

    <div class="jumbotron newbb-header">
        <h3>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php" title="<{$index_title}>">
                <{$index_title}>
            </a>
        </h3>

        <p><{$index_desc}></p>

        <div class="container">
            <div class="row">
                <div class="col">
                    <{include file="db:newbb_index_menu.tpl"}>
                </div>
            </div>

            <{if isset($viewer_level) &&  $viewer_level > 1}>
            <br>
            <div class="row">
                <div class="col">
                    <strong><{$smarty.const._MD_NEWBB_TOPIC}>:</strong>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=active#admin" title="<{$smarty.const._MD_NEWBB_TYPE_ADMIN}>" class="btn btn-primary">
                            <span class="fa fa-tasks" aria-hidden="true"></span>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=pending#admin" title="<{$smarty.const._MD_NEWBB_TYPE_PENDING}>" class="btn btn-primary">
                            <span class="fa fa-check-square-o" aria-hidden="true"></span> <{if !empty($wait_new_topic)}><span class="badge badge-light badge-pill"><{$wait_new_topic}></span><{/if}>
                        </a>

                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=deleted#admin" title="<{$smarty.const._MD_NEWBB_TYPE_DELETED}>" class="btn btn-danger">
                            <span class="fa fa-trash-o" aria-hidden="true"></span> <{if !empty($delete_topic)}><span class="badge badge-light badge-pill"><{$delete_topic}></span><{/if}>
                        </a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <strong><{$smarty.const._MD_NEWBB_POST2}>:</strong>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=active#admin" title="<{$smarty.const._MD_NEWBB_TYPE_ADMIN}>" class="btn btn-primary">
                        <span class="fa fa-tasks" aria-hidden="true"></span>
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=pending#admin" title="<{$smarty.const._MD_NEWBB_TYPE_PENDING}>" class="btn btn-primary">
                        <span class="fa fa-check-square-o" aria-hidden="true"></span> <{if !empty($wait_new_post)}><span class="badge badge-light badge-pill"><{$wait_new_post}></span><{/if}>
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=deleted#admin" title="<{$smarty.const._MD_NEWBB_TYPE_DELETED}>" class="btn btn-primary">
                        <span class="fa fa-trash-o" aria-hidden="true"></span> <{if !empty($delete_post)}><span class="badge badge-light badge-pill"><{$delete_post}></span><{/if}>
                    </a>

                    <{if !empty($report_post)}>
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/admin_report.php" title="_MD_NEWBB_REPORT" class="btn btn-primary">
                        <span class="fa fa-thumbs-o-down" aria-hidden="true"></span> <span class="badge badge-light badge-pill"><{$reported_count}></span>
                    </a>
                    <{/if}>


                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/moderate.php" title="<{$smarty.const._MD_NEWBB_TYPE_SUSPEND}>" class="btn btn-primary">
                        <span class="fa fa-ban" aria-hidden="true">
                    </a>

                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/index.php" title="<{$smarty.const._MD_NEWBB_ADMINCP}>" class="btn btn-primary">
                        <span class="fa fa-cogs" aria-hidden="true"></span>
                    </a>
                </div>
            </div>
            <{/if}>
        </div><!-- .newbb-header -->
    </div>

    <table class="table table-hover">
        <{foreach item=category from=$categories|default:null}><!-- Forum categories -->
        <thead>
        <tr class="thead-light">
            <th scope="col" colspan="5">
                <span class="fa fa-list"></span>
                <span class="font-weight-bold"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.cat_id}>" title="<{$category.cat_title}>"><{$category.cat_title}></a></span>
                <br><small><{$category.cat_description}></small>
            </th>
        </tr>
        <tr class="table-sm">
            <th scope="col"> </th>
            <th scope="col"><{$smarty.const._MD_NEWBB_FORUM}></th>
            <th scope="col" class="d-none d-sm-table-cell"><{$smarty.const._MD_NEWBB_TOPICS}></th>
            <th scope="col" class="d-none d-sm-table-cell"><{$smarty.const._MD_NEWBB_POSTS}></th>
            <th scope="col"><{$smarty.const._MD_NEWBB_LASTPOST}></th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=forum from=$category.forums|default:null}>
        <tr>
            <td><{$forum.forum_folder|default:''}></td>
            <td><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>" title="<{$forum.forum_name}>"><{$forum.forum_name}></a>
                <br><small><{$forum.forum_desc}></small></td>
            <td class="d-none d-sm-table-cell"><{$forum.forum_topics}></td>
            <td class="d-none d-sm-table-cell"><{$forum.forum_posts}></td>
            <td>
                <{if !empty($forum.forum_lastpost_subject)}>
                <{$forum.forum_lastpost_time}> <{$smarty.const._MD_NEWBB_BY}> <{$forum.forum_lastpost_user}>
                <br>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?post_id=<{$forum.forum_lastpost_id}>">
                    <{$forum.forum_lastpost_subject}>
                    <span class="fa fa-forward" aria-hidden="true" title="<{$smarty.const._MD_NEWBB_GOTOLASTPOST}>"></span>
                </a>
                <{else}>
                <{$smarty.const._MD_NEWBB_NOTOPIC}>
                <{/if}>
                <{if !empty($forum.subforum)}>
                <br><{$smarty.const._MD_NEWBB_SUBFORUMS}> <i class="fa fa-chevron-down" aria-hidden="true"></i>
                <{foreach item=subforum from=$forum.subforum|default:null}><br>
                [<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$subforum.forum_id}>"><{$subforum.forum_name}></a>]
                <{/foreach}>
                <{/if}>
            </td>
        </tr>
        <{/foreach}>
        </tbody>
        <{/foreach}>
    </table>

        <div class="row mb10">
            <div class="col-md-12">
                <{$img_forum_new}> = <{$smarty.const._MD_NEWBB_NEWPOSTS}>
                <{$img_forum}> = <{$smarty.const._MD_NEWBB_NONEWPOSTS}>
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
                <button class="btn btn-primary" type="submit" id="searchsubmit"><{$smarty.const.THEME_FORUM_SEARCH}></button>
            </span>
                </form>
            </div>
            <div class="col-md-4">
                <a class="btn btn-primary btn-block" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const.THEME_FORUM_ADVSEARCH}></a>
            </div>
        </div>

        <{if !empty($currenttime)}>
            <div class="row">
                <div class="col-lg-12"><h3 class="nompl"><{$online.statistik}> <{$smarty.const._MD_NEWBB_STATS}></h3></div>
                <div class="col-sm-6 col-md-6">
                    <ul class="list-unstyled lw30">
                        <li><{$currenttime}></li>
                        <li><{$lastvisit}></li>

                        <li><{$smarty.const._MD_NEWBB_TOTALTOPICSC}>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php" title="<{$smarty.const._MD_NEWBB_ALL}>">
                                <{$stats[0].topic.total|default:''}>
                            </a></li>

                        <li><{$smarty.const._MD_NEWBB_TOTALPOSTSC}>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php" title="<{$smarty.const._MD_NEWBB_ALLPOSTS}>">
                                <{$stats[0].post.total|default:''}>
                            </a></li>
                        <{if !empty($stats[0].digest.total)}>
                            <li><{$smarty.const._MD_NEWBB_TOTALDIGESTSC}>
                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=digest" title="<{$smarty.const._MD_NEWBB_TOTALDIGESTSC}>">
                                    <{$stats[0].digest.total}>
                                </a></li>
                        <{/if}>

                        <li><a class="btn btn-primary" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?status=new"
                               title="<{$smarty.const._MD_NEWBB_VIEW_NEWPOSTS}>">
                                <{$smarty.const._MD_NEWBB_VIEW_NEWPOSTS}>
                            </a></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-md-6">
                    <ul class="list-unstyled lw30">
                        <{if !empty($userstats)}>
                            <li><{*$userstats.lastvisit*}>
                                <{$userstats.lastpost}>
                            </li>
                        <{/if}>

                        <li><{$smarty.const._MD_NEWBB_TODAYTOPICSC}> <{$stats[0].topic.day|default:0}></li>
                        <li><{$smarty.const._MD_NEWBB_TODAYPOSTSC}> <{$stats[0].post.day|default:0}></li>

                        <{if !empty($userstats)}>
                            <li><{$userstats.topics}> | <{$userstats.posts}></li>
                            <{if !empty($userstats.digests)}>
                                <li><{$userstats.digests}></li>
                            <{/if}>
                        <{/if}>
                    </ul>
                </div>

            </div>
        <{/if}>

    <{if !empty($online)}>
            <{include file="db:newbb_online.tpl"}>
        <{/if}>

        <a title="NewBB" href="https://xoops.org" class="btn btn-success">NewBB Version <{$version}></a>
        <{if !empty($rss_button)}>
            <div class="text-right">
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/rss.php?c=<{$viewcat}>" target="_blank" title="RSS FEED">
                    <{$rss_button}>
                </a>
            </div>
        <{/if}>

        <{include file='db:system_notification_select.tpl'}>

    </div><!-- .xoops-newbb -->
