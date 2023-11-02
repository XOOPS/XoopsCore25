<div>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$lang_forum_index}></a></li>

        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_NEWBB_FORUMHOME}></a></li>

        <!-- If is subforum-->
        <{if isset($parent_forum)}>
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$parent_forum}>"><{$parent_name}></a></li>
        <{/if}>

        <li class="breadcrumb-item active"><{$lang_title}></li>
    </ol>
</div>
<div class="clear"></div>
<{if isset($viewer_level) && $viewer_level > 1}>
    <div class="right" id="admin">
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
                <input type="hidden" name="uid" value="<{$uid}>">
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary mb-2" href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>" target="_self"
                       title="<{$smarty.const._MD_NEWBB_TYPE_VIEW}>"><{$smarty.const._MD_NEWBB_TYPE_VIEW}></a>
                </div>
            </div>
        <{else}>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>&amp;status=active#admin"
               target="_self"
               title="<{$smarty.const._MD_NEWBB_TYPE_ADMIN}>"><{$smarty.const._MD_NEWBB_TYPE_ADMIN}></a> |
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>&amp;status=pending#admin"
               target="_self"
               title="<{$smarty.const._MD_NEWBB_TYPE_PENDING}>"><{$smarty.const._MD_NEWBB_TYPE_PENDING}></a> |
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewpost.php?uid=<{$uid}>&amp;status=deleted#admin"
               target="_self"
               title="<{$smarty.const._MD_NEWBB_TYPE_DELETED}>"><{$smarty.const._MD_NEWBB_TYPE_DELETED}></a>
        <{/if}>
    </div>
<{/if}>
<div class="clear"></div>
<div class="text-right">
<a id="threadtop"></a><a class="btn btn-info" href="#threadbottom" title="<{$smarty.const._MD_NEWBB_BOTTOM}>">
    <span class="fa fa-arrow-circle-down"></span>
</a>
</div>
<div>
        <div class="form-row">
            <div class="col">
                <select
                        name="topicoption" id="topicoption"
                        class="form-control  mb-2"
                        onchange="if(this.options[this.selectedIndex].value.length >0 )    { window.document.location=this.options[this.selectedIndex].value;}"
                >
                    <option value=""><{$smarty.const._MD_NEWBB_TOPICOPTION}></option>
                    <option value="<{$newpost_link}>"><{$smarty.const._MD_NEWBB_VIEW}>&nbsp;<{$smarty.const._MD_NEWBB_NEWPOSTS}></option>
                    <option value="<{$all_link}>"><{$smarty.const._MD_NEWBB_VIEW}>&nbsp;<{$smarty.const._MD_NEWBB_ALL}></option>
                </select>
            </div>
            <div class="col">
                <select
                        name="viewmode" id="viewmode"
                        class="form-control  mb-2"
                        onchange="if(this.options[this.selectedIndex].value.length >0 )    { window.document.location=this.options[this.selectedIndex].value;}"
                >
                    <option value=""><{$smarty.const._MD_NEWBB_VIEWMODE}></option>
                    <{foreach item=act from=$viewmode_options|default:null}>
                    <option value="<{$act.link}>"><{$act.title}></option>
                    <{/foreach}>
                </select>
            </div>
        </div>


    <div class="generic-pagination col text-right`">
        <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
    </div>
</div>
<div class="clear"></div>
<br>

<{foreach item=post from=$posts|default:null}>
<{include file="db:newbb_thread.tpl" topic_post=$post}>
<!-- irmtfan hardcode removed style="padding: 5px;float: right; text-align:right;" -->
<div class="pagenav">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewtopic.php?topic_id=<{$post.topic_id}>"><strong><{$smarty.const._MD_NEWBB_VIEWTOPIC}></strong></a>
    <{if !isset($forum_name) }>
        |
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$post.forum_id}>"><strong><{$smarty.const._MD_NEWBB_VIEWFORUM}></strong></a>
    <{/if}>
</div>
<div class="clear"></div>
<br>
<br>
<{/foreach}>

<{if isset($mode) && $mode > 1}>
    </form>
<{/if}>

<br>
<div>
    <div class="text-right">
        <a id="threadbottom"></a><a class="btn btn-info" href="#threadtop" title="<{$smarty.const._MD_NEWBB_TOP}>">
            <span class="fa fa-arrow-circle-up"></span>
        </a>
    </div>
    <!-- irmtfan hardcode removed style="float: right; text-align:right;" -->
    <div class="generic-pagination col text-right mt-2">
        <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
        <!-- irmtfan to solve nested forms and id="xo-pagenav" issue -->
    </div>
</div>
<div class="clear"></div>

<div>
    <div class="xoopsform col mb-3">
        <{$forum_jumpbox|replace:' class="select"':' class="btn btn-light"'|replace:"'button'":'"btn btn-sm btn-light"'}>
    </div>
    <div class="xoopsform col">
        <form action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get">
            <input name="term" id="term" type="text" size="15">
            <input type="hidden" name="sortby" id="sortby" value="p.post_time desc">
            <input type="hidden" name="action" id="action" value="yes">
            <input type="hidden" name="searchin" id="searchin" value="both">
            <input type="submit" class="formButton" value="<{$smarty.const._MD_NEWBB_SEARCH}>">
            [<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const._MD_NEWBB_ADVSEARCH}></a>]
        </form>
    </div>
</div>
<div class="clear"></div>
<br>
<{if isset($online)}>
    <br>
    <{include file="db:newbb_online.tpl"}>
<{/if}>
<{include file='db:system_notification_select.tpl'}>
<!-- end module contents -->
