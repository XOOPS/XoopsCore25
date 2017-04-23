<ol class="breadcrumb">
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
</ol>
<div class="row">
    <div class="col-md-6 col-xs-12">
        <h3><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$forum_index_title}></a></h3>
    </div>
    <div class="col-md-6 col-xs-12 pull-right">
        <{if $mode gt 1}>
        <form name="form_topics_admin" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/action.topic.php" method="POST" onsubmit="if(window.document.form_topics_admin.op.value &lt; 1){return false;}">
        <{/if}>
        <{if $viewer_level gt 1}>
            <!-- irmtfan hardcode removed style="padding: 5px;float: right; text-align:right;" -->
            <div class="pagenav" id="admin">
                <{if $mode gt 1}>
                    <{$smarty.const._ALL}>:
                    <input type="checkbox" name="topic_check1" id="topic_check1" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check1');"/>
                    <select class="form-control" name="op">
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
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php" target="_self"
                       title="<{$smarty.const._MD_TYPE_VIEW}>"><{$smarty.const._MD_TYPE_VIEW}></a>
                    <!-- irmtfan remove < { elseif $mode eq 1} > to show all admin links in admin mode in the initial page loading -->
                <{else}>
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=active#admin" target="_self"
                       title="<{$smarty.const._MD_TYPE_ADMIN}>"><{$smarty.const._MD_TYPE_ADMIN}></a>
                    |
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=pending#admin" target="_self"
                       title="<{$smarty.const._MD_TYPE_PENDING}>"><{$smarty.const._MD_TYPE_PENDING}></a>
                    |
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?status=deleted#admin" target="_self"
                       title="<{$smarty.const._MD_TYPE_DELETED}>"><{$smarty.const._MD_TYPE_DELETED}></a>
                    |
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/moderate.php" target="_self"
                       title="<{$smarty.const._MD_TYPE_SUSPEND}>"><{$smarty.const._MD_TYPE_SUSPEND}></a>
                    <!-- irmtfan remove < { else } > no need for mode=1
                    < { else } >
                    <!--<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.topic.php?mode=1#admin" target="_self" title="<{$smarty.const._MD_TYPE_VIEW}>">
                        <{$smarty.const._MD_TYPE_VIEW}></a>-->
                <{/if}>
            </div>
            <br>
        <{else}>
            <br>
        <{/if}>
        <div class="clear"></div>
    
        <div>
            <div>
                <{if $menumode eq 0}>
                    <select class="form-control menu" name="topicoption" id="topicoption" onchange="if(this.options[this.selectedIndex].value.length >0 )    { window.document.location=this.options[this.selectedIndex].value;}">
                        <option value=""><{$smarty.const._MD_TOPICOPTION}></option>
                        <option value="<{$post_link}>"><{$smarty.const._MD_VIEW}>
                            &nbsp;<{$smarty.const._MD_ALLPOSTS}></option>
                        <option value="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>
                            &nbsp;<{$smarty.const._MD_NEWPOSTS}></option>
                        <!-- irmtfan add a separator -->
                        <option value="">--------</option>
                        <{foreach item=filter from=$filters}>
                            <option value="<{$filter.link}>"><{$filter.title}></option>
                        <{/foreach}>
                        <option value="">--------</option>
                        <{foreach item=filter from=$types}>
                            <option value="<{$filter.link}>"><{$filter.title}></option>
                        <{/foreach}>
                    </select>
                <{elseif $menumode eq 1}>
                    <div id="topicoption" class="menu">
                        <table>
                            <tr>
                                <td>
                                    <a class="item" href="<{$post_link}>"><{$smarty.const._MD_VIEW}>
                                        &nbsp;<{$smarty.const._MD_ALLPOSTS}></a>
                                    <a class="item" href="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>
                                        &nbsp;<{$smarty.const._MD_NEWPOSTS}></a>
                                    <a class="item" href="<{$all_link}>"><{$smarty.const._MD_VIEW}>
                                        &nbsp;<{$smarty.const._MD_ALL}></a>
                                    <a class="item" href="<{$digest_link}>"><{$smarty.const._MD_VIEW}>
                                        &nbsp;<{$smarty.const._MD_DIGEST}></a>
                                    <a class="item" href="<{$unreplied_link}>"><{$smarty.const._MD_VIEW}>
                                        &nbsp;<{$smarty.const._MD_UNREPLIED}></a>
                                    <a class="item" href="<{$unread_link}>"><{$smarty.const._MD_VIEW}>
                                        &nbsp;<{$smarty.const._MD_UNREAD}></a>
    
                                </td>
                            </tr>
                        </table>
                    </div>
                    <script type="text/javascript">document.getElementById("topicoption").onmouseout = closeMenu;</script>
                    <div class="menubar"><a href="" onclick="openMenu(event, 'topicoption');return false;"><{$smarty.const._MD_TOPICOPTION|escape:'quotes'}></a>
                    </div>
                <{elseif $menumode eq 2}>
                    <div class="menu">
                        <ul>
                            <li>
                                <div class="item"><strong><{$smarty.const._MD_TOPICOPTION}></strong></div>
                                <ul>
                                    <li>
                                        <table>
                                            <tr>
                                                <td>
                                                    <div class="item"><a href="<{$post_link}>"><{$smarty.const._MD_VIEW}>
                                                            &nbsp;<{$smarty.const._MD_ALLPOSTS}></a></div>
                                                    <div class="item"><a href="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>
                                                            &nbsp;<{$smarty.const._MD_NEWPOSTS}></a></div>
                                                    <div class="item"><a href="<{$all_link}>"><{$smarty.const._MD_VIEW}>
                                                            &nbsp;<{$smarty.const._MD_ALL}></a></div>
                                                    <div class="item"><a href="<{$digest_link}>"><{$smarty.const._MD_VIEW}>
                                                            &nbsp;<{$smarty.const._MD_DIGEST}></a></div>
                                                    <div class="item"><a
                                                                href="<{$unreplied_link}>"><{$smarty.const._MD_VIEW}>
                                                            &nbsp;<{$smarty.const._MD_UNREPLIED}></a></div>
                                                    <div class="item"><a href="<{$unread_link}>"><{$smarty.const._MD_VIEW}>
                                                            &nbsp;<{$smarty.const._MD_UNREAD}></a></div>
    
                                                </td>
                                            </tr>
                                        </table>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <{/if}>
            </div>
            <!-- irmtfan hardcode removed style="padding: 5px;float: right; text-align:right;" -->
            <div class="pagenav">
                <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
                <!-- irmtfan to solve nested forms and id="xo-pagenav" issue -->
            </div>
        </div>
        <div class="clear"></div>
        <br>
        <br>
    <{if $mode gt 1}>
    </form>
    <{/if}>
    
        </div> <!-- end column -->
    </div><!-- end row -->
