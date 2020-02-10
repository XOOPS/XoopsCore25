<{if $content_template}>
	<{if $content_dotitle == 1}>
	<h2><{$content_title}></h2>
	<{/if}>
	<{includeq file="$content_template"}>
<{else}>
	<div class="row">
		<div class="col-sm-12">
			<{if $content_dotitle == 1}>
			<h2><{$content_title}></h2>
			<{/if}>
			<p>
				<{$content_text}>
			</p>
		</div>
	</div>
<{/if}>
<{if $xmdoc_viewdocs == true}>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><{$smarty.const._AM_XMCONTENT_VIEWCONTENT_XMDOC}></h3>
        </div>
        <div class="panel-body">
            <{include file="db:xmdoc_viewdoc.tpl"}>
        </div>
    </div>
<{/if}>
<{if $content_docomment == 1}>
    <div style="text-align: center; padding: 3px; margin:3px;">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>
    <div style="margin:3px; padding: 3px;">
        <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    </div>
<{/if}>