<div class="newbb-viewtopic">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$lang_forum_index}></a></li>

        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_NEWBB_FORUMHOME}></a></li>

        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.id}>"><{$category.title}></a></li>
        <{if isset($parentforum)}>
            <{foreach item=forum from=$parentforum|default:null}>
                <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>"><{$forum.forum_name}></a></li>
            <{/foreach}>
        <{/if}>
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>"><{$forum_name}></a></li>
        <li class="breadcrumb-item active"><{$topic_title|strip_tags}> <{if isset($topicstatus)}><{$topicstatus}><{/if}></li>
    </ol>

    <{if !empty($tagbar)}>
        <div class="newbb-tagbar">
            <{include file="db:tag_bar.tpl"}>
        </div>
    <{/if}>

    <{if !empty($online)}>
        <div class="newbb-online-users row mb10">
            <div class="col-md-12">
                <strong><{$smarty.const._MD_NEWBB_BROWSING}> </strong>
                <{foreach item=user from=$online.users|default:null}>
                    <a href="<{$user.link}>">
                        <{if $user.level == 2}><!-- If is admin -->
                            <label class="label label-success"><{$user.uname}></label>
                        <{elseif $user.level == 1}><!-- If is moderator -->
                            <label class="label label-warning"><{$user.uname}></label>
                        <{else}>
                            <label class="label label-info"><{$user.uname}></label>
                        <{/if}>
                    </a>
                <{/foreach}>

            <{if $online.num_anonymous}>
                    <span class="label label-default"><{$online.num_anonymous}> <{$smarty.const._MD_NEWBB_ANONYMOUS_USERS}></span>
            <{/if}>
            </div>
        </div>
    <{/if}>

    <div class="row mb10">
        <{if isset($viewer_level) && $viewer_level > 1}>
            <div class="col-sm-8 col-md-8">
                <{if isset($mode) && $mode > 1}>
                <form class="form-inline" name="form_posts_admin" action="action.post.php" method="POST" onsubmit="if(window.document.form_posts_admin.op.value &lt; 1){return false;}">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <div class="form-check mb-2">
                                <label for="post_check">
                                    <{$smarty.const._ALL}>:
                                </label>
                                <input type="checkbox" name="post_check" id="post_check" value="1" onclick="xoopsCheckAll('form_posts_admin', 'post_check');">
                            </div>
                        </div>
                        <div class="col-auto">
                            <select name="op" class="custom-select mb-2">
                                <option value="0"><{$smarty.const._SELECT}></option>
                                <option value="delete"><{$smarty.const._DELETE}></option>
                                <{if isset($status) &&  $status == "pending"}>
                                <option value="approve"><{$smarty.const._MD_NEWBB_APPROVE}></option>
                                <{elseif isset($status) &&  $status == "deleted"}>
                                <option value="restore"><{$smarty.const._MD_NEWBB_RESTORE}></option>
                                <{/if}>
                            </select>
                        </div>
                        <input type="hidden" name="topic_id" value="<{$topic_id}>">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary mb-2">Submit</button>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary mb-2" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?topic_id=<{$topic_id}>" target="_self"
                               title="<{$smarty.const._MD_NEWBB_TYPE_VIEW}>"><{$smarty.const._MD_NEWBB_TYPE_VIEW}></a>
                        </div>
                    </div>
                <{else}>
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?topic_id=<{$topic_id}>&amp;status=active#admin" title="<{$smarty.const._MD_NEWBB_TYPE_ADMIN}>"><{$smarty.const._MD_NEWBB_TYPE_ADMIN}></a> |
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?topic_id=<{$topic_id}>&amp;status=pending#admin" title="<{$smarty.const._MD_NEWBB_TYPE_PENDING}>"><{$smarty.const._MD_NEWBB_TYPE_PENDING}></a> |
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?topic_id=<{$topic_id}>&amp;status=deleted#admin" title="<{$smarty.const._MD_NEWBB_TYPE_DELETED}>"><{$smarty.const._MD_NEWBB_TYPE_DELETED}></a>
                <{/if}>
            </div>
        <{/if}>
        <div class="<{if isset($viewer_level) && $viewer_level > 1}>col-sm-4 col-md-4<{else}>col-sm-12 col-md-12<{/if}> generic-pagination text-right">
            <{$forum_page_nav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
        </div>
    </div>

    <{if isset($mode) && $mode <= 1}>
        <{if isset($topic_poll)}>
            <{if !empty($topic_pollresult)}>
                <{include file="db:newbb_poll_results.tpl" poll=$poll|default:''}>
            <{else}>
                <{include file="db:newbb_poll_view.tpl" poll=$poll|default:''}>
            <{/if}>
        <{/if}>
    <{/if}>

    <div class="row mb10">
        <div class="col-sm-6 col-md-6">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/reply.php?topic_id=<{$topic_id}>" title="<{$smarty.const.THEME_FORUM_REPLY}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_REPLY}></a>

            <{if isset($viewer_level) && $viewer_level > 1}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?forum=<{$forum_id}>" title="<{$smarty.const.THEME_FORUM_NEWTOPIC}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_NEWTOPIC}></a>
            <{elseif !$xoops_isuser}>
                <a href="<{$xoops_url}>/user.php" title="<{$smarty.const.THEME_FORUM_REGISTER}>" class="btn btn-success"><{$smarty.const.THEME_FORUM_REGISTER}></a>
            <{/if}>

            <a data-toggle="collapse" href="#forum-search" title="<{$smarty.const.THEME_FORUM_SEARCH}>" class="btn btn-info">
                <span class="fa fa-search"></span>
            </a>

            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/polls.php?op=add&topic_id=<{$topic_id}>" title="<{$smarty.const.THEME_ADD_POLL}>" class="btn btn-info"><{$smarty.const.THEME_ADD_POLL}></a>

        </div>

        <div class="col-sm-6 col-md-6 text-right hidden-xs">
            <a id="threadtop"></a>
            <a class="btn btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?order=<{$order_current}>&amp;topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;move=prev" title="<{$smarty.const._MD_NEWBB_PREVTOPIC}>">
                <span class="fa fa-arrow-circle-left"></span>
            </a>
            <a class="btn btn-info" href="#threadbottom" title="<{$smarty.const._MD_NEWBB_BOTTOM}>">
                <span class="fa fa-arrow-circle-down"></span>
            </a>
            <a class="btn btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?order=<{$order_current}>&amp;topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;move=next" title="<{$smarty.const._MD_NEWBB_NEXTTOPIC}>">
                <span class="fa fa-arrow-circle-right"></span>
            </a>
        </div>
    </div>

    <div class="row collapse mb10" id="forum-search">
        <div class="col-sm-12 col-md-12">
            <{if isset($mode) && $mode <= 1}>
                <form class="input-group" id="search-topic" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get" role="search">
                    <input name="term" id="term" type="text" class="form-control" placeholder="<{$smarty.const.THEME_NEWBB_SEARCH_TOPIC}>">
                    <input type="hidden" name="forum" id="forum" value="<{$forum_id}>">
                    <input type="hidden" name="sortby" id="sortby" value="p.post_time desc">
                    <input type="hidden" name="topic" id="topic" value="<{$topic_id}>">
                    <input type="hidden" name="action" id="action" value="yes">
                    <input type="hidden" name="searchin" id="searchin" value="both">
                    <input type="hidden" name="show_search" id="show_search" value="post_text">
                    <span class="input-group-btn">
                        <input type="submit" class="btn btn-primary" value="<{$smarty.const._MD_NEWBB_SEARCH}>">
                    </span>
                </form>
            <{/if}>
        </div>
    </div>

    <div class="form-row">
        <div class="col">
            <select class="form-control mb-2" name="topicoption" id="topicoption" onchange="if(this.options[this.selectedIndex].value.length >0 ) { window.document.location=this.options[this.selectedIndex].value;}">
                <option value=""><{$smarty.const._MD_NEWBB_TOPICOPTION}></option>
                <{if isset($viewer_level) && $viewer_level > 1}>
                <{foreach item=act from=$admin_actions|default:null}>
                <option value="<{$act.link}>"><{$act.name}></option>
                <{/foreach}>
                <{/if}>
                <{if isset($adminpoll_actions) && $adminpoll_actions|is_array && count($adminpoll_actions) > 0 }>
                <option value="">--------</option>
                <option value=""><{$smarty.const._MD_NEWBB_POLLOPTIONADMIN}></option>
                <{foreach item=actpoll from=$adminpoll_actions|default:null}>
                <option value="<{$actpoll.link}>"><{$actpoll.name}></option>
                <{/foreach}>
                <{/if}>
            </select>
        </div>
        <{if $rating_enable && $forum_post && $forum_reply}>
        <div class="col">
            <select class="form-control  mb-2" name="rate" id="rate" onchange="if(this.options[this.selectedIndex].value.length >0 ) { window.document.location=this.options[this.selectedIndex].value;}">
                <option value=""><{$smarty.const._MD_NEWBB_RATE}></option>
                <option value="<{$xoops_url}>/modules/<{$xoops_dirname}>/ratethread.php?topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;rate=5"><{$smarty.const._MD_NEWBB_RATE5}></option>
                <option value="<{$xoops_url}>/modules/<{$xoops_dirname}>/ratethread.php?topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;rate=4"><{$smarty.const._MD_NEWBB_RATE4}></option>
                <option value="<{$xoops_url}>/modules/<{$xoops_dirname}>/ratethread.php?topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;rate=3"><{$smarty.const._MD_NEWBB_RATE3}></option>
                <option value="<{$xoops_url}>/modules/<{$xoops_dirname}>/ratethread.php?topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;rate=2"><{$smarty.const._MD_NEWBB_RATE2}></option>
                <option value="<{$xoops_url}>/modules/<{$xoops_dirname}>/ratethread.php?topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;rate=1"><{$smarty.const._MD_NEWBB_RATE1}></option>
            </select>
        </div>
        <{/if}>
        <div class="col">
            <select class="form-control mb-2" name="viewmode" id="viewmode" onchange="if(this.options[this.selectedIndex].value.length >0 ) { window.location=this.options[this.selectedIndex].value;}">
                <option value=""><{$smarty.const._MD_NEWBB_VIEWMODE}></option>
                <{foreach item=act from=$viewmode_options|default:null}>
                <option value="<{$act.link}>"><{$act.title}></option>
                <{/foreach}>
            </select>
        </div>
    </div>

    <{if $viewer_level > 1 && $topic_status == 1}>
        <{$smarty.const._MD_NEWBB_TOPICLOCK}>
    <{/if}>

    <{foreach item=topic_post from=$topic_posts|default:null}>
        <{include file="db:newbb_thread.tpl" topic_post=$topic_post mode=$mode}>
    <{foreachelse}>
        <div class="alert alert-warning" role="alert"><{$smarty.const._MD_NEWBB_NOTOPIC}></div>
    <{/foreach}>

    <{if isset($mode) && $mode > 1}>
    </form>
    <{/if}>

    <div class="newbb-viewtopic-footer">
    <div class="row mb10">
        <div class="col-sm-6 col-md-6 hidden-xs">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/reply.php?topic_id=<{$topic_id}>" title="<{$smarty.const.THEME_FORUM_REPLY}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_REPLY}></a>

            <{if isset($viewer_level) && $viewer_level > 1}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/newtopic.php?forum=<{$forum_id}>" title="<{$smarty.const.THEME_FORUM_NEWTOPIC}>" class="btn btn-primary"><{$smarty.const.THEME_FORUM_NEWTOPIC}></a>
            <{else}>
                <a href="<{$xoops_url}>/user.php" title="<{$smarty.const.THEME_FORUM_REGISTER}>" class="btn btn-success"><{$smarty.const.THEME_FORUM_REGISTER}></a>
            <{/if}>

            <{if $quickreply.show}>
                <a href="#quickReply" data-toggle="collapse" title="" class="btn btn-info">Quick Reply</a>
            <{/if}>

            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/polls.php?op=add&topic_id=<{$topic_id}>" title="<{$smarty.const.THEME_ADD_POLL}>" class="btn btn-info"><{$smarty.const.THEME_ADD_POLL}></a>
        </div>

        <div class="xoopsform col-sm-4 col-md-4">
            <{$forum_jumpbox|replace:' class="select"':' class="btn btn-light"'|replace:"'button'":'"btn btn-sm btn-light"'}>
        </div>

        <div class="col-sm-2 col-md-2 text-right nompl hidden-xs">
            <a id="threadbottom"></a>
            <a class="btn btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?order=<{$order_current}>&amp;topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;move=prev" title="<{$smarty.const._MD_NEWBB_PREVTOPIC}>">
                <span class="fa fa-arrow-circle-left"></span>
            </a>
            <a class="btn btn-info" href="#threadtop" title="<{$smarty.const._MD_NEWBB_TOP}>">
                <span class="fa fa-arrow-circle-up"></span>
            </a>
            <a class="btn btn-info" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?order=<{$order_current}>&amp;topic_id=<{$topic_id}>&amp;forum=<{$forum_id}>&amp;move=next" title="<{$smarty.const._MD_NEWBB_NEXTTOPIC}>">
                <span class="fa fa-arrow-circle-right"></span>
            </a>
        </div>
    </div>
    <div class="text-right generic-pagination"><{$forum_page_nav|replace:'form':'div'|replace:'id="xo-pagenav"':''}></div>
    <{if $quickreply.show}>
        <div class="col-md-12 collapse newbb-quick-reply" id="quickReply"><{$quickreply.form}></div>
    <{/if}>
    </div>

<{include file='db:system_notification_select.tpl'}>

</div>

<!-- START irmtfan add scroll js function to scroll down to current post or top of the topic -->
<script type="text/javascript">
    if (document.body.scrollIntoView && window.location.href.indexOf('#') == -1) {
        var el = xoopsGetElementById('<{$forum_post_prefix|default:''}><{$post_id}>');
        if (el) {
            banner.destroy();
            header.destroy();
            el.scrollIntoView();

//        var offset = $(this).offset(); // Contains .top and .left
            offsetleft -= 0;
            offsettop -= 200;

            document.documentElement.scrollTop = el.offsetTop;
        }
    }
</script>
<!-- END irmtfan add scroll js function to scroll down to current post or top of the topic -->