<table class="table table-responsive" width="100%" align="center">
    <!-- irmtfan hardcode removed align="left" -->
    <thead>
    <tr class="head" class="align_left">
        <th width="5%" colspan="2">
            <{if $mode gt 1}>
                <{$smarty.const._ALL}>:
                <input type="checkbox" name="topic_check" id="topic_check" value="1" onclick="xoopsCheckAll('form_topics_admin', 'topic_check');"/>
            <{else}>
                &nbsp;
            <{/if}>
            </tth>
            <th><a href="<{$headers.topic.link}>"><{$headers.topic.title}></a></th>
            <th width="15%" align="center" nowrap="nowrap"><a href="<{$headers.forum.link}>"><{$headers.forum.title}></a></th>
            <th width="5%" align="center" nowrap="nowrap"> <a href="<{$headers.replies.link}>"><{$headers.replies.title}></a></th>
            <th width="10%" align="center" nowrap="nowrap"><a href="<{$headers.poster.link}>"><{$headers.poster.title}></a></td>
            <th width="5%" align="center" nowrap="nowrap"> <a href="<{$headers.views.link}>"><{$headers.views.title}></a></th>
            <th width="15%" align="center" nowrap="nowrap"><a href="<{$headers.lastpost.link}>"><{$headers.lastpost.title}></a></th>
        </tr>
    </thead>
    <tbody>
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
            <{$topic.topic_title}></a><{$topic.attachment}> <{$topic.topic_page_jump}>
            <!-- irmtfan add topic publish time and rating -->
            <br>
            <span><{$headers.publish.title}>: <{$topic.topic_time}></span>
            <{if $rating_enable && $topic.votes}>
                    |&nbsp;
                    <span><{$headers.votes.title}>: <{$topic.votes}>&nbsp;<{$topic.rating_img}></span>
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
    <tr class="foot">
    <td colspan="8" align="center">
        <{strip}>
            <form method="get" action="<{$selection.action}>">
                <strong><{$smarty.const._MD_SORTEDBY}></strong>&nbsp;
                <{$selection.sort}>&nbsp;
                <{$selection.order}>&nbsp;
                <{$selection.since}>&nbsp;
                <{foreach item=hidval key=hidvar from=$selection.vars}>
                    <{if $hidval && $hidvar neq "sort" && $hidvar neq "order" && $hidvar neq "since"}>
                        <!-- irmtfan correct name="$hidvar" -->
                        <input type="hidden" name="<{$hidvar}>" value="<{$hidval}>"/>
                    <{/if}>
                <{/foreach}>
                <!-- irmtfan remove name="refresh" -->
                <input type="submit" value="<{$smarty.const._SUBMIT}>"/>
            </form>
        <{/strip}>
    </td>
    </tr>
    </tbody>
</table>
<!-- end forum main table -->

<{if $pagenav}>
    <!-- irmtfan hardcode removed style="padding: 5px;float: right; text-align:right;" -->
    <div class="pagenav"><{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
        <!-- irmtfan to solve nested forms and id="xo-pagenav" issue --></div>
    <br>
<{/if}>
<div class="clear"></div>

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
    <div class="icon_right">
        <form action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get">
            <input name="term" id="term" type="text" size="15"/>
            <{foreach item=hidval key=hidvar from=$search}>
                <{if $hidval }>
                    <!-- irmtfan correct name="$hidvar" -->
                    <input type="hidden" name="<{$hidvar}>" value="<{$hidval}>"/>
                <{/if}>
            <{/foreach}>
            <input type="submit" class="formButton" value="<{$smarty.const._MD_SEARCH}>"/><br>
            [<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const._MD_ADVSEARCH}></a>]
        </form>
        <br>
        <!-- START irmtfan add forum selection box -->
        <{if $forum_jumpbox }>
            <form method="get" action="<{$selection.action}>">
                <{$selection.forum}>&nbsp;
                <{foreach item=hidval key=hidvar from=$selection.vars}>
                    <{if $hidval && $hidvar neq "forum"}>
                        <input type="hidden" name="<{$hidvar}>" value="<{$hidval}>"/>
                    <{/if}>
                <{/foreach}>
                <input type="submit" value="<{$smarty.const._SUBMIT}>"/>
            </form>
            <br>
            <{$forum_jumpbox}>
        <{/if}>
        <!-- END irmtfan add forum selection box -->
    </div>
</div>
<div class="clear"></div>
<br>

<{if $online}><{includeq file="db:newbb_online.tpl"}><{/if}>
<{includeq file='db:newbb_notification_select.tpl'}>
<!-- end module contents -->
