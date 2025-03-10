<{include file="db:news_item.tpl" story=$story}>

<{if $attached_files_count>0}>
    <{$lang_attached_files}>
    <{foreach item=onefile from=$attached_files|default:null}>
        <a href="<{$onefile.visitlink}>" target="_blank"><{$onefile.file_realname}></a>
    <{/foreach}>
<{/if}>

<div class="row xoops-news-navigation">
    <{if $pagenav|default:false}><{$smarty.const._NW_PAGE}><{$pagenav}><{/if}>
    <{if isset($nav_links)}>
        <div class="col-md-6 alignleft">
            <{if isset($previous_story_id) && $previous_story_id != -1}>
                <a href="<{$xoops_url}>/modules/news/article.php?storyid=<{$previous_story_id}>" title="<{$previous_story_title}>">
                    <span class="fa fa-circle-arrow-left"></span> <{$lang_previous_story}>
                </a>
            <{/if}>
        </div>
        <div class="col-md-6 alignright">
            <{if  isset($next_story_id) && $next_story_id != -1}>
                <a href="<{$xoops_url}>/modules/news/article.php?storyid=<{$next_story_id}>" title="<{$next_story_title}>">
                    <{$lang_next_story}> <span class="fa fa-circle-arrow-right"></span>
                </a>
            <{/if}>
        </div>
    <{/if}>
</div><!-- .row -->

<div class="xoops-news-icons aligncenter">
    <{if isset($showicons) && $showicons == true}>
        <a href="<{$xoops_url}>/modules/news/print.php?storyid=<{$story.id}>" title="<{$lang_printerpage}>">
            <span class="fa fa-print"></span>
        </a>
        <a target="_top" href="<{$mail_link}>" title="<{$lang_sendstory}>">
            <span class="fa fa-envelope"></span>
        </a>
        <a target="_blank" href="<{$xoops_url}>/modules/news/makepdf.php?storyid=<{$story.id}>" title="<{$lang_pdfstory}>">
            <span class="fa fa-file"></span>
        </a>
    <{/if}>

    <{if isset($xoops_isadmin)}>
        <a href="<{$xoops_url}>/modules/news/submit.php?op=edit&storyid=<{$story.id}>" title="Edit">
            <span class="fa fa-pencil-square-o"></span>
        </a>
        <a href="<{$xoops_url}>/modules/news/admin/index.php?op=delete&storyid=<{$story.id}>" title="Delete">
            <span class="fa fa-trash"></span>
        </a>
    <{/if}>
</div>

<{if isset($tags) && $tag == true}>
    <{include file="db:tag_bar.tpl"}>
<{/if}>

<{if $showsummary == true && $summary_count>0}>
    <{$lang_other_story}>
    <{foreach item=onesummary from=$summary|default:null}>
        <{$onesummary.story_published}>
        <a href="<{$xoops_url}>/modules/news/article.php?storyid=<{$onesummary.story_id}>" title="<{$onesummary.tpltitle}>">
            <{$onesummary.story_title}>
        </a>
    <{/foreach}>
<{/if}>

<{if $share == true}>
    <div class='shareaholic-canvas' data-bs-app='share_buttons' data-bs-app-id=''></div>
<{/if}>

<div class="comments-nav">
    <{$commentsnav}>
</div>

<{$lang_notice}>

<{if isset($comment_mode)}>
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
<{/if}>

<{include file='db:system_notification_select.tpl'}>
